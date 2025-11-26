<template>
  <div class="product-detail">
    <div v-if="item" class="detail-card">
      <div class="img-wrap">
        <button class="back" @click="$router.back()">← 返回</button>
        <img class="detail-img" :src="imgUrl" :alt="item.name || ''" />
      </div>
      <div class="detail-info">
        <h1 class="detail-name">{{ item.name }}</h1>
        <div class="detail-price">$ {{ item.price ? item.price.toLocaleString() : '' }}</div>
        <div class="detail-category">分類：{{ formatCategory(item.category) }}</div>
        <p class="detail-desc">{{ item.description || '此商品尚無描述，請在 items.json 中新增 description 欄位。' }}</p>
        <div class="detail-stock">庫存：{{ item.amount }}</div>
        <div class="detail-total">總價：{{ totalPrice }}</div>
        <div class="detail-qty">
            <label for="qty-input">數量：</label>
            <div class="qty-selector">
              <button class="qty-btn" @click="decreaseQty" :disabled="qty <= 1">-</button>
              <input id="qty-input" type="number" v-model.number="qty" :min="1" :max="maxQty" @input="onQtyInput" @keydown="preventInputArrows" />
              <button class="qty-btn" @click="increaseQty" :disabled="qty >= maxQty">+</button>
            </div>
            <div class="cart-actions">
              <button class="cart-btn" @click="addToCart">加入購物車</button>
              <button class="buy-btn" @click="buyNow">直接購買</button>
            </div>
        </div>
      </div>
    </div>
    <div v-else class="not-found">
      找不到商品。
    </div>
  </div>
</template>

<script>
import items from '@/assets/items.json'
import './css/light/productDetail.css'
import './css/dark/productDetail.css'

export default {
  name: 'ProductDetail',
  data () {
    /* 讓返回按鈕在圖片左上角 */
    return {
      item: null,
      imgUrl: '',
      qty: 1,
      maxQty: 1,
      totalPrice: 0
    }
  },
  created () {
    // 初始載入 item
    this.loadItemFromRoute()
  },
  watch: {
    // 當路由變更時重新載入
    '$route.params.id' (newId) {
      this.loadItemFromRoute(newId)
    }
  },
  methods: {
    preventInputArrows (e) {
      // 禁用上下鍵
      if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
        e.preventDefault()
      }
    },
    updateTotalPrice () {
      this.totalPrice = (this.item && this.item.price) ? this.qty * this.item.price : 0
    },
    decreaseQty () {
      if (this.qty > 1) this.qty--
      this.updateTotalPrice()
    },
    increaseQty () {
      if (this.qty < this.maxQty) this.qty++
      this.updateTotalPrice()
    },
    addToCart () {
      // TODO: 實作加入購物車邏輯
    },
    buyNow () {
      // TODO: 實作直接購買邏輯
    },
    loadItemFromRoute (idParam) {
      const id = idParam || this.$route.params.id
      // debug log to confirm created/route-update runs
      // eslint-disable-next-line no-console
      console.log('[ProductDetail] loading item id=', id)
      this.item = items.find(i => i.id === id) || null
      if (this.item) {
        const imgName = String(this.item.img || '').replace(/^\.\/?/, '')
        try {
          this.imgUrl = new URL(`../assets/${imgName}`, import.meta.url)
          console.log('[ProductDetail] loaded image URL:', this.imgUrl)
        } catch (e) {
          try {
            this.imgUrl = new URL('../assets/logo.png', import.meta.url).href
          } catch (err) {
            this.imgUrl = this.item.img || ''
          }
        }
        // set default qty: 1, maxQty 為庫存amount
        const stock = Number(this.item.amount || 0)
        this.maxQty = stock > 0 ? stock : 1
        this.qty = 1
        this.updateTotalPrice()
      } else {
        console.log('[ProductDetail] item not found for id:', id)
        try {
          this.imgUrl = new URL('../assets/logo.png', import.meta.url).href
        } catch (err) {
          this.imgUrl = ''
        }
      }
    },
    formatCategory (cat) {
      if (Array.isArray(cat)) return cat.join('、')
      return cat
    }
  }
}
</script>

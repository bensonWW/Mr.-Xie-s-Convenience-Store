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
        <p class="detail-desc">{{ item.information || '此商品尚無描述。' }}</p>
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
import api from '@/services/api'
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
    async addToCart () {
      if (!localStorage.getItem('token')) {
        alert('請先登入')
        this.$router.push('/profile')
        return
      }

      try {
        await api.post('/cart/items', {
          product_id: this.item.id,
          quantity: this.qty
        })
        alert('已加入購物車')
      } catch (error) {
        console.error('Add to cart error:', error)
        alert('加入購物車失敗')
      }
    },
    buyNow () {
      this.addToCart().then(() => {
        this.$router.push('/car')
      })
    },
    async loadItemFromRoute (idParam) {
      const id = idParam || this.$route.params.id
      try {
        const response = await api.get(`/products/${id}`)
        this.item = response.data

        if (this.item.image) {
          try {
            this.imgUrl = new URL(`../assets/${this.item.image}`, import.meta.url).href
          } catch (e) {
            this.imgUrl = this.item.image
          }
        } else {
          this.imgUrl = ''
        }

        const stock = Number(this.item.stock || 0)
        this.maxQty = stock > 0 ? stock : 1
        this.qty = 1
        this.updateTotalPrice()
      } catch (error) {
        console.error('Fetch product error:', error)
        this.item = null
      }
    },
    formatCategory (cat) {
      if (Array.isArray(cat)) return cat.join('、')
      return cat
    }
  }
}
</script>

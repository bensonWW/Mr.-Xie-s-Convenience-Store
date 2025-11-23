<template>
  <div class="product-detail">
    <button class="back" @click="$router.back()">← 返回</button>
    <div v-if="item" class="detail-card">
      <img class="detail-img" :src="imgUrl" :alt="item.name || ''" />
      <div class="detail-info">
        <h1 class="detail-name">{{ item.name }}</h1>
        <div class="detail-price">$ {{ item.price ? item.price.toLocaleString() : '' }}</div>
        <div class="detail-category">分類：{{ formatCategory(item.category) }}</div>
        <p class="detail-desc">{{ item.description || '此商品尚無描述，請在 items.json 中新增 description 欄位。' }}</p>
      </div>
    </div>
    <div v-else class="not-found">
      找不到商品。
    </div>
  </div>
</template>

<script>
import items from '@/assets/items.json'

export default {
  name: 'ProductDetail',
  data () {
    return {
      item: null,
      imgUrl: ''
    }
  },
  created () {
    this.loadItemFromRoute()
  },
  beforeRouteUpdate (to, from, next) {
    // when route param changes but component is reused, re-load
    this.loadItemFromRoute(to.params.id)
    next()
  },
  // 不使用 safeItem，模板由 v-if="item" 保護
  methods: {
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

<style scoped>
.product-detail { padding: 2rem; }
.back { background: none; border: none; color: var(--main-text); cursor: pointer; margin-bottom: 1rem; }
.detail-card { display: flex; gap: 2rem; align-items: flex-start; justify-content: center; }
.detail-img { width: 360px; height: 360px; object-fit: contain; background: var(--main-card); border-radius: 8px; }
.detail-info { max-width: 600px; text-align: left; }
.detail-name { color: var(--main-text); margin: 0 0 0.5rem 0; }
.detail-price { color: #e74c3c; font-size: 1.8rem; margin-bottom: 0.5rem; }
.detail-category { color: #666; margin-bottom: 1rem; }
.detail-desc { line-height: 1.6; }
.not-found { text-align: center; color: #999; }
</style>

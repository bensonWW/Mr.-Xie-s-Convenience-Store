<template>
  <section v-if="products.length > 0">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-2xl font-bold text-xieBlue flex items-center">
        <i class="fas fa-bolt text-xieOrange mr-2"></i> 限時下殺
      </h2>
      <div class="text-sm font-bold text-white bg-red-600 px-3 py-1 rounded">
        剩餘時間 02:14:59
      </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
      <div 
        v-for="product in products" 
        :key="product.id"
        class="bg-white rounded-lg shadow hover:shadow-lg transition group cursor-pointer"
        @click="goToProduct(product.id)"
      >
        <div class="relative">
          <img :src="product.image || 'https://via.placeholder.com/300'" class="w-full h-40 object-cover rounded-t-lg">
          <span class="absolute top-0 left-0 bg-red-600 text-white text-xs px-2 py-1 font-bold">5折</span>
        </div>
        <div class="p-3">
          <h3 class="text-sm font-bold text-gray-800 line-clamp-2 h-10 mb-2 group-hover:text-xieOrange">{{ product.name }}</h3>
          <div class="text-gray-400 text-xs line-through">NT$ {{ Math.round(product.price * 1.5) }}</div>
          <div class="text-xieOrange font-bold text-lg">NT$ {{ product.price }}</div>
          <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
            <div class="bg-red-500 h-1.5 rounded-full" :style="{ width: getRandomProgress(product.id) + '%' }"></div>
          </div>
          <div class="text-xs text-gray-500 mt-1">已搶 {{ getRandomProgress(product.id) }}%</div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import api from '../../services/api'

export default {
  name: 'FlashSaleSection',
  data () {
    return {
      products: []
    }
  },
  mounted () {
    this.fetchFlashSaleProducts()
  },
  methods: {
    async fetchFlashSaleProducts () {
      try {
        const res = await api.get('/products')
        // Take first 5 products for demo
        this.products = res.data.data ? res.data.data.slice(0, 5) : res.data.slice(0, 5)
      } catch (e) {
        console.error('Fetch flash sale error', e)
      }
    },
    goToProduct (id) {
      this.$router.push({ name: 'item', params: { id } })
    },
    getRandomProgress (id) {
      // Deterministic pseudo-random based on ID so it doesn't jump around
      const seed = id * 17
      return (seed % 60) + 30 // 30% to 90%
    }
  }
}
</script>

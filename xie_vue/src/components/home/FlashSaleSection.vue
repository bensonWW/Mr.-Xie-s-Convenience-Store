<template>
  <section v-if="products.length > 0">
    <!-- Product Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
      <div
        v-for="product in products"
        :key="product.id"
        class="bg-white dark:bg-slate-800 rounded-xl border border-stone-100 dark:border-slate-700 overflow-hidden hover:shadow-lg dark:hover:shadow-slate-900/50 transition group cursor-pointer"
        @click="goToProduct(product.id)"
      >
        <!-- Image -->
        <div class="relative">
          <img 
            :src="product.image || 'https://via.placeholder.com/300'" 
            class="w-full h-40 object-cover transition-transform duration-500 group-hover:scale-105"
            :alt="product.name"
          >
          <!-- Discount Badge -->
          <span class="absolute top-2 left-2 bg-gradient-to-r from-rose-500 to-red-600 text-white text-xs px-2.5 py-1 rounded-full font-medium shadow-lg">
            <i class="fas fa-bolt mr-1"></i>特價
          </span>
        </div>
        <!-- Info -->
        <div class="p-4">
          <h3 class="text-sm font-medium text-slate-700 dark:text-stone-100 line-clamp-2 h-10 mb-2 group-hover:text-xieOrange transition">
            {{ product.name }}
          </h3>
          <div class="flex items-baseline gap-2 mb-3">
            <span class="text-xieOrange font-bold text-lg">{{ formatPrice(product.price) }}</span>
            <span v-if="product.original_price" class="text-stone-400 text-xs line-through">
              {{ formatPrice(product.original_price) }}
            </span>
          </div>
          <!-- Progress Bar -->
          <div class="w-full bg-stone-100 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
            <div 
              class="bg-gradient-to-r from-xieOrange to-rose-500 h-1.5 rounded-full transition-all duration-500" 
              :style="{ width: getRandomProgress(product.id) + '%' }"
            ></div>
          </div>
          <div class="text-xs text-stone-500 dark:text-stone-400 mt-1.5">
            已搶購 {{ getRandomProgress(product.id) }}%
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Empty State -->
  <div v-else class="text-center py-12 text-stone-400 dark:text-stone-500">
    <i class="fas fa-tag text-4xl mb-3 opacity-50"></i>
    <p>目前沒有特價商品</p>
  </div>
</template>

<script>
import api from '../../services/api'
import { formatPrice } from '../../utils/currency'

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
    formatPrice,
    async fetchFlashSaleProducts () {
      try {
        const res = await api.get('/products')
        this.products = res.data.data ? res.data.data.slice(0, 5) : res.data.slice(0, 5)
      } catch (e) {
        console.error('Fetch flash sale error', e)
      }
    },
    goToProduct (id) {
      this.$router.push({ name: 'item', params: { id } })
    },
    getRandomProgress (id) {
      const seed = id * 17
      return (seed % 60) + 30
    }
  }
}
</script>

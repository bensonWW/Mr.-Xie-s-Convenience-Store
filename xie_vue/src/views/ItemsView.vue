<template>
  <div class="bg-wood-50 dark:bg-slate-900 min-h-screen pb-12 transition-colors duration-300">
    <!-- Filters Header -->
    <div class="bg-white dark:bg-slate-800 border-b border-stone-200 dark:border-slate-800/50 sticky top-16 z-dropdown transition-colors duration-300">
      <div class="container mx-auto px-4 py-4">
        <!-- Breadcrumb & Sort -->
        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
          <div class="flex items-center space-x-2 text-sm text-stone-500 dark:text-stone-400">
            <router-link to="/" class="hover:text-slate-700 dark:hover:text-stone-200">首頁</router-link>
            <span>/</span>
            <span class="text-slate-700 dark:text-stone-100 font-medium">{{ displayTitle }}</span>
            <span class="ml-4 text-stone-400 dark:text-stone-500">共 {{ filteredItems.length }} 筆</span>
          </div>

          <!-- Quick Filters -->
          <div class="flex items-center space-x-4">
            <div class="flex overflow-x-auto space-x-2 no-scrollbar">
              <button 
                v-for="cat in validCategories.slice(0, 5)" 
                :key="cat.id"
                class="px-4 py-1.5 rounded-full text-sm whitespace-nowrap border transition-colors"
                :class="selectedCategoryName === cat.displayName ? 'bg-slate-700 dark:bg-slate-600 text-white border-slate-700 dark:border-slate-600' : 'bg-white dark:bg-slate-700 text-stone-600 dark:text-stone-300 border-stone-200 dark:border-slate-600 hover:border-stone-400 dark:hover:border-stone-500'"
                @click="selectCategory(cat)"
              >
                {{ cat.displayName }}
              </button>
            </div>
          </div>

          <!-- Sort -->
          <div class="flex items-center space-x-2">
            <span class="text-xs text-stone-500 dark:text-stone-400">排序:</span>
            <select class="text-sm bg-transparent border-none focus:ring-0 cursor-pointer text-slate-700 dark:text-stone-200 font-medium">
              <option class="dark:bg-slate-800">最新上架</option>
              <option class="dark:bg-slate-800">價格由低到高</option>
              <option class="dark:bg-slate-800">價格由高到低</option>
              <option class="dark:bg-slate-800">熱銷排行</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
      <div class="flex flex-col md:flex-row gap-8">
        
        <!-- Sidebar Filter -->
        <aside class="w-full md:w-64 flex-shrink-0 hidden md:block">
          <div class="bg-white dark:bg-slate-800 p-6 rounded-lg border border-stone-100 dark:border-slate-700 sticky top-36 transition-colors duration-300">
            <h3 class="font-semibold text-slate-700 dark:text-stone-100 mb-4 pb-2 border-b border-stone-100 dark:border-slate-700">商品分類</h3>
            <ul class="space-y-1">
              <li
                v-for="cat in validCategories"
                :key="cat.id"
                @click="selectCategory(cat)"
                :class="['px-3 py-2 rounded-md cursor-pointer transition text-sm font-medium', selectedCategoryName === cat.displayName ? 'bg-xieOrange/10 text-xieOrange border-l-4 border-xieOrange' : 'text-stone-600 dark:text-stone-300 hover:bg-stone-50 dark:hover:bg-slate-700']"
              >
                {{ cat.displayName }}
              </li>
            </ul>

            <h3 class="font-semibold text-slate-700 dark:text-stone-100 mt-8 mb-4 pb-2 border-b border-stone-100 dark:border-slate-700">庫存狀況</h3>
            <label class="flex items-center space-x-2 mb-2 cursor-pointer">
              <input type="checkbox" class="text-xieOrange focus:ring-xieOrange rounded-sm bg-stone-50 dark:bg-slate-700 border-stone-300 dark:border-slate-600">
              <span class="text-sm text-stone-600 dark:text-stone-300">僅顯示有貨</span>
            </label>
            <label class="flex items-center space-x-2 cursor-pointer">
              <input type="checkbox" class="text-xieOrange focus:ring-xieOrange rounded-sm bg-stone-50 dark:bg-slate-700 border-stone-300 dark:border-slate-600">
              <span class="text-sm text-stone-600 dark:text-stone-300">促銷商品</span>
            </label>
          </div>
        </aside>

        <!-- Product Grid -->
        <div class="flex-1">
          <!-- Empty State -->
          <div v-if="filteredItems.length === 0" class="bg-white dark:bg-slate-800 rounded-lg border border-stone-100 dark:border-slate-700 p-12">
            <div class="flex flex-col items-center justify-center text-center">
              <div class="w-20 h-20 rounded-full bg-sky-50 dark:bg-sky-900/20 flex items-center justify-center mb-6">
                <i class="fas fa-search text-3xl text-sky-400 dark:text-sky-500"></i>
              </div>
              <h3 class="text-lg font-semibold text-slate-700 dark:text-stone-100 mb-2">找不到相關商品</h3>
              <p class="text-sm text-stone-500 dark:text-stone-400 mb-6">請嘗試其他關鍵字或分類</p>
              <button @click="showAll" class="bg-xieOrange text-white px-6 py-2 rounded-md hover:bg-[#cf8354] transition font-medium">
                瀏覽所有商品
              </button>
            </div>
          </div>

          <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <div
              v-for="item in filteredItems"
              :key="item.id"
              class="group bg-white dark:bg-slate-800 rounded-lg border border-stone-100 dark:border-slate-700 hover:border-stone-300 dark:hover:border-slate-600 hover:-translate-y-1 transition-all duration-300 overflow-hidden cursor-pointer"
            >
              <router-link :to="`/items/${item.id}`" class="block">
                <!-- Image -->
                <div class="relative aspect-square overflow-hidden bg-stone-100 dark:bg-slate-700">
                  <img v-if="item.img" :src="item.img" :alt="item.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                  <i v-else class="fas fa-image text-4xl text-stone-300 dark:text-slate-500 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"></i>
                  <!-- Quick Add -->
                  <button 
                    @click.prevent="addToCart(item)" 
                    class="absolute bottom-2 right-2 bg-white/90 dark:bg-slate-800/90 p-2 rounded-full shadow-sm text-slate-700 dark:text-stone-200 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-xieOrange hover:text-white"
                  >
                    <i class="fas fa-plus"></i>
                  </button>
                </div>

                <!-- Content -->
                <div class="p-4">
                  <h3 class="text-sm font-medium text-slate-700 dark:text-stone-100 mb-2 line-clamp-2 h-10 group-hover:text-xieOrange transition-colors">
                    {{ item.name }}
                  </h3>
                  <div class="flex items-end justify-between">
                    <div class="flex items-baseline space-x-1">
                      <span class="text-xs text-stone-500 dark:text-stone-400">NT$</span>
                      <span class="text-lg font-bold text-slate-700 dark:text-stone-100">{{ formatPrice(item.price).replace('NT$ ', '') }}</span>
                    </div>
                    <div v-if="item.original_price" class="text-xs text-stone-400 dark:text-stone-500 line-through">
                      {{ formatPrice(item.original_price) }}
                    </div>
                  </div>
                </div>
              </router-link>
            </div>
          </div>
          
          <!-- Pagination -->
          <div class="flex justify-center mt-12 space-x-2" v-if="pagination.last_page > 1">
            <button
              @click="changePage(pagination.current_page - 1)"
              :disabled="pagination.current_page <= 1"
              class="w-10 h-10 flex items-center justify-center border border-stone-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-stone-500 dark:text-stone-400 hover:bg-stone-50 dark:hover:bg-slate-700 rounded-md transition-colors disabled:opacity-50"
            >
              <i class="fas fa-chevron-left"></i>
            </button>

            <button
              v-for="page in pagination.last_page"
              :key="page"
              @click="changePage(page)"
              :class="['w-10 h-10 flex items-center justify-center border rounded-md font-medium transition-colors', page === pagination.current_page ? 'border-xieOrange bg-xieOrange text-white' : 'border-stone-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-stone-500 dark:text-stone-300 hover:bg-stone-50 dark:hover:bg-slate-700']"
            >
              {{ page }}
            </button>

            <button
              @click="changePage(pagination.current_page + 1)"
              :disabled="pagination.current_page >= pagination.last_page"
              class="w-10 h-10 flex items-center justify-center border border-stone-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-stone-500 dark:text-stone-400 hover:bg-stone-50 dark:hover:bg-slate-700 rounded-md transition-colors disabled:opacity-50"
            >
              <i class="fas fa-chevron-right"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api'
import { useToast } from 'vue-toastification'
import { formatPrice } from '../utils/currency'
import { resolveImageUrl } from '../utils/image'
import { useCartStore } from '../stores/cart'

export default {
  name: 'ItemsView',
  setup () {
    const toast = useToast()
    const cartStore = useCartStore()
    return { toast, cartStore }
  },
  data () {
    return {
      categories: [],
      items: [],
      selectedCategory: '',
      pagination: {
        current_page: 1,
        last_page: 1,
        total: 0
      }
    }
  },
  created () {
    if (this.$route.query.category) {
      this.selectedCategory = this.$route.query.category
    }
    this.fetchCategories()
    this.fetchProducts()
  },
  computed: {
    displayTitle () {
      if (this.$route.query.search) {
        return `搜尋: "${this.$route.query.search}"`
      }
      return this.selectedCategoryName ? `${this.selectedCategoryName}` : '所有商品'
    },
    selectedCategoryName () {
      if (typeof this.selectedCategory === 'object' && this.selectedCategory !== null) {
        return this.selectedCategory.displayName || this.selectedCategory.name || ''
      }
      return this.selectedCategory || ''
    },
    validCategories () {
      return this.categories
        .map((cat, index) => {
          if (typeof cat === 'string') {
            return { id: index, name: cat, displayName: cat }
          }
          if (typeof cat === 'object' && cat !== null) {
            const name = cat.name || cat.label || cat.title || ''
            return {
              id: cat.id || index,
              name: name,
              slug: cat.slug || '',
              displayName: name
            }
          }
          return { id: index, name: String(cat), displayName: String(cat) }
        })
        .filter(cat => cat.displayName && cat.displayName.length > 0)
    },
    filteredItems () {
      return this.items
    }
  },
  methods: {
    formatPrice,
    async addToCart (item) {
      if (!localStorage.getItem('token')) {
        this.toast.warning('請先登入')
        this.$router.push('/profile')
        return
      }
      try {
        await this.cartStore.addToCart(item.id, 1)
      } catch (error) {
        console.error('Add to cart error:', error)
        const status = error.response?.status
        const backendMessage = error.response?.data?.message || error.response?.data?.error
        if (!backendMessage) {
          this.toast.error(`加入購物車失敗${status ? ` (${status})` : ''}`)
        }
      }
    },
    async fetchCategories () {
      try {
        const response = await api.get('/categories')
        this.categories = response.data
        if (this.validCategories.length > 0 && !this.selectedCategory && !this.$route.query.search) {
          this.selectedCategory = this.validCategories[0].displayName
          this.fetchProducts(1)
        }
      } catch (error) {
        console.error('Error fetching categories:', error)
      }
    },
    async fetchProducts (page = 1) {
      try {
        const params = {
          page: page,
          category: this.selectedCategoryName || undefined,
          search: this.$route.query.search
        }

        const response = await api.get('/products', { params })

        let productsData = []
        if (Array.isArray(response.data)) {
          productsData = response.data
          this.pagination = { current_page: 1, last_page: 1, total: productsData.length }
        } else if (response.data && typeof response.data === 'object') {
          if (Array.isArray(response.data.data)) {
            productsData = response.data.data
            this.pagination = {
              current_page: response.data.current_page || response.data.meta?.current_page || 1,
              last_page: response.data.last_page || response.data.meta?.last_page || 1,
              total: response.data.total || response.data.meta?.total || 0
            }
          } else {
            productsData = []
          }
        }

        this.items = productsData.map(item => ({
          ...item,
          img: resolveImageUrl(item.image)
        }))
      } catch (error) {
        console.error('Error fetching products:', error)
        this.items = []
        this.toast.error('無法載入商品資料')
      }
    },
    selectCategory (cat) {
      const categoryName = cat.displayName || cat.name || cat
      this.selectedCategory = categoryName
      this.$router.push({ path: '/items', query: { category: categoryName } })
      this.fetchProducts(1)
    },
    showAll () {
      this.selectedCategory = ''
      this.$router.push({ path: '/items' })
      this.fetchProducts(1)
    },
    changePage (page) {
      if (page < 1 || page > this.pagination.last_page) return
      this.fetchProducts(page)
      window.scrollTo({ top: 0, behavior: 'smooth' })
    }
  },
  watch: {
    '$route.query.category' (newVal) {
      if (newVal) {
        this.selectedCategory = newVal
      }
    }
  }
}
</script>

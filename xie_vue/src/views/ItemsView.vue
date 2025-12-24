<template>
  <div class="container mx-auto px-4 py-6 grid grid-cols-12 gap-6">

    <!-- Sidebar -->
    <aside class="col-span-12 md:col-span-3 lg:col-span-2">
      <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="bg-xieBlue text-white px-4 py-3 font-bold">
          <i class="fas fa-list mr-2"></i> 商品分類
        </div>
        <ul class="text-sm text-gray-700 divide-y divide-gray-100 font-medium">
          <li
            v-for="cat in validCategories"
            :key="cat.id"
            @click="selectCategory(cat)"
            :class="['px-4 py-3 border-l-4 cursor-pointer transition', selectedCategoryName === cat.displayName ? 'bg-orange-50 text-xieOrange border-xieOrange' : 'hover:bg-gray-50 hover:text-xieOrange border-transparent']"
          >
            {{ cat.displayName }}
          </li>
        </ul>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="col-span-12 md:col-span-9 lg:col-span-10">

      <!-- Header / Breadcrumbs & Sort -->
      <div class="bg-white p-4 rounded-lg shadow-sm mb-6 flex flex-col md:flex-row justify-between items-center text-sm">
        <div class="mb-4 md:mb-0">
          <router-link to="/" class="text-gray-500 hover:text-xieOrange">首頁</router-link>
          <span class="mx-2 text-gray-400">/</span>
          <span class="font-bold text-xieBlue">{{ displayTitle }}</span>
          <span class="ml-4 text-gray-500">共 {{ filteredItems.length }} 筆商品</span>
        </div>
        <div class="flex items-center gap-4">
          <label class="font-bold text-gray-700">排序：</label>
          <select class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:border-xieOrange">
            <option>綜合排名</option>
            <option>熱銷排行</option>
            <option>價格由低到高</option>
            <option>價格由高到低</option>
          </select>
        </div>
      </div>

      <!-- Product Grid -->
      <div v-if="filteredItems.length === 0" class="col-span-12 text-center py-12 text-gray-500 bg-gray-50 rounded-lg">
          <i class="fas fa-box-open text-4xl mb-4 text-gray-300"></i>
          <p>找不到相關商品</p>
          <button @click="selectCategory('All')" class="mt-4 text-xieOrange underline text-sm">瀏覽所有商品</button>
      </div>

      <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        <div
          v-for="item in filteredItems"
          :key="item.id"
          class="bg-white rounded-lg shadow hover:shadow-lg transition group border border-gray-200 hover:border-xieOrange overflow-hidden"
        >
          <router-link :to="`/items/${item.id}`" class="block relative bg-gray-100 p-4">
            <div class="w-full h-48 flex items-center justify-center bg-white rounded-t-lg overflow-hidden">
               <img v-if="item.img" :src="item.img" :alt="item.name" class="w-full h-full object-cover">
               <i v-else class="fas fa-image text-4xl text-gray-300"></i>
            </div>
          </router-link>
          <div class="p-3">
            <h3 class="text-sm text-gray-800 line-clamp-2 h-10 mb-2 group-hover:text-xieOrange transition leading-tight">
              <router-link :to="`/items/${item.id}`">{{ item.name }}</router-link>
            </h3>
            <div class="flex items-end justify-between">
              <div>
                 <div v-if="item.original_price" class="text-xs text-gray-400 line-through">{{ formatPrice(item.original_price) }}</div>
                 <div class="text-xieOrange font-bold text-lg leading-none">{{ formatPrice(item.price) }}</div>
              </div>
              <button @click.prevent="addToCart(item)" class="bg-xieOrange text-white p-2 rounded-full w-8 h-8 flex items-center justify-center opacity-0 group-hover:opacity-100 transition shadow-md hover:bg-orange-600">
                <i class="fas fa-cart-plus"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <!-- Pagination -->
      <div class="mt-8 flex justify-center space-x-2" v-if="pagination.last_page > 1">
        <button
          @click="changePage(pagination.current_page - 1)"
          :disabled="pagination.current_page <= 1"
          class="px-3 py-2 bg-white border border-gray-300 text-gray-500 rounded hover:bg-gray-100 disabled:opacity-50"
        >
          <i class="fas fa-chevron-left"></i>
        </button>

        <button
          v-for="page in pagination.last_page"
          :key="page"
          @click="changePage(page)"
          :class="['px-3 py-2 border rounded font-bold', page === pagination.current_page ? 'bg-xieOrange border-xieOrange text-white' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-100 hover:text-xieOrange']"
        >
          {{ page }}
        </button>

        <button
          @click="changePage(pagination.current_page + 1)"
          :disabled="pagination.current_page >= pagination.last_page"
          class="px-3 py-2 bg-white border border-gray-300 text-gray-500 rounded hover:bg-gray-100 disabled:opacity-50"
        >
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>

    </main>
  </div>
</template>

<script>
import api from '../services/api'
import { useToast } from 'vue-toastification'
import { formatPrice } from '../utils/currency'
import { resolveImageUrl } from '../utils/image'
import { useCartStore } from '../stores/cart' // Import Store

export default {
  name: 'ItemsView',
  setup () {
    const toast = useToast()
    const cartStore = useCartStore() // Setup store
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
      return `${this.selectedCategoryName}商品`
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
          let displayName = cat.name
          if (typeof displayName === 'object' && displayName !== null) {
            displayName = displayName.name || displayName.label || ''
          }
          return {
            ...cat,
            displayName: displayName || `Category ${cat.id}`
          }
        })
        .filter(cat => cat.displayName && typeof cat.displayName === 'string' && !cat.displayName.includes('[object'))
    },
    // We no longer filter client side because we have pagination.
    // The items in `this.items` are already filtered by the server.
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

      await this.cartStore.addToCart(item.id, 1)
      // Toast is handled in store, or we can add extra logic here.
      // Store success toast: '已加入購物車'
      // Maybe we want to mention item name? Store might not know item name.
      // The store toast is generic. We can suppress store toast or just let it consistantly show.
      // Current store impl shows toast. Success.
    },
    async fetchCategories () {
      try {
        const response = await api.get('/categories')
        this.categories = response.data
        // Use validCategories for initial selection
        if (this.validCategories.length > 0 && !this.selectedCategory && !this.$route.query.search) {
          this.selectedCategory = this.validCategories[0]
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
          category: this.selectedCategoryName !== 'All' ? this.selectedCategoryName : undefined,
          search: this.$route.query.search
        }

        const response = await api.get('/products', { params })

        // Standard Laravel Pagination Response Expected
        if (response.data && response.data.data) {
           this.items = response.data.data.map(item => ({
             ...item,
             img: resolveImageUrl(item.image)
           }))
           this.pagination = {
             current_page: response.data.current_page,
             last_page: response.data.last_page,
             total: response.data.total
           }
        } else {
           console.warn('Unexpected API format, fallback to empty', response.data)
           this.items = []
        }
      } catch (error) {
        console.error('Error fetching products:', error)
        this.items = []
        this.toast.error('無法載入商品資料')
      }
    },
    selectCategory (cat) {
      this.selectedCategory = cat
      const categoryName = cat.name || cat
      if (this.$route.query.search) {
        // If searching, clear search to show category
        this.$router.push({ path: '/items', query: { category: categoryName } })
      } else {
        // Update URL to keep state
        this.$router.push({ path: '/items', query: { category: categoryName } })
      }
      this.fetchProducts(1) // Reset to page 1
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

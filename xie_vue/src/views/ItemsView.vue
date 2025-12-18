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
            v-for="cat in categories"
            :key="cat"
            @click="selectCategory(cat)"
            :class="['px-4 py-3 border-l-4 cursor-pointer transition', selectedCategory === cat ? 'bg-orange-50 text-xieOrange border-xieOrange' : 'hover:bg-gray-50 hover:text-xieOrange border-transparent']"
          >
            {{ cat }}
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
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
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
                 <div v-if="item.original_price" class="text-xs text-gray-400 line-through">NT$ {{ Number(item.original_price).toLocaleString() }}</div>
                 <div class="text-xieOrange font-bold text-lg leading-none">NT$ {{ Number(item.price).toLocaleString() }}</div>
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

export default {
  name: 'ItemsView',
  setup () {
    const toast = useToast()
    return { toast }
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
    this.fetchCategories()
    this.fetchProducts()
  },
  computed: {
    displayTitle () {
      if (this.$route.query.search) {
        return `搜尋: "${this.$route.query.search}"`
      }
      return `${this.selectedCategory}商品`
    },
    // We no longer filter client side because we have pagination.
    // The items in `this.items` are already filtered by the server.
    filteredItems () {
      return this.items
    }
  },
  methods: {
    async addToCart (item) {
      if (!localStorage.getItem('token')) {
        this.toast.warning('請先登入')
        this.$router.push('/profile')
        return
      }

      try {
        await api.post('/cart/items', {
          product_id: item.id,
          quantity: 1
        })
        this.toast.success(`已將 ${item.name} 加入購物車`)
        this.$store.dispatch('cart/fetchCount')
      } catch (error) {
        console.error('Add to cart error:', error)
        this.toast.error('加入購物車失敗')
      }
    },
    async fetchCategories () {
      try {
        const response = await api.get('/categories')
        this.categories = response.data
        if (this.categories.length > 0 && !this.selectedCategory && !this.$route.query.search) {
          // If no category selected and no search, select first
          this.selectedCategory = this.categories[0]
          // Force fetch products with this new category
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
          category: this.selectedCategory !== 'All' ? this.selectedCategory : undefined, // Assuming 'All' or similar handling if needed, though current logic seems to filter client side for search.
          // Wait, the current logic filters by category in computed property `filteredItems` BUT fetches ALL products.
          // With pagination, we MUST fetch by category from the server.
          // Let's check ProductController: it supports `category` and `search` query params.
          // So we should pass these params to the API.
          search: this.$route.query.search
        }

        // If we want to use server-side filtering (efficient), we pass params.
        // However, the original code fetched ALL and filtered client-side.
        // I should switch to server-side filtering now that we rely on pagination.

        if (this.selectedCategory && !this.$route.query.search) {
          params.category = this.selectedCategory
        }

        const response = await api.get('/products', { params })

        const productsData = response.data.data // Laravel pagination 'data' key
        this.pagination = {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          total: response.data.total
        }

        this.items = productsData.map(item => {
          let imgUrl = ''
          if (item.image) {
            if (item.image.startsWith('http')) {
              imgUrl = item.image
            } else {
              const baseUrl = api.defaults.baseURL.replace('/api', '')
              imgUrl = `${baseUrl}/images/${item.image}`
            }
          }
          return {
            ...item,
            img: imgUrl
          }
        })
      } catch (error) {
        console.error('Error fetching products:', error)
      }
    },
    selectCategory (cat) {
      this.selectedCategory = cat
      if (this.$route.query.search) {
        // If searching, clear search to show category
        this.$router.push({ path: '/items', query: { category: cat } })
      } else {
        // Update URL to keep state
        this.$router.push({ path: '/items', query: { category: cat } })
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

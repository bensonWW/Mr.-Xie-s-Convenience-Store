<template>
  <div class="bg-gray-100 min-h-screen pb-12">
    <!-- Breadcrumbs -->
    <div class="bg-gray-100 py-4">
        <div class="container mx-auto px-4 text-sm text-gray-500">
            <router-link to="/" class="hover:text-xieOrange">首頁</router-link> <span class="mx-2">/</span>
            <router-link to="/items" class="hover:text-xieOrange">{{ formatCategory(item?.category) || '商品' }}</router-link> <span class="mx-2">/</span>
            <span class="text-gray-700">{{ item?.name }}</span>
        </div>
    </div>

    <main class="container mx-auto px-4" v-if="item">
        <div class="bg-white rounded-xl shadow-sm p-6 grid grid-cols-1 md:grid-cols-2 gap-10">

            <!-- Image Section -->
            <div class="space-y-4">
                <div class="aspect-square bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200 p-8 relative overflow-hidden group">
                    <img v-if="imgUrl" :src="imgUrl" :alt="item.name" class="w-full h-full object-contain">
                    <i v-else class="fas fa-image text-9xl text-gray-300"></i>
                    <button class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition p-2 bg-white rounded-full shadow-sm" @click="toggleWishlist">
                        <i :class="isFavorited ? 'fas fa-heart text-red-500' : 'far fa-heart'" class="text-xl"></i>
                    </button>
                </div>
                <!-- Mock Thumbnails -->
                <div class="flex gap-2 overflow-x-auto">
                    <div class="w-20 h-20 border-2 border-xieOrange rounded-md p-1 cursor-pointer">
                        <div class="bg-gray-100 w-full h-full flex items-center justify-center text-gray-400 overflow-hidden">
                             <img v-if="imgUrl" :src="imgUrl" class="w-full h-full object-cover">
                             <i v-else class="fas fa-image"></i>
                        </div>
                    </div>
                     <!-- Placeholders for gallery effect -->
                    <div class="w-20 h-20 border-2 border-transparent hover:border-xieOrange rounded-md p-1 cursor-pointer transition">
                        <div class="bg-gray-100 w-full h-full flex items-center justify-center text-gray-400"><i class="fas fa-image"></i></div>
                    </div>
                </div>
            </div>

            <!-- Info Section -->
            <div class="flex flex-col">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">{{ item.name }}</h1>
                <div class="text-sm text-gray-500 mb-4">商品編號：{{ item.id }} | 分類：{{ formatCategory(item.category) }}</div>

                <div class="bg-orange-50 p-4 rounded-lg mb-6 flex items-end gap-3">
                    <span class="text-xs text-gray-500 mb-1">特價</span>
                    <div class="text-4xl font-bold text-xieOrange">{{ formatPrice(item.price) }}</div>
                    <div v-if="item.original_price" class="text-sm text-gray-400 line-through mb-1">{{ formatPrice(item.original_price) }}</div>
                </div>

                <ul class="space-y-2 text-gray-600 mb-6 text-sm">
                    <li><i class="fas fa-check text-xieOrange mr-2"></i>24h 快速出貨</li>
                    <li><i class="fas fa-check text-xieOrange mr-2"></i>原廠正品保證</li>
                    <li><i class="fas fa-check text-xieOrange mr-2"></i>7天鑑賞期</li>
                </ul>

                <hr class="border-gray-100 mb-6">

                <div class="space-y-6">
                    <div class="flex justify-between items-center text-sm">
                        <div class="text-gray-600">庫存狀況：<span class="text-green-600 font-bold">現貨充足 (剩餘 {{ item.stock || 0 }} 件)</span></div>
                        <div class="font-bold text-gray-800">總計：<span class="text-xieOrange text-xl ml-2">{{ formatPrice(totalPrice) }}</span></div>
                    </div>

                    <div class="flex items-center gap-4">
                        <span class="font-bold text-gray-700">數量：</span>
                        <div class="flex items-center border-2 border-gray-300 rounded-lg overflow-hidden focus-within:border-xieOrange transition">
                            <button class="bg-gray-100 px-3 py-2 text-gray-600 hover:bg-orange-100 hover:text-xieOrange transition" @click="decreaseQty" :disabled="qty <= 1">-</button>
                            <input type="number" v-model.number="qty" class="w-16 text-center py-2 font-bold focus:outline-none" :min="1" :max="maxQty" @input="onQtyInput" @keydown="preventInputArrows">
                            <button class="bg-gray-100 px-3 py-2 text-gray-600 hover:bg-orange-100 hover:text-xieOrange transition" @click="increaseQty" :disabled="qty >= maxQty">+</button>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <button @click="addToCart" class="flex-1 bg-xieOrange text-white text-lg font-bold py-3 px-6 rounded-lg hover:bg-orange-600 transition shadow-md flex items-center justify-center gap-2">
                            <i class="fas fa-cart-plus"></i> 加入購物車
                        </button>
                        <button @click="buyNow" class="flex-1 border-2 border-xieOrange text-xieOrange text-lg font-bold py-3 px-6 rounded-lg hover:bg-orange-50 transition flex items-center justify-center gap-2">
                            <i class="fas fa-bolt"></i> 直接購買
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Section -->
        <div class="mt-10 bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="flex border-b border-gray-200">
                <button
                    @click="activeTab = 'details'"
                    :class="['px-6 py-4 font-bold transition', activeTab === 'details' ? 'text-xieOrange border-b-2 border-xieOrange bg-orange-50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50']"
                >
                    商品詳情
                </button>
                <button
                    @click="activeTab = 'reviews'"
                    :class="['px-6 py-4 font-bold transition', activeTab === 'reviews' ? 'text-xieOrange border-b-2 border-xieOrange bg-orange-50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50']"
                >
                    商品評價 (0)
                </button>
            </div>
            <div class="p-8 text-gray-700 leading-relaxed min-h-[200px]">
                <div v-show="activeTab === 'details'">
                    <h3 class="text-lg font-bold mb-4">產品介紹</h3>
                    <p class="mb-4">{{ item.information || '此商品尚無詳細描述。' }}</p>
                </div>
                <div v-show="activeTab === 'reviews'">
                    <p class="text-gray-500">暫無評價。</p>
                </div>
            </div>
        </div>
    </main>

    <div v-else class="container mx-auto px-4 py-12 text-center">
        <div class="text-2xl text-gray-400 mb-4">載入中...</div>
    </div>
  </div>
</template>

<script>
import api from '../services/api'
import { formatPrice } from '../utils/currency'
import { useToast } from 'vue-toastification'
import { useCartStore } from '../stores/cart'

export default {
  name: 'ProductDetail',
  setup () {
    const toast = useToast()
    const cartStore = useCartStore()
    return { toast, cartStore }
  },
  data () {
    return {
      item: null,
      imgUrl: '',
      qty: 1,
      maxQty: 1,
      totalPrice: 0,
      activeTab: 'details',
      isFavorited: false
    }
  },
  created () {
    this.loadItemFromRoute()
    if (localStorage.getItem('token')) {
      this.checkWishlistStatus()
    }
  },
  watch: {
    '$route.params.id' (newId) {
      this.loadItemFromRoute(newId)
      if (localStorage.getItem('token')) {
        this.checkWishlistStatus()
      }
    }
  },
  methods: {
    formatPrice,
    toggleWishlist () {
      if (!localStorage.getItem('token')) {
        this.toast.warning('請先登入')
        return
      }

      if (this.isFavorited) {
        // Remove
        api.delete(`/favorites/${this.item.id}`)
          .then(() => {
            this.isFavorited = false
            this.toast.info('已取消收藏')
          })
      } else {
        // Add
        api.post('/favorites', { product_id: this.item.id })
          .then(() => {
            this.isFavorited = true
            this.toast.success('已加入收藏')
          })
      }
    },
    async checkWishlistStatus () {
      try {
        // Optimally we would check just this ID, but our API is list-based.
        // For now fetching list is okay unless user has thousands.
        // If performance issue, adds GET /favorites/check/{id} endpoint later.
        const res = await api.get('/favorites')
        const favorites = res.data
        // Assuming favorites returns list of products
        this.isFavorited = favorites.some(f => f.id === this.item.id)
      } catch (e) {
        console.error('Check wishlist error', e)
      }
    },
    preventInputArrows (e) {
      if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
        e.preventDefault()
      }
    },
    onQtyInput () {
      if (this.qty < 1) this.qty = 1
      if (this.qty > this.maxQty) this.qty = this.maxQty
      this.updateTotalPrice()
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
        this.toast.warning('請先登入')
        this.$router.push('/profile')
        return
      }

      try {
        await this.cartStore.addToCart(this.item.id, this.qty)
      } catch (error) {
        console.error('Add to cart error (ProductDetail):', error)
        const status = error.response?.status
        const backendMessage = error.response?.data?.message || error.response?.data?.error
        if (!backendMessage) {
          this.toast.error(`加入購物車失敗${status ? ` (狀態: ${status})` : ''}`)
        }
      }
    },
    buyNow () {
      if (!localStorage.getItem('token')) {
        this.toast.warning('請先登入')
        this.$router.push('/profile')
        return
      }
      // Logic: Add to cart then go to cart page
      api.post('/cart/items', {
        product_id: this.item.id,
        quantity: this.qty
      }).then(() => {
        this.$router.push('/car')
      }).catch(error => {
        console.error('Buy now error:', error)
        this.toast.error('購買失敗')
      })
    },
    async loadItemFromRoute (idParam) {
      const id = idParam || this.$route.params.id
      try {
        const response = await api.get(`/products/${id}`)
        this.item = response.data

        if (this.item.image) {
          if (this.item.image.startsWith('http')) {
            this.imgUrl = this.item.image
          } else {
            const baseUrl = api.defaults.baseURL.replace('/api', '')
            this.imgUrl = `${baseUrl}/images/${this.item.image}`
          }
        } else {
          this.imgUrl = ''
        }

        const stock = Number(this.item.stock || 0)
        this.maxQty = stock > 0 ? stock : 1
        this.qty = 1
        this.updateTotalPrice()

        // Check wish status again now that item is loaded?
        // No, checkWishlistStatus uses this.item.id, so check it only after item is loaded OR use route param.
        if (localStorage.getItem('token')) this.checkWishlistStatus()
      } catch (error) {
        console.error('Fetch product error:', error)
        this.item = null
      }
    },
    formatCategory (cat) {
      if (Array.isArray(cat)) return cat.join('、')
      if (cat && typeof cat === 'object') return cat.name || ''
      return cat
    }
  }
}
</script>

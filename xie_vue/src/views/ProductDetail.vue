<template>
  <div class="bg-wood-50 dark:bg-slate-900 min-h-screen pb-24 transition-colors duration-300">
    <!-- Breadcrumbs -->
    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm border-b border-stone-100 dark:border-slate-700/50 py-3 sticky top-0 z-10 transition-all duration-300">
      <div class="container mx-auto px-4 text-sm text-stone-500 dark:text-stone-400 flex items-center gap-2 tracking-wide">
        <router-link to="/" class="hover:text-xieOrange transition flex items-center gap-1">
          <i class="fas fa-home text-xs"></i> 首頁
        </router-link>
        <i class="fas fa-chevron-right text-[10px] text-stone-300 dark:text-slate-600"></i>
        <router-link to="/items" class="hover:text-xieOrange transition">{{ formatCategory(item?.category) || '商品' }}</router-link>
        <i class="fas fa-chevron-right text-[10px] text-stone-300 dark:text-slate-600"></i>
        <span class="text-slate-700 dark:text-stone-200 font-medium truncate max-w-[200px]">{{ item?.name }}</span>
      </div>
    </div>

    <main class="container mx-auto px-4 py-12" v-if="item">
      <!-- Main Product Card -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        
        <!-- Left: Image Gallery -->
        <div class="lg:col-span-5 space-y-5">
          <!-- Main Image -->
          <div class="relative group">
            <div 
              @click="openLightbox"
              class="aspect-square bg-white dark:bg-slate-800 rounded-2xl overflow-hidden border border-stone-100 dark:border-slate-700 shadow-xl shadow-stone-300/30 dark:shadow-black/30 cursor-zoom-in"
            >
              <div class="w-full h-full p-8 flex items-center justify-center">
                <img v-if="imgUrl" :src="imgUrl" :alt="item.name" class="max-w-full max-h-full object-contain transition-transform duration-500 group-hover:scale-105">
                <div v-else class="text-center text-stone-300 dark:text-slate-600">
                  <i class="fas fa-image text-6xl mb-2"></i>
                  <p class="text-sm">暫無圖片</p>
                </div>
              </div>
            </div>
            
            <!-- Wishlist Button -->
            <button 
              @click.stop="toggleWishlist"
              class="absolute top-4 right-4 w-12 h-12 rounded-full bg-white dark:bg-slate-700 shadow-lg flex items-center justify-center transition-all duration-300 hover:scale-110"
              :class="isFavorited ? 'text-rose-500' : 'text-stone-400 dark:text-stone-500 hover:text-rose-400'"
            >
              <i :class="isFavorited ? 'fas fa-heart' : 'far fa-heart'" class="text-xl"></i>
            </button>

            <!-- Category Badge -->
            <div class="absolute top-4 left-4 px-3 py-1.5 bg-slate-900/80 dark:bg-white/90 backdrop-blur-sm rounded-full text-xs font-bold text-white dark:text-slate-900 tracking-widest">
              {{ formatCategory(item.category) }}
            </div>

            <!-- Zoom Hint -->
            <div class="absolute bottom-4 right-4 px-2 py-1 bg-black/50 backdrop-blur-sm rounded text-xs text-white/70 opacity-0 group-hover:opacity-100 transition-opacity">
              <i class="fas fa-search-plus mr-1"></i>點擊放大
            </div>
          </div>

          <!-- Thumbnails -->
          <div class="flex gap-3 justify-center">
            <button 
              class="w-16 h-16 rounded-xl overflow-hidden transition-all"
              :class="selectedThumb === 0 ? 'ring-2 ring-xieOrange/60 bg-xieOrange/5' : 'border border-stone-200 dark:border-slate-600 hover:border-xieOrange/50'"
            >
              <div class="w-full h-full bg-stone-50 dark:bg-slate-700 p-1 flex items-center justify-center">
                <img v-if="imgUrl" :src="imgUrl" class="max-w-full max-h-full object-contain">
                <i v-else class="fas fa-image text-stone-300 dark:text-slate-500"></i>
              </div>
            </button>
            <button v-for="i in 2" :key="i" class="w-16 h-16 rounded-xl overflow-hidden border border-stone-200 dark:border-slate-600 opacity-40 cursor-not-allowed">
              <div class="w-full h-full bg-stone-100 dark:bg-slate-700 flex items-center justify-center">
                <i class="fas fa-plus text-stone-300 dark:text-slate-500"></i>
              </div>
            </button>
          </div>
        </div>

        <!-- Right: Product Info -->
        <div class="lg:col-span-7 flex flex-col">
          <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 border border-stone-100 dark:border-slate-700 shadow-xl shadow-stone-300/30 dark:shadow-black/30 flex-1">
            
            <!-- Product Title -->
            <div class="mb-8">
              <h1 class="text-2xl md:text-3xl font-bold text-slate-800 dark:text-stone-100 leading-tight mb-3">{{ item.name }}</h1>
              <div class="flex items-center gap-4 text-sm text-stone-400 dark:text-stone-500 tracking-widest">
                <span class="flex items-center gap-1.5"><i class="fas fa-barcode"></i> {{ item.id }}</span>
                <span class="w-1 h-1 rounded-full bg-stone-300 dark:bg-slate-600"></span>
                <span class="flex items-center gap-1.5"><i class="fas fa-tag"></i> {{ formatCategory(item.category) }}</span>
                <span v-if="item.rating_avg > 0" class="w-1 h-1 rounded-full bg-stone-300 dark:bg-slate-600"></span>
                <span v-if="item.rating_avg > 0" class="flex items-center gap-1 text-xieOrange">
                  <i class="fas fa-star"></i> {{ item.rating_avg?.toFixed(1) }} ({{ item.rating_count }})
                </span>
              </div>
            </div>

            <!-- Price Section - Left border design -->
            <div class="pl-5 mb-8 border-l-4 border-xieOrange">
              <div class="flex items-baseline gap-3">
                <span class="text-xs text-stone-400 dark:text-stone-500 opacity-70 writing-vertical-rl">特價</span>
                <span class="text-4xl md:text-5xl font-bold text-xieOrange tracking-tight">{{ formatPrice(item.price) }}</span>
                <span v-if="item.original_price" class="text-lg text-stone-400 dark:text-stone-500 line-through">{{ formatPrice(item.original_price) }}</span>
                <span v-if="item.original_price" class="px-2 py-1 bg-rose-500 text-white text-xs font-bold rounded">省 {{ formatPrice(item.original_price - item.price) }}</span>
              </div>
            </div>

            <!-- Trust Badges -->
            <div class="grid grid-cols-3 gap-3 mb-8">
              <div class="text-center p-3 rounded-xl bg-stone-50 dark:bg-slate-700/50 border border-stone-100 dark:border-slate-700">
                <i class="fas fa-shipping-fast text-xl text-emerald-500 mb-1"></i>
                <p class="text-xs font-semibold text-slate-700 dark:text-stone-200 tracking-wide">24h 快速出貨</p>
              </div>
              <div class="text-center p-3 rounded-xl bg-stone-50 dark:bg-slate-700/50 border border-stone-100 dark:border-slate-700">
                <i class="fas fa-shield-alt text-xl text-blue-500 mb-1"></i>
                <p class="text-xs font-semibold text-slate-700 dark:text-stone-200 tracking-wide">原廠正品保證</p>
              </div>
              <div class="text-center p-3 rounded-xl bg-stone-50 dark:bg-slate-700/50 border border-stone-100 dark:border-slate-700">
                <i class="fas fa-undo text-xl text-purple-500 mb-1"></i>
                <p class="text-xs font-semibold text-slate-700 dark:text-stone-200 tracking-wide">7天鑑賞期</p>
              </div>
            </div>

            <!-- Stock & Quantity -->
            <div class="space-y-4 mb-8 p-5 bg-stone-50 dark:bg-slate-700/30 rounded-xl">
              <div class="flex items-center justify-between">
                <span class="text-sm text-stone-500 dark:text-stone-400 tracking-wide">庫存狀態</span>
                <span class="flex items-center gap-2">
                  <span class="w-2 h-2 rounded-full animate-pulse" :class="item.stock > 10 ? 'bg-emerald-500' : item.stock > 0 ? 'bg-yellow-500' : 'bg-red-500'"></span>
                  <span class="font-semibold" :class="item.stock > 10 ? 'text-emerald-600 dark:text-emerald-400' : item.stock > 0 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400'">
                    {{ item.stock > 10 ? '現貨充足' : item.stock > 0 ? `僅剩 ${item.stock} 件` : '已售完' }}
                  </span>
                </span>
              </div>
              
              <div class="flex items-center justify-between">
                <span class="text-sm text-stone-500 dark:text-stone-400 tracking-wide">購買數量</span>
                <div class="flex items-center gap-3">
                  <div class="flex items-center bg-white dark:bg-slate-800 border-[1px] border-stone-200 dark:border-slate-600 rounded-lg overflow-hidden">
                    <button 
                      @click="decreaseQty" 
                      :disabled="qty <= 1"
                      class="w-10 h-10 flex items-center justify-center text-stone-500 dark:text-stone-400 hover:bg-stone-100 dark:hover:bg-slate-700 disabled:opacity-30 disabled:cursor-not-allowed transition"
                    >
                      <i class="fas fa-minus text-sm"></i>
                    </button>
                    <input 
                      type="number" 
                      v-model.number="qty" 
                      class="w-14 h-10 text-center font-bold text-slate-700 dark:text-stone-100 bg-transparent border-x-[1px] border-stone-200 dark:border-slate-600 focus:outline-none"
                      :min="1" 
                      :max="maxQty" 
                      @input="onQtyInput"
                    >
                    <button 
                      @click="increaseQty" 
                      :disabled="qty >= maxQty"
                      class="w-10 h-10 flex items-center justify-center text-stone-500 dark:text-stone-400 hover:bg-stone-100 dark:hover:bg-slate-700 disabled:opacity-30 disabled:cursor-not-allowed transition"
                    >
                      <i class="fas fa-plus text-sm"></i>
                    </button>
                  </div>
                </div>
              </div>

              <div class="flex items-center justify-between pt-4 border-t border-dashed border-stone-200 dark:border-slate-600">
                <span class="text-sm text-stone-500 dark:text-stone-400 tracking-wide">小計</span>
                <span class="text-2xl font-bold text-xieOrange">{{ formatPrice(totalPrice) }}</span>
              </div>
            </div>

            <!-- Action Buttons - Cart wider -->
            <div class="flex gap-3">
              <button 
                @click="addToCart" 
                class="flex-[2] py-4 px-6 bg-xieOrange text-white font-bold rounded-xl hover:bg-[#cf8354] transition-all duration-300 shadow-lg shadow-xieOrange/30 hover:shadow-xl hover:shadow-xieOrange/40 hover:-translate-y-0.5 flex items-center justify-center gap-2"
              >
                <i class="fas fa-cart-plus"></i>
                加入購物車
              </button>
              <button 
                @click="buyNow" 
                class="flex-1 py-4 px-6 border-2 border-slate-700 dark:border-stone-300 text-slate-700 dark:text-stone-200 font-bold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-300 hover:-translate-y-0.5 flex items-center justify-center gap-2"
              >
                <i class="fas fa-bolt"></i>
                直接購買
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Product Details Tabs -->
      <div class="mt-12 bg-white dark:bg-slate-800 rounded-2xl border border-stone-100 dark:border-slate-700 overflow-hidden shadow-xl shadow-stone-300/30 dark:shadow-black/30">
        <!-- Tab Headers - No background, bottom border only -->
        <div class="flex border-b border-stone-100 dark:border-slate-700">
          <button
            @click="activeTab = 'details'"
            class="flex-1 px-6 py-5 font-semibold transition-all relative"
            :class="activeTab === 'details' ? 'text-xieOrange' : 'text-stone-400 dark:text-stone-500 opacity-50 hover:opacity-80'"
          >
            <i class="fas fa-info-circle mr-2"></i>商品詳情
            <div v-if="activeTab === 'details'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-xieOrange"></div>
          </button>
          <button
            @click="activeTab = 'specs'"
            class="flex-1 px-6 py-5 font-semibold transition-all relative"
            :class="activeTab === 'specs' ? 'text-xieOrange' : 'text-stone-400 dark:text-stone-500 opacity-50 hover:opacity-80'"
          >
            <i class="fas fa-list-alt mr-2"></i>規格說明
            <div v-if="activeTab === 'specs'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-xieOrange"></div>
          </button>
          <button
            @click="activeTab = 'reviews'"
            class="flex-1 px-6 py-5 font-semibold transition-all relative"
            :class="activeTab === 'reviews' ? 'text-xieOrange' : 'text-stone-400 dark:text-stone-500 opacity-50 hover:opacity-80'"
          >
            <i class="fas fa-star mr-2"></i>商品評價 <span class="text-xs ml-1">({{ reviews.length }})</span>
            <div v-if="activeTab === 'reviews'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-xieOrange"></div>
          </button>
        </div>
        
        <!-- Tab Content -->
        <div class="p-8 min-h-[250px]">
          <div v-show="activeTab === 'details'" class="prose dark:prose-invert max-w-none">
            <h3 class="text-xl font-bold text-slate-700 dark:text-stone-100 mb-4 flex items-center gap-2">
              <i class="fas fa-book-open text-xieOrange"></i> 產品介紹
            </h3>
            <p class="text-stone-600 dark:text-stone-300 leading-relaxed">{{ item.information || '此商品尚無詳細描述。歡迎聯繫客服了解更多資訊。' }}</p>
          </div>
          
          <div v-show="activeTab === 'specs'" class="space-y-3">
            <h3 class="text-xl font-bold text-slate-700 dark:text-stone-100 mb-4 flex items-center gap-2">
              <i class="fas fa-cogs text-xieOrange"></i> 規格說明
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="flex justify-between py-3 border-b border-dashed border-stone-100 dark:border-slate-700">
                <span class="text-stone-500 dark:text-stone-400">商品編號</span>
                <span class="font-mono font-medium text-slate-700 dark:text-stone-200">{{ item.id }}</span>
              </div>
              <div class="flex justify-between py-3 border-b border-dashed border-stone-100 dark:border-slate-700">
                <span class="text-stone-500 dark:text-stone-400">商品分類</span>
                <span class="font-medium text-slate-700 dark:text-stone-200">{{ formatCategory(item.category) }}</span>
              </div>
              <div class="flex justify-between py-3 border-b border-dashed border-stone-100 dark:border-slate-700">
                <span class="text-stone-500 dark:text-stone-400">庫存數量</span>
                <span class="font-medium text-slate-700 dark:text-stone-200">{{ item.stock }} 件</span>
              </div>
              <div class="flex justify-between py-3 border-b border-dashed border-stone-100 dark:border-slate-700">
                <span class="text-stone-500 dark:text-stone-400">平均評分</span>
                <span class="font-medium text-xieOrange flex items-center gap-1">
                  <i class="fas fa-star"></i> {{ item.rating_avg?.toFixed(1) || '0.0' }}
                </span>
              </div>
            </div>
          </div>
          
          <!-- Reviews Section -->
          <div v-show="activeTab === 'reviews'">
            <!-- Review Form -->
            <div v-if="canReview" class="mb-8 p-6 bg-stone-50 dark:bg-slate-700/30 rounded-xl border border-stone-100 dark:border-slate-700">
              <h4 class="font-semibold text-slate-700 dark:text-stone-100 mb-4">撰寫評價</h4>
              <div class="flex items-center gap-2 mb-4">
                <span class="text-sm text-stone-500 dark:text-stone-400 mr-2">評分：</span>
                <button 
                  v-for="star in 5" 
                  :key="star" 
                  @click="newReview.rating = star"
                  class="text-2xl transition-transform hover:scale-110"
                  :class="star <= newReview.rating ? 'text-xieOrange' : 'text-stone-300 dark:text-slate-600'"
                >
                  <i :class="star <= newReview.rating ? 'fas fa-star' : 'far fa-star'"></i>
                </button>
              </div>
              <textarea 
                v-model="newReview.comment"
                class="w-full p-4 border border-stone-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-stone-100 focus:outline-none focus:ring-2 focus:ring-xieOrange/50 resize-none"
                rows="3"
                placeholder="分享您的使用心得..."
              ></textarea>
              <div class="flex justify-end mt-3">
                <button 
                  @click="submitReview"
                  :disabled="newReview.rating === 0 || submittingReview"
                  class="px-6 py-2 bg-xieOrange text-white font-semibold rounded-lg hover:bg-[#cf8354] disabled:opacity-50 disabled:cursor-not-allowed transition"
                >
                  {{ submittingReview ? '送出中...' : '送出評價' }}
                </button>
              </div>
            </div>

            <!-- Reviews List -->
            <div v-if="reviews.length > 0" class="space-y-6">
              <div v-for="review in reviews" :key="review.id" class="pb-6 border-b border-stone-100 dark:border-slate-700 last:border-0">
                <div class="flex items-start justify-between mb-2">
                  <div>
                    <span class="font-semibold text-slate-700 dark:text-stone-100">{{ review.user?.name || '匿名用戶' }}</span>
                    <div class="flex items-center gap-1 mt-1">
                      <i v-for="star in 5" :key="star" class="text-sm" 
                         :class="star <= review.rating ? 'fas fa-star text-xieOrange' : 'far fa-star text-stone-300 dark:text-slate-600'"></i>
                    </div>
                  </div>
                  <span class="font-mono text-xs text-stone-400 dark:text-stone-500 opacity-50">{{ formatRelativeTime(review.created_at) }}</span>
                </div>
                <p v-if="review.comment" class="text-stone-600 dark:text-stone-300 mt-2">{{ review.comment }}</p>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
              <svg class="w-24 h-24 mx-auto mb-4 text-stone-200 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              </svg>
              <p class="text-stone-500 dark:text-stone-400">暫無評價</p>
              <p class="text-sm text-stone-400 dark:text-stone-500 mt-1">購買後歡迎留下您的寶貴意見！</p>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Loading State -->
    <div v-else class="container mx-auto px-4 py-24 text-center">
      <div class="inline-flex items-center gap-3 text-stone-400 dark:text-stone-500">
        <i class="fas fa-spinner fa-spin text-2xl"></i>
        <span class="text-xl">載入中...</span>
      </div>
    </div>

    <!-- Image Lightbox -->
    <Teleport to="body">
      <Transition name="fade">
        <div 
          v-if="lightboxOpen" 
          @click="closeLightbox"
          class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-xl p-8"
        >
          <button 
            class="absolute top-6 right-6 w-12 h-12 rounded-full bg-white/10 text-white flex items-center justify-center hover:bg-white/20 transition"
            @click="closeLightbox"
          >
            <i class="fas fa-times text-xl"></i>
          </button>
          <img 
            v-if="imgUrl" 
            :src="imgUrl" 
            :alt="item?.name" 
            class="max-w-full max-h-full object-contain"
            @click.stop
          >
        </div>
      </Transition>
    </Teleport>
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
      isFavorited: false,
      selectedThumb: 0,
      lightboxOpen: false,
      reviews: [],
      canReview: false,
      newReview: {
        rating: 0,
        comment: ''
      },
      submittingReview: false
    }
  },
  created () {
    this.loadItemFromRoute()
    if (localStorage.getItem('auth_token')) {
      this.checkWishlistStatus()
    }
  },
  watch: {
    '$route.params.id' (newId) {
      this.loadItemFromRoute(newId)
      if (localStorage.getItem('auth_token')) {
        this.checkWishlistStatus()
      }
    }
  },
  methods: {
    formatPrice,
    formatRelativeTime (dateString) {
      const date = new Date(dateString)
      const now = new Date()
      const diff = now - date
      const days = Math.floor(diff / (1000 * 60 * 60 * 24))
      
      if (days === 0) return '今天'
      if (days === 1) return '昨天'
      if (days < 7) return `${days}天前`
      if (days < 30) return `${Math.floor(days / 7)}週前`
      if (days < 365) return `${Math.floor(days / 30)}個月前`
      
      // Fallback to YYYY.MM.DD format
      return date.toLocaleDateString('zh-TW', { year: 'numeric', month: '2-digit', day: '2-digit' }).replace(/\//g, '.')
    },
    openLightbox () {
      if (this.imgUrl) {
        this.lightboxOpen = true
        document.body.style.overflow = 'hidden'
      }
    },
    closeLightbox () {
      this.lightboxOpen = false
      document.body.style.overflow = ''
    },
    async loadReviews () {
      try {
        const res = await api.get(`/products/${this.item.id}/reviews`)
        this.reviews = res.data.data || res.data
      } catch (e) {
        console.error('Load reviews error', e)
      }
    },
    async checkCanReview () {
      if (!localStorage.getItem('auth_token')) {
        this.canReview = false
        return
      }
      try {
        const res = await api.get(`/products/${this.item.id}/reviews/can-review`)
        this.canReview = res.data.can_review
      } catch (e) {
        this.canReview = false
      }
    },
    async submitReview () {
      if (this.newReview.rating === 0) return
      
      this.submittingReview = true
      try {
        await api.post(`/products/${this.item.id}/reviews`, {
          rating: this.newReview.rating,
          comment: this.newReview.comment
        })
        this.toast.success('評價已送出')
        this.newReview = { rating: 0, comment: '' }
        this.canReview = false
        await this.loadReviews()
        await this.loadItemFromRoute() // Refresh rating cache
      } catch (e) {
        const msg = e.response?.data?.message || e.response?.data?.errors?.product?.[0] || '送出失敗'
        this.toast.error(msg)
      } finally {
        this.submittingReview = false
      }
    },
    toggleWishlist () {
      if (!localStorage.getItem('auth_token')) {
        this.toast.warning('請先登入')
        return
      }

      if (this.isFavorited) {
        api.delete(`/favorites/${this.item.id}`)
          .then(() => {
            this.isFavorited = false
            this.toast.info('已取消收藏')
          })
      } else {
        api.post('/favorites', { product_id: this.item.id })
          .then(() => {
            this.isFavorited = true
            this.toast.success('已加入收藏')
          })
      }
    },
    async checkWishlistStatus () {
      try {
        const res = await api.get('/favorites')
        const favorites = res.data
        this.isFavorited = favorites.some(f => f.id === this.item?.id)
      } catch (e) {
        console.error('Check wishlist error', e)
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
      if (!localStorage.getItem('auth_token')) {
        this.toast.warning('請先登入')
        this.$router.push('/login')
        return
      }

      try {
        await this.cartStore.addToCart(this.item.id, this.qty)
      } catch (error) {
        console.error('Add to cart error:', error)
        const status = error.response?.status
        const backendMessage = error.response?.data?.message || error.response?.data?.error
        if (!backendMessage) {
          this.toast.error(`加入購物車失敗${status ? ` (${status})` : ''}`)
        }
      }
    },
    buyNow () {
      if (!localStorage.getItem('auth_token')) {
        this.toast.warning('請先登入')
        this.$router.push('/login')
        return
      }
      api.post('/cart/items', {
        product_id: this.item.id,
        quantity: this.qty
      }).then(() => {
        window.dispatchEvent(new Event('cart:updated'))
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

        // Load reviews and check if user can review
        await this.loadReviews()
        await this.checkCanReview()

        if (localStorage.getItem('auth_token')) this.checkWishlistStatus()
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

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.writing-vertical-rl {
  writing-mode: vertical-rl;
  text-orientation: mixed;
}
</style>

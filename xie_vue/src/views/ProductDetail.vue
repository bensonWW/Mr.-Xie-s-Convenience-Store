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
                <span v-if="parseFloat(item.rating_avg) > 0" class="w-1 h-1 rounded-full bg-stone-300 dark:bg-slate-600"></span>
                <span v-if="parseFloat(item.rating_avg) > 0" class="flex items-center gap-1 text-xieOrange">
                  <i class="fas fa-star"></i> {{ parseFloat(item.rating_avg || 0).toFixed(1) }} ({{ item.rating_count }})
                </span>
              </div>
            </div>

            <!-- Price Section - Left border design -->
            <div class="pl-5 mb-8 border-l-4 border-xieOrange">
              <div class="flex items-baseline gap-3">
                <span class="text-xs text-stone-400 dark:text-stone-500 opacity-70 writing-vertical-rl">{{ item.has_variants ? '起' : '特價' }}</span>
                <span class="text-4xl md:text-5xl font-bold text-xieOrange tracking-tight">{{ formatPrice(displayPrice) }}</span>
                <span v-if="displayOriginalPrice && displayOriginalPrice > displayPrice" class="text-lg text-stone-400 dark:text-stone-500 line-through">{{ formatPrice(displayOriginalPrice) }}</span>
                <span v-if="displayOriginalPrice && displayOriginalPrice > displayPrice" class="px-2 py-1 bg-rose-500 text-white text-xs font-bold rounded">省 {{ formatPrice(displayOriginalPrice - displayPrice) }}</span>
              </div>
              <div v-if="item.has_variants && item.price_range" class="text-sm text-stone-500 dark:text-stone-400 mt-1">
                價格區間: {{ item.price_range }}
              </div>
            </div>

            <!-- Variant Selector (only for products with variants) -->
            <div v-if="item.has_variants && item.attributes?.length > 0" class="mb-8">
              <VariantSelector
                :attributes="item.attributes"
                :variants="item.variants"
                :valid-combinations="validCombinations"
                :in-stock-combinations="inStockCombinations"
                :default-variant="item.default_variant"
                @variant-selected="onVariantSelected"
              />
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
                  <span class="w-2 h-2 rounded-full animate-pulse" :class="displayStock > 10 ? 'bg-emerald-500' : displayStock > 0 ? 'bg-yellow-500' : 'bg-red-500'"></span>
                  <span class="font-semibold" :class="displayStock > 10 ? 'text-emerald-600 dark:text-emerald-400' : displayStock > 0 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400'">
                    {{ displayStock > 10 ? '現貨充足' : displayStock > 0 ? `僅剩 ${displayStock} 件` : '已售完' }}
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
        <!-- Tab Headers - Pill Style -->
        <div class="flex gap-2 p-4 border-b border-stone-100 dark:border-slate-700 bg-stone-50/50 dark:bg-slate-800/50">
          <button
            @click="activeTab = 'details'"
            class="flex-1 px-5 py-3 font-medium text-sm rounded-xl transition-all"
            :class="activeTab === 'details' ? 'bg-white dark:bg-slate-700 text-xieOrange shadow-sm border border-xieOrange/20' : 'text-stone-500 dark:text-stone-400 hover:bg-white/50 dark:hover:bg-slate-700/50'"
          >
            <i class="fas fa-info-circle mr-2"></i>商品詳情
          </button>
          <button
            @click="activeTab = 'specs'"
            class="flex-1 px-5 py-3 font-medium text-sm rounded-xl transition-all"
            :class="activeTab === 'specs' ? 'bg-white dark:bg-slate-700 text-xieOrange shadow-sm border border-xieOrange/20' : 'text-stone-500 dark:text-stone-400 hover:bg-white/50 dark:hover:bg-slate-700/50'"
          >
            <i class="fas fa-list-alt mr-2"></i>規格說明
          </button>
          <button
            @click="activeTab = 'reviews'"
            class="flex-1 px-5 py-3 font-medium text-sm rounded-xl transition-all"
            :class="activeTab === 'reviews' ? 'bg-white dark:bg-slate-700 text-xieOrange shadow-sm border border-xieOrange/20' : 'text-stone-500 dark:text-stone-400 hover:bg-white/50 dark:hover:bg-slate-700/50'"
          >
            <i class="fas fa-star mr-2"></i>商品評價 <span class="text-xs ml-1 opacity-70">({{ reviews.length }})</span>
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
                  <i class="fas fa-star"></i> {{ parseFloat(item.rating_avg || 0).toFixed(1) }}
                </span>
              </div>
            </div>
          </div>
          
          <!-- Reviews Section -->
          <div v-show="activeTab === 'reviews'">
            <!-- Rating Summary -->
            <div v-if="reviews.length > 0" class="mb-8 p-6 bg-gradient-to-r from-xieOrange/5 to-amber-50 dark:from-xieOrange/10 dark:to-slate-800/50 rounded-xl border border-xieOrange/20">
              <div class="flex items-center gap-6">
                <div class="text-center">
                  <div class="text-4xl font-bold text-xieOrange">{{ averageRating }}</div>
                  <div class="flex items-center justify-center gap-0.5 mt-1">
                    <i v-for="star in 5" :key="star" class="text-sm"
                       :class="star <= Math.round(parseFloat(averageRating)) ? 'fas fa-star text-xieOrange' : 'far fa-star text-stone-300 dark:text-slate-600'"></i>
                  </div>
                  <div class="text-xs text-stone-500 dark:text-stone-400 mt-1">{{ reviews.length }} 則評價</div>
                </div>
                <div class="flex-1 space-y-1">
                  <div v-for="n in 5" :key="n" class="flex items-center gap-2">
                    <span class="text-xs text-stone-500 dark:text-stone-400 w-4">{{ 6 - n }}</span>
                    <i class="fas fa-star text-xs text-xieOrange"></i>
                    <div class="flex-1 h-2 bg-stone-200 dark:bg-slate-700 rounded-full overflow-hidden">
                      <div class="h-full bg-xieOrange rounded-full transition-all"
                           :style="{ width: getRatingPercentage(6 - n) + '%' }"></div>
                    </div>
                    <span class="text-xs text-stone-400 dark:text-stone-500 w-8 text-right">{{ getRatingCount(6 - n) }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Review Form -->
            <div v-if="canReview" class="mb-8 p-6 bg-stone-50 dark:bg-slate-700/30 rounded-xl border border-stone-100 dark:border-slate-700">
              <h4 class="font-semibold text-slate-700 dark:text-stone-100 mb-4">撰寫評價</h4>
              <div class="flex items-center gap-2 mb-4">
                <span class="text-sm text-stone-500 dark:text-stone-400 mr-2">評分：</span>
                <button 
                  v-for="star in 5" 
                  :key="star" 
                  @click="newReview.rating = star"
                  class="text-2xl transition-transform hover:scale-110 bg-transparent border-0 p-0 cursor-pointer focus:outline-none"
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

            <!-- Review Eligibility Message -->
            <div v-else-if="!canReview && reviewReason" class="mb-8 p-5 bg-stone-50 dark:bg-slate-700/30 rounded-xl border border-stone-100 dark:border-slate-700">
              <div v-if="reviewReason === 'not_logged_in'" class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center text-amber-600 dark:text-amber-400">
                  <i class="fas fa-user-lock text-xl"></i>
                </div>
                <div>
                  <p class="font-semibold text-slate-700 dark:text-stone-100">請先登入</p>
                  <p class="text-sm text-stone-500 dark:text-stone-400">登入後即可查看您的評價資格</p>
                </div>
                <router-link to="/login" class="ml-auto px-4 py-2 bg-xieOrange text-white text-sm font-medium rounded-lg hover:bg-[#cf8354] transition">
                  立即登入
                </router-link>
              </div>
              <div v-else-if="reviewReason === 'not_purchased'" class="flex items-center gap-4">
                <div class="w-12 h-12 bg-sky-100 dark:bg-sky-900/30 rounded-full flex items-center justify-center text-sky-600 dark:text-sky-400">
                  <i class="fas fa-shopping-bag text-xl"></i>
                </div>
                <div>
                  <p class="font-semibold text-slate-700 dark:text-stone-100">尚未購買此商品</p>
                  <p class="text-sm text-stone-500 dark:text-stone-400">購買並完成訂單後即可撰寫評價</p>
                </div>
              </div>
              <div v-else-if="reviewReason === 'already_reviewed'" class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                  <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div>
                  <p class="font-semibold text-slate-700 dark:text-stone-100">您已評價過此商品</p>
                  <p class="text-sm text-stone-500 dark:text-stone-400">每位會員僅能評價一次</p>
                </div>
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
import VariantSelector from '../components/VariantSelector.vue'

export default {
  name: 'ProductDetail',
  components: {
    VariantSelector
  },
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
      reviewReason: null,
      newReview: {
        rating: 0,
        comment: ''
      },
      submittingReview: false,
      // Variant selection
      validCombinations: [],
      inStockCombinations: [],
      selectedVariant: null
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
        this.reviewReason = 'not_logged_in'
        return
      }
      try {
        const res = await api.get(`/products/${this.item.id}/reviews/can-review`)
        this.canReview = res.data.can_review
        this.reviewReason = res.data.reason || null
      } catch (e) {
        this.canReview = false
        this.reviewReason = 'error'
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
      const price = this.selectedVariant?.price ?? this.item?.price ?? 0
      this.totalPrice = this.qty * price
    },
    onVariantSelected (variant) {
      this.selectedVariant = variant
      if (variant) {
        this.maxQty = variant.stock > 0 ? variant.stock : 1
        if (this.qty > this.maxQty) this.qty = this.maxQty
        // Update displayed image if variant has one
        if (variant.image) {
          const baseUrl = api.defaults.baseURL.replace('/api', '')
          this.imgUrl = variant.image.startsWith('http') ? variant.image : `${baseUrl}/images/${variant.image}`
        }
      } else {
        this.maxQty = this.item?.stock > 0 ? this.item.stock : 1
      }
      this.updateTotalPrice()
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

      // Check if product has variants but none selected
      if (this.item?.has_variants && !this.selectedVariant) {
        this.toast.warning('請選擇商品規格')
        return
      }

      // Check stock
      const stock = this.selectedVariant?.stock ?? this.item?.stock ?? 0
      if (stock <= 0) {
        this.toast.error('此規格目前缺貨')
        return
      }

      try {
        await this.cartStore.addToCart(this.item.id, this.qty, this.selectedVariant?.id)
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
        // Handle new response format with valid_combinations
        const data = response.data
        this.item = data.product || data
        this.validCombinations = data.valid_combinations || []
        this.inStockCombinations = data.in_stock_combinations || []
        this.selectedVariant = null

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

        // If product has variants, use default variant or first variant
        if (this.item.has_variants && this.item.variants?.length > 0) {
          const defaultVariant = this.item.default_variant || this.item.variants.find(v => v.is_default) || this.item.variants[0]
          this.selectedVariant = defaultVariant
          this.maxQty = defaultVariant?.stock > 0 ? defaultVariant.stock : 1
        } else {
          const stock = Number(this.item.stock || 0)
          this.maxQty = stock > 0 ? stock : 1
        }
        
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
    },
    // Rating helpers
    getRatingCount (rating) {
      return this.reviews.filter(r => r.rating === rating).length
    },
    getRatingPercentage (rating) {
      if (this.reviews.length === 0) return 0
      return (this.getRatingCount(rating) / this.reviews.length) * 100
    }
  },
  computed: {
    averageRating () {
      if (this.reviews.length === 0) return '0.0'
      const sum = this.reviews.reduce((acc, r) => acc + r.rating, 0)
      return (sum / this.reviews.length).toFixed(1)
    },
    displayPrice () {
      if (this.selectedVariant) {
        return this.selectedVariant.price
      }
      if (this.item?.has_variants && this.item?.price_min) {
        return this.item.price_min
      }
      return this.item?.price || 0
    },
    displayOriginalPrice () {
      if (this.selectedVariant) {
        return this.selectedVariant.original_price
      }
      return this.item?.original_price || null
    },
    displayStock () {
      if (this.selectedVariant) {
        return this.selectedVariant.stock
      }
      if (this.item?.has_variants) {
        // Sum of all variant stocks
        return this.item.variants?.reduce((sum, v) => sum + v.stock, 0) || 0
      }
      return this.item?.stock || 0
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

/* Hide number input spinners */
input[type='number']::-webkit-outer-spin-button,
input[type='number']::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type='number'] {
  -moz-appearance: textfield;
}
</style>

<template>
  <div class="space-y-6">
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-stone-100 dark:border-slate-700 p-6 transition-colors duration-300">
      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-slate-700 dark:text-stone-100 flex items-center">
          我的收藏 
          <span class="bg-xieOrange/10 text-xieOrange text-xs px-2.5 py-1 rounded-full ml-2 font-medium">{{ wishlist.length }}</span>
        </h2>
        <button class="text-sm text-rose-500 hover:text-rose-600 transition" @click="clearInvalidWishlistItems">
          <i class="fas fa-trash-alt mr-1"></i> 清空失效商品
        </button>
      </div>

      <!-- Product Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div 
          v-for="item in wishlist" 
          :key="item.id"
          class="border border-stone-200 dark:border-slate-600 rounded-xl p-4 relative hover:shadow-lg dark:hover:shadow-slate-900/50 transition group"
          :class="item.status === 'available' ? 'bg-white dark:bg-slate-700' : 'bg-stone-50 dark:bg-slate-800 opacity-80'"
        >
          <!-- Remove Button -->
          <button 
            class="absolute top-3 right-3 w-8 h-8 rounded-full bg-stone-100 dark:bg-slate-600 text-stone-400 hover:bg-rose-100 hover:text-rose-500 dark:hover:bg-rose-900/30 transition flex items-center justify-center z-10" 
            @click="removeFromWishlist(item.id)"
          >
            <i class="fas fa-times text-sm"></i>
          </button>

          <!-- Product Image -->
          <router-link :to="`/product/${item.id}`" class="block relative mb-3">
            <div class="w-full h-40 bg-stone-100 dark:bg-slate-600 rounded-xl flex items-center justify-center text-stone-300 dark:text-stone-500 overflow-hidden relative">
              <img 
                v-if="item.image" 
                :src="item.image" 
                :alt="item.name" 
                class="w-full h-full object-cover"
                :class="{'opacity-50': item.status !== 'available'}"
              >
              <i v-else :class="[item.icon_class + ' text-5xl', {'opacity-50': item.status !== 'available'}]"></i>
              <div v-if="item.status !== 'available'" class="absolute inset-0 bg-black/40 flex items-center justify-center">
                <span class="bg-slate-800 text-white px-3 py-1.5 text-sm rounded-full font-medium">補貨中</span>
              </div>
            </div>
            <div v-if="item.tag" class="absolute bottom-2 left-2 bg-rose-500 text-white text-xs px-2 py-1 rounded-full font-medium">
              <i class="fas fa-arrow-down mr-1"></i>{{ item.tag }}
            </div>
          </router-link>

          <!-- Product Info -->
          <h3 
            class="font-semibold text-sm line-clamp-2 h-10 mb-2" 
            :class="item.status === 'available' ? 'text-slate-700 dark:text-stone-100 group-hover:text-xieOrange transition' : 'text-stone-500 dark:text-stone-400'"
          >
            <router-link :to="`/product/${item.id}`">{{ item.name }}</router-link>
          </h3>
          <div class="flex items-end gap-2 mb-4">
            <span 
              class="text-xl font-bold" 
              :class="item.status === 'available' ? 'text-xieOrange' : 'text-stone-500 dark:text-stone-400'"
            >
              {{ formatPrice(item.price) }}
            </span>
            <span v-if="item.original_price" class="text-xs text-stone-400 line-through mb-1">{{ formatPrice(item.original_price) }}</span>
          </div>

          <!-- Action Button -->
          <button 
            v-if="item.status === 'available'" 
            class="w-full bg-xieOrange text-white py-2.5 rounded-full font-medium hover:bg-[#cf8354] transition flex items-center justify-center gap-2" 
            @click="addToCartFromWishlist(item)"
          >
            <i class="fas fa-cart-plus"></i> 加入購物車
          </button>
          <button 
            v-else 
            class="w-full border border-stone-300 dark:border-slate-500 text-stone-600 dark:text-stone-300 py-2.5 rounded-full font-medium hover:bg-stone-100 dark:hover:bg-slate-600 transition flex items-center justify-center gap-2"
          >
            <i class="far fa-bell"></i> 到貨通知
          </button>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="wishlist.length === 0" class="text-center py-16 text-stone-400 dark:text-stone-500">
        <i class="far fa-heart text-5xl mb-4 opacity-50"></i>
        <p class="font-medium">暫無收藏商品</p>
        <p class="text-sm mt-1">將喜歡的商品加入追蹤清單</p>
      </div>
    </div>
  </div>
</template>

<script>
import { useToast } from 'vue-toastification'
import api from '../../services/api'
import { formatPrice } from '../../utils/currency'

export default {
  name: 'WishlistGrid',
  props: {
    wishlist: {
      type: Array,
      default: () => []
    }
  },
  emits: ['remove', 'add-to-cart'],
  setup () {
    const toast = useToast()
    return { toast }
  },
  methods: {
    formatPrice,
    async removeFromWishlist (id) {
      if (confirm('確定要移除此商品？')) {
        try {
          await api.delete(`/favorites/${id}`)
          this.$emit('remove', id)
          this.toast.info('已移除商品')
        } catch (error) {
          console.error('Remove wishlist error:', error)
          this.toast.error('移除失敗')
        }
      }
    },
    addToCartFromWishlist (item) {
      this.$emit('add-to-cart', item)
    },
    clearInvalidWishlistItems () {
      if (confirm('確定要清空所有失效商品？')) {
        this.toast.info('功能開發中')
      }
    }
  }
}
</script>

<template>
  <!-- Overlay -->
  <Transition name="fade">
    <div 
      v-if="visible" 
      class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50"
      @click="close"
    ></div>
  </Transition>

  <!-- Drawer -->
  <Transition name="slide-right">
    <div 
      v-if="visible"
      class="fixed right-0 top-0 h-full w-full max-w-md bg-white/95 dark:bg-slate-800/95 backdrop-blur-md shadow-2xl z-50 flex flex-col border-l border-stone-200 dark:border-slate-700 transition-colors duration-300"
    >
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b border-stone-100 dark:border-slate-700">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-xieOrange/10 rounded-full flex items-center justify-center">
            <i class="fas fa-shopping-bag text-xieOrange"></i>
          </div>
          <div>
            <h2 class="font-semibold text-slate-700 dark:text-stone-100">購物車</h2>
            <p class="text-xs text-stone-500 dark:text-stone-400">{{ cartCount }} 件商品</p>
          </div>
        </div>
        <button 
          @click="close"
          class="w-10 h-10 rounded-full hover:bg-stone-100 dark:hover:bg-slate-700 flex items-center justify-center text-stone-400 hover:text-stone-600 dark:hover:text-stone-200 transition"
        >
          <i class="fas fa-times text-lg"></i>
        </button>
      </div>

      <!-- Cart Items -->
      <div class="flex-1 overflow-y-auto p-6">
        <div v-if="cartItems.length === 0" class="text-center py-16">
          <i class="fas fa-shopping-bag text-5xl text-stone-200 dark:text-slate-600 mb-4"></i>
          <p class="text-stone-500 dark:text-stone-400">購物車是空的</p>
          <button 
            @click="goShopping"
            class="mt-4 text-xieOrange font-medium hover:underline"
          >
            開始購物
          </button>
        </div>

        <div v-else class="space-y-4">
          <div 
            v-for="item in cartItems" 
            :key="item.id"
            class="flex gap-4 p-4 bg-stone-50 dark:bg-slate-700/50 rounded-xl"
          >
            <img 
              :src="item.product?.image || 'https://via.placeholder.com/80'" 
              :alt="item.product?.name"
              class="w-20 h-20 object-cover rounded-lg"
            >
            <div class="flex-1 min-w-0">
              <h3 class="font-medium text-slate-700 dark:text-stone-100 text-sm line-clamp-2 mb-1">
                {{ item.product?.name }}
              </h3>
              <p class="text-xieOrange font-bold text-sm">
                {{ formatPrice(item.product?.price) }}
              </p>
              <div class="flex items-center gap-2 mt-2">
                <button 
                  @click="decreaseQty(item)"
                  class="w-7 h-7 rounded-full border border-stone-200 dark:border-slate-600 flex items-center justify-center text-stone-500 hover:border-xieOrange hover:text-xieOrange transition"
                >
                  <i class="fas fa-minus text-xs"></i>
                </button>
                <span class="text-sm font-medium text-slate-700 dark:text-stone-100 w-6 text-center">{{ item.quantity }}</span>
                <button 
                  @click="increaseQty(item)"
                  class="w-7 h-7 rounded-full border border-stone-200 dark:border-slate-600 flex items-center justify-center text-stone-500 hover:border-xieOrange hover:text-xieOrange transition"
                >
                  <i class="fas fa-plus text-xs"></i>
                </button>
              </div>
            </div>
            <button 
              @click="removeItem(item)"
              class="text-stone-400 hover:text-red-500 transition self-start"
            >
              <i class="fas fa-trash-alt text-sm"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div v-if="cartItems.length > 0" class="p-6 border-t border-stone-100 dark:border-slate-700 bg-white/50 dark:bg-slate-800/50">
        <div class="flex justify-between items-center mb-4">
          <span class="text-stone-600 dark:text-stone-400">小計</span>
          <span class="text-xl font-bold text-slate-700 dark:text-stone-100">{{ formatPrice(cartTotal) }}</span>
        </div>
        <button 
          @click="goToCheckout"
          class="w-full bg-xieOrange text-white py-3 rounded-full font-medium hover:bg-[#cf8354] transition shadow-lg shadow-xieOrange/20"
        >
          前往結帳
        </button>
        <button 
          @click="close"
          class="w-full mt-2 text-stone-500 dark:text-stone-400 py-2 text-sm hover:text-slate-700 dark:hover:text-stone-200 transition"
        >
          繼續購物
        </button>
      </div>
    </div>
  </Transition>
</template>

<script>
import { useCartStore } from '@/stores/cart'
import { formatPrice } from '@/utils/currency'

export default {
  name: 'CartDrawer',
  props: {
    visible: {
      type: Boolean,
      default: false
    }
  },
  emits: ['close'],
  setup () {
    const cartStore = useCartStore()
    return { cartStore }
  },
  computed: {
    cartItems () {
      return this.cartStore.cartItems || []
    },
    cartCount () {
      return this.cartStore.cartCount || 0
    },
    cartTotal () {
      return this.cartStore.cartTotal || 0
    }
  },
  methods: {
    formatPrice,
    close () {
      this.$emit('close')
    },
    goShopping () {
      this.close()
      this.$router.push('/items')
    },
    goToCheckout () {
      this.close()
      this.$router.push('/car')
    },
    async increaseQty (item) {
      await this.cartStore.updateQuantity(item.id, item.quantity + 1)
    },
    async decreaseQty (item) {
      if (item.quantity > 1) {
        await this.cartStore.updateQuantity(item.id, item.quantity - 1)
      } else {
        await this.cartStore.removeFromCart(item.id)
      }
    },
    async removeItem (item) {
      await this.cartStore.removeFromCart(item.id)
    }
  }
}
</script>

<style scoped>
/* Fade transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Slide from right transition */
.slide-right-enter-active,
.slide-right-leave-active {
  transition: transform 0.3s ease;
}
.slide-right-enter-from,
.slide-right-leave-to {
  transform: translateX(100%);
}
</style>

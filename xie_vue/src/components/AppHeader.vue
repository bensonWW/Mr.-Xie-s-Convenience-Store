<template>
  <nav class="bg-white dark:bg-slate-900 border-b border-stone-200 dark:border-slate-800/50 sticky top-0 z-sticky transition-colors duration-300">
    <div class="container mx-auto px-4 md:px-6 h-16 flex items-center justify-between">
      <!-- Logo -->
      <router-link to="/" class="flex items-center space-x-2.5 group flex-shrink-0">
        <div class="w-9 h-9 bg-xieOrange rounded-lg flex items-center justify-center text-white font-serif font-bold shadow-sm">
          X
        </div>
        <span class="text-xl font-bold text-slate-700 dark:text-stone-100 tracking-tight group-hover:text-xieOrange transition-colors">
          Xie Mart
        </span>
      </router-link>

      <!-- Search Bar - Rounded pill style with embedded icon -->
      <div class="hidden md:flex flex-1 justify-end max-w-xs mx-6">
        <div class="relative w-full group">
          <input 
            type="text" 
            v-model="search"
            @keyup.enter="doSearch"
            placeholder="搜尋商品..." 
            class="w-full bg-stone-100 dark:bg-slate-800 border-0 rounded-full py-2.5 pl-4 pr-11 focus:outline-none focus:ring-2 focus:ring-xieOrange/50 transition-all text-sm text-slate-700 dark:text-stone-200 placeholder:text-stone-400 dark:placeholder:text-stone-500"
          >
          <button 
            @click="doSearch"
            class="absolute right-1 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full text-stone-400 hover:text-xieOrange hover:bg-stone-200 dark:hover:bg-slate-700 transition"
          >
            <i class="fas fa-search text-sm"></i>
          </button>
        </div>
      </div>

      <!-- Actions - Compact icons with hover tooltips -->
      <div class="flex items-center space-x-1">
        <!-- Dark Mode Toggle -->
        <button 
          @click="toggleTheme" 
          class="relative w-10 h-10 flex items-center justify-center rounded-full text-stone-500 dark:text-stone-400 hover:bg-stone-100 dark:hover:bg-slate-800 hover:text-xieOrange transition group"
          :title="isDarkMode ? '切換淺色模式' : '切換深色模式'"
        >
          <i :class="isDarkMode ? 'fas fa-sun' : 'fas fa-moon'" class="text-lg"></i>
          <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-[10px] tracking-wider text-stone-500 dark:text-stone-400 opacity-0 group-hover:opacity-100 transition whitespace-nowrap">{{ isDarkMode ? '淺色' : '深色' }}</span>
        </button>

        <!-- User -->
        <router-link 
          :to="isLoggedIn ? '/profile' : '/login'" 
          class="relative w-10 h-10 flex items-center justify-center rounded-full text-stone-500 dark:text-stone-400 hover:bg-stone-100 dark:hover:bg-slate-800 hover:text-xieOrange transition group"
          :title="isLoggedIn ? '會員中心' : '登入'"
        >
          <i :class="isLoggedIn ? 'fas fa-user-check' : 'far fa-user'" class="text-lg"></i>
          <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-[10px] tracking-wider text-stone-500 dark:text-stone-400 opacity-0 group-hover:opacity-100 transition whitespace-nowrap">{{ isLoggedIn ? '會員' : '登入' }}</span>
        </router-link>

        <!-- Wishlist -->
        <router-link 
          to="/profile?tab=wishlist" 
          class="relative w-10 h-10 flex items-center justify-center rounded-full text-stone-500 dark:text-stone-400 hover:bg-stone-100 dark:hover:bg-slate-800 hover:text-xieOrange transition group"
          title="收藏清單"
        >
          <i class="far fa-heart text-lg"></i>
          <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-[10px] tracking-wider text-stone-500 dark:text-stone-400 opacity-0 group-hover:opacity-100 transition whitespace-nowrap">收藏</span>
        </router-link>

        <!-- Cart -->
        <button 
          v-if="isLoggedIn"
          @click="$emit('open-cart')"
          class="relative w-10 h-10 flex items-center justify-center rounded-full text-stone-500 dark:text-stone-400 hover:bg-stone-100 dark:hover:bg-slate-800 hover:text-xieOrange transition group"
          title="購物車"
        >
          <i class="fas fa-shopping-bag text-lg"></i>
          <span 
            v-if="!cartLoading && cartCount > 0"
            class="absolute top-0.5 right-0.5 bg-xieOrange text-white text-[9px] font-bold min-w-[16px] h-[16px] flex items-center justify-center rounded-full"
          >
            {{ cartCount > 99 ? '99+' : cartCount }}
          </span>
          <span 
            v-if="cartLoading"
            class="absolute top-0.5 right-0.5 bg-stone-300 dark:bg-slate-600 text-transparent text-[9px] min-w-[16px] h-[16px] rounded-full animate-pulse"
          >0</span>
          <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-[10px] tracking-wider text-stone-500 dark:text-stone-400 opacity-0 group-hover:opacity-100 transition whitespace-nowrap">購物車</span>
        </button>

        <!-- Divider -->
        <div class="w-px h-6 bg-stone-200 dark:bg-slate-700 mx-2 hidden md:block"></div>

        <!-- Admin Link -->
        <router-link 
          v-if="isAdmin" 
          to="/admin" 
          class="hidden md:flex items-center gap-1.5 text-xieOrange font-medium border border-xieOrange rounded-full px-4 py-1.5 text-sm hover:bg-xieOrange hover:text-white transition"
        >
          <i class="fas fa-cogs text-xs"></i>
          <span>後台</span>
        </router-link>

        <!-- Logout -->
        <button 
          v-if="isLoggedIn"
          @click="handleLogout" 
          class="hidden md:flex relative w-10 h-10 items-center justify-center rounded-full text-stone-500 dark:text-stone-400 hover:bg-stone-100 dark:hover:bg-slate-800 hover:text-xieOrange transition group"
          title="登出"
        >
          <i class="fas fa-sign-out-alt text-lg"></i>
          <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-[10px] tracking-wider text-stone-500 dark:text-stone-400 opacity-0 group-hover:opacity-100 transition whitespace-nowrap">登出</span>
        </button>
      </div>
    </div>
  </nav>
</template>

<script>
import { useCartStore } from '../stores/cart'
import { useAuthStore } from '../stores/auth'
import { isDark, toggleDarkMode } from '../utils/darkMode'

export default {
  name: 'AppHeader',
  emits: ['open-cart'],
  setup () {
    const cartStore = useCartStore()
    const authStore = useAuthStore()
    return { cartStore, authStore, isDark }
  },
  data () {
    return {
      search: ''
    }
  },
  computed: {
    isLoggedIn () {
      return this.authStore.isLoggedIn
    },
    isAdmin () {
      return this.authStore.isAdmin
    },
    cartCount () {
      return this.cartStore.cartCount
    },
    cartLoading () {
      return this.cartStore.loading
    },
    isDarkMode () {
      return this.isDark
    }
  },
  methods: {
    doSearch () {
      if (this.search.trim()) {
        this.$router.push({ path: '/items', query: { search: this.search.trim() } })
      }
    },
    async handleLogout () {
      await this.authStore.logout()
      this.$router.push('/login')
      this.cartStore.clearCart()
    },
    toggleTheme () {
      toggleDarkMode()
    }
  },
  mounted () {
    this.$watch(
      () => this.authStore.isLoggedIn,
      (loggedIn) => {
        if (loggedIn) {
          this.cartStore.fetchCart()
        }
      },
      { immediate: true }
    )
  }
}
</script>

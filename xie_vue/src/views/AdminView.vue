<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 to-stone-100 dark:from-slate-950 dark:to-slate-900 flex font-sans transition-colors duration-300">
    <!-- Mobile Sidebar Overlay -->
    <Transition name="fade">
      <div v-if="sidebarOpen" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 md:hidden" @click="sidebarOpen = false"></div>
    </Transition>

    <!-- Admin Sidebar -->
    <aside 
      class="fixed md:static inset-y-0 left-0 z-50 w-72 bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 text-stone-400 flex flex-col transform transition-transform duration-300 ease-in-out md:translate-x-0"
      :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
      <!-- Logo -->
      <div class="h-20 flex items-center px-6 border-b border-slate-800/50">
        <div class="w-10 h-10 bg-gradient-to-br from-xieOrange to-amber-500 rounded-xl flex items-center justify-center text-white font-serif font-bold text-lg shadow-lg shadow-xieOrange/20 mr-3">
          謝
        </div>
        <div>
          <div class="text-white font-bold text-lg">謝老闆商店</div>
          <div class="text-xs text-slate-500">管理後台 v1.0</div>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 py-6 overflow-y-auto">
        <!-- Quick Stats Mini Cards -->
        <div class="px-4 mb-6">
          <div class="grid grid-cols-2 gap-2">
            <div class="bg-slate-800/50 rounded-xl p-3 border border-slate-700/50">
              <div class="text-xs text-slate-500 mb-1">今日訂單</div>
              <div class="text-lg font-bold text-white">{{ todayOrders }}</div>
            </div>
            <div class="bg-slate-800/50 rounded-xl p-3 border border-slate-700/50">
              <div class="text-xs text-slate-500 mb-1">待處理</div>
              <div class="text-lg font-bold text-amber-400">{{ pendingOrders }}</div>
            </div>
          </div>
        </div>

        <div class="px-4 py-2 text-[10px] font-bold text-slate-600 uppercase tracking-widest">主要功能</div>
        
        <router-link 
          to="/admin/dashboard" 
          class="mx-3 mb-1 px-4 py-3 rounded-xl flex items-center gap-3 transition-all duration-200"
          :class="$route.name === 'admin-dashboard' 
            ? 'bg-gradient-to-r from-xieOrange to-amber-500 text-white shadow-lg shadow-xieOrange/20' 
            : 'hover:bg-slate-800/70 hover:text-white'"
        >
          <div class="w-8 h-8 rounded-lg flex items-center justify-center" :class="$route.name === 'admin-dashboard' ? 'bg-white/20' : 'bg-slate-800'">
            <i class="fas fa-chart-pie text-sm"></i>
          </div>
          <span class="font-medium">儀表板</span>
        </router-link>

        <div class="px-4 py-2 text-[10px] font-bold text-slate-600 uppercase tracking-widest mt-4">商店管理</div>
        
        <router-link 
          v-for="item in shopMenuItems" 
          :key="item.route"
          :to="item.route" 
          class="mx-3 mb-1 px-4 py-3 rounded-xl flex items-center gap-3 transition-all duration-200"
          :class="isActiveRoute(item.routeName) 
            ? 'bg-gradient-to-r from-xieOrange to-amber-500 text-white shadow-lg shadow-xieOrange/20' 
            : 'hover:bg-slate-800/70 hover:text-white'"
        >
          <div class="w-8 h-8 rounded-lg flex items-center justify-center" :class="isActiveRoute(item.routeName) ? 'bg-white/20' : 'bg-slate-800'">
            <i :class="item.icon" class="text-sm"></i>
          </div>
          <span class="font-medium">{{ item.label }}</span>
          <span v-if="item.badge" class="ml-auto px-2 py-0.5 text-[10px] font-bold rounded-full" :class="item.badgeClass">
            {{ item.badge }}
          </span>
        </router-link>

        <div class="px-4 py-2 text-[10px] font-bold text-slate-600 uppercase tracking-widest mt-4">顧客與數據</div>
        
        <router-link 
          v-for="item in dataMenuItems" 
          :key="item.route"
          :to="item.route" 
          class="mx-3 mb-1 px-4 py-3 rounded-xl flex items-center gap-3 transition-all duration-200"
          :class="isActiveRoute(item.routeName) 
            ? 'bg-gradient-to-r from-xieOrange to-amber-500 text-white shadow-lg shadow-xieOrange/20' 
            : 'hover:bg-slate-800/70 hover:text-white'"
        >
          <div class="w-8 h-8 rounded-lg flex items-center justify-center" :class="isActiveRoute(item.routeName) ? 'bg-white/20' : 'bg-slate-800'">
            <i :class="item.icon" class="text-sm"></i>
          </div>
          <span class="font-medium">{{ item.label }}</span>
        </router-link>
      </nav>

      <!-- User Section -->
      <div class="p-4 border-t border-slate-800/50">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-full bg-gradient-to-br from-sky-400 to-blue-500 flex items-center justify-center text-white font-bold shadow-lg">
            A
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-sm font-medium text-white truncate">管理員</div>
            <div class="text-xs text-slate-500 truncate">admin@example.com</div>
          </div>
        </div>
        <div class="flex gap-2">
          <router-link to="/" class="flex-1 py-2 text-center text-xs bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white rounded-lg transition">
            <i class="fas fa-store mr-1"></i> 前台
          </router-link>
          <button @click="logout" class="flex-1 py-2 text-center text-xs bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 hover:text-rose-300 rounded-lg transition">
            <i class="fas fa-sign-out-alt mr-1"></i> 登出
          </button>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <!-- Header -->
      <header class="h-16 bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl border-b border-stone-200/50 dark:border-slate-700/50 flex items-center justify-between px-4 md:px-6 sticky top-0 z-30">
        <div class="flex items-center gap-4">
          <!-- Mobile Menu Button -->
          <button 
            class="md:hidden w-10 h-10 rounded-xl bg-stone-100 dark:bg-slate-700 flex items-center justify-center text-stone-500 dark:text-stone-400 hover:bg-stone-200 dark:hover:bg-slate-600 transition"
            @click="sidebarOpen = !sidebarOpen"
          >
            <i class="fas fa-bars"></i>
          </button>
          
          <!-- Breadcrumb -->
          <div>
            <h2 class="font-bold text-lg text-slate-700 dark:text-stone-100">{{ getPageTitle }}</h2>
            <div class="text-xs text-stone-400 dark:text-stone-500 flex items-center gap-1.5">
              <i class="fas fa-home text-[10px]"></i>
              <span>後台</span>
              <i class="fas fa-chevron-right text-[8px]"></i>
              <span class="text-xieOrange">{{ getPageTitle }}</span>
            </div>
          </div>
        </div>

        <!-- Header Actions -->
        <div class="flex items-center gap-2">
          <!-- Search -->
          <div class="hidden md:flex items-center bg-stone-100 dark:bg-slate-700 rounded-xl px-4 py-2">
            <i class="fas fa-search text-stone-400 dark:text-stone-500 text-sm mr-2"></i>
            <input 
              type="text" 
              placeholder="搜尋..." 
              class="bg-transparent border-none outline-none text-sm w-40 text-slate-700 dark:text-stone-200 placeholder-stone-400 dark:placeholder-stone-500"
            >
            <kbd class="hidden lg:inline-block ml-2 px-1.5 py-0.5 text-[10px] font-mono bg-stone-200 dark:bg-slate-600 text-stone-500 dark:text-stone-400 rounded">⌘K</kbd>
          </div>
          
          <!-- Notifications -->
          <button class="relative w-10 h-10 rounded-xl bg-stone-100 dark:bg-slate-700 flex items-center justify-center text-stone-500 dark:text-stone-400 hover:bg-stone-200 dark:hover:bg-slate-600 transition group">
            <i class="fas fa-bell group-hover:animate-wiggle"></i>
            <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-rose-500 rounded-full border-2 border-white dark:border-slate-800 animate-pulse"></span>
          </button>

          <!-- Quick Actions -->
          <button class="hidden md:flex w-10 h-10 rounded-xl bg-stone-100 dark:bg-slate-700 items-center justify-center text-stone-500 dark:text-stone-400 hover:bg-stone-200 dark:hover:bg-slate-600 transition">
            <i class="fas fa-plus"></i>
          </button>
        </div>
      </header>

      <!-- Main Area -->
      <main class="flex-1 overflow-auto p-4 md:p-6">
        <router-view></router-view>
      </main>

      <!-- Footer -->
      <footer class="py-3 px-6 border-t border-stone-200/50 dark:border-slate-700/50 bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm">
        <div class="flex items-center justify-between text-xs text-stone-400 dark:text-stone-500">
          <span>© 2024 謝老闆便利商店</span>
          <span>Version 1.0.0</span>
        </div>
      </footer>
    </div>
  </div>
</template>

<script>
import api from '../services/api'

export default {
  name: 'AdminView',
  data () {
    return {
      sidebarOpen: false,
      todayOrders: 0,
      pendingOrders: 0
    }
  },
  computed: {
    getPageTitle () {
      const map = {
        'admin-dashboard': '儀表板',
        'admin-products': '商品管理',
        'admin-product-edit': '編輯商品',
        'admin-orders': '訂單管理',
        'admin-order-detail': '訂單詳情',
        'admin-coupons': '優惠券管理',
        'admin-categories': '分類管理',
        'admin-users': '會員管理',
        'admin-user-edit': '編輯會員',
        'admin-analytics': '銷售分析'
      }
      return map[this.$route.name] || '後台管理'
    },
    shopMenuItems () {
      return [
        { route: '/admin/products', routeName: 'admin-products', icon: 'fas fa-cube', label: '商品管理' },
        { route: '/admin/orders', routeName: 'admin-order', icon: 'fas fa-shopping-bag', label: '訂單管理', badge: this.pendingOrders || null, badgeClass: 'bg-amber-400 text-slate-900' },
        { route: '/admin/coupons', routeName: 'admin-coupons', icon: 'fas fa-ticket-alt', label: '優惠券' },
        { route: '/admin/categories', routeName: 'admin-categories', icon: 'fas fa-layer-group', label: '分類管理' }
      ]
    },
    dataMenuItems () {
      return [
        { route: '/admin/users', routeName: 'admin-users', icon: 'fas fa-users', label: '會員管理' },
        { route: '/admin/analytics', routeName: 'admin-analytics', icon: 'fas fa-chart-bar', label: '銷售分析' }
      ]
    }
  },
  created () {
    this.fetchStats()
  },
  methods: {
    isActiveRoute (routeName) {
      if (!this.$route.name) return false
      return this.$route.name.includes(routeName)
    },
    async fetchStats () {
      try {
        const res = await api.get('/admin/stats')
        this.todayOrders = res.data.today_orders || 0
        this.pendingOrders = res.data.pending_orders || 0
      } catch (e) {
        console.error('Fetch admin stats error:', e)
      }
    },
    async logout () {
      try {
        await api.post('/logout')
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        localStorage.removeItem('token')
        localStorage.removeItem('auth_token')
        localStorage.removeItem('user_role')
        this.$router.push('/')
      }
    }
  }
}
</script>

<style scoped>
@keyframes wiggle {
  0%, 100% { transform: rotate(0deg); }
  25% { transform: rotate(15deg); }
  50% { transform: rotate(-10deg); }
  75% { transform: rotate(5deg); }
}
.animate-wiggle {
  animation: wiggle 0.5s ease-in-out;
}
.group:hover .group-hover\:animate-wiggle {
  animation: wiggle 0.5s ease-in-out;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>

<template>
  <div class="min-h-screen bg-stone-100 dark:bg-slate-950 flex font-sans transition-colors duration-300">
    <!-- Admin Sidebar (always dark for contrast) -->
    <aside class="w-64 bg-slate-900 text-stone-400 flex flex-col flex-shrink-0 hidden md:flex">
      <div class="h-16 flex items-center px-6 text-white font-bold text-xl border-b border-slate-800">
        <div class="w-8 h-8 bg-xieOrange rounded flex items-center justify-center text-white font-serif font-bold mr-3">
          X
        </div>
        Admin
      </div>
      <nav class="flex-1 py-6 space-y-1 overflow-y-auto">
        <router-link 
          to="/admin/dashboard" 
          class="block px-6 py-3 transition"
          :class="$route.name === 'admin-dashboard' ? 'text-white bg-slate-800 border-l-4 border-xieOrange' : 'hover:bg-slate-800 hover:text-white border-l-4 border-transparent'"
        >
          <i class="fas fa-chart-line w-6"></i> 儀表板
        </router-link>

        <div class="px-6 py-2 text-xs font-semibold text-stone-500 uppercase tracking-wider mt-4">商店管理</div>
        
        <router-link 
          to="/admin/products" 
          class="block px-6 py-3 transition"
          :class="$route.name === 'admin-products' ? 'text-white bg-slate-800 border-l-4 border-xieOrange' : 'hover:bg-slate-800 hover:text-white border-l-4 border-transparent'"
        >
          <i class="fas fa-box w-6"></i> 商品管理
        </router-link>
        <router-link 
          to="/admin/orders" 
          class="block px-6 py-3 transition"
          :class="$route.name && $route.name.includes('admin-order') ? 'text-white bg-slate-800 border-l-4 border-xieOrange' : 'hover:bg-slate-800 hover:text-white border-l-4 border-transparent'"
        >
          <i class="fas fa-shopping-cart w-6"></i> 訂單管理
        </router-link>
        <router-link 
          to="/admin/coupons" 
          class="block px-6 py-3 transition"
          :class="$route.name === 'admin-coupons' ? 'text-white bg-slate-800 border-l-4 border-xieOrange' : 'hover:bg-slate-800 hover:text-white border-l-4 border-transparent'"
        >
          <i class="fas fa-ticket-alt w-6"></i> 優惠券
        </router-link>
        <router-link 
          to="/admin/categories" 
          class="block px-6 py-3 transition"
          :class="$route.name === 'admin-categories' ? 'text-white bg-slate-800 border-l-4 border-xieOrange' : 'hover:bg-slate-800 hover:text-white border-l-4 border-transparent'"
        >
          <i class="fas fa-tags w-6"></i> 分類管理
        </router-link>

        <div class="px-6 py-2 text-xs font-semibold text-stone-500 uppercase tracking-wider mt-4">顧客與數據</div>
        
        <router-link 
          to="/admin/users" 
          class="block px-6 py-3 transition"
          :class="$route.name === 'admin-users' ? 'text-white bg-slate-800 border-l-4 border-xieOrange' : 'hover:bg-slate-800 hover:text-white border-l-4 border-transparent'"
        >
          <i class="fas fa-users w-6"></i> 會員管理
        </router-link>
        <router-link 
          to="/admin/analytics" 
          class="block px-6 py-3 transition"
          :class="$route.name === 'admin-analytics' ? 'text-white bg-slate-800 border-l-4 border-xieOrange' : 'hover:bg-slate-800 hover:text-white border-l-4 border-transparent'"
        >
          <i class="fas fa-chart-bar w-6"></i> 銷售分析
        </router-link>
      </nav>
      <div class="p-4 border-t border-slate-800">
        <a href="#" class="flex items-center text-sm hover:text-white transition" @click.prevent="logout">
          <i class="fas fa-sign-out-alt w-6"></i> 登出
        </a>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <header class="h-16 bg-white dark:bg-slate-800 border-b border-stone-200 dark:border-slate-700 flex items-center justify-between px-6 transition-colors duration-300">
        <div class="flex items-center">
          <button class="md:hidden text-stone-500 dark:text-stone-400 mr-4">
            <i class="fas fa-bars text-xl"></i>
          </button>
          <div>
            <h2 class="font-semibold text-slate-700 dark:text-stone-100">{{ getPageTitle }}</h2>
            <div class="text-xs text-stone-400 dark:text-stone-500 flex items-center gap-1">
              <span class="hover:text-xieOrange cursor-pointer transition">管理後台</span>
              <span>/</span>
              <span class="text-slate-600 dark:text-stone-300">{{ getPageTitle }}</span>
            </div>
          </div>
        </div>
        <div class="flex items-center space-x-4">
          <button class="text-stone-400 dark:text-stone-500 hover:text-slate-700 dark:hover:text-stone-300 relative transition group">
            <i class="fas fa-bell group-hover:animate-wiggle"></i>
            <div class="w-2 h-2 bg-rose-500 rounded-full absolute -top-0.5 -right-0.5 animate-pulse"></div>
          </button>
          <div class="w-9 h-9 rounded-full bg-slate-200 dark:bg-slate-600 flex items-center justify-center text-slate-600 dark:text-stone-300">
            <i class="fas fa-user-shield"></i>
          </div>
        </div>
      </header>

      <main class="flex-1 overflow-auto p-6 md:p-8 bg-stone-100 dark:bg-slate-950">
        <router-view></router-view>
      </main>
    </div>
  </div>
</template>

<script>
import api from '../services/api'

export default {
  name: 'AdminView',
  computed: {
    getPageTitle () {
      const map = {
        'admin-dashboard': '儀表板',
        'admin-products': '商品管理',
        'admin-orders': '訂單管理',
        'admin-order-detail': '訂單詳情',
        'admin-coupons': '優惠券管理',
        'admin-categories': '分類管理',
        'admin-users': '會員管理',
        'admin-analytics': '銷售分析'
      }
      return map[this.$route.name] || '後台管理'
    }
  },
  methods: {
    async logout () {
      try {
        await api.post('/logout')
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        localStorage.removeItem('token')
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
</style>

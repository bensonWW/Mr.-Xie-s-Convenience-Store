<template>
  <div class="flex h-screen overflow-hidden bg-gray-100 font-sans text-gray-700">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md flex-shrink-0 hidden md:flex flex-col z-20">
        <div class="h-16 flex items-center justify-center border-b border-gray-100 bg-xieBlue text-white font-bold text-xl tracking-wider">
            MR. XIE ADMIN
        </div>

        <nav class="flex-1 overflow-y-auto py-4">
            <ul class="space-y-1">
                <li>
                    <router-link to="/admin/dashboard" class="flex items-center px-6 py-3 transition"
                       :class="$route.name === 'admin-dashboard' ? 'text-xieOrange bg-orange-50 border-r-4 border-xieOrange' : 'text-gray-600 hover:bg-gray-50 hover:text-xieOrange'">
                        <i class="fas fa-chart-pie w-6"></i>
                        <span class="font-bold">總覽儀表板</span>
                    </router-link>
                </li>

                <li class="px-6 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider mt-4">商店管理</li>
                <li>
                    <router-link to="/admin/products" class="flex items-center px-6 py-3 transition"
                       :class="$route.name === 'admin-products' ? 'text-xieOrange bg-orange-50 border-r-4 border-xieOrange' : 'text-gray-600 hover:bg-gray-50 hover:text-xieOrange'">
                        <i class="fas fa-box w-6"></i>
                        <span>商品管理</span>
                    </router-link>
                </li>
                <li>
                    <router-link to="/admin/orders" class="flex items-center px-6 py-3 transition"
                       :class="$route.name && $route.name.includes('admin-order') ? 'text-xieOrange bg-orange-50 border-r-4 border-xieOrange' : 'text-gray-600 hover:bg-gray-50 hover:text-xieOrange'">
                        <i class="fas fa-file-invoice-dollar w-6"></i>
                        <span>訂單管理</span>
                    </router-link>
                </li>
                 <li>
                    <router-link to="/admin/coupons" class="flex items-center px-6 py-3 transition"
                       :class="$route.name === 'admin-coupons' ? 'text-xieOrange bg-orange-50 border-r-4 border-xieOrange' : 'text-gray-600 hover:bg-gray-50 hover:text-xieOrange'">
                        <i class="fas fa-ticket-alt w-6"></i>
                        <span>優惠券管理</span>
                    </router-link>
                </li>

                <li class="px-6 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider mt-4">顧客與數據</li>
                <li>
                    <a href="#" class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-xieOrange transition cursor-not-allowed opacity-50">
                        <i class="fas fa-users w-6"></i>
                        <span>會員管理</span>
                    </a>
                </li>
                <li>
                    <router-link to="/admin/analytics" class="flex items-center px-6 py-3 transition"
                       :class="$route.name === 'admin-analytics' ? 'text-xieOrange bg-orange-50 border-r-4 border-xieOrange' : 'text-gray-600 hover:bg-gray-50 hover:text-xieOrange'">
                        <i class="fas fa-chart-line w-6"></i>
                        <span>銷售分析</span>
                    </router-link>
                </li>
            </ul>
        </nav>

        <div class="p-4 border-t border-gray-100">
            <a href="#" class="flex items-center gap-3 text-sm text-gray-500 hover:text-red-500 transition" @click.prevent="logout">
                <i class="fas fa-sign-out-alt"></i> 登出管理後台
            </a>
        </div>
    </aside>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">

        <!-- Header -->
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6 z-10">
            <button class="md:hidden text-gray-500"><i class="fas fa-bars text-xl"></i></button>
            <div class="text-lg font-bold text-gray-800">{{ getPageTitle }}</div>
            <div class="flex items-center gap-4">
                <button class="relative text-gray-400 hover:text-xieOrange transition">
                    <i class="fas fa-bell"></i>
                    <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                <div class="flex items-center gap-2">
                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" class="w-8 h-8 rounded-full border">
                    <span class="text-sm font-bold text-gray-700">Admin</span>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
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
/* Tailwind CSS is used for styling */
</style>

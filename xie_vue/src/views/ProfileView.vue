<template>
  <div class="py-8">
    <!-- TEMP DEBUG BANNER -->
    <div class="bg-red-500 text-white p-2 text-center text-xs font-mono">
      DEBUG: BYPASS={{ bypassEnvValue }} (Process: {{ processEnvValue }})
    </div>

    <!-- 未登入：登入 / 註冊區 -->
    <AuthOverlay v-if="!isLoggedIn" />

    <!-- 已登入：會員中心 -->
    <div v-else class="container mx-auto px-4 pb-12 grid grid-cols-1 md:grid-cols-4 gap-6">

        <UserSidebar
          :user="user"
          :current-view="currentView"
          :avatar-url="avatarUrl"
          @update:currentView="currentView = $event"
          @avatar-change="onAvatarChange"
          @logout="handleLogout"
        />

        <main class="col-span-1 md:col-span-3 space-y-6">

            <div v-if="currentView === 'dashboard'">
              <DashboardStats
                :user="user"
                :coupons="coupons"
                :orders="orders"
              />
              <div class="mt-8">
                 <h3 class="font-bold text-gray-800 mb-4">猜你喜歡 (Todo: Component)</h3>
                 <!-- ... keep existing static content or refactor later ... -->
                  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                      <div class="bg-white rounded-lg shadow-sm p-3 hover:shadow-md transition cursor-pointer">
                          <div class="bg-gray-100 h-24 rounded mb-2 flex items-center justify-center text-gray-300"><i class="fas fa-image"></i></div>
                          <div class="text-xs text-gray-800 font-bold line-clamp-1">Apple Watch S9 GPS</div>
                          <div class="text-xieOrange font-bold text-sm mt-1">NT$ 13,500</div>
                      </div>
                      <!-- More items -->
                  </div>
              </div>
            </div>

            <OrderHistory
              v-if="currentView === 'dashboard' || currentView === 'orders'"
              :orders="orders"
              @order-updated="fetchOrders"
            />

            <CouponWallet
              v-if="currentView === 'coupons'"
              :coupons="coupons"
            />

            <WishlistGrid
              v-if="currentView === 'wishlist'"
              :wishlist="wishlist"
              @update:wishlist="wishlist = $event"
            />

            <ProfileEdit
              v-if="currentView === 'editProfile'"
              :user="user"
              :initial-avatar-url="avatarUrl"
              @cancel="currentView = 'dashboard'"
              @profile-updated="handleProfileUpdated"
              @avatar-change="onAvatarChange"
            />

            <WalletView v-if="currentView === 'wallet'" />

        </main>
    </div>

  </div>
</template>

<script>
import api from '../services/api'
import { mapActions } from 'vuex'
import AuthOverlay from '../components/profile/AuthOverlay.vue'
import UserSidebar from '../components/profile/UserSidebar.vue'
import DashboardStats from '../components/profile/DashboardStats.vue'
import OrderHistory from '../components/profile/OrderHistory.vue'
import CouponWallet from '../components/profile/CouponWallet.vue'
import WishlistGrid from '../components/profile/WishlistGrid.vue'
import ProfileEdit from '../components/profile/ProfileEdit.vue'
import WalletView from '../components/profile/WalletView.vue'

export default {
  name: 'ProfileView',
  components: {
    AuthOverlay,
    UserSidebar,
    DashboardStats,
    OrderHistory,
    CouponWallet,
    WishlistGrid,
    ProfileEdit,
    WalletView
  },
  data () {
    return {
      avatarUrl: 'https://img.icons8.com/color/96/000000/user-male-circle--v2.png',
      user: null,
      orders: [],
      coupons: [],
      // Wishlist mock data (preserved from original)
      wishlist: [
        { id: 1, name: 'Apple iPhone 15 Pro Max 256GB', price: 44900, original_price: 48900, icon_class: 'fab fa-apple', status: 'available', tag: '降價' },
        { id: 2, name: 'PlayStation 5 光碟版主機', price: 17580, original_price: 17580, icon_class: 'fab fa-playstation', status: 'out_of_stock' },
        { id: 3, name: 'Dyson V12 Detect Slim', price: 19900, original_price: 23900, icon_class: 'fas fa-wind', status: 'available' }
      ],
      currentView: 'dashboard',
      bypassEnvValue: process.env.VUE_APP_BYPASS_AUTH_DEV || 'undefined',
      processEnvValue: process.env.VUE_APP_BYPASS_AUTH_DEV || 'undefined'
    }
  },
  computed: {
    isLoggedIn () {
      return !!this.user
    }
  },
  created () {
    this.fetchUser()
  },
  methods: {
    ...mapActions(['logout']), // Use Vuex action we just fixed
    async fetchUser () {
      const token = localStorage.getItem('token')
      if (token) {
        try {
          const response = await api.get('/user')
          this.user = response.data
          this.fetchOrders()
          this.fetchCoupons()
        } catch (error) {
          console.error('Error fetching user:', error)
          // Do NOT remove token here immediately, let user re-login via UI or interceptor handle 401
          // But original code removed it.
          localStorage.removeItem('token')
          this.user = null
        }
      }
    },
    async fetchOrders () {
      if (!this.user) return
      try {
        const response = await api.get('/orders')
        this.orders = response.data
      } catch (error) {
        console.error('Error fetching orders:', error)
      }
    },
    async fetchCoupons () {
      try {
        const response = await api.get('/coupons')
        this.coupons = response.data
      } catch (error) {
        console.error('Fetch coupons error:', error)
      }
    },
    handleLogout () {
      this.logout().then(() => {
        this.$toast.info('已登出')
        setTimeout(() => window.location.reload(), 500)
      })
    },
    handleProfileUpdated (updatedUser) {
      this.user = updatedUser
      this.currentView = 'dashboard'
    },
    onAvatarChange (file) {
      if (file) {
        const reader = new FileReader()
        reader.onload = (e) => {
          this.avatarUrl = e.target.result
        }
        reader.readAsDataURL(file)
        // Todo: Upload logic
      }
    }
  }
}
</script>

<style scoped>
/* Scoped styles can be minimal now */
</style>

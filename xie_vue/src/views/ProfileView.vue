<template>
  <div class="py-8">
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
              @filter-change="fetchOrders"
            />

            <CouponWallet
              v-if="currentView === 'coupons'"
              :coupons="coupons"
            />

            <WishlistGrid
              v-if="currentView === 'wishlist'"
              :wishlist="wishlist"
              @remove="handleWishlistRemove"
              @add-to-cart="handleAddToCart"
            />

            <AddressManager
              v-if="currentView === 'address'"
              :user="user"
              @profile-updated="handleProfileUpdated"
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
import AddressManager from '../components/profile/AddressManager.vue'

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
    WalletView,
    AddressManager
  },
  data () {
    return {
      avatarUrl: 'https://img.icons8.com/color/96/000000/user-male-circle--v2.png',
      user: null,
      orders: [],
      coupons: [],
      wishlist: [],
      currentView: 'dashboard'
    }
  },
  computed: {
    isLoggedIn () {
      return !!this.user
    }
  },
  watch: {
    '$route.query.tab': {
      immediate: true,
      handler (val) {
        if (val === 'wishlist') {
          this.currentView = 'wishlist'
        }
      }
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
          this.fetchWishlist()
        } catch (error) {
          console.error('Error fetching user:', error)
          // Do NOT remove token here immediately, let user re-login via UI or interceptor handle 401
          // But original code removed it.
          localStorage.removeItem('token')
          this.user = null
        }
      }
    },
    async fetchOrders (status = 'all') {
      if (!this.user) return
      try {
        const params = {}
        if (status && status !== 'all') {
             if (status === 'refunded') {
                 params.status = 'cancelled'
             } else {
                 params.status = status
             }
        }
        const response = await api.get('/orders', { params })
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
    async fetchWishlist () {
      try {
        const response = await api.get('/favorites')
        // Map backend product data to partial frontend shape if needed,
        // but let's try to align WishlistGrid to real data.
        // For now, map fields to match the Mock expectations slightly to avoid breaking UI immediately?
        // No, let's just pass real data and fix the grid.
        this.wishlist = response.data.map(product => ({
          ...product,
          status: product.stock > 0 ? 'available' : 'out_of_stock',
          // Use image or fallback icon
          icon_class: product.category === 'Electronics' ? 'fas fa-mobile-alt' : 'fas fa-box'
        }))
      } catch (error) {
        console.error('Fetch wishlist error:', error)
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
    },
    handleWishlistRemove (id) {
      this.wishlist = this.wishlist.filter(item => item.id !== id)
    },
    async handleAddToCart (item) {
      try {
        await api.post('/cart/items', {
          product_id: item.id,
          quantity: 1
        })
        this.$toast.success(`已將 ${item.name} 加入購物車`)
        this.$store.dispatch('cart/fetchCount')
      } catch (error) {
        console.error('Add to cart error:', error)
        this.$toast.error('加入購物車失敗')
      }
    }
  }
}
</script>

<style scoped>
/* Scoped styles can be minimal now */
</style>

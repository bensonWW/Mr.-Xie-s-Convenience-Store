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
                @filter-selected="handleDashboardFilter"
              />
              <!-- Removed duplicate Todo Component placeholder from view -->
            </div>

            <OrderHistory
              v-if="currentView === 'dashboard' || currentView === 'orders'"
              :orders="orders"
              :active-tab="activeOrderTab"
              @order-updated="fetchOrders"
              @filter-change="handleOrderFilterChange"
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
import { mapActions, mapGetters } from 'vuex'
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
      orders: [],
      coupons: [],
      wishlist: [],
      currentView: 'dashboard',
      activeOrderTab: 'all'
    }
  },
  computed: {
    ...mapGetters(['currentUser', 'isLoggedIn']),
    user () {
      return this.currentUser
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
    },
    currentUser: {
      immediate: true,
      handler (newVal) {
        if (newVal) {
          this.fetchOrders() // Initial fetch (all)
          this.fetchCoupons()
          this.fetchWishlist()
        } else {
          // Clear local data on logout
          this.orders = []
          this.coupons = []
          this.wishlist = []
        }
      }
    }
  },
  created () {
    // App.vue already dispatches checkAuth, but we can verify here if needed.
    // user watcher will handle data fetching.
  },
  methods: {
    ...mapActions(['logout']), 
    // fetchUser removed as we use Vuex state
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
    handleOrderFilterChange (status) {
       this.activeOrderTab = status
       // No need to fetch here, OrderHistory watcher will typically handle it, 
       // OR if OrderHistory relies on parent to fetch, we do it here.
       // The existing OrderHistory invokes parent via @filter-change usually?
       // Wait, existing OrderHistory called fetchOrders internally then emitted data?
       // No, my analysis of OrderHistory showed it emits 'filter-change'.
       // Let's ensure alignment.
       this.fetchOrders(status)
    },
    handleDashboardFilter (status) {
       this.activeOrderTab = status
       // Scroll to order history or ensure it's visible?
       // It is visible in dashboard view.
       this.fetchOrders(status)
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
        // No reload needed, reactivity handles switch to AuthOverlay
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

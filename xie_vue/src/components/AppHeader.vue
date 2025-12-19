<template>
  <div>
    <!-- Top Bar -->
    <div class="bg-xieBlue text-white text-sm py-2">
      <div class="container mx-auto px-4 flex justify-between items-center">
        <span><i class="fas fa-check-circle text-xieOrange mr-2"></i>謝先生便利商店 - 您最信賴的居家夥伴</span>
        <div class="space-x-4">
          <a href="#" class="text-white hover:text-xieOrange no-underline">下載 App</a>
          <a href="#" class="text-white hover:text-xieOrange no-underline">店家中心</a>
          <a href="#" class="text-white hover:text-xieOrange no-underline">幫助中心</a>
        </div>
      </div>
    </div>

    <!-- Main Header -->
    <header class="bg-white shadow-md sticky top-0 z-50">
      <div class="container mx-auto px-4 py-4 flex flex-col md:flex-row items-center justify-between gap-4">
        <router-link to="/" class="flex items-center gap-2 group no-underline">
          <div class="bg-xieBlue text-white p-2 rounded text-xl font-bold">Mr. Xie</div>
          <div class="text-2xl font-bold text-xieBlue tracking-tighter group-hover:text-xieOrange transition">
            謝先生<span class="text-xieOrange">便利商店</span>
          </div>
        </router-link>

        <div class="flex-1 w-full max-w-2xl mx-auto">
          <div class="flex">
            <input
              type="text"
              v-model="search"
              @keyup.enter="doSearch"
              placeholder="搜尋：大同電鍋、衛生紙、除濕機..."
              class="w-full border-2 border-xieOrange rounded-l-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-300"
            >
            <button
              @click="doSearch"
              class="bg-xieOrange text-white px-6 py-2 rounded-r-lg font-bold hover:bg-orange-600 transition flex items-center"
            >
              <i class="fas fa-search mr-2"></i> 搜尋
            </button>
          </div>
          <div class="text-xs text-gray-500 mt-1 space-x-2">
            <span class="text-xieOrange font-bold">熱搜：</span>
            <router-link to="/items?search=Dyson" class="hover:underline">Dyson 吸塵器</router-link>
            <router-link to="/items?search=洗衣球" class="hover:underline">洗衣球</router-link>
            <router-link to="/items?search=氣炸鍋" class="hover:underline">氣炸鍋</router-link>
          </div>
        </div>

        <div class="flex items-center space-x-6 text-xieBlue">
          <router-link to="/profile?tab=wishlist" class="flex flex-col items-center text-xieBlue hover:text-xieOrange no-underline">
            <i class="far fa-heart text-xl"></i>
            <span class="text-xs mt-1">收藏</span>
          </router-link>

          <router-link to="/car" class="flex flex-col items-center text-xieBlue hover:text-xieOrange no-underline">
            <i class="fas fa-shopping-cart text-xl relative">
              <span v-if="isLoggedIn && cartCount > 0" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">{{ cartCount }}</span>
            </i>
            <span class="text-xs mt-1">購物車</span>
          </router-link>

          <template v-if="!isLoggedIn">
             <router-link to="/profile" class="flex flex-col items-center text-xieBlue hover:text-xieOrange no-underline">
              <i class="far fa-user text-xl"></i>
              <span class="text-xs mt-1">登入</span>
            </router-link>
          </template>
          <template v-else>
             <router-link to="/profile" class="flex flex-col items-center text-xieBlue hover:text-xieOrange no-underline">
              <i class="fas fa-user-check text-xl"></i>
              <span class="text-xs mt-1">會員中心</span>
            </router-link>

             <router-link v-if="isAdmin" to="/admin" class="flex flex-col items-center text-xieOrange font-bold border border-xieOrange rounded px-2 py-1 hover:bg-xieOrange hover:text-white transition no-underline">
               <i class="fas fa-cogs text-lg"></i>
               <span class="text-xs">後台管理</span>
            </router-link>

             <button @click="logout" class="flex flex-col items-center text-xieBlue hover:text-xieOrange no-underline bg-transparent border-none cursor-pointer">
              <i class="fas fa-sign-out-alt text-xl"></i>
              <span class="text-xs mt-1">登出</span>
            </button>
          </template>

        </div>
      </div>
    </header>
  </div>
</template>

<script>
import { mapActions } from 'vuex'

export default {
  name: 'AppHeader',
  data () {
    return {
      search: ''
    }
  },
  computed: {
    isLoggedIn () {
      return !!this.$store.getters.isLoggedIn
    },
    isAdmin () {
      return this.$store.getters.isAdmin
    },
    cartCount () {
      return this.$store.getters['cart/count']
    }
  },
  methods: {
    ...mapActions('cart', ['fetchCount']),
    ...mapActions(['logout']),
    doSearch () {
      if (this.search.trim()) {
        this.$router.push({ path: '/items', query: { search: this.search.trim() } })
      }
    },
    handleLogout () {
      this.logout().then(() => {
        if (this.$route.path !== '/profile') {
          this.$router.push('/profile')
        }
      })
    }
  },
  created () {
    if (this.isLoggedIn) {
      this.fetchCount()
    }
  }
}
</script>

<style scoped>
/* Scoped styles if needed */
</style>

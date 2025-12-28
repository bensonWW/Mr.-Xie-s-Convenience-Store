<template>
  <aside class="col-span-1 hidden lg:block">
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-stone-100 dark:border-slate-700 overflow-hidden transition-colors duration-300 sticky top-24">
      <!-- User Profile Header -->
      <div class="p-6 text-center border-b border-stone-100 dark:border-slate-700 bg-gradient-to-b from-stone-50 dark:from-slate-700 to-white dark:to-slate-800">
        <div class="w-20 h-20 mx-auto bg-stone-200 dark:bg-slate-600 rounded-full mb-3 border-4 border-white dark:border-slate-700 shadow-md overflow-hidden relative">
          <img :src="avatarUrl || defaultAvatar" alt="Avatar" class="w-full h-full object-cover">
          <button 
            class="absolute bottom-0 right-0 bg-xieOrange text-white rounded-full w-7 h-7 flex items-center justify-center cursor-pointer hover:bg-[#cf8354] transition" 
            @click="triggerFileInput"
          >
            <i class="fas fa-camera text-xs"></i>
          </button>
          <input
            type="file"
            accept="image/*"
            @change="onFileChange"
            ref="avatarInput"
            class="hidden"
          >
        </div>
        <h3 class="font-bold text-slate-700 dark:text-stone-100">{{ user?.name || '會員' }}</h3>
        <span class="inline-block bg-xieOrange text-white text-xs px-3 py-1 rounded-full mt-2 font-medium">一般會員</span>
      </div>

      <!-- Navigation -->
      <nav class="p-3">
        <ul class="space-y-1 text-sm">
          <li>
            <a href="#" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition"
               :class="currentView === 'dashboard' ? 'bg-xieOrange/10 text-xieOrange font-semibold' : 'text-stone-600 dark:text-stone-300 hover:bg-stone-50 dark:hover:bg-slate-700 hover:text-xieOrange'"
               @click.prevent="$emit('update:currentView', 'dashboard')">
              <i class="fas fa-home w-5 text-center"></i> 會員首頁
            </a>
          </li>
          <li>
            <a href="#" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition"
               :class="currentView === 'orders' ? 'bg-xieOrange/10 text-xieOrange font-semibold' : 'text-stone-600 dark:text-stone-300 hover:bg-stone-50 dark:hover:bg-slate-700 hover:text-xieOrange'"
               @click.prevent="$emit('update:currentView', 'orders')">
              <i class="fas fa-file-invoice-dollar w-5 text-center"></i> 我的訂單
            </a>
          </li>
          <li>
            <a href="#" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition"
               :class="currentView === 'wallet' ? 'bg-xieOrange/10 text-xieOrange font-semibold' : 'text-stone-600 dark:text-stone-300 hover:bg-stone-50 dark:hover:bg-slate-700 hover:text-xieOrange'"
               @click.prevent="$emit('update:currentView', 'wallet')">
              <i class="fas fa-wallet w-5 text-center"></i> 我的錢包
            </a>
          </li>
          <li>
            <a href="#" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition"
               :class="currentView === 'coupons' ? 'bg-xieOrange/10 text-xieOrange font-semibold' : 'text-stone-600 dark:text-stone-300 hover:bg-stone-50 dark:hover:bg-slate-700 hover:text-xieOrange'"
               @click.prevent="$emit('update:currentView', 'coupons')">
              <i class="fas fa-ticket-alt w-5 text-center"></i> 我的折價券
            </a>
          </li>
          <li>
            <a href="#" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition"
               :class="currentView === 'wishlist' ? 'bg-xieOrange/10 text-xieOrange font-semibold' : 'text-stone-600 dark:text-stone-300 hover:bg-stone-50 dark:hover:bg-slate-700 hover:text-xieOrange'"
               @click.prevent="$emit('update:currentView', 'wishlist')">
              <i class="far fa-heart w-5 text-center"></i> 追蹤清單
            </a>
          </li>
          <li>
            <a href="#" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition"
               :class="currentView === 'address' ? 'bg-xieOrange/10 text-xieOrange font-semibold' : 'text-stone-600 dark:text-stone-300 hover:bg-stone-50 dark:hover:bg-slate-700 hover:text-xieOrange'"
               @click.prevent="$emit('update:currentView', 'address')">
              <i class="fas fa-map-marker-alt w-5 text-center"></i> 收貨地址
            </a>
          </li>
          <li class="border-t border-stone-100 dark:border-slate-700 mt-2 pt-2">
            <a href="#" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition"
               :class="currentView === 'editProfile' ? 'bg-xieOrange/10 text-xieOrange font-semibold' : 'text-stone-600 dark:text-stone-300 hover:bg-stone-50 dark:hover:bg-slate-700 hover:text-xieOrange'"
               @click.prevent="$emit('update:currentView', 'editProfile')">
              <i class="fas fa-user-cog w-5 text-center"></i> 個人資料
            </a>
          </li>
          <li class="border-t border-stone-100 dark:border-slate-700 mt-2 pt-2">
            <a href="#" 
               class="flex items-center gap-3 px-4 py-3 text-stone-600 dark:text-stone-300 hover:bg-rose-50 dark:hover:bg-rose-900/20 hover:text-rose-500 rounded-xl transition"
               @click.prevent="logout">
              <i class="fas fa-sign-out-alt w-5 text-center"></i> 登出
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>
</template>

<script>
export default {
  name: 'UserSidebar',
  props: {
    user: {
      type: Object,
      required: true
    },
    currentView: {
      type: String,
      default: 'dashboard'
    },
    avatarUrl: {
      type: String,
      default: ''
    }
  },
  emits: ['update:currentView', 'avatar-change', 'logout'],
  data () {
    return {
      defaultAvatar: 'https://img.icons8.com/color/96/000000/user-male-circle--v2.png'
    }
  },
  methods: {
    triggerFileInput () {
      this.$refs.avatarInput.click()
    },
    onFileChange (e) {
      const file = e.target.files[0]
      if (file) {
        this.$emit('avatar-change', file)
      }
    },
    logout () {
      if (confirm('確定要登出嗎？')) {
        this.$emit('logout')
      }
    }
  }
}
</script>

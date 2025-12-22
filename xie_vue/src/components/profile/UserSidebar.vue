<template>
  <div class="col-span-1 hidden md:block">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
      <div class="p-6 text-center border-b border-gray-100 bg-gradient-to-b from-blue-50 to-white">
        <div class="w-20 h-20 mx-auto bg-gray-200 rounded-full mb-3 border-4 border-white shadow-md overflow-hidden relative">
          <img :src="avatarUrl || defaultAvatar" alt="Avatar" class="w-full h-full object-cover">
          <button class="absolute bottom-0 right-0 bg-xieOrange text-white rounded-full p-1 text-xs w-6 h-6 flex items-center justify-center cursor-pointer" @click="triggerFileInput">
            <i class="fas fa-camera"></i>
          </button>
          <input
            type="file"
            accept="image/*"
            @change="onFileChange"
            ref="avatarInput"
            style="display:none"
          >
        </div>
        <h3 class="font-bold text-gray-800">{{ user.name }}</h3>
        <span class="inline-block bg-xieOrange text-white text-xs px-2 py-0.5 rounded-full mt-1">一般會員</span>
      </div>

      <nav class="p-2">
        <ul class="space-y-1 text-sm">
          <li>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg transition"
               :class="currentView === 'dashboard' ? 'bg-orange-50 text-xieOrange font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-xieOrange'"
               @click.prevent="$emit('update:currentView', 'dashboard')">
              <i class="fas fa-user w-5 text-center"></i> 會員首頁
            </a>
          </li>
          <li>
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-xieOrange rounded-lg transition" @click.prevent>
              <i class="fas fa-file-invoice-dollar w-5 text-center"></i> 我的訂單
            </a>
          </li>
          <li>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg transition"
               :class="currentView === 'wallet' ? 'bg-orange-50 text-xieOrange font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-xieOrange'"
               @click.prevent="$emit('update:currentView', 'wallet')">
              <i class="fas fa-wallet w-5 text-center"></i> 我的錢包
            </a>
          </li>
          <li>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg transition"
               :class="currentView === 'coupons' ? 'bg-orange-50 text-xieOrange font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-xieOrange'"
               @click.prevent="$emit('update:currentView', 'coupons')">
              <i class="fas fa-ticket-alt w-5 text-center"></i> 我的折價券
            </a>
          </li>
          <li>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg transition"
               :class="currentView === 'wishlist' ? 'bg-orange-50 text-xieOrange font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-xieOrange'"
               @click.prevent="$emit('update:currentView', 'wishlist')">
              <i class="far fa-heart w-5 text-center"></i> 追蹤清單
            </a>
          </li>
          <li>
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg transition"
               :class="currentView === 'address' ? 'bg-orange-50 text-xieOrange font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-xieOrange'"
               @click.prevent="$emit('update:currentView', 'address')">
              <i class="fas fa-map-marker-alt w-5 text-center"></i> 收貨地址管理
            </a>
          </li>
          <li class="border-t border-gray-100 mt-2 pt-2">
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg transition"
               :class="currentView === 'editProfile' ? 'bg-orange-50 text-xieOrange font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-xieOrange'"
               @click.prevent="$emit('update:currentView', 'editProfile')">
              <i class="fas fa-user-cog w-5 text-center"></i> 修改個人資料
            </a>
          </li>
          <li class="border-t border-gray-100 mt-2 pt-2">
             <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-red-500 rounded-lg transition"
               @click.prevent="logout">
              <i class="fas fa-sign-out-alt w-5 text-center"></i> 登出
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </div>
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

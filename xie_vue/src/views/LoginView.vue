<template>
  <div class="min-h-screen bg-[#d9cdb4] dark:bg-slate-900 py-12 transition-colors duration-300">
    <div class="container mx-auto px-4 flex justify-center">
      <div class="flex items-stretch gap-0 overflow-hidden rounded-2xl shadow-xl max-w-4xl w-full">
        
        <!-- Left Side - Description (Desktop Only) -->
        <div class="hidden lg:flex lg:w-80 bg-slate-700 dark:bg-slate-800 p-8 flex-col justify-center text-white relative border-r border-slate-600 dark:border-slate-600">
          <!-- Decorative Elements -->
          <div class="absolute top-6 right-6 text-white/20 dark:text-white/30 text-6xl font-serif">謝</div>
          
          <h1 class="text-2xl font-serif font-light mb-4 text-white">
            歡迎回來
          </h1>
          <p class="text-white/80 dark:text-stone-300 text-sm leading-relaxed mb-8">
            登入您的會員帳號，即可享受專屬優惠、查看訂單紀錄，以及管理您的收藏清單。
          </p>
          
          <!-- Features -->
          <div class="space-y-4">
            <div class="flex items-center gap-3 text-sm text-white/90 dark:text-stone-200">
              <div class="w-8 h-8 rounded-full bg-xieOrange/20 dark:bg-xieOrange/30 flex items-center justify-center">
                <i class="fas fa-history text-xieOrange text-xs"></i>
              </div>
              <span>查看完整購物紀錄</span>
            </div>
            <div class="flex items-center gap-3 text-sm text-white/90 dark:text-stone-200">
              <div class="w-8 h-8 rounded-full bg-xieOrange/20 dark:bg-xieOrange/30 flex items-center justify-center">
                <i class="fas fa-heart text-xieOrange text-xs"></i>
              </div>
              <span>同步您的收藏清單</span>
            </div>
            <div class="flex items-center gap-3 text-sm text-white/90 dark:text-stone-200">
              <div class="w-8 h-8 rounded-full bg-xieOrange/20 dark:bg-xieOrange/30 flex items-center justify-center">
                <i class="fas fa-tag text-xieOrange text-xs"></i>
              </div>
              <span>專屬會員優惠券</span>
            </div>
          </div>

          <!-- Bottom Brand -->
          <div class="mt-auto pt-8 border-t border-white/20 dark:border-slate-600">
            <p class="text-white/60 dark:text-stone-400 text-xs tracking-widest">XIE MART — EST. 2024</p>
          </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="flex-1 bg-white dark:bg-slate-800 transition-colors duration-300">
          <!-- Header -->
          <div class="text-center p-8 pb-6">
            <div class="w-16 h-16 bg-xieOrange/10 dark:bg-xieOrange/20 rounded-full flex items-center justify-center mx-auto mb-4">
              <i class="fas fa-sign-in-alt text-2xl text-xieOrange"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-700 dark:text-stone-100 mb-2">會員登入</h2>
            <p class="text-stone-500 dark:text-stone-400 text-sm">登入後即可查看購物紀錄與專屬會員優惠</p>
          </div>

          <!-- Form -->
          <div class="px-8 pb-8 space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">Email</label>
              <input
                v-model="email"
                type="email"
                placeholder="請輸入電子郵件"
                class="w-full bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-xieOrange/50 focus:border-xieOrange transition text-slate-700 dark:text-stone-100 placeholder:text-stone-400"
                @keyup.enter="handleLogin"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">密碼</label>
              <input
                v-model="password"
                type="password"
                placeholder="請輸入密碼"
                class="w-full bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-xieOrange/50 focus:border-xieOrange transition text-slate-700 dark:text-stone-100 placeholder:text-stone-400"
                @keyup.enter="handleLogin"
              >
            </div>
            <button 
              class="w-full bg-xieOrange text-white font-semibold py-3 rounded-full hover:bg-[#cf8354] transition shadow-lg shadow-xieOrange/20 mt-2 disabled:opacity-50"
              :disabled="loading"
              @click="handleLogin"
            >
              <span v-if="loading"><i class="fas fa-spinner fa-spin mr-2"></i>登入中...</span>
              <span v-else>登入</span>
            </button>
            
            <div class="text-center pt-4 border-t border-stone-100 dark:border-slate-700 mt-6">
              <p class="text-sm text-stone-500 dark:text-stone-400">
                還沒有帳號？
                <router-link to="/register" class="text-xieOrange font-medium hover:underline">立即註冊</router-link>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useAuthStore } from '../stores/auth'
import { useToast } from 'vue-toastification'

export default {
  name: 'LoginView',
  setup () {
    const toast = useToast()
    const authStore = useAuthStore()
    return { toast, authStore }
  },
  data () {
    return {
      email: '',
      password: '',
      loading: false
    }
  },
  methods: {
    async handleLogin () {
      if (!this.email || !this.password) {
        this.toast.warning('請輸入 Email 和密碼')
        return
      }
      
      this.loading = true
      try {
        await this.authStore.login({
          email: this.email,
          password: this.password
        })
        
        const user = this.authStore.currentUser

        if (!user?.email_verified_at) {
          this.toast.info('已為您寄出驗證信，請至信箱完成驗證')
          this.$router.replace('/verify-email')
          return
        }

        this.toast.success('登入成功！')
        
        const redirect = this.$route.query.redirect || '/profile'
        this.$router.replace(redirect)
      } catch (error) {
        console.error('Login error:', error)
        const msg = this.authStore.error || '登入失敗，請檢查帳號密碼'
        this.toast.error(msg)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

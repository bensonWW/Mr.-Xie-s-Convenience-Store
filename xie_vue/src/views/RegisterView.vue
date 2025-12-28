<template>
  <div class="min-h-screen bg-[#d9cdb4] dark:bg-slate-900 py-12 transition-colors duration-300">
    <div class="flex justify-center px-4">
      <div class="w-full max-w-md bg-white dark:bg-slate-800 rounded-2xl border border-stone-100 dark:border-slate-700 overflow-hidden transition-colors duration-300 shadow-xl">
        <!-- Header -->
        <div class="text-center p-8 pb-6">
          <div class="w-16 h-16 bg-xieOrange/10 dark:bg-xieOrange/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-user-plus text-2xl text-xieOrange"></i>
          </div>
          <h2 class="text-2xl font-bold text-slate-700 dark:text-stone-100 mb-2">建立帳號</h2>
          <p class="text-stone-500 dark:text-stone-400 text-sm">免費註冊成為會員，享受專屬優惠</p>
        </div>

        <!-- Form -->
        <div class="px-8 pb-8 space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">姓名</label>
            <input
              v-model="name"
              type="text"
              placeholder="請輸入姓名"
              class="w-full bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-xieOrange/50 focus:border-xieOrange transition text-slate-700 dark:text-stone-100 placeholder:text-stone-400"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">Email</label>
            <input
              v-model="email"
              type="email"
              placeholder="請輸入電子郵件"
              class="w-full bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-xieOrange/50 focus:border-xieOrange transition text-slate-700 dark:text-stone-100 placeholder:text-stone-400"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">設定密碼</label>
            <input
              v-model="password"
              type="password"
              placeholder="請設定密碼"
              class="w-full bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-xieOrange/50 focus:border-xieOrange transition text-slate-700 dark:text-stone-100 placeholder:text-stone-400"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">確認密碼</label>
            <input
              v-model="passwordConfirm"
              type="password"
              placeholder="請再次輸入密碼"
              class="w-full bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-xieOrange/50 focus:border-xieOrange transition text-slate-700 dark:text-stone-100 placeholder:text-stone-400"
              @keyup.enter="handleRegister"
            >
          </div>
          <button 
            class="w-full bg-xieOrange text-white font-semibold py-3 rounded-full hover:bg-[#cf8354] transition shadow-lg shadow-xieOrange/20 mt-2 disabled:opacity-50"
            :disabled="loading"
            @click="handleRegister"
          >
            <span v-if="loading"><i class="fas fa-spinner fa-spin mr-2"></i>註冊中...</span>
            <span v-else>註冊</span>
          </button>
          
          <div class="text-center pt-4 border-t border-stone-100 dark:border-slate-700 mt-6">
            <p class="text-sm text-stone-500 dark:text-stone-400">
              已有帳號？
              <router-link to="/login" class="text-xieOrange font-medium hover:underline">立即登入</router-link>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api'
import { useAuthStore } from '../stores/auth'
import { useToast } from 'vue-toastification'

export default {
  name: 'RegisterView',
  setup () {
    const toast = useToast()
    const authStore = useAuthStore()
    return { toast, authStore }
  },
  data () {
    return {
      name: '',
      email: '',
      password: '',
      passwordConfirm: '',
      loading: false
    }
  },
  methods: {
    async handleRegister () {
      if (!this.name || !this.email || !this.password || !this.passwordConfirm) {
        this.toast.warning('請填寫完整註冊資料')
        return
      }
      if (this.password !== this.passwordConfirm) {
        this.toast.warning('兩次輸入的密碼不一致')
        return
      }

      this.loading = true
      try {
        await api.post('/register', {
          name: this.name,
          email: this.email,
          password: this.password,
          password_confirmation: this.passwordConfirm
        })
        
        await this.authStore.login({
          email: this.email,
          password: this.password
        })

        this.toast.success('註冊成功！已寄出驗證信')
        this.$router.replace('/verify-email')
      } catch (error) {
        console.error('Register error:', error)
        if (error.response?.data) {
          const msg = error.response.data.message || JSON.stringify(error.response.data)
          this.toast.error('註冊失敗：' + msg)
        } else {
          this.toast.error('註冊失敗，請稍後再試')
        }
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

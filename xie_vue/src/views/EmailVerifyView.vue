<template>
  <div class="min-h-screen bg-wood-50 dark:bg-slate-900 py-12 transition-colors duration-300">
    <div class="container mx-auto px-4 max-w-lg">
      <div class="bg-white dark:bg-slate-800 rounded-2xl border border-stone-100 dark:border-slate-700 p-8 transition-colors duration-300">
        <!-- Header -->
        <div class="text-center mb-8">
          <div class="w-16 h-16 bg-xieOrange/10 dark:bg-xieOrange/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-envelope-open-text text-2xl text-xieOrange"></i>
          </div>
          <h1 class="text-2xl font-bold text-slate-700 dark:text-stone-100 mb-2">驗證您的 Email</h1>
          <p class="text-stone-500 dark:text-stone-400 text-sm">
            我們已寄出 4 位驗證碼到您的信箱
          </p>
        </div>

        <!-- Email Display -->
        <div v-if="userEmail" class="bg-stone-50 dark:bg-slate-700 border border-stone-100 dark:border-slate-600 text-slate-700 dark:text-stone-200 px-4 py-3 rounded-xl mb-6 flex items-center gap-3">
          <i class="fas fa-at text-xieOrange"></i>
          <span class="text-sm">{{ userEmail }}</span>
        </div>

        <!-- Code Input -->
        <div class="space-y-4 mb-6">
          <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-1">驗證碼</label>
          <div class="flex items-center gap-3">
            <input 
              v-model="code" 
              type="text" 
              maxlength="6" 
              class="flex-1 bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl px-4 py-3 text-center text-lg tracking-[0.3em] font-mono focus:outline-none focus:ring-2 focus:ring-xieOrange/50 focus:border-xieOrange transition text-slate-700 dark:text-stone-100" 
              placeholder="輸入驗證碼"
            >
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col gap-3">
          <button 
            class="w-full bg-xieOrange text-white font-semibold px-6 py-3 rounded-full hover:bg-[#cf8354] transition shadow-lg shadow-xieOrange/20 disabled:opacity-50" 
            @click="submitCode" 
            :disabled="isVerifying"
          >
            <i v-if="isVerifying" class="fas fa-spinner fa-spin mr-2"></i>
            {{ isVerifying ? '驗證中...' : '確認驗證' }}
          </button>

          <div class="flex gap-3">
            <button 
              class="flex-1 bg-stone-100 dark:bg-slate-700 hover:bg-stone-200 dark:hover:bg-slate-600 text-slate-700 dark:text-stone-200 font-medium px-4 py-2.5 rounded-full transition disabled:opacity-50" 
              @click="resendEmail" 
              :disabled="isSending"
            >
              <i v-if="isSending" class="fas fa-spinner fa-spin mr-2"></i>
              <i v-else class="fas fa-redo mr-2"></i>
              {{ isSending ? '寄送中...' : '重寄驗證碼' }}
            </button>
            <router-link 
              to="/profile" 
              class="flex-1 text-center border border-stone-200 dark:border-slate-600 text-stone-600 dark:text-stone-300 font-medium px-4 py-2.5 rounded-full hover:bg-stone-50 dark:hover:bg-slate-700 transition"
            >
              返回會員中心
            </router-link>
          </div>
        </div>

        <!-- Helper Text -->
        <p class="text-xs text-stone-400 dark:text-stone-500 mt-6 text-center leading-relaxed">
          若您未收到驗證信，請檢查垃圾郵件匣，或稍後再試。<br>
          如仍有問題，請聯絡客服。
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api'
import { useToast } from 'vue-toastification'
import { useAuthStore } from '@/stores/auth'

export default {
  name: 'EmailVerifyView',
  setup () {
    const toast = useToast()
    const authStore = useAuthStore()
    return { toast, authStore }
  },
  data () {
    return {
      isSending: false,
      isVerifying: false,
      code: ''
    }
  },
  computed: {
    currentUser () {
      return this.authStore.currentUser
    },
    userEmail () {
      return this.currentUser?.email || ''
    },
    isVerified () {
      return !!this.currentUser?.email_verified_at
    }
  },
  watch: {
    isVerified: {
      immediate: true,
      handler (val) {
        if (val) {
          this.$router.replace('/profile')
        }
      }
    }
  },
  mounted () {
    // Automatically send verification code on first page load
    if (!this.isVerified && this.userEmail) {
      this.sendInitialCode()
    }
  },
  methods: {
    async sendInitialCode () {
      // Only send if not already verified
      try {
        await api.post('/email/verification-notification')
        this.toast.info('驗證碼已寄送至您的信箱')
      } catch (error) {
        // Silent fail - user can click resend if needed
        console.error('Initial verification code send failed:', error)
      }
    },
    async resendEmail () {
      this.isSending = true
      try {
        await api.post('/email/verification-notification')
        this.toast.success('驗證碼已重新寄送，請至信箱查收')
      } catch (error) {
        const msg = error.response?.data?.message || '重寄失敗'
        this.toast.error(msg)
      } finally {
        this.isSending = false
      }
    },
    async submitCode () {
      if (!this.code || this.code.length < 4) {
        this.toast.warning('請輸入 4 位驗證碼')
        return
      }
      this.isVerifying = true
      try {
        await api.post('/email/verify-code', { code: this.code })
        this.toast.success('驗證成功！')
        // Refresh user data so email_verified_at is updated
        await this.authStore.fetchUser()
        this.$router.replace('/profile')
      } catch (error) {
        const msg = error.response?.data?.message || '驗證失敗，請檢查驗證碼'
        this.toast.error(msg)
      } finally {
        this.isVerifying = false
      }
    }
  }
}
</script>

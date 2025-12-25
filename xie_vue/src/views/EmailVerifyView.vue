<template>
  <div class="container mx-auto px-4 py-12 max-w-2xl">
    <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-xieOrange">
      <h1 class="text-2xl font-bold text-gray-800 mb-2">請驗證您的 Email</h1>
      <p class="text-gray-600 mb-4">
        我們已寄出 4 位英數驗證碼到您的信箱。請輸入驗證碼完成驗證。
      </p>

      <div v-if="userEmail" class="bg-orange-50 border border-orange-200 text-orange-800 px-4 py-3 rounded mb-4">
        <i class="fas fa-envelope mr-1"></i>
        已登入帳號：<span class="font-semibold">{{ userEmail }}</span>
      </div>

      <div class="flex items-center gap-2 mb-3">
        <input v-model="code" type="text" maxlength="6" class="border border-gray-300 rounded px-3 py-2 text-sm flex-1" placeholder="輸入 4 位驗證碼 (a-z, 0-9)">
        <button class="bg-xieOrange hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded" @click="submitCode" :disabled="isVerifying">
          <i v-if="isVerifying" class="fas fa-spinner fa-spin mr-2"></i>
          {{ isVerifying ? '驗證中…' : '送出驗證' }}
        </button>
      </div>
      <div class="flex gap-2">
        <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded" @click="resendEmail" :disabled="isSending">
          <i v-if="isSending" class="fas fa-spinner fa-spin mr-2"></i>
          {{ isSending ? '寄送中…' : '重寄驗證碼' }}
        </button>
        <router-link to="/profile" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-4 py-2 rounded">
          返回會員中心
        </router-link>
      </div>

      <p class="text-xs text-gray-500 mt-4">
        若您未收到驗證信，請檢查垃圾郵件匣，或稍後再試。若仍有問題，請聯絡客服。
      </p>
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
          // 已驗證則導回會員中心
          this.$router.replace('/profile')
        }
      }
    }
  },
  methods: {
    async resendEmail () {
      this.isSending = true
      try {
        await api.post('/email/verification-notification')
        this.toast.success('驗證碼已重新寄送，請至信箱查收')
      } catch (error) {
        const status = error.response?.status
        const msg = error.response?.data?.message || '目前暫無重寄驗證碼服務'
        this.toast.error(`重寄失敗${status ? ` (狀態: ${status})` : ''}：${msg}`)
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
        // 重新載入使用者資訊或導回會員中心
        this.$router.replace('/profile')
      } catch (error) {
        const status = error.response?.status
        const msg = error.response?.data?.message || '驗證失敗，請檢查驗證碼或重新寄送'
        this.toast.error(`驗證失敗${status ? ` (狀態: ${status})` : ''}：${msg}`)
      } finally {
        this.isVerifying = false
      }
    }
  }
}
</script>

<style scoped>
</style>

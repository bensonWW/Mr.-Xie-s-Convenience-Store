<template>
  <div v-if="visible" class="fixed inset-0 z-modal flex items-center justify-center bg-black/50 backdrop-blur-sm" @click.self="close">
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-stone-100 dark:border-slate-700 shadow-xl w-full max-w-md mx-4 overflow-hidden animate-fade-in">
      <!-- Header -->
      <div class="px-6 py-4 border-b border-stone-100 dark:border-slate-700 flex justify-between items-center">
        <h3 class="font-bold text-lg text-slate-700 dark:text-stone-100 flex items-center gap-2">
          <i class="fas fa-wallet text-xieOrange"></i> 儲值金額
        </h3>
        <button class="w-8 h-8 rounded-full hover:bg-stone-100 dark:hover:bg-slate-700 text-stone-400 hover:text-stone-600 dark:hover:text-stone-200 flex items-center justify-center transition" @click="close">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <!-- Body -->
      <div class="p-6">
        <div class="bg-sky-50 dark:bg-sky-900/20 text-sky-700 dark:text-sky-300 p-3 rounded-xl text-sm mb-6 border border-sky-100 dark:border-sky-800">
          <i class="fas fa-info-circle mr-1"></i> 目前僅支援模擬儲值 (開發測試環境)
        </div>

        <!-- Quick Amount Buttons -->
        <div class="grid grid-cols-3 gap-3 mb-6">
          <button 
            v-for="amount in [100, 500, 1000, 2000, 5000, 10000]" 
            :key="amount"
            @click="topUpAmount = amount"
            class="border rounded-xl py-3 text-sm font-medium transition focus:outline-none"
            :class="topUpAmount === amount 
              ? 'border-xieOrange text-xieOrange bg-xieOrange/10 ring-2 ring-xieOrange/30' 
              : 'border-stone-200 dark:border-slate-600 text-stone-600 dark:text-stone-300 hover:border-xieOrange hover:text-xieOrange'"
          >
            ${{ formatNumber(amount) }}
          </button>
        </div>

        <!-- Custom Input -->
        <div class="mb-6">
          <label class="block text-xs font-medium text-stone-500 dark:text-stone-400 uppercase tracking-wide mb-2">或手動輸入金額</label>
          <div class="relative">
            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-stone-400 font-medium">$</span>
            <input 
              v-model.number="topUpAmount" 
              type="number" 
              min="1" 
              class="w-full bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl pl-8 pr-4 py-3 focus:outline-none focus:border-xieOrange focus:ring-2 focus:ring-xieOrange/30 font-medium text-slate-700 dark:text-stone-100 transition"
            >
          </div>
        </div>

        <!-- Submit Button -->
        <button 
          @click="handleTopUp" 
          :disabled="loading || topUpAmount <= 0"
          class="w-full bg-xieOrange text-white py-3 rounded-full font-medium hover:bg-[#cf8354] transition disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-xieOrange/20"
        >
          <span v-if="loading"><i class="fas fa-spinner fa-spin mr-2"></i>處理中...</span>
          <span v-else>確認儲值</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../../services/api'
import { useToast } from 'vue-toastification'

export default {
  name: 'UserTopUpModal',
  props: {
    visible: {
      type: Boolean,
      default: false
    }
  },
  emits: ['close', 'success'],
  setup () {
    const toast = useToast()
    return { toast }
  },
  data () {
    return {
      topUpAmount: 1000,
      loading: false
    }
  },
  methods: {
    close () {
      this.$emit('close')
    },
    formatNumber (val) {
      return Number(val).toLocaleString()
    },
    async handleTopUp () {
      if (this.topUpAmount <= 0) return
      this.loading = true
      try {
        const res = await api.post('/user/wallet/deposit', {
          amount: this.topUpAmount,
          description: '線上儲值 (Mock)'
        })
        this.$emit('success', res.data)
        this.close()
      } catch (e) {
        console.error('Top-up error:', e)
        const status = e.response?.status
        const backendMessage = e.response?.data?.message || e.response?.data?.error
        this.toast.error(`儲值失敗 (狀態: ${status ?? '未知'})${backendMessage ? '：' + backendMessage : ''}`)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

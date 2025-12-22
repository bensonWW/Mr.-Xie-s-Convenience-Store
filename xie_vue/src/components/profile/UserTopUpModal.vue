<template>
  <div v-if="visible" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm" @click.self="close">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md mx-4 overflow-hidden animate-fade-in-up">
      <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gradient-to-r from-gray-50 to-white">
        <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
          <i class="fas fa-wallet text-xieOrange"></i> 儲值金額
        </h3>
        <button class="text-gray-400 hover:text-red-500 transition" @click="close">
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>
      <div class="p-6">
        <p class="text-sm text-gray-500 mb-4 bg-blue-50 text-blue-700 p-3 rounded border border-blue-100">
          <i class="fas fa-info-circle mr-1"></i> 目前僅支援模擬儲值 (開發測試環境)
        </p>
        <div class="grid grid-cols-3 gap-3 mb-6">
          <button v-for="amount in [100, 500, 1000, 2000, 5000, 10000]" :key="amount"
            @click="topUpAmount = amount"
            class="border rounded py-2 text-sm font-bold transition focus:outline-none"
            :class="topUpAmount === amount ? 'border-xieOrange text-xieOrange bg-orange-50 ring-1 ring-xieOrange' : 'border-gray-200 text-gray-600 hover:border-xieOrange hover:text-xieOrange'">
            ${{ formatNumber(amount) }}
          </button>
        </div>
        <div class="mb-6">
          <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">或手動輸入金額</label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 font-bold">$</span>
            <input v-model.number="topUpAmount" type="number" min="1" class="w-full border border-gray-300 rounded pl-8 pr-4 py-2 focus:outline-none focus:border-xieOrange focus:ring-1 focus:ring-xieOrange font-bold text-gray-700">
          </div>
        </div>
        <button @click="handleTopUp" :disabled="loading || topUpAmount <= 0"
          class="w-full bg-xieOrange text-white py-3 rounded font-bold hover:bg-orange-600 transition disabled:opacity-50 disabled:cursor-not-allowed shadow-md hover:shadow-lg transform active:scale-95">
          <span v-if="loading"><i class="fas fa-spinner fa-spin mr-2"></i>處理中...</span>
          <span v-else>確認儲值</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../../services/api'

export default {
  name: 'UserTopUpModal',
  props: {
    visible: {
      type: Boolean,
      default: false
    }
  },
  emits: ['close', 'success'],
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
        // this.$toast.success('儲值成功！') // Handled by parent
        this.$emit('success', res.data)
        this.close()
      } catch (e) {
        console.error(e)
        this.$toast.error('儲值失敗')
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
/* Utility Classes matching Admin UI Tokens */
.text-xieOrange { color: #ed8936; }
.border-xieOrange { border-color: #ed8936; }
.focus-border-xieOrange:focus { border-color: #ed8936 !important; }
.bg-xieOrange { background-color: #ed8936; }
.hover-bg-orange-600:hover { background-color: #dd6b20; }
.bg-orange-50 { background-color: #fffaf0; }
.ring-xieOrange { --tw-ring-color: #ed8936; }

.animate-fade-in-up {
  animation: fadeInUp 0.3s ease-out;
}
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>

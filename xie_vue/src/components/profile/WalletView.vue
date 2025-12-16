<template>
  <div class="space-y-6">
    <!-- Balance Card -->
    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-xieOrange flex justify-between items-center transition hover:shadow-md">
        <div>
            <h3 class="text-gray-500 text-sm font-bold tracking-wide">我的錢包餘額</h3>
            <div class="text-3xl font-bold text-xieOrange mt-1">NT$ {{ formatNumber(balance) }}</div>
        </div>
        <button @click="showTopUpModal = true" class="bg-xieOrange text-white px-6 py-2 rounded font-bold hover:bg-orange-600 transition shadow-md flex items-center gap-2">
            <i class="fas fa-plus-circle"></i> 儲值
        </button>
    </div>

    <!-- Transaction History -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden min-h-[400px]">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800">交易紀錄</h3>
            <span class="text-xs text-gray-400">最近 20 筆</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left" v-if="transactions.length > 0">
                <thead class="bg-gray-100 text-gray-600 font-bold border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3">時間</th>
                        <th class="px-6 py-3">類型</th>
                        <th class="px-6 py-3">金額</th>
                        <th class="px-6 py-3">說明</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="t in transactions" :key="t.id" class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-gray-500">{{ formatDate(t.created_at) }}</td>
                        <td class="px-6 py-4">
                            <span :class="getTypeClass(t.type)" class="px-2 py-1 rounded text-xs font-bold uppercase tracking-wide">
                                {{ getTypeName(t.type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-bold" :class="isPositive(t.type) ? 'text-green-600' : 'text-red-600'">
                            {{ isPositive(t.type) ? '+' : '-' }}${{ formatNumber(Math.abs(t.amount)) }}
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ t.description }}</td>
                    </tr>
                </tbody>
            </table>
            <div v-else class="flex flex-col items-center justify-center h-64 text-gray-400">
                <i class="fas fa-receipt text-4xl mb-3 text-gray-300"></i>
                <p>尚無交易紀錄</p>
            </div>
        </div>
    </div>

    <!-- Top Up Modal -->
    <div v-if="showTopUpModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm" @click.self="showTopUpModal = false">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-md mx-4 overflow-hidden animate-fade-in-up">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gradient-to-r from-gray-50 to-white">
                <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                    <i class="fas fa-wallet text-xieOrange"></i> 儲值金額
                </h3>
                <button class="text-gray-400 hover:text-red-500 transition" @click="showTopUpModal = false">
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
  </div>
</template>

<script>
import api from '../../services/api'

export default {
  name: 'WalletView',
  data () {
    return {
      balance: 0,
      transactions: [],
      showTopUpModal: false,
      topUpAmount: 1000,
      loading: false
    }
  },
  created () {
    this.fetchWallet()
  },
  methods: {
    async fetchWallet () {
      try {
        const res = await api.get('/user/wallet')
        this.balance = res.data.balance
        this.transactions = res.data.transactions
      } catch (e) {
        console.error(e)
        // this.$toast.error('無法載入錢包資訊')
      }
    },
    async handleTopUp () {
      if (this.topUpAmount <= 0) return
      this.loading = true
      try {
        const res = await api.post('/user/wallet/deposit', {
          amount: this.topUpAmount,
          description: '線上儲值 (Mock)'
        })
        this.$toast.success('儲值成功！')
        this.balance = res.data.balance
        // Add new transaction to list
        this.transactions.unshift(res.data.transaction)
        this.showTopUpModal = false
      } catch (e) {
        console.error(e)
        this.$toast.error('儲值失敗')
      } finally {
        this.loading = false
      }
    },
    formatDate (dateString) {
      if (!dateString) return ''
      return new Date(dateString).toLocaleString()
    },
    formatNumber (val) {
      return Number(val).toLocaleString()
    },
    getTypeName (type) {
      const map = {
        deposit: '儲值',
        withdraw: '提領/扣款',
        payment: '消費支付',
        refund: '退款'
      }
      return map[type] || type
    },
    getTypeClass (type) {
      const map = {
        deposit: 'bg-green-100 text-green-700',
        withdraw: 'bg-red-100 text-red-700',
        payment: 'bg-blue-100 text-blue-700',
        refund: 'bg-purple-100 text-purple-700'
      }
      return map[type] || 'bg-gray-100 text-gray-600'
    },
    isPositive (type) {
      return ['deposit', 'refund'].includes(type)
    }
  }
}
</script>

<style scoped>
/* Utility Classes matching Admin UI Tokens */
.bg-xieBlue { background-color: #2d3748; }
.text-xieBlue { color: #2d3748; }
.bg-xieOrange { background-color: #ed8936; }
.text-xieOrange { color: #ed8936; }
.border-xieOrange { border-color: #ed8936; }
.focus-border-xieOrange:focus { border-color: #ed8936 !important; }
.bg-orange-50 { background-color: #fffaf0; }
.hover-text-red-500:hover { color: #ef4444; }

.animate-fade-in-up {
  animation: fadeInUp 0.3s ease-out;
}
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>

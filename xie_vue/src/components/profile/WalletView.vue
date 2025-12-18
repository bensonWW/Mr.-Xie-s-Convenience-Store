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
    <UserTopUpModal
      :visible="showTopUpModal"
      @close="showTopUpModal = false"
      @success="handleTopUpSuccess"
    />
  </div>
</template>

<script>
import api from '../../services/api'
import UserTopUpModal from './UserTopUpModal.vue'

export default {
  name: 'WalletView',
  components: {
    UserTopUpModal
  },
  data () {
    return {
      balance: 0,
      transactions: [],
      showTopUpModal: false
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
      }
    },
    handleTopUpSuccess (data) {
      if (data && data.balance !== undefined) {
         this.balance = data.balance
         if (data.transaction) {
            this.transactions.unshift(data.transaction)
         }
         this.$toast.success('儲值成功！')
      } else {
         // Fallback re-fetch if data is incomplete
         this.fetchWallet()
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
</style>

<template>
  <div class="space-y-6">
    <!-- Balance Card -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-stone-100 dark:border-slate-700 p-6 flex justify-between items-center transition-colors duration-300">
      <div>
        <h3 class="text-stone-500 dark:text-stone-400 text-sm font-medium tracking-wide">我的錢包餘額</h3>
        <div class="text-3xl font-bold text-xieOrange mt-1">{{ formatPrice(balance) }}</div>
      </div>
      <button @click="showTopUpModal = true" class="bg-xieOrange text-white px-6 py-3 rounded-full font-medium hover:bg-[#cf8354] transition shadow-lg shadow-xieOrange/20 flex items-center gap-2">
        <i class="fas fa-plus-circle"></i> 儲值
      </button>
    </div>

    <!-- Transaction History -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-stone-100 dark:border-slate-700 overflow-hidden min-h-[400px] transition-colors duration-300">
      <div class="px-6 py-4 border-b border-stone-100 dark:border-slate-700 flex justify-between items-center">
        <h3 class="font-semibold text-slate-700 dark:text-stone-100">交易紀錄</h3>
        <span class="text-xs text-stone-400 dark:text-stone-500">最近 20 筆</span>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left" v-if="transactions.length > 0">
          <thead class="bg-stone-50 dark:bg-slate-700 text-stone-600 dark:text-stone-300 font-medium border-b border-stone-100 dark:border-slate-600">
            <tr>
              <th class="px-6 py-3">時間</th>
              <th class="px-6 py-3">類型</th>
              <th class="px-6 py-3">金額</th>
              <th class="px-6 py-3">說明</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-stone-100 dark:divide-slate-700">
            <tr v-for="t in transactions" :key="t.id" class="hover:bg-stone-50 dark:hover:bg-slate-700/50 transition">
              <td class="px-6 py-4 text-stone-500 dark:text-stone-400">{{ formatDate(t.created_at) }}</td>
              <td class="px-6 py-4">
                <span :class="getTypeClass(t.type)" class="px-2.5 py-1 rounded-full text-xs font-medium">
                  {{ getTypeName(t.type) }}
                </span>
              </td>
              <td class="px-6 py-4 font-bold" :class="isPositive(t.type) ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
                {{ isPositive(t.type) ? '+' : '-' }}{{ formatPrice(Math.abs(t.amount)) }}
              </td>
              <td class="px-6 py-4 text-stone-600 dark:text-stone-300">{{ t.description }}</td>
            </tr>
          </tbody>
        </table>
        <div v-else class="flex flex-col items-center justify-center h-64 text-stone-400 dark:text-stone-500">
          <i class="fas fa-receipt text-4xl mb-3 opacity-50"></i>
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
import { formatPrice } from '../../utils/currency'
import UserTopUpModal from './UserTopUpModal.vue'
import { useToast } from 'vue-toastification'

export default {
  name: 'WalletView',
  components: {
    UserTopUpModal
  },
  setup () {
    const toast = useToast()
    return { toast }
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
    formatPrice,
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
      if (data?.balance !== undefined) {
        this.balance = data.balance
        if (data.transaction) {
          this.transactions.unshift(data.transaction)
        }
        this.toast.success('儲值成功！')
      } else {
        this.fetchWallet()
      }
    },
    formatDate (dateString) {
      if (!dateString) return ''
      return new Date(dateString).toLocaleString()
    },
    getTypeName (type) {
      const map = {
        deposit: '儲值',
        withdraw: '提領',
        withdrawal: '提領',
        payment: '消費',
        refund: '退款'
      }
      return map[type] || type
    },
    getTypeClass (type) {
      const map = {
        deposit: 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
        withdraw: 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400',
        withdrawal: 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400',
        payment: 'bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400',
        refund: 'bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400'
      }
      return map[type] || 'bg-stone-100 dark:bg-slate-600 text-stone-600 dark:text-stone-300'
    },
    isPositive (type) {
      return ['deposit', 'refund'].includes(type)
    }
  }
}
</script>

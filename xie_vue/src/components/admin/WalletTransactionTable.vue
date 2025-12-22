<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
      <h3 class="font-bold text-gray-800 flex items-center gap-2">
        <i class="fas fa-history text-gray-400"></i>
        錢包交易紀錄
      </h3>
      <button @click="$emit('open-modal')" class="text-xieOrange text-sm font-bold hover:text-orange-600 transition flex items-center gap-1">
        <i class="fas fa-plus-circle"></i> 新增
      </button>
    </div>
    <div v-if="!transactions || transactions.length === 0" class="flex flex-col items-center justify-center py-12 text-gray-400">
      <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
         <i class="fas fa-receipt text-2xl text-gray-300"></i>
      </div>
      <p class="text-sm font-medium">暫無交易紀錄</p>
    </div>
    <div v-else class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50/80 text-gray-500 font-bold uppercase tracking-wider text-xs">
          <tr>
            <th class="p-4 text-left">ID</th>
            <th class="p-4 text-center">類型</th>
            <th class="p-4 text-right">金額</th>
            <th class="p-4 text-left">說明</th>
            <th class="p-4 text-right">時間</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr v-for="tx in transactions" :key="tx.id" class="hover:bg-blue-50/30 transition-colors duration-150">
            <td class="p-4 text-gray-400 font-mono text-xs">#{{ tx.id }}</td>
            <td class="p-4 text-center">
              <span :class="getTypeClass(tx.type)" class="px-3 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1">
                 <i class="fas" :class="getTypeIcon(tx.type)"></i>
                {{ getTypeLabel(tx.type) }}
              </span>
            </td>
            <td class="p-4 text-right font-bold text-base" :class="isPositive(tx.type) ? 'text-green-500' : 'text-red-500'">
              {{ isPositive(tx.type) ? '+' : '-' }}${{ formatNumber(Math.abs(tx.amount)) }}
            </td>
            <td class="p-4 text-left text-gray-600 max-w-[200px] truncate" :title="tx.description">
              {{ tx.description || '-' }}
            </td>
            <td class="p-4 text-right text-gray-400 text-xs whitespace-nowrap">
              {{ formatDate(tx.created_at) }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: 'WalletTransactionTable',
  props: {
    transactions: {
      type: Array,
      default: () => []
    }
  },
  emits: ['open-modal'],
  methods: {
    formatDate (dateString) {
      if (!dateString) return ''
      return new Date(dateString).toLocaleString('zh-TW', { hour12: false })
    },
    formatNumber (val) {
      return Number(val).toLocaleString()
    },
    isPositive (type) {
      return ['deposit', 'refund'].includes(type)
    },
    getTypeLabel (type) {
      const map = {
        deposit: '儲值',
        withdraw: '扣款',
        payment: '消費',
        refund: '退款'
      }
      return map[type] || type
    },
    getTypeIcon (type) {
      const map = {
        deposit: 'fa-arrow-down',
        withdraw: 'fa-arrow-up',
        payment: 'fa-shopping-bag',
        refund: 'fa-undo'
      }
      return map[type]
    },
    getTypeClass (type) {
      const map = {
        deposit: 'bg-green-100 text-green-600 border border-green-200',
        withdraw: 'bg-red-50 text-red-500 border border-red-100',
        payment: 'bg-blue-50 text-blue-500 border border-blue-100',
        refund: 'bg-purple-50 text-purple-500 border border-purple-100'
      }
      return map[type] || 'bg-gray-100 text-gray-500'
    }
  }
}
</script>

<style scoped>
.text-xieOrange { color: #ed8936; }
</style>

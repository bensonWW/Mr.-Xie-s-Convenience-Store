<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h3 class="font-bold text-gray-800 border-l-4 border-xieOrange pl-3">錢包交易紀錄</h3>
      <button @click="$emit('open-modal')" class="bg-xieOrange text-white text-xs px-4 py-2 rounded font-bold hover:bg-orange-600 transition">
        <i class="fas fa-coins mr-1"></i> 新增交易
      </button>
    </div>
    <div v-if="!transactions || transactions.length === 0" class="text-center py-12 text-gray-500 bg-gray-50 rounded">
      <i class="fas fa-receipt text-4xl mb-4 text-gray-300"></i>
      <p>暫無交易紀錄</p>
    </div>
    <div v-else class="overflow-x-auto">
      <table class="w-full text-sm text-center">
        <thead class="bg-gray-100 text-gray-600 font-bold">
          <tr>
            <th class="p-3">ID</th>
            <th class="p-3">類型</th>
            <th class="p-3">金額</th>
            <th class="p-3 text-left">備註</th>
            <th class="p-3">時間</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="tx in transactions" :key="tx.id" class="hover:bg-gray-50">
            <td class="p-3 text-gray-500">#{{ tx.id }}</td>
            <td class="p-3">
              <span :class="getTypeClass(tx.type)" class="px-2 py-1 rounded text-xs font-bold uppercase">
                {{ tx.type }}
              </span>
            </td>
            <td class="p-3 font-bold" :class="isPositive(tx.type) ? 'text-green-600' : 'text-red-600'">
              {{ isPositive(tx.type) ? '+' : '-' }}${{ formatNumber(Math.abs(tx.amount)) }}
            </td>
            <td class="p-3 text-left text-gray-600">{{ tx.description || '-' }}</td>
            <td class="p-3 text-gray-500">{{ formatDate(tx.created_at) }}</td>
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
      return new Date(dateString).toLocaleString()
    },
    formatNumber (val) {
      return Number(val).toLocaleString()
    },
    isPositive (type) {
      return ['deposit', 'refund'].includes(type)
    },
    getTypeClass (type) {
      const map = {
        deposit: 'bg-green-100 text-green-700',
        withdraw: 'bg-red-100 text-red-700',
        payment: 'bg-blue-100 text-blue-700',
        refund: 'bg-purple-100 text-purple-700'
      }
      return map[type] || 'bg-gray-100 text-gray-600'
    }
  }
}
</script>

<style scoped>
.bg-xieOrange { background-color: #ed8936; }
.border-xieOrange { border-color: #ed8936; }
</style>

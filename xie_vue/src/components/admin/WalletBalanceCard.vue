<template>
  <div class="bg-gray-50 rounded p-4 mb-4 border border-gray-100 transition hover:shadow-sm">
    <div class="text-xs text-gray-400 mb-1">錢包餘額</div>
    <div class="font-bold text-2xl text-xieOrange mb-3">
      ${{ formatNumber(balance) }}
    </div>
    <button
      @click="$emit('open-modal')"
      class="w-full bg-white border border-gray-300 text-gray-700 text-xs font-bold py-2 rounded hover:bg-gray-100 transition flex items-center justify-center gap-2"
    >
      <i class="fas fa-coins"></i> 管理錢包
    </button>
  </div>
</template>

<script>
export default {
  name: 'WalletBalanceCard',
  props: {
    balance: {
      type: [Number, String],
      default: 0
    }
  },
  emits: ['open-modal'],
  methods: {
    formatNumber (val) {
      if (!val) return '0'
      return Number(val).toLocaleString()
    }
  }
}
</script>

<style scoped>
.text-xieOrange { color: #ed8936; }
</style>

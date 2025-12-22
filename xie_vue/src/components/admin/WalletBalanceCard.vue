<template>
  <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 rounded-xl p-6 mb-6 shadow-xl text-white transform hover:scale-[1.01] transition-transform duration-300">
    <!-- Decorative Background Icon -->
    <i class="fas fa-wallet absolute -bottom-4 -right-4 text-9xl text-white opacity-10 pointer-events-none"></i>
    <div class="relative z-10">
      <div class="flex items-center gap-2 mb-2 opacity-80">
        <i class="fas fa-coins text-yellow-300"></i>
        <div class="text-sm font-medium tracking-wide">目前錢包餘額</div>
      </div>
      <div class="font-bold text-4xl mb-6 tracking-tight drop-shadow-md">
        ${{ formatNumber(balance) }}
      </div>
      <button
        @click="$emit('open-modal')"
        class="w-full bg-white/20 backdrop-blur-md border border-white/30 text-white text-sm font-bold py-3 rounded-lg hover:bg-white/30 hover:border-white/50 transition-all duration-200 flex items-center justify-center gap-2 group shadow-lg"
      >
        <i class="fas fa-cog group-hover:rotate-90 transition-transform duration-500"></i>
        管理錢包 (加值/扣款)
      </button>
    </div>
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
/* No extra css needed, Tailwind handles it */
</style>

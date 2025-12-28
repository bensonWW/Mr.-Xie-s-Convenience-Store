<template>
  <div class="space-y-6">
    <!-- Welcome Card -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-stone-100 dark:border-slate-700 p-6 flex flex-col md:flex-row items-center justify-between gap-6 transition-colors duration-300">
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-xieOrange/10 dark:bg-xieOrange/20 rounded-full flex items-center justify-center">
          <i class="fas fa-sun text-xieOrange text-xl"></i>
        </div>
        <div class="text-slate-700 dark:text-stone-100">
          <h2 class="text-xl font-bold">{{ greeting }}，{{ user?.name || '會員' }}！</h2>
          <p class="text-sm text-stone-500 dark:text-stone-400 mt-1">累積消費金額：<span class="text-xieOrange font-bold text-lg">NT$ 0</span></p>
        </div>
      </div>
      <div class="flex gap-8 text-center">
        <div>
          <div class="text-2xl font-bold text-slate-700 dark:text-stone-100">NT$ {{ user?.balance || 0 }}</div>
          <div class="text-xs text-stone-500 dark:text-stone-400">錢包餘額</div>
        </div>
        <div>
          <div class="text-2xl font-bold text-slate-700 dark:text-stone-100">{{ coupons.length }}</div>
          <div class="text-xs text-stone-500 dark:text-stone-400">可用折價券</div>
        </div>
        <div>
          <div class="text-2xl font-bold text-slate-700 dark:text-stone-100">{{ orders.length }}</div>
          <div class="text-xs text-stone-500 dark:text-stone-400">本月下單</div>
        </div>
      </div>
    </div>

    <!-- Order Status Grid -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-stone-100 dark:border-slate-700 p-6 transition-colors duration-300">
      <h3 class="font-semibold text-slate-700 dark:text-stone-100 mb-4 border-b border-stone-100 dark:border-slate-700 pb-3">訂單狀態一覽</h3>
      <div class="grid grid-cols-5 gap-2 text-center">
        <!-- Pending Payment -->
        <div class="group relative py-3 cursor-pointer hover:bg-stone-50 dark:hover:bg-slate-700 rounded-xl transition" @click="$emit('filter-selected', 'pending_payment')">
          <div class="text-3xl text-stone-400 dark:text-stone-500 group-hover:text-xieOrange mb-2 transition relative inline-block">
            <i class="fas fa-wallet"></i>
            <span v-if="countStatus('pending_payment') > 0" class="absolute -top-2 -right-3 bg-rose-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center border-2 border-white dark:border-slate-800">{{ countStatus('pending_payment') }}</span>
          </div>
          <div class="text-sm text-stone-600 dark:text-stone-400 group-hover:text-xieOrange transition">待付款</div>
        </div>

        <!-- Processing -->
        <div class="group relative py-3 cursor-pointer hover:bg-stone-50 dark:hover:bg-slate-700 rounded-xl transition" @click="$emit('filter-selected', 'processing')">
          <div class="text-3xl text-stone-400 dark:text-stone-500 group-hover:text-xieOrange mb-2 transition relative inline-block">
            <i class="fas fa-box-open"></i>
            <span v-if="countStatus('processing') > 0" class="absolute -top-2 -right-3 bg-stone-400 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center border-2 border-white dark:border-slate-800">{{ countStatus('processing') }}</span>
          </div>
          <div class="text-sm text-stone-600 dark:text-stone-400 group-hover:text-xieOrange transition">待出貨</div>
        </div>

        <!-- Shipped -->
        <div class="group relative py-3 cursor-pointer hover:bg-stone-50 dark:hover:bg-slate-700 rounded-xl transition" @click="$emit('filter-selected', 'shipped')">
          <div class="text-3xl text-stone-400 dark:text-stone-500 group-hover:text-xieOrange mb-2 transition relative inline-block">
            <i class="fas fa-truck"></i>
            <span v-if="countStatus('shipped') > 0" class="absolute -top-2 -right-3 bg-sky-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center border-2 border-white dark:border-slate-800">{{ countStatus('shipped') }}</span>
          </div>
          <div class="text-sm text-stone-600 dark:text-stone-400 group-hover:text-xieOrange transition">待收貨</div>
        </div>

        <!-- Completed -->
        <div class="group relative py-3 cursor-pointer hover:bg-stone-50 dark:hover:bg-slate-700 rounded-xl transition" @click="$emit('filter-selected', 'completed')">
          <div class="text-3xl text-stone-400 dark:text-stone-500 group-hover:text-xieOrange mb-2 transition relative inline-block">
            <i class="fas fa-clipboard-check"></i>
            <span v-if="countStatus('completed') > 0" class="absolute -top-2 -right-3 bg-emerald-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center border-2 border-white dark:border-slate-800">{{ countStatus('completed') }}</span>
          </div>
          <div class="text-sm text-stone-600 dark:text-stone-400 group-hover:text-xieOrange transition">已完成</div>
        </div>

        <!-- Cancelled/Refunded -->
        <div class="group relative py-3 cursor-pointer hover:bg-stone-50 dark:hover:bg-slate-700 rounded-xl transition" @click="$emit('filter-selected', 'refunded')">
          <div class="text-3xl text-stone-400 dark:text-stone-500 group-hover:text-xieOrange mb-2 transition">
            <i class="fas fa-undo-alt"></i>
          </div>
          <div class="text-sm text-stone-600 dark:text-stone-400 group-hover:text-xieOrange transition">退貨/取消</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DashboardStats',
  props: {
    user: Object,
    coupons: {
      type: Array,
      default: () => []
    },
    orders: {
      type: Array,
      default: () => []
    }
  },
  computed: {
    greeting () {
      const hour = new Date().getHours()
      if (hour < 12) return '早安'
      if (hour < 18) return '午安'
      return '晚安'
    }
  },
  methods: {
    countStatus (status) {
      return this.orders.filter(o => o.status === status).length
    }
  }
}
</script>

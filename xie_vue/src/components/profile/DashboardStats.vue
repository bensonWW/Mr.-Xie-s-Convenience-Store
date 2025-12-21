<template>
  <div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm p-6 flex flex-col md:flex-row items-center justify-between gap-6 border-l-4 border-xieBlue">
      <div class="flex items-center gap-4">
        <div class="text-gray-700">
          <h2 class="text-xl font-bold">早安，{{ user.name }}！</h2>
          <p class="text-sm text-gray-500 mt-1">累積消費金額：<span class="text-xieOrange font-bold text-lg">NT$ 0</span></p>
        </div>
      </div>
      <div class="flex gap-8 text-center">
        <div>
          <div class="text-2xl font-bold text-xieBlue">NT$ {{ user.balance || 0 }}</div>
          <div class="text-xs text-gray-500">錢包餘額</div>
        </div>
        <div>
          <div class="text-2xl font-bold text-xieBlue">{{ coupons.length }}</div>
          <div class="text-xs text-gray-500">可用折價券</div>
        </div>
        <div>
          <div class="text-2xl font-bold text-xieBlue">{{ orders.length }}</div>
          <div class="text-xs text-gray-500">本月下單</div>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
      <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">訂單狀態</h3>
      <div class="grid grid-cols-5 gap-2 text-center">
        <div class="group relative py-2 cursor-pointer" @click="$emit('filter-selected', 'pending_payment')">
          <div class="text-3xl text-gray-400 group-hover:text-xieOrange mb-2 transition relative inline-block">
            <i class="fas fa-wallet"></i>
            <span v-if="countStatus('pending_payment') > 0" class="absolute -top-2 -right-3 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center border-2 border-white">{{ countStatus('pending_payment') }}</span>
          </div>
          <div class="text-sm text-gray-600 group-hover:text-xieOrange">待付款</div>
        </div>

        <div class="group relative py-2 cursor-pointer" @click="$emit('filter-selected', 'processing')">
          <div class="text-3xl text-gray-400 group-hover:text-xieOrange mb-2 transition relative inline-block">
            <i class="fas fa-box-open"></i>
            <span v-if="countStatus('processing') > 0" class="absolute -top-2 -right-3 bg-gray-400 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center border-2 border-white">{{ countStatus('processing') }}</span>
          </div>
          <div class="text-sm text-gray-600 group-hover:text-xieOrange">待出貨</div>
        </div>

        <div class="group relative py-2 cursor-pointer" @click="$emit('filter-selected', 'shipped')">
          <div class="text-3xl text-gray-400 group-hover:text-xieOrange mb-2 transition relative inline-block">
            <i class="fas fa-truck"></i>
            <span v-if="countStatus('shipped') > 0" class="absolute -top-2 -right-3 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center border-2 border-white">{{ countStatus('shipped') }}</span>
          </div>
          <div class="text-sm text-gray-600 group-hover:text-xieOrange">待收貨</div>
        </div>

        <div class="group relative py-2 cursor-pointer" @click="$emit('filter-selected', 'completed')">
          <div class="text-3xl text-gray-400 group-hover:text-xieOrange mb-2 transition">
            <i class="fas fa-clipboard-check"></i>
            <span v-if="countStatus('completed') > 0" class="absolute -top-2 -right-3 bg-green-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center border-2 border-white">{{ countStatus('completed') }}</span>
          </div>
          <div class="text-sm text-gray-600 group-hover:text-xieOrange">已完成</div>
        </div>

        <div class="group relative py-2 cursor-pointer" @click="$emit('filter-selected', 'refunded')">
          <div class="text-3xl text-gray-400 group-hover:text-xieOrange mb-2 transition">
            <i class="fas fa-undo-alt"></i>
          </div>
          <div class="text-sm text-gray-600 group-hover:text-xieOrange">退貨/取消</div>
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
  methods: {
    countStatus (status) {
      return this.orders.filter(o => o.status === status).length
    }
  }
}
</script>

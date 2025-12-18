<template>
  <div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-100">
        <div class="flex justify-between items-center mb-4">
          <h3 class="font-bold text-gray-800">購物紀錄</h3>
        </div>
        <!-- Status Tabs -->
        <div class="flex space-x-6 text-sm overflow-x-auto no-scrollbar">
          <button
            v-for="tab in tabs"
            :key="tab.value"
            @click="currentTab = tab.value"
            class="pb-2 whitespace-nowrap transition-colors border-b-2"
            :class="currentTab === tab.value ? 'border-xieOrange text-xieOrange font-bold' : 'border-transparent text-gray-500 hover:text-gray-700'"
          >
            {{ tab.label }}
          </button>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left" v-if="orders.length > 0">
          <thead class="bg-gray-50 text-gray-600">
            <tr>
              <th class="px-6 py-3">訂單編號</th>
              <th class="px-6 py-3">下單日期</th>
              <th class="px-6 py-3">總金額</th>
              <th class="px-6 py-3">狀態</th>
              <th class="px-6 py-3 text-right">操作</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="order in filteredOrders" :key="order.id" class="hover:bg-gray-50 transition">
              <td class="px-6 py-4 font-bold text-xieBlue">#{{ order.id }}</td>
              <td class="px-6 py-4 text-gray-500">{{ formatDate(order.created_at) }}</td>
              <td class="px-6 py-4 font-bold">NT$ {{ order.total_amount }}</td>
              <td class="px-6 py-4">
                <span class="py-1 px-3 rounded-full text-xs font-bold"
                      :class="{
                        'bg-red-100 text-red-600': order.status === 'pending_payment',
                        'bg-blue-100 text-blue-600': order.status === 'shipped',
                        'bg-green-100 text-green-600': order.status === 'completed',
                        'bg-gray-100 text-gray-600': order.status === 'processing'
                      }">
                  {{ getStatusText(order.status) }}
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <button v-if="order.status === 'pending_payment'" class="bg-xieOrange text-white px-3 py-1 rounded text-xs hover:bg-orange-600 transition mr-2" @click="openOrderDetails(order.id)">去付款</button>
                <button v-if="order.status === 'processing'" class="bg-red-50 text-red-600 border border-red-200 px-3 py-1 rounded text-xs hover:bg-red-100 transition mr-2" @click="cancelOrder(order)">取消訂單</button>
                <button class="text-gray-400 hover:text-gray-600 text-xs" @click="openOrderDetails(order.id)">詳情</button>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-else class="p-8 text-center text-gray-400">
          尚無訂單紀錄
        </div>
      </div>
    </div>

    <!-- Order Details Modal -->
    <div v-if="showOrderDetails && selectedOrder" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="showOrderDetails = false">
      <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 overflow-hidden animate-fade-in-up">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
           <h3 class="font-bold text-lg text-gray-800">訂單詳情 #{{ selectedOrder.id }}</h3>
           <button class="text-gray-400 hover:text-gray-600" @click="showOrderDetails = false"><i class="fas fa-times"></i></button>
        </div>

        <div class="p-6 space-y-4">
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div class="text-gray-500">下單日期</div>
            <div class="font-bold text-gray-800 text-right">{{ new Date(selectedOrder.created_at).toLocaleString() }}</div>
            <div class="text-gray-500">訂單狀態</div>
            <div class="font-bold text-right text-xieOrange">{{ getStatusText(selectedOrder.status) }}</div>
            <div class="text-gray-500">物流狀態</div>
            <div class="font-bold text-right text-blue-600">{{ getLogisticsStatus(selectedOrder.status) }}</div>
            <div class="text-gray-500">總金額</div>
            <div class="font-bold text-right text-xl">NT$ {{ selectedOrder.total_amount }}</div>
          </div>

          <div class="border-t border-gray-100 pt-4">
             <h4 class="font-bold text-gray-700 mb-3 text-sm">商品列表</h4>
             <ul class="space-y-2">
                <li v-for="item in selectedOrder.items" :key="item.id" class="flex justify-between items-center text-sm">
                  <span class="text-gray-800 truncate flex-1 mr-4">{{ item.product ? item.product.name : '未知商品' }}</span>
                  <div class="text-gray-500 whitespace-nowrap">
                    <span>x{{ item.quantity }}</span>
                    <span class="ml-3 font-bold text-gray-800">NT$ {{ item.price * item.quantity }}</span>
                  </div>
                </li>
             </ul>
          </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
          <button class="px-4 py-2 border border-gray-300 rounded text-gray-600 hover:bg-gray-100 transition text-sm" @click="showOrderDetails = false">關閉</button>
          <button v-if="selectedOrder.status === 'pending_payment'" class="px-6 py-2 bg-xieOrange text-white rounded font-bold hover:bg-orange-600 transition text-sm shadow-md" @click="payOrder">立即付款</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../../services/api'

export default {
  name: 'OrderHistory',
  props: {
    orders: {
      type: Array,
      default: () => []
    }
  },
  emits: ['order-updated'],
  data () {
    return {
      showOrderDetails: false,
      selectedOrder: null,
      currentTab: 'all',
      tabs: [
        { label: '全部', value: 'all' },
        { label: '處理中', value: 'processing' },
        { label: '已出貨', value: 'shipped' },
        { label: '已送達', value: 'delivered' },
        { label: '已完成', value: 'completed' },
        { label: '已取消/退款', value: 'refunded' } // Group cancelled/refunded
      ]
    }
  },
  computed: {
    // filteredOrders removed as we now filter via API
  },
  watch: {
    currentTab () {
       this.fetchOrders()
    }
  },
  mounted () {
    this.fetchOrders()
  },
  methods: {
    async fetchOrders () {
      try {
        const params = {}
        if (this.currentTab !== 'all') {
           // Map 'refunded' tab to 'cancelled' status if backend doesn't support grouping yet, or handle multiple.
           // For now simple mapping:
           if (this.currentTab === 'refunded') {
             // Backend doesn't support array for status yet in our simple change, 
             // so let's just fetch all and filter locally for this special complex tab, 
             // OR fetch 'cancelled' and 'refunded' separately?
             // Let's stick to fetch all for 'refunded' tab for now to avoid complexity, 
             // or just search 'cancelled'.
             params.status = 'cancelled' 
           } else {
             params.status = this.currentTab
           }
        }
        
        const response = await api.get('/orders', { params })
        this.$emit('update:orders', response.data) // If parent controls orders
        // But wait, props says 'orders'. The component doesn't fetch itself in original code?
        // Original code: "fetchOrders" method calls api.get('/orders') and sets "this.orders = ..." in ProfileView!
        // OrderHistory received "orders" as prop.
        // So I should emit event to parent to fetch with filter?
        // OR refactor OrderHistory to fetch its own data?
        // The instructions say "Update OrderHistory.vue".
        // Let's change ProfileView to handle the filter change event from OrderHistory.
        
        // Actually, let's keep it simple: Emitting an event 'filter-change' to parent is better design.
        this.$emit('filter-change', this.currentTab)
      } catch (e) {
        console.error(e)
      }
    },
    formatDate (dateString) {
      return new Date(dateString).toLocaleDateString()
    },
    getStatusText (status) {
      const map = {
        pending_payment: '待付款',
        processing: '處理中',
        shipped: '已出貨',
        delivered: '已送達',
        completed: '已完成',
        cancelled: '已取消',
        returned: '已退貨'
      }
      return map[status] || status
    },
    getLogisticsStatus (status) {
      const map = {
        pending_payment: '待付款',
        processing: '備貨中',
        shipped: '已出貨',
        completed: '已送達',
        cancelled: '已取消'
      }
      return map[status] || status
    },
    async openOrderDetails (orderId) {
      try {
        const response = await api.get(`/orders/${orderId}`)
        this.selectedOrder = response.data
        this.showOrderDetails = true
      } catch (error) {
        console.error('Fetch order details error:', error)
        this.$toast.error('無法取得訂單詳情')
      }
    },
    async payOrder () {
      if (!this.selectedOrder) return
      try {
        await api.post(`/orders/${this.selectedOrder.id}/pay`)
        this.$toast.success('付款成功！')
        this.showOrderDetails = false
        this.$emit('order-updated')
      } catch (error) {
        console.error('Payment error:', error)
        this.$toast.error('付款失敗，請稍後再試。')
      }
    },
    async cancelOrder (order) {
      if (!confirm(`確定要取消訂單 #${order.id} 嗎？\n(若已付款將會全額退款至錢包)`)) return

      try {
        await api.post(`/orders/${order.id}/refund`)
        this.$toast.success('訂單已取消並退款')
        this.$emit('order-updated')
      } catch (error) {
        console.error('Refund error:', error)
        this.$toast.error(error.response?.data?.message || '取消失敗')
      }
    }
  }
}
</script>

<style scoped>
/* Basic fade animation */
.animate-fade-in-up {
  animation: fadeInUp 0.3s ease-out;
}
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>

<template>
  <div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-100 table-header">
        <div class="flex justify-between items-center">
          <h3 class="font-bold text-gray-800 flex items-center">
             購物紀錄
             <span v-if="currentTab !== 'all'" class="ml-2 text-sm font-normal text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
               {{ filterTitle }}
             </span>
          </h3>
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
            <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50 transition">
              <td class="px-6 py-4 font-bold text-xieBlue">#{{ order.id }}</td>
              <td class="px-6 py-4 text-gray-500">{{ formatDate(order.created_at) }}</td>
              <td class="px-6 py-4 font-bold">{{ formatPrice(order.total_amount) }}</td>
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
            <div class="font-bold text-right text-xl">{{ formatPrice(selectedOrder.total_amount) }}</div>
          </div>

          <div class="border-t border-gray-100 pt-4">
             <h4 class="font-bold text-gray-700 mb-3 text-sm">商品列表</h4>
             <ul class="space-y-2">
                <li v-for="item in selectedOrder.items" :key="item.id" class="flex justify-between items-center text-sm">
                  <span class="text-gray-800 truncate flex-1 mr-4">{{ item.product ? item.product.name : '未知商品' }}</span>
                  <div class="text-gray-500 whitespace-nowrap">
                    <span>x{{ item.quantity }}</span>
                    <span class="ml-3 font-bold text-gray-800">{{ formatPrice(item.price * item.quantity) }}</span>
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
import { ORDER_STATUS_LABELS, LOGISTICS_STATUS_LABELS } from '../../utils/constants'
import { formatPrice } from '../../utils/currency'

export default {
  name: 'OrderHistory',
  props: {
    orders: {
      type: Array,
      default: () => []
    },
    activeTab: {
      type: String,
      default: 'all'
    }
  },
  emits: ['order-updated', 'filter-change'],
  data () {
    return {
      showOrderDetails: false,
      selectedOrder: null,
      currentTab: this.activeTab
    }
  },
  computed: {
    filterTitle() {
      return ORDER_STATUS_LABELS[this.currentTab] || this.currentTab
    }
  },
  watch: {
    activeTab (newVal) {
      if (newVal !== this.currentTab) {
        this.currentTab = newVal
      }
    },
    currentTab (newVal) {
      // Avoid infinite loop if prop update triggered it
      if (newVal !== this.activeTab) {
        this.$emit('filter-change', newVal)
      }
      // We don't fetch here anymore, the parent listens to 'filter-change' and fetches.
      // But wait, the parent fetch updates 'orders' prop.
    }
  },
  mounted () {
    // Initial fetch handled by parent usually, or we trigger it?
    // Parent created hook calls fetchOrders('all').
  },
  methods: {
    formatPrice,
    // fetchOrders removed, we rely on parent via filter-change event.
    // Ensure helper methods are kept.
    formatDate (dateString) {
      return new Date(dateString).toLocaleDateString()
    },
    getStatusText (status) {
      return ORDER_STATUS_LABELS[status] || status
    },
    getLogisticsStatus (status) {
      return LOGISTICS_STATUS_LABELS[status] || status
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
        if (error.response?.data?.error_code === 'INSUFFICIENT_BALANCE') {
             this.$toast.error('餘額不足，請前往儲值')
        } else {
             this.$toast.error(error.response?.data?.message || '付款失敗，請稍後再試。')
        }
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

<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">訂單管理</h2>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-100 text-gray-600 text-sm uppercase tracking-wider">
              <th class="p-4 font-bold">訂單編號</th>
              <th class="p-4 font-bold">會員</th>
              <th class="p-4 font-bold">總金額</th>
              <th class="p-4 font-bold">目前狀態</th>
              <th class="p-4 font-bold">下單日期</th>
              <th class="p-4 font-bold">狀態更新</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-if="orders.length === 0">
              <td colspan="6" class="p-8 text-center text-gray-400">暫無訂單資料</td>
            </tr>
            <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50 transition cursor-pointer" @click="$router.push(`/admin/orders/${order.id}`)">
              <td class="p-4 text-gray-500 font-bold">#{{ order.id }}</td>
              <td class="p-4 font-bold text-gray-800">{{ order.user ? order.user.name : 'Unknown' }}</td>
              <td class="p-4 text-xieOrange font-bold">${{ order.total_amount }}</td>
              <td class="p-4">
                <span class="px-2 py-1 rounded text-xs font-bold" :class="getStatusClass(order.status)">
                  {{ order.status }}
                </span>
              </td>
              <td class="p-4 text-gray-600 text-sm">{{ new Date(order.created_at).toLocaleDateString() }}</td>
              <td class="p-4" @click.stop>
                <select class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:border-xieOrange bg-white mr-2"
                        :value="order.status"
                        @change="updateOrderStatus(order.id, $event.target.value)">
                  <option value="pending_payment">Pending Payment</option>
                  <option value="processing">Processing</option>
                  <option value="shipped">Shipped</option>
                  <option value="completed">Completed</option>
                  <option value="cancelled">Cancelled</option>
                </select>
                <button class="text-gray-400 hover:text-xieOrange" @click.stop="$router.push(`/admin/orders/${order.id}`)">
                    <i class="fas fa-edit"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      </div>
    </div>
    <!-- Pagination -->
    <div class="mt-4 flex justify-between items-center" v-if="totalPages > 1">
      <button
        class="px-4 py-2 border rounded bg-white text-gray-600 hover:bg-gray-100 disabled:opacity-50"
        :disabled="currentPage <= 1"
        @click="fetchOrders(currentPage - 1)"
      >
        上一頁
      </button>
      <span class="text-gray-600">第 {{ currentPage }} 頁 / 共 {{ totalPages }} 頁</span>
      <button
        class="px-4 py-2 border rounded bg-white text-gray-600 hover:bg-gray-100 disabled:opacity-50"
        :disabled="currentPage >= totalPages"
        @click="fetchOrders(currentPage + 1)"
      >
        下一頁
      </button>
    </div>
</template>

<script>
import api from '../services/api'

export default {
  name: 'AdminOrders',
  data () {
    return {
      orders: [],
      currentPage: 1,
      totalPages: 1
    }
  },
  created () {
    this.fetchOrders()
  },
  methods: {
    async fetchOrders (page = 1) {
      try {
        const res = await api.get(`/admin/orders?page=${page}`)
        // Laravel paginate(): { data: [...], current_page: 1, last_page: 5, ... }
        this.orders = res.data.data || []
        this.currentPage = res.data.current_page
        this.totalPages = res.data.last_page
      } catch (e) {
        console.error(e)
      }
    },
    async updateOrderStatus (id, status) {
      try {
        await api.put(`/admin/orders/${id}/status`, { status })
        this.$toast.success('狀態更新成功')
        this.fetchOrders(this.currentPage) // Refresh current list
      } catch (e) {
        this.$toast.error('更新失敗')
      }
    },
    getStatusClass (status) {
      const map = {
        pending_payment: 'bg-yellow-100 text-yellow-600',
        processing: 'bg-blue-100 text-blue-600',
        shipped: 'bg-purple-100 text-purple-600',
        completed: 'bg-green-100 text-green-600',
        cancelled: 'bg-red-100 text-red-600'
      }
      return map[status] || 'bg-gray-100 text-gray-600'
    }
  }
}
</script>

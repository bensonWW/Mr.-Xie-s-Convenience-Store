<template>
  <div class="p-6" v-if="order">
    <div class="flex items-center gap-4 mb-6">
        <a href="#" class="text-gray-500 hover:text-xieOrange" @click.prevent="$router.back()"><i class="fas fa-arrow-left"></i> 返回列表</a>
        <h2 class="text-2xl font-bold text-gray-800">訂單 #{{ order.id }} <span class="text-base font-normal text-gray-500 ml-2">{{ new Date(order.created_at).toLocaleString() }} 下單</span></h2>
        <span class="px-3 py-1 rounded text-sm font-bold" :class="getStatusClass(order.status)">{{ order.status }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 font-bold">訂購商品 ({{ order.items ? order.items.length : 0 }})</div>
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500">
                        <tr>
                            <th class="px-6 py-3">商品資訊</th>
                            <th class="px-6 py-3 text-center">單價</th>
                            <th class="px-6 py-3 text-center">數量</th>
                            <th class="px-6 py-3 text-right">小計</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="item in order.items" :key="item.id">
                            <td class="px-6 py-4 flex items-center gap-3">
                                <div class="w-12 h-12 bg-gray-100 rounded border overflow-hidden">
                                    <img v-if="item.product && item.product.image" :src="item.product.image" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800">{{ item.product ? item.product.name : 'Unknown Product' }}</div>
                                    <!-- <div class="text-xs text-gray-500">規格: 全配版</div> -->
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">${{ item.price }}</td>
                            <td class="px-6 py-4 text-center">{{ item.quantity }}</td>
                            <td class="px-6 py-4 text-right font-bold">${{ item.price * item.quantity }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="p-6 border-t border-gray-100 bg-gray-50">
                    <div class="flex justify-between mb-2 text-sm">
                        <span class="text-gray-500">商品小計</span>
                        <span>${{ order.total_amount }}</span>
                    </div>
                    <!-- Placeholder for shipping and discount as they are not in current API response structure yet -->
                    <!-- <div class="flex justify-between mb-2 text-sm">
                        <span class="text-gray-500">運費</span>
                        <span>$100</span>
                    </div>
                    <div class="flex justify-between mb-2 text-sm text-xieOrange">
                        <span class="text-gray-500">優惠折抵</span>
                        <span>-$0</span>
                    </div> -->
                    <div class="flex justify-between mt-4 pt-4 border-t border-gray-200">
                        <span class="font-bold text-lg">訂單總金額</span>
                        <span class="font-bold text-2xl text-xieOrange">${{ order.total_amount }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-bold mb-4">物流與付款</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="block text-gray-500 mb-1">付款方式</span>
                        <span class="font-bold">信用卡 (尚未付款)</span>
                    </div>
                    <div>
                        <span class="block text-gray-500 mb-1">配送方式</span>
                        <span class="font-bold">宅配到府</span>
                    </div>
                    <div>
                        <span class="block text-gray-500 mb-1">物流單號</span>
                        <input type="text" placeholder="輸入物流單號" class="border rounded px-2 py-1 w-full text-sm mt-1 focus:border-xieOrange focus:outline-none">
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-bold mb-4">更新訂單狀態</h3>
                <div class="mb-4">
                    <label class="block text-sm text-gray-500 mb-1">訂單狀態</label>
                    <select v-model="order.status" class="w-full border border-gray-300 rounded px-3 py-2 focus:border-xieOrange focus:outline-none">
                        <option value="pending_payment">待付款</option>
                        <option value="processing">處理中</option>
                        <option value="shipped">已出貨</option>
                        <option value="completed">已完成</option>
                        <option value="cancelled">已取消</option>
                    </select>
                </div>
                <!-- Payment status is not separate in current schema, so omitting for now or mapping to order status -->
                <button class="w-full bg-xieOrange text-white font-bold py-2 rounded hover:bg-orange-600 transition" @click="updateStatus">更新狀態</button>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold">顧客資料</h3>
                    <a href="#" class="text-xs text-blue-500 hover:underline">查看會員</a>
                </div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-500">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <div class="font-bold text-sm">{{ order.user ? order.user.name : 'Unknown' }}</div>
                        <div class="text-xs text-gray-500">{{ order.user ? order.user.email : '' }}</div>
                    </div>
                </div>
                <div class="text-sm border-t pt-4">
                    <div class="mb-2">
                        <span class="text-gray-500">聯絡電話:</span> {{ order.user ? order.user.phone : 'N/A' }}
                    </div>
                    <div class="mb-2">
                        <span class="text-gray-500">配送地址:</span><br>
                        {{ order.address || 'N/A' }}
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-bold mb-2">管理備註 (僅內部可見)</h3>
                <textarea class="w-full border border-gray-300 rounded p-2 text-sm h-24 focus:border-xieOrange focus:outline-none" placeholder="例如：客戶來電要求更換顏色..."></textarea>
                <button class="mt-2 text-sm text-gray-600 border border-gray-300 px-3 py-1 rounded hover:bg-gray-50">儲存備註</button>
            </div>

        </div>
    </div>
  </div>
  <div v-else class="p-6 text-center text-gray-500">
      載入中...
  </div>
</template>

<script>
import api from '../services/api'
import { useRoute } from 'vue-router'

export default {
  name: 'AdminOrderDetail',
  data () {
    return {
      order: null
    }
  },
  mounted () {
    const route = useRoute()
    const id = route.params.id
    this.fetchOrder(id)
  },
  methods: {
    async fetchOrder (id) {
      try {
        // Assuming we have an endpoint to get single order details.
        // If not, we might need to fetch all orders and find one, or update backend.
        // For now, let's try to fetch from the list endpoint and filter, or assume a new endpoint exists.
        // Since backend is not my task, I'll assume standard RESTful: GET /admin/orders/:id
        // If that fails, I'll fallback to filtering from list.
        try {
          const res = await api.get(`/admin/orders/${id}`)
          this.order = res.data
        } catch (e) {
          // Fallback: fetch all and find
          const res = await api.get('/admin/orders')
          this.order = res.data.find(o => o.id === parseInt(id))
        }
      } catch (e) {
        console.error('Fetch order error:', e)
        alert('無法載入訂單資料')
      }
    },
    async updateStatus () {
      try {
        await api.put(`/admin/orders/${this.order.id}/status`, { status: this.order.status })
        alert('狀態更新成功')
      } catch (e) {
        console.error(e)
        alert('更新失敗')
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

<style scoped>
/* Tailwind CSS is used */
</style>

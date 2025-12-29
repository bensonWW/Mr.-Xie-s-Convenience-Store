<template>
  <div class="p-6" v-if="order">
    <div class="flex items-center gap-4 mb-6">
        <a href="#" class="text-gray-500 dark:text-stone-400 hover:text-xieOrange" @click.prevent="$router.back()"><i class="fas fa-arrow-left"></i> 返回列表</a>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-stone-100">訂單 #{{ order.id }} <span class="text-base font-normal text-gray-500 dark:text-stone-400 ml-2">{{ new Date(order.created_at).toLocaleString() }} 下單</span></h2>
        <span class="px-3 py-1 rounded text-sm font-bold" :class="getStatusClass(order.status)">{{ order.status }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm overflow-hidden transition-colors duration-300">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-700 font-bold text-gray-800 dark:text-stone-100">訂購商品 ({{ order.items ? order.items.length : 0 }})</div>
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-slate-900 text-gray-500 dark:text-stone-400">
                        <tr>
                            <th class="px-6 py-3">商品資訊</th>
                            <th class="px-6 py-3 text-center">單價</th>
                            <th class="px-6 py-3 text-center">數量</th>
                            <th class="px-6 py-3 text-right">小計</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        <tr v-for="item in order.items" :key="item.id">
                            <td class="px-6 py-4 flex items-center gap-3">
                                <div class="w-12 h-12 bg-gray-100 dark:bg-slate-700 rounded border dark:border-slate-600 overflow-hidden">
                                    <img v-if="item.product && item.product.image" :src="item.product.image" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800 dark:text-stone-100">{{ item.product ? item.product.name : 'Unknown Product' }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-600 dark:text-stone-300">{{ formatPrice(item.price) }}</td>
                            <td class="px-6 py-4 text-center text-gray-600 dark:text-stone-300">{{ item.quantity }}</td>
                            <td class="px-6 py-4 text-right font-bold text-gray-800 dark:text-stone-100">{{ formatPrice(item.price * item.quantity) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="p-6 border-t border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-900">
                    <div class="flex justify-between mb-2 text-sm">
                        <span class="text-gray-500 dark:text-stone-400">商品小計</span>
                        <span class="text-gray-800 dark:text-stone-100">{{ formatPrice(order.total_amount) }}</span>
                    </div>
                    <div class="flex justify-between mt-4 pt-4 border-t border-gray-200 dark:border-slate-700">
                        <span class="font-bold text-lg text-gray-800 dark:text-stone-100">訂單總金額</span>
                        <span class="font-bold text-2xl text-xieOrange">{{ formatPrice(order.total_amount) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6 transition-colors duration-300">
                <h3 class="font-bold mb-4 text-gray-800 dark:text-stone-100">物流與付款</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="block text-gray-500 dark:text-stone-400 mb-1">付款方式</span>
                        <span class="font-bold text-gray-800 dark:text-stone-100">信用卡 (尚未付款)</span>
                    </div>
                    <div>
                        <span class="block text-gray-500 dark:text-stone-400 mb-1">配送方式</span>
                        <span class="font-bold text-gray-800 dark:text-stone-100">宅配到府</span>
                    </div>
                    <div>
                        <span class="block text-gray-500 dark:text-stone-400 mb-1">物流單號</span>
                        <div class="flex gap-2 mt-1">
                            <input v-model="order.logistics_number" type="text" placeholder="輸入物流單號" class="border dark:border-slate-600 rounded px-2 py-1 flex-1 text-sm focus:border-xieOrange focus:outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-stone-100">
                            <button @click="updateLogistics" class="bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-stone-300 px-3 py-1 rounded text-xs hover:bg-gray-200 dark:hover:bg-slate-600 border border-gray-200 dark:border-slate-600">儲存</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">

            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6 transition-colors duration-300">
                <h3 class="font-bold mb-4 text-gray-800 dark:text-stone-100">更新訂單狀態</h3>
                <div class="mb-4">
                    <label class="block text-sm text-gray-500 dark:text-stone-400 mb-1">訂單狀態</label>
                    <select v-model="order.status" class="w-full border border-gray-300 dark:border-slate-600 rounded px-3 py-2 focus:border-xieOrange focus:outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-stone-100">
                        <option value="pending_payment">待付款</option>
                        <option value="processing">處理中</option>
                        <option value="shipped">已出貨</option>
                        <option value="completed">已完成</option>
                        <option value="cancelled">已取消</option>
                    </select>
                </div>
                <button class="w-full bg-xieOrange text-white font-bold py-2 rounded hover:bg-orange-600 transition" @click="updateStatus">更新狀態</button>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6 transition-colors duration-300">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-gray-800 dark:text-stone-100">顧客資料</h3>
                    <a href="#" class="text-xs text-blue-500 dark:text-blue-400 hover:underline">查看會員</a>
                </div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-gray-200 dark:bg-slate-600 rounded-full flex items-center justify-center text-gray-500 dark:text-stone-300">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <div class="font-bold text-sm flex items-center gap-2 text-gray-800 dark:text-stone-100">
                            {{ customerName }}
                            <span v-if="isSnapshot" class="text-[10px] bg-gray-200 dark:bg-slate-700 px-1 rounded text-gray-600 dark:text-stone-400" title="資料來自訂單快照">快照</span>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-stone-400">{{ customerEmail }}</div>
                    </div>
                </div>
                <div class="text-sm border-t dark:border-slate-700 pt-4 text-gray-800 dark:text-stone-100">
                    <div class="mb-2">
                        <span class="text-gray-500 dark:text-stone-400">聯絡電話:</span> {{ customerPhone }}
                    </div>
                    <div class="mb-2">
                        <span class="text-gray-500 dark:text-stone-400">配送地址:</span><br>
                        {{ shippingAddress }}
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm p-6 transition-colors duration-300">
                <h3 class="font-bold mb-2 text-gray-800 dark:text-stone-100">管理備註 (僅內部可見)</h3>
                <textarea class="w-full border border-gray-300 dark:border-slate-600 rounded p-2 text-sm h-24 focus:border-xieOrange focus:outline-none bg-white dark:bg-slate-700 text-gray-800 dark:text-stone-100" placeholder="例如：客戶來電要求更換顏色..."></textarea>
                <button class="mt-2 text-sm text-gray-600 dark:text-stone-300 border border-gray-300 dark:border-slate-600 px-3 py-1 rounded hover:bg-gray-50 dark:hover:bg-slate-700">儲存備註</button>
            </div>

        </div>
    </div>
  </div>
  <div v-else class="p-6 text-center text-gray-500 dark:text-stone-400">
      載入中...
  </div>
</template>

<script>
import api from '../services/api'
import { useRoute } from 'vue-router'
import { formatPrice } from '../utils/currency'

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
    formatPrice,
    async fetchOrder (id) {
      try {
        const res = await api.get(`/admin/orders/${id}`)
        this.order = res.data
      } catch (e) {
        console.error('Fetch order error:', e)
        this.$toast.error('無法載入訂單資料')
      }
    },
    async updateStatus () {
      if (!confirm('確定要更新訂單狀態嗎？')) return
      try {
        await api.put(`/admin/orders/${this.order.id}/status`, { status: this.order.status })
        this.$toast.success('狀態更新成功')
      } catch (e) {
        console.error(e)
        this.$toast.error('更新失敗')
      }
    },

    async updateLogistics () {
      try {
        await api.put(`/admin/orders/${this.order.id}/logistics`, { logistics_number: this.order.logistics_number })
        this.$toast.success('物流單號已更新')
      } catch (e) {
        console.error(e)
        this.$toast.error('更新失敗')
      }
    },
    getStatusClass (status) {
      const map = {
        pending_payment: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400',
        processing: 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
        shipped: 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
        completed: 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
        cancelled: 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400'
      }
      return map[status] || 'bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-stone-400'
    }
  },
  computed: {
    customerName () {
      return this.order?.snapshot_data?.customer_name || this.order?.user?.name || 'Unknown'
    },
    customerEmail () {
      return this.order?.snapshot_data?.email || this.order?.user?.email || ''
    },
    customerPhone () {
      return this.order?.snapshot_data?.phone || this.order?.user?.phone || 'N/A'
    },
    shippingAddress () {
      // Handle snapshot string
      if (this.order?.snapshot_data?.shipping_address) {
        return this.order.snapshot_data.shipping_address
      }
      // Handle structured address object
      const addr = this.order?.address
      if (addr && typeof addr === 'object') {
        return `${addr.zip_code || ''} ${addr.city || ''}${addr.district || ''}${addr.detail_address || addr.address || ''}`.trim() || 'N/A'
      }
      // Handle address string
      if (typeof addr === 'string') {
        return addr || 'N/A'
      }
      // Fallback to shipping_address string
      return this.order?.shipping_address || 'N/A'
    },
    isSnapshot () {
      return !!this.order?.snapshot_data
    }
  }
}
</script>

<style scoped>
/* Tailwind CSS is used */
</style>

<template>
  <div class="p-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg p-5 shadow-sm border-l-4 border-xieOrange flex items-center justify-between">
            <div>
                <div class="text-gray-400 text-xs font-bold uppercase mb-1">總銷售額</div>
                <div class="text-2xl font-bold text-gray-800">${{ stats.total_sales ? stats.total_sales.toLocaleString() : 0 }}</div>
                <div class="text-green-500 text-xs font-bold mt-1"><i class="fas fa-arrow-up"></i> 12% 較上月</div>
            </div>
            <div class="w-10 h-10 rounded-full bg-orange-100 text-xieOrange flex items-center justify-center text-xl">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg p-5 shadow-sm border-l-4 border-blue-500 flex items-center justify-between">
            <div>
                <div class="text-gray-400 text-xs font-bold uppercase mb-1">總訂單數</div>
                <div class="text-2xl font-bold text-gray-800">{{ stats.order_count || 0 }}</div>
                <div class="text-gray-400 text-xs font-bold mt-1">待出貨: {{ pendingOrdersCount }}</div>
            </div>
            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-500 flex items-center justify-center text-xl">
                <i class="fas fa-shopping-bag"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg p-5 shadow-sm border-l-4 border-purple-500 flex items-center justify-between">
            <div>
                <div class="text-gray-400 text-xs font-bold uppercase mb-1">總會員數</div>
                <div class="text-2xl font-bold text-gray-800">{{ stats.user_count || 0 }}</div>
                <div class="text-green-500 text-xs font-bold mt-1"><i class="fas fa-plus"></i> 新增 3 人</div>
            </div>
            <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-500 flex items-center justify-center text-xl">
                <i class="fas fa-users"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg p-5 shadow-sm border-l-4 border-red-500 flex items-center justify-between">
            <div>
                <div class="text-gray-400 text-xs font-bold uppercase mb-1">低庫存商品</div>
                <div class="text-2xl font-bold text-red-500">{{ stats.low_stock_products ? stats.low_stock_products.length : 0 }}</div>
                <div class="text-red-400 text-xs font-bold mt-1 cursor-pointer hover:underline" @click="$emit('switch-tab', 'products')">立即補貨</div>
            </div>
            <div class="w-10 h-10 rounded-full bg-red-100 text-red-500 flex items-center justify-center text-xl">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Revenue Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6 lg:col-span-2">
            <h3 class="font-bold text-gray-800 mb-4">近 7 天營收趨勢</h3>
            <div class="h-64">
                <canvas ref="revenueChart"></canvas>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow-sm p-0 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">最新訂單</h3>
                <router-link to="/admin/orders" class="text-xs text-xieOrange hover:underline">查看全部</router-link>
            </div>
            <ul class="divide-y divide-gray-100">
                <li v-for="order in stats.recent_orders" :key="order.id" class="px-6 py-3 hover:bg-gray-50 flex justify-between items-center cursor-pointer" @click="$router.push(`/admin/orders/${order.id}`)">
                    <div>
                        <div class="font-bold text-sm text-xieBlue">#{{ order.id }} - {{ order.user ? order.user.name : 'Unknown' }}</div>
                        <div class="text-xs text-gray-500">{{ new Date(order.created_at).toLocaleDateString() }}</div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-sm">${{ order.total_amount }}</div>
                        <span class="text-xs px-2 py-0.5 rounded" :class="getStatusClass(order.status)">{{ order.status }}</span>
                    </div>
                </li>
                <li v-if="!stats.recent_orders || stats.recent_orders.length === 0" class="px-6 py-3 text-center text-gray-400 text-sm">
                    暫無訂單
                </li>
            </ul>
        </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api'
import Chart from 'chart.js/auto'
import { markRaw } from 'vue'

export default {
  name: 'AdminDashboard',
  data () {
    return {
      stats: {}
    }
  },
  computed: {
    pendingOrdersCount () {
      return 2 // Placeholder
    }
  },
  created () {
    this.chart = null
    this.fetchStats()
  },
  mounted () {
    this.isMounted = true
  },
  beforeUnmount () {
    this.isMounted = false
    if (this.chart) {
      try {
        this.chart.stop()
        this.chart.destroy()
      } catch (e) {
        console.warn('Error destroying chart in beforeUnmount:', e)
      }
      this.chart = null
    }
  },
  methods: {
    async fetchStats () {
      try {
        const res = await api.get('/admin/stats')
        if (!this.isMounted) return
        this.stats = res.data
        this.$nextTick(() => {
          this.initChart()
        })
      } catch (e) {
        console.error(e)
      }
    },
    initChart () {
      if (!this.isMounted) return
      const ctx = this.$refs.revenueChart
      if (!ctx) return

      const existingChart = Chart.getChart(ctx)
      if (existingChart) {
        try {
          existingChart.destroy()
        } catch (e) {
          console.warn('Error destroying existing chart:', e)
        }
      }

      if (this.chart && this.chart !== existingChart) {
        try {
          this.chart.destroy()
        } catch (e) {
          console.warn('Error destroying chart:', e)
        }
      }

      try {
        this.chart = markRaw(new Chart(ctx, {
          type: 'line',
          data: {
            labels: this.stats.chart_data ? this.stats.chart_data.labels : [],
            datasets: [{
              label: '營收',
              data: this.stats.chart_data ? this.stats.chart_data.values : [],
              borderColor: '#ed8936',
              backgroundColor: 'rgba(237, 137, 54, 0.1)',
              fill: true,
              tension: 0.4
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            resizeDelay: 200,
            plugins: { legend: { display: false } }
          }
        }))
      } catch (e) {
        console.error('Error creating chart:', e)
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

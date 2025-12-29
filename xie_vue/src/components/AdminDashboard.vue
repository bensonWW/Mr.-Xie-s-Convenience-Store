<template>
  <div class="space-y-8">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Sales Card -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border-l-4 border-xieOrange flex items-center justify-between transition-colors duration-300 relative overflow-hidden">
            <!-- Grid dots background -->
            <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.02]" style="background-image: radial-gradient(circle, currentColor 1px, transparent 1px); background-size: 16px 16px;"></div>
            <div class="relative">
                <div class="text-stone-400 dark:text-stone-500 text-xs font-bold uppercase tracking-wider mb-1">總銷售額</div>
                <div class="text-2xl font-bold font-mono text-gray-800 dark:text-stone-100">${{ stats.total_sales ? stats.total_sales.toLocaleString() : 0 }}</div>
                <div class="text-emerald-500 dark:text-emerald-400 text-xs font-medium mt-1"><i class="fas fa-arrow-up"></i> 12% 較上月</div>
            </div>
            <div class="w-12 h-9 rounded-lg bg-orange-100 dark:bg-xieOrange/10 text-xieOrange flex items-center justify-center text-xl relative">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border-l-4 border-sky-500 flex items-center justify-between transition-colors duration-300 relative overflow-hidden">
            <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.02]" style="background-image: radial-gradient(circle, currentColor 1px, transparent 1px); background-size: 16px 16px;"></div>
            <div class="relative">
                <div class="text-stone-400 dark:text-stone-500 text-xs font-bold uppercase tracking-wider mb-1">總訂單數</div>
                <div class="text-2xl font-bold font-mono text-gray-800 dark:text-stone-100">{{ stats.order_count ? stats.order_count.toLocaleString() : 0 }}</div>
                <div class="text-stone-400 dark:text-stone-500 text-xs font-medium mt-1">待出貨: {{ pendingOrdersCount }}</div>
            </div>
            <div class="w-12 h-9 rounded-lg bg-sky-100 dark:bg-sky-500/10 text-sky-500 dark:text-sky-400 flex items-center justify-center text-lg relative">
                <i class="fas fa-shopping-bag"></i>
            </div>
        </div>

        <!-- Users Card -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border-l-4 border-violet-500 flex items-center justify-between transition-colors duration-300 relative overflow-hidden">
            <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.02]" style="background-image: radial-gradient(circle, currentColor 1px, transparent 1px); background-size: 16px 16px;"></div>
            <div class="relative">
                <div class="text-stone-400 dark:text-stone-500 text-xs font-bold uppercase tracking-wider mb-1">總會員數</div>
                <div class="text-2xl font-bold font-mono text-gray-800 dark:text-stone-100">{{ stats.user_count ? stats.user_count.toLocaleString() : 0 }}</div>
                <div class="text-emerald-500 dark:text-emerald-400 text-xs font-medium mt-1"><i class="fas fa-plus"></i> 新增 3 人</div>
            </div>
            <div class="w-12 h-9 rounded-lg bg-violet-100 dark:bg-violet-500/10 text-violet-500 dark:text-violet-400 flex items-center justify-center text-lg relative">
                <i class="fas fa-users"></i>
            </div>
        </div>

        <!-- Low Stock Card -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-5 shadow-sm border-l-4 border-rose-500 flex items-center justify-between transition-colors duration-300 relative overflow-hidden">
            <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.02]" style="background-image: radial-gradient(circle, currentColor 1px, transparent 1px); background-size: 16px 16px;"></div>
            <div class="relative">
                <div class="text-stone-400 dark:text-stone-500 text-xs font-bold uppercase tracking-wider mb-1">低庫存商品</div>
                <div class="text-2xl font-bold font-mono text-red-500 dark:text-red-400">{{ stats.low_stock_products ? stats.low_stock_products.length.toLocaleString() : 0 }}</div>
                <div class="text-rose-400 dark:text-rose-300 text-xs font-medium mt-1 cursor-pointer hover:underline" @click="$emit('switch-tab', 'products')">立即補貨</div>
            </div>
            <div class="w-12 h-9 rounded-lg bg-rose-100 dark:bg-rose-500/10 text-rose-500 dark:text-rose-400 flex items-center justify-center text-lg relative">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Revenue Chart -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm p-6 lg:col-span-2 transition-colors duration-300 relative overflow-hidden">
            <!-- Subtle grid dots -->
            <div class="absolute inset-0 opacity-[0.02] dark:opacity-[0.015]" style="background-image: radial-gradient(circle, currentColor 1px, transparent 1px); background-size: 24px 24px;"></div>
            <h3 class="font-bold text-slate-800 dark:text-stone-100 mb-4 relative">近 7 天營收趨勢</h3>
            <div class="h-64 relative">
                <canvas ref="revenueChart"></canvas>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm p-0 overflow-hidden transition-colors duration-300">
            <div class="px-6 py-4 border-b border-stone-100 dark:border-slate-700 flex justify-between items-center">
                <h3 class="font-semibold text-slate-700 dark:text-stone-100">最近訂單</h3>
                <router-link to="/admin/orders" class="text-xs text-xieOrange hover:underline font-medium">查看全部</router-link>
            </div>
            <ul class="divide-y divide-stone-100 dark:divide-slate-700">
                <li v-for="order in stats.recent_orders" :key="order.id" class="px-6 py-3 hover:bg-stone-50 dark:hover:bg-slate-700/50 flex justify-between items-center cursor-pointer transition-colors" @click="$router.push(`/admin/orders/${order.id}`)">
                    <div>
                        <div class="font-bold text-sm text-sky-600 dark:text-sky-400">
                            <span class="text-stone-400">#</span><span class="font-mono">{{ order.id }}</span> - {{ order.user ? order.user.name : 'Unknown' }}
                        </div>
                        <div class="text-xs text-stone-500 dark:text-stone-400">{{ new Date(order.created_at).toLocaleDateString() }}</div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold font-mono text-sm text-slate-800 dark:text-stone-100">${{ formatNumber(order.total_amount) }}</div>
                        <span class="text-xs px-2 py-0.5 rounded-full border-[0.5px]" :class="getStatusClass(order.status)">{{ getStatusText(order.status) }}</span>
                    </div>
                </li>
                <li v-if="!stats.recent_orders || stats.recent_orders.length === 0" class="px-6 py-8 text-center text-stone-400 dark:text-stone-500 text-sm">
                    <i class="fas fa-inbox text-2xl mb-2 opacity-50"></i>
                    <p>暫無訂單</p>
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
    formatNumber (value) {
      if (!value && value !== 0) return '0'
      return Number(value).toLocaleString()
    },
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

      // Create gradient fill
      const ctx2d = ctx.getContext('2d')
      const gradient = ctx2d.createLinearGradient(0, 0, 0, 256)
      gradient.addColorStop(0, 'rgba(237, 137, 54, 0.15)')
      gradient.addColorStop(1, 'rgba(237, 137, 54, 0.01)')

      try {
        this.chart = markRaw(new Chart(ctx, {
          type: 'line',
          data: {
            labels: this.stats.chart_data ? this.stats.chart_data.labels : [],
            datasets: [{
              label: '營收',
              data: this.stats.chart_data ? this.stats.chart_data.values : [],
              borderColor: '#ed8936',
              borderWidth: 2,
              backgroundColor: gradient,
              fill: true,
              tension: 0.4,
              pointRadius: 4,
              pointBackgroundColor: '#ed8936',
              pointBorderColor: '#ffffff',
              pointBorderWidth: 2,
              pointHoverRadius: 6
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 800, easing: 'easeOutQuart' },
            resizeDelay: 200,
            plugins: { 
              legend: { display: false },
              tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: { size: 13 },
                bodyFont: { size: 12 },
                cornerRadius: 8
              }
            },
            scales: {
              x: {
                grid: { display: false },
                ticks: { color: '#9ca3af' }
              },
              y: {
                grid: { color: 'rgba(156, 163, 175, 0.1)' },
                ticks: { 
                  color: '#9ca3af',
                  callback: (value) => '$' + value.toLocaleString()
                }
              }
            }
          }
        }))
      } catch (e) {
        console.error('Error creating chart:', e)
      }
    },
    getStatusClass (status) {
      const map = {
        pending_payment: 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 border-amber-200/50 dark:border-amber-700/50',
        processing: 'bg-slate-100 dark:bg-slate-600 text-slate-600 dark:text-slate-300 border-slate-200/50 dark:border-slate-500/50',
        shipped: 'bg-violet-50 dark:bg-violet-900/20 text-violet-600 dark:text-violet-400 border-violet-200/50 dark:border-violet-700/50',
        completed: 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 border-emerald-200/50 dark:border-emerald-700/50',
        cancelled: 'bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 border-rose-200/50 dark:border-rose-700/50'
      }
      return map[status] || 'bg-stone-100 dark:bg-slate-700 text-stone-600 dark:text-stone-400 border-stone-200/50'
    },
    getStatusText (status) {
      const map = {
        pending_payment: '待付款',
        processing: '處理中',
        shipped: '已出貨',
        completed: '已完成',
        cancelled: '已取消'
      }
      return map[status] || status
    }
  }
}
</script>

<style scoped>
/* Tailwind CSS is used */
</style>

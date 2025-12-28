<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-stone-100">銷售分析報表</h2>
        <div class="flex bg-white dark:bg-slate-800 rounded shadow-sm overflow-hidden border border-gray-200 dark:border-slate-700">
            <button class="px-4 py-2 text-sm bg-xieOrange text-white font-bold">近 7 天</button>
            <button class="px-4 py-2 text-sm text-gray-600 dark:text-stone-300 hover:bg-gray-50 dark:hover:bg-slate-700">本月</button>
            <button class="px-4 py-2 text-sm text-gray-600 dark:text-stone-300 hover:bg-gray-50 dark:hover:bg-slate-700">本季</button>
            <input type="date" class="border-l border-gray-200 dark:border-slate-700 px-2 text-sm text-gray-500 dark:text-stone-400 focus:outline-none bg-transparent">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-lg shadow-sm text-center transition-colors duration-300">
            <div class="text-gray-500 dark:text-stone-400 text-sm mb-1">總銷售額</div>
            <div class="text-3xl font-bold text-xieBlue dark:text-sky-400">${{ stats.total_sales }}</div>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-lg shadow-sm text-center transition-colors duration-300">
            <div class="text-gray-500 dark:text-stone-400 text-sm mb-1">總訂單數</div>
            <div class="text-3xl font-bold text-xieBlue dark:text-sky-400">{{ stats.order_count }}</div>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-lg shadow-sm text-center transition-colors duration-300">
            <div class="text-gray-500 dark:text-stone-400 text-sm mb-1">平均客單價 (AOV)</div>
            <div class="text-3xl font-bold text-xieBlue dark:text-sky-400">${{ stats.aov }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-lg shadow-sm transition-colors duration-300">
            <h3 class="font-bold text-gray-800 dark:text-stone-100 mb-4">銷售類別佔比</h3>
            <div class="h-64 flex justify-center">
                <canvas ref="categoryChart"></canvas>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-6 rounded-lg shadow-sm transition-colors duration-300">
            <h3 class="font-bold text-gray-800 dark:text-stone-100 mb-4">熱銷商品 TOP 5</h3>
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="text-gray-500 dark:text-stone-400 border-b border-gray-200 dark:border-slate-700">
                        <th class="pb-2">商品名稱</th>
                        <th class="pb-2 text-right">銷量</th>
                        <th class="pb-2 text-right">總金額</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    <tr v-for="prod in stats.top_products" :key="prod.id">
                        <td class="py-3 flex items-center gap-2 text-gray-800 dark:text-stone-100">
                             <div class="w-8 h-8 bg-gray-100 dark:bg-slate-700 rounded overflow-hidden">
                                <img v-if="prod.image" :src="prod.image" class="w-full h-full object-cover">
                            </div>
                            {{ prod.name }}
                        </td>
                        <td class="text-right text-gray-600 dark:text-stone-300">{{ prod.total_qty }}</td>
                        <td class="text-right font-bold text-xieOrange">${{ prod.total_amount }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
  </div>
</template>

<script>
import Chart from 'chart.js/auto'
import { markRaw } from 'vue'
import api from '../services/api'

export default {
  name: 'AdminAnalytics',
  data () {
    return {
      stats: {
        total_sales: 0,
        order_count: 0,
        aov: 0,
        sales_by_category: [],
        top_products: []
      }
    }
  },
  created () {
    this.chart = null
    this.fetchStats()
  },
  methods: {
    async fetchStats () {
      try {
        const res = await api.get('/admin/stats')
        this.stats = res.data
        this.renderChart()
      } catch (e) {
        console.error('Fetch stats error:', e)
      }
    },
    renderChart () {
      if (!this.$refs.categoryChart) return

      const ctx = this.$refs.categoryChart
      const existingChart = Chart.getChart(ctx)
      if (existingChart) existingChart.destroy()

      const labels = this.stats.sales_by_category.map(item => item.category)
      const data = this.stats.sales_by_category.map(item => item.total)
      const colors = ['#ed8936', '#2b6cb0', '#ed64a6', '#48bb78', '#667eea', '#f56565']

      this.chart = markRaw(new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: labels,
          datasets: [{
            data: data,
            backgroundColor: colors.slice(0, labels.length)
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false
        }
      }))
    }
  },
  beforeUnmount () {
    if (this.chart) {
      this.chart.destroy()
    }
  }
}
</script>

<style scoped>
/* Tailwind CSS is used */
</style>

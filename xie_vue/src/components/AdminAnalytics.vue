<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">銷售分析報表</h2>
        <div class="flex bg-white rounded shadow-sm overflow-hidden">
            <button class="px-4 py-2 text-sm bg-xieOrange text-white font-bold">近 7 天</button>
            <button class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">本月</button>
            <button class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">本季</button>
            <input type="date" class="border-l border-gray-200 px-2 text-sm text-gray-500 focus:outline-none">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-sm text-center">
            <div class="text-gray-500 text-sm mb-1">平均客單價 (AOV)</div>
            <div class="text-3xl font-bold text-xieBlue">$42,124</div>
            <div class="text-green-500 text-xs mt-2">+5%</div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm text-center">
            <div class="text-gray-500 text-sm mb-1">訂單轉換率</div>
            <div class="text-3xl font-bold text-xieBlue">2.8%</div>
            <div class="text-red-500 text-xs mt-2">-0.5%</div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm text-center">
            <div class="text-gray-500 text-sm mb-1">總退貨金額</div>
            <div class="text-3xl font-bold text-xieBlue">$0</div>
            <div class="text-gray-400 text-xs mt-2">表現優異</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h3 class="font-bold text-gray-800 mb-4">銷售類別佔比</h3>
            <div class="h-64 flex justify-center">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h3 class="font-bold text-gray-800 mb-4">熱銷商品 TOP 5</h3>
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="text-gray-500 border-b">
                        <th class="pb-2">商品名稱</th>
                        <th class="pb-2 text-right">銷量</th>
                        <th class="pb-2 text-right">金額</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr>
                        <td class="py-3 flex items-center gap-2">
                            <div class="w-8 h-8 bg-gray-100 rounded"></div>
                            iPhone 15 Pro Max
                        </td>
                        <td class="text-right">3</td>
                        <td class="text-right font-bold text-xieOrange">$146,700</td>
                    </tr>
                    <tr>
                        <td class="py-3 flex items-center gap-2">
                            <div class="w-8 h-8 bg-gray-100 rounded"></div>
                            Galaxy S24 Ultra
                        </td>
                        <td class="text-right">1</td>
                        <td class="text-right font-bold text-xieOrange">$42,900</td>
                    </tr>
                    <tr>
                        <td class="py-3 flex items-center gap-2">
                            <div class="w-8 h-8 bg-gray-100 rounded"></div>
                            Dyson V12
                        </td>
                        <td class="text-right">1</td>
                        <td class="text-right font-bold text-xieOrange">$18,900</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
  </div>
</template>

<script>
import Chart from 'chart.js/auto'

export default {
  name: 'AdminAnalytics',
  mounted () {
    const ctx = document.getElementById('categoryChart')
    if (ctx) {
      // eslint-disable-next-line no-new
      new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: ['手機', '家電', '美妝', '食品'],
          datasets: [{
            data: [70, 20, 5, 5],
            backgroundColor: ['#ed8936', '#2b6cb0', '#ed64a6', '#48bb78']
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false
        }
      })
    }
  }
}
</script>

<style scoped>
/* Tailwind CSS is used */
</style>

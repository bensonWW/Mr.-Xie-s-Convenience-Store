<template>
  <div class="container mx-auto px-4 py-6 grid grid-cols-12 gap-6">

    <!-- Sidebar -->
    <aside class="hidden lg:block col-span-2 bg-white rounded-lg shadow-sm overflow-hidden h-fit">
      <div class="bg-xieBlue text-white px-4 py-3 font-bold flex items-center">
        <i class="fas fa-bars mr-2"></i> 商品分類
      </div>
      <ul class="text-sm text-gray-700 divide-y divide-gray-100">
        <li
          v-for="cat in categories"
          :key="cat"
          class="hover:bg-orange-50 hover:text-xieOrange cursor-pointer px-4 py-3 flex justify-between group"
          @click="goToCategory(cat)"
        >
          <span><i class="fas fa-tag w-6 text-center text-gray-400 group-hover:text-xieOrange"></i> {{ cat }}</span>
          <i class="fas fa-chevron-right text-xs mt-1"></i>
        </li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="col-span-12 lg:col-span-10 space-y-6">

      <!-- Hero Banner -->
      <div class="bg-gray-800 rounded-xl overflow-hidden relative h-64 md:h-80 flex items-center">
        <img src="https://images.unsplash.com/photo-1556911220-e15b29be8c8f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&q=80" alt="Kitchen Appliances" class="absolute w-full h-full object-cover opacity-60">
        <div class="relative z-10 px-8 md:px-16 text-white">
          <span class="bg-xieOrange px-3 py-1 rounded text-sm font-bold mb-2 inline-block">本月強檔</span>
          <h1 class="text-3xl md:text-5xl font-bold mb-4">居家生活節 <br> 全館家電 85 折起</h1>
          <p class="mb-6 text-gray-200">打造夢想廚房，從這一刻開始。指定商品再送清潔組。</p>
          <router-link to="/items" class="bg-white text-xieBlue px-6 py-2 rounded font-bold hover:bg-gray-200 transition">立即搶購</router-link>
        </div>
      </div>

      <!-- Features -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-lg shadow-sm flex items-center justify-center gap-3 border-b-4 border-xieOrange">
          <i class="fas fa-truck-fast text-2xl text-xieBlue"></i>
          <div class="leading-tight">
            <div class="font-bold">24h 快速到貨</div>
            <div class="text-xs text-gray-500">雙北地區適用</div>
          </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm flex items-center justify-center gap-3 border-b-4 border-gray-200">
          <i class="fas fa-shield-alt text-2xl text-xieBlue"></i>
          <div class="leading-tight">
            <div class="font-bold">原廠正品</div>
            <div class="text-xs text-gray-500">安心保固承諾</div>
          </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm flex items-center justify-center gap-3 border-b-4 border-gray-200">
          <i class="fas fa-undo text-2xl text-xieBlue"></i>
          <div class="leading-tight">
            <div class="font-bold">10天鑑賞期</div>
            <div class="text-xs text-gray-500">退換貨無負擔</div>
          </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm flex items-center justify-center gap-3 border-b-4 border-gray-200">
          <i class="fas fa-ticket-alt text-2xl text-xieBlue"></i>
          <div class="leading-tight">
            <div class="font-bold">領取折價券</div>
            <div class="text-xs text-gray-500">天天有優惠</div>
          </div>
        </div>
      </div>

      <!-- Flash Sale -->
      <section>
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-2xl font-bold text-xieBlue flex items-center">
            <i class="fas fa-bolt text-xieOrange mr-2"></i> 限時下殺
          </h2>
          <div class="text-sm font-bold text-white bg-red-600 px-3 py-1 rounded">
            剩餘時間 02:14:59
          </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
          <div class="bg-white rounded-lg shadow hover:shadow-lg transition group">
            <div class="relative">
              <img src="https://images.unsplash.com/photo-1585837575652-2c9620943a76?auto=format&fit=crop&w=300&q=80" class="w-full h-40 object-cover rounded-t-lg">
              <span class="absolute top-0 left-0 bg-red-600 text-white text-xs px-2 py-1 font-bold">5折</span>
            </div>
            <div class="p-3">
              <h3 class="text-sm font-bold text-gray-800 line-clamp-2 h-10 mb-2 group-hover:text-xieOrange">倍潔雅 柔軟抽取式衛生紙 (150抽x12包)</h3>
              <div class="text-gray-400 text-xs line-through">NT$ 299</div>
              <div class="text-xieOrange font-bold text-lg">NT$ 159</div>
              <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
                <div class="bg-red-500 h-1.5 rounded-full" style="width: 80%"></div>
              </div>
              <div class="text-xs text-gray-500 mt-1">已搶 80%</div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow hover:shadow-lg transition group">
            <div class="relative">
              <img src="https://images.unsplash.com/photo-1544239334-038d8a245d6f?auto=format&fit=crop&w=300&q=80" class="w-full h-40 object-cover rounded-t-lg">
            </div>
            <div class="p-3">
              <h3 class="text-sm font-bold text-gray-800 line-clamp-2 h-10 mb-2 group-hover:text-xieOrange">日本 IRIS 智能除蟎吸塵器 大拍 3.5</h3>
              <div class="text-gray-400 text-xs line-through">NT$ 3,990</div>
              <div class="text-xieOrange font-bold text-lg">NT$ 2,480</div>
                <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
                <div class="bg-red-500 h-1.5 rounded-full" style="width: 45%"></div>
              </div>
              <div class="text-xs text-gray-500 mt-1">已搶 45%</div>
            </div>
          </div>

            <div class="bg-white rounded-lg shadow hover:shadow-lg transition group">
            <div class="relative">
              <img src="https://plus.unsplash.com/premium_photo-1664372599728-6677cb2d45c5?auto=format&fit=crop&w=300&q=80" class="w-full h-40 object-cover rounded-t-lg">
            </div>
            <div class="p-3">
              <h3 class="text-sm font-bold text-gray-800 line-clamp-2 h-10 mb-2 group-hover:text-xieOrange">Ariel 4D 抗菌洗衣膠囊 (39顆x3袋)</h3>
              <div class="text-gray-400 text-xs line-through">NT$ 899</div>
              <div class="text-xieOrange font-bold text-lg">NT$ 599</div>
                <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
                <div class="bg-red-500 h-1.5 rounded-full" style="width: 90%"></div>
              </div>
              <div class="text-xs text-gray-500 mt-1">即將售完</div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow hover:shadow-lg transition group">
            <div class="relative">
              <img src="https://images.unsplash.com/photo-1574269909862-7e1d70bb8078?auto=format&fit=crop&w=300&q=80" class="w-full h-40 object-cover rounded-t-lg">
            </div>
            <div class="p-3">
              <h3 class="text-sm font-bold text-gray-800 line-clamp-2 h-10 mb-2 group-hover:text-xieOrange">Philips 飛利浦 智慧萬用鍋 (金小萬)</h3>
              <div class="text-gray-400 text-xs line-through">NT$ 12,900</div>
              <div class="text-xieOrange font-bold text-lg">NT$ 8,888</div>
                <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
                <div class="bg-red-500 h-1.5 rounded-full" style="width: 30%"></div>
              </div>
              <div class="text-xs text-gray-500 mt-1">已搶 30%</div>
            </div>
          </div>

          <div class="bg-white rounded-lg shadow hover:shadow-lg transition group">
            <div class="relative">
              <img src="https://images.unsplash.com/photo-1626806775351-638563e48278?auto=format&fit=crop&w=300&q=80" class="w-full h-40 object-cover rounded-t-lg">
            </div>
            <div class="p-3">
              <h3 class="text-sm font-bold text-gray-800 line-clamp-2 h-10 mb-2 group-hover:text-xieOrange">小米 智慧直流變頻電風扇 2 Pro</h3>
              <div class="text-gray-400 text-xs line-through">NT$ 2,995</div>
              <div class="text-xieOrange font-bold text-lg">NT$ 2,395</div>
                <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
                <div class="bg-red-500 h-1.5 rounded-full" style="width: 15%"></div>
              </div>
              <div class="text-xs text-gray-500 mt-1">熱賣中</div>
            </div>
          </div>

        </div>
      </section>
    </main>
  </div>
</template>

<script>
import api from '../services/api'

export default {
  name: 'HomeView',
  data () {
    return {
      categories: []
    }
  },
  created () {
    this.fetchCategories()
  },
  methods: {
    async fetchCategories () {
      try {
        const response = await api.get('/categories')
        this.categories = response.data
      } catch (error) {
        console.error('Error fetching categories:', error)
      }
    },
    goToCategory (cat) {
      this.$router.push({ path: '/items', query: { category: cat } })
    }
  }
}
</script>

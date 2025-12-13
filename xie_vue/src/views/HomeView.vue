<template>
  <div class="container mx-auto px-4 py-6 grid grid-cols-12 gap-6">

    <!-- Sidebar -->
    <CategorySidebar :categories="categories" @select-category="goToCategory" />

    <!-- Main Content -->
    <main class="col-span-12 lg:col-span-10 space-y-6">

      <!-- Hero Banner -->
      <HeroBanner />

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
      <FlashSaleSection />

    </main>
  </div>
</template>

<script>
import api from '../services/api'
import HeroBanner from '@/components/home/HeroBanner.vue'
import FlashSaleSection from '@/components/home/FlashSaleSection.vue'
import CategorySidebar from '@/components/home/CategorySidebar.vue'

export default {
  name: 'HomeView',
  components: {
    HeroBanner,
    FlashSaleSection,
    CategorySidebar
  },
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

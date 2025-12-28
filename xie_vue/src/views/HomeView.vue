<template>
  <div class="animate-fade-in bg-wood-50 dark:bg-slate-900 relative">
    <!-- Subtle noise texture overlay -->
    <div class="fixed inset-0 pointer-events-none opacity-[0.015] dark:opacity-[0.03] z-0" 
      style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%270 0 200 200%27 xmlns=%27http://www.w3.org/2000/svg%27%3E%3Cfilter id=%27noiseFilter%27%3E%3CfeTurbulence type=%27fractalNoise%27 baseFrequency=%270.65%27 numOctaves=%273%27 stitchTiles=%27stitch%27/%3E%3C/filter%3E%3Crect width=%27100%25%27 height=%27100%25%27 filter=%27url(%23noiseFilter)%27/%3E%3C/svg%3E');"></div>
    
    <!-- Hero Section -->
    <section class="relative h-[560px] w-full bg-stone-200 dark:bg-slate-800 overflow-hidden">
      <!-- Background with blur, desaturation -->
      <div 
        class="absolute inset-0 bg-cover bg-center saturate-[0.7] scale-105" 
        style="background-image: url('https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=2574&auto=format&fit=crop');"
      ></div>
      <!-- Gradient overlay -->
      <div class="absolute inset-0 bg-gradient-to-r from-stone-50/90 dark:from-slate-900/95 via-stone-50/60 dark:via-slate-900/70 to-transparent"></div>
      
      <!-- Vertical Japanese Text (Left Side) -->
      <div class="absolute left-4 md:left-8 top-1/2 -translate-y-1/2 hidden md:block">
        <p class="text-stone-400/40 dark:text-stone-500/30 text-xs tracking-[0.5em] font-light" style="writing-mode: vertical-rl;">
          2025 コレクション — QUALITY LIFE CHOICE
        </p>
      </div>
      
      <div class="relative container mx-auto px-4 h-full flex items-center">
        <!-- Glass Card -->
        <div class="max-w-xl bg-white/75 dark:bg-slate-800/80 backdrop-blur-md p-8 md:p-12 rounded-2xl border border-white/50 dark:border-slate-700/50 shadow-xl shadow-stone-900/5 ml-8 md:ml-16">
          <!-- Decorative line -->
          <div class="w-8 h-px bg-xieOrange mb-4"></div>
          <h2 class="text-xs font-medium tracking-[0.3em] text-stone-500 dark:text-stone-400 uppercase mb-5">Quality Life Choice</h2>
          <!-- Title with hierarchy: light vs bold contrast -->
          <h1 class="text-3xl md:text-5xl font-serif font-light text-slate-700 dark:text-stone-100 mb-2 leading-tight">
            日式美學，
          </h1>
          <h1 class="text-3xl md:text-5xl font-serif font-bold text-xieOrange mb-6 leading-tight">
            量販價格。
          </h1>
          <p class="text-stone-600 dark:text-stone-400 mb-8 text-sm leading-relaxed max-w-md">
            我們精選優質商品，以最實惠的價格提供給會員。<br>
            從日常食品到居家生活，為您打造極致的生活體驗。
          </p>
          <div class="flex gap-3">
            <router-link 
              to="/items" 
              class="inline-block bg-xieOrange text-white px-7 py-3 text-sm font-medium tracking-widest hover:bg-[#cf8354] transition rounded-full shadow-lg shadow-xieOrange/25"
            >
              立即選購
            </router-link>
            <router-link 
              to="/items" 
              class="inline-block bg-transparent border border-slate-400 dark:border-stone-500 text-slate-600 dark:text-stone-300 px-7 py-3 text-sm font-medium tracking-widest hover:border-slate-700 dark:hover:border-stone-200 hover:text-slate-800 dark:hover:text-white transition rounded-full"
            >
              了解更多
            </router-link>
          </div>
        </div>
      </div>

      <!-- Vertical Text (Right Side) -->
      <div class="absolute right-4 md:right-8 top-1/2 -translate-y-1/2 hidden md:block">
        <p class="text-stone-400/30 dark:text-stone-500/20 text-[10px] tracking-[0.4em] font-light" style="writing-mode: vertical-rl;">
          謝氏雜貨 — EST. 2024
        </p>
      </div>
    </section>

    <!-- Categories Bento Grid - Asymmetric 3:2:1 Layout -->
    <section class="py-16 container mx-auto px-4">
      <div class="text-center mb-12">
        <h2 class="text-2xl font-serif font-semibold text-slate-700 dark:text-stone-100 mb-3">精選分類</h2>
        <div class="w-6 h-0.5 bg-xieOrange mx-auto"></div>
      </div>
      
      <!-- Asymmetric Bento Layout -->
      <div class="grid grid-cols-6 gap-4 md:h-[500px]">
        <!-- Large Block (3 cols) -->
        <router-link 
          v-if="displayCategories.length > 0"
          :to="{ path: '/items', query: { category: displayCategories[0].name } }"
          class="col-span-6 md:col-span-3 md:row-span-2 relative group overflow-hidden bg-stone-100 dark:bg-slate-700 cursor-pointer rounded-xl min-h-[280px]"
        >
          <img 
            :src="categoryImages[0]" 
            class="w-full h-full object-cover transition-all duration-700 group-hover:scale-105 saturate-[0.8] group-hover:saturate-100" 
            :alt="displayCategories[0].name"
          >
          <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
          <!-- Micro Label -->
          <span class="absolute top-4 left-4 bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm text-[10px] font-medium tracking-wider text-slate-600 dark:text-stone-300 px-3 py-1 rounded-full border border-white/50 dark:border-slate-600">
            SELECTED
          </span>
          <div class="absolute bottom-6 left-6 text-white">
            <h3 class="text-2xl font-bold mb-1">{{ displayCategories[0].name }}</h3>
            <p class="text-sm opacity-0 group-hover:opacity-90 transition-opacity duration-300 tracking-wide">探索更多 →</p>
          </div>
        </router-link>
        
        <!-- Medium Block (2 cols) -->
        <router-link 
          v-if="displayCategories.length > 1"
          :to="{ path: '/items', query: { category: displayCategories[1].name } }"
          class="col-span-6 md:col-span-2 relative group overflow-hidden bg-stone-100 dark:bg-slate-700 cursor-pointer rounded-xl min-h-[200px]"
        >
          <img 
            :src="categoryImages[1]" 
            class="w-full h-full object-cover transition-all duration-700 group-hover:scale-105 saturate-[0.8] group-hover:saturate-100" 
            :alt="displayCategories[1].name"
          >
          <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
          <!-- Micro Label -->
          <span class="absolute top-3 left-3 bg-xieOrange/90 text-white text-[9px] font-medium tracking-wider px-2.5 py-1 rounded-full">
            NEW
          </span>
          <div class="absolute bottom-4 left-4 text-white">
            <h3 class="text-lg font-bold">{{ displayCategories[1].name }}</h3>
            <p class="text-xs opacity-0 group-hover:opacity-80 transition-opacity duration-300 tracking-wide mt-1">查看商品 →</p>
          </div>
        </router-link>

        <!-- Small Lifestyle Shot (1 col) - Visual breathing space -->
        <div class="col-span-3 md:col-span-1 relative overflow-hidden rounded-xl min-h-[200px]">
          <img 
            src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=500&auto=format" 
            class="w-full h-full object-cover saturate-[0.6]" 
            alt="Lifestyle atmosphere"
          >
          <div class="absolute inset-0 bg-gradient-to-t from-stone-900/30 to-transparent"></div>
        </div>

        <!-- Flash Sale Block (2 cols) -->
        <router-link 
          to="/items" 
          class="col-span-3 md:col-span-2 relative group overflow-hidden cursor-pointer rounded-xl min-h-[200px]"
        >
          <div class="absolute inset-0 bg-gradient-to-br from-slate-800 to-slate-950"></div>
          <!-- Subtle pattern -->
          <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\' fill-rule=\'evenodd\'%3E%3Ccircle cx=\'3\' cy=\'3\' r=\'1\'/%3E%3C/g%3E%3C/svg%3E');"></div>
          <div class="absolute inset-0 flex items-center justify-center transition-transform duration-500 group-hover:scale-105">
            <div class="text-center text-white z-10">
              <div class="border border-white/30 px-8 py-4 backdrop-blur-sm rounded-lg bg-white/5">
                <p class="text-[10px] tracking-[0.4em] mb-2 opacity-60 font-medium">LIMITED TIME</p>
                <h3 class="text-xl font-bold tracking-[0.2em] mb-1">FLASH SALE</h3>
                <p class="text-sm opacity-70">限時特賣中</p>
              </div>
            </div>
          </div>
        </router-link>

        <!-- Organic Label Block (1 col) -->
        <div 
          v-if="displayCategories.length > 2"
          class="col-span-6 md:col-span-1 relative group overflow-hidden bg-stone-100 dark:bg-slate-700 cursor-pointer rounded-xl min-h-[200px]"
          @click="goToCategory(displayCategories[2].name)"
        >
          <img 
            src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?q=80&w=500&auto=format" 
            class="w-full h-full object-cover transition-all duration-700 group-hover:scale-105 saturate-[0.75]" 
            alt="Organic"
          >
          <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
          <!-- Organic Label -->
          <span class="absolute top-3 left-3 bg-green-600/90 text-white text-[9px] font-medium tracking-wider px-2.5 py-1 rounded-full">
            ORGANIC
          </span>
          <div class="absolute bottom-3 left-3 text-white">
            <h3 class="text-sm font-bold">有機生活</h3>
          </div>
        </div>
      </div>
    </section>

    <!-- New Arrivals / Flash Sale -->
    <section class="py-16 bg-white dark:bg-slate-800 border-y border-stone-100 dark:border-slate-700 transition-colors duration-300">
      <div class="container mx-auto px-4">
        <div class="flex justify-between items-end mb-8">
          <div>
            <h2 class="text-2xl font-serif font-semibold text-slate-700 dark:text-stone-100">新品上市</h2>
            <p class="text-stone-500 dark:text-stone-400 text-sm mt-1">New Arrivals this week.</p>
          </div>
          <router-link 
            to="/items" 
            class="text-slate-700 dark:text-stone-200 text-sm hover:text-xieOrange font-medium border-b border-slate-700 dark:border-stone-200 hover:border-xieOrange pb-0.5 transition-colors"
          >
            查看全部
          </router-link>
        </div>

        <FlashSaleSection />
      </div>
    </section>

    <!-- Value Proposition -->
    <section class="py-16 relative overflow-hidden transition-colors duration-300">
      <!-- Wood texture background -->
      <div class="absolute inset-0 bg-wood-100 dark:bg-slate-800/50"></div>
      
      <div class="relative container mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
        <div class="p-6">
          <div class="w-14 h-14 bg-white dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-700 dark:text-stone-200 border border-stone-100 dark:border-slate-600 shadow-sm">
            <i class="fas fa-truck text-xl"></i>
          </div>
          <h3 class="font-semibold text-slate-700 dark:text-stone-100 mb-2">快速配送</h3>
          <p class="text-sm text-stone-500 dark:text-stone-400">今日下單，隔日送達</p>
        </div>
        <div class="p-6">
          <div class="w-14 h-14 bg-white dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-700 dark:text-stone-200 border border-stone-100 dark:border-slate-600 shadow-sm">
            <i class="fas fa-undo text-xl"></i>
          </div>
          <h3 class="font-semibold text-slate-700 dark:text-stone-100 mb-2">30天退換保證</h3>
          <p class="text-sm text-stone-500 dark:text-stone-400">不滿意全額退款</p>
        </div>
        <div class="p-6">
          <div class="w-14 h-14 bg-white dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-700 dark:text-stone-200 border border-stone-100 dark:border-slate-600 shadow-sm">
            <i class="fas fa-medal text-xl"></i>
          </div>
          <h3 class="font-semibold text-slate-700 dark:text-stone-100 mb-2">品質嚴選</h3>
          <p class="text-sm text-stone-500 dark:text-stone-400">日本進口，品質保證</p>
        </div>
      </div>
    </section>

    <!-- Back to Top Button -->
    <button 
      v-show="showBackToTop"
      @click="scrollToTop"
      class="fixed bottom-6 right-6 w-12 h-12 bg-xieOrange/20 hover:bg-xieOrange/30 text-xieOrange rounded-full flex items-center justify-center shadow-lg backdrop-blur-sm border border-xieOrange/20 transition-all duration-300 hover:scale-110 z-50"
      title="返回頂部"
    >
      <i class="fas fa-chevron-up"></i>
    </button>
  </div>
</template>

<script>
import api from '../services/api'
import FlashSaleSection from '@/components/home/FlashSaleSection.vue'

export default {
  name: 'HomeView',
  components: {
    FlashSaleSection
  },
  data () {
    return {
      categories: [],
      categoryImages: [
        'https://images.unsplash.com/photo-1550989460-0adf9ea622e2?q=80&w=2574&auto=format&fit=crop',
        'https://images.unsplash.com/photo-1616486338812-3dadae4b4f9d?q=80&w=2670&auto=format&fit=crop',
        'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?q=80&w=2670&auto=format&fit=crop'
      ],
      showBackToTop: false
    }
  },
  computed: {
    displayCategories () {
      return this.categories.slice(0, 4)
    }
  },
  created () {
    this.fetchCategories()
  },
  mounted () {
    window.addEventListener('scroll', this.handleScroll)
  },
  beforeUnmount () {
    window.removeEventListener('scroll', this.handleScroll)
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
    goToCategory (name) {
      this.$router.push({ path: '/items', query: { category: name } })
    },
    handleScroll () {
      this.showBackToTop = window.scrollY > 400
    },
    scrollToTop () {
      window.scrollTo({ top: 0, behavior: 'smooth' })
    }
  }
}
</script>

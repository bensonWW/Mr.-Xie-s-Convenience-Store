<template>
  <div class="animate-fade-in">
    <!-- Hero Section -->
    <section class="relative h-[500px] w-full bg-stone-200 dark:bg-slate-800 overflow-hidden">
      <!-- Image Placeholder for Slider -->
      <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=2574&auto=format&fit=crop');"></div>
      <div class="absolute inset-0 bg-black/20 dark:bg-black/40"></div>
      
      <div class="relative container mx-auto px-4 h-full flex items-center">
        <div class="max-w-xl bg-white/90 dark:bg-slate-800/95 backdrop-blur-sm p-8 md:p-12 shadow-lg rounded-sm">
          <h2 class="text-xs font-bold tracking-widest text-slate-700 dark:text-stone-300 uppercase mb-4">Quality Life Choice</h2>
          <h1 class="text-3xl md:text-5xl font-serif text-slate-700 dark:text-stone-100 mb-6 leading-tight">
            日式美學，<br>量販價格。
          </h1>
          <p class="text-stone-600 dark:text-stone-400 mb-8 text-sm leading-relaxed">
            我們精選優質商品，以最實惠的價格提供給會員。<br>
            從日常食品到居家生活，為您打造極致的生活體驗。
          </p>
          <router-link to="/items" class="inline-block bg-[#E79460] text-white px-8 py-3 text-sm tracking-widest hover:bg-[#cf8354] transition shadow-lg shadow-[#E79460]/10">
            立即選購
          </router-link>
        </div>
      </div>
    </section>

    <!-- Categories / Features (Bento Grid) -->
    <section class="py-16 container mx-auto px-4">
      <div class="text-center mb-12">
        <h2 class="text-2xl font-serif text-slate-700 dark:text-stone-100 mb-2">精選分類</h2>
        <div class="w-12 h-0.5 bg-[#E79460] mx-auto"></div>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:h-[400px]">
        <!-- Large Block -->
        <div class="md:col-span-1 md:row-span-2 relative group overflow-hidden bg-stone-100 dark:bg-slate-700 cursor-pointer">
          <img src="https://images.unsplash.com/photo-1550989460-0adf9ea622e2?q=80&w=2574&auto=format&fit=crop" class="w-full h-full object-cover transition duration-700 group-hover:scale-105" alt="生鮮食品">
          <div class="absolute inset-0 bg-black/10 transition group-hover:bg-black/20"></div>
          <div class="absolute bottom-6 left-6 text-white">
            <h3 class="text-2xl font-bold mb-1">生鮮食品</h3>
            <p class="text-sm opacity-90">Fresh Food</p>
          </div>
        </div>
        
        <!-- Medium Block 1 -->
        <div class="relative group overflow-hidden bg-stone-100 dark:bg-slate-700 cursor-pointer">
          <img src="https://images.unsplash.com/photo-1616486338812-3dadae4b4f9d?q=80&w=2670&auto=format&fit=crop" class="w-full h-full object-cover transition duration-700 group-hover:scale-105" alt="居家生活">
          <div class="absolute bottom-4 left-4">
            <h3 class="text-lg font-bold text-slate-700 dark:text-stone-100 bg-white/80 dark:bg-slate-800/80 backdrop-blur px-3 py-1 inline-block">居家生活</h3>
          </div>
        </div>

        <!-- Medium Block 2 -->
        <div class="relative group overflow-hidden bg-stone-100 dark:bg-slate-700 cursor-pointer">
          <img src="https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?q=80&w=2670&auto=format&fit=crop" class="w-full h-full object-cover transition duration-700 group-hover:scale-105" alt="3C 數位">
          <div class="absolute bottom-4 left-4">
            <h3 class="text-lg font-bold text-slate-700 dark:text-stone-100 bg-white/80 dark:bg-slate-800/80 backdrop-blur px-3 py-1 inline-block">3C 數位</h3>
          </div>
        </div>

        <!-- Wide Block -->
        <div class="md:col-span-2 relative group overflow-hidden bg-stone-100 dark:bg-slate-700 cursor-pointer">
          <img src="https://images.unsplash.com/photo-1607082349566-187342175e2f?q=80&w=2670&auto=format&fit=crop" class="w-full h-full object-cover transition duration-700 group-hover:scale-105" alt="快閃特賣">
           <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-center text-white z-10">
             <div class="border border-white/50 px-8 py-4 backdrop-blur-sm bg-white/10">
               <h3 class="text-xl font-bold tracking-widest mb-1">FLASH SALE</h3>
               <p class="text-sm">限時特賣中</p>
             </div>
          </div>
          <div class="absolute inset-0 bg-slate-900/40"></div>
        </div>
      </div>
    </section>

    <!-- New Arrivals -->
    <section class="py-16 bg-white dark:bg-slate-800 border-y border-stone-100 dark:border-slate-700 transition-colors duration-300">
      <div class="container mx-auto px-4">
        <div class="flex justify-between items-end mb-8">
          <div>
            <h2 class="text-2xl font-serif text-slate-700 dark:text-stone-100">新品上市</h2>
            <p class="text-stone-500 dark:text-stone-400 text-sm mt-1">New Arrivals this week.</p>
          </div>
          <router-link to="/items" class="text-slate-700 dark:text-stone-200 text-sm hover:text-[#E79460] font-medium border-b border-slate-700 dark:border-stone-200 hover:border-[#E79460] pb-0.5 transition-colors">
            查看全部
          </router-link>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
          <ProductCard v-for="i in 4" :key="i" title="有機純棉舒適T恤" price="299" image="https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?q=80&w=2000&auto=format&fit=crop" tag="New" />
        </div>
      </div>
    </section>

    <!-- Value Proposition -->
    <section class="py-16 bg-stone-100 dark:bg-slate-800/50 transition-colors duration-300">
      <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
        <div class="p-6">
          <div class="w-12 h-12 bg-white dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-700 dark:text-stone-200 shadow-sm">
            <i class="fas fa-truck"></i>
          </div>
          <h3 class="font-bold text-slate-700 dark:text-stone-100 mb-2">快速配送</h3>
          <p class="text-sm text-stone-500 dark:text-stone-400">今日下單，隔日送達</p>
        </div>
        <div class="p-6">
          <div class="w-12 h-12 bg-white dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-700 dark:text-stone-200 shadow-sm">
             <i class="fas fa-undo"></i>
          </div>
          <h3 class="font-bold text-slate-700 dark:text-stone-100 mb-2">30天退換保證</h3>
          <p class="text-sm text-stone-500 dark:text-stone-400">不滿意全額退款</p>
        </div>
        <div class="p-6">
          <div class="w-12 h-12 bg-white dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-700 dark:text-stone-200 shadow-sm">
            <i class="fas fa-medal"></i>
          </div>
          <h3 class="font-bold text-slate-700 dark:text-stone-100 mb-2">品質嚴選</h3>
          <p class="text-sm text-stone-500 dark:text-stone-400">日本進口，品質保證</p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import ProductCard from '../components/ProductCard.vue';
</script>


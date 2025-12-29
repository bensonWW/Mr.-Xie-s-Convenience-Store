<template>
  <div class="bg-stone-50 dark:bg-slate-900 min-h-screen pb-12 transition-colors duration-300">
    <!-- Filters Header -->
    <div class="bg-white dark:bg-slate-800 border-b border-stone-200 dark:border-slate-700 sticky top-16 z-40 transition-colors duration-300">
      <div class="container mx-auto px-4 py-4">
        <!-- Breadcrumb & Sort -->
        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
          <div class="flex items-center space-x-2 text-sm text-stone-500 dark:text-stone-400">
            <router-link to="/" class="hover:text-slate-700 dark:hover:text-stone-200">首頁</router-link>
            <span>/</span>
            <span class="text-slate-700 dark:text-stone-100 font-medium">所有商品</span>
          </div>

          <!-- Quick Filters -->
          <div class="flex items-center space-x-4">
             <div class="flex overflow-x-auto space-x-2 no-scrollbar">
                <button 
                  v-for="cat in categories" 
                  :key="cat"
                  class="px-4 py-1.5 rounded-full text-sm whitespace-nowrap border transition-colors"
                  :class="activeCat === cat ? 'bg-slate-700 dark:bg-slate-600 text-white border-slate-700 dark:border-slate-600' : 'bg-white dark:bg-slate-700 text-stone-600 dark:text-stone-300 border-stone-200 dark:border-slate-600 hover:border-stone-400 dark:hover:border-stone-500'"
                  @click="activeCat = cat"
                >
                  {{ cat }}
                </button>
             </div>
          </div>

          <!-- Sort -->
          <div class="flex items-center space-x-2">
            <span class="text-xs text-stone-500 dark:text-stone-400">排序:</span>
            <select class="text-sm bg-transparent border-none focus:ring-0 cursor-pointer text-slate-700 dark:text-stone-200 font-medium">
              <option class="dark:bg-slate-800">最新上架</option>
              <option class="dark:bg-slate-800">價格由低到高</option>
              <option class="dark:bg-slate-800">價格由高到低</option>
              <option class="dark:bg-slate-800">熱銷排行</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
      <div class="flex flex-col md:flex-row gap-8">
        
        <!-- Sidebar Filter (Optional - can be toggleable) -->
        <aside class="w-full md:w-64 flex-shrink-0 hidden md:block">
           <div class="bg-white dark:bg-slate-800 p-6 rounded-sm shadow-sm border border-stone-100 dark:border-slate-700 sticky top-36 transition-colors duration-300">
             <h3 class="font-bold text-slate-700 dark:text-stone-100 mb-4 pb-2 border-b border-stone-100 dark:border-slate-700">庫存狀況</h3>
             <label class="flex items-center space-x-2 mb-2 cursor-pointer">
               <input type="checkbox" class="text-slate-700 dark:text-[#E79460] focus:ring-slate-700 dark:focus:ring-[#E79460] rounded-sm bg-white dark:bg-slate-700 border-stone-300 dark:border-slate-600">
               <span class="text-sm text-stone-600 dark:text-stone-300">僅顯示有貨</span>
             </label>
             <label class="flex items-center space-x-2 cursor-pointer">
               <input type="checkbox" class="text-slate-700 dark:text-[#E79460] focus:ring-slate-700 dark:focus:ring-[#E79460] rounded-sm bg-white dark:bg-slate-700 border-stone-300 dark:border-slate-600">
               <span class="text-sm text-stone-600 dark:text-stone-300">促銷商品</span>
             </label>

             <h3 class="font-bold text-slate-700 dark:text-stone-100 mt-8 mb-4 pb-2 border-b border-stone-100 dark:border-slate-700">價格區間</h3>
             <div class="flex items-center space-x-2">
               <input type="number" placeholder="Min" class="w-full border border-stone-200 dark:border-slate-600 p-2 text-sm rounded bg-stone-50 dark:bg-slate-700 text-slate-700 dark:text-stone-200 placeholder:text-stone-400 dark:placeholder:text-stone-500">
               <span class="text-stone-400 dark:text-stone-500">-</span>
               <input type="number" placeholder="Max" class="w-full border border-stone-200 dark:border-slate-600 p-2 text-sm rounded bg-stone-50 dark:bg-slate-700 text-slate-700 dark:text-stone-200 placeholder:text-stone-400 dark:placeholder:text-stone-500">
             </div>
           </div>
        </aside>

        <!-- Product Grid -->
        <div class="flex-1">
          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
             <ProductCard 
               v-for="i in 12" 
               :key="i"
               title="日式簡約收納盒"
               price="150"
               :sold="i * 10"
               image="https://images.unsplash.com/photo-1594269146507-681b75c12035?q=80&w=2670&auto=format&fit=crop"
             />
          </div>
          
          <!-- Pagination -->
          <div class="flex justify-center mt-12 space-x-2">
            <button class="w-10 h-10 flex items-center justify-center border border-stone-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-stone-500 dark:text-stone-400 hover:bg-stone-50 dark:hover:bg-slate-700 rounded transition-colors">
              <i class="fas fa-chevron-left"></i>
            </button>
            <button class="w-10 h-10 flex items-center justify-center border border-slate-700 dark:border-[#E79460] bg-slate-700 dark:bg-[#E79460] text-white rounded">1</button>
            <button class="w-10 h-10 flex items-center justify-center border border-stone-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-stone-500 dark:text-stone-300 hover:bg-stone-50 dark:hover:bg-slate-700 rounded transition-colors">2</button>
            <button class="w-10 h-10 flex items-center justify-center border border-stone-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-stone-500 dark:text-stone-300 hover:bg-stone-50 dark:hover:bg-slate-700 rounded transition-colors">3</button>
            <button class="w-10 h-10 flex items-center justify-center border border-stone-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-stone-500 dark:text-stone-400 hover:bg-stone-50 dark:hover:bg-slate-700 rounded transition-colors">
              <i class="fas fa-chevron-right"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import ProductCard from '../components/ProductCard.vue';

const categories = ['全部', '生鮮食品', '零食飲料', '居家生活', '3C 數位', '美妝護理'];
const activeCat = ref('全部');
</script>


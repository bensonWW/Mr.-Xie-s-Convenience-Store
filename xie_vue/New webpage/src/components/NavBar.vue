<template>
  <nav class="bg-white dark:bg-slate-900 border-b border-stone-200 dark:border-slate-700 sticky top-0 z-50 transition-colors duration-300">
    <div class="container mx-auto px-4 md:px-6 h-16 flex items-center justify-between">
      <!-- Logo -->
      <router-link to="/" class="flex items-center space-x-2">
        <div class="w-8 h-8 bg-[#E79460] rounded flex items-center justify-center text-white font-serif font-bold shadow-sm">
          X
        </div>
        <span class="text-xl font-bold text-slate-700 dark:text-stone-100 tracking-tight">Xie Mart</span>
      </router-link>

      <!-- Search Bar (Desktop) -->
      <div class="hidden md:flex flex-1 max-w-lg mx-8">
        <div class="relative w-full">
          <input 
            type="text" 
            placeholder="搜尋商品..." 
            class="w-full bg-stone-50 dark:bg-slate-800 border border-stone-200 dark:border-slate-600 rounded-md py-2 px-4 focus:outline-none focus:border-[#E79460] focus:ring-1 focus:ring-[#E79460] transition-colors text-sm text-slate-700 dark:text-stone-200 placeholder:text-stone-400 dark:placeholder:text-stone-500"
          >
          <button class="absolute right-0 top-0 h-full px-3 text-stone-400 hover:text-[#E79460] transition">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex items-center space-x-6">
        <!-- Dark Mode Toggle -->
        <button @click="toggleTheme" class="text-stone-600 dark:text-stone-300 hover:text-[#E79460] dark:hover:text-[#E79460] transition flex flex-col items-center">
          <i :class="isDark ? 'fas fa-sun' : 'fas fa-moon'" class="text-lg"></i>
          <span class="text-[10px] hidden sm:block">{{ isDark ? '淺色' : '深色' }}</span>
        </button>

        <!-- User -->
        <router-link to="/profile" class="text-stone-600 dark:text-stone-300 hover:text-[#E79460] dark:hover:text-[#E79460] transition flex flex-col items-center">
          <i class="fas fa-user text-lg"></i>
          <span class="text-[10px] hidden sm:block">會員</span>
        </router-link>

        <!-- Wishlist -->
        <router-link to="/profile?tab=wishlist" class="text-stone-600 dark:text-stone-300 hover:text-[#E79460] dark:hover:text-[#E79460] transition flex flex-col items-center">
          <i class="fas fa-heart text-lg"></i>
          <span class="text-[10px] hidden sm:block">收藏</span>
        </router-link>

        <!-- Cart -->
        <router-link to="/car" class="relative text-stone-600 dark:text-stone-300 hover:text-[#E79460] dark:hover:text-[#E79460] transition flex flex-col items-center group">
          <div class="relative">
            <i class="fas fa-shopping-bag text-lg"></i>
            <span class="absolute -top-1 -right-2 bg-[#E79460] text-white text-[10px] font-bold px-1.5 rounded-full border-2 border-white dark:border-slate-900 shadow-sm">3</span>
          </div>
          <span class="text-[10px] hidden sm:block">購物車</span>
        </router-link>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { isDark, toggleDarkMode } from '../utils/darkMode.js';

const toggleTheme = () => {
  toggleDarkMode();
};
</script>


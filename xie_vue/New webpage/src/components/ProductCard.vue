<template>
  <div class="group bg-white dark:bg-slate-800 rounded-md border border-stone-100 dark:border-slate-700 shadow-sm hover:shadow-md dark:hover:shadow-slate-900/50 transition-all duration-300 overflow-hidden cursor-pointer">
    <!-- Image -->
    <div class="relative aspect-square overflow-hidden bg-stone-100 dark:bg-slate-700">
      <img :src="image" :alt="title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
      <!-- Tag (Optional) -->
      <div v-if="tag" class="absolute top-2 left-2 bg-slate-700 dark:bg-slate-600 text-white text-[10px] px-2 py-1 uppercase tracking-wider">
        {{ tag }}
      </div>
      <!-- Add to Cart (Quick View) -->
      <button class="absolute bottom-2 right-2 bg-white/90 dark:bg-slate-800/90 p-2 rounded-full shadow-sm text-slate-700 dark:text-stone-200 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-[#E79460] hover:text-white">
        <i class="fas fa-plus"></i>
      </button>
    </div>

    <!-- Content -->
    <div class="p-4">
      <div class="text-xs text-stone-400 dark:text-stone-500 mb-1">{{ category }}</div>
      <h3 class="text-sm font-medium text-slate-700 dark:text-stone-100 mb-2 line-clamp-2 h-10 group-hover:text-[#E79460] transition-colors">
        {{ title }}
      </h3>
      <div class="flex items-end justify-between">
        <div class="flex items-baseline space-x-1">
          <span class="text-xs text-stone-500 dark:text-stone-400">NT$</span>
          <span class="text-lg font-bold text-slate-700 dark:text-stone-100">{{ price }}</span>
        </div>
        <div class="text-[10px] text-stone-400 dark:text-stone-500">已售 {{ sold }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  image: { type: String, default: 'https://via.placeholder.com/300' },
  title: { type: String, default: '商品名稱' },
  price: { type: [Number, String], default: 100 },
  category: { type: String, default: '分類' },
  tag: { type: String, default: '' },
  sold: { type: [Number, String], default: 0 }
});
</script>


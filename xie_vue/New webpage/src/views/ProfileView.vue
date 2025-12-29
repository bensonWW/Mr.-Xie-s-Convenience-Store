<template>
  <div class="bg-stone-50 dark:bg-slate-900 min-h-screen py-12 transition-colors duration-300">
    <div class="container mx-auto px-4 max-w-5xl">
      <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar -->
        <div class="w-full md:w-64 flex-shrink-0">
          <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-stone-200 dark:border-slate-700 overflow-hidden transition-colors duration-300">
            <div class="p-6 bg-stone-100/50 dark:bg-slate-700/50 border-b border-stone-200 dark:border-slate-700 text-center">
              <div class="w-16 h-16 bg-slate-700 dark:bg-[#E79460] rounded-full mx-auto mb-3 flex items-center justify-center text-white text-2xl font-serif">
                X
              </div>
              <h3 class="font-bold text-slate-700 dark:text-stone-100">謝老闆</h3>
              <p class="text-xs text-stone-500 dark:text-stone-400">一般會員 (Lv.1)</p>
            </div>
            <nav class="p-2">
              <a href="#" class="block px-4 py-3 rounded text-sm font-bold text-[#E79460] bg-orange-50 dark:bg-orange-900/20 transition mb-1 border-l-4 border-[#E79460]">
                <i class="fas fa-th-large w-6 text-center"></i> 我的帳戶
              </a>
              <a href="#" class="block px-4 py-3 rounded text-sm font-medium text-stone-600 dark:text-stone-300 hover:bg-stone-50 dark:hover:bg-slate-700 hover:text-slate-700 dark:hover:text-stone-100 transition mb-1">
                <i class="fas fa-box w-6 text-center"></i> 訂單查詢
              </a>
               <a href="#" class="block px-4 py-3 rounded text-sm font-medium text-stone-600 dark:text-stone-300 hover:bg-stone-50 dark:hover:bg-slate-700 hover:text-slate-700 dark:hover:text-stone-100 transition mb-1">
                <i class="fas fa-heart w-6 text-center"></i> 收藏清單
              </a>
               <a href="#" class="block px-4 py-3 rounded text-sm font-medium text-stone-600 dark:text-stone-300 hover:bg-stone-50 dark:hover:bg-slate-700 hover:text-slate-700 dark:hover:text-stone-100 transition mb-1">
                <i class="fas fa-map-marker-alt w-6 text-center"></i> 地址管理
              </a>
               <a href="#" class="block px-4 py-3 rounded text-sm font-medium text-stone-600 dark:text-stone-300 hover:bg-stone-50 dark:hover:bg-slate-700 hover:text-slate-700 dark:hover:text-stone-100 transition mb-1 border-t border-stone-100 dark:border-slate-700 mt-2 text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                <i class="fas fa-sign-out-alt w-6 text-center"></i> 登出
              </a>
            </nav>
          </div>
        </div>
        
        <!-- Content -->
        <div class="flex-1">
          <h2 class="text-xl font-bold text-slate-700 dark:text-stone-100 mb-6">我的帳戶</h2>
          
          <!-- Stats Cards -->
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
            <div class="bg-slate-700 dark:bg-[#E79460] rounded-md p-6 text-white shadow-lg shadow-slate-200 dark:shadow-none">
               <div class="text-xs opacity-70 mb-1">錢包餘額</div>
               <div class="text-2xl font-bold">NT$ 5,240</div>
            </div>
             <div class="bg-white dark:bg-slate-800 rounded-md border border-stone-200 dark:border-slate-700 p-6 shadow-sm transition-colors duration-300">
               <div class="text-xs text-stone-500 dark:text-stone-400 mb-1">可用優惠券</div>
               <div class="text-2xl font-bold text-slate-700 dark:text-stone-100">3 張</div>
            </div>
             <div class="bg-white dark:bg-slate-800 rounded-md border border-stone-200 dark:border-slate-700 p-6 shadow-sm transition-colors duration-300">
               <div class="text-xs text-stone-500 dark:text-stone-400 mb-1">累積消費</div>
               <div class="text-2xl font-bold text-slate-700 dark:text-stone-100">NT$ 12,020</div>
            </div>
          </div>

          <!-- Recent Orders -->
          <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-stone-200 dark:border-slate-700 transition-colors duration-300">
            <div class="px-6 py-4 border-b border-stone-200 dark:border-slate-700 flex justify-between items-center bg-stone-50/50 dark:bg-slate-700/30">
              <h3 class="font-bold text-slate-700 dark:text-stone-100 text-sm">最近訂單</h3>
              <a href="#" class="text-xs text-stone-500 dark:text-stone-400 hover:text-slate-700 dark:hover:text-stone-200 transition">查看全部</a>
            </div>
            <div class="p-6">
               <div class="border border-stone-100 dark:border-slate-700 rounded-md p-4 mb-4 hover:border-stone-300 dark:hover:border-slate-500 transition">
                 <div class="flex justify-between items-start mb-4">
                   <div>
                     <div class="text-xs text-stone-400 dark:text-stone-500 mb-1">2025-12-25</div>
                     <div class="font-bold text-slate-700 dark:text-stone-100">#ORD-20251225-001</div>
                   </div>
                   <div class="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 text-[10px] px-2 py-0.5 rounded-full font-bold">已完成</div>
                 </div>
                 <div class="flex items-center space-x-4">
                   <div class="w-12 h-12 bg-stone-100 dark:bg-slate-700 rounded-sm"></div>
                   <div class="w-12 h-12 bg-stone-100 dark:bg-slate-700 rounded-sm"></div>
                   <div class="text-xs text-stone-500 dark:text-stone-400">+2 more</div>
                 </div>
               </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


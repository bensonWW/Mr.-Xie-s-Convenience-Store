<template>
  <div class="min-h-screen bg-stone-100 dark:bg-slate-950 flex font-sans transition-colors duration-300">
    <!-- Admin Sidebar (already dark by design) -->
    <aside class="w-64 bg-slate-900 text-stone-400 flex flex-col flex-shrink-0">
      <div class="h-16 flex items-center px-6 text-white font-bold text-xl border-b border-slate-800">
        Xie Admin
      </div>
      <nav class="flex-1 py-6 space-y-1">
        <a href="#" class="block px-6 py-3 hover:bg-slate-800 hover:text-white transition text-white border-l-4 border-orange-600 bg-slate-800">
          <i class="fas fa-chart-line w-6"></i> 儀表板
        </a>
        <a href="#" class="block px-6 py-3 hover:bg-slate-800 hover:text-white transition">
           <i class="fas fa-box w-6"></i> 商品管理
        </a>
        <a href="#" class="block px-6 py-3 hover:bg-slate-800 hover:text-white transition">
           <i class="fas fa-shopping-cart w-6"></i> 訂單管理
        </a>
        <a href="#" class="block px-6 py-3 hover:bg-slate-800 hover:text-white transition">
           <i class="fas fa-users w-6"></i> 會員管理
        </a>
        <a href="#" class="block px-6 py-3 hover:bg-slate-800 hover:text-white transition">
           <i class="fas fa-ticket-alt w-6"></i> 優惠券
        </a>
      </nav>
      <div class="p-4 border-t border-slate-800">
        <button class="w-full text-left text-sm hover:text-white transition flex items-center">
          <i class="fas fa-sign-out-alt w-6"></i> 登出
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
       <header class="h-16 bg-white dark:bg-slate-800 shadow-sm border-b border-stone-200 dark:border-slate-700 flex items-center justify-between px-6 transition-colors duration-300">
         <h2 class="font-bold text-slate-700 dark:text-stone-100">儀表板總覽</h2>
         <div class="flex items-center space-x-4">
           <button class="text-stone-400 dark:text-stone-500 hover:text-slate-700 dark:hover:text-stone-300 relative transition">
             <i class="fas fa-bell"></i>
             <div class="w-2 h-2 bg-red-500 rounded-full absolute top-0 right-0"></div>
           </button>
           <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-600"></div>
         </div>
       </header>

       <main class="flex-1 overflow-auto p-6 md:p-8">
         <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-slate-800 p-6 rounded shadow-sm border border-stone-200 dark:border-slate-700 transition-colors duration-300">
               <div class="text-sm text-stone-500 dark:text-stone-400 mb-1">今日訂單</div>
               <div class="text-2xl font-bold text-slate-700 dark:text-stone-100">45</div>
            </div>
             <div class="bg-white dark:bg-slate-800 p-6 rounded shadow-sm border border-stone-200 dark:border-slate-700 transition-colors duration-300">
               <div class="text-sm text-stone-500 dark:text-stone-400 mb-1">今日營業額</div>
               <div class="text-2xl font-bold text-slate-700 dark:text-stone-100">NT$ 32,400</div>
            </div>
         </div>
         
         <div class="bg-white dark:bg-slate-800 rounded shadow-sm border border-stone-200 dark:border-slate-700 h-96 flex items-center justify-center text-stone-400 dark:text-stone-500 transition-colors duration-300">
           [ Admin Router View Content Here ]
         </div>
       </main>
    </div>
  </div>
</template>


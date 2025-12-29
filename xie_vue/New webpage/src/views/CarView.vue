<template>
  <div class="bg-stone-50 dark:bg-slate-900 min-h-screen py-12 transition-colors duration-300">
    <div class="container mx-auto px-4 max-w-5xl">
      <h1 class="text-2xl font-serif text-slate-700 dark:text-stone-100 mb-8 flex items-center">
        <span class="bg-slate-700 dark:bg-[#E79460] text-white w-8 h-8 inline-flex items-center justify-center rounded-full text-sm mr-3">1</span>
        購物車
      </h1>

      <div class="flex flex-col lg:flex-row gap-8">
        <!-- Cart Items -->
        <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-stone-200 dark:border-slate-700 flex-1 overflow-hidden transition-colors duration-300">
          <div class="overflow-x-auto">
            <table class="w-full text-left">
              <thead class="bg-stone-50 dark:bg-slate-700/50 text-stone-500 dark:text-stone-400 text-xs uppercase border-b border-stone-200 dark:border-slate-700">
                <tr>
                  <th class="px-6 py-4 font-medium">商品資訊</th>
                  <th class="px-6 py-4 font-medium">單價</th>
                  <th class="px-6 py-4 font-medium">數量</th>
                  <th class="px-6 py-4 font-medium">小計</th>
                  <th class="px-6 py-4 font-medium"></th>
                </tr>
              </thead>
              <tbody class="divide-y divide-stone-100 dark:divide-slate-700">
                <tr v-for="i in 3" :key="i" class="hover:bg-stone-50/50 dark:hover:bg-slate-700/50 transition">
                  <td class="px-6 py-6">
                    <div class="flex items-center space-x-4">
                      <div class="w-20 h-20 bg-stone-100 dark:bg-slate-700 rounded-sm overflow-hidden flex-shrink-0">
                         <img src="https://images.unsplash.com/photo-1594269146507-681b75c12035?q=80&w=2670&auto=format&fit=crop" class="w-full h-full object-cover">
                      </div>
                      <div>
                        <div class="text-sm font-bold text-slate-700 dark:text-stone-100 mb-1">日式收納盒 XL</div>
                        <div class="text-xs text-stone-400 dark:text-stone-500">白色</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-6 text-sm text-stone-600 dark:text-stone-300">NT$ 150</td>
                  <td class="px-6 py-6">
                     <div class="flex items-center border border-stone-200 dark:border-slate-600 rounded w-24 bg-white dark:bg-slate-700">
                       <button class="px-2 py-1 hover:bg-stone-100 dark:hover:bg-slate-600 text-stone-600 dark:text-stone-300 transition">-</button>
                       <input type="text" value="2" class="w-full text-center text-sm bg-transparent text-slate-700 dark:text-stone-200">
                       <button class="px-2 py-1 hover:bg-stone-100 dark:hover:bg-slate-600 text-stone-600 dark:text-stone-300 transition">+</button>
                     </div>
                  </td>
                  <td class="px-6 py-6 text-sm font-bold text-[#E79460]">NT$ 300</td>
                  <td class="px-6 py-6 text-right">
                    <button class="text-stone-400 dark:text-stone-500 hover:text-red-500 transition">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="p-6 bg-stone-50 dark:bg-slate-700/30 border-t border-stone-200 dark:border-slate-700 text-right">
             <button class="text-stone-500 dark:text-stone-400 hover:text-slate-700 dark:hover:text-stone-200 text-sm transition">清空購物車</button>
          </div>
        </div>

        <!-- Summary -->
        <div class="w-full lg:w-80 flex-shrink-0">
          <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-stone-200 dark:border-slate-700 p-6 sticky top-24 transition-colors duration-300">
            <h3 class="font-bold text-slate-700 dark:text-stone-100 mb-6 pb-4 border-b border-stone-100 dark:border-slate-700">訂單摘要</h3>
            
            <div class="space-y-3 text-sm mb-6">
              <div class="flex justify-between text-stone-600 dark:text-stone-300">
                <span>商品小計</span>
                <span>NT$ 900</span>
              </div>
              <div class="flex justify-between text-stone-600 dark:text-stone-300">
                <span>運費</span>
                <span>NT$ 60</span>
              </div>
              <div class="flex justify-between text-[#E79460]">
                <span>折扣</span>
                <span>- NT$ 0</span>
              </div>
            </div>
            
            <div class="border-t border-stone-100 dark:border-slate-700 pt-4 mb-8 flex justify-between items-end">
              <span class="font-bold text-slate-700 dark:text-stone-100">總金額</span>
              <span class="text-2xl font-bold text-slate-700 dark:text-stone-100">NT$ 960</span>
            </div>

            <button class="w-full bg-[#E79460] text-white py-3 rounded-md font-bold hover:bg-[#cf8354] transition shadow-lg shadow-[#E79460]/20 mb-4">
              前往結帳
            </button>
            
            <div class="text-center">
               <router-link to="/items" class="text-xs text-stone-400 dark:text-stone-500 hover:text-slate-700 dark:hover:text-stone-300 border-b border-stone-200 dark:border-slate-600 hover:border-slate-700 dark:hover:border-stone-300 transition-colors pb-0.5">
                 繼續購物
               </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


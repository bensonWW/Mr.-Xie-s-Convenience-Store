<template>
  <div class="bg-stone-50 dark:bg-slate-900 min-h-screen py-12 transition-colors duration-300">
    <div class="container mx-auto px-4">
      <!-- Product Main Section -->
      <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-stone-200 dark:border-slate-700 p-6 md:p-10 mb-8 transition-colors duration-300">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
          
          <!-- Gallery -->
          <div class="space-y-4">
            <div class="aspect-square bg-stone-100 dark:bg-slate-700 rounded-sm overflow-hidden border border-stone-100 dark:border-slate-600 relative group">
              <img src="https://images.unsplash.com/photo-1594269146507-681b75c12035?q=80&w=2670&auto=format&fit=crop" class="w-full h-full object-cover" alt="Product">
              <div class="absolute top-4 left-4 bg-[#E79460] text-white text-xs px-2 py-1">Hot Sale</div>
            </div>
            <div class="grid grid-cols-4 gap-4">
              <div class="aspect-square bg-stone-100 dark:bg-slate-700 rounded-sm border border-stone-200 dark:border-slate-600 cursor-pointer hover:border-slate-700 dark:hover:border-[#E79460] transition"></div>
              <div class="aspect-square bg-stone-100 dark:bg-slate-700 rounded-sm border border-stone-200 dark:border-slate-600 cursor-pointer hover:border-slate-700 dark:hover:border-[#E79460] transition"></div>
              <div class="aspect-square bg-stone-100 dark:bg-slate-700 rounded-sm border border-stone-200 dark:border-slate-600 cursor-pointer hover:border-slate-700 dark:hover:border-[#E79460] transition"></div>
              <div class="aspect-square bg-stone-100 dark:bg-slate-700 rounded-sm border border-stone-200 dark:border-slate-600 cursor-pointer hover:border-slate-700 dark:hover:border-[#E79460] transition"></div>
            </div>
          </div>

          <!-- Info -->
          <div class="flex flex-col h-full">
            <div class="flex-1">
               <div class="flex items-center space-x-2 mb-2 text-sm">
                 <span class="text-[#E79460] font-bold">Xie Select</span>
                 <span class="text-stone-400 dark:text-stone-500">|</span>
                 <span class="text-stone-500 dark:text-stone-400">居家生活</span>
               </div>
               <h1 class="text-3xl font-serif text-slate-700 dark:text-stone-100 mb-4 leading-tight">日式簡約大容量收納盒 - 白色 XL號</h1>
               <div class="text-stone-500 dark:text-stone-400 text-sm mb-6 leading-relaxed">
                 採用環保材質，堅固耐用。<br>
                 可堆疊設計，節省空間，讓您的居家環境井井有條。
               </div>

               <!-- Price -->
               <div class="bg-stone-50 dark:bg-slate-700/50 p-6 rounded-sm mb-8 flex items-end space-x-2">
                 <span class="text-sm text-stone-500 dark:text-stone-400 mb-2">建議售價</span>
                 <span class="text-3xl font-bold text-[#E79460]">NT$ 150</span>
                 <span class="text-stone-400 dark:text-stone-500 line-through text-sm mb-1">NT$ 200</span>
               </div>

               <!-- Options -->
               <div class="space-y-6 mb-8">
                 <div>
                   <label class="block text-sm font-bold text-slate-700 dark:text-stone-100 mb-2">顏色</label>
                   <div class="flex space-x-3">
                     <button class="w-8 h-8 rounded-full border border-stone-300 dark:border-slate-500 bg-white ring-2 ring-slate-700 dark:ring-[#E79460] ring-offset-2 dark:ring-offset-slate-800"></button>
                     <button class="w-8 h-8 rounded-full border border-stone-300 dark:border-slate-500 bg-stone-500 hover:ring-2 hover:ring-slate-300 dark:hover:ring-slate-500 ring-offset-2 dark:ring-offset-slate-800"></button>
                     <button class="w-8 h-8 rounded-full border border-stone-300 dark:border-slate-500 bg-black hover:ring-2 hover:ring-slate-300 dark:hover:ring-slate-500 ring-offset-2 dark:ring-offset-slate-800"></button>
                   </div>
                 </div>
                 
                 <div>
                   <label class="block text-sm font-bold text-slate-700 dark:text-stone-100 mb-2">數量</label>
                   <div class="flex items-center border border-stone-300 dark:border-slate-600 rounded w-32 bg-white dark:bg-slate-700">
                     <button class="px-3 py-2 text-stone-600 dark:text-stone-300 hover:bg-stone-100 dark:hover:bg-slate-600 transition">-</button>
                     <input type="text" value="1" class="w-full text-center border-x border-stone-300 dark:border-slate-600 py-2 focus:outline-none text-slate-700 dark:text-stone-200 bg-transparent">
                     <button class="px-3 py-2 text-stone-600 dark:text-stone-300 hover:bg-stone-100 dark:hover:bg-slate-600 transition">+</button>
                   </div>
                   <p class="text-xs text-stone-400 dark:text-stone-500 mt-2">庫存充足</p>
                 </div>
               </div>
            </div>

            <!-- Actions -->
            <div class="flex space-x-4">
              <button class="flex-1 bg-white dark:bg-transparent border-2 border-slate-700 dark:border-stone-300 text-slate-700 dark:text-stone-200 py-4 px-6 rounded-md font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                加入購物車
              </button>
              <button class="flex-1 bg-[#E79460] text-white py-4 px-6 rounded-md font-bold hover:bg-[#cf8354] transition shadow-lg shadow-[#E79460]/20">
                立即購買
              </button>
              <button class="p-4 border border-stone-200 dark:border-slate-600 rounded-md text-stone-400 dark:text-stone-500 hover:text-red-500 hover:border-red-200 dark:hover:border-red-400 transition">
                 <i class="far fa-heart"></i>
              </button>
            </div>

          </div>
        </div>
      </div>

      <!-- Detail Tabs -->
      <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm border border-stone-200 dark:border-slate-700 transition-colors duration-300">
        <div class="flex border-b border-stone-200 dark:border-slate-700">
          <button class="px-8 py-4 text-sm font-bold text-slate-700 dark:text-[#E79460] border-b-2 border-slate-700 dark:border-[#E79460] bg-stone-50 dark:bg-slate-700/50">商品詳情</button>
          <button class="px-8 py-4 text-sm font-medium text-stone-500 dark:text-stone-400 hover:text-slate-700 dark:hover:text-stone-200">規格說明</button>
          <button class="px-8 py-4 text-sm font-medium text-stone-500 dark:text-stone-400 hover:text-slate-700 dark:hover:text-stone-200">會員評價 (12)</button>
        </div>
        <div class="p-8 md:p-12 text-stone-600 dark:text-stone-300 leading-relaxed space-y-4">
          <p>
            這裡放置詳細的商品介紹 HTML 內容。
            <br><br>
            • 清新簡約設計，適合各種居家風格<br>
            • 優質 PP 材質，無毒無味<br>
            • 底部加高設計，防滑耐磨
          </p>
          <div class="bg-stone-100 dark:bg-slate-700 p-8 text-center text-stone-400 dark:text-stone-500 rounded">
            [ 商品詳細圖片展示區 ]
          </div>
        </div>
      </div>
      
    </div>
  </div>
</template>


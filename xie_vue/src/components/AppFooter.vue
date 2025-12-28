<template>
  <footer class="bg-gray-200 dark:bg-slate-900 border-t border-gray-300 dark:border-slate-800/50 pt-12 pb-8 text-gray-600 dark:text-stone-400 transition-colors duration-300">
    <div class="container mx-auto px-4 md:px-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
        <!-- Brand -->
        <div class="space-y-4">
          <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-xieOrange rounded flex items-center justify-center text-white font-serif font-bold">
              X
            </div>
            <h3 class="text-slate-700 dark:text-stone-100 font-bold text-lg">Xie Mart</h3>
          </div>
          <p class="text-sm leading-relaxed text-stone-500 dark:text-stone-400">
            即使是便利店，也要追求極致的生活品質。<br>
            提供類似好市多的量販優惠，<br>
            以及日式商店的貼心服務。
          </p>
        </div>

        <!-- Links 1 -->
        <div>
          <h4 class="font-semibold text-slate-700 dark:text-stone-100 mb-4">關於我們</h4>
          <ul class="space-y-2 text-sm">
            <li><a href="#" class="hover:text-xieOrange transition">品牌故事</a></li>
            <li><a href="#" class="hover:text-xieOrange transition">門市資訊</a></li>
            <li><a href="#" class="hover:text-xieOrange transition">招募人才</a></li>
          </ul>
        </div>

        <!-- Links 2 -->
        <div>
          <h4 class="font-semibold text-slate-700 dark:text-stone-100 mb-4">顧客服務</h4>
          <ul class="space-y-2 text-sm">
            <li><a href="#" class="hover:text-xieOrange transition">常見問題</a></li>
            <li><a href="#" class="hover:text-xieOrange transition">運送說明</a></li>
            <li><a href="#" class="hover:text-xieOrange transition">退換貨政策</a></li>
            <li><a href="#" class="hover:text-xieOrange transition">聯絡我們</a></li>
          </ul>
        </div>

        <!-- Newsletter -->
        <div>
          <h4 class="font-semibold text-slate-700 dark:text-stone-100 mb-4">訂閱電子報</h4>
          <p class="text-xs text-stone-500 dark:text-stone-400 mb-3">獲得最新優惠與日式生活提案。</p>
          <div class="flex">
            <input 
              type="email" 
              placeholder="您的 Email" 
              class="flex-1 bg-white dark:bg-slate-800 border border-stone-200 dark:border-slate-700 rounded-l-md px-3 py-2 text-sm focus:outline-none focus:border-xieOrange text-slate-700 dark:text-stone-200 placeholder:text-stone-400 dark:placeholder:text-stone-500"
            >
            <button class="bg-xieOrange text-white px-4 py-2 text-sm rounded-r-md hover:bg-[#cf8354] transition font-medium">
              訂閱
            </button>
          </div>
        </div>
      </div>

      <div class="border-t border-stone-200 dark:border-slate-800/50 pt-6 flex flex-col md:flex-row justify-between items-center text-xs text-stone-400 dark:text-stone-500">
        <p>&copy; 2025 Mr. Xie's Convenience Store. All rights reserved.</p>
        <div class="flex space-x-4 mt-2 md:mt-0">
          <a href="#" class="hover:text-stone-600 dark:hover:text-stone-300 transition">隱私權政策</a>
          <a href="#" class="hover:text-stone-600 dark:hover:text-stone-300 transition">使用條款</a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'AppFooter'
}
</script>

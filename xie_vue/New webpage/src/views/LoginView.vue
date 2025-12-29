<template>
  <div class="min-h-screen bg-stone-50 dark:bg-slate-900 flex items-center justify-center p-4 transition-colors duration-300">
    <div class="bg-white dark:bg-slate-800 w-full max-w-4xl rounded-xl shadow-lg shadow-stone-200/50 dark:shadow-slate-900/50 overflow-hidden flex flex-col md:flex-row border border-stone-100 dark:border-slate-700 transition-colors duration-300">
      
      <!-- Left: Visual Section -->
      <div class="md:w-1/2 bg-slate-700 relative hidden md:flex items-center justify-center p-12 text-white overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1579113800032-c38bd60600d6?q=80&w=2569&auto=format&fit=crop')] bg-cover bg-center opacity-40 grayscale mix-blend-overlay"></div>
        <div class="relative z-10 text-center">
          <div class="w-16 h-16 bg-[#E79460] rounded mb-6 mx-auto flex items-center justify-center text-2xl font-serif font-bold shadow-lg shadow-[#E79460]/20">
            X
          </div>
          <h2 class="text-3xl font-serif font-bold mb-4 tracking-wide">Xie Mart</h2>
          <p class="text-stone-300 text-sm leading-relaxed max-w-xs mx-auto">
            發現日式生活美學。<br>
            享受精選商品的極致體驗。
          </p>
        </div>
      </div>

      <!-- Right: Form Section -->
      <div class="md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
        <div class="text-center md:text-left mb-8">
          <h2 class="text-2xl font-bold text-slate-700 dark:text-stone-100 mb-2">歡迎回來</h2>
          <p class="text-sm text-stone-500 dark:text-stone-400">請輸入您的帳號密碼以登入會員</p>
        </div>

        <form class="space-y-6">
          <div>
            <label class="block text-sm font-bold text-slate-700 dark:text-stone-100 mb-2" for="email">電子信箱</label>
            <input 
              id="email"
              type="email" 
              placeholder="name@example.com" 
              class="w-full bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-md py-3 px-4 focus:outline-none focus:border-[#E79460] focus:ring-1 focus:ring-[#E79460] transition-colors text-slate-700 dark:text-stone-200 placeholder:text-stone-400 dark:placeholder:text-stone-500"
            >
          </div>

          <div>
            <div class="flex justify-between items-center mb-2">
              <label class="block text-sm font-bold text-slate-700 dark:text-stone-100" for="password">密碼</label>
              <a href="#" class="text-xs text-[#E79460] hover:text-[#cf8354]">忘記密碼?</a>
            </div>
            <input 
              id="password"
              type="password" 
              placeholder="••••••••" 
              class="w-full bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-md py-3 px-4 focus:outline-none focus:border-[#E79460] focus:ring-1 focus:ring-[#E79460] transition-colors text-slate-700 dark:text-stone-200 placeholder:text-stone-400 dark:placeholder:text-stone-500"
            >
          </div>

          <button class="w-full bg-[#E79460] text-white font-bold py-3 px-4 rounded-md hover:bg-[#cf8354] transition shadow-md shadow-[#E79460]/20">
            立即登入
          </button>
        </form>

        <div class="mt-8 text-center">
          <p class="text-sm text-stone-500 dark:text-stone-400">
            還沒有帳號? 
            <router-link to="/register" class="text-[#E79460] font-bold hover:text-[#cf8354] border-b border-[#E79460] pb-0.5">
              免費註冊
            </router-link>
          </p>
        </div>

        <!-- Social Login -->
        <div class="mt-8 pt-8 border-t border-stone-100 dark:border-slate-700">
           <div class="flex justify-center space-x-4">
             <button class="flex items-center justify-center w-full border border-stone-200 dark:border-slate-600 rounded-md py-2.5 hover:bg-stone-50 dark:hover:bg-slate-700 transition text-sm text-slate-700 dark:text-stone-200 font-medium">
               <i class="fab fa-google mr-2 text-stone-400 dark:text-stone-500"></i> Google
             </button>
             <button class="flex items-center justify-center w-full border border-stone-200 dark:border-slate-600 rounded-md py-2.5 hover:bg-stone-50 dark:hover:bg-slate-700 transition text-sm text-slate-700 dark:text-stone-200 font-medium">
               <i class="fab fa-line mr-2 text-green-500"></i> LINE
             </button>
           </div>
        </div>
      </div>
    </div>
  </div>
</template>


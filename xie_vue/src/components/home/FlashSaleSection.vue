<template>
  <section>
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-2xl font-bold text-xieBlue flex items-center">
        <i class="fas fa-bolt text-xieOrange mr-2"></i> 限時下殺
      </h2>
      <div class="text-sm font-bold text-white bg-red-600 px-3 py-1 rounded">
        剩餘時間 02:14:59
      </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
      <div class="bg-white rounded-lg shadow hover:shadow-lg transition group">
        <div class="relative">
          <img src="https://images.unsplash.com/photo-1585837575652-2c9620943a76?auto=format&fit=crop&w=300&q=80" class="w-full h-40 object-cover rounded-t-lg">
          <span class="absolute top-0 left-0 bg-red-600 text-white text-xs px-2 py-1 font-bold">5折</span>
        </div>
        <div class="p-3">
          <h3 class="text-sm font-bold text-gray-800 line-clamp-2 h-10 mb-2 group-hover:text-xieOrange">倍潔雅 柔軟抽取式衛生紙 (150抽x12包)</h3>
          <div class="text-gray-400 text-xs line-through">NT$ 299</div>
          <div class="text-xieOrange font-bold text-lg">NT$ 159</div>
          <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
            <div class="bg-red-500 h-1.5 rounded-full" style="width: 80%"></div>
          </div>
          <div class="text-xs text-gray-500 mt-1">已搶 80%</div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow hover:shadow-lg transition group">
        <div class="relative">
          <img src="https://images.unsplash.com/photo-1544239334-038d8a245d6f?auto=format&fit=crop&w=300&q=80" class="w-full h-40 object-cover rounded-t-lg">
        </div>
        <div class="p-3">
          <h3 class="text-sm font-bold text-gray-800 line-clamp-2 h-10 mb-2 group-hover:text-xieOrange">日本 IRIS 智能除蟎吸塵器 大拍 3.5</h3>
          <div class="text-gray-400 text-xs line-through">NT$ 3,990</div>
          <div class="text-xieOrange font-bold text-lg">NT$ 2,480</div>
            <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
            <div class="bg-red-500 h-1.5 rounded-full" style="width: 45%"></div>
          </div>
          <div class="text-xs text-gray-500 mt-1">已搶 45%</div>
        </div>
      </div>

        <div class="bg-white rounded-lg shadow hover:shadow-lg transition group">
        <div class="relative">
          <img src="https://plus.unsplash.com/premium_photo-1664372599728-6677cb2d45c5?auto=format&fit=crop&w=300&q=80" class="w-full h-40 object-cover rounded-t-lg">
        </div>
        <div class="p-3">
          <h3 class="text-sm font-bold text-gray-800 line-clamp-2 h-10 mb-2 group-hover:text-xieOrange">Ariel 4D 抗菌洗衣膠囊 (39顆x3袋)</h3>
          <div class="text-gray-400 text-xs line-through">NT$ 899</div>
          <div class="text-xieOrange font-bold text-lg">NT$ 599</div>
            <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
            <div class="bg-red-500 h-1.5 rounded-full" style="width: 90%"></div>
          </div>
          <div class="text-xs text-gray-500 mt-1">即將售完</div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow hover:shadow-lg transition group">
        <div class="relative">
          <img src="https://images.unsplash.com/photo-1574269909862-7e1d70bb8078?auto=format&fit=crop&w=300&q=80" class="w-full h-40 object-cover rounded-t-lg">
        </div>
        <div class="p-3">
          <h3 class="text-sm font-bold text-gray-800 line-clamp-2 h-10 mb-2 group-hover:text-xieOrange">Philips 飛利浦 智慧萬用鍋 (金小萬)</h3>
          <div class="text-gray-400 text-xs line-through">NT$ 12,900</div>
          <div class="text-xieOrange font-bold text-lg">NT$ 8,888</div>
            <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
            <div class="bg-red-500 h-1.5 rounded-full" style="width: 30%"></div>
          </div>
          <div class="text-xs text-gray-500 mt-1">已搶 30%</div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow hover:shadow-lg transition group">
        <div class="relative">
          <img src="https://images.unsplash.com/photo-1626806775351-638563e48278?auto=format&fit=crop&w=300&q=80" class="w-full h-40 object-cover rounded-t-lg">
        </div>
        <div class="p-3">
          <h3 class="text-sm font-bold text-gray-800 line-clamp-2 h-10 mb-2 group-hover:text-xieOrange">小米 智慧直流變頻電風扇 2 Pro</h3>
          <div class="text-gray-400 text-xs line-through">NT$ 2,995</div>
          <div class="text-xieOrange font-bold text-lg">NT$ 2,395</div>
            <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
            <div class="bg-red-500 h-1.5 rounded-full" style="width: 15%"></div>
          </div>
          <div class="text-xs text-gray-500 mt-1">熱賣中</div>
        </div>
      </div>

    </div>
  </section>
</template>

<script>
export default {
  name: 'FlashSaleSection'
}
</script>

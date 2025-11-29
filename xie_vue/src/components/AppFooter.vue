<template>
  <footer class="bg-xieBlue text-gray-400 py-10 mt-auto border-t-4 border-xieOrange">
    <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-8">
      <div>
        <h3 class="text-white font-bold text-lg mb-4">關於 謝先生便利商店</h3>
        <ul class="space-y-2 text-sm">
          <li><a href="#" class="hover:text-white">品牌故事</a></li>
          <li><a href="#" class="hover:text-white">人才招募</a></li>
          <li><a href="#" class="hover:text-white">隱私權聲明</a></li>
        </ul>
      </div>
      <div>
        <h3 class="text-white font-bold text-lg mb-4">顧客服務</h3>
        <ul class="space-y-2 text-sm">
          <li><a href="#" class="hover:text-white">退換貨政策</a></li>
          <li><a href="#" class="hover:text-white">運送說明</a></li>
          <li><a href="#" class="hover:text-white">聯絡我們</a></li>
        </ul>
      </div>
      <div>
        <h3 class="text-white font-bold text-lg mb-4">付款方式</h3>
        <div class="flex gap-2 text-2xl">
          <i class="fab fa-cc-visa hover:text-white"></i>
          <i class="fab fa-cc-mastercard hover:text-white"></i>
          <i class="fab fa-cc-jcb hover:text-white"></i>
          <i class="fab fa-apple-pay hover:text-white"></i>
        </div>
      </div>
      <div>
        <h3 class="text-white font-bold text-lg mb-4">追蹤我們</h3>
        <div class="flex gap-4 text-2xl">
          <a href="#" class="hover:text-blue-400"><i class="fab fa-facebook"></i></a>
          <a href="#" class="hover:text-pink-400"><i class="fab fa-instagram"></i></a>
          <a href="#" class="hover:text-green-400"><i class="fab fa-line"></i></a>
        </div>
      </div>
    </div>
    <div class="text-center text-xs mt-10 border-t border-gray-700 pt-6">
      &copy; 2025 Mr. Xie's Convenience Store. All rights reserved.
    </div>
  </footer>
</template>

<script>
export default {
  name: 'AppFooter'
}
</script>

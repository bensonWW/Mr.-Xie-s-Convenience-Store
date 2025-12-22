<template>
  <div class="bg-gray-800 rounded-xl overflow-hidden relative h-64 md:h-80 flex items-center">
    <img src="https://images.unsplash.com/photo-1556911220-e15b29be8c8f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&q=80" alt="Kitchen Appliances" class="absolute w-full h-full object-cover opacity-60">
    <div class="relative z-10 px-8 md:px-16 text-white">
      <span class="bg-xieOrange px-3 py-1 rounded text-sm font-bold mb-2 inline-block">本月強檔</span>
      <h1 class="text-3xl md:text-5xl font-bold mb-4">居家生活節 <br> 全館家電 85 折起</h1>
      <p class="mb-6 text-gray-200">打造夢想廚房，從這一刻開始。指定商品再送清潔組。</p>
      <router-link to="/items" class="bg-white text-xieBlue px-6 py-2 rounded font-bold hover:bg-gray-200 transition">立即搶購</router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: 'HeroBanner'
}
</script>

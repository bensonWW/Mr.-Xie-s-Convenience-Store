<template>
  <div class="home">
    <div class="home-search">
      <input type="text" v-model="search" placeholder="搜尋商品、品牌、分類..." />
      <button @click="doSearch">搜尋</button>
    </div>
    <div class="home-carousel">
      <div class="carousel-img-wrap">
        <transition :name="carouselTransition">
          <div :key="carouselIndex" class="carousel-img-holder">
            <img :src="carouselImages[carouselIndex]" alt="banner" class="carousel-img" />
            <div v-if="isPreloading" class="carousel-loading-overlay">
              <span class="loader"></span>
            </div>
          </div>
        </transition>
      </div>
      <div class="carousel-dots">
        <span v-for="(img, idx) in carouselImages" :key="idx"
              :class="['dot', {active: idx === carouselIndex}]"
              @click="goToCarousel(idx)"></span>
      </div>
    </div>
    <div class="home-brands">
      <img src="https://upload.wikimedia.org/wikipedia/commons/2/24/Samsung_Logo.svg" alt="Samsung" />
      <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Amd_ryzen_logo.svg" alt="AMD" />
      <img src="https://upload.wikimedia.org/wikipedia/commons/0/02/ASUS_Logo.svg" alt="ASUS" />
      <img src="https://upload.wikimedia.org/wikipedia/commons/6/6e/Gigabyte_Technology_logo.svg" alt="Gigabyte" />
      <img src="https://upload.wikimedia.org/wikipedia/commons/6/6a/Corsair_logo.svg" alt="Corsair" />
    </div>
  </div>
</template>

<script>
import './css/light/HomeView.css'

export default {
  name: 'HomeView',
  data () {
    return {
      search: '',
      carouselImages: [
        '/1.jpg',
        '/2.jpg',
        '/3.jpg',
        '/4.jpg'
      ],
      carouselIndex: 0,
      carouselTimer: null,
      isPreloading: false,
      // 'slide-left' or 'slide-right' to control transition direction
      carouselTransition: 'slide-left'
    }
  },
  mounted () {
    this.isPreloading = false
    // 直接啟動輪播，不在首屏預載所有圖片以縮短首屏載入時間
    this.startCarousel()
  },
  beforeUnmount () {
    clearInterval(this.carouselTimer)
  },
  methods: {
    startCarousel () {
      if (this.carouselTimer) clearInterval(this.carouselTimer)
      this.carouselTimer = setInterval(() => {
        this.nextCarousel()
      }, 3500)
    },
    prevCarousel () {
      const prev = (this.carouselIndex - 1 + this.carouselImages.length) % this.carouselImages.length
      this.carouselTransition = 'slide-right'
      this.carouselIndex = prev
      this.startCarousel()
    },
    nextCarousel () {
      const next = (this.carouselIndex + 1) % this.carouselImages.length
      this.carouselTransition = 'slide-left'
      this.preloadImg(next).then(() => {
        this.carouselIndex = next
      })
    },
    goToCarousel (idx) {
      if (idx === this.carouselIndex) return
      // 根據目標索引決定動畫方向：目標在後方則往左滑，否則往右滑
      this.carouselTransition = idx > this.carouselIndex ? 'slide-left' : 'slide-right'
      this.preloadImg(idx).then(() => {
        this.carouselIndex = idx
        // 手動點擊切換時重新啟動計時器，讓下一次自動切換從現在重新計時
        this.startCarousel()
      })
    },
    preloadImg (idx) {
      // return promise resolved when image loaded
      this.isPreloading = true
      return new Promise((resolve) => {
        const img = new window.Image()
        img.src = this.carouselImages[idx]
        img.onload = () => {
          this.isPreloading = false
          resolve()
        }
        img.onerror = () => {
          this.isPreloading = false
          resolve()
        }
      })
    },
    doSearch () {
      if (this.search.trim()) {
        // 這裡可導向搜尋頁或顯示搜尋結果
        alert('搜尋：' + this.search)
      }
    }
  }
}
</script>

<style scoped>
.home {
  margin-top: 2rem;
  text-align: center;
}
.home h1 {
  color: #e67e22;
  margin-bottom: 1rem;
}
.home p {
  font-size: 1.2rem;
  color: #555;
}
</style>


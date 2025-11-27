/* eslint-disable */
<template>
  <div class="items-view">
    <aside class="category-menu">
      <h3>分類</h3>
      <ul>
        <li v-for="cat in categories" :key="cat" :class="{active: cat === selectedCategory}" @click="selectCategory(cat)">{{ cat }}</li>
      </ul>
    </aside>
    <main class="items-main">
      <h2 v-if="$route.query.search">搜尋結果: "{{ $route.query.search }}"</h2>
      <h2 v-else>{{ selectedCategory }}商品</h2>
      <div class="item-grid">
        <router-link class="item-card" v-for="item in filteredItems" :key="item.id" :to="`/items/${item.id}`">
          <img class="item-img" :src="item.img" :alt="item.name" />
          <div class="item-name">{{ item.name }}</div>
          <div class="item-price">${{ item.price.toLocaleString() }}</div>
        </router-link>
      </div>
    </main>
  </div>
</template>

<script>
import api from '@/services/api'

// 根據主題動態引入 CSS
import './css/light/ItemsView.css'
import './css/dark/ItemsView.css'

export default {
  name: 'ItemsView',
  data () {
    return {
      categories: ['Fruit', 'Dairy', '手機', '家電', '美妝', '食品', '日用品', '玩具', '服飾', '書籍'], // Updated with seeded categories
      items: [],
      selectedCategory: 'Fruit'
    }
  },
  created () {
    this.fetchProducts()
  },
  computed: {
    filteredItems () {
      const search = this.$route.query.search
      if (search) {
        const q = search.toLowerCase()
        return this.items.filter(item => item.name.toLowerCase().includes(q))
      }
      return this.items.filter(item => item.category === this.selectedCategory)
    }
  },
  methods: {
    async fetchProducts () {
      try {
        const response = await api.get('/products')
        this.items = response.data.map(item => {
          let imgUrl = ''
          if (item.image) {
            try {
              // Resolve local asset
              imgUrl = new URL(`../assets/${item.image}`, import.meta.url).href
            } catch (e) {
              // Fallback to original string if not a local asset or resolution fails
              imgUrl = item.image
            }
          }
          return {
            ...item,
            img: imgUrl
          }
        })
      } catch (error) {
        console.error('Error fetching products:', error)
      }
    },
    selectCategory (cat) {
      this.selectedCategory = cat
      if (this.$route.query.search) {
        this.$router.push({ path: '/items' })
      }
    }
  }
}
</script>

<!-- 樣式已移至 ItemsView.css -->

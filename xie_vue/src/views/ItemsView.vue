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
      <h2>{{ selectedCategory }}商品</h2>
      <div class="search-bar" style="margin: 1rem 0;">
        <input
          type="text"
          v-model="searchText"
          placeholder="搜尋商品名稱..."
          style="padding: 0.5rem 1rem; border-radius: 6px; border: 1px solid #ccc; width: 100%; max-width: 320px;"
        />
      </div>
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
import items from '@/assets/items.json'

// 根據主題動態引入 CSS
import './css/light/ItemsView.css'
import './css/dark/ItemsView.css'

// 以下為從資料庫撈資料的範例框架（需後端 API 支援）
/*
import axios from 'axios'

export default {
  name: 'ItemsView',
  data () {
    return {
      categories: [],
      items: [],
      selectedCategory: ''
    }
  },
  created() {
    // 取得分類
    axios.get('/api/categories').then(res => {
      this.categories = res.data
      this.selectedCategory = this.categories[0] || ''
    })
    // 取得商品
    axios.get('/api/items').then(res => {
      this.items = res.data
    })
  },
  computed: {
    filteredItems() {
      return this.items.filter(item => item.category === this.selectedCategory)
    }
  },
  methods: {
    selectCategory(cat) {
      this.selectedCategory = cat
    }
  }
}
*/
export default {
  name: 'ItemsView',
  data () {
    return {
      categories: ['手機', '家電', '美妝', '食品', '日用品', '玩具', '服飾', '書籍'],
      items,
      selectedCategory: '手機',
      searchText: ''
    }
  },
  computed: {
    filteredItems () {
      // 搜尋時忽略分類，否則依分類
      return this.items.filter(item => {
        const matchName = this.searchText
          ? item.name && item.name.toLowerCase().includes(this.searchText.toLowerCase())
          : true
        if (this.searchText) {
          return matchName
        } else {
          const matchCategory = Array.isArray(item.category)
            ? item.category.includes(this.selectedCategory)
            : item.category === this.selectedCategory
          return matchCategory && matchName
        }
      })
    }
  },
  methods: {
    selectCategory (cat) {
      this.selectedCategory = cat
    }
  }
}
</script>

<!-- 樣式已移至 ItemsView.css -->

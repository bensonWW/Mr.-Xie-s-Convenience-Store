<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800 dark:text-stone-100">商品管理</h2>
      <button class="bg-xieOrange text-white px-4 py-2 rounded-lg font-bold hover:bg-orange-600 transition shadow-sm flex items-center gap-2" @click="$router.push('/admin/products/new')">
        <i class="fas fa-plus"></i> 新增商品
      </button>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm overflow-hidden transition-colors duration-300">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50 dark:bg-slate-900 border-b border-gray-100 dark:border-slate-700 text-gray-600 dark:text-stone-400 text-sm uppercase tracking-wider">
              <th class="p-4 font-bold">ID</th>
              <th class="p-4 font-bold">圖片</th>
              <th class="p-4 font-bold">名稱</th>
              <th class="p-4 font-bold">價格</th>
              <th class="p-4 font-bold">分類</th>
              <th class="p-4 font-bold">庫存</th>
              <th class="p-4 font-bold text-right">操作</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
            <tr v-if="products.length === 0">
              <td colspan="7" class="p-8 text-center text-gray-400 dark:text-stone-500">暫無商品資料</td>
            </tr>
            <tr v-for="prod in products" :key="prod.id" class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
              <td class="p-4 text-gray-500 dark:text-stone-400">#{{ prod.id }}</td>
              <td class="p-4">
                <div class="w-12 h-12 bg-gray-100 dark:bg-slate-700 rounded overflow-hidden border border-gray-200 dark:border-slate-600">
                   <img v-if="prod.image" :src="getImageUrl(prod.image)" class="w-full h-full object-cover">
                   <div v-else class="w-full h-full flex items-center justify-center text-gray-300 dark:text-slate-500"><i class="fas fa-image"></i></div>
                </div>
              </td>
              <td class="p-4 font-bold text-gray-800 dark:text-stone-100">{{ prod.name }}</td>
              <td class="p-4 text-xieOrange font-bold">{{ formatPrice(prod.price) }}</td>
              <td class="p-4 text-gray-600 dark:text-stone-300"><span class="bg-gray-100 dark:bg-slate-700 px-2 py-1 rounded text-xs">{{ getCategoryName(prod.category) }}</span></td>
              <td class="p-4">
                <span :class="prod.stock < 10 ? 'text-red-500 dark:text-red-400 font-bold' : 'text-green-600 dark:text-emerald-400'">{{ prod.stock }}</span>
              </td>
              <td class="p-4 text-right space-x-2">
                <button class="text-blue-500 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition" @click="$router.push(`/admin/products/${prod.id}/edit`)">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition" @click="deleteProduct(prod.id)">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      </div>

       <!-- Pagination Controls -->
      <div class="px-6 py-4 border-t border-gray-100 dark:border-slate-700 flex items-center justify-between bg-white dark:bg-slate-800" v-if="totalPages > 1">
        <span class="text-sm text-gray-500 dark:text-stone-400">
          顯示第 {{ (currentPage - 1) * 20 + 1 }} 到 {{ Math.min(currentPage * 20, totalItems) }} 筆，共 {{ totalItems }} 筆
        </span>
        <div class="flex gap-2">
          <button
            @click="fetchProducts(currentPage - 1)"
            :disabled="currentPage === 1"
            class="px-3 py-1 border dark:border-slate-600 rounded hover:bg-gray-50 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed text-gray-600 dark:text-stone-300 bg-white dark:bg-slate-800"
          >
            上一頁
          </button>
          <span class="px-3 py-1 bg-xieOrange text-white rounded">{{ currentPage }}</span>
          <button
            @click="fetchProducts(currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="px-3 py-1 border dark:border-slate-600 rounded hover:bg-gray-50 dark:hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed text-gray-600 dark:text-stone-300 bg-white dark:bg-slate-800"
          >
            下一頁
          </button>
        </div>
      </div>
    </div>
</template>

<script>
import api from '../services/api'
import { formatPrice } from '../utils/currency'

export default {
  name: 'AdminProducts',
  data () {
    return {
      products: [],
      currentPage: 1,
      totalPages: 1,
      totalItems: 0
    }
  },
  created () {
    this.fetchProducts()
  },
  methods: {
    formatPrice,
    async fetchProducts (page = 1) {
      try {
        const res = await api.get(`/admin/products?page=${page}`)
        this.products = res.data.data
        this.currentPage = res.data.current_page
        this.totalPages = res.data.last_page
        this.totalItems = res.data.total
      } catch (e) {
        console.error(e)
      }
    },
    async deleteProduct (id) {
      if (!confirm('確定刪除此商品？此操作無法復原。')) return
      try {
        await api.delete(`/admin/products/${id}`)
        this.fetchProducts()
        this.$toast.success('商品已刪除')
      } catch (e) {
        this.$toast.error('刪除失敗')
      }
    },
    getImageUrl (image) {
      if (!image) return ''
      if (image.startsWith('http')) return image
      // Get backend base URL from api config
      const apiBaseUrl = api.defaults.baseURL || ''
      const baseUrl = apiBaseUrl.replace('/api', '')
      // Laravel stores images at storage/app/public, accessible via /storage symlink
      if (image.startsWith('storage/') || image.startsWith('/storage/')) {
        return baseUrl + '/' + image.replace(/^\//, '')
      }
      return baseUrl + '/storage/' + image
    },
    getCategoryName (cat) {
      if (!cat) return ''
      if (typeof cat === 'string') return cat
      return cat.name || cat.slug || String(cat)
    }
  }
}
</script>

<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">商品管理</h2>
      <button class="bg-xieOrange text-white px-4 py-2 rounded-lg font-bold hover:bg-orange-600 transition shadow-sm flex items-center gap-2" @click="$router.push('/admin/products/new')">
        <i class="fas fa-plus"></i> 新增商品
      </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-100 text-gray-600 text-sm uppercase tracking-wider">
              <th class="p-4 font-bold">ID</th>
              <th class="p-4 font-bold">圖片</th>
              <th class="p-4 font-bold">名稱</th>
              <th class="p-4 font-bold">價格</th>
              <th class="p-4 font-bold">分類</th>
              <th class="p-4 font-bold">庫存</th>
              <th class="p-4 font-bold text-right">操作</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-if="products.length === 0">
              <td colspan="7" class="p-8 text-center text-gray-400">暫無商品資料</td>
            </tr>
            <tr v-for="prod in products" :key="prod.id" class="hover:bg-gray-50 transition">
              <td class="p-4 text-gray-500">#{{ prod.id }}</td>
              <td class="p-4">
                <div class="w-12 h-12 bg-gray-100 rounded overflow-hidden border border-gray-200">
                   <img v-if="prod.image" :src="getImageUrl(prod.image)" class="w-full h-full object-cover">
                   <div v-else class="w-full h-full flex items-center justify-center text-gray-300"><i class="fas fa-image"></i></div>
                </div>
              </td>
              <td class="p-4 font-bold text-gray-800">{{ prod.name }}</td>
              <td class="p-4 text-xieOrange font-bold">${{ prod.price }}</td>
              <td class="p-4 text-gray-600"><span class="bg-gray-100 px-2 py-1 rounded text-xs">{{ prod.category }}</span></td>
              <td class="p-4">
                <span :class="prod.stock < 10 ? 'text-red-500 font-bold' : 'text-green-600'">{{ prod.stock }}</span>
              </td>
              <td class="p-4 text-right space-x-2">
                <button class="text-blue-500 hover:text-blue-700 transition" @click="$router.push(`/admin/products/${prod.id}/edit`)">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="text-red-500 hover:text-red-700 transition" @click="deleteProduct(prod.id)">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api'

export default {
  name: 'AdminProducts',
  data () {
    return {
      products: []
    }
  },
  created () {
    this.fetchProducts()
  },
  methods: {
    async fetchProducts () {
      try {
        const res = await api.get('/products')
        this.products = res.data
      } catch (e) {
        console.error(e)
      }
    },
    async deleteProduct (id) {
      if (!confirm('確定刪除此商品？此操作無法復原。')) return
      try {
        await api.delete(`/admin/products/${id}`)
        this.fetchProducts()
        alert('商品已刪除')
      } catch (e) {
        alert('刪除失敗')
      }
    },
    getImageUrl (image) {
      if (!image) return ''
      if (image.startsWith('http')) return image
      return '/images/' + image
    }
  }
}
</script>

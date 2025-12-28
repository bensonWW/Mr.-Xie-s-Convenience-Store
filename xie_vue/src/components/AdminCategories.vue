<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800 dark:text-stone-100">
        <i class="fas fa-tags text-xieOrange mr-2"></i>分類管理
      </h1>
      <button @click="showAddModal = true" class="bg-xieOrange text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition flex items-center gap-2">
        <i class="fas fa-plus"></i> 新增分類
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-12">
      <i class="fas fa-spinner fa-spin text-3xl text-xieOrange"></i>
      <p class="mt-2 text-gray-500 dark:text-stone-400">載入中...</p>
    </div>

    <!-- Categories Table -->
    <div v-else class="bg-white dark:bg-slate-800 rounded-lg shadow-sm overflow-hidden transition-colors duration-300">
      <table class="w-full">
        <thead class="bg-gray-50 dark:bg-slate-900 text-gray-600 dark:text-stone-400 text-sm uppercase">
          <tr>
            <th class="p-4 text-left">ID</th>
            <th class="p-4 text-left">分類名稱</th>
            <th class="p-4 text-left">代碼 (Slug)</th>
            <th class="p-4 text-left">商品數量</th>
            <th class="p-4 text-left">描述</th>
            <th class="p-4 text-center">操作</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
          <tr v-for="cat in categories" :key="cat.id" class="hover:bg-gray-50 dark:hover:bg-slate-700/50">
            <td class="p-4 text-gray-500 dark:text-stone-400">{{ cat.id }}</td>
            <td class="p-4 font-medium text-gray-800 dark:text-stone-100">{{ cat.name }}</td>
            <td class="p-4 text-gray-500 dark:text-stone-400 font-mono text-sm">{{ cat.slug }}</td>
            <td class="p-4">
              <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400 px-2 py-1 rounded text-sm">{{ cat.products_count }} 件</span>
            </td>
            <td class="p-4 text-gray-500 dark:text-stone-400 text-sm max-w-xs truncate">{{ cat.description || '-' }}</td>
            <td class="p-4 text-center">
              <button @click="editCategory(cat)" class="text-blue-500 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 mr-3" title="編輯">
                <i class="fas fa-edit"></i>
              </button>
              <button @click="confirmDelete(cat)" class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300" title="刪除">
                <i class="fas fa-trash"></i>
              </button>
            </td>
          </tr>
          <tr v-if="categories.length === 0">
            <td colspan="6" class="p-8 text-center text-gray-500 dark:text-stone-400">
              <i class="fas fa-folder-open text-4xl mb-2"></i>
              <p>尚無分類資料</p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showAddModal || showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md p-6 transition-colors duration-300">
        <h3 class="text-xl font-bold text-gray-800 dark:text-stone-100 mb-4">
          <i class="fas fa-tag text-xieOrange mr-2"></i>
          {{ showEditModal ? '編輯分類' : '新增分類' }}
        </h3>
        <form @submit.prevent="showEditModal ? updateCategory() : createCategory()">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-stone-200 mb-1">分類名稱 *</label>
            <input v-model="form.name" type="text" required class="w-full border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-xieOrange bg-white dark:bg-slate-700 text-gray-800 dark:text-stone-100" placeholder="例如：飲料">
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-stone-200 mb-1">描述</label>
            <textarea v-model="form.description" rows="3" class="w-full border border-gray-300 dark:border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-xieOrange bg-white dark:bg-slate-700 text-gray-800 dark:text-stone-100" placeholder="分類說明（選填）"></textarea>
          </div>
          <div class="flex justify-end gap-2">
            <button type="button" @click="closeModal" class="px-4 py-2 text-gray-500 dark:text-stone-400 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg">取消</button>
            <button type="submit" :disabled="saving" class="px-6 py-2 bg-xieOrange text-white font-bold rounded-lg hover:bg-orange-600 transition disabled:opacity-50">
              <i v-if="saving" class="fas fa-spinner fa-spin mr-1"></i>
              {{ showEditModal ? '儲存變更' : '新增分類' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md p-6 transition-colors duration-300">
        <h3 class="text-xl font-bold text-red-600 dark:text-red-400 mb-4">
          <i class="fas fa-exclamation-triangle mr-2"></i>確認刪除
        </h3>
        <p class="text-gray-600 dark:text-stone-300 mb-2">
          您確定要刪除分類「<strong>{{ deleteTarget?.name }}</strong>」嗎？
        </p>
        <p v-if="deleteTarget?.products_count > 0" class="text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-900/20 p-3 rounded mb-4">
          <i class="fas fa-info-circle mr-1"></i>
          此分類下有 <strong>{{ deleteTarget.products_count }}</strong> 件商品，刪除後這些商品將變成「未分類」。
        </p>

        <!-- Reassign Option -->
        <div v-if="deleteTarget?.products_count > 0" class="mb-4">
          <label class="flex items-center mb-2">
            <input type="checkbox" v-model="reassignProducts" class="mr-2">
            <span class="text-sm text-gray-700 dark:text-stone-300">將商品轉移到其他分類</span>
          </label>
          <select v-if="reassignProducts" v-model="targetCategoryId" class="w-full border border-gray-300 dark:border-slate-600 rounded px-3 py-2 bg-white dark:bg-slate-700 text-gray-800 dark:text-stone-100">
            <option value="">選擇目標分類</option>
            <option v-for="cat in categories.filter(c => c.id !== deleteTarget?.id)" :key="cat.id" :value="cat.id">
              {{ cat.name }} ({{ cat.products_count }} 件)
            </option>
          </select>
        </div>

        <div class="flex justify-end gap-2">
          <button @click="showDeleteModal = false" class="px-4 py-2 text-gray-500 dark:text-stone-400 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg">取消</button>
          <button @click="deleteCategory" :disabled="deleting || (reassignProducts && !targetCategoryId)" class="px-6 py-2 bg-red-500 text-white font-bold rounded-lg hover:bg-red-600 transition disabled:opacity-50">
            <i v-if="deleting" class="fas fa-spinner fa-spin mr-1"></i>
            確認刪除
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api'

export default {
  name: 'AdminCategories',
  data () {
    return {
      categories: [],
      loading: true,
      saving: false,
      deleting: false,
      showAddModal: false,
      showEditModal: false,
      showDeleteModal: false,
      editingId: null,
      deleteTarget: null,
      reassignProducts: false,
      targetCategoryId: '',
      form: {
        name: '',
        description: ''
      }
    }
  },
  mounted () {
    this.loadCategories()
  },
  methods: {
    async loadCategories () {
      this.loading = true
      try {
        const res = await api.get('/admin/categories')
        this.categories = res.data
      } catch (error) {
        alert('載入分類失敗')
      } finally {
        this.loading = false
      }
    },
    async createCategory () {
      this.saving = true
      try {
        await api.post('/admin/categories', this.form)
        this.closeModal()
        this.loadCategories()
      } catch (error) {
        alert(error.response?.data?.message || '新增失敗')
      } finally {
        this.saving = false
      }
    },
    editCategory (cat) {
      this.editingId = cat.id
      this.form.name = cat.name
      this.form.description = cat.description || ''
      this.showEditModal = true
    },
    async updateCategory () {
      this.saving = true
      try {
        await api.put(`/admin/categories/${this.editingId}`, this.form)
        this.closeModal()
        this.loadCategories()
      } catch (error) {
        alert(error.response?.data?.message || '更新失敗')
      } finally {
        this.saving = false
      }
    },
    confirmDelete (cat) {
      this.deleteTarget = cat
      this.reassignProducts = false
      this.targetCategoryId = ''
      this.showDeleteModal = true
    },
    async deleteCategory () {
      this.deleting = true
      try {
        if (this.reassignProducts && this.targetCategoryId) {
          await api.post(`/admin/categories/${this.deleteTarget.id}/reassign`, {
            target_category_id: this.targetCategoryId
          })
        } else {
          await api.delete(`/admin/categories/${this.deleteTarget.id}`)
        }
        this.showDeleteModal = false
        this.loadCategories()
      } catch (error) {
        alert(error.response?.data?.message || '刪除失敗')
      } finally {
        this.deleting = false
      }
    },
    closeModal () {
      this.showAddModal = false
      this.showEditModal = false
      this.editingId = null
      this.form = { name: '', description: '' }
    }
  }
}
</script>

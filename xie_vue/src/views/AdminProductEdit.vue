<template>
  <div class="admin-product-edit">
    <div class="header">
      <h2>{{ isEdit ? '編輯商品' : '新增商品' }}</h2>
      <button class="outline-btn" @click="$router.push('/admin')">返回列表</button>
    </div>

    <div class="form-container">
      <div class="form-group">
        <label>商品名稱</label>
        <input v-model="form.name" type="text" placeholder="請輸入商品名稱">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>價格</label>
          <input v-model="form.price" type="number" min="0">
        </div>
        <div class="form-group">
          <label>庫存</label>
          <input v-model="form.stock" type="number" min="0">
        </div>
      </div>

      <div class="form-group">
        <label>分類</label>
        <input v-model="form.category" type="text" placeholder="例如：飲料、零食">
      </div>

      <div class="form-group">
        <label>商品描述 (Information)</label>
        <textarea v-model="form.information" rows="5" placeholder="請輸入商品詳細資訊"></textarea>
      </div>

      <div class="form-group">
        <label>商品圖片</label>
        <div v-if="previewImage" class="image-preview">
          <img :src="previewImage" alt="Preview">
        </div>
        <input type="file" @change="handleFileUpload" accept="image/*">
        <p class="hint">若不選擇新圖片，將維持原樣。</p>
      </div>

      <div class="actions">
        <button class="primary-btn" @click="saveProduct" :disabled="loading">
          {{ loading ? '儲存中...' : '儲存商品' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api'

export default {
  name: 'AdminProductEdit',
  data () {
    return {
      loading: false,
      form: {
        name: '',
        price: 0,
        stock: 0,
        category: '',
        information: '',
        image: null
      },
      previewImage: null,
      categories: []
    }
  },
  computed: {
    isEdit () {
      return !!this.$route.params.id
    }
  },
  created () {
    this.fetchCategories()
    if (this.isEdit) {
      this.fetchProduct(this.$route.params.id)
    }
  },
  methods: {
    async fetchCategories () {
      try {
        const res = await api.get('/categories')
        this.categories = res.data
      } catch (e) {
        console.error(e)
      }
    },
    async fetchProduct (id) {
      try {
        const res = await api.get(`/products/${id}`)
        const prod = res.data
        this.form = {
          name: prod.name,
          price: prod.price,
          stock: prod.stock,
          category: prod.category,
          information: prod.information,
          image: null // Don't set file object, just keep null unless changed
        }
        if (prod.image) {
          if (prod.image.startsWith('http')) {
            this.previewImage = prod.image
          } else {
            this.previewImage = '/images/' + prod.image
          }
        }
      } catch (e) {
        console.error(e)
        alert('無法載入商品資料')
        this.$router.push('/admin')
      }
    },
    handleFileUpload (e) {
      const file = e.target.files[0]
      if (file) {
        if (file.size > 2 * 1024 * 1024) {
          alert('圖片大小請勿超過 2MB')
          e.target.value = ''
          return
        }
        this.form.image = file
        const reader = new FileReader()
        reader.onload = (e) => {
          this.previewImage = e.target.result
        }
        reader.readAsDataURL(file)
      }
    },
    async saveProduct () {
      if (!this.form.name || !this.form.price) {
        alert('請填寫必要欄位')
        return
      }

      // Check for new category
      if (this.form.category && !this.categories.includes(this.form.category)) {
        if (!confirm(`"${this.form.category}" 是一個新的分類，確定要新增嗎？`)) {
          return
        }
      }

      this.loading = true
      try {
        const formData = new FormData()
        formData.append('name', this.form.name)
        formData.append('price', this.form.price)
        formData.append('stock', this.form.stock)
        formData.append('category', this.form.category)
        formData.append('information', this.form.information || '')

        if (this.form.image) {
          formData.append('image', this.form.image)
        }

        const token = localStorage.getItem('token')
        if (!token) {
          alert('請先登入')
          this.$router.push('/profile')
          return
        }

        const config = {
          headers: {
            Authorization: `Bearer ${token}`
          }
        }

        if (this.isEdit) {
          formData.append('_method', 'PUT')
          await api.post(`/admin/products/${this.$route.params.id}`, formData, config)
        } else {
          await api.post('/admin/products', formData, config)
        }

        alert('儲存成功')
        this.$router.push('/admin')
      } catch (e) {
        console.error(e)
        const msg = e.response?.data?.message || '儲存失敗'
        alert('錯誤: ' + msg)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.admin-product-edit {
  max-width: 800px;
  margin: 2rem auto;
  padding: 0 1rem;
}
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}
.form-container {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.form-group {
  margin-bottom: 1.5rem;
}
.form-row {
  display: flex;
  gap: 1rem;
}
.form-row .form-group {
  flex: 1;
}
label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: bold;
  color: #333;
}
input[type="text"],
input[type="number"],
textarea {
  width: 100%;
  padding: 0.8rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}
textarea {
  resize: vertical;
}
.image-preview {
  margin-bottom: 1rem;
  border: 1px solid #eee;
  padding: 0.5rem;
  border-radius: 4px;
  width: fit-content;
}
.image-preview img {
  max-width: 200px;
  max-height: 200px;
  object-fit: cover;
}
.hint {
  font-size: 0.85rem;
  color: #888;
  margin-top: 0.3rem;
}
.actions {
  margin-top: 2rem;
  text-align: right;
}
.primary-btn {
  background: #e67e22;
  color: white;
  border: none;
  padding: 0.8rem 2rem;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
}
.primary-btn:disabled {
  background: #fab1a0;
  cursor: not-allowed;
}
.outline-btn {
  background: transparent;
  border: 1px solid #ccc;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
}
</style>

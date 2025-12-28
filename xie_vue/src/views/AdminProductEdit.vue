<template>
  <div class="flex h-screen overflow-hidden bg-gray-100 font-sans text-gray-700">

    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md flex-shrink-0 hidden md:flex flex-col z-20">
      <div class="h-16 flex items-center justify-center border-b border-gray-100 bg-xieBlue text-white font-bold text-xl tracking-wider">
        MR. XIE ADMIN
      </div>

      <nav class="flex-1 overflow-y-auto py-4">
        <ul class="space-y-1">
          <li>
            <router-link to="/admin/dashboard" class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-50 hover-text-xieOrange transition">
              <i class="fas fa-chart-pie w-6"></i><span class="font-bold">總覽儀表板</span>
            </router-link>
          </li>

          <li class="px-6 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider mt-4">商店管理</li>
          <li>
            <router-link to="/admin/products" class="flex items-center px-6 py-3" :class="$route.path.includes('/products') ? 'text-xieOrange bg-orange-50 border-r-4 border-xieOrange' : 'text-gray-600 hover:bg-gray-50 hover-text-xieOrange'">
              <i class="fas fa-box w-6"></i><span>商品管理</span>
            </router-link>
          </li>
          <li>
            <router-link to="/admin/orders" class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-50 hover-text-xieOrange transition">
              <i class="fas fa-file-invoice-dollar w-6"></i><span>訂單管理</span>
            </router-link>
          </li>
          <li>
            <router-link to="/admin/coupons" class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-50 hover-text-xieOrange transition">
              <i class="fas fa-ticket-alt w-6"></i><span>優惠券管理</span>
            </router-link>
          </li>

          <li class="px-6 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider mt-4">顧客與數據</li>
          <li>
            <router-link to="/admin/users" class="flex items-center px-6 py-3 text-gray-600 hover:bg-gray-50 hover-text-xieOrange transition">
              <i class="fas fa-users w-6"></i><span>會員管理</span>
            </router-link>
          </li>
        </ul>
      </nav>
      <div class="p-4 border-t border-gray-100">
        <button @click="logout" class="flex items-center gap-3 text-sm text-gray-500 hover:text-red-500 w-full text-left">
          <i class="fas fa-sign-out-alt"></i> 登出
        </button>
      </div>
    </aside>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">

      <!-- Header -->
      <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6 z-10 shrink-0">
        <div class="flex items-center gap-4">
          <button class="md:hidden text-gray-500"><i class="fas fa-bars text-xl"></i></button>
          <div class="text-lg font-bold text-gray-800">{{ isEdit ? '編輯商品' : '新增商品' }}</div>
        </div>
        <div class="flex items-center gap-4">
             <button @click="$router.push('/admin/products')" class="text-gray-500 hover:text-red-500 font-bold px-4 py-2 rounded transition">取消</button>
             <button @click="saveProduct" :disabled="loading" class="bg-xieOrange text-white px-6 py-2 rounded font-bold hover:bg-orange-600 shadow-md transition flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-save'"></i> {{ loading ? '儲存中...' : '儲存商品' }}
             </button>
        </div>
      </header>

      <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">

        <form @submit.prevent="saveProduct" class="max-w-6xl mx-auto">
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 space-y-6">

              <!-- Basic Info -->
              <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">基本資訊</h3>

                <div class="mb-4">
                  <label class="block text-sm font-bold text-gray-700 mb-2">商品名稱</label>
                  <input v-model="form.name" type="text" placeholder="例如：Dyson V12 無線吸塵器" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus-border-xieOrange focus:ring-1 focus-ring-xieOrange transition">
                </div>

                <div>
                  <label class="block text-sm font-bold text-gray-700 mb-2">商品描述</label>
                  <div class="border border-gray-300 rounded-t bg-gray-50 px-3 py-2 flex gap-3 text-gray-500 text-sm">
                    <button type="button" class="hover:text-black"><i class="fas fa-bold"></i></button>
                    <button type="button" class="hover:text-black"><i class="fas fa-italic"></i></button>
                    <button type="button" class="hover:text-black"><i class="fas fa-underline"></i></button>
                    <span class="border-r border-gray-300 mx-1"></span>
                    <button type="button" class="hover:text-black"><i class="fas fa-list-ul"></i></button>
                    <button type="button" class="hover:text-black"><i class="fas fa-list-ol"></i></button>
                    <span class="border-r border-gray-300 mx-1"></span>
                    <button type="button" class="hover:text-black"><i class="far fa-image"></i></button>
                  </div>
                  <textarea v-model="form.information" rows="6" class="w-full border border-gray-300 border-t-0 rounded-b px-4 py-2 focus:outline-none focus-border-xieOrange focus:ring-1 focus-ring-xieOrange transition" placeholder="請輸入詳細的商品介紹..."></textarea>
                </div>
              </div>

              <!-- Image Upload -->
              <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">商品圖片</h3>

                <div @click="$refs.fileInput.click()" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-xieOrange hover-bg-orange-50 transition cursor-pointer group">
                  <div class="text-gray-400 group-hover-text-xieOrange text-4xl mb-3">
                    <i class="fas fa-cloud-upload-alt"></i>
                  </div>
                  <p class="text-sm text-gray-600 font-bold">點擊上傳 或 拖曳圖片至此</p>
                  <p class="text-xs text-gray-400 mt-1">支援 JPG, PNG, WEBP (最大 5MB)</p>
                  <input ref="fileInput" type="file" class="hidden" @change="handleFileUpload" accept="image/*">
                </div>

                <div v-if="previewImage" class="flex gap-4 mt-4">
                  <div class="relative w-24 h-24 border rounded overflow-hidden group">
                    <img :src="previewImage" class="w-full h-full object-cover">
                    <!-- <button type="button" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition"><i class="fas fa-times"></i></button> -->
                    <p class="text-xs text-center bg-gray-100 py-1">預覽</p>
                  </div>
                </div>
              </div>

              <!-- Price & Stock -->
              <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">價格與庫存</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                  <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">銷售價格 (整數)</label>
                    <div class="relative">
                      <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                      <input v-model.number="form.price" type="number" step="1" min="0" placeholder="0" @keydown="preventDecimal" class="w-full pl-7 border border-gray-300 rounded px-4 py-2 focus:outline-none focus-border-xieOrange">
                    </div>
                  </div>
                  <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">原價 (整數)</label>
                    <div class="relative">
                      <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                      <input v-model.number="form.original_price" type="number" step="1" min="0" placeholder="0" @keydown="preventDecimal" class="w-full pl-7 border border-gray-300 rounded px-4 py-2 focus:outline-none focus-border-xieOrange">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">若填寫此欄位，前台將顯示刪除線價格</p>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                  <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">庫存數量</label>
                    <input v-model="form.stock" type="number" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus-border-xieOrange">
                  </div>
                  <!--
                  <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">商品貨號 (SKU)</label>
                    <input v-model="form.sku" type="text" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus-border-xieOrange">
                  </div>
                  <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">條碼 (Barcode)</label>
                    <input v-model="form.barcode" type="text" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus-border-xieOrange">
                  </div>
                  -->
                </div>
              </div>

            </div>

            <!-- Sidebar Info -->
            <div class="space-y-6">

              <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4 text-sm uppercase tracking-wide">商品狀態</h3>
                <select v-model="form.status" class="w-full border border-gray-300 rounded px-4 py-2 mb-4 focus:outline-none focus-border-xieOrange">
                  <option value="active">上架中 (Active)</option>
                  <option value="draft">草稿 (Draft)</option>
                  <option value="archived">已封存 (Archived)</option>
                </select>

                <div>
                  <label class="block text-xs font-bold text-gray-500 mb-1">預計上架時間</label>
                  <input type="date" class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:outline-none focus-border-xieOrange">
                </div>
              </div>

              <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4 text-sm uppercase tracking-wide">分類與組織</h3>

                <div class="mb-4">
                  <label class="block text-sm font-bold text-gray-700 mb-2">商品分類</label>
                  <select v-model="form.category" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus-border-xieOrange">
                    <option value="">選擇分類...</option>
                    <option v-for="cat in validCategories" :key="cat.id" :value="cat.name">{{ cat.name }}</option>
                  </select>
                </div>

                <!-- Optional Fields Section (Toggleable or collapsed by default?) -->
                <!-- For simplicity as requested, we hide non-essential fields. -->

                <!--
                <div class="mb-4">
                  <label class="block text-sm font-bold text-gray-700 mb-2">品牌 / 供應商</label>
                  <input v-model="form.brand" type="text" placeholder="例如：Apple, Dyson" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus-border-xieOrange">
                </div>

                <div>
                  <label class="block text-sm font-bold text-gray-700 mb-2">標籤 (Tags)</label>
                  <input v-model="tagInput" @keydown.enter.prevent="addTag" type="text" placeholder="輸入後按 Enter" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus-border-xieOrange">
                  <div class="flex flex-wrap gap-2 mt-2">
                    <span v-for="(tag, index) in form.tags" :key="index" class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded flex items-center">
                      {{ tag }} <button type="button" class="ml-1 hover:text-red-500" @click="removeTag(index)">&times;</button>
                    </span>
                  </div>
                </div>
                -->
              </div>

            </div>
          </div>
        </form>

      </main>
    </div>

  </div>
</template>

<script>
import api from '../services/api'
import { useToast } from 'vue-toastification'

export default {
  name: 'AdminProductEdit',
  setup () {
    const toast = useToast()
    return { toast }
  },
  data () {
    return {
      loading: false,
      form: {
        name: '',
        price: 0,
        original_price: null,
        stock: 0,
        category: '',
        information: '',
        image: null,
        status: 'draft',
        sku: '',
        barcode: '',
        brand: '',
        tags: []
      },
      tagInput: '',
      previewImage: null,
      categories: []
    }
  },
  computed: {
    isEdit () {
      return !!this.$route.params.id
    },
    validCategories () {
      return this.categories
        .map((cat, index) => {
          if (typeof cat === 'string') {
            return { id: index, name: cat }
          }
          let name = cat.name
          if (typeof name === 'object' && name !== null) {
            name = name.name || name.label || ''
          }
          return { id: cat.id || index, name: name || '' }
        })
        .filter(cat => cat.name && typeof cat.name === 'string' && !cat.name.includes('[object'))
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
        
        // Handle category - could be object or string
        let categoryName = ''
        if (prod.category) {
          if (typeof prod.category === 'object') {
            categoryName = prod.category.name || ''
          } else {
            categoryName = prod.category
          }
        }
        
        this.form = {
          name: prod.name,
          price: prod.price, // Already integer, no conversion needed
          original_price: prod.original_price || null, // Already integer
          stock: prod.stock,
          category: categoryName,
          category_id: prod.category_id || (prod.category?.id),
          information: prod.information,
          image: null,
          status: prod.status || 'active',
          sku: prod.sku || '',
          barcode: prod.barcode || '',
          brand: prod.brand || '',
          tags: prod.tags ? JSON.parse(prod.tags) : [] // Assuming tags might be JSON
        }

        if (prod.image) {
          if (prod.image.startsWith('http')) {
            this.previewImage = prod.image
          } else {
            // Adjust path based on your storage
            this.previewImage = '/storage/' + prod.image // Usually Laravel uses storage link
            // Fallback for demo
            if (!prod.image.includes('/')) {
              // If it's just filename
              this.previewImage = '/images/' + prod.image // Or whatever your base is
            }
            // For safety in this environment where I don't know exact public path mapping:
            if (!this.previewImage.startsWith('/') && !this.previewImage.startsWith('http')) {
              this.previewImage = '/' + prod.image
            }
          }
        }
      } catch (e) {
        console.error(e)
        this.toast.error('無法載入商品資料')
        this.$router.push('/admin/products')
      }
    },
    handleFileUpload (e) {
      const file = e.target.files[0]
      if (file) {
        if (file.size > 5 * 1024 * 1024) {
          this.toast.warning('圖片大小請勿超過 5MB')
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
    addTag () {
      const val = this.tagInput.trim()
      if (val && !this.form.tags.includes(val)) {
        this.form.tags.push(val)
      }
      this.tagInput = ''
    },
    removeTag (index) {
      this.form.tags.splice(index, 1)
    },
    preventDecimal (e) {
      // Block decimal point and comma input
      if (e.key === '.' || e.key === ',') {
        e.preventDefault()
      }
    },
    logout () {
      localStorage.removeItem('token')
      localStorage.removeItem('user_role')
      this.$router.push('/profile')
    },
    async saveProduct () {
      if (!this.form.name || !this.form.price) {
        this.toast.warning('請填寫必要欄位')
        return
      }

      this.loading = true
      try {
        const formData = new FormData()
        formData.append('name', this.form.name)
        // Price sent as raw float (Dollars), backend handles conversion to Cents
        formData.append('price', this.form.price)
        if (this.form.original_price) formData.append('original_price', this.form.original_price)
        formData.append('stock', this.form.stock)
        formData.append('category', this.form.category)
        formData.append('information', this.form.information || '')
        formData.append('status', this.form.status)
        // Add extra fields even if backend might ignore them currently
        formData.append('sku', this.form.sku)
        formData.append('barcode', this.form.barcode)
        formData.append('brand', this.form.brand)
        // Tags as JSON string
        formData.append('tags', JSON.stringify(this.form.tags))

        if (this.form.image) {
          formData.append('image', this.form.image)
        }

        const config = {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        }

        if (this.isEdit) {
          formData.append('_method', 'PUT') // Method spoofing for Laravel FormData
          await api.post(`/admin/products/${this.$route.params.id}`, formData, config)
        } else {
          await api.post('/admin/products', formData, config)
        }

        this.toast.success('儲存成功')
        this.$router.push('/admin/products')
      } catch (e) {
        console.error(e)
        const msg = e.response?.data?.message || '儲存失敗'
        this.toast.error('錯誤: ' + msg)
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
/* Scoped styles for custom colors if not in tailwind config */
.bg-xieBlue { background-color: #2d3748; }
.text-xieBlue { color: #2d3748; }
.bg-xieOrange { background-color: #ed8936; }
.text-xieOrange { color: #ed8936; }
.border-xieOrange { border-color: #ed8936; }
.focus-border-xieOrange:focus { border-color: #ed8936; }
.focus-ring-xieOrange:focus { --tw-ring-color: #ed8936; }
.bg-orange-50 { background-color: #fffaf0; }
.hover-bg-orange-50:hover { background-color: #fffaf0; }
.hover-text-xieOrange:hover { color: #ed8936; }

/* Custom scrollbar for sidebar */
nav::-webkit-scrollbar {
  width: 4px;
}
nav::-webkit-scrollbar-track {
  background: transparent;
}
nav::-webkit-scrollbar-thumb {
  background-color: rgba(156, 163, 175, 0.5);
  border-radius: 20px;
}
</style>

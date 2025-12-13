<template>
  <div class="p-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
      <h2 class="text-2xl font-bold text-gray-800">會員列表 ({{ totalItems }})</h2>
      <div class="flex gap-3">
        <button @click="exportToExcel" class="bg-white border border-gray-300 text-gray-600 px-4 py-2 rounded hover:bg-gray-50 transition flex items-center gap-2">
          <i class="fas fa-file-export"></i> 匯出 Excel
        </button>
        <button class="bg-xieOrange text-white px-4 py-2 rounded font-bold hover:bg-orange-600 transition flex items-center gap-2" @click="$router.push('/admin/users/new')">
          <i class="fas fa-plus"></i> 新增會員
        </button>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-1">
          <label class="block text-xs font-bold text-gray-500 mb-1">搜尋</label>
          <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
              <i class="fas fa-search"></i>
            </span>
            <input v-model="filters.search" type="text" placeholder="姓名 / Email / 手機" class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:border-xieOrange">
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-500 mb-1">會員等級</label>
          <select v-model="filters.level" class="w-full py-2 px-3 border border-gray-300 rounded text-sm focus:outline-none focus:border-xieOrange">
            <option value="">全部等級</option>
            <option value="normal">一般會員</option>
            <option value="vip">VIP 會員</option>
            <option value="vvip">VVIP 大戶</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-500 mb-1">帳號狀態</label>
          <select v-model="filters.status" class="w-full py-2 px-3 border border-gray-300 rounded text-sm focus:outline-none focus:border-xieOrange">
            <option value="">全部狀態</option>
            <option value="active">正常使用</option>
            <option value="banned">已停用 (黑名單)</option>
          </select>
        </div>

        <div class="flex items-end">
          <button @click="resetFilters" class="w-full bg-xieBlue text-white py-2 rounded text-sm font-bold hover:bg-gray-700 transition">
            重置篩選
          </button>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-gray-50 text-gray-500 text-xs uppercase border-b border-gray-100">
            <th class="px-6 py-4 font-bold">會員資料</th>
            <th class="px-6 py-4 font-bold">聯絡電話</th>
            <th class="px-6 py-4 font-bold text-center">等級</th>
            <th class="px-6 py-4 font-bold">消費數據 (LTV)</th>
            <th class="px-6 py-4 font-bold text-center">狀態</th>
            <th class="px-6 py-4 font-bold text-right">操作</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-sm">
          <tr v-for="user in users" :key="user.id" class="hover:bg-orange-50 transition group">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden flex items-center justify-center text-gray-500 font-bold text-lg">
                  {{ user.name.charAt(0) }}
                </div>
                <div>
                  <div class="font-bold text-gray-800">{{ user.name }}</div>
                  <div class="text-xs text-gray-400">{{ user.email }}</div>
                  <div class="text-xs text-gray-400">註冊日: {{ formatDate(user.created_at) }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-gray-600">{{ user.phone || '未填寫' }}</td>
            <td class="px-6 py-4 text-center">
              <span :class="getLevelBadgeClass(getUserLevel(user.orders_sum_total_amount))">
                {{ getUserLevel(user.orders_sum_total_amount) }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div>
                <span class="text-xs text-gray-400">總消費:</span>
                <span class="font-bold text-xieOrange">${{ formatPrice(user.orders_sum_total_amount || 0) }}</span>
              </div>
              <div class="text-xs text-gray-400">訂單數: {{ user.orders_count || 0 }} 筆</div>
            </td>
            <td class="px-6 py-4 text-center">
              <span class="inline-block w-2 h-2 rounded-full mr-1" :class="user.status === 'active' ? 'bg-green-500' : 'bg-red-500'"></span>
              <span class="font-bold text-xs" :class="user.status === 'active' ? 'text-green-600' : 'text-red-600'">
                {{ user.status === 'active' ? '正常' : '已停用' }}
              </span>
            </td>
            <td class="px-6 py-4 text-right">
              <button class="text-gray-400 hover:text-xieOrange mx-1" title="編輯" @click="$router.push(`/admin/users/${user.id}/edit`)">
                <i class="fas fa-edit"></i>
              </button>
              <button class="text-gray-400 hover:text-xieBlue mx-1" title="查看訂單">
                <i class="fas fa-file-invoice"></i>
              </button>
            </td>
          </tr>
          <tr v-if="users.length === 0">
            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
              沒有找到符合條件的會員
            </td>
          </tr>
        </tbody>
      </table>

      <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-white" v-if="users.length > 0">
        <span class="text-xs text-gray-500">顯示第 {{ (currentPage - 1) * itemsPerPage + 1 }} 至 {{ Math.min(currentPage * itemsPerPage, totalItems) }} 筆，共 {{ totalItems }} 筆會員資料</span>
        <div class="flex gap-1">
          <button
            @click="fetchUsers(currentPage - 1)"
            :disabled="currentPage === 1"
            class="px-3 py-1 border border-gray-300 rounded text-gray-500 text-sm hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >上一頁</button>

          <span class="px-3 py-1 bg-xieOrange text-white rounded text-sm font-bold">{{ currentPage }} / {{ lastPage }}</span>

          <button
            @click="fetchUsers(currentPage + 1)"
            :disabled="currentPage === lastPage"
            class="px-3 py-1 border border-gray-300 rounded text-gray-500 text-sm hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >下一頁</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api'

export default {
  name: 'AdminUsers',
  data () {
    return {
      users: [],
      filters: {
        search: '',
        level: '',
        status: ''
      },
      currentPage: 1,
      lastPage: 1,
      totalItems: 0,
      itemsPerPage: 15
    }
  },
  created () {
    this.fetchUsers()
  },
  methods: {
    async fetchUsers (page = 1) {
      this.currentPage = page
      try {
        const params = {
          page: page,
          search: this.filters.search,
          status: this.filters.status
        }
        const response = await api.get('/admin/users', { params })
        this.users = response.data.data || []
        this.currentPage = response.data.current_page
        this.lastPage = response.data.last_page
        this.totalItems = response.data.total
      } catch (error) {
        console.error('Error fetching users:', error)
        this.$toast.error('無法載入會員列表')
      }
    },
    formatDate (dateString) {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleDateString('zh-TW')
    },
    formatPrice (price) {
      return price.toLocaleString()
    },
    getUserLevel (totalAmount) {
      const amount = Number(totalAmount) || 0
      if (amount >= 100000) return 'VVIP'
      if (amount >= 10000) return 'VIP'
      return '一般'
    },
    getLevelBadgeClass (level) {
      if (level === 'VVIP') return 'bg-purple-100 text-purple-600 px-2 py-1 rounded text-xs font-bold border border-purple-200'
      if (level === 'VIP') return 'bg-yellow-100 text-yellow-600 px-2 py-1 rounded text-xs font-bold border border-yellow-200'
      return 'bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold border border-gray-200'
    },
    resetFilters () {
      this.filters = {
        search: '',
        level: '',
        status: ''
      }
      this.currentPage = 1
    },
    exportToExcel () {
      // For simplicity in optimization phase, currently only exports current page or needs a separate API.
      // Warn user:
      if (!confirm('注意：目前僅會匯出當前頁面的會員資料。是否繼續？')) return

      const users = this.users
      let csvContent = 'data:text/csv;charset=utf-8,'
      csvContent += 'Name,Email,Phone,Level,Total Consumption,Order Count,Status,Join Date\n'

      users.forEach(user => {
        const row = [
          user.name,
          user.email,
          user.phone || '',
          this.getUserLevel(user.orders_sum_total_amount),
          user.orders_sum_total_amount || 0,
          user.orders_count || 0,
          user.status === 'active' ? 'Active' : 'Banned',
          this.formatDate(user.created_at)
        ].map(e => `"${e}"`).join(',')
        csvContent += row + '\n'
      })

      const encodedUri = encodeURI(csvContent)
      const link = document.createElement('a')
      link.setAttribute('href', encodedUri)
      link.setAttribute('download', 'members_export.csv')
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
    }
  },
  watch: {
    filters: {
      handler () {
        this.fetchUsers(1)
      },
      deep: true
    }
  }
}
</script>

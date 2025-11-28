<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const coupons = ref([])
const showModal = ref(false)
const isEditing = ref(false)
const form = ref({
  id: null,
  code: '',
  discount_amount: 0,
  type: 'fixed',
  limit_price: 0,
  starts_at: '',
  ends_at: ''
})

onMounted(() => {
  fetchCoupons()
})

async function fetchCoupons () {
  try {
    const response = await api.get('/admin/coupons')
    coupons.value = response.data
  } catch (error) {
    console.error('Fetch coupons error:', error)
    alert('無法載入優惠卷列表')
  }
}

function formatDate (dateString) {
  if (!dateString) return ''
  const date = new Date(dateString)
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

function openCreateModal () {
  isEditing.value = false
  form.value = {
    id: null,
    code: '',
    discount_amount: 0,
    type: 'fixed',
    limit_price: 0,
    starts_at: '',
    ends_at: ''
  }
  showModal.value = true
}

function openEditModal (coupon) {
  isEditing.value = true
  form.value = {
    ...coupon,
    starts_at: formatDate(coupon.starts_at),
    ends_at: formatDate(coupon.ends_at)
  }
  showModal.value = true
}

async function saveCoupon () {
  try {
    if (isEditing.value) {
      await api.put(`/admin/coupons/${form.value.id}`, form.value)
      alert('更新成功')
    } else {
      await api.post('/admin/coupons', form.value)
      alert('新增成功')
    }
    showModal.value = false
    fetchCoupons()
  } catch (error) {
    console.error('Save coupon error:', error)
    alert(error.response?.data?.message || '儲存失敗')
  }
}

async function deleteCoupon (id) {
  if (!confirm('確定要刪除此優惠卷嗎？')) return
  try {
    await api.delete(`/admin/coupons/${id}`)
    alert('刪除成功')
    fetchCoupons()
  } catch (error) {
    console.error('Delete coupon error:', error)
    alert('刪除失敗')
  }
}
</script>

<template>
  <div class="admin-coupon-container">
    <div class="header">
      <h1>🎟️ 優惠卷管理</h1>
      <button class="create-btn" @click="openCreateModal">新增優惠卷</button>
    </div>

    <table class="coupon-table">
      <thead>
        <tr>
          <th>代碼</th>
          <th>折扣</th>
          <th>類型</th>
          <th>低消限制</th>
          <th>開始日期</th>
          <th>結束日期</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="coupon in coupons" :key="coupon.id">
          <td>{{ coupon.code }}</td>
          <td>{{ coupon.discount_amount }}</td>
          <td>{{ coupon.type === 'fixed' ? '定額' : '百分比' }}</td>
          <td>{{ coupon.limit_price || '無' }}</td>
          <td>{{ coupon.starts_at ? coupon.starts_at.slice(0, 10) : '即時' }}</td>
          <td>{{ coupon.ends_at ? coupon.ends_at.slice(0, 10) : '永久' }}</td>
          <td>
            <button class="edit-btn" @click="openEditModal(coupon)">編輯</button>
            <button class="delete-btn" @click="deleteCoupon(coupon.id)">刪除</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Modal -->
    <div v-if="showModal" class="modal-overlay">
      <div class="modal-content">
        <h2>{{ isEditing ? '編輯優惠卷' : '新增優惠卷' }}</h2>

        <div class="form-group">
          <label>代碼</label>
          <input v-model="form.code" type="text" required>
        </div>

        <div class="form-group">
          <label>折扣類型</label>
          <select v-model="form.type">
            <option value="fixed">定額折扣 ($)</option>
            <option value="percentage">百分比折扣 (%)</option>
          </select>
        </div>

        <div class="form-group">
          <label>折扣數值</label>
          <input v-model.number="form.discount_amount" type="number" min="0" required>
        </div>

        <div class="form-group">
          <label>最低消費限制 (選填)</label>
          <input v-model.number="form.limit_price" type="number" min="0">
        </div>

        <div class="form-group">
          <label>開始日期 (選填)</label>
          <input v-model="form.starts_at" type="date">
        </div>

        <div class="form-group">
          <label>結束日期 (選填)</label>
          <input v-model="form.ends_at" type="date">
        </div>

        <div class="modal-actions">
          <button @click="showModal = false" class="cancel-btn">取消</button>
          <button @click="saveCoupon" class="save-btn">儲存</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.admin-coupon-container {
  padding: 20px;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.create-btn {
  background-color: #2ecc71;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
}

.coupon-table {
  width: 100%;
  border-collapse: collapse;
}

.coupon-table th, .coupon-table td {
  border: 1px solid #ddd;
  padding: 12px;
  text-align: left;
}

.coupon-table th {
  background-color: #f2f2f2;
}

.edit-btn {
  background-color: #3498db;
  color: white;
  border: none;
  padding: 5px 10px;
  border-radius: 4px;
  margin-right: 5px;
  cursor: pointer;
}

.delete-btn {
  background-color: #e74c3c;
  color: white;
  border: none;
  padding: 5px 10px;
  border-radius: 4px;
  cursor: pointer;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-content {
  background: white;
  padding: 20px;
  border-radius: 8px;
  width: 400px;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
}

.form-group input, .form-group select {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}

.cancel-btn {
  background: #95a5a6;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
}

.save-btn {
  background: #2ecc71;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
}
</style>

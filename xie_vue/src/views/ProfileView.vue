<template>
  <div class="profile">

    <!-- 未登入：登入 / 註冊區 -->
    <div v-if="!isLoggedIn" class="auth-wrapper">
      <div class="auth-card">
        <div class="auth-header">
          <h2>會員登入 / 註冊</h2>
          <p>登入後即可查看購物紀錄與專屬會員優惠。</p>
        </div>

        <div class="auth-panels">
          <!-- 登入 -->
          <div class="auth-box">
            <h3>已經有帳號</h3>
            <p class="hint">請輸入 Email 與密碼登入。</p>
            <input
              v-model="loginEmail"
              type="email"
              placeholder="Email"
            >
            <input
              v-model="loginPassword"
              type="password"
              placeholder="密碼"
            >
            <button class="primary-btn" @click="handleLogin">登入</button>
          </div>

          <!-- 註冊 -->
          <div class="auth-box">
            <h3>還沒有帳號？</h3>
            <p class="hint">註冊一個簡單的會員帳號吧！</p>
            <input
              v-model="registerName"
              type="text"
              placeholder="姓名"
            >
            <input
              v-model="registerEmail"
              type="email"
              placeholder="Email"
            >
            <input
              v-model="registerPassword"
              type="password"
              placeholder="設定密碼"
            >
            <input
              v-model="registerPasswordConfirm"
              type="password"
              placeholder="再次輸入密碼"
            >
            <button class="secondary-btn" @click="handleRegister">註冊</button>
          </div>
        </div>
      </div>
    </div>

    <!-- 已登入：會員中心 -->
    <div v-else class="member-wrapper">
      <!-- 頭像 + 基本資訊 -->
      <div class="profile-header card">
        <div class="avatar-wrap">
          <img class="avatar" :src="avatarUrl" alt="avatar">
          <button class="avatar-btn" @click="triggerFileInput">
            更換頭像
          </button>
          <input
            type="file"
            accept="image/*"
            @change="onFileChange"
            id="avatarInput"
            style="display:none"
          >
        </div>

        <div class="profile-info">
          <h2>{{ user.name }}</h2>
          <p class="level">會員等級：一般會員</p>
          <p class="spent">累積消費：<span class="money">$0</span></p>

          <div class="tag-row">
            <span class="tag">本月下單 {{ orders.length }} 次</span>
          </div>
        </div>

        <div class="quick-actions">
          <button class="outline-btn" @click="openCoupons">查看優惠券</button>
          <button class="outline-btn" @click="openEditProfile">編輯個人資料</button>
          <button class="danger-btn" @click="logout">登出</button>
        </div>
      </div>

      <!-- 購物紀錄 + 狀態清單 -->
      <div class="grid">
        <div class="card">
          <h3>最近購物紀錄</h3>
          <ul class="orders" v-if="orders.length > 0">
            <li v-for="order in orders" :key="order.id" @click="openOrderDetails(order.id)" style="cursor: pointer;">
              <div class="order-top">
                <span class="order-title">訂單編號：{{ order.id }}</span>
                <span class="status" :class="order.status">{{ getStatusText(order.status) }}</span>
              </div>
              <div class="order-bottom">
                <span>{{ new Date(order.created_at).toLocaleDateString() }}</span>
                <span>數量：{{ order.items.length }} ｜ 金額：$ {{ order.total_amount }}</span>
              </div>
            </li>
          </ul>
          <div v-else class="no-orders">
            尚無訂單紀錄
          </div>
        </div>

        <div class="card">
          <h3>訂單狀態總覽</h3>
          <ul class="status-list">
            <li>
              <span class="dot all"></span>
              全部訂單
              <span class="badge">{{ orders.length }}</span>
            </li>
            <li>
              <span class="dot pay"></span>
              待付款
              <span class="badge">{{ countStatus('pending_payment') }}</span>
            </li>
            <li>
              <span class="dot ship"></span>
              待出貨
              <span class="badge">{{ countStatus('processing') }}</span>
            </li>
            <li>
              <span class="dot receive"></span>
              待收貨
              <span class="badge">{{ countStatus('shipped') }}</span>
            </li>
            <li>
              <span class="dot done"></span>
              已完成
              <span class="badge">{{ countStatus('completed') }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Edit Profile Modal -->
    <div v-if="showEditProfile" class="modal-overlay" @click.self="showEditProfile = false">
      <div class="modal-content">
        <h3>編輯個人資料</h3>
        <div class="form-group">
          <label>姓名</label>
          <input v-model="editForm.name" type="text">
        </div>
        <div class="form-group">
          <label>電話</label>
          <input v-model="editForm.phone" type="text">
        </div>
        <div class="form-group">
          <label>地址</label>
          <input v-model="editForm.address" type="text">
        </div>
        <div class="form-group">
          <label>生日</label>
          <input v-model="editForm.birthday" type="date">
        </div>
        <div class="modal-actions">
          <button class="outline-btn" @click="showEditProfile = false">取消</button>
          <button class="primary-btn" @click="updateProfile">儲存</button>
        </div>
      </div>
    </div>

    <!-- Coupons Modal -->
    <div v-if="showCoupons" class="modal-overlay" @click.self="showCoupons = false">
      <div class="modal-content">
        <h3>我的優惠券</h3>
        <ul class="coupon-list" v-if="coupons.length > 0">
          <li v-for="coupon in coupons" :key="coupon.id" class="coupon-item">
            <div class="coupon-info">
              <span class="code">{{ coupon.code }}</span>
              <span class="desc">
                {{ coupon.type === 'fixed' ? '折抵 $' + coupon.discount_amount : '打 ' + (100 - coupon.discount_amount) + ' 折' }}
              </span>
              <span class="limit" v-if="coupon.limit_price">滿 ${{ coupon.limit_price }} 可用</span>
            </div>
            <button class="copy-btn" @click="copyCode(coupon.code)">複製</button>
          </li>
        </ul>
        <div v-else class="no-data">暫無優惠券</div>
        <div class="modal-actions">
          <button class="primary-btn" @click="showCoupons = false">關閉</button>
        </div>
      </div>
    </div>

    <!-- Order Details Modal -->
    <div v-if="showOrderDetails && selectedOrder" class="modal-overlay" @click.self="showOrderDetails = false">
      <div class="modal-content">
        <h3>訂單詳情 #{{ selectedOrder.id }}</h3>
        <div class="order-meta">
          <p><strong>下單日期：</strong> {{ new Date(selectedOrder.created_at).toLocaleString() }}</p>
          <p><strong>訂單狀態：</strong> {{ getStatusText(selectedOrder.status) }}</p>
          <p><strong>物流狀態：</strong> <span class="highlight">{{ getLogisticsStatus(selectedOrder.status) }}</span></p>
          <p><strong>總金額：</strong> ${{ selectedOrder.total_amount }}</p>
        </div>

        <h4>商品列表</h4>
        <ul class="order-items-list">
          <li v-for="item in selectedOrder.items" :key="item.id" class="order-item-row">
            <span>{{ item.product.name }}</span>
            <span>x{{ item.quantity }}</span>
            <span>${{ item.price * item.quantity }}</span>
          </li>
        </ul>

        <div class="modal-actions">
          <button class="outline-btn" @click="showOrderDetails = false">關閉</button>
          <button v-if="selectedOrder.status === 'pending_payment'" class="primary-btn" @click="payOrder">立即付款</button>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import api from '@/services/api'

export default {
  name: 'ProfileView',
  data () {
    return {
      avatarUrl: 'https://img.icons8.com/color/96/000000/user-male-circle--v2.png',
      // 登入
      loginEmail: '',
      loginPassword: '',
      // 註冊
      registerName: '',
      registerEmail: '',
      registerPassword: '',
      registerPasswordConfirm: '',
      user: null,
      orders: [],
      // Modals
      showEditProfile: false,
      showCoupons: false,
      showOrderDetails: false,
      selectedOrder: null,
      // Edit Profile Form
      editForm: {
        name: '',
        phone: '',
        address: '',
        birthday: ''
      },
      // Coupons
      coupons: []
    }
  },
  computed: {
    isLoggedIn () {
      return !!this.user
    }
  },
  created () {
    this.fetchUser()
    this.fetchOrders()
  },
  methods: {
    async fetchUser () {
      const token = localStorage.getItem('token')
      if (token) {
        try {
          const response = await api.get('/user')
          this.user = response.data
          this.fetchOrders()
        } catch (error) {
          console.error('Error fetching user:', error)
          localStorage.removeItem('token')
          this.user = null
        }
      }
    },
    async fetchOrders () {
      if (!this.user) return
      try {
        const response = await api.get('/orders')
        this.orders = response.data
      } catch (error) {
        console.error('Error fetching orders:', error)
      }
    },
    async handleLogin () {
      try {
        const response = await api.post('/login', {
          email: this.loginEmail,
          password: this.loginPassword
        })
        const { access_token: accessToken, user } = response.data
        localStorage.setItem('token', accessToken)
        localStorage.setItem('user_role', user.role)
        this.user = user
        this.loginPassword = ''
        this.fetchOrders()
        alert('登入成功！')
        window.location.reload()
      } catch (error) {
        console.error('Login error:', error)
        alert('登入失敗，請檢查帳號密碼。')
      }
    },
    async handleRegister () {
      if (!this.registerName || !this.registerEmail || !this.registerPassword || !this.registerPasswordConfirm) {
        alert('請把註冊資料填寫完整。')
        return
      }
      if (this.registerPassword !== this.registerPasswordConfirm) {
        alert('兩次輸入的密碼不一致。')
        return
      }

      try {
        const response = await api.post('/register', {
          name: this.registerName,
          email: this.registerEmail,
          password: this.registerPassword,
          password_confirmation: this.registerPasswordConfirm
        })
        const { access_token: accessToken, user } = response.data
        localStorage.setItem('token', accessToken)
        this.user = user

        // Clear form
        this.registerName = ''
        this.registerEmail = ''
        this.registerPassword = ''
        this.registerPasswordConfirm = ''
        this.fetchOrders()

        alert('註冊成功！')
      } catch (error) {
        console.error('Register error:', error)
        if (error.response && error.response.data) {
          const msg = error.response.data.message || JSON.stringify(error.response.data)
          alert('註冊失敗：' + msg)
        } else {
          alert('註冊失敗，請稍後再試。')
        }
      }
    },
    async logout () {
      try {
        await api.post('/logout')
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        localStorage.removeItem('token')
        localStorage.removeItem('user_role')
        this.user = null
        this.orders = []
        alert('已登出')
        window.location.reload()
      }
    },
    triggerFileInput () {
      this.$el.querySelector('#avatarInput').click()
    },
    onFileChange (e) {
      const file = e.target.files[0]
      if (file) {
        const reader = new FileReader()
        reader.onload = (e2) => {
          this.avatarUrl = e2.target.result
        }
        reader.readAsDataURL(file)
      }
    },
    getStatusClass (status) {
      const map = {
        pending_payment: 'waiting',
        processing: 'waiting',
        shipped: 'waiting',
        delivered: 'received',
        completed: 'received',
        cancelled: 'waiting', // or error color
        returned: 'waiting'
      }
      return map[status] || ''
    },
    getStatusText (status) {
      const map = {
        pending_payment: '待付款',
        processing: '處理中',
        shipped: '已出貨',
        delivered: '已送達',
        completed: '已完成',
        cancelled: '已取消',
        returned: '已退貨'
      }
      return map[status] || status
    },
    formatDate (dateString) {
      return new Date(dateString).toLocaleDateString()
    },
    countStatus (status) {
      return this.orders.filter(o => o.status === status).length
    },
    openEditProfile () {
      this.editForm = {
        name: this.user.name,
        phone: this.user.phone || '',
        address: this.user.address || '',
        birthday: this.user.birthday ? this.user.birthday.split('T')[0] : ''
      }
      this.showEditProfile = true
    },
    async updateProfile () {
      try {
        const response = await api.put('/profile', this.editForm)
        this.user = response.data.user
        this.showEditProfile = false
        alert('個人資料更新成功！')
      } catch (error) {
        console.error('Update profile error:', error)
        alert('更新失敗，請稍後再試。')
      }
    },
    async openCoupons () {
      this.showCoupons = true
      try {
        const response = await api.get('/coupons')
        this.coupons = response.data
      } catch (error) {
        console.error('Fetch coupons error:', error)
      }
    },
    copyCode (code) {
      navigator.clipboard.writeText(code).then(() => {
        alert('優惠碼已複製：' + code)
      })
    },
    async openOrderDetails (orderId) {
      try {
        const response = await api.get(`/orders/${orderId}`)
        this.selectedOrder = response.data
        this.showOrderDetails = true
      } catch (error) {
        console.error('Fetch order details error:', error)
        alert('無法取得訂單詳情')
      }
    },
    getLogisticsStatus (status) {
      const map = {
        pending_payment: '待付款',
        processing: '備貨中',
        shipped: '已出貨',
        completed: '已送達',
        cancelled: '已取消'
      }
      return map[status] || status
    },
    async payOrder () {
      if (!this.selectedOrder) return
      try {
        await api.post(`/orders/${this.selectedOrder.id}/pay`)
        alert('付款成功！')
        this.showOrderDetails = false
        this.fetchOrders() // Refresh list
      } catch (error) {
        console.error('Payment error:', error)
        alert('付款失敗，請稍後再試。')
      }
    }
  }
}
</script>

<style scoped>
.profile {
  margin: 2rem auto;
  max-width: 900px;
  padding: 1rem;
}

/* ========== 未登入 ========== */

.auth-wrapper {
  display: flex;
  justify-content: center;
}

.auth-card {
  width: 100%;
  max-width: 780px;
  padding: 2rem 2.5rem;
  border-radius: 16px;
  background: var(--main-card);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
  text-align: left;
}

.auth-header h2 {
  margin: 0 0 0.5rem;
}

.auth-header p {
  margin: 0;
  color: #888;
  font-size: 0.95rem;
}

.auth-panels {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  margin-top: 1.8rem;
}

.auth-box {
  flex: 1 1 260px;
  padding: 1rem 1.3rem;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.7);
  border: 1px solid #eee;
}

.dark .auth-box {
  background: rgba(32, 32, 32, 0.9);
  border-color: #444;
}

.auth-box h3 {
  margin: 0 0 0.3rem;
}

.auth-box .hint {
  margin: 0 0 0.8rem;
  font-size: 0.9rem;
  color: #999;
}

.auth-box input {
  width: 100%;
  padding: 0.45rem 0.6rem;
  margin-bottom: 0.6rem;
  border-radius: 6px;
  border: 1px solid #ccc;
  font-size: 0.95rem;
  box-sizing: border-box;
}

.dark .auth-box input {
  background: #222;
  border-color: #555;
  color: #f5f5f5;
}

.primary-btn,
.secondary-btn {
  display: inline-block;
  padding: 0.45rem 1.4rem;
  border-radius: 999px;
  border: none;
  cursor: pointer;
  font-size: 0.95rem;
  font-weight: 600;
  color: #fff;
}

.primary-btn {
  background: #3498db;
}

.secondary-btn {
  background: #e67e22;
}

/* ========== 已登入 ========== */

.member-wrapper {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.card {
  border-radius: 16px;
  background: #2c3e50;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
  padding: 1.6rem 2rem;
  text-align: left;
  color: white;
}

.profile-header {
  display: grid;
  grid-template-columns: auto 1fr auto;
  gap: 1.5rem;
  align-items: center;
}

.avatar-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.6rem;
}

.avatar {
  width: 96px;
  height: 96px;
  border-radius: 50%;
  border: 3px solid #e67e22;
  object-fit: cover;
}

.avatar-btn {
  font-size: 0.8rem;
  padding: 0.2rem 0.8rem;
  border-radius: 999px;
  border: 1px solid #465c71;
  background: transparent;
  color: #ecf0f1;
  cursor: pointer;
}

.profile-info h2 {
  margin: 0 0 0.4rem;
  color: white;
}

.level {
  margin: 0.1rem 0;
  color: #bdc3c7;
}

.spent {
  margin: 0.2rem 0 0.8rem;
  font-size: 0.95rem;
  color: #ecf0f1;
}

.money {
  color: #e67e22;
  font-weight: 600;
}

.tag-row {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
}

.tag {
  font-size: 0.8rem;
  padding: 0.15rem 0.6rem;
  border-radius: 999px;
  background: #ecf0f1;
  color: #2c3e50;
  font-weight: 600;
}

.tag-warning {
  background: rgba(230, 126, 34, 0.1);
  color: #e67e22;
}

.quick-actions {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
  align-items: flex-end;
}

.outline-btn,
.danger-btn {
  font-size: 0.85rem;
  padding: 0.3rem 0.9rem;
  border-radius: 999px;
  cursor: pointer;
}

.outline-btn {
  background: transparent;
  border: 1px solid #ccc;
}

.danger-btn {
  background: #e74c3c;
  color: #fff;
  border: none;
}

/* grid 區塊 */

.grid {
  display: grid;
  grid-template-columns: 2fr 1.2fr;
  gap: 1.5rem;
}

.orders {
  list-style: none;
  padding: 0;
  margin: 0.6rem 0 0;
}

.orders li {
  padding: 0.8rem 0.4rem;
  border-bottom: 1px solid #465c71;
}

.order-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 0.8rem;
}

.order-bottom {
  display: flex;
  justify-content: space-between;
  font-size: 0.85rem;
  color: #bdc3c7;
  margin-top: 0.2rem;
}

.order-title {
  font-weight: 600;
  color: #ecf0f1;
}

.status {
  padding: 0.1rem 0.6rem;
  border-radius: 999px;
  font-size: 0.8rem;
}

.status.received {
  background: rgba(39, 174, 96, 0.2);
  color: #2ecc71;
}

.status.waiting {
  background: rgba(230, 126, 34, 0.2);
  color: #e67e22;
}

.link-btn {
  margin-top: 0.6rem;
  border: none;
  background: none;
  color: #3498db;
  cursor: pointer;
  font-size: 0.9rem;
}

.no-orders {
  padding: 1rem;
  color: #bdc3c7;
  text-align: center;
}

/* 狀態總覽 */

.status-list {
  list-style: none;
  padding: 0;
  margin: 0.8rem 0 0;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.status-list li {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 0.95rem;
  color: #ecf0f1;
}

.status-list li > span:nth-child(2) {
  flex: 1;
  margin-left: 0.4rem;
}

.dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  display: inline-block;
}

.dot.all { background: #e67e22; }
.dot.pay { background: #f39c12; }
.dot.ship { background: #3498db; }
.dot.receive { background: #e67e22; }
.dot.done { background: #27ae60; }

.badge {
  min-width: 20px;
  padding: 0.1rem 0.4rem;
  text-align: center;
  border-radius: 999px;
  background: #ecf0f1;
  color: #2c3e50;
  font-weight: bold;
  font-size: 0.8rem;
  background: #f3f3f3;
}

/* RWD */

@media (max-width: 768px) {
  .auth-card {
    padding: 1.5rem 1.2rem;
  }

  .profile-header {
    grid-template-columns: 1fr;
    text-align: left;
  }

  .quick-actions {
    flex-direction: row;
    justify-content: flex-start;
    margin-top: 0.5rem;
  }

  .grid {
    grid-template-columns: 1fr;
  }
}

/* Modals */
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
  z-index: 1000;
}

.modal-content {
  background: var(--main-card);
  padding: 2rem;
  border-radius: 12px;
  width: 90%;
  max-width: 400px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.modal-content h3 {
  margin-top: 0;
}

.form-group {
  margin-bottom: 1rem;
  text-align: left;
}

.form-group label {
  display: block;
  margin-bottom: 0.3rem;
  font-size: 0.9rem;
  color: #666;
}

.form-group input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 6px;
  box-sizing: border-box;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.8rem;
  margin-top: 1.5rem;
}

.coupon-list {
  list-style: none;
  padding: 0;
  margin: 0;
  max-height: 300px;
  overflow-y: auto;
}

.coupon-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.8rem;
  border: 1px dashed #e67e22;
  border-radius: 8px;
  margin-bottom: 0.8rem;
  background: rgba(230, 126, 34, 0.05);
}

.coupon-info {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.coupon-info .code {
  font-weight: bold;
  color: #e67e22;
  font-size: 1.1rem;
}

.coupon-info .desc {
  font-size: 0.9rem;
  color: #555;
}

.coupon-info .limit {
  font-size: 0.8rem;
  color: #888;
}

.copy-btn {
  background: #e67e22;
  color: #fff;
  border: none;
  padding: 0.3rem 0.8rem;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.85rem;
}

.no-data {
  text-align: center;
  color: #888;
  padding: 1rem;
}

.order-meta p {
  margin: 0.5rem 0;
  color: #555;
}

.highlight {
  color: #e67e22;
  font-weight: bold;
}

.order-items-list {
  list-style: none;
  padding: 0;
  margin: 1rem 0;
  border-top: 1px solid #eee;
  border-bottom: 1px solid #eee;
}

.order-item-row {
  display: flex;
  justify-content: space-between;
  padding: 0.8rem 0;
  border-bottom: 1px solid #f9f9f9;
}

.order-item-row:last-child {
  border-bottom: none;
}
</style>

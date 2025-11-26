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
            <p class="hint">請輸入帳號與密碼登入。</p>
            <input
              v-model="loginUsername"
              type="text"
              placeholder="帳號"
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
              v-model="registerUsername"
              type="text"
              placeholder="註冊帳號"
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
          <h2>沒良心先生</h2>
          <p class="level">會員等級：黑心 VIP</p>
          <p class="spent">累積消費：<span class="money">$99,999</span></p>

          <div class="tag-row">
            <span class="tag">本月下單 3 次</span>
            <span class="tag tag-warning">再消費 $1,001 升級</span>
          </div>
        </div>

        <div class="quick-actions">
          <button class="outline-btn">查看優惠券</button>
          <button class="outline-btn">編輯個人資料</button>
          <button class="danger-btn" @click="logout">登出</button>
        </div>
      </div>

      <!-- 購物紀錄 + 狀態清單 -->
      <div class="grid">
        <div class="card">
          <h3>最近購物紀錄</h3>
          <ul class="orders">
            <li>
              <div class="order-top">
                <span class="order-title">Whey Protein多口味乳清蛋白飲 (500/1kg裝)</span>
                <span class="status received">已收貨</span>
              </div>
              <div class="order-bottom">
                <span>訂單編號：NO20231101001</span>
                <span>數量：1 ｜ 金額：$1,280</span>
              </div>
            </li>
            <li>
              <div class="order-top">
                <span class="order-title">隔日配玩偶 濃魚娃娃</span>
                <span class="status waiting">待收貨</span>
              </div>
              <div class="order-bottom">
                <span>訂單編號：NO20231102009</span>
                <span>數量：1 ｜ 金額：$599</span>
              </div>
            </li>
          </ul>
          <button class="link-btn">查看全部訂單 ➜</button>
        </div>

        <div class="card">
          <h3>訂單狀態總覽</h3>
          <ul class="status-list">
            <li>
              <span class="dot all"></span>
              全部訂單
              <span class="badge">8</span>
            </li>
            <li>
              <span class="dot pay"></span>
              待付款
              <span class="badge">1</span>
            </li>
            <li>
              <span class="dot ship"></span>
              待出貨
              <span class="badge">2</span>
            </li>
            <li>
              <span class="dot receive"></span>
              待收貨
              <span class="badge">1</span>
            </li>
            <li>
              <span class="dot done"></span>
              已完成
              <span class="badge">4</span>
            </li>
          </ul>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
export default {
  name: 'ProfileView',
  data () {
    return {
      avatarUrl: 'https://img.icons8.com/color/96/000000/user-male-circle--v2.png',
      // 登入
      loginUsername: '',
      loginPassword: '',
      // 註冊
      registerUsername: '',
      registerPassword: '',
      registerPasswordConfirm: ''
    }
  },
  computed: {
    isLoggedIn () {
      return this.$store.state.isLoggedIn
    }
  },
  methods: {
    handleLogin () {
      // TODO: 更完整的登入驗證
      const stored = localStorage.getItem('user')
      if (!stored) {
        alert('目前尚未有註冊帳號，請先註冊。')
        return
      }
      const user = JSON.parse(stored)
      if (this.loginUsername === user.username && this.loginPassword === user.password) {
        this.$store.commit('setLoggedIn', true)
        this.loginPassword = ''
      } else {
        alert('帳號或密碼錯誤')
      }
    },
    handleRegister () {
      // TODO: 更完整的註冊驗證
      if (!this.registerUsername || !this.registerPassword || !this.registerPasswordConfirm) {
        alert('請把註冊帳號與兩次密碼都填寫完整。')
        return
      }
      if (this.registerPassword !== this.registerPasswordConfirm) {
        alert('兩次輸入的密碼不一致。')
        return
      }

      const user = {
        username: this.registerUsername,
        password: this.registerPassword
      }
      localStorage.setItem('user', JSON.stringify(user))
      alert('註冊成功，請使用此帳號登入。')

      // 把登入帳號帶好
      this.loginUsername = this.registerUsername
      this.loginPassword = ''
      this.registerUsername = ''
      this.registerPassword = ''
      this.registerPasswordConfirm = ''
    },
    logout () {
      this.$store.commit('setLoggedIn', false)
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
  background: var(--main-card);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
  padding: 1.6rem 2rem;
  text-align: left;
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
  border: 1px solid #ddd;
  background: transparent;
  cursor: pointer;
}

.profile-info h2 {
  margin: 0 0 0.4rem;
}

.level {
  margin: 0.1rem 0;
  color: #888;
}

.spent {
  margin: 0.2rem 0 0.8rem;
  font-size: 0.95rem;
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
  background: #f3f3f3;
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
  border-bottom: 1px solid #eee;
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
  color: #888;
  margin-top: 0.2rem;
}

.order-title {
  font-weight: 600;
}

.status {
  padding: 0.1rem 0.6rem;
  border-radius: 999px;
  font-size: 0.8rem;
}

.status.received {
  background: rgba(39, 174, 96, 0.1);
  color: #27ae60;
}

.status.waiting {
  background: rgba(230, 126, 34, 0.1);
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
</style>

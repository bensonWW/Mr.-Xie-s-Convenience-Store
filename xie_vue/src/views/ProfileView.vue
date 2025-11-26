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

<style>
@import './css/ProfileView.css';
</style>

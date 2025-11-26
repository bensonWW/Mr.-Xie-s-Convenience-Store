<template>
  <div class="profile">

    <!-- 未登入畫面：登入 + 註冊 -->
    <div v-if="!isLoggedIn" class="not-login">
      <h2>會員登入 / 註冊</h2>
      <p>請先登入後即可查看您的購物紀錄與會員資訊。</p>

      <!-- 登入區 -->
      <div class="auth-box">
        <h3>登入</h3>
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
        <button class="login-btn" @click="handleLogin">登入</button>
      </div>

      <!-- 註冊區 -->
      <div class="auth-box">
        <h3>還沒有帳號？註冊一個</h3>
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
        <button class="login-btn" @click="handleRegister">註冊</button>
      </div>
    </div>

    <!-- 已登入畫面 -->
    <div v-else>
      <div class="profile-header">
        <img class="avatar" :src="avatarUrl" alt="avatar">
        <div>
          <h2>沒良心先生</h2>
          <p>會員等級：黑心VIP</p>
          <p>累積消費：$99999</p>
          <div class="upload-btn">
            <input
              type="file"
              accept="image/*"
              @change="onFileChange"
              id="avatarInput"
              style="display:none"
            >
            <button @click="triggerFileInput">上傳大頭照</button>
          </div>
        </div>
      </div>

      <div class="section">
        <h3>購物紀錄</h3>
        <ul class="orders">
          <li>
            <div class="order-title">Whey Protein多口味乳清蛋白飲 (500/1kg裝)</div>
            <div class="order-detail">
              數量：1 | 狀態：<span class="status received">已收貨</span>
            </div>
          </li>
          <li>
            <div class="order-title">隔日配玩偶 濃魚娃娃</div>
            <div class="order-detail">
              數量：1 | 狀態：<span class="status waiting">待收貨</span>
            </div>
          </li>
        </ul>
      </div>

      <div class="section">
        <h3>確認狀態清單</h3>
        <ul class="status-list">
          <li><span class="dot all"></span> 全部</li>
          <li><span class="dot pay"></span> 待付款</li>
          <li><span class="dot ship"></span> 待出貨</li>
          <li><span class="dot receive"></span> 待收貨</li>
          <li><span class="dot done"></span> 已完成</li>
        </ul>
      </div>

      <div style="margin-top: 1.5rem;">
        <button class="logout-btn" @click="logout">登出</button>
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
      // 登入用
      loginUsername: '',
      loginPassword: '',
      // 註冊用
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
    // 登入：拿 localStorage 的 user 來比對
    handleLogin () {
      const stored = localStorage.getItem('user')
      if (!stored) {
        alert('目前沒有已註冊的帳號，請先註冊。')
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

    // 註冊：把帳號密碼寫進 localStorage
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

      // 幫你自動帶入登入帳號
      this.loginUsername = this.registerUsername
      // 清空註冊欄位
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
  max-width: 600px;
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 2px 8px #eee;
  padding: 2rem;
}

.dark .profile {
  background: var(--main-card);
  color: var(--main-text);
}

.dark .profile-header h2,
.dark .profile-header p,
.dark .section h3,
.dark .order-title,
.dark .order-detail,
.dark .status-list li {
  color: var(--main-text);
}

.dark .orders li {
  background: #2b2b2b;
  box-shadow: none;
}

.dark .login-btn,
.dark .upload-btn button {
  background: #1976d2;
}

.dark .logout-btn {
  border: 1px solid #fff;
}

.not-login {
  text-align: center;
}

.login-btn {
  background: #3498db;
  color: #fff;
  border: none;
  padding: 0.5rem 1.3rem;
  border-radius: 5px;
  cursor: pointer;
  font-size: 1.1rem;
  margin-top: 1rem;
}

.logout-btn {
  background: #e74c3c;
  color: #fff;
  border: none;
  padding: 0.5rem 1.3rem;
  border-radius: 5px;
  cursor: pointer;
  font-size: 1.1rem;
}

.profile-header {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.avatar {
  width: 96px;
  height: 96px;
  border-radius: 50%;
  border: 2px solid #e67e22;
}

.section {
  margin-top: 2rem;
}

.section h3 {
  color: #e67e22;
  margin-bottom: 1rem;
  font-size: 1.1rem;
}

.orders {
  list-style: none;
  padding: 0;
}

.orders li {
  background: #fafafa;
  border-radius: 6px;
  margin-bottom: 1rem;
  padding: 1rem;
  box-shadow: 0 1px 3px #eee;
}

.status.received {
  color: #27ae60;
}

.status.waiting {
  color: #e67e22;
}

.status-list {
  display: flex;
  gap: 1.2rem;
  list-style: none;
  padding: 0;
}

.dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  display: inline-block;
  margin-right: 5px;
}

.dot.all { background: #e67e22; }
.dot.pay { background: #f39c12; }
.dot.ship { background: #3498db; }
.dot.receive { background: #e67e22; }
.dot.done { background: #27ae60; }
</style>

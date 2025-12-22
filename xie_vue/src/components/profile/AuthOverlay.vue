<template>
  <div class="auth-wrapper">
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
</template>

<script>
import api from '../../services/api'
import { mapActions } from 'vuex'
import { useToast } from 'vue-toastification'

export default {
  name: 'AuthOverlay',
  setup () {
    const toast = useToast()
    return { toast }
  },
  data () {
    return {
      loginEmail: '',
      loginPassword: '',
      registerName: '',
      registerEmail: '',
      registerPassword: '',
      registerPasswordConfirm: ''
    }
  },
  methods: {
    ...mapActions(['login']),
    async handleLogin () {
      try {
        await this.login({
          email: this.loginEmail,
          password: this.loginPassword
        })

        // 成功後停留本頁，不做全頁跳轉/重整
        this.toast.success('登入成功！')
        // 視圖會因為 isLoggedIn 變為 true 而自動切到會員中心
        // 若一定需要導回特定頁面，使用 SPA 導航即可保留 Toast：
        // const redirect = this.$route.query.redirect
        // if (redirect && typeof redirect === 'string' && redirect.startsWith('/')) {
        //   this.$router.replace(redirect)
        // }
      } catch (error) {
        console.error('Login error:', error)
        this.toast.error('登入失敗，請檢查帳號密碼。')
      }
    },
    async handleRegister () {
      if (!this.registerName || !this.registerEmail || !this.registerPassword || !this.registerPasswordConfirm) {
        this.toast.warning('請把註冊資料填寫完整。')
        return
      }
      if (this.registerPassword !== this.registerPasswordConfirm) {
        this.toast.warning('兩次輸入的密碼不一致。')
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

        // 不重整，直接更新 Vuex 狀態並觸發登入態
        this.$store.commit('SET_TOKEN', accessToken)
        if (user) this.$store.commit('SET_USER', user)
        // 再保險一次請求用戶資料（若後端在註冊回傳無完整 user）
        this.$store.dispatch('checkAuth').catch(() => {})

        this.toast.success('註冊成功！')
        // 視圖會因為 isLoggedIn 變為 true 而自動切到會員中心
      } catch (error) {
        console.error('Register error:', error)
        if (error.response && error.response.data) {
          const msg = error.response.data.message || JSON.stringify(error.response.data)
          this.toast.error('註冊失敗：' + msg)
        } else {
          this.toast.error('註冊失敗，請稍後再試。')
        }
      }
    }
  }
}
</script>

<style scoped>
.auth-wrapper {
  display: flex;
  justify-content: center;
  margin-top: 2rem;
}

.auth-card {
  width: 100%;
  max-width: 780px;
  padding: 2rem 2.5rem;
  border-radius: 16px;
  background: #ffffff;
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
  border-color: #ccc;
  font-size: 0.95rem;
  height: 42px;
  border-width: 1px;
  border-style: solid;
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
  transition: opacity 0.2s;
}

.primary-btn:hover, .secondary-btn:hover {
  opacity: 0.9;
}

.primary-btn {
  background: #3498db;
}

.secondary-btn {
  background: #e67e22;
}
</style>

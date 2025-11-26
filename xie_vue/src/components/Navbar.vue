<template>
  <nav class="navbar">
    <div class="nav-inner">
      <ul class="nav-links">
      <li><router-link to="/">首頁</router-link></li>
      <li><router-link to="/items">物品頁面</router-link></li>
      <li><router-link to="/profile">個人資料</router-link></li>
      <li><router-link to="/car">購物車</router-link></li>
      <li v-if="!isLoggedIn">
        <router-link to="/profile">登入 / 註冊</router-link>
      </li>
      <!-- 已登入 -->
      <li v-else class="member-area">
        <router-link to="/profile">會員中心</router-link>
        <button class="logout-btn" @click="logout">登出</button>
      </li>
    </ul>
      <div class="theme-toggle">
        <button @click="setTheme('light')" :class="{ active: theme === 'light' }">☀️</button>
        <button @click="setTheme('dark')" :class="{ active: theme === 'dark' }">🌙</button>
      </div>
    </div>
  </nav>
</template>

<script>
export default {
  name: 'Navbar',
  computed: {
    isLoggedIn () {
      return this.$store.state.isLoggedIn
    }
  },
  props: {
    theme: {
      type: String,
      default: 'light'
    }
  },
  methods: {
    logout () {
      this.$store.commit('setLoggedIn', false)
      this.$router.push('/profile')
    },
    setTheme (t) {
      this.$emit('set-theme', t)
    }
  }
}
</script>

<style scoped>
.navbar {
  background: #2c3e50;
  padding: 1rem 0;
}

.nav-inner {
  max-width: 1100px;
  margin: 0 auto;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center; /* keep links centered */
  padding: 0 1rem;
}
.nav-links {
  list-style: none;
  display: flex;
  gap: 2rem;
  margin: 0;
  padding: 0;
}

.navbar a {
  color: #fff;
  text-decoration: none;
  font-size: 1.2rem;
  font-weight: bold;
  transition: color 0.2s;
}
.navbar a.router-link-exact-active {
  color: #e67e22;
}
.member-area {
  display: flex;
  align-items: center;
  gap: 0.8rem;
}
.logout-btn {
  background: transparent;
  border: 1px solid #fff;
  border-radius: 4px;
  color: #fff;
  padding: 0.2rem 0.6rem;
  cursor: pointer;
  font-size: 0.95rem;
  }
.theme-toggle {
  display: flex;
  gap: 0.5rem;
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
}
.theme-toggle button {
  background: none;
  border: 1px solid #bbb;
  color: var(--main-text);
  padding: 0.3rem 1.1rem;
  border-radius: 20px;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}
.theme-toggle button.active, .theme-toggle button:hover {
  background: #e67e22;
  color: #fff;
  border-color: #e67e22;
}
</style>

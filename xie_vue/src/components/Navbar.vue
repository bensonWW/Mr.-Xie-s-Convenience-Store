<template>
  <nav class="navbar">
    <ul>
      <li><router-link to="/">首頁</router-link></li>
      <li><router-link to="/items">物品頁面</router-link></li>

      <!-- 未登入 -->
      <li v-if="!isLoggedIn">
        <router-link to="/profile">登入 / 註冊</router-link>
      </li>

      <!-- 已登入 -->
      <li v-else class="member-area">
        <router-link to="/profile">會員中心</router-link>
        <button class="logout-btn" @click="logout">登出</button>
      </li>
    </ul>
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
  methods: {
    logout () {
      this.$store.commit('setLoggedIn', false)
      this.$router.push('/profile')
    }
  }
}
</script>

<style scoped>
.navbar {
  background: #2c3e50;
  padding: 1rem 0;
}

.navbar ul {
  list-style: none;
  display: flex;
  justify-content: center;
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
</style>
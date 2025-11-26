<template>
  <nav class="navbar">
    <ul>
      <li><router-link to="/">首頁</router-link></li>
      <li><router-link to="/items">物品頁面</router-link></li>
      <li><router-link to="/profile">個人資料</router-link></li>
      <li><router-link to="/car">購物車</router-link></li>

    </ul>
  </nav>
</template>

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
</style>

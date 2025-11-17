<template>
  <div :class="theme">
    <div class="theme-toggle">
      <button @click="setTheme('light')" :class="{ active: theme === 'light' }">☀️ </button>
      <button @click="setTheme('dark')" :class="{ active: theme === 'dark' }">🌙 </button>
    </div>
    <Navbar />
    <router-view/>
  </div>
</template>

<script>
import Navbar from './components/Navbar.vue'
export default {
  components: { Navbar },
  data () {
    return {
      theme: localStorage.getItem('theme') || 'light'
    }
  },
  watch: {
    theme (val) {
      localStorage.setItem('theme', val)
    }
  },
  methods: {
    setTheme (t) {
      this.theme = t
    }
  }
}
</script>

<style lang="scss">
html, body {
  height: 100%;
  margin: 0;
  padding: 0;
  background: var(--main-bg);
}
:root {
  --main-bg: #fff;
  --main-text: #2c3e50;
  --main-card: #fff;
}
.dark {
  --main-bg: #181818;
  --main-text: #f5f5f5;
  --main-card: #232323;
}
.light {
  --main-bg: #fff;
  --main-text: #2c3e50;
  --main-card: #fff;
}
#app, .light, .dark {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  background: var(--main-bg);
  color: var(--main-text);
  min-height: 100vh;
  transition: background 0.3s, color 0.3s;
}
.theme-toggle {
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
  padding: 1rem 2rem 0 0;
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

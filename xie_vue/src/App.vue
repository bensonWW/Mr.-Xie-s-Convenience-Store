<template>
  <div :class="theme">
    <Navbar :theme="theme" @set-theme="setTheme" />
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
</style>

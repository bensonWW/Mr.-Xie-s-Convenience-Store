<template>
  <div :class="theme" class="min-h-screen flex flex-col">
    <AppHeader />
    <router-view class="flex-grow"/>
    <AppFooter />
  </div>
</template>

<script>
import AppHeader from './components/AppHeader.vue'
import AppFooter from './components/AppFooter.vue'

export default {
  components: { AppHeader, AppFooter },
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
  },
  created () {
    this.$store.dispatch('checkAuth')
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
  --main-bg: #f7fafc; /* bg-gray-100 */
  --main-text: #2d3748;
}
.dark {
  --main-bg: #1a202c;
  --main-text: #f7fafc;
}
#app, .light, .dark {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  background: var(--main-bg);
  color: var(--main-text);
  min-height: 100vh;
  transition: background 0.3s, color 0.3s;
}
</style>

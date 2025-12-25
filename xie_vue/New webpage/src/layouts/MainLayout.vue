<template>
  <div class="min-h-screen bg-stone-50 dark:bg-slate-900 text-stone-800 dark:text-stone-100 font-sans flex flex-col transition-colors duration-300">
    <NavBar />
    <main class="flex-grow">
      <router-view />
    </main>
    <FooterBar />
  </div>
</template>

<script setup>
import NavBar from '../components/NavBar.vue';
import FooterBar from '../components/FooterBar.vue';
import { initDarkMode } from '../utils/darkMode.js';
import { onMounted } from 'vue';

onMounted(() => {
  initDarkMode();
});
</script>


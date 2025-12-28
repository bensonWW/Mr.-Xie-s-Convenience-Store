<template>
  <div class="min-h-screen flex flex-col bg-[#d9cdb4] dark:bg-slate-900 text-slate-700 dark:text-stone-100 transition-colors duration-300 relative">
    <!-- Wood texture overlay -->
    <div 
      class="fixed inset-0 pointer-events-none z-0 opacity-[0.35] dark:opacity-0"
      style="background-image: url('https://www.transparenttextures.com/patterns/light-wood.png');"
    ></div>
    
    <AppHeader @open-cart="showCartDrawer = true" class="relative z-10" />
    <router-view class="flex-grow relative z-10"/>
    <AppFooter class="relative z-10" />
    
    <!-- Cart Drawer -->
    <CartDrawer :visible="showCartDrawer" @close="showCartDrawer = false" />
  </div>
</template>

<script>
import AppHeader from './components/AppHeader.vue'
import AppFooter from './components/AppFooter.vue'
import CartDrawer from './components/CartDrawer.vue'
import { useAuthStore } from '@/stores/auth'
import { initDarkMode } from '@/utils/darkMode'

export default {
  components: { AppHeader, AppFooter, CartDrawer },
  data () {
    return {
      showCartDrawer: false
    }
  },
  created () {
    // Initialize dark mode
    initDarkMode()
    
    // Initialize auth session
    const authStore = useAuthStore()
    authStore.fetchUser()
  }
}
</script>

<style lang="scss">
// Global Styles - Japanese Minimalist Design System

// Base styles
html, body {
  height: 100%;
  margin: 0;
  padding: 0;
}

#app {
  font-family: "Noto Sans TC", Inter, system-ui, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  min-height: 100vh;
}

// Functional colors for alerts/status
.text-success { @apply text-emerald-600; }
.dark .text-success { @apply text-emerald-400; }

.text-error { @apply text-rose-500; }
.dark .text-error { @apply text-rose-400; }

.text-warning { @apply text-amber-500; }
.dark .text-warning { @apply text-amber-400; }

.text-info { @apply text-sky-500; }
.dark .text-info { @apply text-sky-400; }

// Toast Notification Styling - Japanese Aesthetic
.Vue-Toastification__toast {
  font-family: "Noto Sans TC", Inter, system-ui, sans-serif;
  border-radius: 0.5rem;
  padding: 0.75rem 1rem;
  font-size: 0.875rem;
  font-weight: 500;
  box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
}

.Vue-Toastification__toast--success {
  background-color: #ecfdf5 !important;
  color: #059669 !important;
  border: 1px solid #a7f3d0 !important;
}

.Vue-Toastification__toast--error {
  background-color: #fff1f2 !important;
  color: #e11d48 !important;
  border: 1px solid #fecdd3 !important;
}

.Vue-Toastification__toast--warning {
  background-color: #fffbeb !important;
  color: #d97706 !important;
  border: 1px solid #fde68a !important;
}

.Vue-Toastification__toast--info {
  background-color: #f0f9ff !important;
  color: #0284c7 !important;
  border: 1px solid #bae6fd !important;
}

// Dark mode Toast overrides
.dark .Vue-Toastification__toast--success {
  background-color: rgba(6, 78, 59, 0.3) !important;
  color: #6ee7b7 !important;
  border-color: rgba(16, 185, 129, 0.3) !important;
}

.dark .Vue-Toastification__toast--error {
  background-color: rgba(136, 19, 55, 0.3) !important;
  color: #fda4af !important;
  border-color: rgba(244, 63, 94, 0.3) !important;
}

.dark .Vue-Toastification__toast--warning {
  background-color: rgba(120, 53, 15, 0.3) !important;
  color: #fcd34d !important;
  border-color: rgba(245, 158, 11, 0.3) !important;
}

.dark .Vue-Toastification__toast--info {
  background-color: rgba(7, 89, 133, 0.3) !important;
  color: #7dd3fc !important;
  border-color: rgba(14, 165, 233, 0.3) !important;
}

// Smooth transitions for theme switching
* {
  transition-property: background-color, border-color;
  transition-duration: 150ms;
  transition-timing-function: ease-in-out;
}

// Animation utilities
.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}

// Japanese typography - subtle letter spacing for elegance
h1, h2, .font-serif {
  letter-spacing: 0.05em;
  text-rendering: optimizeLegibility;
  -webkit-font-smoothing: antialiased;
}

// Vertical text utility for Japanese aesthetic
.vertical-text {
  writing-mode: vertical-rl;
  text-orientation: mixed;
  font-family: serif;
}

// Subtle paper texture background overlay
.bg-texture {
  position: relative;
  
  &::before {
    content: "";
    position: absolute;
    inset: 0;
    opacity: 0.03;
    pointer-events: none;
    background-image: url('https://www.transparenttextures.com/patterns/natural-paper.png');
    z-index: 0;
  }
}

// Wood grain texture overlay
.bg-wood-texture {
  position: relative;
  
  &::before {
    content: "";
    position: absolute;
    inset: 0;
    opacity: 0.015;
    pointer-events: none;
    background-image: url('https://www.transparenttextures.com/patterns/light-wood.png');
    z-index: 0;
  }
}
</style>

<template>
  <div 
    class="bg-white dark:bg-slate-800 border border-stone-200 dark:border-slate-700 rounded-lg transition-colors duration-300"
    :class="[
      padding,
      hoverable ? 'hover:border-stone-300 dark:hover:border-slate-600 hover:-translate-y-0.5 transition-transform' : '',
      className
    ]"
    v-bind="$attrs"
  >
    <slot />
  </div>
</template>

<script setup>
defineOptions({
  inheritAttrs: false
})

defineProps({
  padding: {
    type: String,
    default: 'p-6'
  },
  hoverable: {
    type: Boolean,
    default: false
  },
  className: {
    type: String,
    default: ''
  }
})
</script>

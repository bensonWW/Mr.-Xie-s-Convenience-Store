<template>
  <button
    :class="[
      'inline-flex items-center justify-center font-medium rounded-md transition-all duration-200',
      sizeClasses,
      variantClasses,
      { 'opacity-50 cursor-not-allowed': disabled },
      className
    ]"
    :disabled="disabled"
    v-bind="$attrs"
  >
    <i v-if="icon && !loading" :class="[icon, 'mr-2']"></i>
    <i v-if="loading" class="fas fa-spinner fa-spin mr-2"></i>
    <slot />
  </button>
</template>

<script setup>
import { computed } from 'vue'

defineOptions({
  inheritAttrs: false
})

const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: v => ['primary', 'secondary', 'danger', 'ghost'].includes(v)
  },
  size: {
    type: String,
    default: 'md',
    validator: v => ['sm', 'md', 'lg'].includes(v)
  },
  disabled: {
    type: Boolean,
    default: false
  },
  loading: {
    type: Boolean,
    default: false
  },
  icon: {
    type: String,
    default: ''
  },
  className: {
    type: String,
    default: ''
  }
})

const sizeClasses = computed(() => {
  const sizes = {
    sm: 'px-3 py-1.5 text-sm',
    md: 'px-6 py-3 text-sm',
    lg: 'px-8 py-4 text-base'
  }
  return sizes[props.size]
})

const variantClasses = computed(() => {
  const variants = {
    primary: 'bg-xieOrange text-white hover:bg-[#cf8354] shadow-sm shadow-xieOrange/20',
    secondary: 'bg-white dark:bg-transparent border-2 border-slate-700 dark:border-stone-300 text-slate-700 dark:text-stone-200 hover:bg-slate-50 dark:hover:bg-slate-700',
    danger: 'bg-rose-500 text-white hover:bg-rose-600 shadow-sm shadow-rose-500/20',
    ghost: 'bg-transparent text-slate-700 dark:text-stone-200 hover:bg-stone-100 dark:hover:bg-slate-700'
  }
  return variants[props.variant]
})
</script>

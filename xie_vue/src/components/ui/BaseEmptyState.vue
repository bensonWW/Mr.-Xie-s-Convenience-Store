<template>
  <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
    <!-- Icon -->
    <div 
      class="w-20 h-20 rounded-full flex items-center justify-center mb-6"
      :class="iconBgClass"
    >
      <i :class="[icon, 'text-3xl', iconColorClass]"></i>
    </div>
    
    <!-- Title -->
    <h3 class="text-lg font-semibold text-slate-700 dark:text-stone-100 mb-2">
      {{ title }}
    </h3>
    
    <!-- Description -->
    <p class="text-sm text-stone-500 dark:text-stone-400 max-w-sm mb-6 leading-relaxed">
      {{ description }}
    </p>
    
    <!-- Action Slot -->
    <slot name="action" />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: {
    type: String,
    default: '目前沒有資料'
  },
  description: {
    type: String,
    default: ''
  },
  icon: {
    type: String,
    default: 'fas fa-inbox'
  },
  variant: {
    type: String,
    default: 'default',
    validator: v => ['default', 'search', 'cart', 'order', 'wishlist'].includes(v)
  }
})

const iconBgClass = computed(() => {
  const variants = {
    default: 'bg-stone-100 dark:bg-slate-700',
    search: 'bg-sky-50 dark:bg-sky-900/20',
    cart: 'bg-amber-50 dark:bg-amber-900/20',
    order: 'bg-emerald-50 dark:bg-emerald-900/20',
    wishlist: 'bg-rose-50 dark:bg-rose-900/20'
  }
  return variants[props.variant]
})

const iconColorClass = computed(() => {
  const variants = {
    default: 'text-stone-400 dark:text-stone-500',
    search: 'text-sky-400 dark:text-sky-500',
    cart: 'text-amber-400 dark:text-amber-500',
    order: 'text-emerald-400 dark:text-emerald-500',
    wishlist: 'text-rose-400 dark:text-rose-500'
  }
  return variants[props.variant]
})
</script>

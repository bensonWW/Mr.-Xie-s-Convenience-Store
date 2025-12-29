<template>
  <div class="w-full">
    <label 
      v-if="label" 
      :for="inputId"
      class="block text-sm font-semibold text-slate-700 dark:text-stone-100 mb-2"
    >
      {{ label }}
      <span v-if="required" class="text-rose-500">*</span>
    </label>
    
    <div class="relative">
      <div v-if="icon" class="absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 dark:text-stone-500">
        <i :class="icon"></i>
      </div>
      
      <input
        :id="inputId"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :required="required"
        class="w-full bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-md py-3 px-4 text-slate-700 dark:text-stone-200 placeholder:text-stone-400 dark:placeholder:text-stone-500 focus:outline-none focus:border-xieOrange focus:ring-1 focus:ring-xieOrange transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        :class="[
          icon ? 'pl-10' : '',
          error ? 'border-rose-500 dark:border-rose-400 focus:border-rose-500 focus:ring-rose-500' : '',
          className
        ]"
        @input="$emit('update:modelValue', $event.target.value)"
        v-bind="$attrs"
      >
    </div>
    
    <p v-if="error" class="mt-1 text-sm text-rose-500 dark:text-rose-400">
      {{ error }}
    </p>
    <p v-else-if="hint" class="mt-1 text-xs text-stone-500 dark:text-stone-400">
      {{ hint }}
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

defineOptions({
  inheritAttrs: false
})

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
  },
  icon: {
    type: String,
    default: ''
  },
  error: {
    type: String,
    default: ''
  },
  hint: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  },
  required: {
    type: Boolean,
    default: false
  },
  className: {
    type: String,
    default: ''
  }
})

defineEmits(['update:modelValue'])

const inputId = computed(() => `input-${Math.random().toString(36).substr(2, 9)}`)
</script>

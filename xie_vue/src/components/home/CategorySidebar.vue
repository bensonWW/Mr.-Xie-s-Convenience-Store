<template>
  <aside class="hidden lg:block col-span-2 bg-white rounded-lg shadow-sm overflow-hidden h-fit">
    <div class="bg-xieBlue text-white px-4 py-3 font-bold flex items-center">
      <i class="fas fa-bars mr-2"></i> 商品分類
    </div>
    <ul class="text-sm text-gray-700 divide-y divide-gray-100">
      <li
        v-for="cat in validCategories"
        :key="cat.id"
        class="hover:bg-orange-50 hover:text-xieOrange cursor-pointer px-4 py-3 flex justify-between group"
        @click="$emit('select-category', cat)"
      >
        <span><i class="fas fa-tag w-6 text-center text-gray-400 group-hover:text-xieOrange"></i> {{ cat.displayName }}</span>
        <i class="fas fa-chevron-right text-xs mt-1"></i>
      </li>
    </ul>
  </aside>
</template>

<script>
export default {
  name: 'CategorySidebar',
  props: {
    categories: {
      type: Array,
      required: true
    }
  },
  emits: ['select-category'],
  computed: {
    validCategories () {
      return this.categories
        .map((cat, index) => {
          // Handle both object and string formats
          if (typeof cat === 'string') {
            return { id: index, name: cat, displayName: cat }
          }
          // Extract name safely - if name is object, try to get string representation
          let displayName = cat.name
          if (typeof displayName === 'object' && displayName !== null) {
            displayName = displayName.name || displayName.label || JSON.stringify(displayName)
          }
          return {
            ...cat,
            displayName: displayName || `Category ${cat.id}`
          }
        })
        .filter(cat => cat.displayName && typeof cat.displayName === 'string' && !cat.displayName.includes('[object'))
    }
  }
}
</script>

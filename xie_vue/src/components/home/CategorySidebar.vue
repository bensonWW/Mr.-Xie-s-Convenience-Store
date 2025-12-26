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
          // Handle string format (legacy)
          if (typeof cat === 'string') {
            return { id: index, name: cat, displayName: cat }
          }
          
          // Handle object format from API
          if (typeof cat === 'object' && cat !== null) {
            // Direct name property
            const name = cat.name || cat.label || cat.title || ''
            return {
              id: cat.id || index,
              name: name,
              slug: cat.slug || '',
              displayName: name
            }
          }
          
          // Fallback
          return { id: index, name: String(cat), displayName: String(cat) }
        })
        .filter(cat => cat.displayName && cat.displayName.length > 0)
    }
  }
}
</script>

<template>
  <div v-if="hasVariants" class="variant-selector space-y-4">
    <!-- Attribute Selectors -->
    <div v-for="attr in attributes" :key="attr.id" class="attribute-group">
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        {{ attr.name }}
      </label>
      <div class="flex flex-wrap gap-2">
        <button
          v-for="value in attr.values"
          :key="value.id"
          @click="selectOption(attr.id, value.id)"
          :disabled="!isValueAvailable(attr.id, value.id)"
          :class="getButtonClasses(attr.id, value.id, value.color_code)"
        >
          <!-- Color swatch for color attributes -->
          <span
            v-if="value.color_code"
            class="inline-block w-5 h-5 rounded-full border border-gray-300 dark:border-gray-600 mr-2"
            :style="{ backgroundColor: value.color_code }"
          ></span>
          {{ value.value }}
          <!-- Out of stock indicator -->
          <span
            v-if="!isInStock(attr.id, value.id)"
            class="text-xs text-gray-400 ml-1"
          >(缺貨)</span>
        </button>
      </div>
    </div>

    <!-- Selected Variant Info -->
    <div v-if="selectedVariant" class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
      <div class="flex items-center justify-between">
        <div>
          <div class="text-sm text-gray-500 dark:text-gray-400">已選規格</div>
          <div class="font-medium text-gray-900 dark:text-white">{{ selectedVariant.options_text }}</div>
        </div>
        <div class="text-right">
          <div v-if="selectedVariant.original_price && selectedVariant.original_price > selectedVariant.price" class="text-sm text-gray-400 line-through">
            ${{ formatPrice(selectedVariant.original_price) }}
          </div>
          <div class="text-xl font-bold text-xieOrange">
            ${{ formatPrice(selectedVariant.price) }}
          </div>
        </div>
      </div>
      <div class="mt-2 flex items-center justify-between text-sm">
        <span :class="selectedVariant.stock > 0 ? 'text-green-600' : 'text-red-500'">
          {{ selectedVariant.stock > 0 ? `庫存 ${selectedVariant.stock} 件` : '缺貨中' }}
        </span>
        <span class="text-gray-400">SKU: {{ selectedVariant.sku }}</span>
      </div>
    </div>

    <!-- No valid selection warning -->
    <div v-else-if="Object.keys(selectedOptions).length > 0" class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl text-yellow-700 dark:text-yellow-300 text-sm">
      <i class="fas fa-exclamation-triangle mr-2"></i>
      此規格組合暫無庫存，請選擇其他規格
    </div>
  </div>
</template>

<script>
export default {
  name: 'VariantSelector',
  props: {
    // Product attributes with values
    attributes: {
      type: Array,
      required: true,
      default: () => []
    },
    // All variants for the product
    variants: {
      type: Array,
      required: true,
      default: () => []
    },
    // Valid option combinations (all combos that exist)
    validCombinations: {
      type: Array,
      default: () => []
    },
    // In-stock combinations (combos that are available)
    inStockCombinations: {
      type: Array,
      default: () => []
    },
    // Default selected variant
    defaultVariant: {
      type: Object,
      default: null
    }
  },
  emits: ['variant-selected', 'selection-changed'],
  data() {
    return {
      selectedOptions: {} // { attribute_id: value_id }
    }
  },
  computed: {
    hasVariants() {
      return this.attributes.length > 0 && this.variants.length > 0
    },
    selectedVariant() {
      if (Object.keys(this.selectedOptions).length !== this.attributes.length) {
        return null
      }
      
      return this.variants.find(v => {
        const variantOptions = v.options
        return Object.keys(this.selectedOptions).every(attrId => 
          String(variantOptions[attrId]) === String(this.selectedOptions[attrId])
        )
      })
    }
  },
  watch: {
    selectedVariant: {
      handler(newVariant) {
        this.$emit('variant-selected', newVariant)
      },
      immediate: true
    },
    selectedOptions: {
      handler(newOptions) {
        this.$emit('selection-changed', newOptions)
      },
      deep: true
    },
    defaultVariant: {
      handler(newDefault) {
        if (newDefault && Object.keys(this.selectedOptions).length === 0) {
          this.initializeFromDefault()
        }
      },
      immediate: true
    }
  },
  methods: {
    initializeFromDefault() {
      if (this.defaultVariant && this.defaultVariant.options) {
        this.selectedOptions = { ...this.defaultVariant.options }
      }
    },
    
    selectOption(attrId, valueId) {
      // Create a new selection with this option
      const newOptions = { ...this.selectedOptions, [attrId]: valueId }
      
      // Check if this creates a valid combination path
      // Don't disable selections - let user explore
      this.selectedOptions = newOptions
    },
    
    /**
     * Check if a value is available (exists in any valid combination)
     */
    isValueAvailable(attrId, valueId) {
      // A value is available if there exists at least one valid combination
      // that includes this value, regardless of current selections
      return this.validCombinations.some(combo => 
        String(combo[attrId]) === String(valueId)
      )
    },
    
    /**
     * Check if selecting this value would result in an in-stock combo
     */
    isInStock(attrId, valueId) {
      const testOptions = { ...this.selectedOptions, [attrId]: valueId }
      
      // Check if any in-stock combination matches
      return this.inStockCombinations.some(combo => {
        return Object.keys(testOptions).every(key => 
          String(combo[key]) === String(testOptions[key])
        )
      })
    },
    
    /**
     * Get button CSS classes based on selection state
     */
    getButtonClasses(attrId, valueId, colorCode) {
      const isSelected = String(this.selectedOptions[attrId]) === String(valueId)
      const isAvailable = this.isValueAvailable(attrId, valueId)
      const inStock = this.isInStock(attrId, valueId)
      
      const base = 'px-4 py-2 rounded-lg border transition-all duration-200 text-sm font-medium flex items-center'
      
      if (!isAvailable) {
        return `${base} border-gray-200 dark:border-gray-700 text-gray-300 dark:text-gray-600 cursor-not-allowed bg-gray-50 dark:bg-gray-800`
      }
      
      if (isSelected) {
        return `${base} border-xieOrange bg-xieOrange text-white shadow-md`
      }
      
      if (!inStock) {
        return `${base} border-gray-300 dark:border-gray-600 text-gray-400 dark:text-gray-500 hover:border-gray-400 dark:hover:border-gray-500 bg-white dark:bg-gray-800`
      }
      
      return `${base} border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-xieOrange hover:text-xieOrange bg-white dark:bg-gray-800`
    },
    
    formatPrice(cents) {
      return (cents / 100).toLocaleString()
    },
    
    /**
     * Reset selection to default
     */
    reset() {
      this.selectedOptions = {}
      if (this.defaultVariant) {
        this.initializeFromDefault()
      }
    }
  }
}
</script>

<style scoped>
.variant-selector {
  /* Add any custom styles here */
}
</style>

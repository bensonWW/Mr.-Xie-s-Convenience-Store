<template>
  <div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800 flex items-center">
          我的收藏 <span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-full ml-2">{{ wishlist.length }}</span>
        </h2>
        <button class="text-sm text-red-500 hover:underline" @click="clearInvalidWishlistItems"><i class="fas fa-trash-alt mr-1"></i> 清空失效商品</button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <div v-for="item in wishlist" :key="item.id"
             class="border border-gray-200 rounded-lg p-4 relative hover:shadow-lg transition group"
             :class="item.status === 'available' ? 'bg-white' : 'bg-gray-50 opacity-90'">

          <button class="absolute top-3 right-3 text-gray-300 hover:text-red-500 z-10 p-1" @click="removeFromWishlist(item.id)">
            <i class="fas" :class="item.status === 'available' ? 'fa-times text-lg' : 'fa-trash-alt'"></i>
          </button>

          <a href="#" class="block relative mb-3">
            <div class="w-full h-40 bg-gray-100 rounded flex items-center justify-center text-gray-300 overflow-hidden relative">
              <i :class="[item.icon_class + ' text-5xl', {'opacity-50': item.status !== 'available'}]"></i>
              <div v-if="item.status !== 'available'" class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center">
                <span class="bg-gray-800 text-white px-3 py-1 text-sm rounded">補貨中</span>
              </div>
            </div>
            <div v-if="item.tag" class="absolute bottom-0 left-0 bg-red-500 text-white text-xs px-2 py-1 rounded-tr">
              <i class="fas fa-arrow-down mr-1"></i>{{ item.tag }}
            </div>
          </a>

          <h3 class="font-bold text-sm line-clamp-2 h-10 mb-2" :class="item.status === 'available' ? 'text-gray-800 group-hover:text-xieOrange' : 'text-gray-500'">
            <a href="#">{{ item.name }}</a>
          </h3>
          <div class="flex items-end gap-2 mb-4">
            <span class="text-xl font-bold" :class="item.status === 'available' ? 'text-xieOrange' : 'text-gray-500'">NT$ {{ item.price.toLocaleString() }}</span>
            <span v-if="item.original_price" class="text-xs text-gray-400 line-through mb-1">NT$ {{ item.original_price.toLocaleString() }}</span>
          </div>

          <button v-if="item.status === 'available'" class="w-full bg-xieOrange text-white py-2 rounded font-bold hover:bg-orange-600 transition flex items-center justify-center gap-2" @click="addToCartFromWishlist(item)">
            <i class="fas fa-cart-plus"></i> 加入購物車
          </button>
          <button v-else class="w-full border border-gray-400 text-gray-600 py-2 rounded font-bold hover:bg-gray-200 transition flex items-center justify-center gap-2">
            <i class="far fa-bell"></i> 到貨通知
          </button>
        </div>

      </div>

      <div class="mt-8 flex justify-center space-x-2">
        <a href="#" class="px-3 py-2 bg-white border border-gray-300 text-gray-500 rounded hover:bg-gray-100"><i class="fas fa-chevron-left"></i></a>
        <a href="#" class="px-3 py-2 bg-xieOrange border border-xieOrange text-white rounded font-bold">1</a>
        <a href="#" class="px-3 py-2 bg-white border border-gray-300 text-gray-500 rounded hover:bg-gray-100"><i class="fas fa-chevron-right"></i></a>
      </div>
    </div>
  </div>
</template>

<script>
import { useToast } from 'vue-toastification'

export default {
  name: 'WishlistGrid',
  props: {
    wishlist: {
      type: Array,
      default: () => []
    }
  },
  emits: ['update:wishlist'],
  setup () {
    const toast = useToast()
    return { toast }
  },
  methods: {
    removeFromWishlist (id) {
      if (confirm('確定要將此商品移出追蹤清單嗎？')) {
        const newList = this.wishlist.filter(item => item.id !== id)
        this.$emit('update:wishlist', newList)
        this.toast.info('已移除商品')
      }
    },
    addToCartFromWishlist (item) {
      this.toast.success(`已將 ${item.name} 加入購物車！`)
      // API call to add to cart
    },
    clearInvalidWishlistItems () {
      if (confirm('確定要清空所有失效商品嗎？')) {
        const newList = this.wishlist.filter(item => item.status !== 'out_of_stock')
        this.$emit('update:wishlist', newList)
        this.toast.info('已清空失效商品')
      }
    }
  }
}
</script>

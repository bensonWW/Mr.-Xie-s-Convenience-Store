<template>
  <div v-show="visible" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition-opacity">
    <div class="bg-white rounded-lg shadow-xl w-96 p-6 transform transition-all">
      <h3 class="text-xl font-bold text-gray-800 mb-4">
        <i class="fas fa-map-marker-alt text-xieOrange mr-2"></i> 請設定收貨地址
      </h3>
      <p class="text-gray-500 text-sm mb-4">
        為了順利配送您的商品，請先前往個人資料頁面設定您的收貨地址。
      </p>

      <div class="bg-orange-50 border border-orange-200 rounded p-3 mb-4 text-sm text-orange-800">
        <i class="fas fa-info-circle mr-1"></i>
        您需要填寫完整的結構化地址（縣市、區域、郵遞區號、詳細地址）
      </div>

      <div class="flex justify-end gap-2">
          <button type="button" @click="$emit('close')" class="px-4 py-2 text-gray-500 hover:bg-gray-100 rounded">
              稍後再說
          </button>
          <button type="button" @click="goToProfile" class="px-6 py-2 bg-xieOrange text-white font-bold rounded hover:bg-orange-600 transition">
              <i class="fas fa-user-edit mr-1"></i> 前往設定
          </button>
      </div>
    </div>
  </div>
</template>

<script>
import { useRouter } from 'vue-router'

export default {
  name: 'InlineAddressModal',
  props: {
    visible: Boolean
  },
  emits: ['close', 'success'],
  setup () {
    const router = useRouter()
    return { router }
  },
  methods: {
    goToProfile () {
      this.$emit('close')
      this.router.push('/profile')
    }
  }
}
</script>

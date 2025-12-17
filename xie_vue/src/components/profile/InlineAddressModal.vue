<template>
  <div v-show="visible" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition-opacity">
    <div class="bg-white rounded-lg shadow-xl w-96 p-6 transform transition-all">
      <h3 class="text-xl font-bold text-gray-800 mb-4">
        <i class="fas fa-map-marker-alt text-xieOrange mr-2"></i> 請填寫收貨地址
      </h3>
      <p class="text-gray-500 text-sm mb-4">
        為了順利配送您的商品，請在結帳前設定您的收貨地址。
      </p>

      <form @submit.prevent="saveAddress">
        <div class="mb-4">
          <textarea
            v-model="address"
            rows="3"
            placeholder="請輸入詳細地址..."
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-xieOrange focus:ring-1 focus:ring-xieOrange"
            required
          ></textarea>
        </div>

        <div class="flex justify-end gap-2">
            <button type="button" @click="$emit('close')" class="px-4 py-2 text-gray-500 hover:bg-gray-100 rounded">
                稍後再說
            </button>
            <button type="submit" class="px-6 py-2 bg-xieOrange text-white font-bold rounded hover:bg-orange-600 transition" :disabled="loading">
                <i v-if="loading" class="fas fa-spinner fa-spin mr-1"></i>
                {{ loading ? '儲存中...' : '確認並繼續' }}
            </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import api from '../../services/api'
import { useToast } from 'vue-toastification'

export default {
  name: 'InlineAddressModal',
  props: {
    visible: Boolean
  },
  emits: ['close', 'success'],
  setup () {
    const toast = useToast()
    return { toast }
  },
  data () {
    return {
      address: '',
      loading: false
    }
  },
  methods: {
    async saveAddress () {
      if (!this.address.trim()) return
      this.loading = true
      try {
        const res = await api.put('/profile', { address: this.address })
        this.$emit('success', this.address)
        this.$emit('close')
      } catch (error) {
        console.error(error)
        this.toast.error('儲存失敗，請稍後再試')
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

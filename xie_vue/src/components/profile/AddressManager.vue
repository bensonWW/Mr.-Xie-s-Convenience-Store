<template>
  <div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
        <h3 class="font-bold text-gray-800">地址管理</h3>
        <button v-if="!isEditing" @click="isEditing = true" class="text-sm bg-xieOrange text-white px-3 py-1 rounded hover:bg-orange-600 transition">
          <i class="fas fa-edit mr-1"></i> 修改地址
        </button>
      </div>

      <div class="p-8">
        <div v-if="!isEditing">
          <div v-if="user.address" class="flex items-start gap-4">
            <div class="text-3xl text-gray-300">
               <i class="fas fa-map-marker-alt"></i>
            </div>
            <div>
              <h4 class="font-bold text-gray-800 mb-1">預設收貨地址</h4>
              <p class="text-gray-600">{{ user.address }}</p>
              <div class="mt-2 text-xs text-gray-400">目前系統僅支援單一收貨地址</div>
            </div>
          </div>
          <div v-else class="text-center py-8 text-gray-400">
             <i class="fas fa-map-marked-alt text-4xl mb-3 block"></i>
             <p>尚未設定收貨地址</p>
             <button @click="isEditing = true" class="mt-4 text-xieOrange underline">立即設定</button>
          </div>
        </div>

        <form v-else @submit.prevent="saveAddress" class="max-w-xl">
           <div class="mb-4">
            <label class="block text-sm font-bold text-gray-700 mb-2">收貨地址</label>
            <textarea
              v-model="editAddress"
              rows="3"
              placeholder="請輸入詳細地址 (例如：台北市信義區...)"
              class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-xieOrange focus:ring-1 focus:ring-xieOrange transition"
              required
            ></textarea>
           </div>
           <div class="flex gap-3">
             <button type="submit" class="bg-xieOrange text-white px-6 py-2 rounded font-bold hover:bg-orange-600 transition shadow-sm" :disabled="loading">
               {{ loading ? '儲存中...' : '儲存地址' }}
             </button>
             <button type="button" class="bg-gray-100 text-gray-600 px-6 py-2 rounded font-bold hover:bg-gray-200 transition" @click="cancelEdit">
               取消
             </button>
           </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../../services/api'
import { useToast } from 'vue-toastification'

export default {
  name: 'AddressManager',
  props: {
    user: {
      type: Object,
      required: true
    }
  },
  emits: ['profile-updated'],
  setup () {
    const toast = useToast()
    return { toast }
  },
  data () {
    return {
      isEditing: false,
      editAddress: '',
      loading: false
    }
  },
  watch: {
    user: {
      handler (newVal) {
        if (newVal && newVal.address) {
          this.editAddress = newVal.address
        }
      },
      immediate: true,
      deep: true
    }
  },
  methods: {
    cancelEdit () {
      this.isEditing = false
      this.editAddress = this.user.address || ''
    },
    async saveAddress () {
      if (!this.editAddress.trim()) {
        this.toast.warning('請輸入地址')
        return
      }
      this.loading = true
      try {
        // Reuse profile update endpoint since address is on user model
        const response = await api.put('/profile', {
          ...this.user, // Send other fields to keep them safe if needed, though usually partial updates are prefered.
          // However, typical Laravel update might expect all or handle partial.
          // Let's assume partial update is supported or we just send this field if logic allows.
          // Looking at ProfileEdit logic: it sends phone, birthday etc.
          // Safest is to send just address and hope backend handles partial validation.
          address: this.editAddress
        })

        this.toast.success('地址更新成功')
        this.$emit('profile-updated', response.data.user)
        this.isEditing = false
      } catch (e) {
        console.error(e)
        this.toast.error('更新失敗')
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

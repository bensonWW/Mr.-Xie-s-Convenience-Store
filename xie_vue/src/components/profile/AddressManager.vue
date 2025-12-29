<template>
  <div class="space-y-6">
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-stone-100 dark:border-slate-700 overflow-hidden transition-colors duration-300">
      <!-- Header -->
      <div class="px-6 py-4 border-b border-stone-100 dark:border-slate-700 flex justify-between items-center">
        <h3 class="font-semibold text-slate-700 dark:text-stone-100">地址管理</h3>
        <button 
          v-if="!isEditing" 
          @click="isEditing = true" 
          class="text-sm bg-xieOrange text-white px-4 py-1.5 rounded-full font-medium hover:bg-[#cf8354] transition"
        >
          <i class="fas fa-edit mr-1"></i> 修改地址
        </button>
      </div>

      <div class="p-8">
        <!-- Display Mode -->
        <div v-if="!isEditing">
          <div v-if="user.addresses && user.addresses.length > 0" class="flex items-start gap-4">
            <div class="w-12 h-12 bg-xieOrange/10 rounded-xl flex items-center justify-center text-xieOrange">
              <i class="fas fa-map-marker-alt text-xl"></i>
            </div>
            <div>
              <h4 class="font-medium text-slate-700 dark:text-stone-100 mb-1">預設收貨地址</h4>
              <p class="text-stone-600 dark:text-stone-300 font-medium mb-1">{{ user.addresses[0].recipient_name }} {{ user.addresses[0].phone }}</p>
              <p class="text-stone-500 dark:text-stone-400">{{ user.addresses[0].zip_code }} {{ user.addresses[0].city }}{{ user.addresses[0].district }}{{ user.addresses[0].detail_address }}</p>
            </div>
          </div>
          <div v-else class="text-center py-12 text-stone-400 dark:text-stone-500">
            <i class="fas fa-map-marked-alt text-4xl mb-3 opacity-50"></i>
            <p class="mb-4">尚未設定收貨地址</p>
            <button @click="isEditing = true" class="text-xieOrange font-medium hover:underline">立即設定</button>
          </div>
        </div>

        <!-- Edit Mode -->
        <form v-else @submit.prevent="saveAddress" class="max-w-xl space-y-5">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">收件人姓名</label>
              <input 
                v-model="form.recipient_name" 
                type="text" 
                class="w-full bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-xieOrange focus:ring-2 focus:ring-xieOrange/30 text-slate-700 dark:text-stone-100 transition" 
                required
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">聯絡電話</label>
              <input 
                v-model="form.phone" 
                type="tel" 
                class="w-full bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-xieOrange focus:ring-2 focus:ring-xieOrange/30 text-slate-700 dark:text-stone-100 transition" 
                required
              >
            </div>
          </div>

          <div class="grid grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">縣市</label>
              <select 
                v-model="form.city" 
                class="w-full bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-xieOrange text-slate-700 dark:text-stone-100 transition" 
                required
              >
                <option value="" disabled>請選擇</option>
                <option v-for="c in taiwanDistricts" :key="c.city" :value="c.city">{{ c.city }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">鄉鎮市區</label>
              <select 
                v-model="form.district" 
                class="w-full bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-xieOrange text-slate-700 dark:text-stone-100 transition" 
                :disabled="!form.city" 
                required
              >
                <option value="" disabled>請選擇</option>
                <option v-for="d in availableDistricts" :key="d.name" :value="d.name">{{ d.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">郵遞區號</label>
              <input 
                v-model="form.zip_code" 
                type="text" 
                class="w-full bg-stone-100 dark:bg-slate-600 border border-stone-200 dark:border-slate-500 rounded-xl px-4 py-3 text-stone-500 dark:text-stone-400" 
                readonly
              >
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">詳細地址</label>
            <input
              v-model="form.detail_address"
              type="text"
              placeholder="街道、巷弄、門牌號碼..."
              class="w-full bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-xieOrange focus:ring-2 focus:ring-xieOrange/30 text-slate-700 dark:text-stone-100 placeholder:text-stone-400 transition"
              required
            >
          </div>

          <div class="flex gap-3 pt-4">
            <button 
              type="submit" 
              class="bg-xieOrange text-white px-6 py-2.5 rounded-full font-medium hover:bg-[#cf8354] transition shadow-lg shadow-xieOrange/20" 
              :disabled="loading"
            >
              {{ loading ? '儲存中...' : '儲存地址' }}
            </button>
            <button 
              type="button" 
              class="border border-stone-300 dark:border-slate-500 text-stone-600 dark:text-stone-300 px-6 py-2.5 rounded-full font-medium hover:bg-stone-100 dark:hover:bg-slate-600 transition" 
              @click="cancelEdit"
            >
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
import taiwanDistricts from '../../assets/taiwan_districts.json'

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
      loading: false,
      taiwanDistricts,
      form: {
        recipient_name: '',
        phone: '',
        city: '',
        district: '',
        zip_code: '',
        detail_address: ''
      }
    }
  },
  computed: {
    availableDistricts () {
      if (!this.form.city) return []
      const cityData = this.taiwanDistricts.find(c => c.city === this.form.city)
      return cityData ? cityData.districts : []
    }
  },
  watch: {
    user: {
      handler (newVal) {
        if (newVal) {
          const defaultAddress = newVal.addresses && newVal.addresses.find(a => a.is_default)
          if (defaultAddress) {
            this.form = { ...defaultAddress }
          } else {
            this.form = {
              recipient_name: newVal.name,
              phone: newVal.phone,
              city: '',
              district: '',
              zip_code: '',
              detail_address: ''
            }
          }
        }
      },
      immediate: true,
      deep: true
    },
    'form.city' (newVal) {
      const cityData = this.taiwanDistricts.find(c => c.city === newVal)
      if (cityData) {
        const validDistricts = cityData.districts.map(d => d.name)
        if (!validDistricts.includes(this.form.district)) {
          this.form.district = ''
          this.form.zip_code = ''
        }
      }
    },
    'form.district' (newVal) {
      if (newVal && this.form.city) {
        const cityData = this.taiwanDistricts.find(c => c.city === this.form.city)
        if (cityData) {
          const districtData = cityData.districts.find(d => d.name === newVal)
          if (districtData) {
            this.form.zip_code = districtData.zip
          }
        }
      }
    }
  },
  methods: {
    cancelEdit () {
      this.isEditing = false
    },
    async saveAddress () {
      if (!this.form.city || !this.form.district || !this.form.detail_address) {
        this.toast.warning('請完整填寫地址資訊')
        return
      }
      this.loading = true
      try {
        const response = await api.put('/profile', {
          ...this.form
        })

        this.toast.success('地址更新成功')
        this.$emit('profile-updated', response.data.user)
        this.isEditing = false
      } catch (e) {
        console.error(e)
        this.toast.error(e.response?.data?.message || '更新失敗')
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

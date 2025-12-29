<template>
  <Teleport to="body">
    <Transition name="fade">
      <div v-if="visible" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="$emit('close')">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
        
        <!-- Modal -->
        <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
          <!-- Header -->
          <div class="px-6 py-5 border-b border-stone-100 dark:border-slate-700 bg-stone-50/50 dark:bg-slate-700/30">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-xieOrange/10 rounded-full flex items-center justify-center text-xieOrange">
                <i class="fas fa-map-marker-alt text-lg"></i>
              </div>
              <div>
                <h3 class="font-bold text-slate-700 dark:text-stone-100">設定收貨地址</h3>
                <p class="text-xs text-stone-500 dark:text-stone-400">請填寫完整地址以便順利配送</p>
              </div>
            </div>
          </div>
          
          <!-- Form -->
          <form @submit.prevent="saveAddress" class="p-6 space-y-5">
            <!-- Name & Phone -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">收件人姓名</label>
                <input 
                  v-model="form.recipient_name" 
                  type="text" 
                  placeholder="收件人姓名"
                  class="w-full bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-xieOrange focus:ring-2 focus:ring-xieOrange/30 text-slate-700 dark:text-stone-100 placeholder:text-stone-400 transition text-sm" 
                  required
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">聯絡電話</label>
                <input 
                  v-model="form.phone" 
                  type="tel"
                  placeholder="09XX-XXX-XXX"
                  class="w-full bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-xieOrange focus:ring-2 focus:ring-xieOrange/30 text-slate-700 dark:text-stone-100 placeholder:text-stone-400 transition text-sm" 
                  required
                >
              </div>
            </div>

            <!-- City, District, Zip -->
            <div class="grid grid-cols-3 gap-3">
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">縣市</label>
                <select 
                  v-model="form.city" 
                  class="w-full bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl px-3 py-3 focus:outline-none focus:border-xieOrange text-slate-700 dark:text-stone-100 transition text-sm" 
                  required
                >
                  <option value="" disabled>選擇縣市</option>
                  <option v-for="c in taiwanDistricts" :key="c.city" :value="c.city">{{ c.city }}</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">鄉鎮市區</label>
                <select 
                  v-model="form.district" 
                  class="w-full bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl px-3 py-3 focus:outline-none focus:border-xieOrange text-slate-700 dark:text-stone-100 transition text-sm" 
                  :disabled="!form.city" 
                  required
                >
                  <option value="" disabled>選擇區域</option>
                  <option v-for="d in availableDistricts" :key="d.name" :value="d.name">{{ d.name }}</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">郵遞區號</label>
                <input 
                  v-model="form.zip_code" 
                  type="text" 
                  class="w-full bg-stone-100 dark:bg-slate-600 border border-stone-200 dark:border-slate-500 rounded-xl px-3 py-3 text-stone-500 dark:text-stone-400 text-sm" 
                  readonly
                  placeholder="自動帶入"
                >
              </div>
            </div>

            <!-- Detail Address -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">詳細地址</label>
              <input
                v-model="form.detail_address"
                type="text"
                placeholder="街道、巷弄、門牌號碼..."
                class="w-full bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-xieOrange focus:ring-2 focus:ring-xieOrange/30 text-slate-700 dark:text-stone-100 placeholder:text-stone-400 transition text-sm"
                required
              >
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-2">
              <button 
                type="button" 
                class="flex-1 border border-stone-300 dark:border-slate-500 text-stone-600 dark:text-stone-300 py-3 rounded-xl font-medium hover:bg-stone-100 dark:hover:bg-slate-600 transition" 
                @click="$emit('close')"
              >
                取消
              </button>
              <button 
                type="submit" 
                class="flex-[2] bg-xieOrange text-white py-3 rounded-xl font-semibold hover:bg-[#cf8354] transition shadow-lg shadow-xieOrange/20 disabled:opacity-50" 
                :disabled="loading"
              >
                <i v-if="loading" class="fas fa-spinner fa-spin mr-2"></i>
                {{ loading ? '儲存中...' : '確認地址並結帳' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script>
import api from '../../services/api'
import { useToast } from 'vue-toastification'
import taiwanDistricts from '../../assets/taiwan_districts.json'

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
    },
    fullAddress () {
      return `${this.form.zip_code} ${this.form.city}${this.form.district}${this.form.detail_address}`
    }
  },
  watch: {
    visible (val) {
      if (val) {
        this.loadUserData()
      }
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
    async loadUserData () {
      try {
        const res = await api.get('/user')
        const user = res.data
        this.form.recipient_name = user.name || ''
        this.form.phone = user.phone || ''
        
        // Load existing address if available
        const defaultAddress = user.addresses?.find(a => a.is_default)
        if (defaultAddress) {
          this.form = { ...defaultAddress }
        }
      } catch (e) {
        console.error('Load user error:', e)
      }
    },
    async saveAddress () {
      if (!this.form.city || !this.form.district || !this.form.detail_address) {
        this.toast.warning('請完整填寫地址資訊')
        return
      }
      if (!this.form.recipient_name || !this.form.phone) {
        this.toast.warning('請填寫收件人姓名和電話')
        return
      }

      this.loading = true
      try {
        // Save address to profile
        await api.put('/profile', {
          ...this.form
        })

        this.toast.success('地址已儲存')
        this.$emit('success', this.fullAddress)
        this.$emit('close')
      } catch (e) {
        console.error('Save address error:', e)
        this.toast.error(e.response?.data?.message || '儲存失敗')
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>

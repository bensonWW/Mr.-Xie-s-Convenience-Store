<template>
  <div class="space-y-6">
    <form class="bg-white dark:bg-slate-800 rounded-2xl border border-stone-100 dark:border-slate-700 overflow-hidden transition-colors duration-300" @submit.prevent="updateProfile">
      <!-- Header -->
      <div class="px-6 py-4 border-b border-stone-100 dark:border-slate-700">
        <h2 class="font-semibold text-slate-700 dark:text-stone-100">基本資料設定</h2>
      </div>

      <div class="p-8">
        <!-- Avatar Section -->
        <div class="flex items-center gap-6 mb-8">
          <div class="relative w-24 h-24">
            <img 
              :src="avatarUrl || 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'" 
              class="w-full h-full rounded-full object-cover border-4 border-stone-100 dark:border-slate-700 shadow-lg"
            >
            <button 
              type="button" 
              class="absolute bottom-0 right-0 bg-xieOrange text-white rounded-full w-8 h-8 flex items-center justify-center border-2 border-white dark:border-slate-800 hover:bg-[#cf8354] transition shadow-lg" 
              @click="triggerFileInput"
            >
              <i class="fas fa-camera text-sm"></i>
            </button>
            <input type="file" ref="fileInput" class="hidden" accept="image/*" @change="onFileChange">
          </div>
          <div>
            <div class="text-sm font-medium text-slate-700 dark:text-stone-100 mb-1">個人頭像</div>
            <div class="text-xs text-stone-500 dark:text-stone-400 mb-2">支援 JPG, PNG 格式，檔案大小不超過 2MB</div>
            <button 
              type="button" 
              class="text-sm border border-stone-300 dark:border-slate-600 text-stone-600 dark:text-stone-300 px-4 py-1.5 rounded-full hover:bg-stone-50 dark:hover:bg-slate-700 transition" 
              @click="triggerFileInput"
            >
              選擇圖片
            </button>
          </div>
        </div>

        <!-- Form Fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">會員帳號 / Email</label>
            <input 
              type="text" 
              :value="user.email" 
              disabled 
              class="w-full bg-stone-100 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl px-4 py-3 text-stone-500 dark:text-stone-400 cursor-not-allowed"
            >
            <p class="text-xs text-stone-400 dark:text-stone-500 mt-1">帳號設定後無法修改</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">手機號碼</label>
            <input 
              type="tel" 
              v-model="editForm.phone" 
              class="w-full bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-xieOrange focus:ring-2 focus:ring-xieOrange/30 text-slate-700 dark:text-stone-100 transition"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">顯示名稱</label>
            <input 
              type="text" 
              v-model="editForm.name" 
              class="w-full bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-xieOrange focus:ring-2 focus:ring-xieOrange/30 text-slate-700 dark:text-stone-100 transition"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">生日</label>
            <input 
              type="date" 
              v-model="editForm.birthday" 
              class="w-full bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-xieOrange focus:ring-2 focus:ring-xieOrange/30 text-slate-700 dark:text-stone-100 transition"
            >
          </div>

          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">性別</label>
            <div class="flex gap-6 mt-2">
              <label class="flex items-center cursor-pointer">
                <input type="radio" name="gender" value="male" v-model="editForm.gender" class="text-xieOrange focus:ring-xieOrange">
                <span class="ml-2 text-sm text-slate-700 dark:text-stone-200">男</span>
              </label>
              <label class="flex items-center cursor-pointer">
                <input type="radio" name="gender" value="female" v-model="editForm.gender" class="text-xieOrange focus:ring-xieOrange">
                <span class="ml-2 text-sm text-slate-700 dark:text-stone-200">女</span>
              </label>
              <label class="flex items-center cursor-pointer">
                <input type="radio" name="gender" value="other" v-model="editForm.gender" class="text-xieOrange focus:ring-xieOrange">
                <span class="ml-2 text-sm text-slate-700 dark:text-stone-200">不透露</span>
              </label>
            </div>
          </div>
        </div>
      </div>

      <!-- Password Section -->
      <div class="px-6 py-4 border-t border-b border-stone-100 dark:border-slate-700">
        <h2 class="font-semibold text-slate-700 dark:text-stone-100">變更密碼</h2>
      </div>

      <div class="p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">目前密碼</label>
            <input 
              type="password" 
              v-model="passwordForm.currentPassword" 
              placeholder="若不修改密碼請留空" 
              class="w-full md:w-1/2 bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-xieOrange focus:ring-2 focus:ring-xieOrange/30 text-slate-700 dark:text-stone-100 placeholder:text-stone-400 transition"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">新密碼</label>
            <input 
              type="password" 
              v-model="passwordForm.newPassword" 
              class="w-full bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-xieOrange focus:ring-2 focus:ring-xieOrange/30 text-slate-700 dark:text-stone-100 transition"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-stone-200 mb-2">確認新密碼</label>
            <input 
              type="password" 
              v-model="passwordForm.confirmPassword" 
              class="w-full bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:border-xieOrange focus:ring-2 focus:ring-xieOrange/30 text-slate-700 dark:text-stone-100 transition"
            >
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="bg-stone-50 dark:bg-slate-700 px-8 py-4 text-right border-t border-stone-100 dark:border-slate-600 flex justify-end gap-3">
        <button 
          type="button" 
          class="text-stone-600 dark:text-stone-300 px-6 py-2.5 rounded-full border border-stone-300 dark:border-slate-500 hover:bg-stone-100 dark:hover:bg-slate-600 transition" 
          @click="$emit('cancel')"
        >
          取消
        </button>
        <button 
          type="submit" 
          class="bg-xieOrange text-white px-8 py-2.5 rounded-full font-medium hover:bg-[#cf8354] transition shadow-lg shadow-xieOrange/20"
        >
          儲存變更
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import api from '../../services/api'
import { useToast } from 'vue-toastification'

export default {
  name: 'ProfileEdit',
  props: {
    user: {
      type: Object,
      required: true
    },
    initialAvatarUrl: {
      type: String,
      default: ''
    }
  },
  emits: ['cancel', 'profile-updated', 'avatar-change'],
  setup () {
    const toast = useToast()
    return { toast }
  },
  data () {
    return {
      editForm: {
        name: this.user.name,
        phone: this.user.phone || '',
        birthday: this.user.birthday ? this.user.birthday.split('T')[0] : '',
        gender: this.user.gender || 'male'
      },
      passwordForm: {
        currentPassword: '',
        newPassword: '',
        confirmPassword: ''
      },
      avatarUrl: this.initialAvatarUrl
    }
  },
  watch: {
    initialAvatarUrl (newVal) {
      this.avatarUrl = newVal
    },
    user: {
      handler (newVal) {
        this.editForm = {
          name: newVal.name,
          phone: newVal.phone || '',
          birthday: newVal.birthday ? newVal.birthday.split('T')[0] : '',
          gender: newVal.gender || 'male'
        }
      },
      deep: true
    }
  },
  methods: {
    triggerFileInput () {
      this.$refs.fileInput.click()
    },
    onFileChange (e) {
      const file = e.target.files[0]
      if (file) {
        const reader = new FileReader()
        reader.onload = (e2) => {
          this.avatarUrl = e2.target.result
          this.$emit('avatar-change', file)
        }
        reader.readAsDataURL(file)
      }
    },
    async updateProfile () {
      try {
        const payload = { ...this.editForm }

        if (this.passwordForm.newPassword) {
          if (this.passwordForm.newPassword !== this.passwordForm.confirmPassword) {
            this.toast.warning('新密碼與確認密碼不符')
            return
          }
          payload.password = this.passwordForm.newPassword
          payload.current_password = this.passwordForm.currentPassword
        }

        const response = await api.put('/profile', payload)
        this.toast.success('個人資料更新成功！')

        this.passwordForm.currentPassword = ''
        this.passwordForm.newPassword = ''
        this.passwordForm.confirmPassword = ''

        this.$emit('profile-updated', response.data.user)
      } catch (error) {
        console.error('Update profile error:', error)
        this.toast.error('更新失敗，請稍後再試。')
      }
    }
  }
}
</script>

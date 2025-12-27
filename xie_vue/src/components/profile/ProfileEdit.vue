<template>
  <div class="space-y-6">
    <form class="bg-white rounded-lg shadow-sm overflow-hidden" @submit.prevent="updateProfile">
      <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
        <h2 class="font-bold text-gray-800">基本資料設定</h2>
      </div>

      <div class="p-8">
        <div class="flex items-center gap-6 mb-8">
          <div class="relative w-24 h-24">
            <img :src="avatarUrl || 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png'" class="w-full h-full rounded-full object-cover border-4 border-gray-100 shadow">
            <button type="button" class="absolute bottom-0 right-0 bg-xieOrange text-white rounded-full p-2 border-2 border-white hover:bg-orange-600 transition shadow-sm" @click="triggerFileInput">
              <i class="fas fa-camera"></i>
            </button>
            <input type="file" ref="fileInput" class="hidden" accept="image/*" @change="onFileChange">
          </div>
          <div>
            <div class="text-sm font-bold text-gray-700 mb-1">個人頭像</div>
            <div class="text-xs text-gray-500 mb-2">支援 JPG, PNG 格式，檔案大小不超過 2MB</div>
            <button type="button" class="text-sm border border-gray-300 px-3 py-1 rounded hover:bg-gray-50 transition" @click="triggerFileInput">選擇圖片</button>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">會員帳號 / Email</label>
            <input type="text" :value="user.email" disabled class="w-full bg-gray-100 border border-gray-300 rounded px-4 py-2 text-gray-500 cursor-not-allowed">
            <p class="text-xs text-gray-400 mt-1">帳號設定後無法修改</p>
          </div>

          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">手機號碼</label>
            <input type="tel" v-model="editForm.phone" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-xieOrange focus:ring-1 focus:ring-xieOrange">
          </div>

          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">顯示名稱</label>
            <input type="text" v-model="editForm.name" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-xieOrange focus:ring-1 focus:ring-xieOrange">
          </div>

          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">生日</label>
            <div class="relative">
              <input type="date" v-model="editForm.birthday" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-xieOrange focus:ring-1 focus:ring-xieOrange">
            </div>
          </div>

          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">性別</label>
            <div class="flex gap-4 mt-2">
              <label class="flex items-center cursor-pointer">
                <input type="radio" name="gender" value="male" v-model="editForm.gender" class="text-xieOrange focus:ring-xieOrange">
                <span class="ml-2 text-sm">男</span>
              </label>
              <label class="flex items-center cursor-pointer">
                <input type="radio" name="gender" value="female" v-model="editForm.gender" class="text-xieOrange focus:ring-xieOrange">
                <span class="ml-2 text-sm">女</span>
              </label>
              <label class="flex items-center cursor-pointer">
                <input type="radio" name="gender" value="other" v-model="editForm.gender" class="text-xieOrange focus:ring-xieOrange">
                <span class="ml-2 text-sm">不透露</span>
              </label>
            </div>
          </div>
        </div>
      </div>

      <div class="px-6 py-4 border-t border-b border-gray-100 bg-gray-50 mt-4">
        <h2 class="font-bold text-gray-800">變更密碼</h2>
      </div>

      <div class="p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="md:col-span-2">
            <label class="block text-sm font-bold text-gray-700 mb-2">目前密碼</label>
            <input type="password" v-model="passwordForm.currentPassword" placeholder="若不修改密碼請留空" class="w-full md:w-1/2 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-xieOrange focus:ring-1 focus:ring-xieOrange">
          </div>
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">新密碼</label>
            <input type="password" v-model="passwordForm.newPassword" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-xieOrange focus:ring-1 focus:ring-xieOrange">
          </div>
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">確認新密碼</label>
            <input type="password" v-model="passwordForm.confirmPassword" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-xieOrange focus:ring-1 focus:ring-xieOrange">
          </div>
        </div>
      </div>

      <div class="bg-gray-50 px-8 py-4 text-right border-t border-gray-200">
        <button type="button" class="text-gray-500 px-6 py-2 rounded hover:bg-gray-200 transition mr-2" @click="$emit('cancel')">取消</button>
        <button type="submit" class="bg-xieOrange text-white px-8 py-2 rounded font-bold hover:bg-orange-600 transition shadow-md">儲存變更</button>
      </div>

    </form>
  </div>
</template>

<script>
import api from '../../services/api'

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
        // Update local form if user prop updates (e.g. initial fetch)
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
          this.avatarUrl = e2.target.result // Preview
          this.$emit('avatar-change', file) // Emit file up
        }
        reader.readAsDataURL(file)
      }
    },
    async updateProfile () {
      try {
        const payload = { ...this.editForm }

        if (this.passwordForm.newPassword) {
          if (this.passwordForm.newPassword !== this.passwordForm.confirmPassword) {
            this.$toast.warning('新密碼與確認密碼不符')
            return
          }
          payload.password = this.passwordForm.newPassword
          payload.current_password = this.passwordForm.currentPassword
        }

        const response = await api.put('/profile', payload)
        this.$toast.success('個人資料更新成功！')

        // Clear password form
        this.passwordForm.currentPassword = ''
        this.passwordForm.newPassword = ''
        this.passwordForm.confirmPassword = ''

        this.$emit('profile-updated', response.data.user)
      } catch (error) {
        console.error('Update profile error:', error)
        this.$toast.error('更新失敗，請稍後再試。')
      }
    }
  }
}
</script>

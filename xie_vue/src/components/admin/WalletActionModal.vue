<template>
  <div v-if="visible" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 transition-opacity">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md overflow-hidden animate-fade-in-up">
      <div class="bg-gray-50 p-4 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">人工加值/扣款</h3>
        <button @click="close" class="text-gray-400 hover:text-gray-600">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="p-6">
        <div class="mb-4">
          <label class="block text-sm font-bold text-gray-700 mb-2">操作類型</label>
          <div class="flex gap-4">
            <label class="flex-1 cursor-pointer">
              <input type="radio" value="deposit" v-model="localForm.type" class="hidden peer">
              <div class="text-center py-2 rounded border border-gray-200 peer-checked:bg-green-100 peer-checked:text-green-700 peer-checked:border-green-300 transition hover:bg-gray-50">
                <i class="fas fa-plus-circle mr-1"></i> 儲值 (給予)
              </div>
            </label>
            <label class="flex-1 cursor-pointer">
              <input type="radio" value="withdraw" v-model="localForm.type" class="hidden peer">
              <div class="text-center py-2 rounded border border-gray-200 peer-checked:bg-red-100 peer-checked:text-red-700 peer-checked:border-red-300 transition hover:bg-gray-50">
                <i class="fas fa-minus-circle mr-1"></i> 扣款 (懲罰/修正)
              </div>
            </label>
          </div>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-bold text-gray-700 mb-2">金額</label>
          <div class="relative">
            <span class="absolute left-3 top-2 text-gray-500">$</span>
            <input v-model.number="localForm.amount" type="number" min="1" placeholder="0" class="w-full border border-gray-300 rounded pl-8 pr-4 py-2 focus:outline-none focus-border-xieOrange font-mono text-lg">
          </div>
        </div>

        <div class="mb-6">
          <label class="block text-sm font-bold text-gray-700 mb-2">備註說明</label>
          <textarea v-model="localForm.description" rows="3" placeholder="請輸入原因 (例如: 活動獎勵、取消訂單退款)" class="w-full border border-gray-300 rounded p-3 focus:outline-none focus-border-xieOrange text-sm"></textarea>
        </div>

        <div class="flex justify-end gap-3">
          <button @click="close" class="px-4 py-2 text-gray-500 hover:text-gray-700 font-bold">取消</button>
          <button @click="submit" :disabled="loading" class="bg-xieOrange text-white px-6 py-2 rounded font-bold hover:bg-orange-600 shadow-md transition flex items-center gap-2">
            <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-check'"></i> 確認送出
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'WalletActionModal',
  props: {
    visible: {
      type: Boolean,
      default: false
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  emits: ['close', 'submit'],
  data () {
    return {
      localForm: {
        type: 'deposit',
        amount: '',
        description: ''
      }
    }
  },
  watch: {
    visible (val) {
      if (val) {
        // Reset form when opened
        this.localForm = {
          type: 'deposit',
          amount: '',
          description: ''
        }
      }
    }
  },
  methods: {
    close () {
      this.$emit('close')
    },
    submit () {
      this.$emit('submit', this.localForm)
    }
  }
}
</script>

<style scoped>
.bg-xieOrange { background-color: #ed8936; }
.hover-bg-orange-600:hover { background-color: #dd6b20; }
.focus-border-xieOrange:focus { border-color: #ed8936; }

@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-fade-in-up {
  animation: fade-in-up 0.3s ease-out;
}
</style>

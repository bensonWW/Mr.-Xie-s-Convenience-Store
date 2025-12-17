<template>
  <div v-if="visible" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 transition-all duration-300">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in-up transform scale-100">
      <div class="bg-gray-50/80 p-5 border-b border-gray-100 flex justify-between items-center backdrop-blur">
        <h3 class="font-bold text-gray-800 text-lg">
           <i class="fas fa-wallet text-xieOrange mr-2"></i>人工加值 / 扣款
        </h3>
        <button @click="close" class="text-gray-400 hover:text-gray-600 w-8 h-8 rounded-full hover:bg-gray-200 flex items-center justify-center transition">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="p-6 space-y-5">
        <div>
          <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">操作類型</label>
          <div class="grid grid-cols-2 gap-3">
            <label class="cursor-pointer group">
              <input type="radio" value="deposit" v-model="localForm.type" class="hidden peer">
              <div class="text-center py-3 rounded-xl border border-gray-200 peer-checked:bg-green-50 peer-checked:text-green-700 peer-checked:border-green-400 peer-checked:ring-1 peer-checked:ring-green-400 transition hover:bg-gray-50 font-bold flex flex-col items-center gap-1 group-hover:border-gray-300">
                <i class="fas fa-plus-circle text-lg mb-1"></i>
                <span>儲值 (給予)</span>
              </div>
            </label>
            <label class="cursor-pointer group">
              <input type="radio" value="withdraw" v-model="localForm.type" class="hidden peer">
              <div class="text-center py-3 rounded-xl border border-gray-200 peer-checked:bg-red-50 peer-checked:text-red-700 peer-checked:border-red-400 peer-checked:ring-1 peer-checked:ring-red-400 transition hover:bg-gray-50 font-bold flex flex-col items-center gap-1 group-hover:border-gray-300">
                <i class="fas fa-minus-circle text-lg mb-1"></i>
                <span>扣款 (懲罰)</span>
              </div>
            </label>
          </div>
        </div>

        <div>
           <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">金額</label>
           <div class="relative">
             <span class="absolute left-4 top-3 text-gray-400 font-bold">$</span>
             <input v-model.number="localForm.amount" type="number" min="1" placeholder="0" class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-8 pr-4 py-3 focus:outline-none focus:border-xieOrange focus:ring-2 focus:ring-orange-100 font-mono text-xl font-bold text-gray-800 transition">
           </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">備註說明</label>
          <textarea v-model="localForm.description" rows="3" placeholder="請輸入原因 (例如: 活動獎勵、取消訂單退款)" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 focus:outline-none focus:border-xieOrange focus:ring-2 focus:ring-orange-100 text-sm transition"></textarea>
        </div>

        <div class="flex justify-end gap-3 pt-2">
          <button @click="close" class="px-5 py-2.5 text-gray-500 hover:text-gray-700 font-bold hover:bg-gray-100 rounded-lg transition">取消</button>
          <button @click="submit" :disabled="loading" class="bg-gradient-to-r from-orange-400 to-orange-500 text-white px-6 py-2.5 rounded-lg font-bold hover:from-orange-500 hover:to-orange-600 shadow-md hover:shadow-lg transform active:scale-95 transition flex items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
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

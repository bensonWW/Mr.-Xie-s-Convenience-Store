<template>
  <div class="space-y-6">
    <!-- Redeem Code Input -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-stone-100 dark:border-slate-700 p-6 transition-colors duration-300">
      <label class="block text-sm font-semibold text-slate-700 dark:text-stone-100 mb-3">新增優惠券</label>
      <div class="flex gap-3">
        <input 
          type="text" 
          placeholder="輸入折扣代碼 (例如: XIE2025)" 
          class="flex-1 bg-stone-50 dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-xieOrange/50 focus:border-xieOrange transition text-slate-700 dark:text-stone-100 placeholder:text-stone-400"
        >
        <button class="bg-xieOrange text-white px-6 py-3 rounded-full font-medium hover:bg-[#cf8354] transition shadow-lg shadow-xieOrange/20">
          領取
        </button>
      </div>
    </div>

    <!-- Coupons List -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-stone-100 dark:border-slate-700 min-h-[400px] overflow-hidden transition-colors duration-300">
      <!-- Tabs -->
      <div class="flex border-b border-stone-100 dark:border-slate-700">
        <button 
          class="flex-1 py-4 text-center font-medium transition focus:outline-none border-b-2"
          :class="couponTab === 'available' ? 'text-xieOrange border-xieOrange bg-xieOrange/5' : 'text-stone-500 dark:text-stone-400 hover:text-slate-700 dark:hover:text-stone-200 border-transparent'"
          @click="couponTab = 'available'"
        >
          可使用 ({{ coupons.length }})
        </button>
        <button 
          class="flex-1 py-4 text-center font-medium transition focus:outline-none border-b-2"
          :class="couponTab === 'used' ? 'text-xieOrange border-xieOrange bg-xieOrange/5' : 'text-stone-500 dark:text-stone-400 hover:text-slate-700 dark:hover:text-stone-200 border-transparent'"
          @click="couponTab = 'used'"
        >
          已使用 (0)
        </button>
        <button 
          class="flex-1 py-4 text-center font-medium transition focus:outline-none border-b-2"
          :class="couponTab === 'expired' ? 'text-xieOrange border-xieOrange bg-xieOrange/5' : 'text-stone-500 dark:text-stone-400 hover:text-slate-700 dark:hover:text-stone-200 border-transparent'"
          @click="couponTab = 'expired'"
        >
          已失效 (0)
        </button>
      </div>

      <!-- Coupon Cards -->
      <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4" v-if="couponTab === 'available'">
        <div 
          v-for="coupon in coupons" 
          :key="coupon.id" 
          class="bg-white dark:bg-slate-700 border border-stone-200 dark:border-slate-600 rounded-xl flex overflow-hidden hover:shadow-lg transition group"
        >
          <!-- Ticket Left Side -->
          <div class="w-28 bg-gradient-to-br from-xieOrange to-[#cf8354] text-white flex flex-col items-center justify-center p-3 relative">
            <span class="text-[10px] font-medium opacity-80" v-if="coupon.limit_price">滿 ${{ coupon.limit_price }}</span>
            <div class="font-bold text-xl">
              {{ coupon.type === 'fixed' ? '$' + coupon.discount_amount : (100 - coupon.discount_amount) + '折' }}
            </div>
            <!-- Ticket holes -->
            <div class="absolute -right-2 top-1/2 -translate-y-1/2 w-4 h-4 bg-white dark:bg-slate-800 rounded-full"></div>
          </div>
          <!-- Ticket Right Side -->
          <div class="flex-1 p-4 flex flex-col justify-between border-l-2 border-dashed border-stone-200 dark:border-slate-600">
            <div>
              <h4 class="font-semibold text-slate-700 dark:text-stone-100 group-hover:text-xieOrange transition">{{ coupon.code }}</h4>
              <p class="text-xs text-stone-500 dark:text-stone-400 mt-1">全館商品適用</p>
            </div>
            <div class="flex justify-between items-end mt-3">
              <span class="text-xs text-stone-400 dark:text-stone-500">有效期限: 2025/12/31</span>
              <button 
                class="text-xieOrange border border-xieOrange px-3 py-1 rounded-full text-xs hover:bg-xieOrange hover:text-white transition" 
                @click="copyCode(coupon.code)"
              >
                複製
              </button>
            </div>
          </div>
        </div>

        <div v-if="coupons.length === 0" class="col-span-2 text-center py-12 text-stone-400 dark:text-stone-500">
          <i class="fas fa-ticket-alt text-4xl mb-3 opacity-50"></i>
          <p>暫無可用的優惠券</p>
        </div>
      </div>

      <div class="p-6 text-center text-stone-400 dark:text-stone-500" v-else>
        <i class="fas fa-ticket-alt text-4xl mb-3 opacity-50"></i>
        <p>暫無資料</p>
      </div>

      <div class="px-6 pb-6 text-xs text-stone-400 dark:text-stone-500 mt-auto">
        * 優惠券使用規則請參閱 <a href="#" class="underline hover:text-xieOrange">服務條款</a>。
      </div>
    </div>
  </div>
</template>

<script>
import { useToast } from 'vue-toastification'

export default {
  name: 'CouponWallet',
  props: {
    coupons: {
      type: Array,
      default: () => []
    }
  },
  setup () {
    const toast = useToast()
    return { toast }
  },
  data () {
    return {
      couponTab: 'available'
    }
  },
  methods: {
    copyCode (code) {
      navigator.clipboard.writeText(code).then(() => {
        this.toast.success('優惠碼已複製：' + code)
      })
    }
  }
}
</script>

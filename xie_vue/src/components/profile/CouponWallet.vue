<template>
  <div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm p-6">
      <label class="block text-sm font-bold text-gray-700 mb-2">新增優惠券 / 儲值折扣碼</label>
      <div class="flex gap-3">
        <input type="text" placeholder="請輸入折扣代碼 (例如: XIE2025)" class="flex-1 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-xieOrange focus:ring-1 focus:ring-xieOrange">
        <button class="bg-xieBlue text-white px-6 py-2 rounded font-bold hover:bg-gray-700 transition">領取</button>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm min-h-[500px] overflow-hidden">

      <div class="flex border-b border-gray-200">
        <button class="flex-1 py-4 text-center font-bold transition focus:outline-none border-0 border-b-2"
                :class="couponTab === 'available' ? 'text-xieOrange bg-orange-50 border-xieOrange' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50 border-transparent'"
                @click="couponTab = 'available'">
          可使用 ({{ coupons.length }})
        </button>
        <button class="flex-1 py-4 text-center font-bold transition focus:outline-none border-0 border-b-2"
                :class="couponTab === 'used' ? 'text-xieOrange bg-orange-50 border-xieOrange' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50 border-transparent'"
                @click="couponTab = 'used'">
          已使用 (0)
        </button>
        <button class="flex-1 py-4 text-center font-bold transition focus:outline-none border-0 border-b-2"
                :class="couponTab === 'expired' ? 'text-xieOrange bg-orange-50 border-xieOrange' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50 border-transparent'"
                @click="couponTab = 'expired'">
          已失效 (0)
        </button>
      </div>

      <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4" v-if="couponTab === 'available'">

        <div v-for="coupon in coupons" :key="coupon.id" class="bg-white border border-gray-200 rounded-lg flex overflow-hidden hover:shadow-md transition group">
          <div class="w-32 bg-gradient-to-br from-orange-400 to-xieOrange text-white flex flex-col items-center justify-center p-2 relative border-r-2 border-dashed border-white">
            <span class="text-xs font-bold opacity-80" v-if="coupon.limit_price">滿${{ coupon.limit_price }}</span>
            <div class="font-bold text-2xl">
              {{ coupon.type === 'fixed' ? '折$' + coupon.discount_amount : (100 - coupon.discount_amount) + '折' }}
            </div>
            <div class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-white rounded-full"></div> <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-white rounded-full"></div>
          </div>
          <div class="flex-1 p-4 flex flex-col justify-between">
            <div>
              <h4 class="font-bold text-gray-800 group-hover:text-xieOrange">{{ coupon.code }}</h4>
              <p class="text-xs text-gray-500 mt-1">全館商品適用</p>
            </div>
            <div class="flex justify-between items-end mt-3">
              <span class="text-xs text-gray-400">有效期限: 2025/12/31</span>
              <button class="text-xieOrange border border-xieOrange px-3 py-1 rounded text-xs hover:bg-xieOrange hover:text-white transition" @click="copyCode(coupon.code)">複製代碼</button>
            </div>
          </div>
        </div>

        <div v-if="coupons.length === 0" class="col-span-2 text-center py-8 text-gray-400">
          暫無可用的優惠券
        </div>

      </div>

      <div class="p-6 text-center text-gray-400" v-else>
        暫無資料
      </div>

      <div class="px-6 pb-6 text-xs text-gray-400 mt-auto">
        * 優惠券使用規則請參閱 <a href="#" class="underline hover:text-xieOrange">服務條款</a>。
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CouponWallet',
  props: {
    coupons: {
      type: Array,
      default: () => []
    }
  },
  data () {
    return {
      couponTab: 'available'
    }
  },
  methods: {
    copyCode (code) {
      navigator.clipboard.writeText(code).then(() => {
        this.$toast.success('優惠碼已複製：' + code)
      })
    }
  }
}
</script>

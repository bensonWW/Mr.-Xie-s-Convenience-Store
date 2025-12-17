<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../services/api'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import UserTopUpModal from '../components/profile/UserTopUpModal.vue'
import InlineAddressModal from '../components/profile/InlineAddressModal.vue'

const router = useRouter()
const toast = useToast()
const cartItems = ref([])
const availableCoupons = ref([])
const selectedCouponId = ref('')
const discountAmount = ref(0)
const appliedCoupon = ref(null)

const userBalance = ref(0)
const userLevel = ref('normal')
const userAddress = ref('')
const showTopUpModal = ref(false)
const showAddressModal = ref(false)
const isProcessing = ref(false)

onMounted(() => {
  fetchCart()
  fetchCoupons()
  fetchUserProfile()
  fetchUserWallet()
})

async function fetchUserProfile () {
  try {
    const res = await api.get('/user')
    userAddress.value = res.data.address || ''
    userLevel.value = res.data.member_level || 'normal'
  } catch (error) {
    console.error('Fetch user profile error:', error)
  }
}

async function fetchUserWallet () {
  try {
    const res = await api.get('/user/wallet')
    userBalance.value = res.data.balance || 0
  } catch (error) {
    console.error('Fetch wallet error:', error)
  }
}

async function fetchCoupons () {
  try {
    const response = await api.get('/coupons')
    availableCoupons.value = response.data
  } catch (error) {
    console.error('Fetch coupons error:', error)
  }
}

async function fetchCart () {
  const token = localStorage.getItem('token')
  if (!token) {
    toast.warning('請先登入')
    router.push('/profile')
    return
  }

  try {
    const response = await api.get('/cart')
    cartItems.value = response.data.items.map(item => ({
      id: item.id,
      name: item.product.name,
      price: Number(item.product.price),
      quantity: item.quantity,
      productId: item.product.id
    }))
  } catch (error) {
    console.error('Fetch cart error:', error)
  }
}

const cartTotal = computed(() =>
  cartItems.value.reduce((sum, item) => sum + item.price * item.quantity, 0)
)

const memberDiscountAmount = computed(() => {
  const rates = {
    'normal': 0,
    'vip': 0.05,
    'platinum': 0.10
  }
  const rate = rates[userLevel.value] || 0
  return Math.round(cartTotal.value * rate)
})

const finalTotal = computed(() => {
  let total = cartTotal.value - memberDiscountAmount.value
  total -= discountAmount.value
  return Math.max(0, Math.round(total))
})

// Check if balance is sufficient
const isBalanceInsufficient = computed(() => {
  return userBalance.value < finalTotal.value
})

async function applyCoupon () {
  if (!selectedCouponId.value) {
    discountAmount.value = 0
    appliedCoupon.value = null
    return
  }

  const coupon = availableCoupons.value.find(c => c.id === selectedCouponId.value)
  if (!coupon) return

  try {
    const response = await api.post('/coupons/check', {
      code: coupon.code,
      total_amount: cartTotal.value
    })
    appliedCoupon.value = response.data
    discountAmount.value = Math.round(response.data.discount_amount)
    toast.success(`優惠卷已套用：${response.data.message}`)
  } catch (error) {
    console.error('Coupon error:', error)
    toast.error(error.response?.data?.message || '優惠卷無效')
    discountAmount.value = 0
    appliedCoupon.value = null
    selectedCouponId.value = ''
  }
}

async function removeItem (id) {
  if (!confirm('確定要刪除此商品嗎？')) return
  try {
    await api.delete(`/cart/items/${id}`)
    cartItems.value = cartItems.value.filter((i) => i.id !== id)
    // Re-validate coupon if total changed (optional, but good practice)
    if (appliedCoupon.value) {
      applyCoupon()
    }
    toast.info('已刪除商品')
  } catch (error) {
    console.error('Remove item error:', error)
    toast.error('刪除失敗')
  }
}

async function updateQuantity (item, change) {
  const newQty = item.quantity + change
  if (newQty < 1) return

  try {
    await api.put(`/cart/items/${item.id}`, { quantity: newQty })
    item.quantity = newQty
    // Re-validate coupon if total changed
    if (appliedCoupon.value) {
      applyCoupon()
    }
  } catch (error) {
    console.error('Update quantity error:', error)
    toast.error('更新數量失敗')
  }
}

async function checkout () {
  if (cartItems.value.length === 0) return

  // 1. Check Address
  if (!userAddress.value) {
    // toast.warning('請先設定收貨地址') // Optional
    showAddressModal.value = true
    return
  }

  // 2. Check Balance
  if (isBalanceInsufficient.value) {
    toast.error('餘額不足，請先儲值')
    showTopUpModal.value = true
    return
  }

  if (!confirm('確定要使用錢包餘額結帳嗎？')) return

  isProcessing.value = true
  try {
    await api.post('/orders', {
      coupon_code: appliedCoupon.value ? appliedCoupon.value.code : null
    })
    toast.success('訂單支付成功！')
    cartItems.value = []
    discountAmount.value = 0
    appliedCoupon.value = null
    selectedCouponId.value = ''
    // Refresh balance just in case user stays here? Usually redirect.
    router.push('/profile')
  } catch (error) {
    console.error('Checkout error:', error)
    if (error.response?.status === 402) {
      toast.error('餘額不足，請先儲值')
      showTopUpModal.value = true
    } else {
      toast.error(error.response?.data?.message || '結帳失敗')
    }
  } finally {
    isProcessing.value = false
  }
}

function handleTopUpSuccess (data) {
  userBalance.value = data.balance
  // Auto-close handled by component emit usually, but here we just update data
  toast.success('儲值成功，您可以繼續結帳了')
}

function handleAddressSuccess (newAddress) {
  userAddress.value = newAddress
  toast.success('地址已設定，請再次點擊結帳')
  // We could auto-trigger checkout here, but let's let user confirm.
  // checkout() // Potentially risky if user wants to review.
}
</script>

<template>
  <div class="bg-gray-100 font-sans text-gray-700 min-h-screen">
    <main class="container mx-auto px-4 py-8">

        <div class="bg-orange-50 border border-orange-200 text-orange-800 px-4 py-3 rounded mb-6 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-truck-fast"></i>
                <span>還差 <span class="font-bold">NT$ 800</span> 可享免運優惠！</span>
            </div>
            <a href="#" class="text-sm underline hover:text-xieOrange">去湊單 &rarr;</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <div class="lg:col-span-8 space-y-4">

                <div class="bg-white px-6 py-3 rounded-t-lg shadow-sm border-b border-gray-100 hidden md:grid grid-cols-12 text-sm text-gray-500 font-bold">
                    <div class="col-span-6">商品資料</div>
                    <div class="col-span-2 text-center">單價</div>
                    <div class="col-span-2 text-center">數量</div>
                    <div class="col-span-1 text-center">小計</div>
                    <div class="col-span-1 text-right">操作</div>
                </div>

                <div v-if="cartItems.length === 0" class="bg-white p-8 text-center text-gray-500 rounded-lg shadow-sm">
                    購物車目前是空的～
                </div>

                <div v-else v-for="item in cartItems" :key="item.id" class="bg-white p-4 md:px-6 md:py-4 rounded-lg shadow-sm grid grid-cols-1 md:grid-cols-12 gap-4 items-center relative group">
                    <div class="col-span-6 flex gap-4 items-center">
                        <input type="checkbox" checked class="w-4 h-4 text-xieOrange focus:ring-xieOrange border-gray-300 rounded">
                        <div class="w-20 h-20 bg-gray-100 rounded border border-gray-200 flex items-center justify-center overflow-hidden shrink-0">
                            <i class="fas fa-box-open text-3xl text-gray-300"></i>
                             </div>
                        <div>
                            <h3 class="font-bold text-gray-800 line-clamp-2">{{ item.name }}</h3>
                            <div class="text-xs text-gray-500 mt-1">規格：預設</div>
                            <div class="text-xs text-green-600 mt-1"><i class="fas fa-check-circle"></i> 24h 到貨</div>
                        </div>
                    </div>

                    <div class="col-span-2 text-center text-sm text-gray-500">
                        <span class="md:hidden mr-2">單價:</span>$ {{ item.price }}
                    </div>

                    <div class="col-span-2 flex justify-center">
                        <div class="flex items-center border border-gray-300 rounded overflow-hidden h-8">
                            <button class="px-2 bg-gray-50 hover:bg-gray-200 text-gray-600" @click="updateQuantity(item, -1)" :disabled="item.quantity <= 1">-</button>
                            <input type="text" :value="item.quantity" class="w-10 text-center text-sm focus:outline-none border-x border-gray-300" readonly>
                            <button class="px-2 bg-gray-50 hover:bg-gray-200 text-gray-600" @click="updateQuantity(item, 1)">+</button>
                        </div>
                    </div>

                    <div class="col-span-1 text-center font-bold text-xieOrange">
                        $ {{ item.price * item.quantity }}
                    </div>

                    <div class="col-span-1 text-right">
                        <button class="text-gray-400 hover:text-red-500 transition p-2" @click="removeItem(item.id)">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-lg shadow-sm border border-orange-100">
                    <h4 class="font-bold text-gray-700 mb-3 text-sm">
                        <i class="fas fa-fire text-red-500 mr-1"></i> 超值加價購
                    </h4>
                    <div class="flex gap-4 overflow-x-auto pb-2">
                        <div class="flex-shrink-0 w-32 border border-gray-200 rounded p-2 text-center group cursor-pointer hover:border-xieOrange transition bg-white">
                            <div class="h-16 bg-gray-100 mb-2 flex items-center justify-center text-gray-300 rounded"><i class="fas fa-plug"></i></div>
                            <div class="text-xs truncate mb-1">20W 快速充電頭</div>
                            <div class="text-xieOrange font-bold text-sm">$290</div>
                            <button class="mt-1 text-xs bg-gray-100 hover:bg-xieOrange hover:text-white w-full py-1 rounded transition">加入</button>
                        </div>
                        <div class="flex-shrink-0 w-32 border border-gray-200 rounded p-2 text-center group cursor-pointer hover:border-xieOrange transition bg-white">
                            <div class="h-16 bg-gray-100 mb-2 flex items-center justify-center text-gray-300 rounded"><i class="fas fa-mobile"></i></div>
                            <div class="text-xs truncate mb-1">螢幕保護貼</div>
                            <div class="text-xieOrange font-bold text-sm">$199</div>
                            <button class="mt-1 text-xs bg-gray-100 hover:bg-xieOrange hover:text-white w-full py-1 rounded transition">加入</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-4">
                <div class="bg-white p-6 rounded-lg shadow-lg sticky top-24 border-t-4 border-xieOrange">
                    <h3 class="font-bold text-lg mb-4 text-gray-800">訂單摘要</h3>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">使用優惠券 / 折扣碼</label>
                        <div class="flex gap-2 mb-2">
                            <input type="text" placeholder="輸入代碼" class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-xieOrange">
                            <button class="bg-gray-800 text-white px-3 py-2 rounded text-sm hover:bg-gray-700">套用</button>
                        </div>
                        <div>
                            <select v-model="selectedCouponId" @change="applyCoupon" class="w-full border border-gray-300 rounded px-3 py-2 text-sm text-gray-600 focus:outline-none focus:border-xieOrange">
                                <option value="">選擇可用的優惠券</option>
                                <option v-for="coupon in availableCoupons" :key="coupon.id" :value="coupon.id">
                                    {{ coupon.code }} - {{ coupon.type === 'fixed' ? '$' + coupon.discount_amount : coupon.discount_amount + '%' }} OFF
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-3 text-sm text-gray-600 border-b border-gray-100 pb-4 mb-4">
                        <div class="flex justify-between">
                            <span>商品總計 ({{ cartItems.length }}件)</span>
                            <span>$ {{ cartTotal }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>運費 (宅配)</span>
                            <span>$0</span>
                        </div>
                        <div class="flex justify-between text-xieOrange" v-if="memberDiscountAmount > 0">
                            <span>會員折扣 ({{ userLevel.toUpperCase() }})</span>
                            <span>-$ {{ memberDiscountAmount }}</span>
                        </div>
                        <div class="flex justify-between text-green-600" v-if="discountAmount > 0">
                            <span>活動折扣</span>
                            <span>-$ {{ discountAmount }}</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-end mb-6">
                        <span class="font-bold text-gray-800">應付總金額</span>
                        <div class="text-right">
                            <span class="text-3xl font-bold text-xieOrange">$ {{ finalTotal }}</span>
                        </div>
                    </div>

                    <!-- Insufficient Balance Warning -->
                    <div v-if="isBalanceInsufficient" class="mb-4 bg-red-50 border border-red-200 rounded p-3 text-red-700 text-sm flex items-start gap-2">
                         <i class="fas fa-exclamation-circle mt-0.5"></i>
                         <div>
                             <p class="font-bold">餘額不足 (現有 NT$ {{ userBalance }})</p>
                             <p class="text-xs">請先儲值後再進行結帳付款。</p>
                         </div>
                    </div>

                    <div v-if="isBalanceInsufficient" class="flex gap-2 mb-3">
                         <button @click="showTopUpModal = true" class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg text-lg hover:bg-blue-700 transition shadow-md">
                             <i class="fas fa-wallet mr-1"></i> 立即儲值
                         </button>
                    </div>

                    <button v-else class="w-full bg-xieOrange text-white font-bold py-3 rounded-lg text-lg hover:bg-orange-600 transition shadow-md mb-3 disabled:opacity-50 disabled:cursor-not-allowed" @click="checkout" :disabled="isProcessing">
                        <i v-if="isProcessing" class="fas fa-spinner fa-spin mr-2"></i>
                        {{ isProcessing ? '處理中...' : '確認付款' }}
                    </button>

                    <router-link to="/items" class="block text-center text-gray-500 text-sm hover:text-xieOrange underline">繼續購物</router-link>

                    <div class="mt-6 flex justify-center gap-3 opacity-50 grayscale">
                        <i class="fab fa-cc-visa text-2xl"></i>
                        <i class="fab fa-cc-mastercard text-2xl"></i>
                        <i class="fab fa-cc-jcb text-2xl"></i>
                        <i class="fas fa-lock text-2xl"></i>
                    </div>
                </div>
            </div>

        </div>
    </main>
    <UserTopUpModal :visible="showTopUpModal" @close="showTopUpModal = false" @success="handleTopUpSuccess" />
    <InlineAddressModal :visible="showAddressModal" @close="showAddressModal = false" @success="handleAddressSuccess" />
  </div>
</template>

<style scoped>
/* No custom styles needed, using Tailwind classes */
</style>

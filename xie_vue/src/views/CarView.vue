<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'
import { useRouter } from 'vue-router'

const router = useRouter()
const cartItems = ref([])
const availableCoupons = ref([])
const selectedCouponId = ref('')
const discountAmount = ref(0)
const appliedCoupon = ref(null)

onMounted(() => {
  fetchCart()
  fetchCoupons()
})

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
    alert('請先登入')
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

const finalTotal = computed(() => {
  return Math.max(0, Math.round(cartTotal.value - discountAmount.value))
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
    alert(`優惠卷已套用：${response.data.message}`)
  } catch (error) {
    console.error('Coupon error:', error)
    alert(error.response?.data?.message || '優惠卷無效')
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
  } catch (error) {
    console.error('Remove item error:', error)
    alert('刪除失敗')
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
    alert('更新數量失敗')
  }
}

async function checkout () {
  if (cartItems.value.length === 0) return
  if (!confirm('確定要結帳嗎？')) return

  try {
    await api.post('/orders', {
      coupon_code: appliedCoupon.value ? appliedCoupon.value.code : null
    })
    alert('訂單已送出！')
    cartItems.value = []
    discountAmount.value = 0
    appliedCoupon.value = null
    selectedCouponId.value = ''
    router.push('/profile')
  } catch (error) {
    console.error('Checkout error:', error)
    alert('結帳失敗')
  }
}
</script>

<template>
  <div class="cart-container">
    <h1 class="cart-title">🛒 購物車</h1>

    <div v-if="cartItems.length === 0" class="empty-cart">
      購物車目前是空的～
    </div>

    <div v-else>
        <table class="cart-table">
        <thead>
            <tr>
            <th>商品名稱</th>
            <th>單價</th>
            <th>數量</th>
            <th>小計</th>
            <th>操作</th>
            </tr>
        </thead>

        <tbody>
            <tr v-for="item in cartItems" :key="item.id">
            <td>{{ item.name }}</td>
            <td>$ {{ item.price }}</td>
            <td class="quantity-cell">
              <button class="qty-btn" @click="updateQuantity(item, -1)" :disabled="item.quantity <= 1">-</button>
              <span class="qty-value">{{ item.quantity }}</span>
              <button class="qty-btn" @click="updateQuantity(item, 1)">+</button>
            </td>
            <td>$ {{ item.price * item.quantity }}</td>
            <td>
                <button class="remove-btn" @click="removeItem(item.id)">刪除</button>
            </td>
            </tr>
        </tbody>
        </table>

        <div class="coupon-section">
            <label>選擇優惠卷：</label>
            <select v-model="selectedCouponId" @change="applyCoupon" class="coupon-select">
                <option value="">不使用優惠卷</option>
                <option v-for="coupon in availableCoupons" :key="coupon.id" :value="coupon.id">
                    {{ coupon.code }} - {{ coupon.type === 'fixed' ? '$' + coupon.discount_amount : coupon.discount_amount + '%' }} OFF
                </option>
            </select>
        </div>

        <div class="cart-total">
            <div v-if="discountAmount > 0" class="discount-row">
                折扣：<span class="discount-amount">- $ {{ discountAmount }}</span>
            </div>
            總金額：<span class="total-amount">$ {{ finalTotal }}</span>
            <button class="checkout-btn" @click="checkout">前往結帳</button>
        </div>
    </div>
  </div>
</template>

<style scoped>
.cart-container {
  max-width: 900px;
  margin: 40px auto;
  padding: 16px;
}

.cart-title {
  font-size: 28px;
  margin-bottom: 20px;
  font-weight: bold;
}

.cart-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}

.cart-table th,
.cart-table td {
  padding: 12px;
  border-bottom: 1px solid #ddd;
  text-align: left;
}

.remove-btn {
  background-color: #e74c3c;
  border: none;
  padding: 6px 12px;
  border-radius: 4px;
  color: white;
  cursor: pointer;
}

.remove-btn:hover {
  background-color: #c0392b;
}

.cart-total {
  font-size: 22px;
  font-weight: bold;
  text-align: right;
  margin-top: 20px;
}

.total-amount {
  color: #27ae60;
}

.checkout-btn {
  margin-left: 20px;
  padding: 10px 20px;
  background-color: #3498db;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.checkout-btn:hover {
  background-color: #2980b9;
}

.coupon-section {
    margin-top: 20px;
    text-align: right;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 10px;
}

.coupon-select {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    min-width: 200px;
}

.discount-row {
    font-size: 18px;
    color: #e74c3c;
    margin-bottom: 5px;
}

.quantity-cell {
  display: flex;
  align-items: center;
  gap: 8px;
}

.qty-btn {
  width: 24px;
  height: 24px;
  border: 1px solid #ccc;
  background: #f8f9fa;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  color: #333;
}

.qty-btn:hover:not(:disabled) {
  background: #e9ecef;
}

.qty-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.qty-value {
  min-width: 20px;
  text-align: center;
  font-weight: bold;
}
</style>

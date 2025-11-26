<script setup>
import { ref, computed } from 'vue'

// 假資料
const cartItems = ref([
  { id: 1, name: '黑色T-shirt', price: 299, quantity: 2 },
  { id: 2, name: '白色帽子', price: 450, quantity: 1 },
  { id: 3, name: '牛仔褲', price: 799, quantity: 1 }
])

const cartTotal = computed(() =>
  cartItems.value.reduce((sum, item) => sum + item.price * item.quantity, 0)
)

function removeItem (id) {
  cartItems.value = cartItems.value.filter((i) => i.id !== id)
}
</script>

<template>
  <div class="cart-container">
    <h1 class="cart-title">🛒 購物車</h1>

    <div v-if="cartItems.length === 0" class="empty-cart">
      購物車目前是空的～
    </div>

    <table v-else class="cart-table">
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
          <td>{{ item.quantity }}</td>
          <td>$ {{ item.price * item.quantity }}</td>
          <td>
            <button class="remove-btn" @click="removeItem(item.id)">刪除</button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="cartItems.length > 0" class="cart-total">
      總金額：<span class="total-amount">$ {{ cartTotal }}</span>
      <button class="checkout-btn">前往結帳</button>
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
</style>

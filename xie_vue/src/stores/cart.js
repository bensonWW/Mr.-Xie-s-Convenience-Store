import { defineStore } from 'pinia'
import api from '../services/api'
import { useToast } from 'vue-toastification'

const toast = useToast()

export const useCartStore = defineStore('cart', {
    state: () => ({
        items: [],
        count: 0,
        totalAmount: 0 // In cents if backend sends cents, or dollars. Will adjust based on API.
    }),

    getters: {
        cartCount: (state) => state.count,
        cartTotal: (state) => state.totalAmount
    },

    actions: {
        async fetchCart() {
            try {
                const res = await api.get('/cart')
                // Assuming backend structure: { items: [], total_amount: 0, ... }
                // or array of items. Let's assume standard response structure.
                // Based on previous Context, CartController::index returns Cart Model or Items.
                // Let's inspect CartController if needed, but for now assume typical structure.
                // If it returns just items array:
                if (Array.isArray(res.data)) {
                    this.items = res.data
                    this.count = this.items.reduce((sum, item) => sum + item.quantity, 0)
                    // Security Fix from Professor: Do not calculate price on frontend
                    this.totalAmount = res.data.total_amount || 0
                } else if (res.data.items) {
                    // If wrapped
                    this.items = res.data.items
                    this.count = res.data.items.reduce((sum, item) => sum + item.quantity, 0)
                    this.totalAmount = res.data.total_amount || 0
                }
            } catch (e) {
                console.error('Fetch cart error', e)
            }
        },

        async addToCart(productId, quantity = 1) {
            try {
                await api.post('/cart/items', {
                    product_id: productId,
                    quantity: quantity
                })
                toast.success('已加入購物車')
                await this.fetchCart() // Refresh state
            } catch (e) {
                console.error(e)
                const msg = e.response?.data?.message || '加入失敗'
                toast.error(msg)
            }
        },

        async updateItem(itemId, quantity) {
            try {
                await api.put(`/cart/items/${itemId}`, { quantity })
                await this.fetchCart()
            } catch (e) {
                console.error(e)
                // toast.error('更新失敗')
            }
        },

        async removeItem(itemId) {
            try {
                await api.delete(`/cart/items/${itemId}`)
                toast.success('已移除商品')
                await this.fetchCart()
            } catch (e) {
                console.error(e)
                toast.error('移除失敗')
            }
        }
    }
})

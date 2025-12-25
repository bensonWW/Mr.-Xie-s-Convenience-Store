import { describe, it, expect, beforeEach, vi } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import { useCartStore } from '@/stores/cart'

// Mock the API module
vi.mock('@/services/api', () => ({
    default: {
        get: vi.fn(),
        post: vi.fn(),
        put: vi.fn(),
        delete: vi.fn()
    }
}))

// Mock vue-toastification
vi.mock('vue-toastification', () => ({
    useToast: () => ({
        success: vi.fn(),
        error: vi.fn()
    })
}))

import api from '@/services/api'

describe('Cart Store', () => {
    let store

    beforeEach(() => {
        setActivePinia(createPinia())
        store = useCartStore()
        vi.clearAllMocks()
    })

    describe('Initial State', () => {
        it('should have empty initial state', () => {
            expect(store.items).toEqual([])
            expect(store.count).toBe(0)
            expect(store.totalAmount).toBe(0)
        })

        it('should have correct getters', () => {
            expect(store.cartCount).toBe(0)
            expect(store.cartTotal).toBe(0)
        })
    })

    describe('fetchCart', () => {
        it('should fetch and set cart items from array response', async () => {
            const mockItems = [
                { id: 1, product_id: 1, quantity: 2, product: { name: 'Product 1' } },
                { id: 2, product_id: 2, quantity: 3, product: { name: 'Product 2' } }
            ]
            mockItems.total_amount = 50000

            api.get.mockResolvedValueOnce({ data: mockItems })

            await store.fetchCart()

            expect(api.get).toHaveBeenCalledWith('/cart')
            expect(store.items).toEqual(mockItems)
            expect(store.count).toBe(5) // 2 + 3
        })

        it('should fetch and set cart items from wrapped response', async () => {
            const mockResponse = {
                items: [
                    { id: 1, product_id: 1, quantity: 1 },
                    { id: 2, product_id: 2, quantity: 4 }
                ],
                total_amount: 30000
            }

            api.get.mockResolvedValueOnce({ data: mockResponse })

            await store.fetchCart()

            expect(store.items).toEqual(mockResponse.items)
            expect(store.count).toBe(5) // 1 + 4
            expect(store.totalAmount).toBe(30000)
        })

        it('should handle fetch error gracefully', async () => {
            api.get.mockRejectedValueOnce(new Error('Network error'))

            await store.fetchCart()

            // Should not throw, items should remain empty
            expect(store.items).toEqual([])
        })
    })

    describe('addToCart', () => {
        it('should add item and call API correctly', async () => {
            api.post.mockResolvedValueOnce({ data: { success: true } })
            // fetchCart is called after, mock it to return updated cart
            api.get.mockResolvedValueOnce({ data: [{ id: 1, quantity: 2 }] })

            await store.addToCart(123, 2)

            expect(api.post).toHaveBeenCalledWith('/cart/items', {
                product_id: 123,
                quantity: 2
            })
        })

        it('should use default quantity of 1', async () => {
            api.post.mockResolvedValueOnce({ data: { success: true } })
            api.get.mockResolvedValueOnce({ data: [{ id: 1, quantity: 1 }] })

            await store.addToCart(456)

            expect(api.post).toHaveBeenCalledWith('/cart/items', {
                product_id: 456,
                quantity: 1
            })
        })

        it('should throw and show error on failure', async () => {
            const error = new Error('API Error')
            error.response = { status: 400, data: { message: 'Out of stock' } }
            api.post.mockRejectedValueOnce(error)

            await expect(store.addToCart(789)).rejects.toThrow()
        })
    })

    describe('updateItem', () => {
        it('should update item quantity and refresh cart', async () => {
            api.put.mockResolvedValueOnce({ data: { success: true } })
            api.get.mockResolvedValueOnce({ data: [] })

            await store.updateItem(1, 5)

            expect(api.put).toHaveBeenCalledWith('/cart/items/1', { quantity: 5 })
            expect(api.get).toHaveBeenCalled()
        })

        it('should handle update error gracefully', async () => {
            api.put.mockRejectedValueOnce(new Error('Update failed'))

            // Should not throw
            await store.updateItem(1, 5)
        })
    })

    describe('removeItem', () => {
        it('should remove item and refresh cart', async () => {
            api.delete.mockResolvedValueOnce({ data: { success: true } })
            api.get.mockResolvedValueOnce({ data: [] })

            await store.removeItem(1)

            expect(api.delete).toHaveBeenCalledWith('/cart/items/1')
            expect(api.get).toHaveBeenCalled()
        })

        it('should handle remove error', async () => {
            api.delete.mockRejectedValueOnce(new Error('Delete failed'))

            // Should not throw, error is caught internally
            await store.removeItem(1)
        })
    })

    describe('Computed Values', () => {
        it('should calculate count from items', async () => {
            const mockItems = [
                { id: 1, quantity: 10 },
                { id: 2, quantity: 5 },
                { id: 3, quantity: 3 }
            ]
            api.get.mockResolvedValueOnce({ data: mockItems })

            await store.fetchCart()

            expect(store.count).toBe(18)
            expect(store.cartCount).toBe(18)
        })
    })
})

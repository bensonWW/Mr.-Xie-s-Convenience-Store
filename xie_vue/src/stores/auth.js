import { defineStore } from 'pinia'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        loading: false,
        error: null,
        initialized: false
    }),
    getters: {
        isLoggedIn: (state) => !!state.user,
        isAdmin: (state) => state.user?.role === 'admin' || state.user?.role === 'staff',
        currentUser: (state) => state.user
    },
    actions: {
        async fetchUser() {
            this.loading = true
            try {
                const response = await api.get('/user')
                this.user = response.data
            } catch (error) {
                if (error.response?.status === 401) {
                    this.user = null
                } else {
                    console.error('Fetch user failed', error)
                }
            } finally {
                this.loading = false
                this.initialized = true
            }
        },
        async login(credentials) {
            this.loading = true
            this.error = null
            try {
                // CSRF protection for SPA
                await api.get('/sanctum/csrf-cookie')
                // Login matches Laravel AuthController (web guard via Sanctum)
                await api.post('/login', credentials)
                await this.fetchUser()
                return true
            } catch (error) {
                this.error = error.response?.data?.message || 'Login failed'
                throw error
            } finally {
                this.loading = false
            }
        },
        async logout() {
            try {
                await api.post('/logout')
            } catch (err) {
                console.error('Logout error', err)
            } finally {
                this.user = null
            }
        },
        async updateProfile(data) {
            const response = await api.put('/user/profile', data)
            this.user = response.data.user
            return response.data
        }
    }
})

import { createStore } from 'vuex'
import api from '../services/api' // Assuming api service exists for making requests

export default createStore({
  state: {
    user: null,
    token: localStorage.getItem('token') || null,
    isLoggedIn: !!localStorage.getItem('token')
  },
  getters: {
    isLoggedIn: state => !!state.token,
    isAdmin: state => state.user?.role === 'admin' || state.user?.role === 'staff',
    currentUser: state => state.user,
    token: state => state.token
  },
  mutations: {
    SET_TOKEN (state, token) {
      state.token = token
      state.isLoggedIn = !!token
      if (token) {
        localStorage.setItem('token', token)
      } else {
        localStorage.removeItem('token')
      }
    },
    SET_USER (state, user) {
      state.user = user
      if (user && user.role) {
        localStorage.setItem('user_role', user.role)
      } else {
        localStorage.removeItem('user_role')
      }
    },
    CLEAR_AUTH (state) {
      state.token = null
      state.user = null
      state.isLoggedIn = false
      localStorage.removeItem('token')
      localStorage.removeItem('user_role')
    }
  },
  actions: {
    async login ({ commit }, credentials) {
      const response = await api.post('/login', credentials)
      const { access_token: accessToken, user } = response.data
      commit('SET_TOKEN', accessToken)
      commit('SET_USER', user)
      return response
    },
    async logout ({ commit }) {
      try {
        await api.post('/logout')
      } catch (error) {
        console.error('Logout error', error)
      } finally {
        commit('CLEAR_AUTH')
      }
    },
    async checkAuth ({ commit, state }) {
      if (!state.token) {
        commit('CLEAR_AUTH')
        return
      }
      try {
        const response = await api.get('/user')
        commit('SET_USER', response.data)
      } catch (error) {
        console.error('Auth check failed:', error)
        commit('CLEAR_AUTH')
      }
    }
  },
  modules: {
  }
})

import { createStore } from 'vuex'

export default createStore({
  state: {
    isLoggedIn: false
  },
  getters: {
    isLoggedIn (state) {
      return state.isLoggedIn
    }
  },
  mutations: {
    setLoggedIn (state, value) {
      state.isLoggedIn = value
    }
  },
  actions: {
  },
  modules: {
  }
})

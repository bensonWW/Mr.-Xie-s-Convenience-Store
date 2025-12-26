import axios from 'axios'

const api = axios.create({
  baseURL: process.env.VUE_APP_API_URL || 'https://mr-xie-s-convenience-store-main-d3awzd.laravel.cloud/api',
  withCredentials: false, // Token-based auth, no cookies needed
  headers: {
    Accept: 'application/json'
  }
})

// Request interceptor - add Bearer token
api.interceptors.request.use(
  config => {
    const token = localStorage.getItem('auth_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  error => Promise.reject(error)
)

// Response interceptor - handle 401
api.interceptors.response.use(
  response => response,
  error => {
    if (error.response && error.response.status === 401) {
      // Token expired or invalid - clear it
      localStorage.removeItem('auth_token')
    }
    return Promise.reject(error)
  }
)

export default api


import axios from 'axios'

const api = axios.create({
  baseURL: process.env.VUE_APP_API_URL || 'https://mr-xie-s-convenience-store-main-d3awzd.laravel.cloud/api',
  withCredentials: true, // Enable HttpOnly Cookies
  headers: {
    Accept: 'application/json'
  }
})

// Interceptors (Token logic removed for HttpOnly migration)
api.interceptors.response.use(
  response => response,
  error => {
    // Optional: Global 401 handling
    if (error.response && error.response.status === 401) {
      // Redirect to login or clear state?
      // window.location.href = '/login'; // Maybe too aggressive
    }
    return Promise.reject(error)
  }
)

export default api

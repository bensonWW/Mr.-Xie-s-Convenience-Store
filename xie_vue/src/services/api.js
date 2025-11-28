import axios from 'axios'

const api = axios.create({
  baseURL: 'https://mr-xie-s-convenience-store-main-d3awzd.laravel.cloud/api',
  headers: {
    Accept: 'application/json'
  }
})

// Add a request interceptor to attach the token
api.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export default api

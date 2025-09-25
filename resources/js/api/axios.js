import axios from 'axios'

// Create axios instance
const api = axios.create({
  baseURL: '/api/v1',
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  }
})

// Request interceptor
api.interceptors.request.use(
  (config) => {
    // Add auth token if available
    const token = localStorage.getItem('auth_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    
    // Add CSRF token for Laravel
    const csrfToken = document.querySelector('meta[name="csrf-token"]')
    if (csrfToken) {
      config.headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content')
    }
    
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor
api.interceptors.response.use(
  (response) => {
    return response
  },
  (error) => {
    // Handle common errors
    if (error.response?.status === 401) {
      // Unauthorized - redirect to login
      localStorage.removeItem('auth_token')
      window.location.href = '/login'
    } else if (error.response?.status === 422) {
      // Validation errors
      console.error('Validation Error:', error.response.data.errors)
    } else if (error.response?.status >= 500) {
      // Server errors
      console.error('Server Error:', error.response.data.message)
    }
    
    return Promise.reject(error)
  }
)

export { api }

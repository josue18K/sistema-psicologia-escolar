import { boot } from 'quasar/wrappers'
import axios from 'axios'

// Configuración base de Axios para conectar con tu backend Laravel
const api = axios.create({
  baseURL: 'http://localhost:8000/api', // Ajusta el puerto si es necesario
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  withCredentials: true, // Importante para Sanctum
  timeout: 10000
})

// Interceptor para requests
api.interceptors.request.use(
  (config) => {
    // Aquí agregaremos el token cuando actives Sanctum
    // const token = localStorage.getItem('auth_token')
    // if (token) {
    //   config.headers.Authorization = `Bearer ${token}`
    // }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Interceptor para responses
api.interceptors.response.use(
  (response) => {
    return response
  },
  (error) => {
    if (error.response?.status === 401) {
      // Redirigir a login si no está autenticado
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user_data')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export default boot(({ app }) => {
  app.config.globalProperties.$axios = axios
  app.config.globalProperties.$api = api
})

export { axios, api }

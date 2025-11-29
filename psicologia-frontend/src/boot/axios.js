import { boot } from 'quasar/wrappers'
import axios from 'axios'
import { Notify } from 'quasar'

// Crear instancia de axios
const api = axios.create({
baseURL: 'http://52.15.51.86/api', // URL de tu backend Laravel http://localhost:8000/api
headers: {
'Content-Type': 'application/json',
'Accept': 'application/json'
}
})

// Interceptor de Request - Agregar token a todas las peticiones
api.interceptors.request.use(
(config) => {
const token = localStorage.getItem('token')
if (token) {
  config.headers.Authorization = `Bearer ${token}`
}
return config
},
(error) => {
return Promise.reject(error)
}
)

// Interceptor de Response - Manejar errores globalmente
api.interceptors.response.use(
(response) => {
return response
},
(error) => {
if (error.response) {
// Token expirado o no autorizado
if (error.response.status === 401) {
localStorage.removeItem('token')
  localStorage.removeItem('user')
    Notify.create({
      type: 'negative',
      message: 'Sesión expirada. Por favor inicia sesión nuevamente.',
      position: 'top'
    })

    // Redirigir al login
    window.location.href = '/login'
  }

  // Error de validación
  if (error.response.status === 422) {
    const errors = error.response.data.errors
    if (errors) {
      Object.values(errors).forEach((errorMessages) => {
        errorMessages.forEach((message) => {
          Notify.create({
            type: 'negative',
            message: message,
            position: 'top'
          })
        })
      })
    }
  }

  // Error del servidor
  if (error.response.status >= 500) {
    Notify.create({
      type: 'negative',
      message: 'Error del servidor. Intenta nuevamente.',
      position: 'top'
    })
  }
} else if (error.request) {
  Notify.create({
    type: 'negative',
    message: 'No se pudo conectar con el servidor.',
    position: 'top'
  })
}

return Promise.reject(error)
}
)

export default boot(({ app }) => {
// Para uso dentro de componentes: this.$axios y this.$api
app.config.globalProperties.$axios = axios
app.config.globalProperties.$api = api
})

export { api }

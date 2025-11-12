import { defineStore } from 'pinia'
import { authService } from 'src/services/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null,
    isAuthenticated: false
  }),

  getters: {
    getUser: (state) => state.user,
    isAuth: (state) => state.isAuthenticated,
    getUserRole: (state) => state.user?.rol,
    isTOE: (state) => state.user?.rol === 'TOE',
    isPsicologo: (state) => state.user?.rol === 'PSICOLOGO',
    isTutor: (state) => state.user?.rol === 'TUTOR',
    isDirector: (state) => state.user?.rol === 'DIRECTOR'
  },

  actions: {
    async login(credentials) {
      try {
        const response = await authService.login(credentials)

        if (response.data.user) {
          this.user = response.data.user
          this.isAuthenticated = true

          // Guardar en localStorage (temporal hasta Sanctum)
          localStorage.setItem('user_data', JSON.stringify(this.user))
          localStorage.setItem('isAuthenticated', 'true')

          return { success: true, user: this.user }
        }
      } catch (error) {
        console.error('Error en login:', error)
        return {
          success: false,
          error: error.response?.data?.message || 'Error de conexión'
        }
      }
    },

    async logout() {
      try {
        await authService.logout()
      } catch (error) {
        console.error('Error en logout:', error)
      } finally {
        this.clearAuth()
      }
    },

    async checkAuth() {
      try {
        const response = await authService.me()
        if (response.data.user) {
          this.user = response.data.user
          this.isAuthenticated = true
          return true
        }
      } catch {
        this.clearAuth()
        return false
      }
    },

    clearAuth() {
      this.user = null
      this.token = null
      this.isAuthenticated = false
      localStorage.removeItem('user_data')
      localStorage.removeItem('isAuthenticated')
      localStorage.removeItem('auth_token')
    },

    initializeAuth() {
      // Recuperar datos del localStorage al iniciar la app
      const userData = localStorage.getItem('user_data')
      const isAuth = localStorage.getItem('isAuthenticated')

      if (userData && isAuth === 'true') {
        this.user = JSON.parse(userData)
        this.isAuthenticated = true
      }
    }
  }
})

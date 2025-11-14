import { defineStore } from 'pinia'
import { api } from 'boot/axios'
import { Notify } from 'quasar'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || null,
    token: localStorage.getItem('token') || null,
    isAuthenticated: !!localStorage.getItem('token'),
    loading: false
  }),

  getters: {
    currentUser: (state) => state.user,
    userRole: (state) => state.user?.rol || null,
    isLoggedIn: (state) => state.isAuthenticated,
    isTOE: (state) => state.user?.rol === 'TOE',
    isPsicologo: (state) => state.user?.rol === 'PSICOLOGO',
    isTutor: (state) => state.user?.rol === 'TUTOR',
    isDirector: (state) => state.user?.rol === 'DIRECTOR'
  },

  actions: {
    async login(credentials) {
      this.loading = true
      try {
        const response = await api.post('/login', credentials)

        const { token, user } = response.data

        localStorage.setItem('token', token)
        localStorage.setItem('user', JSON.stringify(user))

        this.token = token
        this.user = user
        this.isAuthenticated = true

        Notify.create({
          type: 'positive',
          message: `Bienvenido ${user.name}`,
          position: 'top'
        })

        return { success: true, user }
      } catch (error) {
        Notify.create({
          type: 'negative',
          message: error.response?.data?.message || 'Error al iniciar sesión',
          position: 'top'
        })
        return { success: false, error }
      } finally {
        this.loading = false
      }
    },

    async logout() {
      this.loading = true
      try {
        await api.post('/logout')
      } catch (error) {
        console.error('Error al cerrar sesión:', error)
      } finally {
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        this.token = null
        this.user = null
        this.isAuthenticated = false
        this.loading = false

        Notify.create({
          type: 'info',
          message: 'Sesión cerrada exitosamente',
          position: 'top'
        })
      }
    },

    async checkAuth() {
      if (!this.token) {
        return false
      }

      try {
        const response = await api.get('/me')
        this.user = response.data.user
        this.isAuthenticated = true
        return true
      } catch {
        this.logout()
        return false
      }
    }
  }
})

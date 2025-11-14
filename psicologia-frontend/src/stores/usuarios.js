import { defineStore } from 'pinia'
import { api } from 'boot/axios'
import { Notify } from 'quasar'

export const useUsuariosStore = defineStore('usuarios', {
  state: () => ({
    usuarios: [],
    loading: false
  }),

  getters: {
    tutores: (state) => state.usuarios.filter(u => u.rol === 'TUTOR' && u.estado),
    psicologos: (state) => state.usuarios.filter(u => u.rol === 'PSICOLOGO' && u.estado)
  },

  actions: {
    async fetchAll(filters = {}) {
      this.loading = true
      try {
        const params = new URLSearchParams(filters).toString()
        const response = await api.get(`/users?${params}`)
        this.usuarios = response.data.users
      } catch (error) {
        console.error('Error al cargar usuarios:', error)
      } finally {
        this.loading = false
      }
    },

    async create(userData) {
      this.loading = true
      try {
        const response = await api.post('/users', userData)
        this.usuarios.unshift(response.data.user)

        Notify.create({
          type: 'positive',
          message: 'Usuario creado exitosamente',
          position: 'top'
        })

        return { success: true }
      } catch (error) {
        return { success: false, error }
      } finally {
        this.loading = false
      }
    },

    async update(id, userData) {
      this.loading = true
      try {
        const response = await api.put(`/users/${id}`, userData)
        const index = this.usuarios.findIndex(u => u.id === id)
        if (index !== -1) {
          this.usuarios[index] = response.data.user
        }

        Notify.create({
          type: 'positive',
          message: 'Usuario actualizado exitosamente',
          position: 'top'
        })

        return { success: true }
      } catch (error) {
        return { success: false, error }
      } finally {
        this.loading = false
      }
    },

    async delete(id) {
      this.loading = true
      try {
        await api.delete(`/users/${id}`)
        this.usuarios = this.usuarios.filter(u => u.id !== id)

        Notify.create({
          type: 'positive',
          message: 'Usuario eliminado exitosamente',
          position: 'top'
        })

        return { success: true }
      } catch (error) {
        return { success: false, error }
      } finally {
        this.loading = false
      }
    },

    async toggleEstado(id) {
      try {
        const response = await api.patch(`/users/${id}/toggle-estado`)
        const index = this.usuarios.findIndex(u => u.id === id)
        if (index !== -1) {
          this.usuarios[index] = response.data.user
        }

        Notify.create({
          type: 'positive',
          message: response.data.message,
          position: 'top'
        })

        return { success: true }
      } catch (error) {
        return { success: false, error }
      }
    },

    async fetchTutores() {
      try {
        const response = await api.get('/tutores')
        return response.data.tutores
      } catch (error) {
        console.error('Error al cargar tutores:', error)
        return []
      }
    },

    async fetchPsicologos() {
      try {
        const response = await api.get('/psicologos')
        return response.data.psicologos
      } catch (error) {
        console.error('Error al cargar psicólogos:', error)
        return []
      }
    }
  }
})

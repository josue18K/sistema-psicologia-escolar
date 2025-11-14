import { defineStore } from 'pinia'
import { api } from 'boot/axios'

export const useApoderadosStore = defineStore('apoderados', {
  state: () => ({
    apoderados: [],
    loading: false
  }),

  actions: {
    async fetchAll() {
      this.loading = true
      try {
        const response = await api.get('/apoderados')
        this.apoderados = response.data.apoderados
      } catch (error) {
        console.error('Error al cargar apoderados:', error)
      } finally {
        this.loading = false
      }
    },

    async create(apoderadoData) {
      try {
        const response = await api.post('/apoderados', apoderadoData)
        this.apoderados.unshift(response.data.apoderado)
        return { success: true, apoderado: response.data.apoderado }
      } catch (error) {
        return { success: false, error }
      }
    }
  }
})

import { defineStore } from 'pinia'
import { api } from 'boot/axios'
import { Notify } from 'quasar'

export const useCitasStore = defineStore('citas', {
  state: () => ({
    citas: [],
    citasPendientes: [],
    loading: false
  }),

  actions: {
    async fetchAll() {
      this.loading = true
      try {
        const response = await api.get('/citas')
        this.citas = response.data.citas
      } catch (error) {
        console.error('Error al cargar citas:', error)
      } finally {
        this.loading = false
      }
    },

    async fetchPendientes() {
      try {
        const response = await api.get('/citas-pendientes')
        this.citasPendientes = response.data.citas
      } catch (error) {
        console.error('Error al cargar citas pendientes:', error)
      }
    },

    async create(citaData) {
      this.loading = true
      try {
        const response = await api.post('/citas', citaData)
        this.citas.unshift(response.data.cita)

        Notify.create({
          type: 'positive',
          message: 'Cita registrada exitosamente',
          position: 'top'
        })

        return { success: true }
      } catch (error) {
        return { success: false, error }
      } finally {
        this.loading = false
      }
    },

    async update(id, citaData) {
      this.loading = true
      try {
        const response = await api.put(`/citas/${id}`, citaData)
        const index = this.citas.findIndex(c => c.id === id)
        if (index !== -1) {
          this.citas[index] = response.data.cita
        }

        Notify.create({
          type: 'positive',
          message: 'Cita actualizada exitosamente',
          position: 'top'
        })

        return { success: true }
      } catch (error) {
        return { success: false, error }
      } finally {
        this.loading = false
      }
    },

    async marcarRealizada(id) {
      try {
        const response = await api.patch(`/citas/${id}/realizada`)
        const index = this.citas.findIndex(c => c.id === id)
        if (index !== -1) {
          this.citas[index] = response.data.cita
        }

        Notify.create({
          type: 'positive',
          message: 'Cita marcada como realizada',
          position: 'top'
        })

        return { success: true }
      } catch (error) {
        return { success: false, error }
      }
    },

    async delete(id) {
      this.loading = true
      try {
        await api.delete(`/citas/${id}`)
        this.citas = this.citas.filter(c => c.id !== id)

        Notify.create({
          type: 'positive',
          message: 'Cita eliminada exitosamente',
          position: 'top'
        })

        return { success: true }
      } catch (error) {
        return { success: false, error }
      } finally {
        this.loading = false
      }
    }
  }
})

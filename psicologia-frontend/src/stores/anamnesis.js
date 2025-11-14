import { defineStore } from 'pinia'
import { api } from 'boot/axios'
import { Notify } from 'quasar'

export const useAnamnesisStore = defineStore('anamnesis', {
  state: () => ({
    anamnesisList: [],
    loading: false
  }),

  actions: {
    async fetchAll() {
      this.loading = true
      try {
        const response = await api.get('/anamnesis')
        this.anamnesisList = response.data.anamnesis
      } catch (error) {
        console.error('Error al cargar anamnesis:', error)
      } finally {
        this.loading = false
      }
    },

    async getByEstudiante(dni) {
      try {
        const response = await api.get(`/anamnesis-estudiante/${dni}`)
        return response.data.anamnesis
      } catch (error) {
        console.error('Error al obtener anamnesis:', error)
        return []
      }
    },

    async verificarExistencia(dni) {
      try {
        const response = await api.get(`/anamnesis-verificar/${dni}`)
        return response.data
      } catch (error) {
        console.error('Error al verificar anamnesis:', error)
        return { existe: false, anamnesis: null }
      }
    },

    async create(anamnesisData) {
      this.loading = true
      try {
        const response = await api.post('/anamnesis', anamnesisData)
        this.anamnesisList.unshift(response.data.anamnesis)

        Notify.create({
          type: 'positive',
          message: 'Anamnesis registrada exitosamente',
          position: 'top'
        })

        return { success: true, anamnesis: response.data.anamnesis }
      } catch (error) {
        return { success: false, error }
      } finally {
        this.loading = false
      }
    },

    async update(id, anamnesisData) {
      this.loading = true
      try {
        const response = await api.put(`/anamnesis/${id}`, anamnesisData)
        const index = this.anamnesisList.findIndex(a => a.id === id)
        if (index !== -1) {
          this.anamnesisList[index] = response.data.anamnesis
        }

        Notify.create({
          type: 'positive',
          message: 'Anamnesis actualizada exitosamente',
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
        await api.delete(`/anamnesis/${id}`)
        this.anamnesisList = this.anamnesisList.filter(a => a.id !== id)

        Notify.create({
          type: 'positive',
          message: 'Anamnesis eliminada exitosamente',
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

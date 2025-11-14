import { defineStore } from 'pinia'
import { api } from 'boot/axios'
import { Notify } from 'quasar'

export const useEstudiantesStore = defineStore('estudiantes', {
  state: () => ({
    estudiantes: [],
    loading: false
  }),

  actions: {
    async fetchAll() {
      this.loading = true
      try {
        const response = await api.get('/estudiantes')
        this.estudiantes = response.data.estudiantes
      } catch (error) {
        console.error('Error al cargar estudiantes:', error)
      } finally {
        this.loading = false
      }
    },

    async search(query) {
      try {
        const response = await api.get(`/estudiante-search?query=${query}`)
        return response.data.estudiantes
      } catch (error) {
        console.error('Error al buscar estudiantes:', error)
        return []
      }
    },

    async getByDni(dni) {
      try {
        const response = await api.get(`/estudiantes/${dni}`)
        return response.data.estudiante
      } catch (error) {
        console.error('Error al obtener estudiante:', error)
        return null
      }
    },

    async create(estudianteData) {
      this.loading = true
      try {
        const response = await api.post('/estudiantes', estudianteData)
        this.estudiantes.unshift(response.data.estudiante)

        Notify.create({
          type: 'positive',
          message: 'Estudiante registrado exitosamente',
          position: 'top'
        })

        return { success: true, estudiante: response.data.estudiante }
      } catch (error) {
        return { success: false, error }
      } finally {
        this.loading = false
      }
    },

    async update(dni, estudianteData) {
      this.loading = true
      try {
        const response = await api.put(`/estudiantes/${dni}`, estudianteData)
        const index = this.estudiantes.findIndex(e => e.dni === dni)
        if (index !== -1) {
          this.estudiantes[index] = response.data.estudiante
        }

        Notify.create({
          type: 'positive',
          message: 'Estudiante actualizado exitosamente',
          position: 'top'
        })

        return { success: true }
      } catch (error) {
        return { success: false, error }
      } finally {
        this.loading = false
      }
    },

    async delete(dni) {
      this.loading = true
      try {
        await api.delete(`/estudiantes/${dni}`)
        this.estudiantes = this.estudiantes.filter(e => e.dni !== dni)

        Notify.create({
          type: 'positive',
          message: 'Estudiante eliminado exitosamente',
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

import { defineStore } from 'pinia'
import { api } from 'boot/axios'
import { Notify } from 'quasar'

export const useDiagnosticosStore = defineStore('diagnosticos', {
  state: () => ({
    diagnosticos: [],
    estadisticas: null,
    loading: false
  }),

  actions: {
    async fetchAll() {
      this.loading = true
      try {
        const response = await api.get('/diagnosticos')
        this.diagnosticos = response.data.diagnosticos
      } catch (error) {
        console.error('Error al cargar diagnósticos:', error)
      } finally {
        this.loading = false
      }
    },

    async getByEstudiante(dni) {
      try {
        const response = await api.get(`/diagnosticos-estudiante/${dni}`)
        return response.data.diagnosticos
      } catch (error) {
        console.error('Error al obtener diagnósticos:', error)
        return []
      }
    },

    async fetchEstadisticas() {
      try {
        const response = await api.get('/diagnosticos-estadisticas')
        this.estadisticas = response.data
        return response.data
      } catch (error) {
        console.error('Error al cargar estadísticas:', error)
        return null
      }
    },

    async create(diagnosticoData) {
      this.loading = true
      try {
        const response = await api.post('/diagnosticos', diagnosticoData)
        this.diagnosticos.unshift(response.data.diagnostico)

        Notify.create({
          type: 'positive',
          message: 'Diagnóstico registrado exitosamente',
          position: 'top'
        })

        return { success: true }
      } catch (error) {
        return { success: false, error }
      } finally {
        this.loading = false
      }
    },

    async update(id, diagnosticoData) {
      this.loading = true
      try {
        const response = await api.put(`/diagnosticos/${id}`, diagnosticoData)
        const index = this.diagnosticos.findIndex(d => d.id === id)
        if (index !== -1) {
          this.diagnosticos[index] = response.data.diagnostico
        }

        Notify.create({
          type: 'positive',
          message: 'Diagnóstico actualizado exitosamente',
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
        await api.delete(`/diagnosticos/${id}`)
        this.diagnosticos = this.diagnosticos.filter(d => d.id !== id)

        Notify.create({
          type: 'positive',
          message: 'Diagnóstico eliminado exitosamente',
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

import { defineStore } from 'pinia'
import { api } from 'boot/axios'
import { Notify } from 'quasar'

export const useReportesStore = defineStore('reportes', {
  state: () => ({
    reportes: [],
    estadisticasGlobales: null,
    loading: false
  }),

  actions: {
    async fetchAll() {
      this.loading = true
      try {
        const response = await api.get('/reportes')
        this.reportes = response.data.reportes
      } catch (error) {
        console.error('Error al cargar reportes:', error)
      } finally {
        this.loading = false
      }
    },

    async fetchEstadisticasGlobales() {
      try {
        const response = await api.get('/reportes-estadisticas-globales')
        this.estadisticasGlobales = response.data
        return response.data
      } catch (error) {
        console.error('Error al cargar estadísticas:', error)
        return null
      }
    },

    async generarMensual(data) {
      this.loading = true
      try {
        const response = await api.post('/reportes-generar-mensual', data)
        this.reportes.unshift(response.data.reporte)

        Notify.create({
          type: 'positive',
          message: 'Reporte generado exitosamente',
          position: 'top'
        })

        return { success: true, reporte: response.data.reporte }
      } catch (error) {
        return { success: false, error }
      } finally {
        this.loading = false
      }
    },

    async create(reporteData) {
      this.loading = true
      try {
        const response = await api.post('/reportes', reporteData)
        this.reportes.unshift(response.data.reporte)

        Notify.create({
          type: 'positive',
          message: 'Reporte creado exitosamente',
          position: 'top'
        })

        return { success: true }
      } catch (error) {
        return { success: false, error }
      } finally {
        this.loading = false
      }
    },

    async update(id, reporteData) {
      this.loading = true
      try {
        const response = await api.put(`/reportes/${id}`, reporteData)
        const index = this.reportes.findIndex(r => r.id === id)
        if (index !== -1) {
          this.reportes[index] = response.data.reporte
        }

        Notify.create({
          type: 'positive',
          message: 'Reporte actualizado exitosamente',
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
        await api.delete(`/reportes/${id}`)
        this.reportes = this.reportes.filter(r => r.id !== id)

        Notify.create({
          type: 'positive',
          message: 'Reporte eliminado exitosamente',
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

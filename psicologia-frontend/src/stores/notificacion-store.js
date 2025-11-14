import { defineStore } from 'pinia'
import { notificacionService } from 'src/services/api'

export const useNotificacionStore = defineStore('notificacion', {
  state: () => ({
    notificaciones: [],
    notificacionesNoLeidas: [],
    totalNoLeidas: 0
  }),

  getters: {
    getNotificaciones: (state) => state.notificaciones,
    getNoLeidas: (state) => state.notificacionesNoLeidas,
    getTotalNoLeidas: (state) => state.totalNoLeidas
  },

  actions: {
    async fetchNotificaciones() {
      try {
        const response = await notificacionService.getNotificaciones()
        this.notificaciones = response.data.notificaciones
        return this.notificaciones
      } catch (error) {
        console.error('Error fetching notificaciones:', error)
        throw error
      }
    },

    async fetchNotificacionesNoLeidas() {
      try {
        const response = await notificacionService.getNoLeidas()
        this.notificacionesNoLeidas = response.data.notificaciones
        this.totalNoLeidas = response.data.total
        return this.notificacionesNoLeidas
      } catch (error) {
        console.error('Error fetching notificaciones no leídas:', error)
        throw error
      }
    },

    async marcarComoLeida(id) {
      try {
        await notificacionService.marcarLeida(id)
        // Actualizar estado local
        const notificacion = this.notificaciones.find(n => n.id === id)
        if (notificacion) {
          notificacion.leida = true
        }
        await this.fetchNotificacionesNoLeidas()
      } catch (error) {
        console.error('Error marcando notificación como leída:', error)
        throw error
      }
    },

    async marcarTodasLeidas() {
      try {
        await notificacionService.marcarTodasLeidas()
        // Actualizar estado local
        this.notificaciones.forEach(n => { n.leida = true })
        this.notificacionesNoLeidas = []
        this.totalNoLeidas = 0
      } catch (error) {
        console.error('Error marcando todas como leídas:', error)
        throw error
      }
    },

    async eliminarNotificacion(id) {
      try {
        await notificacionService.eliminarNotificacion(id)
        this.notificaciones = this.notificaciones.filter(n => n.id !== id)
        await this.fetchNotificacionesNoLeidas()
      } catch (error) {
        console.error('Error eliminando notificación:', error)
        throw error
      }
    }
  }
})

import { defineStore } from 'pinia'
import { api } from 'boot/axios'

export const useNotificacionesStore = defineStore('notificaciones', {
  state: () => ({
    notificaciones: [],
    unreadCount: 0,
    loading: false
  }),

  actions: {
    async fetchAll() {
      this.loading = true
      try {
        const response = await api.get('/notificaciones')
        this.notificaciones = response.data.notificaciones
      } catch (error) {
        console.error('Error al cargar notificaciones:', error)
      } finally {
        this.loading = false
      }
    },

    async fetchUnread() {
      try {
        const response = await api.get('/notificaciones-no-leidas')
        this.unreadCount = response.data.total
      } catch (error) {
        console.error('Error al cargar notificaciones no leídas:', error)
      }
    },

    async marcarLeida(id) {
      try {
        await api.patch(`/notificaciones/${id}/leida`)
        this.unreadCount = Math.max(0, this.unreadCount - 1)

        // Actualizar en el array local
        const notif = this.notificaciones.find(n => n.id === id)
        if (notif) notif.leida = true
      } catch (error) {
        console.error('Error al marcar como leída:', error)
      }
    },

    async marcarTodasLeidas() {
      try {
        await api.post('/notificaciones-marcar-todas-leidas')
        this.unreadCount = 0
        this.notificaciones.forEach(n => n.leida = true)
      } catch (error) {
        console.error('Error al marcar todas como leídas:', error)
      }
    }
  }
})

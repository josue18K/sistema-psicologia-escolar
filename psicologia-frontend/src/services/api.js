import { api } from 'boot/axios'

// Servicio de Autenticación
export const authService = {
  login(credentials) {
    return api.post('/login', credentials)
  },
  logout() {
    return api.post('/logout')
  },
  me() {
    return api.get('/me')
  },
  register(userData) {
    return api.post('/register', userData)
  },
}

// Servicio de Usuarios
export const userService = {
  getUsers(params = {}) {
    return api.get('/users', { params })
  },
  createUser(userData) {
    return api.post('/users', userData)
  },
  getUser(id) {
    return api.get(`/users/${id}`)
  },
  updateUser(id, userData) {
    return api.put(`/users/${id}`, userData)
  },
  deleteUser(id) {
    return api.delete(`/users/${id}`)
  },
  toggleEstado(id) {
    return api.patch(`/users/${id}/toggle-estado`)
  },
  getTutores() {
    return api.get('/tutores')
  },
  getPsicologos() {
    return api.get('/psicologos')
  },
  cambiarPassword(passwordData) {
    return api.post('/cambiar-password', passwordData)
  },
}

// Servicio de Estudiantes
export const estudianteService = {
  getEstudiantes() {
    return api.get('/estudiantes')
  },
  createEstudiante(estudianteData) {
    return api.post('/estudiantes', estudianteData) // NOTA: Cambiar a POST en backend
  },
  getEstudiante(dni) {
    return api.get(`/estudiantes/${dni}`)
  },
  updateEstudiante(dni, estudianteData) {
    return api.put(`/estudiantes/${dni}`, estudianteData)
  },
  deleteEstudiante(dni) {
    return api.delete(`/estudiantes/${dni}`)
  },
  searchEstudiantes(params) {
    return api.get('/estudiante-search', { params })
  },
}

// Servicio de Apoderados
export const apoderadoService = {
  getApoderados() {
    return api.get('/apoderados')
  },
  createApoderado(apoderadoData) {
    return api.post('/apoderados', apoderadoData)
  },
  getApoderado(id) {
    return api.get(`/apoderados/${id}`)
  },
  updateApoderado(id, apoderadoData) {
    return api.put(`/apoderados/${id}`, apoderadoData)
  },
  deleteApoderado(id) {
    return api.delete(`/apoderados/${id}`)
  },
}

export const diagnosticoService = {
  getDiagnosticos(params = {}) {
    return api.get('/diagnosticos', { params })
  },
  createDiagnostico(diagnosticoData) {
    return api.post('/diagnosticos', diagnosticoData)
  },
  getDiagnostico(id) {
    return api.get(`/diagnosticos/${id}`)
  },
  updateDiagnostico(id, diagnosticoData) {
    return api.put(`/diagnosticos/${id}`, diagnosticoData)
  },
  deleteDiagnostico(id) {
    return api.delete(`/diagnosticos/${id}`)
  },
  getByEstudiante(dni) {
    return api.get(`/diagnosticos-estudiante/${dni}`)
  },
  getEstadisticas() {
    return api.get('/diagnosticos-estadisticas')
  },
}

export const citaService = {
  getCitas(params = {}) {
    return api.get('/citas', { params })
  },
  createCita(citaData) {
    return api.post('/citas', citaData)
  },
  getCita(id) {
    return api.get(`/citas/${id}`)
  },
  updateCita(id, citaData) {
    return api.put(`/citas/${id}`, citaData)
  },
  deleteCita(id) {
    return api.delete(`/citas/${id}`)
  },
  getPendientes() {
    return api.get('/citas-pendientes')
  },
  marcarRealizada(id) {
    return api.patch(`/citas/${id}/realizada`)
  },
}

export const reporteService = {
  getReportes() {
    return api.get('/reportes')
  },
  createReporte(reporteData) {
    return api.post('/reportes', reporteData)
  },
  getReporte(id) {
    return api.get(`/reportes/${id}`)
  },
  updateReporte(id, reporteData) {
    return api.put(`/reportes/${id}`, reporteData)
  },
  deleteReporte(id) {
    return api.delete(`/reportes/${id}`)
  },
  generarMensual(psicologoId) {
    return api.post('/reportes-generar-mensual', { psicologo_id: psicologoId })
  },
  getEstadisticasGlobales() {
    return api.get('/reportes-estadisticas-globales')
  },
}

export const notificacionService = {
  getNotificaciones() {
    return api.get('/notificaciones')
  },
  createNotificacion(notificacionData) {
    return api.post('/notificaciones', notificacionData)
  },
  getNotificacion(id) {
    return api.get(`/notificaciones/${id}`)
  },
  deleteNotificacion(id) {
    return api.delete(`/notificaciones/${id}`)
  },
  marcarLeida(id) {
    return api.patch(`/notificaciones/${id}/leida`)
  },
  marcarTodasLeidas() {
    return api.post('/notificaciones-marcar-todas-leidas')
  },
  getNoLeidas() {
    return api.get('/notificaciones-no-leidas')
  },
  getEnviadas() {
    return api.get('/notificaciones-enviadas')
  },
}

export default api

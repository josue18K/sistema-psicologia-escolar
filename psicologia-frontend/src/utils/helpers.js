// Formatear fecha para display
export const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Formatear fecha y hora
export const formatDateTime = (dateTimeString) => {
  if (!dateTimeString) return ''
  const date = new Date(dateTimeString)
  return date.toLocaleString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Validar DNI peruano (8 dígitos)
export const isValidDNI = (dni) => {
  return /^\d{8}$/.test(dni)
}

// Validar email
export const isValidEmail = (email) => {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
}

// Obtener color según nivel de riesgo
export const getRiskColor = (nivel) => {
  const colors = {
    'BAJO': 'positive',
    'MEDIO': 'warning',
    'ALTO': 'negative'
  }
  return colors[nivel] || 'grey'
}

// Obtener color según estado de cita
export const getStatusColor = (estado) => {
  const colors = {
    'PENDIENTE': 'warning',
    'REALIZADA': 'positive',
    'CANCELADA': 'negative'
  }
  return colors[estado] || 'grey'
}

// Calcular edad desde fecha de nacimiento
export const calculateAge = (birthDate) => {
  const today = new Date()
  const birth = new Date(birthDate)
  let age = today.getFullYear() - birth.getFullYear()
  const monthDiff = today.getMonth() - birth.getMonth()

  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
    age--
  }

  return age
}

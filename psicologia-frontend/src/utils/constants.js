// Roles del sistema
export const ROLES = {
  TOE: 'TOE',
  PSICOLOGO: 'PSICOLOGO',
  TUTOR: 'TUTOR',
  DIRECTOR: 'DIRECTOR'
}

// Niveles de riesgo para diagnósticos
export const NIVELES_RIESGO = [
  { label: 'BAJO', value: 'BAJO', color: 'positive' },
  { label: 'MEDIO', value: 'MEDIO', color: 'warning' },
  { label: 'ALTO', value: 'ALTO', color: 'negative' }
]

// Estados de citas
export const ESTADOS_CITA = [
  { label: 'PENDIENTE', value: 'PENDIENTE', color: 'warning' },
  { label: 'REALIZADA', value: 'REALIZADA', color: 'positive' },
  { label: 'CANCELADA', value: 'CANCELADA', color: 'negative' }
]

// Grados escolares
export const GRADOS = [
  '1ro', '2do', '3ro', '4to', '5to', '6to'
]

// Secciones
export const SECCIONES = [
  'A', 'B', 'C', 'D'
]

// Parentescos de apoderados
export const PARENTESCOS = [
  'Madre',
  'Padre',
  'Tutor Legal',
  'Abuelo/a',
  'Tío/a',
  'Otro'
]

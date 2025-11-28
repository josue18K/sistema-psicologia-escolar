<template>
  <q-page padding>
    <div class="dashboard-container">
      <!-- Header -->
      <div class="dashboard-header q-mb-lg">
        <div class="text-h4 text-weight-bold">Dashboard</div>
        <div class="text-grey-7">Bienvenido al sistema de gestión psicológica</div>
      </div>

      <!-- Stats Cards -->
      <div class="row q-col-gutter-lg q-mb-xl">
        <!-- Estudiantes -->
        <div class="col-12 col-sm-6 col-md-3">
          <q-card class="stat-card-modern stat-yellow" flat @click="navigateTo('/estudiantes')">
            <q-card-section>
              <div class="stat-icon">
                <q-icon name="school" size="48px" />
              </div>
              <div class="stat-number">{{ stats.totalEstudiantes }}</div>
              <div class="stat-label">Estudiantes</div>
            </q-card-section>
          </q-card>
        </div>

        <!-- Diagnósticos -->
        <div class="col-12 col-sm-6 col-md-3">
          <q-card class="stat-card-modern stat-blue" flat @click="navigateTo('/diagnosticos')">
            <q-card-section>
              <div class="stat-icon">
                <q-icon name="assignment" size="48px" />
              </div>
              <div class="stat-number">{{ stats.totalDiagnosticos }}</div>
              <div class="stat-label">Diagnósticos</div>
            </q-card-section>
          </q-card>
        </div>

        <!-- Citas Pendientes -->
        <div class="col-12 col-sm-6 col-md-3">
          <q-card class="stat-card-modern stat-green" flat @click="navigateTo('/citas')">
            <q-card-section>
              <div class="stat-icon">
                <q-icon name="event" size="48px" />
              </div>
              <div class="stat-number">{{ stats.citasPendientes }}</div>
              <div class="stat-label">Citas Pendientes</div>
            </q-card-section>
          </q-card>
        </div>

        <!-- Casos Alto Riesgo -->
        <div class="col-12 col-sm-6 col-md-3">
          <q-card class="stat-card-modern stat-dark" flat @click="navigateTo('/diagnosticos')">
            <q-card-section>
              <div class="stat-icon">
                <q-icon name="warning" size="48px" />
              </div>
              <div class="stat-number">{{ stats.casosAltoRiesgo }}</div>
              <div class="stat-label">Alto Riesgo</div>
            </q-card-section>
          </q-card>
        </div>
      </div>

      <div class="row q-col-gutter-lg">
        <!-- Columna izquierda -->
        <div class="col-12 col-md-6">
          <!-- Diagnósticos Recientes -->
          <q-card flat bordered class="modern-card q-mb-lg">
            <q-card-section class="card-header">
              <div class="row items-center">
                <div class="col">
                  <div class="text-h6 text-weight-bold">Diagnósticos Recientes</div>
                </div>
                <div class="col-auto">
                  <q-icon name="more_vert" size="sm" class="cursor-pointer" />
                </div>
              </div>
            </q-card-section>

            <q-card-section class="q-pa-none">
              <q-list separator>
                <q-item
                  v-for="(diagnostico, index) in recentDiagnosticos"
                  :key="index"
                  clickable
                  class="diagnostico-item"
                >
                  <q-item-section avatar>
                    <q-avatar
                      :color="getRiesgoColor(diagnostico.nivel_riesgo)"
                      text-color="white"
                      icon="assignment"
                    />
                  </q-item-section>

                  <q-item-section>
                    <q-item-label class="text-weight-bold">
                      {{ diagnostico.tipo }}
                    </q-item-label>
                    <q-item-label caption>
                      {{ diagnostico.estudiante }} - {{ diagnostico.fecha }}
                    </q-item-label>
                  </q-item-section>

                  <q-item-section side>
                    <q-btn
                      flat
                      round
                      dense
                      icon="visibility"
                      color="positive"
                      size="sm"
                    >
                      <q-tooltip>Ver</q-tooltip>
                    </q-btn>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-card-section>
          </q-card>

          <!-- Estudiantes Asignados -->
          <q-card flat bordered class="modern-card">
            <q-card-section class="card-header">
              <div class="row items-center">
                <div class="col">
                  <div class="text-h6 text-weight-bold">Estudiantes Asignados</div>
                </div>
                <div class="col-auto">
                  <q-icon name="more_vert" size="sm" class="cursor-pointer" />
                </div>
              </div>
            </q-card-section>

            <q-card-section class="q-pa-none">
              <q-list separator>
                <q-item
                  v-for="(estudiante, index) in estudiantesAsignados"
                  :key="index"
                  clickable
                  class="estudiante-item"
                >
                  <q-item-section avatar>
                    <q-avatar
                      :color="getAvatarColor(index)"
                      text-color="white"
                    >
                      {{ estudiante.iniciales }}
                    </q-avatar>
                  </q-item-section>

                  <q-item-section>
                    <q-item-label class="text-weight-bold">
                      {{ estudiante.nombre }}
                    </q-item-label>
                    <q-item-label caption>
                      Rol: {{ estudiante.rol }}
                    </q-item-label>
                  </q-item-section>

                  <q-item-section side>
                    <div class="row q-gutter-xs">
                      <q-btn
                        flat
                        round
                        dense
                        icon="visibility"
                        color="positive"
                        size="sm"
                      >
                        <q-tooltip>Ver</q-tooltip>
                      </q-btn>
                      <q-btn
                        flat
                        round
                        dense
                        icon="download"
                        color="primary"
                        size="sm"
                      >
                        <q-tooltip>Descargar</q-tooltip>
                      </q-btn>
                    </div>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-card-section>
          </q-card>
        </div>

        <!-- Columna derecha -->
        <div class="col-12 col-md-6">
          <!-- Próximas Citas -->
          <q-card flat bordered class="modern-card q-mb-lg">
            <q-card-section class="card-header">
              <div class="row items-center">
                <div class="col">
                  <div class="text-h6 text-weight-bold">Próximas Citas</div>
                </div>
                <div class="col-auto">
                  <q-icon name="more_vert" size="sm" class="cursor-pointer" />
                </div>
              </div>
            </q-card-section>

            <q-card-section class="q-pa-none">
              <q-list separator>
                <q-item
                  v-for="(cita, index) in proximasCitas"
                  :key="index"
                  clickable
                  class="cita-item"
                >
                  <q-item-section avatar>
                    <q-avatar
                      :color="getAvatarColor(index)"
                      text-color="white"
                    >
                      {{ cita.iniciales }}
                    </q-avatar>
                  </q-item-section>

                  <q-item-section>
                    <q-item-label class="text-weight-bold">
                      {{ cita.nombre }}
                    </q-item-label>
                    <q-item-label caption>
                      {{ cita.fecha }} - {{ cita.hora }}
                    </q-item-label>
                  </q-item-section>

                  <q-item-section side>
                    <q-btn
                      flat
                      round
                      dense
                      icon="visibility"
                      color="positive"
                      size="sm"
                    >
                      <q-tooltip>Ver</q-tooltip>
                    </q-btn>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-card-section>
          </q-card>

          <!-- Reportes Mensuales -->
          <q-card flat bordered class="modern-card">
            <q-card-section class="card-header">
              <div class="row items-center">
                <div class="col">
                  <div class="text-h6 text-weight-bold">Reportes Abril</div>
                </div>
                <div class="col-auto">
                  <q-icon name="more_vert" size="sm" class="cursor-pointer" />
                </div>
              </div>
            </q-card-section>

            <q-card-section class="q-pa-none">
              <q-list separator>
                <q-item
                  v-for="(reporte, index) in reportesMensuales"
                  :key="index"
                  clickable
                  class="reporte-item"
                >
                  <q-item-section avatar>
                    <q-avatar
                      :color="getAvatarColor(index)"
                      text-color="white"
                    >
                      {{ reporte.iniciales }}
                    </q-avatar>
                  </q-item-section>

                  <q-item-section>
                    <q-item-label class="text-weight-bold">
                      {{ reporte.nombre }}
                    </q-item-label>
                    <q-item-label caption>
                      Casos: {{ reporte.casos }}
                    </q-item-label>
                  </q-item-section>

                  <q-item-section side>
                    <div class="progress-container">
                      <q-linear-progress
                        :value="reporte.progreso / 100"
                        :color="getProgressColor(reporte.progreso)"
                        size="8px"
                        rounded
                        class="q-mb-xs"
                      />
                      <div class="text-caption text-center">
                        {{ reporte.estado }}
                      </div>
                    </div>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-card-section>
          </q-card>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useEstudiantesStore } from 'stores/estudiantes'
import { useDiagnosticosStore } from 'stores/diagnosticos'
import { useCitasStore } from 'stores/citas'

const router = useRouter()
const estudiantesStore = useEstudiantesStore()
const diagnosticosStore = useDiagnosticosStore()
const citasStore = useCitasStore()

const stats = ref({
  totalEstudiantes: 0,
  totalDiagnosticos: 0,
  citasPendientes: 0,
  casosAltoRiesgo: 0
})

const recentDiagnosticos = ref([
  { tipo: 'Ansiedad Generalizada', estudiante: 'Juan Pérez', fecha: '20 mins atrás', nivel_riesgo: 'MEDIO' },
  { tipo: 'Déficit Atencional', estudiante: 'María García', fecha: '10 mins atrás', nivel_riesgo: 'BAJO' },
  { tipo: 'Depresión', estudiante: 'Carlos López', fecha: '5 mins atrás', nivel_riesgo: 'ALTO' }
])

const estudiantesAsignados = ref([
  { nombre: 'Aman', rol: 'Product Manager', iniciales: 'AM' },
  { nombre: 'Gelila', rol: 'Sales Executive', iniciales: 'GE' },
  { nombre: 'Biruk', rol: 'UI/UX Designer', iniciales: 'BI' }
])

const proximasCitas = ref([
  { nombre: 'Feven Tesfaye', fecha: 'Mañana', hora: '10:00 AM', iniciales: 'FT' },
  { nombre: 'Yanet Mekuriya', fecha: 'Hoy', hora: '3:00 PM', iniciales: 'YM' },
  { nombre: 'Aman Beyene', fecha: 'Hoy', hora: '4:30 PM', iniciales: 'AB' }
])

const reportesMensuales = ref([
  { nombre: 'Aman', casos: '30,000 casos', progreso: 100, estado: 'Pagado', iniciales: 'AM' },
  { nombre: 'Gelila', casos: '50,000 casos', progreso: 60, estado: 'Procesando', iniciales: 'GE' },
  { nombre: 'Biruk', casos: '40,000 casos', progreso: 60, estado: 'Procesando', iniciales: 'BI' }
])

const navigateTo = (route) => {
  router.push(route)
}

const getRiesgoColor = (nivel) => {
  switch (nivel) {
    case 'ALTO': return 'negative'
    case 'MEDIO': return 'warning'
    case 'BAJO': return 'positive'
    default: return 'grey'
  }
}

const getAvatarColor = (index) => {
  const colors = ['amber-8', 'blue-8', 'red-8', 'green-8', 'purple-8']
  return colors[index % colors.length]
}

const getProgressColor = (progreso) => {
  if (progreso === 100) return 'positive'
  if (progreso >= 50) return 'warning'
  return 'grey'
}

const loadDashboardData = async () => {
  await estudiantesStore.fetchAll()
  await diagnosticosStore.fetchAll()
  await citasStore.fetchAll()

  stats.value.totalEstudiantes = estudiantesStore.estudiantes.length
  stats.value.totalDiagnosticos = diagnosticosStore.diagnosticos.length
  stats.value.citasPendientes = citasStore.citas.filter(c => c.estado === 'PENDIENTE').length
  stats.value.casosAltoRiesgo = diagnosticosStore.diagnosticos.filter(d => d.nivel_riesgo === 'ALTO').length
}

onMounted(() => {
  loadDashboardData()
})
</script>

<style scoped lang="scss">
.dashboard-container {
  max-width: 1400px;
  margin: 0 auto;
}

.dashboard-header {
  padding: 20px 0;
}

// Stats Cards
.stat-card-modern {
  border-radius: 16px;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;

  &:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
  }

  .q-card__section {
    padding: 28px;
    color: white;
    position: relative;
    z-index: 1;
  }

  .stat-icon {
    position: absolute;
    right: 20px;
    top: 20px;
    opacity: 0.3;
  }

  .stat-number {
    font-size: 48px;
    font-weight: 700;
    margin-bottom: 4px;
  }

  .stat-label {
    font-size: 16px;
    font-weight: 500;
    opacity: 0.95;
  }

  &.stat-yellow {
    background: linear-gradient(135deg, #FDB813 0%, #F59E0B 100%);
  }

  &.stat-blue {
    background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%);
  }

  &.stat-green {
    background: linear-gradient(135deg, #10B981 0%, #059669 100%);
  }

  &.stat-dark {
    background: linear-gradient(135deg, #374151 0%, #1F2937 100%);
  }
}

// Modern Cards
.modern-card {
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;

  &:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  }

  .card-header {
    border-bottom: 1px solid #f0f0f0;
    padding: 20px 24px;
  }
}

// List Items
.diagnostico-item,
.estudiante-item,
.cita-item,
.reporte-item {
  padding: 16px 24px;
  transition: background 0.2s ease;

  &:hover {
    background: rgba(102, 126, 234, 0.05);
  }
}

// Progress Container
.progress-container {
  min-width: 120px;

  .q-linear-progress {
    border-radius: 10px;
  }
}

// Responsive
@media (max-width: 599px) {
  .stat-card-modern {
    .stat-number {
      font-size: 36px;
    }

    .stat-icon {
      display: none;
    }
  }
}
</style>

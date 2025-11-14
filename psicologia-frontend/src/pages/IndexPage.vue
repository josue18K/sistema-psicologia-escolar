<template>
  <q-page padding>
    <div class="q-pa-md">
      <div class="text-h4 q-mb-md">
        Bienvenido, {{ authStore.user?.name }}
      </div>

      <!-- Cards de estadísticas -->
      <div class="row q-col-gutter-md q-mb-md">
        <div class="col-12 col-sm-6 col-md-3">
          <q-card flat bordered>
            <q-card-section class="text-center">
              <q-icon name="school" size="48px" color="primary" />
              <div class="text-h4 q-mt-sm">{{ stats.totalEstudiantes }}</div>
              <div class="text-grey-7">Total Estudiantes</div>
            </q-card-section>
          </q-card>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
          <q-card flat bordered>
            <q-card-section class="text-center">
              <q-icon name="assignment" size="48px" color="secondary" />
              <div class="text-h4 q-mt-sm">{{ stats.totalDiagnosticos }}</div>
              <div class="text-grey-7">Diagnósticos</div>
            </q-card-section>
          </q-card>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
          <q-card flat bordered>
            <q-card-section class="text-center">
              <q-icon name="event" size="48px" color="warning" />
              <div class="text-h4 q-mt-sm">{{ stats.citasPendientes }}</div>
              <div class="text-grey-7">Citas Pendientes</div>
            </q-card-section>
          </q-card>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
          <q-card flat bordered>
            <q-card-section class="text-center">
              <q-icon name="warning" size="48px" color="negative" />
              <div class="text-h4 q-mt-sm">{{ stats.casosAltoRiesgo }}</div>
              <div class="text-grey-7">Alto Riesgo</div>
            </q-card-section>
          </q-card>
        </div>
      </div>

      <!-- Gráficos -->
      <div class="row q-col-gutter-md">
        <!-- Gráfico de diagnósticos por nivel de riesgo -->
        <div class="col-12 col-md-6">
          <q-card flat bordered>
            <q-card-section>
              <div class="text-h6 q-mb-md">Diagnósticos por Nivel de Riesgo</div>
              <Doughnut
                v-if="chartDataRiesgo"
                :data="chartDataRiesgo"
                :options="chartOptions"
              />
            </q-card-section>
          </q-card>
        </div>

        <!-- Gráfico de citas por estado -->
        <div class="col-12 col-md-6">
          <q-card flat bordered>
            <q-card-section>
              <div class="text-h6 q-mb-md">Estado de Citas</div>
              <Bar
                v-if="chartDataCitas"
                :data="chartDataCitas"
                :options="chartOptions"
              />
            </q-card-section>
          </q-card>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from 'stores/auth'
import { useDiagnosticosStore } from 'stores/diagnosticos'
import { useCitasStore } from 'stores/citas'
import { useEstudiantesStore } from 'stores/estudiantes'
import {
  Chart as ChartJS,
  ArcElement,
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js'
import { Doughnut, Bar } from 'vue-chartjs'

ChartJS.register(
  ArcElement,
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
)

const authStore = useAuthStore()
const diagnosticosStore = useDiagnosticosStore()
const citasStore = useCitasStore()
const estudiantesStore = useEstudiantesStore()

const stats = ref({
  totalEstudiantes: 0,
  totalDiagnosticos: 0,
  citasPendientes: 0,
  casosAltoRiesgo: 0
})

const chartDataRiesgo = ref(null)
const chartDataCitas = ref(null)

const chartOptions = {
  responsive: true,
  maintainAspectRatio: true,
  plugins: {
    legend: {
      position: 'bottom'
    }
  }
}

const loadDashboardData = async () => {
  await estudiantesStore.fetchAll()
  await diagnosticosStore.fetchAll()
  await citasStore.fetchAll()

  // Estadísticas
  stats.value.totalEstudiantes = estudiantesStore.estudiantes.length
  stats.value.totalDiagnosticos = diagnosticosStore.diagnosticos.length
  stats.value.citasPendientes = citasStore.citas.filter(c => c.estado === 'PENDIENTE').length
  stats.value.casosAltoRiesgo = diagnosticosStore.diagnosticos.filter(d => d.nivel_riesgo === 'ALTO').length

  // Gráfico de riesgo
  const riesgoCounts = {
    BAJO: diagnosticosStore.diagnosticos.filter(d => d.nivel_riesgo === 'BAJO').length,
    MEDIO: diagnosticosStore.diagnosticos.filter(d => d.nivel_riesgo === 'MEDIO').length,
    ALTO: diagnosticosStore.diagnosticos.filter(d => d.nivel_riesgo === 'ALTO').length
  }

  chartDataRiesgo.value = {
    labels: ['Bajo', 'Medio', 'Alto'],
    datasets: [{
      data: [riesgoCounts.BAJO, riesgoCounts.MEDIO, riesgoCounts.ALTO],
      backgroundColor: ['#4CAF50', '#FF9800', '#F44336']
    }]
  }

  // Gráfico de citas
  const citasCounts = {
    PENDIENTE: citasStore.citas.filter(c => c.estado === 'PENDIENTE').length,
    REALIZADA: citasStore.citas.filter(c => c.estado === 'REALIZADA').length,
    CANCELADA: citasStore.citas.filter(c => c.estado === 'CANCELADA').length
  }

  chartDataCitas.value = {
    labels: ['Pendiente', 'Realizada', 'Cancelada'],
    datasets: [{
      label: 'Cantidad',
      data: [citasCounts.PENDIENTE, citasCounts.REALIZADA, citasCounts.CANCELADA],
      backgroundColor: ['#FF9800', '#4CAF50', '#F44336']
    }]
  }
}

onMounted(() => {
  loadDashboardData()
})
</script>

<template>
  <q-page padding>
    <div class="q-pa-md fade-in">
      <div class="text-h4 q-mb-md">Estadísticas Globales</div>

      <!-- KPIs principales -->
      <div class="row q-col-gutter-md q-mb-xl">
        <div class="col-12 col-sm-6 col-md-3">
          <q-card class="info-card card-shadow stat-card" style="border-left-color: #667eea">
            <q-card-section>
              <div class="row items-center">
                <q-icon name="school" size="48px" color="primary" class="q-mr-md" />
                <div>
                  <div class="text-h4 text-weight-bold">{{ stats.totalEstudiantes }}</div>
                  <div class="text-grey-7">Total Estudiantes</div>
                </div>
              </div>
            </q-card-section>
          </q-card>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
          <q-card class="info-card card-shadow stat-card" style="border-left-color: #4caf50">
            <q-card-section>
              <div class="row items-center">
                <q-icon name="assignment_turned_in" size="48px" color="positive" class="q-mr-md" />
                <div>
                  <div class="text-h4 text-weight-bold">{{ stats.totalDiagnosticos }}</div>
                  <div class="text-grey-7">Diagnósticos</div>
                </div>
              </div>
            </q-card-section>
          </q-card>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
          <q-card class="info-card card-shadow stat-card" style="border-left-color: #ff9800">
            <q-card-section>
              <div class="row items-center">
                <q-icon name="event" size="48px" color="warning" class="q-mr-md" />
                <div>
                  <div class="text-h4 text-weight-bold">{{ stats.citasPendientes }}</div>
                  <div class="text-grey-7">Citas Pendientes</div>
                </div>
              </div>
            </q-card-section>
          </q-card>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
          <q-card class="info-card card-shadow stat-card" style="border-left-color: #f44336">
            <q-card-section>
              <div class="row items-center">
                <q-icon name="warning" size="48px" color="negative" class="q-mr-md" />
                <div>
                  <div class="text-h4 text-weight-bold">{{ stats.casosAltoRiesgo }}</div>
                  <div class="text-grey-7">Alto Riesgo</div>
                </div>
              </div>
            </q-card-section>
          </q-card>
        </div>
      </div>

      <!-- Gráficos -->
      <div class="row q-col-gutter-md">
        <div class="col-12 col-md-8">
          <q-card class="card-shadow">
            <q-card-section>
              <div class="text-h6 q-mb-md">Tendencia Mensual de Diagnósticos</div>
              <Line v-if="chartDataLine" :data="chartDataLine" :options="chartOptions" />
            </q-card-section>
          </q-card>
        </div>

        <div class="col-12 col-md-4">
          <q-card class="card-shadow">
            <q-card-section>
              <div class="text-h6 q-mb-md">Distribución por Grado</div>
              <Pie v-if="chartDataPie" :data="chartDataPie" :options="chartOptions" />
            </q-card-section>
          </q-card>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useEstudiantesStore } from 'stores/estudiantes'
import { useDiagnosticosStore } from 'stores/diagnosticos'
import { useCitasStore } from 'stores/citas'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  ArcElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js'
import { Line, Pie } from 'vue-chartjs'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  ArcElement,
  Title,
  Tooltip,
  Legend
)

const estudiantesStore = useEstudiantesStore()
const diagnosticosStore = useDiagnosticosStore()
const citasStore = useCitasStore()

const stats = ref({
  totalEstudiantes: 0,
  totalDiagnosticos: 0,
  citasPendientes: 0,
  casosAltoRiesgo: 0
})

const chartDataLine = ref(null)
const chartDataPie = ref(null)

const chartOptions = {
  responsive: true,
  maintainAspectRatio: true,
  plugins: {
    legend: {
      position: 'bottom'
    }
  }
}

const loadData = async () => {
  await estudiantesStore.fetchAll()
  await diagnosticosStore.fetchAll()
  await citasStore.fetchAll()

  stats.value.totalEstudiantes = estudiantesStore.estudiantes.length
  stats.value.totalDiagnosticos = diagnosticosStore.diagnosticos.length
  stats.value.citasPendientes = citasStore.citas.filter(c => c.estado === 'PENDIENTE').length
  stats.value.casosAltoRiesgo = diagnosticosStore.diagnosticos.filter(d => d.nivel_riesgo === 'ALTO').length

  // Gráfico de línea - mock data
  chartDataLine.value = {
    labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
    datasets: [{
      label: 'Diagnósticos',
      data: [12, 19, 15, 25, 22, 30],
      borderColor: '#667eea',
      backgroundColor: 'rgba(102, 126, 234, 0.1)',
      tension: 0.4
    }]
  }

  // Gráfico circular - distribución por grado
  const gradoCounts = {}
  estudiantesStore.estudiantes.forEach(e => {
    gradoCounts[e.grado] = (gradoCounts[e.grado] || 0) + 1
  })

  chartDataPie.value = {
    labels: Object.keys(gradoCounts),
    datasets: [{
      data: Object.values(gradoCounts),
      backgroundColor: [
        '#667eea',
        '#764ba2',
        '#f093fb',
        '#4facfe',
        '#00f2fe',
        '#43e97b'
      ]
    }]
  }
}

onMounted(() => {
  loadData()
})
</script>

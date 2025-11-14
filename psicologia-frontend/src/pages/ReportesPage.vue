<template>
  <q-page class="q-pa-lg">
    <!-- Header Mejorado -->
    <div class="row items-center q-mb-xl">
      <div class="col">
        <div class="text-h3 text-weight-bold text-primary q-mb-xs">
          Reportes y Estadísticas
        </div>
        <div class="text-h6 text-grey-7">
          Reportes mensuales y análisis del sistema
        </div>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          icon="refresh"
          label="Actualizar"
          @click="loadData"
          :loading="loading"
          unelevated
          class="q-px-lg"
          size="lg"
        >
          <q-tooltip>Actualizar datos</q-tooltip>
        </q-btn>
      </div>
    </div>

    <!-- Estadísticas Globales Mejoradas -->
    <div class="row q-col-gutter-xl q-mb-xl">
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="stat-card" flat bordered>
          <q-card-section class="q-pa-xl text-center">
            <q-icon name="school" size="xl" color="primary" class="q-mb-md" />
            <div class="text-h3 text-weight-bold text-primary">{{ estadisticasGlobales?.resumen?.total_estudiantes || 0 }}</div>
            <div class="text-h6 text-grey-7">Total Estudiantes</div>
            <q-linear-progress size="6px" color="primary" class="q-mt-md" :value="1" />
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="stat-card" flat bordered>
          <q-card-section class="q-pa-xl text-center">
            <q-icon name="clinical_notes" size="xl" color="green" class="q-mb-md" />
            <div class="text-h3 text-weight-bold text-positive">{{ estadisticasGlobales?.resumen?.total_diagnosticos || 0 }}</div>
            <div class="text-h6 text-grey-7">Total Diagnósticos</div>
            <q-linear-progress size="6px" color="positive" class="q-mt-md" :value="Math.min((estadisticasGlobales?.resumen?.total_diagnosticos || 0) / 100, 1)" />
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="stat-card" flat bordered>
          <q-card-section class="q-pa-xl text-center">
            <q-icon name="warning" size="xl" color="negative" class="q-mb-md" />
            <div class="text-h3 text-weight-bold text-negative">{{ estadisticasGlobales?.resumen?.casos_criticos || 0 }}</div>
            <div class="text-h6 text-grey-7">Casos Críticos</div>
            <q-linear-progress size="6px" color="negative" class="q-mt-md" :value="Math.min((estadisticasGlobales?.resumen?.casos_criticos || 0) / 20, 1)" />
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="stat-card" flat bordered>
          <q-card-section class="q-pa-xl text-center">
            <q-icon name="event" size="xl" color="orange" class="q-mb-md" />
            <div class="text-h3 text-weight-bold text-orange">{{ estadisticasGlobales?.resumen?.citas_pendientes || 0 }}</div>
            <div class="text-h6 text-grey-7">Citas Pendientes</div>
            <q-linear-progress size="6px" color="orange" class="q-mt-md" :value="Math.min((estadisticasGlobales?.resumen?.citas_pendientes || 0) / 15, 1)" />
          </q-card-section>
        </q-card>
      </div>
    </div>

    <div class="row q-col-gutter-xl">
      <!-- Columna Izquierda: Gráficos Mejorados -->
      <div class="col-12 col-lg-8">
        <!-- Gráfico de Diagnósticos por Mes -->
        <q-card class="q-mb-xl shadow-1" flat bordered>
          <q-card-section class="q-pa-xl">
            <div class="text-h5 text-weight-bold q-mb-lg">Diagnósticos por Mes (Últimos 6 meses)</div>
            <div v-if="loading" class="text-center q-pa-xl">
              <q-spinner-puff size="xl" color="primary" />
              <div class="text-h6 q-mt-md text-grey-6">Cargando estadísticas...</div>
            </div>
            <div v-else-if="estadisticasGlobales?.graficas?.diagnosticos_por_mes?.length" class="chart-container">
              <div class="q-gutter-y-lg">
                <div
                  v-for="mes in estadisticasGlobales.graficas.diagnosticos_por_mes"
                  :key="mes.mes"
                  class="row items-center"
                >
                  <div class="col-3">
                    <div class="text-weight-medium">{{ formatMes(mes.mes) }}</div>
                  </div>
                  <div class="col-7">
                    <q-linear-progress
                      :value="mes.total / maxDiagnosticosMes"
                      color="primary"
                      size="25px"
                      class="progress-bar"
                    >
                      <div class="absolute-full flex flex-center">
                        <q-badge
                          color="primary"
                          text-color="white"
                          :label="`${mes.total} diagnósticos`"
                          style="font-size: 12px;"
                        />
                      </div>
                    </q-linear-progress>
                  </div>
                  <div class="col-2 text-right">
                    <div class="text-h6 text-weight-bold text-primary">{{ Math.round((mes.total / maxDiagnosticosMes) * 100) }}%</div>
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-center q-pa-xl">
              <q-icon name="analytics" size="xl" color="grey-4" />
              <div class="text-h6 q-mt-md text-grey-6">No hay datos de diagnósticos por mes</div>
            </div>
          </q-card-section>
        </q-card>

        <!-- Distribución por Nivel de Riesgo -->
        <q-card class="q-mb-xl shadow-1" flat bordered>
          <q-card-section class="q-pa-xl">
            <div class="text-h5 text-weight-bold q-mb-lg">Distribución por Nivel de Riesgo</div>
            <div v-if="loading" class="text-center q-pa-xl">
              <q-spinner-puff size="xl" color="primary" />
            </div>
            <div v-else-if="estadisticasGlobales?.graficas?.por_nivel_riesgo?.length" class="row items-center justify-around">
              <div
                v-for="item in estadisticasGlobales.graficas.por_nivel_riesgo"
                :key="item.nivel_riesgo"
                class="col-auto text-center q-pa-lg"
              >
                <q-circular-progress
                  show-value
                  font-size="16px"
                  :value="(item.total / totalDiagnosticos) * 100"
                  size="120px"
                  :thickness="0.25"
                  :color="getRiskColor(item.nivel_riesgo)"
                  track-color="grey-3"
                  class="q-mb-md"
                >
                  <div class="text-center">
                    <div class="text-h6 text-weight-bold">{{ item.total }}</div>
                    <div class="text-caption text-grey-7">{{ Math.round((item.total / totalDiagnosticos) * 100) }}%</div>
                  </div>
                </q-circular-progress>
                <div class="text-h6 text-capitalize text-weight-medium" :class="`text-${getRiskColor(item.nivel_riesgo)}`">
                  {{ item.nivel_riesgo.toLowerCase() }}
                </div>
              </div>
            </div>
            <div v-else class="text-center q-pa-xl">
              <q-icon name="pie_chart" size="xl" color="grey-4" />
              <div class="text-grey-6 q-mt-md">No hay datos por nivel de riesgo</div>
            </div>
          </q-card-section>
        </q-card>

        <!-- Distribución por Grado -->
        <q-card class="shadow-1" flat bordered>
          <q-card-section class="q-pa-xl">
            <div class="text-h5 text-weight-bold q-mb-lg">Diagnósticos por Grado</div>
            <div v-if="loading" class="text-center q-pa-xl">
              <q-spinner-puff size="xl" color="primary" />
            </div>
            <div v-else-if="estadisticasGlobales?.graficas?.por_grado?.length" class="q-gutter-y-lg">
              <div
                v-for="item in estadisticasGlobales.graficas.por_grado"
                :key="item.grado"
                class="row items-center"
              >
                <div class="col-3">
                  <div class="text-weight-medium text-h6">{{ item.grado }}</div>
                </div>
                <div class="col-7">
                  <q-linear-progress
                    :value="item.total / maxDiagnosticosGrado"
                    color="orange"
                    size="20px"
                    class="progress-bar"
                  >
                    <div class="absolute-full flex flex-center">
                      <q-badge
                        color="orange"
                        text-color="white"
                        :label="`${item.total} estudiantes`"
                        style="font-size: 11px;"
                      />
                    </div>
                  </q-linear-progress>
                </div>
                <div class="col-2 text-right">
                  <q-badge color="orange" class="q-px-md q-py-sm" style="font-size: 14px;">
                    {{ item.total }}
                  </q-badge>
                </div>
              </div>
            </div>
            <div v-else class="text-center q-pa-xl">
              <q-icon name="school" size="xl" color="grey-4" />
              <div class="text-grey-6 q-mt-md">No hay datos por grado</div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <!-- Columna Derecha: Reportes y Acciones Mejoradas -->
      <div class="col-12 col-lg-4">
        <!-- Generar Reporte Automático -->
        <q-card class="q-mb-xl shadow-1" flat bordered v-if="isTOE || isPsicologo">
          <q-card-section class="q-pa-xl">
            <div class="text-h5 text-weight-bold q-mb-lg">Generar Reporte Mensual</div>
            <q-select
              v-model="psicologoReporte"
              :options="psicologosOptions"
              label="Seleccionar Psicólogo"
              outlined
              color="primary"
              class="q-mb-md"
              map-options
              emit-value
            >
              <template v-slot:prepend>
                <q-icon name="psychology" color="primary" />
              </template>
            </q-select>
            <q-btn
              color="primary"
              icon="description"
              label="Generar Reporte Automático"
              class="full-width"
              @click="generarReporteMensual"
              :loading="loadingGenerarReporte"
              :disable="!psicologoReporte"
              unelevated
              size="lg"
            >
              <q-tooltip>Generar reporte del mes actual</q-tooltip>
            </q-btn>
            <div class="text-caption text-grey-6 q-mt-sm">
              Se generará un reporte con las estadísticas del mes actual
            </div>
          </q-card-section>
        </q-card>

        <!-- Lista de Reportes Mejorada -->
        <q-card class="shadow-1" flat bordered>
          <q-card-section class="q-pa-xl">
            <div class="text-h5 text-weight-bold q-mb-lg">Reportes Mensuales</div>

            <div v-if="loadingReportes" class="text-center q-pa-lg">
              <q-spinner-puff size="xl" color="primary" />
              <div class="text-grey-6 q-mt-md">Cargando reportes...</div>
            </div>

            <div v-else-if="reportes.length === 0" class="text-center q-pa-xl">
              <q-icon name="description" size="xl" color="grey-4" />
              <div class="text-h6 q-mt-md text-grey-6">No hay reportes</div>
              <div class="text-grey-6">Los reportes generados aparecerán aquí</div>
            </div>

            <div v-else class="q-gutter-y-md">
              <q-card
                v-for="reporte in reportes.slice(0, 5)"
                :key="reporte.id"
                flat
                bordered
                class="reporte-card cursor-pointer"
                @click="verReporte(reporte)"
              >
                <q-card-section class="q-pa-lg">
                  <div class="row items-center q-mb-sm">
                    <div class="col">
                      <div class="text-h6 text-weight-medium text-primary">
                        {{ reporte.mes }}
                      </div>
                    </div>
                    <div class="col-auto">
                      <q-badge color="primary" class="q-px-md q-py-sm">
                        {{ reporte.total_estudiantes }} est.
                      </q-badge>
                    </div>
                  </div>

                  <div class="row items-center">
                    <div class="col">
                      <div class="text-caption text-grey-7">
                        <q-icon name="person" size="16px" class="q-mr-xs" />
                        {{ reporte.psicologo?.name }}
                      </div>
                    </div>
                    <div class="col-auto">
                      <div class="text-caption text-grey-6">
                        {{ formatDate(reporte.created_at) }}
                      </div>
                    </div>
                  </div>

                  <div class="row items-center q-mt-sm">
                    <div class="col">
                      <div class="text-caption">
                        <q-icon name="warning" color="negative" size="16px" class="q-mr-xs" />
                        {{ reporte.casos_criticos }} casos críticos
                      </div>
                    </div>
                    <div class="col-auto">
                      <q-btn
                        flat
                        round
                        icon="visibility"
                        color="info"
                        size="sm"
                        @click.stop="verReporte(reporte)"
                      >
                        <q-tooltip>Ver reporte completo</q-tooltip>
                      </q-btn>
                    </div>
                  </div>
                </q-card-section>
              </q-card>

              <div class="text-center q-pt-md" v-if="reportes.length > 5">
                <q-btn
                  flat
                  color="primary"
                  label="Ver todos los reportes"
                  @click="verTodosReportes"
                  icon-right="arrow_forward"
                />
              </div>
            </div>
          </q-card-section>
        </q-card>

        <!-- Acciones Rápidas -->
        <q-card class="q-mt-xl shadow-1" flat bordered>
          <q-card-section class="q-pa-xl">
            <div class="text-h5 text-weight-bold q-mb-lg">Acciones Rápidas</div>
            <div class="q-gutter-y-md">
              <q-btn
                color="primary"
                icon="download"
                label="Exportar Estadísticas"
                class="full-width"
                @click="exportarEstadisticas"
                unelevated
              />
              <q-btn
                color="green"
                icon="summarize"
                label="Reporte General"
                class="full-width"
                @click="generarReporteGeneral"
                unelevated
              />
              <q-btn
                color="orange"
                icon="calendar_view_month"
                label="Vista Mensual"
                class="full-width"
                @click="verVistaMensual"
                unelevated
              />
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useQuasar } from 'quasar'
import { useAuthStore } from 'src/stores/auth-store'
import { reporteService, userService } from 'src/services/api'
import { formatDate, getRiskColor } from 'src/utils/helpers'

const $q = useQuasar()
const authStore = useAuthStore()

const loading = ref(false)
const loadingReportes = ref(false)
const loadingGenerarReporte = ref(false)
const reportes = ref([])
const psicologos = ref([])
const estadisticasGlobales = ref({})
const psicologoReporte = ref(null)

// Computed
const isTOE = computed(() => authStore.isTOE)
const isPsicologo = computed(() => authStore.isPsicologo)

const psicologosOptions = computed(() =>
  psicologos.value.map(psic => ({
    label: psic.name,
    value: psic.id
  }))
)

const totalDiagnosticos = computed(() =>
  estadisticasGlobales.value?.resumen?.total_diagnosticos || 0
)

const maxDiagnosticosMes = computed(() => {
  const diagnosticosPorMes = estadisticasGlobales.value?.graficas?.diagnosticos_por_mes || []
  return Math.max(...diagnosticosPorMes.map(mes => mes.total), 1)
})

const maxDiagnosticosGrado = computed(() => {
  const porGrado = estadisticasGlobales.value?.graficas?.por_grado || []
  return Math.max(...porGrado.map(grado => grado.total), 1)
})

// Methods
async function loadData() {
  loading.value = true
  try {
    await Promise.all([
      loadEstadisticasGlobales(),
      loadReportes(),
      loadPsicologos()
    ])
  } catch (error) {
    console.error('Error cargando datos:', error)
    $q.notify({
      type: 'negative',
      message: 'Error cargando datos del sistema',
      position: 'top'
    })
  } finally {
    loading.value = false
  }
}

async function loadEstadisticasGlobales() {
  try {
    const response = await reporteService.getEstadisticasGlobales()
    estadisticasGlobales.value = response.data
  } catch (error) {
    console.error('Error cargando estadísticas globales:', error)
  }
}

async function loadReportes() {
  loadingReportes.value = true
  try {
    const response = await reporteService.getReportes()
    reportes.value = response.data.reportes
  } catch (error) {
    console.error('Error cargando reportes:', error)
  } finally {
    loadingReportes.value = false
  }
}

async function loadPsicologos() {
  try {
    const response = await userService.getPsicologos()
    psicologos.value = response.data.psicologos
  } catch (error) {
    console.error('Error cargando psicólogos:', error)
  }
}

function formatMes(mesString) {
  if (!mesString) return ''
  const [year, month] = mesString.split('-')
  const date = new Date(year, month - 1)
  return date.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' })
}

async function generarReporteMensual() {
  if (!psicologoReporte.value) return

  loadingGenerarReporte.value = true
  try {
    await reporteService.generarMensual(psicologoReporte.value)

    $q.notify({
      type: 'positive',
      message: 'Reporte mensual generado exitosamente',
      position: 'top'
    })

    await loadReportes()
    psicologoReporte.value = null

  } catch (error) {
    console.error('Error generando reporte:', error)
    $q.notify({
      type: 'negative',
      message: error.response?.data?.message || 'Error generando reporte',
      position: 'top'
    })
  } finally {
    loadingGenerarReporte.value = false
  }
}

function verReporte(reporte) {
  $q.notify({
    message: `Reporte: ${reporte.mes}`,
    caption: `Generado por: ${reporte.psicologo?.name}`,
    color: 'info',
    icon: 'description',
    position: 'top'
  })
}

function verTodosReportes() {
  $q.notify({
    message: 'Vista completa de reportes',
    caption: 'Funcionalidad en desarrollo',
    color: 'info',
    icon: 'info',
    position: 'top'
  })
}

function exportarEstadisticas() {
  $q.notify({
    message: 'Exportando estadísticas',
    caption: 'La exportación comenzará pronto',
    color: 'positive',
    icon: 'download',
    position: 'top'
  })
}

function generarReporteGeneral() {
  $q.notify({
    message: 'Generando reporte general',
    caption: 'Esta función estará disponible pronto',
    color: 'info',
    icon: 'summarize',
    position: 'top'
  })
}

function verVistaMensual() {
  $q.notify({
    message: 'Vista mensual',
    caption: 'Navegando a la vista de calendario',
    color: 'orange',
    icon: 'calendar_view_month',
    position: 'top'
  })
}

// Lifecycle
onMounted(() => {
  loadData()
})
</script>

<style scoped>
.stat-card {
  transition: all 0.3s ease;
  border-radius: 16px;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.chart-container {
  min-height: 300px;
}

.progress-bar {
  border-radius: 10px;
}

.reporte-card {
  transition: all 0.3s ease;
  border-radius: 12px;
  border-left: 4px solid #1976d2;
}

.reporte-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}
</style>

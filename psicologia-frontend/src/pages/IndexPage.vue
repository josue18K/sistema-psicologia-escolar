<template>
  <q-page class="q-pa-lg">
    <!-- Header del Dashboard -->
    <div class="row items-center q-mb-xl">
      <div class="col">
        <div class="text-h3 text-weight-bold text-primary q-mb-xs">
          Dashboard
        </div>
        <div class="text-h6 text-grey-7">
          Bienvenido, <span class="text-weight-medium">{{ user?.name }}</span>
        </div>
        <div class="text-caption text-grey-6 q-mt-sm">
          Resumen general del sistema - {{ new Date().toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}
        </div>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          icon="refresh"
          label="Actualizar"
          @click="loadDashboardData"
          :loading="loading"
          unelevated
          class="q-px-lg"
        />
      </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row q-col-gutter-xl q-mb-xl">
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="stat-card cursor-pointer" flat bordered @click="$router.push('/estudiantes')">
          <q-card-section class="q-pa-xl text-center">
            <q-icon name="school" size="xl" color="primary" class="q-mb-md stat-icon" />
            <div class="text-h3 text-weight-bold text-primary q-mb-sm">{{ stats.totalEstudiantes || 0 }}</div>
            <div class="text-h6 text-grey-7">Total Estudiantes</div>
            <q-linear-progress
              size="6px"
              color="primary"
              class="q-mt-md"
              :value="1"
            />
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="stat-card cursor-pointer" flat bordered @click="$router.push('/citas')">
          <q-card-section class="q-pa-xl text-center">
            <q-icon name="event" size="xl" color="orange" class="q-mb-md stat-icon" />
            <div class="text-h3 text-weight-bold text-orange q-mb-sm">{{ stats.citasPendientes || 0 }}</div>
            <div class="text-h6 text-grey-7">Citas Pendientes</div>
            <q-linear-progress
              size="6px"
              color="orange"
              class="q-mt-md"
              :value="Math.min((stats.citasPendientes || 0) / 10, 1)"
            />
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="stat-card cursor-pointer" flat bordered @click="$router.push('/diagnosticos')">
          <q-card-section class="q-pa-xl text-center">
            <q-icon name="warning" size="xl" color="negative" class="q-mb-md stat-icon" />
            <div class="text-h3 text-weight-bold text-negative q-mb-sm">{{ stats.casosCriticos || 0 }}</div>
            <div class="text-h6 text-grey-7">Casos Críticos</div>
            <q-linear-progress
              size="6px"
              color="negative"
              class="q-mt-md"
              :value="Math.min((stats.casosCriticos || 0) / 5, 1)"
            />
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="stat-card cursor-pointer" flat bordered @click="$router.push('/diagnosticos')">
          <q-card-section class="q-pa-xl text-center">
            <q-icon name="clinical_notes" size="xl" color="green" class="q-mb-md stat-icon" />
            <div class="text-h3 text-weight-bold text-positive q-mb-sm">{{ stats.totalDiagnosticos || 0 }}</div>
            <div class="text-h6 text-grey-7">Total Diagnósticos</div>
            <q-linear-progress
              size="6px"
              color="positive"
              class="q-mt-md"
              :value="Math.min((stats.totalDiagnosticos || 0) / 50, 1)"
            />
          </q-card-section>
        </q-card>
      </div>
    </div>

    <div class="row q-col-gutter-xl">
      <!-- Columna Izquierda -->
      <div class="col-12 col-lg-8">
        <!-- Gráfico de Diagnósticos -->
        <q-card class="q-mb-xl shadow-1" flat bordered>
          <q-card-section class="q-pa-xl">
            <div class="text-h5 text-weight-bold q-mb-md">Diagnósticos por Nivel de Riesgo</div>
            <div v-if="loading" class="text-center q-pa-xl">
              <q-spinner-puff size="xl" color="primary" />
              <div class="q-mt-md text-grey-6">Cargando estadísticas...</div>
            </div>
            <div v-else-if="estadisticas.por_nivel_riesgo?.length" class="row items-center justify-around">
              <div
                v-for="item in estadisticas.por_nivel_riesgo"
                :key="item.nivel_riesgo"
                class="col-auto text-center q-pa-lg"
              >
                <q-circular-progress
                  show-value
                  font-size="16px"
                  :value="(item.total / estadisticas.total) * 100"
                  size="120px"
                  :thickness="0.25"
                  :color="getRiskColor(item.nivel_riesgo)"
                  track-color="grey-3"
                  class="q-mb-md"
                >
                  <div class="text-center">
                    <div class="text-h6 text-weight-bold">{{ item.total }}</div>
                    <div class="text-caption text-grey-7">{{ Math.round((item.total / estadisticas.total) * 100) }}%</div>
                  </div>
                </q-circular-progress>
                <div class="text-h6 text-capitalize text-weight-medium" :class="`text-${getRiskColor(item.nivel_riesgo)}`">
                  {{ item.nivel_riesgo.toLowerCase() }}
                </div>
              </div>
            </div>
            <div v-else class="text-center q-pa-xl">
              <q-icon name="analytics" size="xl" color="grey-4" />
              <div class="text-h6 q-mt-md text-grey-6">No hay datos de diagnósticos</div>
            </div>
          </q-card-section>
        </q-card>

        <!-- Citas Recientes -->
        <q-card class="shadow-1" flat bordered>
          <q-card-section class="q-pa-xl">
            <div class="row items-center q-mb-lg">
              <div class="col">
                <div class="text-h5 text-weight-bold">Citas Pendientes</div>
                <div class="text-caption text-grey-6">Próximas citas programadas</div>
              </div>
              <div class="col-auto">
                <q-btn
                  flat
                  color="primary"
                  label="Ver Todas"
                  to="/citas"
                  icon-right="arrow_forward"
                  unelevated
                />
              </div>
            </div>

            <div v-if="loadingCitas" class="text-center q-pa-xl">
              <q-spinner-puff size="xl" color="primary" />
              <div class="q-mt-md text-grey-6">Cargando citas...</div>
            </div>
            <div v-else-if="citasPendientes.length" class="q-gutter-y-md">
              <q-card
                v-for="cita in citasPendientes.slice(0, 5)"
                :key="cita.id"
                flat
                bordered
                class="cita-card cursor-pointer"
                @click="verCita(cita)"
              >
                <q-card-section class="q-pa-lg">
                  <div class="row items-center">
                    <div class="col-auto q-mr-lg">
                      <q-avatar size="60px" color="primary" text-color="white" class="shadow-2">
                        {{ cita.estudiante?.nombres?.charAt(0) || 'E' }}
                      </q-avatar>
                    </div>
                    <div class="col">
                      <div class="text-h6 text-weight-medium q-mb-xs">
                        {{ cita.estudiante?.nombres }} {{ cita.estudiante?.apellidos }}
                      </div>
                      <div class="text-caption text-grey-7 q-mb-xs">
                        {{ cita.motivo }}
                      </div>
                      <div class="text-caption text-orange text-weight-medium">
                        <q-icon name="schedule" size="16px" class="q-mr-xs" />
                        {{ formatDateTime(cita.fecha_cita) }}
                      </div>
                    </div>
                    <div class="col-auto">
                      <q-badge
                        :color="getStatusColor(cita.estado)"
                        class="text-capitalize q-px-md q-py-sm"
                        style="font-size: 12px;"
                      >
                        {{ cita.estado.toLowerCase() }}
                      </q-badge>
                    </div>
                  </div>
                </q-card-section>
              </q-card>
            </div>
            <div v-else class="text-center q-pa-xl">
              <q-icon name="event_available" size="xl" color="grey-4" />
              <div class="text-h6 q-mt-md text-grey-6">No hay citas pendientes</div>
              <q-btn
                v-if="isTOE || isPsicologo"
                color="primary"
                label="Agendar Primera Cita"
                to="/citas"
                unelevated
                class="q-mt-md"
              />
            </div>
          </q-card-section>
        </q-card>
      </div>

      <!-- Columna Derecha -->
      <div class="col-12 col-lg-4">
        <!-- Acciones Rápidas -->
        <q-card class="q-mb-xl shadow-1" flat bordered>
          <q-card-section class="q-pa-xl">
            <div class="text-h5 text-weight-bold q-mb-lg">Acciones Rápidas</div>
            <div class="q-gutter-y-md">
              <q-btn
                v-if="isTOE || isPsicologo"
                color="primary"
                icon="add"
                label="Nuevo Estudiante"
                class="full-width action-btn"
                to="/estudiantes/nuevo"
                unelevated
                size="lg"
              />
              <q-btn
                v-if="isTOE || isPsicologo"
                color="green"
                icon="event"
                label="Agendar Cita"
                class="full-width action-btn"
                to="/citas"
                unelevated
                size="lg"
              />
              <q-btn
                v-if="isTOE || isPsicologo"
                color="orange"
                icon="clinical_notes"
                label="Nuevo Diagnóstico"
                class="full-width action-btn"
                to="/diagnosticos"
                unelevated
                size="lg"
              />
              <q-btn
                v-if="isTOE"
                color="purple"
                icon="people"
                label="Gestionar Usuarios"
                class="full-width action-btn"
                to="/usuarios"
                unelevated
                size="lg"
              />
              <q-btn
                color="blue-grey"
                icon="assessment"
                label="Ver Reportes"
                class="full-width action-btn"
                to="/reportes"
                unelevated
                size="lg"
              />
            </div>
          </q-card-section>
        </q-card>

        <!-- Estadísticas por Grado -->
        <q-card class="shadow-1" flat bordered>
          <q-card-section class="q-pa-xl">
            <div class="text-h5 text-weight-bold q-mb-lg">Estudiantes por Grado</div>
            <div v-if="loading" class="text-center q-pa-lg">
              <q-spinner-puff size="xl" color="primary" />
            </div>
            <div v-else-if="estadisticas.por_grado?.length" class="q-gutter-y-lg">
              <div
                v-for="item in estadisticas.por_grado"
                :key="item.grado"
                class="row items-center"
              >
                <div class="col">
                  <div class="text-weight-medium text-h6">{{ item.grado }}</div>
                </div>
                <div class="col-auto">
                  <q-badge color="primary" class="q-px-md q-py-sm" style="font-size: 14px;">
                    {{ item.total }} estudiantes
                  </q-badge>
                </div>
              </div>
            </div>
            <div v-else class="text-center q-pa-lg">
              <q-icon name="school" size="xl" color="grey-4" />
              <div class="text-grey-6 q-mt-md">No hay datos por grado</div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useAuthStore } from 'src/stores/auth-store'
import { reporteService, citaService } from 'src/services/api'
import { formatDateTime, getRiskColor, getStatusColor } from 'src/utils/helpers'

const $q = useQuasar()
const router = useRouter()
const authStore = useAuthStore()

const loading = ref(false)
const loadingCitas = ref(false)
const stats = ref({})
const estadisticas = ref({})
const citasPendientes = ref([])

// Computed
const user = computed(() => authStore.getUser)
const isTOE = computed(() => authStore.isTOE)
const isPsicologo = computed(() => authStore.isPsicologo)

// Methods
async function loadDashboardData() {
  loading.value = true
  try {
    const [estadisticasResponse, citasResponse] = await Promise.all([
      reporteService.getEstadisticasGlobales(),
      citaService.getPendientes()
    ])

    stats.value = estadisticasResponse.data.resumen
    estadisticas.value = estadisticasResponse.data.graficas
    citasPendientes.value = citasResponse.data.citas

  } catch (error) {
    console.error('Error cargando datos del dashboard:', error)
    $q.notify({
      type: 'negative',
      message: 'Error cargando datos del dashboard',
      position: 'top'
    })
  } finally {
    loading.value = false
  }
}

async function loadCitasPendientes() {
  loadingCitas.value = true
  try {
    const response = await citaService.getPendientes()
    citasPendientes.value = response.data.citas
  } catch (error) {
    console.error('Error cargando citas pendientes:', error)
  } finally {
    loadingCitas.value = false
  }
}

function verCita() {
  router.push('/citas')
}

// Lifecycle
onMounted(() => {
  loadDashboardData()
  loadCitasPendientes()
})
</script>

<style scoped>
.stat-card {
  transition: all 0.3s ease;
  border-radius: 16px;
  overflow: hidden;
}

.stat-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.1);
}

.stat-icon {
  transition: transform 0.3s ease;
}

.stat-card:hover .stat-icon {
  transform: scale(1.1);
}

.cita-card {
  transition: all 0.3s ease;
  border-radius: 12px;
}

.cita-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.action-btn {
  border-radius: 10px;
  transition: all 0.3s ease;
}

.action-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
</style>

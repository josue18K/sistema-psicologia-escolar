<template>
  <q-page class="q-pa-lg">
    <!-- Header Mejorado -->
    <div class="row items-center q-mb-xl">
      <div class="col">
        <div class="text-h3 text-weight-bold text-primary q-mb-xs">
          Gestión de Diagnósticos
        </div>
        <div class="text-h6 text-grey-7">
          Administra los diagnósticos psicológicos del sistema
        </div>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          icon="add"
          label="Nuevo Diagnóstico"
          @click="nuevoDiagnostico"
          v-if="isTOE || isPsicologo"
          unelevated
          class="q-px-lg"
          size="lg"
        >
          <q-tooltip>Registrar nuevo diagnóstico</q-tooltip>
        </q-btn>
      </div>
    </div>

    <!-- Tarjetas de Resumen -->
    <div class="row q-col-gutter-xl q-mb-xl">
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="summary-card" flat bordered @click="filtrarPorNivel('')">
          <q-card-section class="q-pa-xl text-center">
            <q-icon name="clinical_notes" size="xl" color="primary" class="q-mb-md" />
            <div class="text-h4 text-weight-bold text-primary">{{ estadisticas.total || 0 }}</div>
            <div class="text-h6 text-grey-7">Total Diagnósticos</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="summary-card" flat bordered @click="filtrarPorNivel('ALTO')">
          <q-card-section class="q-pa-xl text-center">
            <q-icon name="warning" size="xl" color="negative" class="q-mb-md" />
            <div class="text-h4 text-weight-bold text-negative">{{ getCountByNivel('ALTO') }}</div>
            <div class="text-h6 text-grey-7">Casos Críticos</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="summary-card" flat bordered @click="filtrarPorNivel('MEDIO')">
          <q-card-section class="q-pa-xl text-center">
            <q-icon name="info" size="xl" color="warning" class="q-mb-md" />
            <div class="text-h4 text-weight-bold text-warning">{{ getCountByNivel('MEDIO') }}</div>
            <div class="text-h6 text-grey-7">Casos Medios</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="summary-card" flat bordered @click="filtrarPorNivel('BAJO')">
          <q-card-section class="q-pa-xl text-center">
            <q-icon name="check_circle" size="xl" color="positive" class="q-mb-md" />
            <div class="text-h4 text-weight-bold text-positive">{{ getCountByNivel('BAJO') }}</div>
            <div class="text-h6 text-grey-7">Casos Leves</div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Panel de Filtros Mejorado -->
    <q-card class="q-mb-xl shadow-1" flat bordered>
      <q-card-section class="q-pa-xl">
        <div class="text-h5 text-weight-bold q-mb-lg">Filtros de Búsqueda</div>
        <div class="row q-col-gutter-lg items-end">
          <div class="col-12 col-sm-4">
            <q-input
              v-model="filters.search"
              label="Buscar por estudiante o DNI"
              clearable
              @update:model-value="onFilterChange"
              outlined
              color="primary"
            >
              <template v-slot:prepend>
                <q-icon name="search" color="primary" />
              </template>
            </q-input>
          </div>
          <div class="col-12 col-sm-3">
            <q-select
              v-model="filters.nivel_riesgo"
              :options="NIVELES_RIESGO_OPTIONS"
              label="Nivel de Riesgo"
              clearable
              @update:model-value="onFilterChange"
              outlined
              color="primary"
            />
          </div>
          <div class="col-12 col-sm-3">
            <q-input
              v-model="filters.fecha"
              label="Fecha del diagnóstico"
              type="date"
              clearable
              @update:model-value="onFilterChange"
              outlined
              color="primary"
            />
          </div>
          <div class="col-12 col-sm-2">
            <q-btn
              color="primary"
              icon="filter_alt"
              label="Filtrar"
              @click="loadDiagnosticos"
              :loading="loading"
              unelevated
              class="full-width"
            />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Tabla Mejorada -->
    <q-card class="shadow-1" flat bordered>
      <q-card-section class="q-pa-xl">
        <div class="row items-center q-mb-lg">
          <div class="col">
            <div class="text-h5 text-weight-bold">
              Lista de Diagnósticos
              <q-badge v-if="diagnosticos.length" color="primary" class="q-ml-md" style="font-size: 16px;">
                {{ diagnosticos.length }}
              </q-badge>
            </div>
            <div class="text-caption text-grey-6">
              Historial completo de evaluaciones psicológicas
            </div>
          </div>
          <div class="col-auto">
            <q-btn
              flat
              round
              icon="refresh"
              @click="loadDiagnosticos"
              :loading="loading"
              color="primary"
              size="lg"
            >
              <q-tooltip>Actualizar lista</q-tooltip>
            </q-btn>
          </div>
        </div>

        <!-- Estado de Carga -->
        <div v-if="loading" class="text-center q-pa-xl">
          <q-spinner-puff size="xl" color="primary" />
          <div class="text-h6 q-mt-md text-grey-6">Cargando diagnósticos...</div>
        </div>

        <!-- Estado Vacío -->
        <div v-else-if="diagnosticos.length === 0" class="text-center q-pa-xl">
          <q-icon name="clinical_notes" size="xl" color="grey-4" />
          <div class="text-h5 q-mt-md text-grey-6">No se encontraron diagnósticos</div>
          <div class="text-grey-6 q-mb-lg">No hay diagnósticos que coincidan con los filtros aplicados</div>
          <q-btn
            v-if="isTOE || isPsicologo"
            color="primary"
            icon="add"
            label="Registrar Primer Diagnóstico"
            @click="nuevoDiagnostico"
            unelevated
            size="lg"
          />
        </div>

        <!-- Lista de Diagnósticos -->
        <div v-else class="q-gutter-y-md">
          <q-card
            v-for="diagnostico in diagnosticos"
            :key="diagnostico.id"
            flat
            bordered
            class="diagnostico-card cursor-pointer"
            @click="verDiagnostico(diagnostico)"
          >
            <q-card-section class="q-pa-lg">
              <div class="row items-center">
                <div class="col-auto q-mr-lg">
                  <q-avatar
                    size="60px"
                    :color="getRiskColor(diagnostico.nivel_riesgo)"
                    text-color="white"
                    class="shadow-2"
                  >
                    <q-icon name="psychology" size="28px" />
                  </q-avatar>
                </div>

                <div class="col">
                  <div class="row items-center q-mb-xs">
                    <div class="col">
                      <div class="text-h6 text-weight-medium">
                        {{ diagnostico.estudiante?.nombres }} {{ diagnostico.estudiante?.apellidos }}
                      </div>
                    </div>
                    <div class="col-auto">
                      <q-badge
                        :color="getRiskColor(diagnostico.nivel_riesgo)"
                        class="text-capitalize q-px-md q-py-sm"
                        style="font-size: 12px;"
                      >
                        {{ diagnostico.nivel_riesgo.toLowerCase() }}
                      </q-badge>
                    </div>
                  </div>

                  <div class="row items-center">
                    <div class="col">
                      <div class="text-caption text-grey-7 q-mb-xs">
                        <q-icon name="description" size="16px" class="q-mr-xs" />
                        {{ diagnostico.tipo }}
                      </div>
                      <div class="text-caption text-grey-6">
                        <q-icon name="calendar_today" size="16px" class="q-mr-xs" />
                        {{ formatDate(diagnostico.fecha) }} •
                        <q-icon name="person" size="16px" class="q-ml-xs q-mr-xs" />
                        Por: {{ diagnostico.psicologo?.name }}
                      </div>
                    </div>
                    <div class="col-auto">
                      <div class="row no-wrap q-gutter-xs">
                        <q-btn
                          flat
                          round
                          icon="visibility"
                          color="info"
                          size="sm"
                          @click.stop="verDiagnostico(diagnostico)"
                          class="action-btn"
                        >
                          <q-tooltip>Ver detalles</q-tooltip>
                        </q-btn>

                        <q-btn
                          v-if="isTOE || isPsicologo"
                          flat
                          round
                          icon="edit"
                          color="primary"
                          size="sm"
                          @click.stop="editarDiagnostico(diagnostico)"
                          class="action-btn"
                        >
                          <q-tooltip>Editar diagnóstico</q-tooltip>
                        </q-btn>

                        <q-btn
                          v-if="isTOE || isPsicologo"
                          flat
                          round
                          icon="delete"
                          color="negative"
                          size="sm"
                          @click.stop="eliminarDiagnostico(diagnostico)"
                          class="action-btn"
                        >
                          <q-tooltip>Eliminar diagnóstico</q-tooltip>
                        </q-btn>
                      </div>
                    </div>
                  </div>

                  <!-- Observaciones Preview -->
                  <div v-if="diagnostico.observaciones" class="q-mt-md">
                    <div class="text-caption text-weight-medium text-grey-8">Observaciones:</div>
                    <div class="text-body2 text-grey-7 line-clamp-2">
                      {{ diagnostico.observaciones }}
                    </div>
                  </div>
                </div>
              </div>
            </q-card-section>
          </q-card>
        </div>
      </q-card-section>
    </q-card>

    <!-- Dialog de Confirmación para Eliminar -->
    <q-dialog v-model="dialogEliminar" persistent>
      <q-card class="delete-dialog">
        <q-card-section class="row items-center">
          <q-avatar icon="warning" color="orange" text-color="white" size="lg" />
          <span class="q-ml-md text-h6">¿Eliminar diagnóstico?</span>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <div class="text-h6 text-weight-medium q-mb-xs">
            {{ diagnosticoSeleccionado?.estudiante?.nombres }} {{ diagnosticoSeleccionado?.estudiante?.apellidos }}
          </div>
          <div class="text-grey-6">
            {{ formatDate(diagnosticoSeleccionado?.fecha) }} - {{ diagnosticoSeleccionado?.tipo }}
          </div>
          <div class="text-negative q-mt-md">
            <q-icon name="info" size="sm" class="q-mr-xs" />
            Esta acción no se puede deshacer
          </div>
        </q-card-section>

        <q-card-actions align="right" class="q-pa-md">
          <q-btn flat label="Cancelar" color="grey" v-close-popup />
          <q-btn
            label="Eliminar Diagnóstico"
            color="negative"
            @click="confirmarEliminar"
            :loading="loadingEliminar"
            unelevated
          />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Dialog de Formulario de Diagnóstico -->
    <q-dialog v-model="dialogFormDiagnostico" persistent maximized>
      <q-card style="width: 900px; max-width: 95vw;" class="form-dialog">
        <q-card-section class="row items-center q-pb-none bg-primary text-white">
          <div class="text-h5">{{ esEdicionDiagnostico ? 'Editar Diagnóstico' : 'Nuevo Diagnóstico' }}</div>
          <q-space />
          <q-btn flat round dense icon="close" v-close-popup class="text-white" />
        </q-card-section>

        <q-card-section class="q-pt-none">
          <DiagnosticoForm
            :diagnostico="diagnosticoSeleccionado"
            :es-edicion="esEdicionDiagnostico"
            @guardado="onDiagnosticoGuardado"
            @cancelado="dialogFormDiagnostico = false"
          />
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useQuasar } from 'quasar'
import { useAuthStore } from 'src/stores/auth-store'
import { diagnosticoService } from 'src/services/api'
import { formatDate, getRiskColor } from 'src/utils/helpers'
import { NIVELES_RIESGO } from 'src/utils/constants'
import DiagnosticoForm from 'src/components/DiagnosticoForm.vue'

const $q = useQuasar()
const authStore = useAuthStore()

const loading = ref(false)
const loadingEliminar = ref(false)
const diagnosticos = ref([])
const estadisticas = ref({})
const dialogEliminar = ref(false)
const dialogFormDiagnostico = ref(false)
const diagnosticoSeleccionado = ref(null)
const esEdicionDiagnostico = ref(false)

const filters = ref({
  search: '',
  nivel_riesgo: '',
  fecha: ''
})

// Computed
const isTOE = computed(() => authStore.isTOE)
const isPsicologo = computed(() => authStore.isPsicologo)

const NIVELES_RIESGO_OPTIONS = NIVELES_RIESGO.map(nivel => ({
  label: nivel.label,
  value: nivel.value
}))

// Methods
async function loadDiagnosticos() {
  loading.value = true
  try {
    const params = {}
    if (filters.value.search) {
      params.dni_estudiante = filters.value.search
    }
    if (filters.value.nivel_riesgo) {
      params.nivel_riesgo = filters.value.nivel_riesgo
    }
    if (filters.value.fecha) {
      params.fecha = filters.value.fecha
    }

    const [diagnosticosResponse, estadisticasResponse] = await Promise.all([
      diagnosticoService.getDiagnosticos(params),
      diagnosticoService.getEstadisticas()
    ])

    diagnosticos.value = diagnosticosResponse.data.diagnosticos
    estadisticas.value = estadisticasResponse.data
  } catch (error) {
    console.error('Error cargando diagnósticos:', error)
    $q.notify({
      type: 'negative',
      message: 'Error cargando la lista de diagnósticos',
      position: 'top'
    })
  } finally {
    loading.value = false
  }
}

function onFilterChange() {
  clearTimeout(window.filterTimeout)
  window.filterTimeout = setTimeout(() => {
    loadDiagnosticos()
  }, 800)
}

function getCountByNivel(nivel) {
  if (!estadisticas.value.por_nivel_riesgo) return 0
  const item = estadisticas.value.por_nivel_riesgo.find(item => item.nivel_riesgo === nivel)
  return item ? item.total : 0
}

function filtrarPorNivel(nivel) {
  filters.value.nivel_riesgo = nivel
  loadDiagnosticos()
}

function nuevoDiagnostico() {
  diagnosticoSeleccionado.value = null
  esEdicionDiagnostico.value = false
  dialogFormDiagnostico.value = true
}

function verDiagnostico(diagnostico) {
  $q.notify({
    message: `Diagnóstico: ${diagnostico.tipo}`,
    caption: `Nivel: ${diagnostico.nivel_riesgo}`,
    color: getRiskColor(diagnostico.nivel_riesgo),
    icon: 'psychology',
    position: 'top'
  })
}

function editarDiagnostico(diagnostico) {
  diagnosticoSeleccionado.value = { ...diagnostico }
  esEdicionDiagnostico.value = true
  dialogFormDiagnostico.value = true
}

function eliminarDiagnostico(diagnostico) {
  diagnosticoSeleccionado.value = diagnostico
  dialogEliminar.value = true
}

async function confirmarEliminar() {
  if (!diagnosticoSeleccionado.value) return

  loadingEliminar.value = true
  try {
    await diagnosticoService.deleteDiagnostico(diagnosticoSeleccionado.value.id)

    $q.notify({
      type: 'positive',
      message: 'Diagnóstico eliminado exitosamente',
      position: 'top'
    })

    await loadDiagnosticos()
    dialogEliminar.value = false
    diagnosticoSeleccionado.value = null

  } catch (error) {
    console.error('Error eliminando diagnóstico:', error)
    $q.notify({
      type: 'negative',
      message: error.response?.data?.message || 'Error eliminando diagnóstico',
      position: 'top'
    })
  } finally {
    loadingEliminar.value = false
  }
}

function onDiagnosticoGuardado() {
  dialogFormDiagnostico.value = false
  loadDiagnosticos()
}

// Lifecycle
onMounted(() => {
  loadDiagnosticos()
})
</script>

<style scoped>
.summary-card {
  transition: all 0.3s ease;
  border-radius: 16px;
  cursor: pointer;
}

.summary-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.diagnostico-card {
  transition: all 0.3s ease;
  border-radius: 12px;
  border-left: 4px solid;
}

.diagnostico-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.diagnostico-card[style*="border-left-color: positive"] {
  border-left-color: #4caf50;
}

.diagnostico-card[style*="border-left-color: warning"] {
  border-left-color: #ff9800;
}

.diagnostico-card[style*="border-left-color: negative"] {
  border-left-color: #f44336;
}

.action-btn {
  transition: all 0.3s ease;
}

.action-btn:hover {
  transform: scale(1.1);
}

.delete-dialog {
  width: 450px;
  max-width: 90vw;
  border-radius: 16px;
}

.form-dialog {
  border-radius: 12px;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>

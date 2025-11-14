<template>
  <q-page class="q-pa-lg">
    <!-- Header Mejorado -->
    <div class="row items-center q-mb-xl">
      <div class="col">
        <div class="text-h3 text-weight-bold text-primary q-mb-xs">
          Gestión de Citas
        </div>
        <div class="text-h6 text-grey-7">
          Administra la agenda de citas psicológicas
        </div>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          icon="add"
          label="Nueva Cita"
          @click="nuevaCita"
          v-if="isTOE || isPsicologo"
          unelevated
          class="q-px-lg"
          size="lg"
        >
          <q-tooltip>Agendar nueva cita</q-tooltip>
        </q-btn>
      </div>
    </div>

    <!-- Tarjetas de Resumen -->
    <div class="row q-col-gutter-xl q-mb-xl">
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="summary-card" flat bordered @click="filtrarPorEstado('')">
          <q-card-section class="q-pa-xl text-center">
            <q-icon name="event" size="xl" color="primary" class="q-mb-md" />
            <div class="text-h4 text-weight-bold text-primary">{{ estadisticas.total || 0 }}</div>
            <div class="text-h6 text-grey-7">Total Citas</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="summary-card" flat bordered @click="filtrarPorEstado('PENDIENTE')">
          <q-card-section class="q-pa-xl text-center">
            <q-icon name="schedule" size="xl" color="orange" class="q-mb-md" />
            <div class="text-h4 text-weight-bold text-orange">{{ getCountByEstado('PENDIENTE') }}</div>
            <div class="text-h6 text-grey-7">Pendientes</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="summary-card" flat bordered @click="filtrarPorEstado('REALIZADA')">
          <q-card-section class="q-pa-xl text-center">
            <q-icon name="check_circle" size="xl" color="positive" class="q-mb-md" />
            <div class="text-h4 text-weight-bold text-positive">{{ getCountByEstado('REALIZADA') }}</div>
            <div class="text-h6 text-grey-7">Realizadas</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="summary-card" flat bordered @click="filtrarPorEstado('CANCELADA')">
          <q-card-section class="q-pa-xl text-center">
            <q-icon name="cancel" size="xl" color="negative" class="q-mb-md" />
            <div class="text-h4 text-weight-bold text-negative">{{ getCountByEstado('CANCELADA') }}</div>
            <div class="text-h6 text-grey-7">Canceladas</div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Panel de Filtros Mejorado -->
    <q-card class="q-mb-xl shadow-1" flat bordered>
      <q-card-section class="q-pa-xl">
        <div class="text-h5 text-weight-bold q-mb-lg">Filtros de Búsqueda</div>
        <div class="row q-col-gutter-lg items-end">
          <div class="col-12 col-sm-3">
            <q-input
              v-model="filters.search"
              label="Buscar por estudiante"
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
              v-model="filters.estado"
              :options="ESTADOS_OPTIONS"
              label="Estado de la cita"
              clearable
              @update:model-value="onFilterChange"
              outlined
              color="primary"
            />
          </div>
          <div class="col-12 col-sm-3">
            <q-input
              v-model="filters.fecha"
              label="Fecha de la cita"
              type="date"
              clearable
              @update:model-value="onFilterChange"
              outlined
              color="primary"
            />
          </div>
          <div class="col-12 col-sm-3">
            <div class="row q-col-gutter-sm">
              <div class="col-6">
                <q-btn
                  color="primary"
                  icon="filter_alt"
                  label="Filtrar"
                  @click="loadCitas"
                  :loading="loading"
                  unelevated
                  class="full-width"
                />
              </div>
              <div class="col-6">
                <q-btn
                  color="orange"
                  icon="event"
                  label="Pendientes"
                  @click="verPendientes"
                  unelevated
                  class="full-width"
                />
              </div>
            </div>
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Vista de Calendario y Lista -->
    <q-card class="shadow-1" flat bordered>
      <q-card-section class="q-pa-xl">
        <div class="row items-center q-mb-lg">
          <div class="col">
            <div class="text-h5 text-weight-bold">
              Agenda de Citas
              <q-badge v-if="citas.length" color="primary" class="q-ml-md" style="font-size: 16px;">
                {{ citas.length }}
              </q-badge>
            </div>
            <div class="text-caption text-grey-6">
              Gestión completa de la agenda psicológica
            </div>
          </div>
          <div class="col-auto">
            <q-btn
              flat
              round
              icon="refresh"
              @click="loadCitas"
              :loading="loading"
              color="primary"
              size="lg"
            >
              <q-tooltip>Actualizar agenda</q-tooltip>
            </q-btn>
          </div>
        </div>

        <!-- Estado de Carga -->
        <div v-if="loading" class="text-center q-pa-xl">
          <q-spinner-puff size="xl" color="primary" />
          <div class="text-h6 q-mt-md text-grey-6">Cargando citas...</div>
        </div>

        <!-- Estado Vacío -->
        <div v-else-if="citas.length === 0" class="text-center q-pa-xl">
          <q-icon name="event" size="xl" color="grey-4" />
          <div class="text-h5 q-mt-md text-grey-6">No se encontraron citas</div>
          <div class="text-grey-6 q-mb-lg">No hay citas que coincidan con los filtros aplicados</div>
          <q-btn
            v-if="isTOE || isPsicologo"
            color="primary"
            icon="add"
            label="Agendar Primera Cita"
            @click="nuevaCita"
            unelevated
            size="lg"
          />
        </div>

        <!-- Lista de Citas -->
        <div v-else class="q-gutter-y-md">
          <q-card
            v-for="cita in citas"
            :key="cita.id"
            flat
            bordered
            class="cita-card cursor-pointer"
            @click="verCita(cita)"
          >
            <q-card-section class="q-pa-lg">
              <div class="row items-center">
                <div class="col-auto q-mr-lg">
                  <q-avatar
                    size="60px"
                    :color="getStatusColor(cita.estado)"
                    text-color="white"
                    class="shadow-2"
                  >
                    <q-icon name="event" size="28px" />
                  </q-avatar>
                </div>

                <div class="col">
                  <div class="row items-center q-mb-xs">
                    <div class="col">
                      <div class="text-h6 text-weight-medium">
                        {{ cita.estudiante?.nombres }} {{ cita.estudiante?.apellidos }}
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

                  <div class="row items-center">
                    <div class="col">
                      <div class="text-caption text-grey-7 q-mb-xs">
                        <q-icon name="schedule" size="16px" class="q-mr-xs" />
                        {{ formatDateTime(cita.fecha_cita) }}
                      </div>
                      <div class="text-caption text-grey-6">
                        <q-icon name="description" size="16px" class="q-mr-xs" />
                        {{ cita.motivo }}
                      </div>
                      <div class="text-caption text-grey-6 q-mt-xs">
                        <q-icon
                          :name="cita.correo_enviado ? 'mark_email_read' : 'mark_unread'"
                          size="16px"
                          class="q-mr-xs"
                          :color="cita.correo_enviado ? 'positive' : 'grey'"
                        />
                        {{ cita.correo_enviado ? 'Notificación enviada' : 'Notificación pendiente' }}
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
                          @click.stop="verCita(cita)"
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
                          @click.stop="editarCita(cita)"
                          class="action-btn"
                        >
                          <q-tooltip>Editar cita</q-tooltip>
                        </q-btn>

                        <q-btn
                          v-if="(isTOE || isPsicologo) && cita.estado === 'PENDIENTE'"
                          flat
                          round
                          icon="check"
                          color="positive"
                          size="sm"
                          @click.stop="marcarRealizada(cita)"
                          class="action-btn"
                        >
                          <q-tooltip>Marcar como realizada</q-tooltip>
                        </q-btn>

                        <q-btn
                          v-if="isTOE || isPsicologo"
                          flat
                          round
                          icon="delete"
                          color="negative"
                          size="sm"
                          @click.stop="eliminarCita(cita)"
                          class="action-btn"
                        >
                          <q-tooltip>Eliminar cita</q-tooltip>
                        </q-btn>
                      </div>
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
          <span class="q-ml-md text-h6">¿Eliminar cita?</span>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <div class="text-h6 text-weight-medium q-mb-xs">
            {{ citaSeleccionada?.estudiante?.nombres }} {{ citaSeleccionada?.estudiante?.apellidos }}
          </div>
          <div class="text-grey-6">
            {{ formatDateTime(citaSeleccionada?.fecha_cita) }}
          </div>
          <div class="text-negative q-mt-md">
            <q-icon name="info" size="sm" class="q-mr-xs" />
            Esta acción no se puede deshacer
          </div>
        </q-card-section>

        <q-card-actions align="right" class="q-pa-md">
          <q-btn flat label="Cancelar" color="grey" v-close-popup />
          <q-btn
            label="Eliminar Cita"
            color="negative"
            @click="confirmarEliminar"
            :loading="loadingEliminar"
            unelevated
          />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Dialog de Confirmación para Marcar como Realizada -->
    <q-dialog v-model="dialogMarcarRealizada" persistent>
      <q-card class="confirm-dialog">
        <q-card-section class="row items-center">
          <q-avatar icon="check_circle" color="positive" text-color="white" size="lg" />
          <span class="q-ml-md text-h6">¿Marcar como realizada?</span>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <div class="text-h6 text-weight-medium q-mb-xs">
            {{ citaSeleccionada?.estudiante?.nombres }} {{ citaSeleccionada?.estudiante?.apellidos }}
          </div>
          <div class="text-grey-6">
            {{ formatDateTime(citaSeleccionada?.fecha_cita) }}
          </div>
          <div class="text-positive q-mt-md">
            <q-icon name="info" size="sm" class="q-mr-xs" />
            La cita será marcada como completada
          </div>
        </q-card-section>

        <q-card-actions align="right" class="q-pa-md">
          <q-btn flat label="Cancelar" color="grey" v-close-popup />
          <q-btn
            label="Marcar Realizada"
            color="positive"
            @click="confirmarMarcarRealizada"
            :loading="loadingMarcarRealizada"
            unelevated
          />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Dialog de Formulario de Cita -->
    <q-dialog v-model="dialogFormCita" persistent maximized>
      <q-card style="width: 900px; max-width: 95vw;" class="form-dialog">
        <q-card-section class="row items-center q-pb-none bg-primary text-white">
          <div class="text-h5">{{ esEdicionCita ? 'Editar Cita' : 'Nueva Cita' }}</div>
          <q-space />
          <q-btn flat round dense icon="close" v-close-popup class="text-white" />
        </q-card-section>

        <q-card-section class="q-pt-none">
          <CitaForm
            :cita="citaSeleccionada"
            :es-edicion="esEdicionCita"
            @guardado="onCitaGuardada"
            @cancelado="dialogFormCita = false"
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
import { citaService } from 'src/services/api'
import { formatDateTime, getStatusColor } from 'src/utils/helpers'
import CitaForm from 'src/components/CitaForm.vue'

const $q = useQuasar()
const authStore = useAuthStore()

const loading = ref(false)
const loadingEliminar = ref(false)
const loadingMarcarRealizada = ref(false)
const citas = ref([])
const estadisticas = ref({})
const dialogEliminar = ref(false)
const dialogMarcarRealizada = ref(false)
const dialogFormCita = ref(false)
const citaSeleccionada = ref(null)
const esEdicionCita = ref(false)

const filters = ref({
  search: '',
  estado: '',
  fecha: ''
})

// Computed
const isTOE = computed(() => authStore.isTOE)
const isPsicologo = computed(() => authStore.isPsicologo)

const ESTADOS_OPTIONS = [
  { label: 'Pendiente', value: 'PENDIENTE' },
  { label: 'Realizada', value: 'REALIZADA' },
  { label: 'Cancelada', value: 'CANCELADA' }
]

// Methods
async function loadCitas() {
  loading.value = true
  try {
    const params = {}
    if (filters.value.search) {
      params.dni_estudiante = filters.value.search
    }
    if (filters.value.estado) {
      params.estado = filters.value.estado
    }
    if (filters.value.fecha) {
      params.fecha = filters.value.fecha
    }

    const response = await citaService.getCitas(params)
    citas.value = response.data.citas

    // Calcular estadísticas básicas
    estadisticas.value = {
      total: citas.value.length,
      por_estado: citas.value.reduce((acc, cita) => {
        acc[cita.estado] = (acc[cita.estado] || 0) + 1
        return acc
      }, {})
    }
  } catch (error) {
    console.error('Error cargando citas:', error)
    $q.notify({
      type: 'negative',
      message: 'Error cargando la lista de citas',
      position: 'top'
    })
  } finally {
    loading.value = false
  }
}

function onFilterChange() {
  clearTimeout(window.filterTimeout)
  window.filterTimeout = setTimeout(() => {
    loadCitas()
  }, 800)
}

function getCountByEstado(estado) {
  return estadisticas.value.por_estado?.[estado] || 0
}

function filtrarPorEstado(estado) {
  filters.value.estado = estado
  loadCitas()
}

function verPendientes() {
  filters.value.estado = 'PENDIENTE'
  loadCitas()
}

function nuevaCita() {
  citaSeleccionada.value = null
  esEdicionCita.value = false
  dialogFormCita.value = true
}

function verCita(cita) {
  $q.notify({
    message: `Cita: ${cita.estudiante?.nombres}`,
    caption: `Estado: ${cita.estado}`,
    color: getStatusColor(cita.estado),
    icon: 'event',
    position: 'top'
  })
}

function editarCita(cita) {
  citaSeleccionada.value = { ...cita }
  esEdicionCita.value = true
  dialogFormCita.value = true
}

function marcarRealizada(cita) {
  citaSeleccionada.value = cita
  dialogMarcarRealizada.value = true
}

async function confirmarMarcarRealizada() {
  if (!citaSeleccionada.value) return

  loadingMarcarRealizada.value = true
  try {
    await citaService.marcarRealizada(citaSeleccionada.value.id)

    $q.notify({
      type: 'positive',
      message: 'Cita marcada como realizada',
      position: 'top'
    })

    await loadCitas()
    dialogMarcarRealizada.value = false
    citaSeleccionada.value = null

  } catch (error) {
    console.error('Error marcando cita como realizada:', error)
    $q.notify({
      type: 'negative',
      message: error.response?.data?.message || 'Error marcando cita como realizada',
      position: 'top'
    })
  } finally {
    loadingMarcarRealizada.value = false
  }
}

function eliminarCita(cita) {
  citaSeleccionada.value = cita
  dialogEliminar.value = true
}

async function confirmarEliminar() {
  if (!citaSeleccionada.value) return

  loadingEliminar.value = true
  try {
    await citaService.deleteCita(citaSeleccionada.value.id)

    $q.notify({
      type: 'positive',
      message: 'Cita eliminada exitosamente',
      position: 'top'
    })

    await loadCitas()
    dialogEliminar.value = false
    citaSeleccionada.value = null

  } catch (error) {
    console.error('Error eliminando cita:', error)
    $q.notify({
      type: 'negative',
      message: error.response?.data?.message || 'Error eliminando cita',
      position: 'top'
    })
  } finally {
    loadingEliminar.value = false
  }
}

function onCitaGuardada() {
  dialogFormCita.value = false
  loadCitas()
}

// Lifecycle
onMounted(() => {
  loadCitas()
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

.cita-card {
  transition: all 0.3s ease;
  border-radius: 12px;
  border-left: 4px solid;
}

.cita-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.cita-card[style*="border-left-color: warning"] {
  border-left-color: #ff9800;
}

.cita-card[style*="border-left-color: positive"] {
  border-left-color: #4caf50;
}

.cita-card[style*="border-left-color: negative"] {
  border-left-color: #f44336;
}

.action-btn {
  transition: all 0.3s ease;
}

.action-btn:hover {
  transform: scale(1.1);
}

.delete-dialog,
.confirm-dialog {
  width: 450px;
  max-width: 90vw;
  border-radius: 16px;
}

.form-dialog {
  border-radius: 12px;
}
</style>

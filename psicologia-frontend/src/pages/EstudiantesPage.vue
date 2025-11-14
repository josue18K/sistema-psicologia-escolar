<template>
  <q-page class="q-pa-lg">
    <!-- Header Mejorado -->
    <div class="row items-center q-mb-xl">
      <div class="col">
        <div class="text-h3 text-weight-bold text-primary q-mb-xs">
          Gestión de Estudiantes
        </div>
        <div class="text-h6 text-grey-7">
          Administra los estudiantes del sistema educativo
        </div>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          icon="add"
          label="Nuevo Estudiante"
          @click="$router.push('/estudiantes/nuevo')"
          v-if="isTOE || isPsicologo"
          unelevated
          class="q-px-lg"
          size="lg"
        >
          <q-tooltip>Registrar nuevo estudiante</q-tooltip>
        </q-btn>
      </div>
    </div>

    <!-- Tarjetas de Resumen -->
    <div class="row q-col-gutter-xl q-mb-xl">
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="summary-card" flat bordered>
          <q-card-section class="q-pa-xl text-center">
            <q-icon name="school" size="xl" color="primary" class="q-mb-md" />
            <div class="text-h4 text-weight-bold text-primary">{{ totalEstudiantes }}</div>
            <div class="text-h6 text-grey-7">Total Estudiantes</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="summary-card" flat bordered>
          <q-card-section class="q-pa-xl text-center">
            <q-icon name="group" size="xl" color="green" class="q-mb-md" />
            <div class="text-h4 text-weight-bold text-positive">{{ estudiantesConApoderado }}</div>
            <div class="text-h6 text-grey-7">Con Apoderado</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="summary-card" flat bordered>
          <q-card-section class="q-pa-xl text-center">
            <q-icon name="person" size="xl" color="orange" class="q-mb-md" />
            <div class="text-h4 text-weight-bold text-orange">{{ estudiantesSinApoderado }}</div>
            <div class="text-h6 text-grey-7">Sin Apoderado</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="summary-card" flat bordered>
          <q-card-section class="q-pa-xl text-center">
            <q-icon name="diversity" size="xl" color="purple" class="q-mb-md" />
            <div class="text-h4 text-weight-bold text-purple">{{ gradosUnicos }}</div>
            <div class="text-h6 text-grey-7">Grados Diferentes</div>
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
              label="Buscar por nombre, apellido o DNI"
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
              v-model="filters.grado"
              :options="GRADOS"
              label="Filtrar por Grado"
              clearable
              @update:model-value="onFilterChange"
              outlined
              color="primary"
            />
          </div>
          <div class="col-12 col-sm-3">
            <q-select
              v-model="filters.seccion"
              :options="SECCIONES"
              label="Filtrar por Sección"
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
              label="Aplicar"
              @click="loadEstudiantes"
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
              Lista de Estudiantes
              <q-badge v-if="estudiantes.length" color="primary" class="q-ml-md" style="font-size: 16px;">
                {{ estudiantes.length }}
              </q-badge>
            </div>
            <div class="text-caption text-grey-6">
              Gestiona la información de los estudiantes registrados
            </div>
          </div>
          <div class="col-auto">
            <q-btn
              flat
              round
              icon="refresh"
              @click="loadEstudiantes"
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
          <div class="text-h6 q-mt-md text-grey-6">Cargando estudiantes...</div>
        </div>

        <!-- Estado Vacío -->
        <div v-else-if="estudiantes.length === 0" class="text-center q-pa-xl">
          <q-icon name="school" size="xl" color="grey-4" />
          <div class="text-h5 q-mt-md text-grey-6">No se encontraron estudiantes</div>
          <div class="text-grey-6 q-mb-lg">No hay estudiantes que coincidan con los filtros aplicados</div>
          <q-btn
            v-if="isTOE || isPsicologo"
            color="primary"
            icon="add"
            label="Registrar Primer Estudiante"
            @click="$router.push('/estudiantes/nuevo')"
            unelevated
            size="lg"
          />
        </div>

        <!-- Tabla de Estudiantes -->
        <q-table
          v-else
          :rows="estudiantes"
          :columns="columns"
          row-key="dni"
          :pagination="pagination"
          flat
          bordered
          class="students-table"
          :loading="loading"
        >
          <template v-slot:body-cell-estudiante="props">
            <q-td :props="props">
              <div class="row items-center">
                <q-avatar size="40px" color="primary" text-color="white" class="q-mr-sm">
                  {{ props.row.nombres.charAt(0) }}{{ props.row.apellidos.charAt(0) }}
                </q-avatar>
                <div>
                  <div class="text-weight-medium">
                    {{ props.row.nombres }} {{ props.row.apellidos }}
                  </div>
                  <div class="text-caption text-grey-6">
                    DNI: {{ props.row.dni }}
                  </div>
                </div>
              </div>
            </q-td>
          </template>

          <template v-slot:body-cell-grado_seccion="props">
            <q-td :props="props">
              <q-badge color="primary" class="q-px-md q-py-xs">
                {{ props.row.grado }} {{ props.row.seccion }}
              </q-badge>
            </q-td>
          </template>

          <template v-slot:body-cell-tutor="props">
            <q-td :props="props">
              <div v-if="props.row.tutor" class="text-weight-medium">
                {{ props.row.tutor.name }}
              </div>
              <q-badge v-else color="grey" outline>
                Sin tutor
              </q-badge>
            </q-td>
          </template>

          <template v-slot:body-cell-apoderado="props">
            <q-td :props="props">
              <div v-if="props.row.apoderado" class="text-weight-medium">
                {{ props.row.apoderado.nombre }}
                <div class="text-caption text-grey-6">
                  {{ props.row.apoderado.parentesco }}
                </div>
              </div>
              <q-badge v-else color="orange" outline>
                Sin apoderado
              </q-badge>
            </q-td>
          </template>

          <template v-slot:body-cell-actions="props">
            <q-td :props="props">
              <div class="row no-wrap q-gutter-xs justify-center">
                <q-btn
                  flat
                  round
                  icon="visibility"
                  color="info"
                  size="sm"
                  @click="verEstudiante(props.row)"
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
                  @click="editarEstudiante(props.row)"
                  class="action-btn"
                >
                  <q-tooltip>Editar estudiante</q-tooltip>
                </q-btn>

                <q-btn
                  v-if="isTOE || isPsicologo"
                  flat
                  round
                  icon="delete"
                  color="negative"
                  size="sm"
                  @click="eliminarEstudiante(props.row)"
                  class="action-btn"
                >
                  <q-tooltip>Eliminar estudiante</q-tooltip>
                </q-btn>
              </div>
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <!-- Dialog de Confirmación para Eliminar -->
    <q-dialog v-model="dialogEliminar" persistent>
      <q-card class="delete-dialog">
        <q-card-section class="row items-center">
          <q-avatar icon="warning" color="orange" text-color="white" size="lg" />
          <span class="q-ml-md text-h6">¿Eliminar estudiante?</span>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <div class="text-h6 text-weight-medium q-mb-xs">
            {{ estudianteSeleccionado?.nombres }} {{ estudianteSeleccionado?.apellidos }}
          </div>
          <div class="text-grey-6">
            DNI: {{ estudianteSeleccionado?.dni }} - {{ estudianteSeleccionado?.grado }} {{ estudianteSeleccionado?.seccion }}
          </div>
          <div class="text-negative q-mt-md">
            <q-icon name="info" size="sm" class="q-mr-xs" />
            Esta acción no se puede deshacer
          </div>
        </q-card-section>

        <q-card-actions align="right" class="q-pa-md">
          <q-btn flat label="Cancelar" color="grey" v-close-popup />
          <q-btn
            label="Eliminar Estudiante"
            color="negative"
            @click="confirmarEliminar"
            :loading="loadingEliminar"
            unelevated
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useAuthStore } from 'src/stores/auth-store'
import { estudianteService } from 'src/services/api'
import { GRADOS, SECCIONES } from 'src/utils/constants'
import { formatDate } from 'src/utils/helpers'

const $q = useQuasar()
const router = useRouter()
const authStore = useAuthStore()

const loading = ref(false)
const loadingEliminar = ref(false)
const estudiantes = ref([])
const dialogEliminar = ref(false)
const estudianteSeleccionado = ref(null)

const filters = ref({
  search: '',
  grado: '',
  seccion: ''
})

const pagination = ref({
  sortBy: 'apellidos',
  descending: false,
  page: 1,
  rowsPerPage: 10
})

// Computed
const isTOE = computed(() => authStore.isTOE)
const isPsicologo = computed(() => authStore.isPsicologo)

const totalEstudiantes = computed(() => estudiantes.value.length)
const estudiantesConApoderado = computed(() =>
  estudiantes.value.filter(est => est.apoderado).length
)
const estudiantesSinApoderado = computed(() =>
  estudiantes.value.filter(est => !est.apoderado).length
)
const gradosUnicos = computed(() =>
  new Set(estudiantes.value.map(est => est.grado)).size
)

// Columnas de la tabla mejoradas
const columns = [
  {
    name: 'estudiante',
    required: true,
    label: 'Estudiante',
    align: 'left',
    field: row => `${row.nombres} ${row.apellidos}`,
    sortable: true
  },
  {
    name: 'grado_seccion',
    label: 'Grado/Sección',
    align: 'center',
    field: row => `${row.grado} ${row.seccion}`,
    sortable: true
  },
  {
    name: 'edad',
    label: 'Edad',
    align: 'center',
    field: 'edad',
    sortable: true
  },
  {
    name: 'fecha_nacimiento',
    label: 'Fecha Nac.',
    align: 'center',
    field: row => formatDate(row.fecha_nacimiento),
    sortable: true
  },
  {
    name: 'tutor',
    label: 'Tutor',
    align: 'left',
    field: row => row.tutor?.name
  },
  {
    name: 'apoderado',
    label: 'Apoderado',
    align: 'left',
    field: row => row.apoderado?.nombre
  },
  {
    name: 'actions',
    label: 'Acciones',
    align: 'center',
    sortable: false
  }
]

// Methods
async function loadEstudiantes() {
  loading.value = true
  try {
    let response

    if (filters.value.search || filters.value.grado || filters.value.seccion) {
      const params = {}
      if (filters.value.search) {
        if (/^\d+$/.test(filters.value.search)) {
          params.dni = filters.value.search
        } else {
          params.nombre = filters.value.search
        }
      }
      if (filters.value.grado) params.grado = filters.value.grado
      if (filters.value.seccion) params.seccion = filters.value.seccion

      response = await estudianteService.searchEstudiantes(params)
    } else {
      response = await estudianteService.getEstudiantes()
    }

    estudiantes.value = response.data.estudiantes
  } catch (error) {
    console.error('Error cargando estudiantes:', error)
    $q.notify({
      type: 'negative',
      message: 'Error cargando la lista de estudiantes',
      position: 'top'
    })
  } finally {
    loading.value = false
  }
}

function onFilterChange() {
  clearTimeout(window.filterTimeout)
  window.filterTimeout = setTimeout(() => {
    loadEstudiantes()
  }, 800)
}

function verEstudiante(estudiante) {
  router.push(`/estudiantes/${estudiante.dni}`)
}

function editarEstudiante(estudiante) {
  router.push(`/estudiantes/editar/${estudiante.dni}`)
}

function eliminarEstudiante(estudiante) {
  estudianteSeleccionado.value = estudiante
  dialogEliminar.value = true
}

async function confirmarEliminar() {
  if (!estudianteSeleccionado.value) return

  loadingEliminar.value = true
  try {
    await estudianteService.deleteEstudiante(estudianteSeleccionado.value.dni)

    $q.notify({
      type: 'positive',
      message: 'Estudiante eliminado exitosamente',
      position: 'top'
    })

    await loadEstudiantes()
    dialogEliminar.value = false
    estudianteSeleccionado.value = null

  } catch (error) {
    console.error('Error eliminando estudiante:', error)
    $q.notify({
      type: 'negative',
      message: error.response?.data?.message || 'Error eliminando estudiante',
      position: 'top'
    })
  } finally {
    loadingEliminar.value = false
  }
}

// Lifecycle
onMounted(() => {
  loadEstudiantes()
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

.students-table {
  border-radius: 12px;
}

.students-table :deep(thead tr) {
  background-color: #f8f9fa;
}

.students-table :deep(thead th) {
  font-weight: 600;
  font-size: 14px;
  color: #2c3e50;
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
</style>

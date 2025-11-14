<template>
  <q-page class="q-pa-md">
    <!-- Header -->
    <div class="row items-center q-mb-md">
      <div class="col">
        <div class="text-h4 text-weight-bold text-primary">
          Gestión de Apoderados
        </div>
        <div class="text-h6 text-grey-7">
          Administra los apoderados del sistema
        </div>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          icon="add"
          label="Nuevo Apoderado"
          @click="nuevoApoderado"
          v-if="isTOE || isPsicologo"
        />
      </div>
    </div>

    <!-- Tabla de Apoderados -->
    <q-card flat bordered>
      <q-card-section>
        <div class="row items-center">
          <div class="col">
            <div class="text-h6">
              Lista de Apoderados
              <q-badge v-if="apoderados.length" color="primary">
                {{ apoderados.length }}
              </q-badge>
            </div>
          </div>
          <div class="col-auto">
            <q-btn
              flat
              round
              icon="refresh"
              @click="loadApoderados"
              :loading="loading"
            >
              <q-tooltip>Actualizar lista</q-tooltip>
            </q-btn>
          </div>
        </div>
      </q-card-section>

      <q-card-section class="q-pt-none">
        <div v-if="loading" class="text-center q-pa-xl">
          <q-spinner-gears size="xl" color="primary" />
          <div class="q-mt-md">Cargando apoderados...</div>
        </div>

        <div v-else-if="apoderados.length === 0" class="text-center q-pa-xl">
          <q-icon name="family_restroom" size="xl" color="grey" />
          <div class="text-h6 q-mt-md text-grey">No hay apoderados</div>
          <div class="text-grey q-mb-md">No se encontraron apoderados registrados</div>
          <q-btn
            color="primary"
            icon="add"
            label="Agregar Primer Apoderado"
            @click="nuevoApoderado"
            v-if="isTOE || isPsicologo"
          />
        </div>

        <q-table
          v-else
          :rows="apoderados"
          :columns="columns"
          row-key="id"
          :pagination="pagination"
          flat
          bordered
        >
          <template v-slot:body-cell-estudiantes="props">
            <q-td :props="props">
              <div v-if="props.row.estudiantes && props.row.estudiantes.length">
                <q-badge color="primary">
                  {{ props.row.estudiantes.length }} estudiante(s)
                </q-badge>
                <div class="q-mt-xs">
                  <div
                    v-for="est in props.row.estudiantes.slice(0, 2)"
                    :key="est.dni"
                    class="text-caption"
                  >
                    {{ est.nombres }} {{ est.apellidos }}
                  </div>
                  <div
                    v-if="props.row.estudiantes.length > 2"
                    class="text-caption text-grey"
                  >
                    +{{ props.row.estudiantes.length - 2 }} más
                  </div>
                </div>
              </div>
              <div v-else class="text-grey">
                Sin estudiantes
              </div>
            </q-td>
          </template>

          <template v-slot:body-cell-actions="props">
            <q-td :props="props" class="q-gutter-x-sm">
              <q-btn
                flat
                round
                icon="visibility"
                color="info"
                size="sm"
                @click="verApoderado(props.row)"
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
                @click="editarApoderado(props.row)"
              >
                <q-tooltip>Editar</q-tooltip>
              </q-btn>

              <q-btn
                v-if="isTOE || isPsicologo"
                flat
                round
                icon="delete"
                color="negative"
                size="sm"
                @click="eliminarApoderado(props.row)"
                :disable="props.row.estudiantes && props.row.estudiantes.length > 0"
              >
                <q-tooltip>
                  {{ props.row.estudiantes && props.row.estudiantes.length > 0 ? 'No se puede eliminar - Tiene estudiantes asignados' : 'Eliminar' }}
                </q-tooltip>
              </q-btn>
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <!-- Dialog de Confirmación para Eliminar -->
    <q-dialog v-model="dialogEliminar" persistent>
      <q-card>
        <q-card-section class="row items-center">
          <q-avatar icon="warning" color="orange" text-color="white" />
          <span class="q-ml-sm">¿Estás seguro de eliminar a {{ apoderadoSeleccionado?.nombre }}?</span>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancelar" color="primary" v-close-popup />
          <q-btn flat label="Eliminar" color="negative" @click="confirmarEliminar" :loading="loadingEliminar" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useQuasar } from 'quasar'
import { useAuthStore } from 'src/stores/auth-store'
import { apoderadoService } from 'src/services/api'

const $q = useQuasar()
const authStore = useAuthStore()

const loading = ref(false)
const loadingEliminar = ref(false)
const apoderados = ref([])
const dialogEliminar = ref(false)
const apoderadoSeleccionado = ref(null)

const pagination = ref({
  sortBy: 'nombre',
  descending: false,
  page: 1,
  rowsPerPage: 10
})

// Computed
const isTOE = computed(() => authStore.isTOE)
const isPsicologo = computed(() => authStore.isPsicologo)

// Columnas de la tabla
const columns = [
  {
    name: 'nombre',
    required: true,
    label: 'Nombre',
    align: 'left',
    field: 'nombre',
    sortable: true
  },
  {
    name: 'parentesco',
    label: 'Parentesco',
    align: 'center',
    field: 'parentesco',
    sortable: true
  },
  {
    name: 'correo',
    label: 'Correo',
    align: 'left',
    field: 'correo',
    sortable: true
  },
  {
    name: 'telefono',
    label: 'Teléfono',
    align: 'center',
    field: 'telefono',
    sortable: true
  },
  {
    name: 'estudiantes',
    label: 'Estudiantes',
    align: 'left',
    sortable: false
  },
  {
    name: 'actions',
    label: 'Acciones',
    align: 'center',
    sortable: false
  }
]

// Methods
async function loadApoderados() {
  loading.value = true
  try {
    const response = await apoderadoService.getApoderados()
    apoderados.value = response.data.apoderados
  } catch (error) {
    console.error('Error cargando apoderados:', error)
    $q.notify({
      type: 'negative',
      message: 'Error cargando la lista de apoderados'
    })
  } finally {
    loading.value = false
  }
}

function nuevoApoderado() {
  // Aquí podrías implementar un formulario modal o redirigir a una nueva página
  $q.notify({
    type: 'info',
    message: 'Los apoderados se crean automáticamente al registrar estudiantes'
  })
}

function verApoderado(apoderado) {
  // Aquí podrías implementar la vista de detalle
  $q.notify({
    type: 'info',
    message: `Vista de detalle para ${apoderado.nombre}`
  })
}

function editarApoderado(apoderado) {
  // Aquí podrías implementar la edición
  $q.notify({
    type: 'info',
    message: `Editar apoderado ${apoderado.nombre}`
  })
}

function eliminarApoderado(apoderado) {
  if (apoderado.estudiantes && apoderado.estudiantes.length > 0) {
    $q.notify({
      type: 'warning',
      message: 'No se puede eliminar un apoderado con estudiantes asignados'
    })
    return
  }

  apoderadoSeleccionado.value = apoderado
  dialogEliminar.value = true
}

async function confirmarEliminar() {
  if (!apoderadoSeleccionado.value) return

  loadingEliminar.value = true
  try {
    await apoderadoService.deleteApoderado(apoderadoSeleccionado.value.id)

    $q.notify({
      type: 'positive',
      message: 'Apoderado eliminado exitosamente'
    })

    // Recargar la lista
    await loadApoderados()
    dialogEliminar.value = false
    apoderadoSeleccionado.value = null

  } catch (error) {
    console.error('Error eliminando apoderado:', error)
    $q.notify({
      type: 'negative',
      message: error.response?.data?.message || 'Error eliminando apoderado'
    })
  } finally {
    loadingEliminar.value = false
  }
}

// Lifecycle
onMounted(() => {
  loadApoderados()
})
</script>

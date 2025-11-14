<template>
  <q-page class="q-pa-lg">
    <!-- Header Mejorado -->
    <div class="row items-center q-mb-xl">
      <div class="col">
        <div class="text-h3 text-weight-bold text-primary q-mb-xs">
          Gestión de Usuarios
        </div>
        <div class="text-h6 text-grey-7">
          Administra los usuarios y permisos del sistema
        </div>
      </div>
      <div class="col-auto">
        <q-btn
          color="primary"
          icon="add"
          label="Nuevo Usuario"
          @click="nuevoUsuario"
          v-if="isTOE"
          unelevated
          class="q-px-lg"
          size="lg"
        >
          <q-tooltip>Registrar nuevo usuario</q-tooltip>
        </q-btn>
      </div>
    </div>

    <!-- Tarjetas de Resumen -->
    <div class="row q-col-gutter-xl q-mb-xl">
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="summary-card" flat bordered @click="filtrarPorRol('')">
          <q-card-section class="q-pa-xl text-center">
            <q-icon name="people" size="xl" color="primary" class="q-mb-md" />
            <div class="text-h4 text-weight-bold text-primary">{{ totalUsuarios }}</div>
            <div class="text-h6 text-grey-7">Total Usuarios</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="summary-card" flat bordered @click="filtrarPorRol('PSICOLOGO')">
          <q-card-section class="q-pa-xl text-center">
            <q-icon name="psychology" size="xl" color="green" class="q-mb-md" />
            <div class="text-h4 text-weight-bold text-positive">{{ getCountByRol('PSICOLOGO') }}</div>
            <div class="text-h6 text-grey-7">Psicólogos</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="summary-card" flat bordered @click="filtrarPorRol('TUTOR')">
          <q-card-section class="q-pa-xl text-center">
            <q-icon name="person" size="xl" color="orange" class="q-mb-md" />
            <div class="text-h4 text-weight-bold text-orange">{{ getCountByRol('TUTOR') }}</div>
            <div class="text-h6 text-grey-7">Tutores</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="summary-card" flat bordered @click="filtrarPorEstado(true)">
          <q-card-section class="q-pa-xl text-center">
            <q-icon name="check_circle" size="xl" color="positive" class="q-mb-md" />
            <div class="text-h4 text-weight-bold text-positive">{{ usuariosActivos }}</div>
            <div class="text-h6 text-grey-7">Usuarios Activos</div>
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
              label="Buscar por nombre o email"
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
              v-model="filters.rol"
              :options="ROLES_OPTIONS"
              label="Filtrar por Rol"
              clearable
              @update:model-value="onFilterChange"
              outlined
              color="primary"
            />
          </div>
          <div class="col-12 col-sm-3">
            <q-select
              v-model="filters.estado"
              :options="ESTADOS_OPTIONS"
              label="Filtrar por Estado"
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
              @click="loadUsuarios"
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
              Lista de Usuarios
              <q-badge v-if="usuarios.length" color="primary" class="q-ml-md" style="font-size: 16px;">
                {{ usuarios.length }}
              </q-badge>
            </div>
            <div class="text-caption text-grey-6">
              Gestión completa de usuarios y permisos del sistema
            </div>
          </div>
          <div class="col-auto">
            <q-btn
              flat
              round
              icon="refresh"
              @click="loadUsuarios"
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
          <div class="text-h6 q-mt-md text-grey-6">Cargando usuarios...</div>
        </div>

        <!-- Estado Vacío -->
        <div v-else-if="usuarios.length === 0" class="text-center q-pa-xl">
          <q-icon name="people" size="xl" color="grey-4" />
          <div class="text-h5 q-mt-md text-grey-6">No se encontraron usuarios</div>
          <div class="text-grey-6 q-mb-lg">No hay usuarios que coincidan con los filtros aplicados</div>
          <q-btn
            v-if="isTOE"
            color="primary"
            icon="add"
            label="Registrar Primer Usuario"
            @click="nuevoUsuario"
            unelevated
            size="lg"
          />
        </div>

        <!-- Lista de Usuarios -->
        <div v-else class="q-gutter-y-md">
          <q-card
            v-for="usuario in usuarios"
            :key="usuario.id"
            flat
            bordered
            class="usuario-card"
          >
            <q-card-section class="q-pa-lg">
              <div class="row items-center">
                <div class="col-auto q-mr-lg">
                  <q-avatar
                    size="60px"
                    color="primary"
                    text-color="white"
                    class="shadow-2"
                  >
                    {{ usuario.name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2) }}
                  </q-avatar>
                </div>

                <div class="col">
                  <div class="row items-center q-mb-xs">
                    <div class="col">
                      <div class="text-h6 text-weight-medium">
                        {{ usuario.name }}
                      </div>
                    </div>
                    <div class="col-auto">
                      <div class="row items-center q-gutter-sm">
                        <q-badge
                          :color="usuario.estado ? 'positive' : 'negative'"
                          class="text-capitalize q-px-md q-py-sm"
                          style="font-size: 12px;"
                        >
                          {{ usuario.estado ? 'Activo' : 'Inactivo' }}
                        </q-badge>
                        <q-badge
                          color="primary"
                          class="text-capitalize q-px-md q-py-sm"
                          style="font-size: 12px;"
                        >
                          {{ usuario.rol.toLowerCase() }}
                        </q-badge>
                      </div>
                    </div>
                  </div>

                  <div class="row items-center">
                    <div class="col">
                      <div class="text-caption text-grey-7 q-mb-xs">
                        <q-icon name="email" size="16px" class="q-mr-xs" />
                        {{ usuario.email }}
                      </div>
                      <div class="text-caption text-grey-6">
                        <q-icon name="calendar_today" size="16px" class="q-mr-xs" />
                        Registrado: {{ formatDate(usuario.created_at) }}
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
                          @click="verUsuario(usuario)"
                          class="action-btn"
                        >
                          <q-tooltip>Ver detalles</q-tooltip>
                        </q-btn>

                        <q-btn
                          v-if="isTOE"
                          flat
                          round
                          icon="edit"
                          color="primary"
                          size="sm"
                          @click="editarUsuario(usuario)"
                          class="action-btn"
                        >
                          <q-tooltip>Editar usuario</q-tooltip>
                        </q-btn>

                        <q-btn
                          v-if="isTOE && usuario.id !== user?.id"
                          flat
                          round
                          :icon="usuario.estado ? 'toggle_off' : 'toggle_on'"
                          :color="usuario.estado ? 'orange' : 'green'"
                          size="sm"
                          @click="toggleEstado(usuario)"
                          class="action-btn"
                        >
                          <q-tooltip>
                            {{ usuario.estado ? 'Desactivar' : 'Activar' }} usuario
                          </q-tooltip>
                        </q-btn>

                        <q-btn
                          v-if="isTOE && usuario.id !== user?.id"
                          flat
                          round
                          icon="delete"
                          color="negative"
                          size="sm"
                          @click="eliminarUsuario(usuario)"
                          class="action-btn"
                        >
                          <q-tooltip>Eliminar usuario</q-tooltip>
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
          <span class="q-ml-md text-h6">¿Eliminar usuario?</span>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <div class="text-h6 text-weight-medium q-mb-xs">
            {{ usuarioSeleccionado?.name }}
          </div>
          <div class="text-grey-6">
            {{ usuarioSeleccionado?.email }} - {{ usuarioSeleccionado?.rol?.toLowerCase() }}
          </div>
          <div class="text-negative q-mt-md">
            <q-icon name="info" size="sm" class="q-mr-xs" />
            Esta acción no se puede deshacer
          </div>
        </q-card-section>

        <q-card-actions align="right" class="q-pa-md">
          <q-btn flat label="Cancelar" color="grey" v-close-popup />
          <q-btn
            label="Eliminar Usuario"
            color="negative"
            @click="confirmarEliminar"
            :loading="loadingEliminar"
            unelevated
          />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Dialog de Confirmación para Cambiar Estado -->
    <q-dialog v-model="dialogToggleEstado" persistent>
      <q-card class="confirm-dialog">
        <q-card-section class="row items-center">
          <q-avatar
            :icon="usuarioSeleccionado?.estado ? 'toggle_off' : 'toggle_on'"
            :color="usuarioSeleccionado?.estado ? 'orange' : 'green'"
            text-color="white"
            size="lg"
          />
          <span class="q-ml-md text-h6">
            {{ usuarioSeleccionado?.estado ? 'Desactivar' : 'Activar' }} usuario?
          </span>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <div class="text-h6 text-weight-medium q-mb-xs">
            {{ usuarioSeleccionado?.name }}
          </div>
          <div class="text-grey-6">
            {{ usuarioSeleccionado?.email }}
          </div>
          <div :class="`text-${usuarioSeleccionado?.estado ? 'orange' : 'positive'} q-mt-md`">
            <q-icon name="info" size="sm" class="q-mr-xs" />
            {{ usuarioSeleccionado?.estado ?
              'El usuario no podrá acceder al sistema' :
              'El usuario podrá acceder al sistema nuevamente'
            }}
          </div>
        </q-card-section>

        <q-card-actions align="right" class="q-pa-md">
          <q-btn flat label="Cancelar" color="grey" v-close-popup />
          <q-btn
            :label="usuarioSeleccionado?.estado ? 'Desactivar' : 'Activar'"
            :color="usuarioSeleccionado?.estado ? 'orange' : 'green'"
            @click="confirmarToggleEstado"
            :loading="loadingToggleEstado"
            unelevated
          />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Dialog de Formulario de Usuario -->
    <q-dialog v-model="dialogFormUsuario" persistent maximized>
      <q-card style="width: 800px; max-width: 95vw;" class="form-dialog">
        <q-card-section class="row items-center q-pb-none bg-primary text-white">
          <div class="text-h5">{{ esEdicionUsuario ? 'Editar Usuario' : 'Nuevo Usuario' }}</div>
          <q-space />
          <q-btn flat round dense icon="close" v-close-popup class="text-white" />
        </q-card-section>

        <q-card-section class="q-pt-none">
          <UsuarioForm
            :usuario="usuarioSeleccionado"
            :es-edicion="esEdicionUsuario"
            @guardado="onUsuarioGuardado"
            @cancelado="dialogFormUsuario = false"
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
import { userService } from 'src/services/api'
import { formatDate } from 'src/utils/helpers'
import UsuarioForm from 'src/components/UsuarioForm.vue'

const $q = useQuasar()
const authStore = useAuthStore()

const loading = ref(false)
const loadingEliminar = ref(false)
const loadingToggleEstado = ref(false)
const usuarios = ref([])
const dialogEliminar = ref(false)
const dialogToggleEstado = ref(false)
const dialogFormUsuario = ref(false)
const usuarioSeleccionado = ref(null)
const esEdicionUsuario = ref(false)

const filters = ref({
  search: '',
  rol: '',
  estado: ''
})

// Computed
const isTOE = computed(() => authStore.isTOE)
const user = computed(() => authStore.getUser)

const ROLES_OPTIONS = [
  { label: 'TOE', value: 'TOE' },
  { label: 'Psicólogo', value: 'PSICOLOGO' },
  { label: 'Tutor', value: 'TUTOR' },
  { label: 'Director', value: 'DIRECTOR' }
]

const ESTADOS_OPTIONS = [
  { label: 'Activo', value: true },
  { label: 'Inactivo', value: false }
]

const totalUsuarios = computed(() => usuarios.value.length)
const usuariosActivos = computed(() => usuarios.value.filter(u => u.estado).length)

// Methods
async function loadUsuarios() {
  loading.value = true
  try {
    const params = {}
    if (filters.value.search) {
      params.search = filters.value.search
    }
    if (filters.value.rol) {
      params.rol = filters.value.rol
    }
    if (filters.value.estado !== '') {
      params.estado = filters.value.estado
    }

    const response = await userService.getUsers(params)
    usuarios.value = response.data.users
  } catch (error) {
    console.error('Error cargando usuarios:', error)
    $q.notify({
      type: 'negative',
      message: 'Error cargando la lista de usuarios',
      position: 'top'
    })
  } finally {
    loading.value = false
  }
}

function onFilterChange() {
  clearTimeout(window.filterTimeout)
  window.filterTimeout = setTimeout(() => {
    loadUsuarios()
  }, 800)
}

function getCountByRol(rol) {
  return usuarios.value.filter(u => u.rol === rol).length
}

function filtrarPorRol(rol) {
  filters.value.rol = rol
  loadUsuarios()
}

function filtrarPorEstado(estado) {
  filters.value.estado = estado
  loadUsuarios()
}

function nuevoUsuario() {
  usuarioSeleccionado.value = null
  esEdicionUsuario.value = false
  dialogFormUsuario.value = true
}

function verUsuario(usuario) {
  $q.notify({
    message: `Usuario: ${usuario.name}`,
    caption: `Rol: ${usuario.rol}`,
    color: 'info',
    icon: 'person',
    position: 'top'
  })
}

function editarUsuario(usuario) {
  usuarioSeleccionado.value = { ...usuario }
  esEdicionUsuario.value = true
  dialogFormUsuario.value = true
}

function toggleEstado(usuario) {
  if (usuario.id === user.value?.id) {
    $q.notify({
      type: 'warning',
      message: 'No puedes cambiar el estado de tu propia cuenta',
      position: 'top'
    })
    return
  }

  usuarioSeleccionado.value = usuario
  dialogToggleEstado.value = true
}

async function confirmarToggleEstado() {
  if (!usuarioSeleccionado.value) return

  loadingToggleEstado.value = true
  try {
    await userService.toggleEstado(usuarioSeleccionado.value.id)

    $q.notify({
      type: 'positive',
      message: `Usuario ${usuarioSeleccionado.value.estado ? 'desactivado' : 'activado'} exitosamente`,
      position: 'top'
    })

    await loadUsuarios()
    dialogToggleEstado.value = false
    usuarioSeleccionado.value = null

  } catch (error) {
    console.error('Error cambiando estado:', error)
    $q.notify({
      type: 'negative',
      message: error.response?.data?.message || 'Error cambiando estado del usuario',
      position: 'top'
    })
  } finally {
    loadingToggleEstado.value = false
  }
}

function eliminarUsuario(usuario) {
  if (usuario.id === user.value?.id) {
    $q.notify({
      type: 'warning',
      message: 'No puedes eliminar tu propia cuenta',
      position: 'top'
    })
    return
  }

  usuarioSeleccionado.value = usuario
  dialogEliminar.value = true
}

async function confirmarEliminar() {
  if (!usuarioSeleccionado.value) return

  loadingEliminar.value = true
  try {
    await userService.deleteUser(usuarioSeleccionado.value.id)

    $q.notify({
      type: 'positive',
      message: 'Usuario eliminado exitosamente',
      position: 'top'
    })

    await loadUsuarios()
    dialogEliminar.value = false
    usuarioSeleccionado.value = null

  } catch (error) {
    console.error('Error eliminando usuario:', error)
    $q.notify({
      type: 'negative',
      message: error.response?.data?.message || 'Error eliminando usuario',
      position: 'top'
    })
  } finally {
    loadingEliminar.value = false
  }
}

function onUsuarioGuardado() {
  dialogFormUsuario.value = false
  loadUsuarios()
}

// Lifecycle
onMounted(() => {
  loadUsuarios()
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

.usuario-card {
  transition: all 0.3s ease;
  border-radius: 12px;
}

.usuario-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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

<template>
  <q-layout view="lHh Lpr lFf" class="bg-grey-1">
    <q-header elevated class="bg-gradient-primary text-white shadow-4">
      <q-toolbar class="q-py-sm">
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="toggleLeftDrawer"
          class="q-mr-sm"
        />

        <q-toolbar-title class="row items-center no-wrap">
          <q-icon name="psychology" size="28px" class="q-mr-sm" />
          <div>
            <div class="text-h6 text-weight-bold">Sistema de Psicología Escolar</div>
            <div class="text-caption opacity-60">Plataforma de gestión integral</div>
          </div>
        </q-toolbar-title>

        <q-space />

        <!-- Notificaciones -->
        <!-- Notificaciones -->
        <q-btn flat round icon="notifications" class="q-mr-sm" @click="toggleNotificaciones">
          <q-badge v-if="totalNoLeidas > 0" color="red" floating rounded class="notification-badge">
            {{ totalNoLeidas > 99 ? '99+' : totalNoLeidas }}
          </q-badge>
          <q-tooltip>Notificaciones</q-tooltip>
        </q-btn>

        <!-- Usuario - CORREGIDO -->
        <q-btn-dropdown
          flat
          no-icon-animation
          class="user-dropdown"
          :label="user?.name"
          :loading="loadingUserMenu"
        >
          <template v-slot:label>
            <div class="row items-center no-wrap">
              <q-avatar size="32px" class="q-mr-sm bg-white text-primary">
                {{ userInitials }}
              </q-avatar>
              <div class="text-capitalize text-caption">
                {{ user?.rol?.toLowerCase() }}
              </div>
            </div>
          </template>

          <!-- Menú Desplegable -->
          <q-list class="user-menu-list">
            <!-- Información del Usuario -->
            <q-item class="user-info-section">
              <q-item-section>
                <q-item-label class="text-weight-bold">{{ user?.name }}</q-item-label>
                <q-item-label caption class="text-grey-7">{{ user?.email }}</q-item-label>
                <q-item-label>
                  <q-chip size="sm" color="primary" text-color="white" class="text-capitalize">
                    {{ user?.rol?.toLowerCase() }}
                  </q-chip>
                </q-item-label>
              </q-item-section>
            </q-item>

            <q-separator />

            <!-- Cambiar Contraseña -->
            <q-item clickable v-ripple @click="cambiarPassword">
              <q-item-section avatar>
                <q-icon name="lock" color="primary" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Cambiar Contraseña</q-item-label>
              </q-item-section>
            </q-item>

            <q-separator />

            <!-- Cerrar Sesión -->
            <q-item clickable v-ripple @click="logout" class="text-negative">
              <q-item-section avatar>
                <q-icon name="logout" color="negative" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Cerrar Sesión</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      class="bg-white text-grey-9"
      :width="280"
    >
      <q-scroll-area class="fit">
        <q-list padding class="menu-list">
          <q-item-label header class="text-weight-bold text-grey-8 q-pb-md">
            Navegación Principal
          </q-item-label>

          <q-item
            clickable
            v-ripple
            to="/"
            exact
            class="menu-item q-mb-xs"
            active-class="menu-item-active"
          >
            <q-item-section avatar>
              <q-icon name="dashboard" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Dashboard</q-item-label>
              <q-item-label caption>Resumen del sistema</q-item-label>
            </q-item-section>
          </q-item>

          <q-item
            clickable
            v-ripple
            to="/estudiantes"
            class="menu-item q-mb-xs"
            active-class="menu-item-active"
          >
            <q-item-section avatar>
              <q-icon name="school" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Estudiantes</q-item-label>
              <q-item-label caption>Gestión de estudiantes</q-item-label>
            </q-item-section>
          </q-item>

          <q-item
            clickable
            v-ripple
            to="/apoderados"
            class="menu-item q-mb-xs"
            active-class="menu-item-active"
          >
            <q-item-section avatar>
              <q-icon name="family_restroom" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Apoderados</q-item-label>
              <q-item-label caption>Gestión de apoderados</q-item-label>
            </q-item-section>
          </q-item>

          <!-- Menú Psicología -->
          <template v-if="isPsicologo || isTOE">
            <q-separator class="q-my-md" />

            <q-item-label header class="text-weight-bold text-grey-8 q-pb-md">
              Módulo Psicología
            </q-item-label>

            <q-item
              clickable
              v-ripple
              to="/diagnosticos"
              class="menu-item q-mb-xs"
              active-class="menu-item-active"
            >
              <q-item-section avatar>
                <q-icon name="clinical_notes" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Diagnósticos</q-item-label>
                <q-item-label caption>Evaluaciones psicológicas</q-item-label>
              </q-item-section>
            </q-item>

            <q-item
              clickable
              v-ripple
              to="/citas"
              class="menu-item q-mb-xs"
              active-class="menu-item-active"
            >
              <q-item-section avatar>
                <q-icon name="event" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Citas</q-item-label>
                <q-item-label caption>Agenda de consultas</q-item-label>
              </q-item-section>
            </q-item>
          </template>

          <!-- Menú Administración -->
          <template v-if="isTOE || isDirector">
            <q-separator class="q-my-md" />

            <q-item-label header class="text-weight-bold text-grey-8 q-pb-md">
              Administración
            </q-item-label>

            <q-item
              v-if="isTOE"
              clickable
              v-ripple
              to="/usuarios"
              class="menu-item q-mb-xs"
              active-class="menu-item-active"
            >
              <q-item-section avatar>
                <q-icon name="people" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Usuarios</q-item-label>
                <q-item-label caption>Gestión de usuarios</q-item-label>
              </q-item-section>
            </q-item>

            <q-item
              clickable
              v-ripple
              to="/reportes"
              class="menu-item q-mb-xs"
              active-class="menu-item-active"
            >
              <q-item-section avatar>
                <q-icon name="assessment" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Reportes</q-item-label>
                <q-item-label caption>Estadísticas y reportes</q-item-label>
              </q-item-section>
            </q-item>
          </template>
        </q-list>
      </q-scroll-area>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>

    <!-- Dialog de Notificaciones -->
    <q-dialog v-model="dialogNotificaciones" position="top">
      <q-card class="notifications-card">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Notificaciones</div>
          <q-space />
          <q-btn flat round dense icon="close" v-close-popup />
          <q-btn
            flat
            round
            dense
            icon="done_all"
            @click="marcarTodasLeidas"
            v-if="totalNoLeidas > 0"
            class="q-ml-xs"
          >
            <q-tooltip>Marcar todas como leídas</q-tooltip>
          </q-btn>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <div v-if="notificaciones.length === 0" class="text-center q-pa-xl">
            <q-icon name="notifications_off" size="xl" color="grey-4" />
            <div class="text-grey q-mt-md text-h6">No hay notificaciones</div>
            <div class="text-grey-6">Cuando tengas notificaciones, aparecerán aquí</div>
          </div>

          <q-scroll-area v-else style="height: 400px">
            <q-list bordered separator class="rounded-borders">
              <q-item
                v-for="notif in notificaciones"
                :key="notif.id"
                clickable
                :class="{ 'bg-blue-1': !notif.leida }"
                @click="verNotificacion(notif)"
                class="notification-item"
              >
                <q-item-section avatar>
                  <q-avatar
                    :color="notif.leida ? 'grey-3' : 'primary'"
                    text-color="white"
                    size="lg"
                  >
                    <q-icon :name="notif.leida ? 'mark_email_read' : 'mark_unread'" size="20px" />
                  </q-avatar>
                </q-item-section>
                <q-item-section>
                  <q-item-label class="text-caption text-grey-7">
                    De: {{ notif.remitente?.name }}
                  </q-item-label>
                  <q-item-label class="text-weight-medium">{{ notif.mensaje }}</q-item-label>
                  <q-item-label caption class="text-grey-6">
                    {{ formatDateTime(notif.fecha_envio) }}
                  </q-item-label>
                </q-item-section>
                <q-item-section side top class="gt-xs">
                  <q-btn
                    flat
                    round
                    dense
                    icon="close"
                    size="sm"
                    color="grey-6"
                    @click.stop="eliminarNotificacion(notif.id)"
                  />
                </q-item-section>
              </q-item>
            </q-list>
          </q-scroll-area>
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- Dialog Cambiar Contraseña -->
    <q-dialog v-model="dialogCambiarPassword" persistent>
      <q-card class="password-card">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Cambiar Contraseña</div>
          <q-space />
          <q-btn flat round dense icon="close" v-close-popup />
        </q-card-section>

        <q-card-section class="q-pt-none">
          <q-form @submit="submitCambiarPassword" class="q-gutter-md">
            <q-input
              v-model="passwordForm.password_actual"
              label="Contraseña Actual"
              :type="showPassword ? 'text' : 'password'"
              required
              outlined
              color="primary"
            >
              <template v-slot:prepend>
                <q-icon name="lock" />
              </template>
              <template v-slot:append>
                <q-icon
                  :name="showPassword ? 'visibility_off' : 'visibility'"
                  class="cursor-pointer"
                  @click="showPassword = !showPassword"
                />
              </template>
            </q-input>

            <q-input
              v-model="passwordForm.password_nuevo"
              label="Nueva Contraseña"
              :type="showPassword ? 'text' : 'password'"
              required
              outlined
              color="primary"
            >
              <template v-slot:prepend>
                <q-icon name="password" />
              </template>
            </q-input>

            <q-input
              v-model="passwordForm.password_nuevo_confirmation"
              label="Confirmar Nueva Contraseña"
              :type="showPassword ? 'text' : 'password'"
              required
              outlined
              color="primary"
            >
              <template v-slot:prepend>
                <q-icon name="check_circle" />
              </template>
            </q-input>
          </q-form>
        </q-card-section>

        <q-card-actions align="right" class="q-pa-md">
          <q-btn flat label="Cancelar" color="grey" v-close-popup />
          <q-btn
            label="Cambiar Contraseña"
            color="primary"
            @click="submitCambiarPassword"
            :loading="loadingPassword"
            unelevated
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-layout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useAuthStore } from 'src/stores/auth-store'
import { useNotificacionStore } from 'src/stores/notificacion-store'
import { userService } from 'src/services/api'
import { formatDateTime } from 'src/utils/helpers'

const $q = useQuasar()
const router = useRouter()
const authStore = useAuthStore()
const notificacionStore = useNotificacionStore()

const leftDrawerOpen = ref(false)
const dialogNotificaciones = ref(false)
const dialogCambiarPassword = ref(false)
const showPassword = ref(false)
const loadingPassword = ref(false)
const loadingUserMenu = ref(false)

const passwordForm = ref({
  password_actual: '',
  password_nuevo: '',
  password_nuevo_confirmation: ''
})

// Computed
const user = computed(() => authStore.getUser)
const userInitials = computed(() => {
  if (!user.value?.name) return 'U'
  return user.value.name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
    .substring(0, 2)
})

const isTOE = computed(() => authStore.isTOE)
const isPsicologo = computed(() => authStore.isPsicologo)
const isDirector = computed(() => authStore.isDirector)

const notificaciones = computed(() => notificacionStore.getNotificaciones)
const totalNoLeidas = computed(() => notificacionStore.getTotalNoLeidas)

// Methods
function toggleLeftDrawer() {
  leftDrawerOpen.value = !leftDrawerOpen.value
}

function toggleNotificaciones() {
  dialogNotificaciones.value = true
  loadNotificaciones()
}

async function loadNotificaciones() {
  try {
    await notificacionStore.fetchNotificaciones()
    await notificacionStore.fetchNotificacionesNoLeidas()
  } catch (error) {
    console.error('Error cargando notificaciones:', error)
  }
}

async function verNotificacion(notif) {
  if (!notif.leida) {
    await notificacionStore.marcarComoLeida(notif.id)
  }
  dialogNotificaciones.value = false
  $q.notify({
    message: `Notificación: ${notif.mensaje}`,
    color: 'primary',
    icon: 'info'
  })
}

async function eliminarNotificacion(id) {
  try {
    await notificacionStore.eliminarNotificacion(id)
    $q.notify({
      type: 'positive',
      message: 'Notificación eliminada',
      position: 'top'
    })
  } catch {
    $q.notify({
      type: 'negative',
      message: 'Error eliminando notificación',
      position: 'top'
    })
  }
}

async function marcarTodasLeidas() {
  try {
    await notificacionStore.marcarTodasLeidas()
    $q.notify({
      type: 'positive',
      message: 'Todas las notificaciones marcadas como leídas',
      position: 'top'
    })
  } catch {
    $q.notify({
      type: 'negative',
      message: 'Error marcando notificaciones',
      position: 'top'
    })
  }
}

function cambiarPassword() {
  dialogCambiarPassword.value = true
  passwordForm.value = {
    password_actual: '',
    password_nuevo: '',
    password_nuevo_confirmation: ''
  }
}

async function submitCambiarPassword() {
  if (!user.value) {
    $q.notify({
      type: 'negative',
      message: 'No se pudo identificar al usuario'
    })
    return
  }

  loadingPassword.value = true
  try {
    await userService.cambiarPassword({
      user_id: user.value.id,
      ...passwordForm.value
    })

    $q.notify({
      type: 'positive',
      message: 'Contraseña cambiada exitosamente'
    })

    dialogCambiarPassword.value = false
    passwordForm.value = {
      password_actual: '',
      password_nuevo: '',
      password_nuevo_confirmation: ''
    }
  } catch (error) {
    console.error('Error cambiando contraseña:', error)
    $q.notify({
      type: 'negative',
      message: error.response?.data?.message || 'Error cambiando contraseña'
    })
  } finally {
    loadingPassword.value = false
  }
}

async function logout() {
  try {
    loadingUserMenu.value = true

    $q.dialog({
      title: 'Cerrar Sesión',
      message: '¿Estás seguro de que quieres cerrar sesión?',
      cancel: true,
      persistent: true,
      ok: {
        label: 'Sí, cerrar sesión',
        color: 'primary',
        unelevated: true
      },
    }).onOk(async () => {
      try {
        $q.loading.show({
          message: 'Cerrando sesión...'
        })

        await authStore.logout()

        $q.loading.hide()
        router.push('/login')

        $q.notify({
          type: 'positive',
          message: 'Sesión cerrada exitosamente',
          position: 'top',
          timeout: 2000
        })

      } catch (error) {
        $q.loading.hide()
        console.error('Error durante logout:', error)

        authStore.clearAuth()
        router.push('/login')

        $q.notify({
          type: 'info',
          message: 'Sesión cerrada',
          position: 'top'
        })
      }
    })
  } finally {
    loadingUserMenu.value = false
  }
}

// Lifecycle
onMounted(() => {
  loadNotificaciones()
})
</script>

<style scoped>
.bg-gradient-primary {
  background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
}

.menu-list {
  padding: 16px 8px;
}

.menu-item {
  border-radius: 8px;
  transition: all 0.3s ease;
  margin-bottom: 4px;
}

.menu-item:hover {
  background-color: #e3f2fd;
  transform: translateX(4px);
}

.menu-item-active {
  background-color: #e3f2fd !important;
  border-right: 4px solid #1976d2;
  color: #1976d2;
}

.menu-item-active .q-icon {
  color: #1976d2;
}

.user-avatar {
  border-radius: 50%;
  transition: all 0.3s ease;
}

.user-avatar:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.user-menu {
  border-radius: 12px;
  overflow: hidden;
}

.notifications-card {
  width: 450px;
  max-width: 90vw;
  border-radius: 12px;
}

.notification-item {
  border-radius: 8px;
  margin: 4px 0;
  transition: all 0.3s ease;
}

.notification-item:hover {
  background-color: #f5f5f5;
  transform: translateY(-1px);
}

.notification-badge {
  font-size: 10px;
  padding: 2px 4px;
  min-width: 18px;
  height: 18px;
}

.password-card {
  width: 400px;
  max-width: 90vw;
  border-radius: 12px;
}

.opacity-60 {
  opacity: 0.6;
}

.opacity-80 {
  opacity: 0.8;
}
</style>

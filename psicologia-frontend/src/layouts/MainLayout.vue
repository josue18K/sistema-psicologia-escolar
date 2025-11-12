<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated class="bg-primary text-white">
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="toggleLeftDrawer"
        />

        <q-toolbar-title>
          <q-icon name="psychology" class="q-mr-sm" />
          Sistema de Psicología Escolar
        </q-toolbar-title>

        <div class="q-gutter-sm row items-center">
          <!-- Notificaciones -->
          <q-btn flat round icon="notifications" @click="toggleNotificaciones">
            <q-badge
              v-if="totalNoLeidas > 0"
              color="red"
              floating
              rounded
            >
              {{ totalNoLeidas }}
            </q-badge>
            <q-tooltip>Notificaciones</q-tooltip>
          </q-btn>

          <!-- Usuario -->
          <q-btn flat round icon="person">
            <q-menu auto-close>
              <q-list style="min-width: 200px">
                <q-item>
                  <q-item-section avatar>
                    <q-avatar color="primary" text-color="white">
                      {{ userInitials }}
                    </q-avatar>
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>{{ user?.name }}</q-item-label>
                    <q-item-label caption>{{ user?.email }}</q-item-label>
                    <q-item-label caption class="text-capitalize">
                      {{ user?.rol?.toLowerCase() }}
                    </q-item-label>
                  </q-item-section>
                </q-item>

                <q-separator />

                <q-item clickable @click="cambiarPassword">
                  <q-item-section avatar>
                    <q-icon name="lock" />
                  </q-item-section>
                  <q-item-section>Cambiar Contraseña</q-item-section>
                </q-item>

                <q-separator />

                <q-item clickable @click="logout">
                  <q-item-section avatar>
                    <q-icon name="logout" />
                  </q-item-section>
                  <q-item-section>Cerrar Sesión</q-item-section>
                </q-item>
              </q-list>
            </q-menu>
            <q-tooltip>Perfil de usuario</q-tooltip>
          </q-btn>
        </div>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      class="bg-grey-2"
    >
      <q-list padding>
        <q-item-label header class="text-weight-bold">
          Navegación Principal
        </q-item-label>

        <EssentialLink
          v-for="link in essentialLinks"
          :key="link.title"
          v-bind="link"
        />

        <q-separator class="q-my-md" />

        <!-- Menú según rol -->
        <template v-if="isTOE || isDirector">
          <q-item-label header class="text-weight-bold">
            Administración
          </q-item-label>

          <EssentialLink
            v-if="isTOE"
            icon="people"
            title="Gestión de Usuarios"
            caption="Administrar usuarios del sistema"
            link="/usuarios"
          />

          <EssentialLink
            icon="assessment"
            title="Reportes Globales"
            caption="Estadísticas y reportes"
            link="/reportes"
          />
        </template>

        <template v-if="isPsicologo || isTOE">
          <q-item-label header class="text-weight-bold">
            Psicología
          </q-item-label>

          <EssentialLink
            icon="event"
            title="Gestión de Citas"
            caption="Agendar y gestionar citas"
            link="/citas"
          />

          <EssentialLink
            icon="clinical_notes"
            title="Diagnósticos"
            caption="Registrar y consultar diagnósticos"
            link="/diagnosticos"
          />
        </template>
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>

    <!-- Dialog de Notificaciones -->
    <q-dialog v-model="dialogNotificaciones" position="top">
      <q-card style="width: 400px; max-width: 80vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Notificaciones</div>
          <q-space />
          <q-btn
            flat
            round
            dense
            icon="close"
            v-close-popup
          />
          <q-btn
            flat
            round
            dense
            icon="done_all"
            @click="marcarTodasLeidas"
            v-if="totalNoLeidas > 0"
          >
            <q-tooltip>Marcar todas como leídas</q-tooltip>
          </q-btn>
        </q-card-section>

        <q-card-section>
          <div v-if="notificaciones.length === 0" class="text-center q-pa-md">
            <q-icon name="notifications_off" size="xl" color="grey" />
            <div class="text-grey q-mt-sm">No hay notificaciones</div>
          </div>

          <q-list bordered separator v-else>
            <q-item
              v-for="notif in notificaciones"
              :key="notif.id"
              clickable
              :class="{ 'bg-blue-1': !notif.leida }"
              @click="verNotificacion(notif)"
            >
              <q-item-section avatar>
                <q-icon
                  :name="notif.leida ? 'mark_email_read' : 'mark_unread'"
                  :color="notif.leida ? 'grey' : 'primary'"
                />
              </q-item-section>
              <q-item-section>
                <q-item-label class="text-caption text-grey">
                  De: {{ notif.remitente?.name }}
                </q-item-label>
                <q-item-label>{{ notif.mensaje }}</q-item-label>
                <q-item-label caption>
                  {{ formatDateTime(notif.fecha_envio) }}
                </q-item-label>
              </q-item-section>
              <q-item-section side top>
                <q-btn
                  flat
                  round
                  dense
                  icon="close"
                  size="sm"
                  @click.stop="eliminarNotificacion(notif.id)"
                />
              </q-item-section>
            </q-item>
          </q-list>
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- Dialog Cambiar Contraseña -->
    <q-dialog v-model="dialogCambiarPassword" persistent>
      <q-card style="min-width: 400px">
        <q-card-section>
          <div class="text-h6">Cambiar Contraseña</div>
        </q-card-section>

        <q-card-section class="q-pt-none">
          <q-form @submit="submitCambiarPassword" class="q-gutter-md">
            <q-input
              v-model="passwordForm.password_actual"
              label="Contraseña Actual"
              :type="showPassword ? 'text' : 'password'"
              required
            >
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
            />

            <q-input
              v-model="passwordForm.password_nuevo_confirmation"
              label="Confirmar Nueva Contraseña"
              :type="showPassword ? 'text' : 'password'"
              required
            />
          </q-form>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancelar" color="primary" v-close-popup />
          <q-btn
            label="Cambiar"
            color="primary"
            @click="submitCambiarPassword"
            :loading="loadingPassword"
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
import EssentialLink from 'components/EssentialLink.vue'

const $q = useQuasar()
const router = useRouter()
const authStore = useAuthStore()
const notificacionStore = useNotificacionStore()

const leftDrawerOpen = ref(false)
const dialogNotificaciones = ref(false)
const dialogCambiarPassword = ref(false)
const showPassword = ref(false)
const loadingPassword = ref(false)

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

// Links esenciales según rol
const essentialLinks = computed(() => {
  const baseLinks = [
    {
      icon: 'dashboard',
      title: 'Dashboard',
      caption: 'Resumen del sistema',
      link: '/'
    },
    {
      icon: 'school',
      title: 'Estudiantes',
      caption: 'Gestión de estudiantes',
      link: '/estudiantes'
    },
    {
      icon: 'family_restroom',
      title: 'Apoderados',
      caption: 'Gestión de apoderados',
      link: '/apoderados'
    }
  ]

  return baseLinks
})

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
  // Aquí podrías redirigir a la acción correspondiente
  dialogNotificaciones.value = false
}

async function eliminarNotificacion(id) {
  try {
    await notificacionStore.eliminarNotificacion(id)
    $q.notify({
      type: 'positive',
      message: 'Notificación eliminada'
    })
  } catch {
    $q.notify({
      type: 'negative',
      message: 'Error eliminando notificación'
    })
  }
}

async function marcarTodasLeidas() {
  try {
    await notificacionStore.marcarTodasLeidas()
    $q.notify({
      type: 'positive',
      message: 'Todas las notificaciones marcadas como leídas'
    })
  } catch {
    $q.notify({
      type: 'negative',
      message: 'Error marcando notificaciones'
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
  loadingPassword.value = true
  try {
    await userService.cambiarPassword({
      user_id: user.value.id, // Temporal - cambiar por auth cuando esté listo
      ...passwordForm.value
    })

    $q.notify({
      type: 'positive',
      message: 'Contraseña cambiada exitosamente'
    })

    dialogCambiarPassword.value = false
  } catch (error) {
    $q.notify({
      type: 'negative',
      message: error.response?.data?.message || 'Error cambiando contraseña'
    })
  } finally {
    loadingPassword.value = false
  }
}

async function logout() {
  $q.dialog({
    title: 'Cerrar Sesión',
    message: '¿Estás seguro de que quieres cerrar sesión?',
    cancel: true,
    persistent: true
  }).onOk(async () => {
    await authStore.logout()
    router.push('/login')
  })
}

// Lifecycle
onMounted(() => {
  loadNotificaciones()
})
</script>

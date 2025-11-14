<template>
  <q-layout view="hHh lpR fFf">
    <!-- Header -->
    <q-header elevated class="bg-primary text-white">
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          icon="menu"
          @click="toggleLeftDrawer"
        />

        <q-toolbar-title>
          Sistema de Gestión Psicológica
        </q-toolbar-title>

        <!-- Badge notificaciones -->
        <q-btn
          flat
          dense
          round
          icon="notifications"
          @click="goToNotifications"
        >
          <q-badge
            v-if="unreadCount > 0"
            color="red"
            floating
          >
            {{ unreadCount }}
          </q-badge>
        </q-btn>

        <!-- Usuario dropdown -->
        <q-btn-dropdown
          flat
          dense
          :label="authStore.user?.name"
          icon="account_circle"
        >
          <q-list>
            <q-item>
              <q-item-section>
                <q-item-label caption>Rol</q-item-label>
                <q-item-label>{{ authStore.user?.rol }}</q-item-label>
              </q-item-section>
            </q-item>

            <q-separator />

            <q-item clickable v-close-popup @click="handleChangePassword">
              <q-item-section avatar>
                <q-icon name="lock" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Cambiar contraseña</q-item-label>
              </q-item-section>
            </q-item>

            <q-item clickable v-close-popup @click="handleLogout">
              <q-item-section avatar>
                <q-icon name="logout" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Cerrar sesión</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>
      </q-toolbar>
    </q-header>

    <!-- Drawer (Sidebar) -->
    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      :width="250"
      :breakpoint="1024"
    >
      <q-scroll-area class="fit">
        <q-list padding>
          <!-- Menú según rol -->
          <template v-for="item in menuItems" :key="item.name">
            <q-item
              v-if="!item.children"
              clickable
              :to="item.to"
              active-class="bg-primary text-white"
            >
              <q-item-section avatar>
                <q-icon :name="item.icon" />
              </q-item-section>
              <q-item-section>
                <q-item-label>{{ item.label }}</q-item-label>
              </q-item-section>
            </q-item>

            <!-- Menú con submenús -->
            <q-expansion-item
              v-else
              :icon="item.icon"
              :label="item.label"
              :default-opened="item.defaultOpened"
            >
              <q-item
                v-for="child in item.children"
                :key="child.name"
                clickable
                :to="child.to"
                active-class="bg-primary text-white"
                class="q-pl-lg"
              >
                <q-item-section avatar>
                  <q-icon :name="child.icon" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>{{ child.label }}</q-item-label>
                </q-item-section>
              </q-item>
            </q-expansion-item>
          </template>
        </q-list>
      </q-scroll-area>
    </q-drawer>

    <!-- Contenido de las páginas -->
    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from 'stores/auth'
import { useNotificacionesStore } from 'stores/notificaciones'
import { useQuasar } from 'quasar'

const router = useRouter()
const authStore = useAuthStore()
const notificacionesStore = useNotificacionesStore()
const $q = useQuasar()

const leftDrawerOpen = ref(false)
const unreadCount = computed(() => notificacionesStore.unreadCount)

const toggleLeftDrawer = () => {
  leftDrawerOpen.value = !leftDrawerOpen.value
}

// Menú dinámico según rol
const menuItems = computed(() => {
  const role = authStore.userRole

  const menus = {
    TOE: [
      { name: 'dashboard', label: 'Dashboard', icon: 'dashboard', to: '/' },
      { name: 'usuarios', label: 'Usuarios', icon: 'people', to: '/usuarios' },
      { name: 'estudiantes', label: 'Estudiantes', icon: 'school', to: '/estudiantes' },
      { name: 'apoderados', label: 'Apoderados', icon: 'supervisor_account', to: '/apoderados' },
      { name: 'notificaciones', label: 'Notificaciones', icon: 'notifications', to: '/notificaciones' }
    ],
    PSICOLOGO: [
      { name: 'dashboard', label: 'Dashboard', icon: 'dashboard', to: '/' },
      { name: 'estudiantes', label: 'Estudiantes', icon: 'school', to: '/estudiantes' },
      {
        name: 'evaluaciones',
        label: 'Evaluaciones',
        icon: 'assessment',
        defaultOpened: true,
        children: [
          { name: 'anamnesis', label: 'Anamnesis', icon: 'description', to: '/anamnesis' },
          { name: 'diagnosticos', label: 'Diagnósticos', icon: 'assignment', to: '/diagnosticos' }
        ]
      },
      { name: 'citas', label: 'Citas', icon: 'event', to: '/citas' },
      { name: 'reportes', label: 'Reportes', icon: 'bar_chart', to: '/reportes' },
      { name: 'notificaciones', label: 'Notificaciones', icon: 'notifications', to: '/notificaciones' }
    ],
    TUTOR: [
      { name: 'dashboard', label: 'Dashboard', icon: 'dashboard', to: '/' },
      { name: 'mis-estudiantes', label: 'Mis Estudiantes', icon: 'school', to: '/mis-estudiantes' },
      { name: 'notificaciones', label: 'Notificaciones', icon: 'notifications', to: '/notificaciones' }
    ],
    DIRECTOR: [
      { name: 'dashboard', label: 'Dashboard', icon: 'dashboard', to: '/' },
      { name: 'estadisticas', label: 'Estadísticas', icon: 'analytics', to: '/estadisticas' },
      { name: 'reportes', label: 'Reportes', icon: 'bar_chart', to: '/reportes' },
      { name: 'notificaciones', label: 'Notificaciones', icon: 'notifications', to: '/notificaciones' }
    ]
  }

  return menus[role] || []
})

const goToNotifications = () => {
  router.push('/notificaciones')
}

const handleChangePassword = () => {
  $q.dialog({
    title: 'Cambiar contraseña',
    message: 'Función en desarrollo',
    color: 'primary'
  })
}

const handleLogout = async () => {
  $q.dialog({
    title: 'Cerrar sesión',
    message: '¿Estás seguro de cerrar sesión?',
    cancel: true,
    persistent: true
  }).onOk(async () => {
    await authStore.logout()
    router.push('/login')
  })
}

onMounted(() => {
  // Cargar notificaciones no leídas
  notificacionesStore.fetchUnread()
})
</script>

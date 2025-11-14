const routes = [
  // Ruta pública - Login
  {
    path: '/login',
    name: 'login',
    component: () => import('pages/auth/LoginPage.vue'),
    meta: { requiresAuth: false }
  },

  // Layout principal (requiere autenticación)
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      // Dashboard según rol
      {
        path: '',
        name: 'dashboard',
        component: () => import('pages/IndexPage.vue')
      },

      // --- RUTAS PARA TOE ---
      {
        path: '/usuarios',
        name: 'usuarios',
        component: () => import('pages/toe/UsuariosPage.vue'),
        meta: { roles: ['TOE'] }
      },

      // --- RUTAS PARA PSICOLOGO ---
      {
        path: '/estudiantes',
        name: 'estudiantes',
        component: () => import('pages/estudiantes/EstudiantesPage.vue'),
        meta: { roles: ['TOE', 'PSICOLOGO'] }
      },
      {
        path: '/diagnosticos',
        name: 'diagnosticos',
        component: () => import('pages/diagnosticos/DiagnosticosPage.vue'),
        meta: { roles: ['PSICOLOGO'] }
      },
      {
        path: '/anamnesis',
        name: 'anamnesis',
        component: () => import('pages/anamnesis/AnamnesisPage.vue'),
        meta: { roles: ['PSICOLOGO'] }
      },
      {
        path: '/citas',
        name: 'citas',
        component: () => import('pages/citas/CitasPage.vue'),
        meta: { roles: ['PSICOLOGO'] }
      },
      {
        path: '/reportes',
        name: 'reportes',
        component: () => import('pages/reportes/ReportesPage.vue'),
        meta: { roles: ['PSICOLOGO', 'DIRECTOR'] }
      },

      // --- RUTAS PARA TUTOR ---
      {
        path: '/mis-estudiantes',
        name: 'mis-estudiantes',
        component: () => import('pages/tutor/MisEstudiantesPage.vue'),
        meta: { roles: ['TUTOR'] }
      },

      // --- RUTAS PARA DIRECTOR ---
      {
        path: '/estadisticas',
        name: 'estadisticas',
        component: () => import('pages/director/EstadisticasPage.vue'),
        meta: { roles: ['DIRECTOR'] }
      },

      // --- NOTIFICACIONES (todos los roles) ---
      {
        path: '/notificaciones',
        name: 'notificaciones',
        component: () => import('pages/notificaciones/NotificacionesPage.vue')
      }
    ]
  },

  // Página de acceso denegado
  {
    path: '/forbidden',
    name: 'forbidden',
    component: () => import('pages/ErrorForbidden.vue')
  },

  // 404 - Siempre al final
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes

import { ROLES } from 'src/utils/constants'

const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'dashboard',
        component: () => import('pages/IndexPage.vue'),
        meta: {
          title: 'Dashboard',
          requiresAuth: true
        }
      },
      {
        path: 'estudiantes',
        name: 'estudiantes',
        component: () => import('pages/EstudiantesPage.vue'),
        meta: {
          title: 'Estudiantes',
          requiresAuth: true
        }
      },
      {
        path: 'estudiantes/nuevo',
        name: 'estudiante-nuevo',
        component: () => import('pages/EstudianteFormPage.vue'),
        meta: {
          title: 'Nuevo Estudiante',
          requiresAuth: true,
          roles: [ROLES.TOE, ROLES.PSICOLOGO]
        }
      },
      {
        path: 'estudiantes/editar/:dni',
        name: 'estudiante-editar',
        component: () => import('pages/EstudianteFormPage.vue'),
        meta: {
          title: 'Editar Estudiante',
          requiresAuth: true,
          roles: [ROLES.TOE, ROLES.PSICOLOGO]
        }
      },
      {
        path: 'estudiantes/:dni',
        name: 'estudiante-detalle',
        component: () => import('pages/EstudianteDetailPage.vue'),
        meta: {
          title: 'Detalle Estudiante',
          requiresAuth: true
        }
      },
      {
        path: 'apoderados',
        name: 'apoderados',
        component: () => import('pages/ApoderadosPage.vue'),
        meta: {
          title: 'Apoderados',
          requiresAuth: true
        }
      },
      {
        path: 'usuarios',
        name: 'usuarios',
        component: () => import('pages/UsuariosPage.vue'),
        meta: {
          title: 'Usuarios',
          requiresAuth: true,
          roles: [ROLES.TOE]
        }
      },
      {
        path: 'diagnosticos',
        name: 'diagnosticos',
        component: () => import('pages/DiagnosticosPage.vue'),
        meta: {
          title: 'Diagnósticos',
          requiresAuth: true,
          roles: [ROLES.TOE, ROLES.PSICOLOGO]
        }
      },
      {
        path: 'citas',
        name: 'citas',
        component: () => import('pages/CitasPage.vue'),
        meta: {
          title: 'Citas',
          requiresAuth: true,
          roles: [ROLES.TOE, ROLES.PSICOLOGO]
        }
      },
      {
        path: 'reportes',
        name: 'reportes',
        component: () => import('pages/ReportesPage.vue'),
        meta: {
          title: 'Reportes',
          requiresAuth: true,
          roles: [ROLES.TOE, ROLES.PSICOLOGO, ROLES.DIRECTOR]
        }
      }
    ]
  },

  // Auth Layout
  {
    path: '/',
    component: () => import('layouts/AuthLayout.vue'),
    children: [
      {
        path: 'login',
        name: 'login',
        component: () => import('pages/LoginPage.vue'),
        meta: {
          title: 'Iniciar Sesión',
          requiresAuth: false
        }
      }
    ]
  },

  // Always leave this as last one
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes

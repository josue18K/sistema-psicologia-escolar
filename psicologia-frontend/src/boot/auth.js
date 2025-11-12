import { boot } from 'quasar/wrappers'
import { useAuthStore } from 'src/stores/auth-store'

export default boot(({ router, store }) => {
  const authStore = useAuthStore(store)

  // Inicializar autenticación al cargar la app
  authStore.initializeAuth()

  // Guard de rutas - protección por autenticación
  router.beforeEach((to, from, next) => {
    const isAuthenticated = authStore.isAuthenticated
    const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
    const userRole = authStore.getUserRole

    if (requiresAuth && !isAuthenticated) {
      // Redirigir a login si la ruta requiere auth y no está autenticado
      next('/login')
    } else if (to.path === '/login' && isAuthenticated) {
      // Redirigir al dashboard si ya está autenticado y trata de acceder a login
      next('/')
    } else if (to.meta.roles && userRole) {
      // Verificar roles específicos si la ruta los requiere
      const hasRequiredRole = to.meta.roles.includes(userRole)
      if (hasRequiredRole) {
        next()
      } else {
        next('/unauthorized')
      }
    } else {
      next()
    }
  })
})

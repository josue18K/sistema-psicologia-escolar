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

    console.log('Navigation guard:', {
      to: to.path,
      requiresAuth,
      isAuthenticated,
      userRole
    })

    if (requiresAuth && !isAuthenticated) {
      console.log('Redirecting to login: requires auth but not authenticated')
      next('/login')
    } else if (to.path === '/login' && isAuthenticated) {
      console.log('Redirecting to dashboard: already authenticated')
      next('/')
    } else if (to.meta.roles && userRole) {
      // Verificar roles específicos si la ruta los requiere
      const hasRequiredRole = to.meta.roles.includes(userRole)
      if (hasRequiredRole) {
        next()
      } else {
        console.log('Access denied: insufficient role')
        next('/')
      }
    } else {
      next()
    }
  })
})

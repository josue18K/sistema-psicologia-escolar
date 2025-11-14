import { route } from 'quasar/wrappers'
import { createRouter, createMemoryHistory, createWebHistory, createWebHashHistory } from 'vue-router'
import routes from './routes'
import { useAuthStore } from 'stores/auth'

export default route(function (/* { store, ssrContext } */) {
const createHistory = process.env.SERVER
? createMemoryHistory
: (process.env.VUE_ROUTER_MODE === 'history' ? createWebHistory : createWebHashHistory)

const Router = createRouter({
scrollBehavior: () => ({ left: 0, top: 0 }),
routes,
history: createHistory(process.env.VUE_ROUTER_BASE)
})

// Guard global - Verificar autenticación
Router.beforeEach((to, from, next) => {
const authStore = useAuthStore()
const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
const allowedRoles = to.meta.roles || []


// Si la ruta requiere autenticación
if (requiresAuth) {
  if (!authStore.isAuthenticated) {
    // No está autenticado, redirigir al login
    next({
      name: 'login',
      query: { redirect: to.fullPath }
    })
  } else if (allowedRoles.length > 0 && !allowedRoles.includes(authStore.userRole)) {
    // Está autenticado pero no tiene el rol adecuado
    next({ name: 'forbidden' })
  } else {
    // Todo OK
    next()
  }
} else {
  // Ruta pública
  next()
}
})

return Router
})

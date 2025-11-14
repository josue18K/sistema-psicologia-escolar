import { route } from 'quasar/wrappers'
import { createRouter, createWebHistory } from 'vue-router'
import routes from './routes'

export default route(function (/* { store, ssrContext } */) {
  const Router = createRouter({
    scrollBehavior: () => ({ left: 0, top: 0 }),
    routes,
    history: createWebHistory(process.env.VUE_ROUTER_BASE),
  })

  // Middleware para títulos de página
  Router.beforeEach((to, from, next) => {
    if (to.meta.title) {
      document.title = `${to.meta.title} - Sistema de Psicología`
    }
    next()
  })

  return Router
})

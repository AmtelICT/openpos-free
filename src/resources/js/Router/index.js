import VueRouter from 'vue-router'
import routes from './routes'
import store from '../Store'

const router = new VueRouter({
  base: process.env.BASE_URL,
  routes
})

// Navigation Guards
router.beforeEach((to, from, next) => {
  if(to.matched.some(route => route.meta.requiresAuth)) {
    if(!store.getters['auth/authenticated']) {
      if(!store.getters['welcome/status']) {
        next('/welcome')
      } else {
        next('/login')
      }
    } else {
      next()
    }
  } else {
    next()
  }
})

export default router;

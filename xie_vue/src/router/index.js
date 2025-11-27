import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import ItemsView from '../views/ItemsView.vue'
import ProfileView from '../views/ProfileView.vue'
import CarView from '../views/CarView.vue'
import ProductDetail from '../views/ProductDetail.vue'
const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/items',
    name: 'items',
    component: ItemsView
  },
  {
    path: '/items/:id',
    name: 'item',
    component: ProductDetail,
    props: true
  },
  {
    path: '/profile',
    name: 'profile',
    component: ProfileView
  },
  {
    path: '/car',
    name: 'car',
    component: CarView
  },
  {
    path: '/admin',
    name: 'admin',
    component: () => import('../views/AdminView.vue'),
    beforeEnter: (to, from, next) => {
      const role = localStorage.getItem('user_role')
      if (role === 'admin' || role === 'staff') {
        next()
      } else {
        alert('無權限訪問')
        next('/')
      }
    }
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router

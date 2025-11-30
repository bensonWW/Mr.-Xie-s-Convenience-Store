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
    component: () => import('../views/AdminView.vue'),
    beforeEnter: (to, from, next) => {
      const role = localStorage.getItem('user_role')
      if (role === 'admin' || role === 'staff') {
        next()
      } else {
        alert('無權限訪問')
        next('/')
      }
    },
    children: [
      {
        path: '',
        redirect: '/admin/dashboard'
      },
      {
        path: 'dashboard',
        name: 'admin-dashboard',
        component: () => import('../components/AdminDashboard.vue')
      },
      {
        path: 'products',
        name: 'admin-products',
        component: () => import('../components/AdminProducts.vue')
      },
      {
        path: 'orders',
        name: 'admin-orders',
        component: () => import('../components/AdminOrders.vue')
      },
      {
        path: 'orders/:id',
        name: 'admin-order-detail',
        component: () => import('../components/AdminOrderDetail.vue'),
        props: true
      },
      {
        path: 'coupons',
        name: 'admin-coupons',
        component: () => import('../components/AdminCoupon.vue')
      },
      {
        path: 'analytics',
        name: 'admin-analytics',
        component: () => import('../components/AdminAnalytics.vue')
      }
    ]
  },
  {
    path: '/admin/products/new',
    name: 'admin-product-create',
    component: () => import('../views/AdminProductEdit.vue'),
    beforeEnter: (to, from, next) => {
      const role = localStorage.getItem('user_role')
      if (role === 'admin' || role === 'staff') {
        next()
      } else {
        next('/')
      }
    }
  },
  {
    path: '/admin/products/:id/edit',
    name: 'admin-product-edit',
    component: () => import('../views/AdminProductEdit.vue'),
    beforeEnter: (to, from, next) => {
      const role = localStorage.getItem('user_role')
      if (role === 'admin' || role === 'staff') {
        next()
      } else {
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

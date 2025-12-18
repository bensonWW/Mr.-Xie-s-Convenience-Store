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
    meta: { requiresAuth: true, roles: ['admin', 'staff'] },
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
      },
      {
        path: 'users',
        name: 'admin-users',
        component: () => import('../components/AdminUsers.vue')
      }
    ]
  },
  {
    path: '/admin/products/new',
    name: 'admin-product-create',
    component: () => import('../views/AdminProductEdit.vue'),
    meta: { requiresAuth: true, roles: ['admin', 'staff'] }
  },
  {
    path: '/admin/users/new',
    name: 'admin-user-create',
    component: () => import('../views/AdminUserEdit.vue'),
    meta: { requiresAuth: true, roles: ['admin', 'staff'] }
  },
  {
    path: '/admin/users/:id/edit',
    name: 'admin-user-edit',
    component: () => import('../views/AdminUserEdit.vue'),
    meta: { requiresAuth: true, roles: ['admin', 'staff'] }
  },
  {
    path: '/admin/products/:id/edit',
    name: 'admin-product-edit',
    component: () => import('../views/AdminProductEdit.vue'),
    meta: { requiresAuth: true, roles: ['admin', 'staff'] }
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth) {
    const token = localStorage.getItem('token')
    const role = localStorage.getItem('user_role')

    if (!token) {
      // alert('請先登入')
      // Use simple redirect, Toast is hard to invoke outside component context without setup,
      // but we can try importing it if installed as a plugin usually exposes it.
      // For now, let's just redirect. The user will see login screen.
      return next({ path: '/profile', query: { redirect: to.fullPath } })
    }

    if (to.meta.roles && !to.meta.roles.includes(role)) {
      // alert('無權限訪問')
      return next('/')
    }
  }
  next()
})

export default router

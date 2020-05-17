import Home from '@/views/Home.vue'

export default [
  {
    path: '/',
    name: 'Home',
    component: Home,
    meta: {
      icon: 'el-icon-s-home',
      layout: 'default'
    }
  },
  {
    path: '/user',
    name: 'Users',
    component: () => import('@/views/admin/users/Index.vue'),
    meta: {
      icon: 'el-icon-s-home',
      layout: 'default',
      redirect: 'all'
    },
    children: [
      {
        path: 'all',
        name: 'All',
        component: () => import('@/views/admin/users/List.vue'),
      },
      {
        path: ':id/detail',
        name: 'Detail',
        component: () => import('@/views/admin/users/Form.vue'),
      },
    ]
  },
  {
    path: '/login',
    name: 'Login',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import('../views/auth/Login.vue'),
    meta: {
      icon: 'el-icon-edit',
      layout: 'no-sidebar',
      hidden: true,
      noAuth: true
    }
  }
]

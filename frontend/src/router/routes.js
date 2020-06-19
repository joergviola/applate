import Home from '@/views/Home.vue'
import Tabs from '@/components/layout/Tabs.vue'
import List from '@/components/generic/List.vue'
import Details from '@/components/generic/Details.vue'

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
    path: '/users',
    name: 'Users',
    component: Tabs,
    props: true,
    meta: {
      icon: 'el-icon-s-home',
      layout: 'default',
      redirect: 'all',
      bottom: true,
      rights: ['CRUD'],
    },
    children: [
      {
        path: 'all',
        name: 'All',
        component: List,
        props: route => ({
          type: 'users',
          template: {  },
          with: {  },
          columns: [
            { name: 'name', label: 'Name', editable: true },
            { name: 'email', label: 'E-mail', editable: true },
          ],
          detail: "/users",
          createBy: "button",
    
        }),
      },
      {
        path: ':id/detail',
        name: 'Detail',
        component: Details,
        props: route => ({
          type: 'users',
          id: route.params.id,
          with: {
            documents: {
              many: 'document',
              that: 'item_id',
              query: {
                and: {
                  type: 'users',
                }
              }
              
            }
          },
          fields: [
            { name: 'name', label: 'Name' },
            { name: 'email', label: 'E-Mail' },
            { name: 'phone', label: 'Phone' },
            { name: 'password', label: 'Password', type: 'password' },
            { name: 'role_id', label: 'Role', type: 'to-one', ref: 'role', display: 'name'},
            { name: 'avatar', label: 'Avatar', type: 'doc'},
          ],
        }),
      },
    ]
  },
  {
    path: '/roles',
    name: 'Roles',
    component: () => import('@/views/admin/roles/Index.vue'),
    meta: {
      icon: 'el-icon-s-home',
      layout: 'default',
      bottom: true,
      roles: ['Admin']
    },
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

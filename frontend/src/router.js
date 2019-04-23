import Vue from 'vue'
import Router from 'vue-router'
import Layout from './Layout.vue'
import UserList from './views/user/list.vue'
import UserEdit from './views/user/edit.vue'
import VerfahrenListe from './views/verfahren/liste.vue'
import Verfahren from './views/verfahren/edit.vue'
import Cockpit from './views/cockpit/edit.vue'
import Login from './views/user/login.vue'
import Profile from './views/user/profile.vue'

Vue.use(Router)

export default new Router({
  routes: [
    { path: '/login', name: 'login', component: Login, meta: { pub: true } },
    { path: '/profile', name: 'profile', component: Profile, meta: { pub: true } }, // not pub later...
    { path: '/', name: 'app', component: Layout, children: [
        { path: '/verfahren', name: 'verfahren-liste', component: VerfahrenListe },
        { path: '/verfahren/:id', name: 'verfahren-edit', component: Verfahren },
        { path: '/about', name: 'about', component: () => import(/* webpackChunkName: "about" */ './views/About.vue') },
        { path: '/user', name: 'users-list', component: UserList },
        { path: '/user/:id', name: 'users-edit', component: UserEdit },
      ] },
    { path: '/public/verfahren/:id', name: 'public-verfahren-edit', component: Verfahren },
    { path: '/public/datenschutz/:role', name: 'datenschutz', component: Cockpit },
  ]
})

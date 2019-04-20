import Vue from 'vue'
import Router from 'vue-router'
import Layout from './Layout.vue'
import VerfahrenListe from './views/verfahren/liste.vue'
import Verfahren from './views/verfahren/edit.vue'
import Cockpit from './views/cockpit/edit.vue'

Vue.use(Router)

export default new Router({
  routes: [
    { path: '/app', name: 'app', component: Layout, children: [
        { path: '/verfahren', name: 'verfahren-liste', component: VerfahrenListe },
        { path: '/verfahren/:id', name: 'verfahren-edit', component: Verfahren },
        { path: '/about', name: 'about', component: () => import(/* webpackChunkName: "about" */ './views/About.vue') },
      ] },
    { path: '/public/verfahren/:id', name: 'public-verfahren-edit', component: Verfahren },
    { path: '/public/datenschutz/:role', name: 'datenschutz', component: Cockpit },
  ]
})

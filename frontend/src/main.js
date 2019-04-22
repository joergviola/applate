import Vue from 'vue'
import './plugins/vuetify'
import App from './App.vue'
import router from './router'
import './registerServiceWorker'
import api from './lib/api'

Vue.config.productionTip = false

router.beforeEach((to, from, next) => {
  const pub = to.matched.some(record => record.meta.pub);

  if (!api.user() && !pub) {
    const loginpath = window.location.pathname;
    next({ name: 'login', query: { from: loginpath } });
  }
  else {
    next();
  }
});

new Vue({
  router,
  render: h => h(App)
}).$mount('#app')

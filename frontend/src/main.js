import Vue from 'vue'
import App from './App.vue'
import './registerServiceWorker'
import router from './router'
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import _ from 'lodash'

Vue.use(ElementUI);
Vue.prototype._ = _

Vue.config.productionTip = false

new Vue({
  router,
  render: h => h(App)
}).$mount('#app')

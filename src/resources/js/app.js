require('./bootstrap');

import Vue from 'vue'
import VueRouter from 'vue-router';
import Vuetify from '../plugins/vuetify';

import router from './Router/index'
import store from './Store/index';
import App from './App.vue'

require('./Store/subscriber')
require('./Store/helpers')

import VueFilterDateParse from '@vuejs-community/vue-filter-date-parse'
import VueFilterDateFormat from '@vuejs-community/vue-filter-date-format'

Vue.use(VueFilterDateParse)
Vue.use(VueFilterDateFormat)
Vue.use(VueRouter)

Vue.config.productionTip = false
axios.defaults.withCredentials = true
axios.defaults.baseURL = 'http://127.0.0.1:8000' // 8000 to laravel; 5555 to electron

store.dispatch('welcome/checkinstall').then(() => {
  store.dispatch('auth/attempt', localStorage.getItem('token'))
  .then(() => {
    const app = new Vue({
      vuetify: Vuetify,
      el: '#app',
      router,
      store,
      components: { App }
    });
  });
})

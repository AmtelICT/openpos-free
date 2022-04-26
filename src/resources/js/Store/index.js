import Vue from 'vue'
import Vuex from 'vuex'

import auth from './modules/auth'
import location from './modules/location'
import welcome from './modules/welcome'
import configuration from './modules/configuration'
import employee from './modules/employee'
import provider from './modules/provider'

Vue.use(Vuex);

const store = new Vuex.Store({
  modules: {
    auth,
    location,
    welcome,
    configuration,
    employee,
    provider
  }
})

export default store;

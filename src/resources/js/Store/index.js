import Vue from 'vue'
import Vuex from 'vuex'

import auth from './modules/auth'
import location from './modules/location'
import welcome from './modules/welcome'
import configuration from './modules/configuration'
import employee from './modules/employee'
import provider from './modules/provider'
import stock from './modules/stock'
import customer from './modules/customer'
import order from './modules/order'
import invoice from './modules/invoice'

Vue.use(Vuex);

const store = new Vuex.Store({
  modules: {
    auth,
    location,
    welcome,
    configuration,
    employee,
    provider,
    stock,
    customer,
    order,
    invoice
  }
})

export default store;

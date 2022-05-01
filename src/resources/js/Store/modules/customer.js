import axios from 'axios';

const customer = {
  namespaced: true,

  state: {
    errors: [],
    customers: [],
    current_customer: [],

    created: false,
    edition: false,
    data: []
  },

  actions: {
    async populate({commit}) {
      await axios.get('/api/customers')
      .then((response) => {
        commit('SET_CUSTOMER', response.data)
      })
    },

    async retrieve({commit}, id) {
      await axios.get('/api/customer/'+ id)
      .then((response) => {
        commit('SET_DATA', response.data)
        commit('SET_EDITION', true)
      })
    },

    async register({commit, dispatch}, form) {
      await axios.post('/api/customer', form)
      .then(() => {
        commit('CLEAR')
        commit('SET_STATUS', true)
        dispatch('populate')
      })
      .catch((errors) => {
        commit('SHOW_ERRORS', errors.response.data.errors)
      })
    },

    async update({dispatch}, payload) {
      await axios.put('/api/customer/'+payload.id, payload)
      .then(() => {
        dispatch('populate')
      })
    },

    async delete({dispatch}, payload) {
      await axios.delete('/api/customer/'+payload.id)
      .then(() => {
        dispatch('populate')
      })
    },

    toggle_status({commit}, payload) {
      commit('SET_STATUS', payload)
    },

    toggle_edition({commit}, payload) {
      commit('SET_EDITION', payload)
    },

    clear({commit}) {
      commit('CLEAR')
    }
  },

  mutations: {
    SET_CUSTOMER(state, payload) {
      state.customers = payload
    },

    SHOW_ERRORS(state, payload) {
      state.errors = payload
    },

    SET_DATA(state, payload) {
      state.data = payload
    },

    SET_EDITION(state, payload) {
      state.edition = payload
    },

    SET_STATUS(state, payload) {
      state.created = payload
    },

    CLEAR(state) {
      state.errors = []
      state.editmode = []
    }
  },

  getters: {
    customers(state) {
      return state.customers
    },

    errors(state) {
      return state.errors
    },

    created(state) {
      return state.created
    },

    data(state) {
      return state.data
    },

    edition(state) {
      return state.edition
    },

    current_customer(state) {
      return state.current_customer
    }
  }
}

export default customer
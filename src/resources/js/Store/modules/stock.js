import axios from 'axios';

const stock = {
  namespaced: true,

  state: {
    errors: [],
    stocks: [],
  
    stock: [],

    created: false,
    edition: false,
    editmode: []
  },

  actions: {
    async populate({commit}) {
      await axios.get('/api/stocks')
      .then((response) => {
        commit('SET_STOCKS', response.data)
      })
    },

    async retrieve({commit}, id) {
      await axios.get('/api/stock/'+ id)
      .then((response) => {
        commit('SET_MODE', response.data)
        commit('SET_EDITION', true)
      })
    },

    async register({commit, dispatch}, form) {
      await axios.post('/api/stock', form)
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
      await axios.put('/api/stock/'+payload.id, payload)
      .then(() => {
        dispatch('populate')
      })
    },

    async delete({dispatch}, payload) {
      await axios.delete('/api/stock/'+payload.id)
      .then(() => {
        dispatch('populate')
      }).catch((errors) => {
        console.log(errors.response.data.errors)
      })
    },

    async barcode_scan({commit}, barcode) {
      await axios.get('/api/article/'+barcode)
      .then((response) => {
        commit('SET_STOCK', response.data)
      })
    },

    async buy({commit,dispatch}, payload) {
      await axios.put('/api/stock/buy/'+ payload.id, payload)
      .then(() => {
        dispatch('populate')
      })
      .catch((errors) => {
        commit('SHOW_ERRORS', errors.response.data.errors)
      })
    },

    toggle_found({commit}, payload) {
      commit('SET_FOUND', payload)
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
    SET_STOCKS(state, payload) {
      state.stocks = payload
    },

    SET_STOCK(state, payload) {
      state.stock = payload
    },

    SET_FOUND(state, payload) {
      state.found = payload
    },

    SHOW_ERRORS(state, payload) {
      state.errors = payload
    },

    SET_MODE(state, payload) {
      state.editmode = payload
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
    stocks(state) {
      return state.stocks
    },

    errors(state) {
      return state.errors
    },

    created(state) {
      return state.created
    },

    editmode(state) {
      return state.editmode
    },

    edition(state) {
      return state.edition
    },

    stock(state) {
      return state.stock 
    },

    found(state) {
      return state.found
    }
  }
}

export default stock
import axios from 'axios';

const provider = {
  namespaced: true,

  state: {
    errors: [],
    providers: [],

    created: false,
    edition: false,
    data: []
  },

  actions: {
    async populate({commit}) {
      await axios.get('/api/providers')
      .then((response) => {
        commit('SET_PROVIDERS', response.data)
      })
    },

    async register({commit, dispatch}, form) {
      await axios.post('/api/providers', form)
      .then(() => {
        commit('CLEAR')
        commit('SET_STATUS', true)
        dispatch('populate')
      })
      .catch((errors) => {
        commit('SHOW_ERRORS', errors.response.data.errors)
      })
    },

    async retrieve({commit}, id) {
      await axios.get('/api/providers/'+ id)
      .then((response) => {
        commit('SET_DATA', response.data)
        commit('SET_EDITION', true)
      })
    },

    async update({dispatch}, payload) {
      await axios.put('/api/providers/'+payload.id, payload)
      .then(() => {
        dispatch('populate')
      })
    },

    async delete({dispatch}, payload) {
      await axios.delete('/api/providers/'+payload.id)
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
    SET_PROVIDERS(state, payload) {
      state.providers = payload
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
    providers(state) {
      return state.providers
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
    }
  }
}

export default provider 
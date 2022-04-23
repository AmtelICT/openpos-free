import axios from 'axios';

const employee = {
  namespaced: true,

  state: {
    errors: [],
    employees: [],

    created: false,
    edition: false,
    editmode: []
  },

  actions: {
    async populate({commit}) {
      await axios.get('/api/employees')
      .then((response) => {
        commit('SET_EMPLOYEES', response.data)
      })
      .catch((errors) => {
        console.log((errors.response.data.errors))
      })
    },

    async register({commit, dispatch}, form) {
      await axios.post('/api/employees', form)
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
      await axios.get('/api/employees/'+ id)
      .then((response) => {
        commit('SET_MODE', response.data)
        commit('SET_EDITION', true)
      })
      .catch((errors) => {
        console.log(errors.reponse.data.errors)
      })
    },

    async update({dispatch}, payload) {
      await axios.put('/api/employees/'+payload.id, payload)
      .then(() => {
        dispatch('populate')
      })
      .catch((errors) => {
        console.log(errors.response.data.errors)
      })
    },

    async delete({dispatch}, payload) {
      await axios.delete('/api/employees/'+payload.id)
      .then(() => {
        dispatch('populate')
      }).catch((errors) => {
        console.log(errors.response.data.errors)
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
    SET_EMPLOYEES(state, payload) {
      state.employees = payload
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
    employees(state) {
      return state.employees
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
    }
  }
}

export default employee
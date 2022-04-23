import axios from 'axios'

const location = {
  namespaced: true,

  state: {
    countries: [],
    states: [],
    cities: []
  },

  actions: {
    async get_countries({commit}) {
      await axios.get('/api/countries')
      .then((response) => {
        commit('SET_COUNTRIES', response.data)
      })
      .catch((errors) => {
        console.log(errors.response.data.errors)
      })
    },

    async get_states({commit}, id) {
      await axios.get('/api/states/' + id)
      .then((response) => {
        commit('SET_STATES', response.data)
      })
      .catch((errors) => {
        console.log(errors.response.data.errors)
      })
    },

    async get_cities({commit}, id) {
      await axios.get('/api/cities/' + id)
      .then((response) => {
        commit('SET_CITIES', response.data)
      })
      .catch((errors) => {
        console.log(errors.response.data.errors)
      })
    },
  },

  mutations: {
    SET_COUNTRIES(state, payload) {
      return state.countries = payload
    },

    SET_STATES(state, payload) {
      return state.states = payload
    },

    SET_CITIES(state, payload) {
      return state.cities = payload
    }
  },

  getters: {
    countries(state) {
      return state.countries
    },

    states(state) {
      return state.states
    },

    cities(state) {
      return state.cities
    }
  }
}

export default location
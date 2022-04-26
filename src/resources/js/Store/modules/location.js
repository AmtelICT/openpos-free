import axios from 'axios'

const location = {
  namespaced: true,

  state: {
    countries: [],
    states: [],
    state: null,
    cities: []
  },

  actions: {
    async get_countries({commit}) {
      await axios.get('/api/countries')
      .then((response) => {
        commit('SET_COUNTRIES', response.data)
      })
    },

    async get_states({commit}, id) {
      await axios.get('/api/states/' + id)
      .then((response) => {
        commit('SET_STATES', response.data)
      })
    },

    async get_state({commit}, id) {
      await axios.get('/api/state/' + id)
      .then((response) => {
        commit('SET_STATE', response.data)
      })
    },

    async get_cities({commit}, id) {
      await axios.get('/api/cities/' + id)
      .then((response) => {
        commit('SET_CITIES', response.data)
      })
    },

    async get_city({commit}, id) {
      await axios.get('/api/city/' + id) 
      .then((response) => {
        commit('SET_CITY', response.data)
      })
    }
  },

  mutations: {
    SET_COUNTRIES(state, payload) {
      state.countries = payload
    },

    SET_STATES(state, payload) {
      state.states = payload
    },

    SET_STATE(state, payload) {
      state.state = payload
    },

    SET_CITIES(state, payload) {
      state.cities = payload
    },

    SET_CITY(state, payload) {
      state.city = payload
    }
  },

  getters: {
    countries(state) {
      return state.countries
    },

    states(state) {
      return state.states
    },

    state(state) {
      return state.state
    },

    cities(state) {
      return state.cities
    },

    city(state) {
      return state.city
    }
  }
}

export default location
import axios from 'axios'

const configuration = {
  namespaced: true,

  state: {
    config: {}
  },

  actions: {
    async populate({commit}) {
      await axios.get('/api/configuration')
      .then((response) => {
        commit('SET_CONFIG', response.data)
      })
    },

    async update({dispatch}, form) {
      await axios.post('/api/configuration', form)
      .then(() => {
        dispatch('populate')
      })
    }
  },

  mutations: {
    SET_CONFIG(state, payload) {
      state.config = payload
    }
  },

  getters: {
    config(state) {
      return state.config
    }
  }
}

export default configuration
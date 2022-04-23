import axios from 'axios'

const welcome = {
  namespaced: true,

  state:{
    status: null
  },

  actions:{
    async checkinstall({commit}) {
      await axios.get('/api/checkinstall')
      .then((response) => {
        commit('SET_STATUS', response.data)
      })
    },

    async store_shop({commit}, form) {
      await axios.post('/api/install', form)
    },

    async store_admin({commit}, form) {
      await axios.post('/api/register', form)
    }
  },

  mutations:{
    SET_STATUS(state, payload) {
      state.status = payload
    }
  },

  getters:{
    status(state) {
      return state.status
    }
  }
}

export default welcome
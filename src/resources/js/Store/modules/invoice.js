import axios from 'axios'

const invoice = {
  namespaced: true,

  state: {
    invoice: null
  },

  actions: {
    async populate({commit}, order_number) {
      await axios.get('/api/invoice/'+ order_number)
      .then((response) => {
          commit('SET_INVOICE', response.data)
      })
    }
  },  

  mutations: {
    SET_INVOICE(state, payload) {
      state.invoice = payload
    }
  },

  getters: {
    invoice(state) {
      return state.invoice
    }
  }
}

export default invoice
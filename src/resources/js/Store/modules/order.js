import axios from 'axios'

const order = {
  namespaced: true,

  state: {
    order_number: null,
    orders:[],
    article: null,
    payment: []
  },

  actions: {
    async populate({commit, dispatch, state}) {
      await axios.get('/api/orders/'+ state.order_number)
      .then((response) => {
        commit('SET_ORDERS', response.data)
        dispatch('invoice/populate', state.order_number, {root: true})
      })
    },

    async getcode({commit, dispatch}) {
      await axios.get('/api/order/getcode')
      .then((response) => {
        if(response.data.length !== 0) {
          commit('SET_NUMBER', response.data)
          dispatch('populate')
        }
      })
    },

    async setcode({commit}) {
      await axios.get('/api/order/setcode')
      .then((response) => {
        commit('SET_NUMBER', response.data)
      })
    },

    async query({commit}, barcode) {
      await axios.get('/api/articles/' + barcode) 
      .then((response) => {
        commit('SET_ARTICLE', response.data)
      })
    },

    async generate_order({dispatch, state}, order) {
      await axios.put('/api/order/'+ state.article.barcode +'?order_number='+order)
      .then(() => {
        dispatch('populate')
      })
    },

    async update_quantity({dispatch, state}, form) {
      await axios.get('/api/orders/update/' + form.article_id + '?order_number=' + state.order_code + '&quantity=' + form.quantity)
      dispatch('populate')
    },

    async update_customer({dispatch, state}, form) {
      await axios.get('/api/orders/customer/' + state.order_code + '?dni=' + form)
      .then(() => {
        dispatch('populate')
      })
    },

    async payment({commit, state}, payment) {
      await axios.get('/api/orders/payment/'+ state.order_number + '?payment=' + payment)
      .then((response) => {
        commit('SET_PAYDATA', {'payment' : payment, 'change' : response.data})
        commit('SET_NUMBER', null)
      })
    },

    async remove_item({dispatch, state}, order) {
      if(state.article.barcode) {
        await axios.get('/api/orders/delete/' + order + '?barcode=' + state.article.barcode)
        .then(() => {
          dispatch('populate')
          dispatch('retrieve')
        })
      }
    },

    async discount({dispatch}, form) {
      await axios.get('/api/orders/discount/' + form.order + '?article_id=' + form.article_id + '&discount=' + form.discount)
      .then(() => {
        dispatch('populate')
        dispatch('retrieve')
      })
    },

    async removeall({commit}, code) {
       await axios.delete('/api/orders/cancel/' + code)
      .then(() => {
        commit('SET_NUMBER', null)
        commit('SET_ORDERS', [])
        commit('SET_INVOICE', [])
      })
    },

    unset_payment({commit}) {
      commit('SET_PAYDATA', [])
    },

    unset_article({commit}) {
      commit('SET_ARTICLE', null)
    },

    clear({commit}) {
      commit('CLEAR')
    }
  },

  mutations: {
    SET_NUMBER(state, payload) {
      state.order_number = payload
    },

    SET_ARTICLE(state, payload) {
      state.article = payload
    },

    SET_ORDERS(state, payload) {
      state.orders = payload
    },

    SET_PAYMENT(state, payload) {
      state.payment = payload
    },

    CLEAR(state) {
      state.errors = []
    }
  },

  getters: {
    orders(state) {
      return state.orders
    },

    order_number(state) {
      return state.order_number
    },

    errors(state) {
      return state.errors
    },

    article(state) {
      return state.article
    },

    payment(state) {
      return state.paydata
    }
  }
}
export default order
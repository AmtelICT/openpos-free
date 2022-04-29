import axios from 'axios'

const auth = {
  namespaced: true,

  state: {
    errors: [],
    user: [],
    token: [],
    status: false
  },

  actions: {
    async login({dispatch}, credentials) {
      await axios.post('/api/login', credentials)
      .then((response) => {
        dispatch('attempt', response.data)
      })
      .catch((errors) => {
        dispatch('error', errors.response.data.errors)
      })
    },

    async attempt({commit}, token) {
      if(token) {
        await commit('SET_TOKEN', token)
        commit('SET_STATUS', true)
      }

      if(!token) {
        commit('SET_USER', null)
        commit('SET_STATUS', false)
      } else {
        try {
          let response =  await axios.get('/api/user')
          commit('SET_USER', response.data)
        } catch (errors) {
          commit('SHOW_ERRORS', errors)
          commit('SET_TOKEN', null)
          commit('SET_USER', null)
        }
      }
    },

    async logout({commit}) {
      return axios.post('/api/logout')
      .then(() => {
        commit('SET_TOKEN', null)
        commit('SET_USER', null)
        commit('SET_STATUS', false)
      })
    }
  },

  mutations: {
    SHOW_ERRORS(state, payload) {
      state.errors = payload;
    },
    SET_TOKEN(state, payload) {
      state.token = payload
    },
    SET_USER(state, payload) {
      state.user = payload;
    },
    SET_STATUS(state, payload) {
      state.status = payload
      if(payload === false) {
        localStorage.removeItem('token')
      }
    }
  },

  getters: {
    authenticated(state) {
      return state.status
    },
    user(state) {
      return state.user
    },
    token(state) {
      return state.token
    },
    errors(state) {
      return state.errors
    }
  }
}

export default auth
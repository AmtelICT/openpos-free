import store from '../Store'
import axios from 'axios'

store.subscribe((mutation) => {
  switch(mutation.type) {
    case 'auth/SET_TOKEN':
      if(mutation.payload) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${mutation.payload}`
        localStorage.setItem('token', mutation.payload)

        // Populate configurations
        store.dispatch('configuration/populate')
      } else {
        axios.defaults.headers.common['Authorization'] = null
        localStorage.removeItem('token', mutation.payload)
      }
    break
  }
})
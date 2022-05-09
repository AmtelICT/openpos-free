<template>
  <v-app-bar
    dense
    elevation="0"
    dark
    color="#202124">
    <v-app-bar-nav-icon 
      class="included"
      @click="$router.push('/dashboard'), () => null">
    </v-app-bar-nav-icon>

    <v-toolbar-title
      class="brand-title"
    >OpenPOS
    </v-toolbar-title>

    <v-spacer></v-spacer>

    <v-btn depressed class="mr-2" color="#202124">
      <v-icon>mdi-cash-register</v-icon> 
      {{ terminal() }}
    </v-btn>
  
    <v-btn depressed color="#202124">
      <v-icon>mdi-account-tie</v-icon>
      {{ user.username }}
    </v-btn>
  </v-app-bar>
</template>

<script>
import {mapGetters, mapActions} from 'vuex'

export default {
  name: 'navbar',

  computed: {
    ...mapGetters({
      user: 'auth/user'
    })
  },

  methods: {
    terminal() {
      if(this.user.role === 'admin') {
        return 0;
      } else {
        return this.user.terminal
      }
    }
  }
}
</script>

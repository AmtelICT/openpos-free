<template>
  <div class="settings">
    <v-app-bar
      class="setting-navigation"
      color="#3c4042"
      dense
      dark>
      <v-app-bar-nav-icon @click="$router.push('/')"></v-app-bar-nav-icon>
      <v-toolbar-title>{{ $route.meta.title }}</v-toolbar-title>
      <v-spacer></v-spacer>

      <v-btn
        @click="logoff()" 
        depressed
        color="#202124"> 
      <v-icon>mdi-logout</v-icon> 
        Logout
      </v-btn>
    </v-app-bar>  

    <v-navigation-drawer
      v-model="drawer"
      :mini-variant="mini"
      permanent
      dark
      mini-variant-width="108"
      class="sidebar"
      color="#202124">
      <v-list flat :class="{'sidebar-list': this.user.role === 'admin', 'sidebar-list-common': this.user.role != 'admin'}">
        <v-list-item-group>

          <v-list-item 
            class="px-0 align-center justify-center"
            v-for="(item, index) in items" :key="index"
            router :to="{name: item.route}">
            <v-list-item-content class="sidebar-list-content">
              <v-icon v-text="item.icon" size="26"></v-icon>
              <v-list-item-subtitle 
                align="center" 
                v-text="item.text" 
                class="mt-3 caption">
              </v-list-item-subtitle>
            </v-list-item-content>
          </v-list-item>

        </v-list-item-group>
      </v-list>
    </v-navigation-drawer>
              
    <v-main style="margin-left: 108px;" class="mt-0"> <!--default mt-15 -->
      <router-view/>
    </v-main>
  </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex'

export default {
  name: 'settings',
  data () {
    return {
      drawer: true,
      items: [],
      mini: true,
    }
  },

  computed: {
    ...mapGetters({
      user: 'auth/user'
    }),
  },

  methods: {
    ...mapActions({
      logout: 'auth/logout'
    }),

    logoff() {
      this.logout()
      .then(this.$router.push('/login'))
    },

    roles() {
      if(this.user.role === 'admin') {
        this.items = [
          {icon: 'mdi-view-dashboard', text: 'Dashboard', route: 'dashboard'},
          {icon: 'mdi-account-multiple', text: 'Customers', route: 'customer'},
          {icon: 'mdi-face-agent', text: 'Employees', route: 'employee'},
          {icon: 'mdi-archive-cog', text: 'Stock', route: 'stock'},
          {icon: 'mdi-truck', text: 'Providers', route: 'provider'},
          {icon: 'mdi-cog', text: 'Configuration', route: 'configuration'}
        ]
      } else {
        this.items = [
          {icon: 'mdi-view-dashboard', text: 'Dashboard', route: 'dashboard'},
          {icon: 'mdi-account-multiple', text: 'Customers', route: 'customer'},
          {icon: 'mdi-archive-cog', text: 'Stock', route: 'stock'},
          {icon: 'mdi-truck', text: 'Providers', route: 'provider'}
        ]
      }
    }
  },

  mounted() {
    this.roles();
  }
}
</script>
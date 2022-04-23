<template>
  <v-app>
    <v-main>
      <router-view></router-view>
    </v-main>
  </v-app>
</template>

<script>
export default {
  name: 'App',
  data: () => ({
    //
  }),
  watch: {
    '$route' (to, from) {
      document.title = to.meta.title || 'OpenPOS'
    }
  },
};
</script>
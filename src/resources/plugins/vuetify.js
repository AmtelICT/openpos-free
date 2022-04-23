import Vue from 'vue';
import Vuetify from 'vuetify/lib/framework';

Vue.use(Vuetify);

import es from 'vuetify/src/locale/es.ts'

const opts = {
  lang: {
    locales: {
      es,
    },
    current: 'es',
  },
  theme: { dark: true }
}
export default new Vuetify(opts);
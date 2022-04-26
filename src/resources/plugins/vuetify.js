import Vue from 'vue';
import Vuetify from 'vuetify/lib/framework';

Vue.use(Vuetify);

import es from 'vuetify/src/locale/es.ts'
import en from 'vuetify/src/locale/en.ts'

const opts = {
  lang: {
    locales: {
      es,
      en
    },
    current: 'en',
  },
  theme: { dark: true }
}
export default new Vuetify(opts);
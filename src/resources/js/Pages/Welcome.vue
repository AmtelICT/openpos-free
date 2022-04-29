<template>
  <div class="welcome">
    <v-stepper v-model="e1" class="stepper-container">
      <v-stepper-header class="stepper-header grey darken-4">
        <v-stepper-step
          :complete="e1 > 1"
          step="1">
          Shop details
        </v-stepper-step>

        <v-divider class="stepper-divider" :class="{'teal darken-3': e1 === 1}"></v-divider>

        <v-stepper-step
          :complete="e1 > 2"
          step="2">
          Administrator account
        </v-stepper-step>

        <v-divider class="stepper-divider"></v-divider>

        <v-stepper-step 
          :complete="e1 > 3"
          step="3">
          Finish
        </v-stepper-step>
      </v-stepper-header>

      <v-stepper-items class="stepper-items">
        <v-stepper-content step="1">
          <v-card class="mb-5 pt-5">
              <div class="internal container">
                <v-form ref="shopForm" lazy-validation class="vform">
                  <v-row>
                    <v-col cols="12" md="6">
                      <v-text-field
                        :rules="shopname"
                        v-model="shop_form.name"
                        label="Shop name">
                        <v-icon
                          slot="prepend-inner"
                          color="#5af158">
                          mdi-store
                        </v-icon>
                      </v-text-field>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field
                        :rules="rut"
                        v-model="shop_form.rut"
                        label="RUT">
                        <v-icon
                          slot="prepend-inner"
                          color="#cddc39">
                          mdi-account-cash
                        </v-icon>
                      </v-text-field>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col cols="12" md="6">
                      <v-select
                        :rules="country" 
                        solo
                        :items="countries" 
                        item-text="name"
                        item-value="id"
                        label="Select country"
                        v-model="selected_country"
                        @input="retrieve_states()">
                        <template slot="selection" slot-scope="data">
                          <!-- flag emoji doesn't show in chrome/electron then 
                          as a quick fix i'm making use of www.phoca.cz/cssflags -->
                          <div class="phoca-box">
                            <div class="phoca-flagbox">
                              <span :class="'phoca-flag '+ data.item.iso2.toLowerCase()"></span> 
                            </div>
                          </div> {{ data.item.name }}
                        </template>
                        <template v-slot:item="{ item, index }">
                          <div class="phoca-box">
                            <div class="phoca-flagbox">
                              <span :class="'phoca-flag '+ item.iso2.toLowerCase()"></span> 
                            </div>
                          </div> {{ item.name }}
                        </template>
                      </v-select>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-select 
                        :rules="state"
                        solo
                        :disabled="!selected_country"
                        :items="states" 
                        item-text="name"
                        item-value="id"
                        label="Select state"
                        v-model="selected_state"
                        @input="retrieve_cities()">
                        <template slot="selection" slot-scope="data">
                          {{ data.item.emoji }} {{ data.item.name }}
                        </template>
                        <template v-slot:item="{ item, index }">
                          {{item.emoji}} {{ item.name }}
                        </template>
                      </v-select>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-select 
                        :rules="city"
                        solo
                        :disabled="!selected_state"
                        :items="cities" 
                        item-text="name"
                        item-value="id"
                        label="Select city"
                        v-model="selected_city">
                        <template slot="selection" slot-scope="data">
                          {{ data.item.emoji }} {{ data.item.name }}
                        </template>
                        <template v-slot:item="{ item, index }">
                          {{item.emoji}} {{ item.name }}
                        </template>
                      </v-select>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field
                        :rules="address"
                        v-model="shop_form.address"
                        label="Address">
                        <v-icon
                          slot="prepend-inner"
                          color="#e51c23">
                          mdi-map-marker
                        </v-icon>
                      </v-text-field>
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col cols="12" md="6">
                      <v-text-field
                        :rules="phone"
                        v-model="shop_form.phone"
                        label="Phone">
                        <v-icon
                          slot="prepend-inner"
                          color="#00bcd4">
                          mdi-phone
                        </v-icon>
                      </v-text-field>
                    </v-col>

                    <v-col cols="12" md="6">
                      <v-checkbox :rules="checkbox">
                        <template v-slot:label>
                          <div>
                            I don't gonna use that software with commercial purposes BUT if i would is because i already did read the
                            <v-tooltip bottom>
                              <template v-slot:activator="{ on }">
                                <a
                                  target="_blank"
                                  href="https://github.com/vuexlaravel/openpos-free#disclaimer"
                                  @click.stop
                                  v-on="on">
                                  disclaimer
                                </a>
                              </template>
                              Opens github project link
                            </v-tooltip>
                            .
                          </div>
                        </template>
                      </v-checkbox>
                    </v-col>
                  </v-row>
                </v-form>
              </div>
            </v-card>

          <div class="right next-buttons">
            <v-btn text>
              Cancel
            </v-btn>
            <v-btn
              color="teal darken-4"
              @click="register_shop">
              Continue
            </v-btn>
          </div>
        </v-stepper-content>

        <v-stepper-content step="2">
          <v-card class="mb-5 pt-5">
            <div class="internal container">
              <v-form ref="adminForm" lazy-validation class="admform">
                <v-row>
                  <v-col cols="12" md="6">
                    <v-text-field
                      :rules="username"
                      v-model="admin_form.username"
                      label="Username">
                      <v-icon
                        slot="prepend-inner"
                        color="#5af158">
                        mdi-account
                      </v-icon>
                    </v-text-field>
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-text-field
                      :rules="email"
                      v-model="admin_form.email"
                      label="E-Mail">
                      <v-icon
                        slot="prepend-inner"
                        color="#5af158">
                        mdi-email
                      </v-icon>
                    </v-text-field>
                  </v-col>
                </v-row>
                
                <v-row>
                  <v-col cols="12" md="6">
                    <v-text-field
                      :rules="password"
                      v-model="admin_form.password"
                      :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                      @click:append="showPassword = !showPassword"
                      :type="showPassword ? 'text' : 'password'"
                      label="Password">
                      <v-icon
                        slot="prepend-inner"
                        color="#5af158">
                        mdi-lock
                      </v-icon>
                      
                    </v-text-field>
                    <template>
                      <password v-model="admin_form.password" :strength-meter-only="true" />
                    </template>
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-text-field
                      :rules="validate_password"
                      v-model="admin_form.password_confirmation"
                      :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                      @click:append="showPassword = !showPassword"
                      :type="showPassword ? 'text' : 'password'"
                      label="Password confirmation">
                      <v-icon
                        slot="prepend-inner"
                        color="#5af158">
                        mdi-lock-question
                      </v-icon>
                    </v-text-field>
                  </v-col>
                </v-row>
              </v-form>
            </div>
          </v-card>

          <div class="right next-buttons">
            <v-btn text
              @click="e1 = 1">
              Cancel
            </v-btn>
            <v-btn
              color="teal darken-4"
              @click="register_admin">
              Continue
            </v-btn>
          </div>
        </v-stepper-content>

        <v-stepper-content step="3">
          <v-card class="mb-5 pt-5">
            <div class=" internal container">
              <div class="endForm">
                <h1 class="text-center">Choose your theme</h1>
                <p class="text-center"><v-icon style="margin-top:-8px;">mdi-lightbulb-on</v-icon> Tip: you can change theme later in configuration settings</p>
                <v-radio-group
                  active-class="active"
                  class="rgroup"
                  v-model="selected_theme"
                  row>
                  <v-col cols="6" md="6">
                    <div class="lightCard">
                      <div class="lttitle">Title</div>
                      <div class="ltcontent">some example text </div>
                    </div>
                     <v-radio
                      active-class="active"
                      label="Light theme"
                      value="light"
                      color="blue">
                    </v-radio>
                  </v-col>

                  <v-col cols="6" md="6">
                    <div class="darkCard">
                      <div class="dctitle">Title</div>
                      <div class="dccontent">some example text </div>
                    </div>
                     <v-radio
                      active-class="active"
                      label="Dark theme"
                      value="dark">
                    </v-radio>
                  </v-col>
                </v-radio-group>
              </div>
            </div>
          </v-card>

          <div class="right next-buttons">
            <v-btn text
              @click="e1 = 2">
              Cancel
            </v-btn>
            <v-btn
              color="teal darken-4"
              @click="register_all">
              Continue
            </v-btn>
          </div>
        </v-stepper-content>
      </v-stepper-items>
    </v-stepper>
  </div>
</template>

<script>
import {mapGetters, mapActions} from 'vuex'
import Password from 'vue-password-strength-meter'

export default {
  name: 'welcome',
  components: { Password },
  data: () => ({
    e1: 1,
    shop_form: {},
    admin_form: {},

    showPassword: false,
    password_score: 'strenght',
    selected_country: null,
    selected_state: null,
    selected_city: null,
    selected_theme: 'dark',

    // Shop Form
    shopname: [
      v => !!v || 'Shop name is required'
    ],
    rut: [
      v => !!v || 'This field is required'
    ],
    country: [
      v => !!v || 'Country is required'
    ],
    state: [
      v => !!v || 'State is required'
    ],
    city: [
      v => !!v || 'A city is required'
    ],
    address: [
      v => !!v || 'This field is required'
    ],
    phone: [
      v => !!v || 'Phone number is required'
    ],
    checkbox: [
      v => !!v || 'Are you agree?'
    ],
    
    // Admin Form
    username: [
      v => !!v || 'Username is required'
    ],
    email: [
      v => !!v || 'Email is required',
      v => /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(v) || 'E-mail must be valid'
    ],
    password: [
      v => !!v || 'A password is required'
    ]
  }),

  computed: {
    ...mapGetters({
      countries:     'location/countries',
      states:        'location/states',
      cities:        'location/cities'
    }),
    validate_password() {
      return [
        (v) => !!v || 'Is required to confirm your password',
        (v) => (v === this.admin_form.password) || 'The given passwords mismatch!',
      ];
    },
  },

  methods: {
    ...mapActions({
      get_countries:  'location/get_countries',
      get_states:     'location/get_states',
      get_cities:     'location/get_cities',

      store_shop:     'welcome/store_shop',
      store_admin:    'welcome/store_admin',
      completed:      'welcome/checkinstall'
    }),

    retrieve_states() {
      this.get_states(this.selected_country)
    },

    retrieve_cities() {
      this.get_cities(this.selected_state)
    },

    register_shop() {
      this.shop_form.country_id = this.selected_country
      this.shop_form.state_id = this.selected_state
      this.shop_form.city_id = this.selected_city

      const valid = this.$refs.shopForm.validate()
      if(valid) {
        this.e1 = 2
      }
    },

    register_admin() {
      const valid = this.$refs.adminForm.validate()
      if(valid) {
        this.e1 = 3
      }
    },

    async register_all() {
      this.shop_form.theme = this.selected_theme
      await this.store_shop(this.shop_form)
      await this.store_admin(this.admin_form)
      await this.completed()
      .then(() => {
        this.$router.push('/')
      })
    }
  },

  mounted() {
    this.get_countries()
  }
}
</script>
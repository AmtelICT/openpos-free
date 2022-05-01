<template>
  <div class="configuration">
    <v-card class="vcard-container">
      <div class="internal-form container">
        <v-form ref="form" lazy-validation class="configuration-form" elevation="0">
          <v-row>
            <v-col cols="12" md="6">
              <v-text-field
                outlined
                :rules="shopname"
                v-model="form.name"
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
                outlined
                :rules="rut"
                v-model="form.rut"
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
                outlined
                :rules="country"
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
                outlined
                :rules="state"
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
          </v-row>

          <v-row>
            <v-col cols="12" md="6">
              <v-select 
                outlined
                :rules="city"
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
                outlined
                :rules="address"
                v-model="form.address"
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
              <div class="theme-label">Theme</div>
              <v-radio-group
                v-model="form.theme"
                row>
                <v-radio
                  active-class="active"
                  label="light theme"
                  value="light">
                </v-radio>
                <v-radio
                  active-class="active"
                  label="dark theme"
                  value="dark">
                </v-radio>
              </v-radio-group>
            </v-col>

            <v-col cols="12" md="6">
              <div class="update_conf">
                <v-btn
                  color="teal accent-4"
                  @click="save_changes">
                  Save changes
                </v-btn>
              </div>
            </v-col>
          </v-row>
        </v-form>
      </div>
    </v-card>
  </div>
</template>

<script>
import {mapGetters, mapActions} from 'vuex'
  export default {
    name: 'configuration',
    data: () => ({
      form: null,
      selected_country: null,
      selected_state: null,
      selected_city: null,

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
    }),

    computed: {
      ...mapGetters({
        config:     'configuration/config',
        countries:  'location/countries',
        states:     'location/states',
        cities:     'location/cities'
      })
    },

    methods: {
      ...mapActions({
        populate:       'configuration/populate',
        get_countries:  'location/get_countries',
        get_states:     'location/get_states',
        get_cities:     'location/get_cities',
        update:         'configuration/update'
      }),

      retrieve_states() {
        this.form.state_id = null 
        this.form.city_id = null
        this.selected_state = null
        this.selected_city = null
        this.get_states(this.selected_country)
      },

      retrieve_cities() {
        this.get_cities(this.selected_state)
      },

      set_values() {
        this.selected_country = this.config.country_id
        this.selected_state = this.config.state_id
        this.selected_city = this.config.city_id
        this.form = this.config
      },

      save_changes() {
        const valid = this.$refs.form.validate()
        if(valid) {
          this.update(this.form)
        }
      }
    },

    created() {
      this.populate()
      this.get_countries()
      this.get_states(this.config.country_id)
      this.get_cities(this.config.state_id)
      this.set_values()
    }
  }
</script>
<template>
  <div class="customer">   
    <div class="table-container">
      <!-- Table -->
      <v-card v-resize="onResize" class="mt-30 vcard-table" elevation="0">     
        <v-card-title>
          <v-text-field
            v-model="search"
            append-icon="mdi-magnify"
            label="Search"
            single-line
            hide-details
            class="mt-n2">
          </v-text-field>
          <v-btn @click.stop="dialog = true, toggle_edition(false)"
            color="teal darken-1 mb-2 float-right ml-4 mt-4">
            <v-icon>mdi-plus</v-icon>
              Add
            </v-btn>
        </v-card-title>
        <v-data-table
          :headers="headers"
          :items="customers"
          :height="windowSize.y - 64 - 24 - 59 - 36 - 28"
          fixed-header
          :search="search"
          :items-per-page="10"
          :sort-desc="true"
          class="mt-n3">
          <template v-slot:[`item.spent`]="{ item }">
            {{ item.spent | toUSD }}
          </template>
          <template v-slot:[`item.actions`]="{ item }">
            <v-btn icon 
              @click="loading(item.id), edit(item.id)" 
              :loading="loader === item.id">
              <v-icon small class="edit-icon">mdi-pencil</v-icon>
            </v-btn>
            <v-btn @click="remove(item)" icon>
              <v-icon small class="delete-icon">mdi-delete</v-icon>
            </v-btn>
          </template> 
        </v-data-table>
      </v-card>
    </div>
    <!-- /Table -->

    <!-- dialog -->
    <v-dialog v-model="dialog" width="600" persistent>    
      <v-card>
        <v-card-title>
          <span v-if="!edition">New customer</span>
          <span v-if="edition">Edit customer</span>
        </v-card-title>
        <v-card-text>
          <v-form ref="form" lazy-validation>

            <v-row class="mb-n8">
              <v-col cols="12" md="6">
                <v-text-field
                  outlined
                  label="Name"
                  :rules="nameRules"
                  :error-messages="errors.name"
                  v-model="form.name">
                  <v-icon
                    slot="prepend-inner"
                    color="#9c27b0">
                    mdi-form-textbox
                  </v-icon>
                </v-text-field>
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  outlined
                  type="text"
                  label="DNI"
                  :rules="dniRules"
                  :error-messages="errors.dni"
                  v-model="form.dni">
                  <v-icon
                    slot="prepend-inner"
                    color="#8bc34a">
                    mdi-card-account-details
                  </v-icon>
                </v-text-field>
              </v-col>
            </v-row>

            <v-row class="mb-n8">
              <v-col cols="12" md="6">
                <v-select
                  outlined
                  :items="states"
                  color="white"
                  item-text="name"
                  item-value="id"
                  label="State"
                  :rules="stateRules"
                  @input="set_state"
                  v-model="selected_state">
                  <v-icon
                    slot="prepend-inner"
                    color="#e64a19">
                    mdi-city
                  </v-icon>
                </v-select>
              </v-col>

              <v-col cols="12" md="6">            
                <v-autocomplete
                  outlined
                  :items="cities"
                  item-text="name"
                  item-value="id"
                  label="City"
                  :disabled="!form.state_id"
                  :rules="cityRules"
                  @input="set_city"
                  v-model="selected_city">
                  <v-icon
                    slot="prepend-inner"
                    color="#ff5722">
                    mdi-home-group
                  </v-icon>
                </v-autocomplete>
              </v-col>
            </v-row>

            <v-row class="mb-n8">
              <v-col cols="12" md="6">
                <v-text-field
                  outlined
                  type="text"
                  label="Address"
                  :rules="addressRules"
                  :error-messages="errors.address"
                  v-model="form.address">
                  <v-icon
                    slot="prepend-inner"
                    color="#ffc107">
                    mdi-map-marker
                  </v-icon>
                </v-text-field>
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  outlined
                  type="tel"
                  label="Phone"
                  :rules="phoneRules"
                  :error-messages="errors.phone"
                  v-model="form.phone">
                  <v-icon
                    slot="prepend-inner"
                    color="#00bcd4">
                    mdi-phone
                  </v-icon>
                </v-text-field>
              </v-col>
            </v-row>
          </v-form>

        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="red" text @click="close">Cancel</v-btn>

          <!-- If New Employee -->
          <v-btn v-if="!edition" 
            color="teal"
            @click="submit_record"
            ref="save_btn">
            Save
          </v-btn>
          <!-- /If New Employee -->

          <!-- If EditMode -->
          <v-btn v-if="edition"
            color="teal"
            @click="update_record"
            ref="save_btn">
            Save changes
          </v-btn>
          <!-- /If EditMode -->
        </v-card-actions>
      </v-card>
    </v-dialog>
    <!-- /dialog -->

    <!-- snackbar -->
    <v-snackbar 
      v-model="snackbar" 
      right 
      color="teal darken-2"
      timeout="1000"
      transition="scroll-y-transition">
      {{ text }}
    </v-snackbar>
    <!-- /snackbar -->

    <!-- delete confirmation -->
    <v-dialog v-model="confirmation" max-width="435px">
      <v-card>
        <v-card-title class="text-h5">Really do you want to delete this?</v-card-title>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="red" text @click="close_dialog">Cancel</v-btn>
          <v-btn color="green" text @click="delete_item">OK</v-btn>
          <v-spacer></v-spacer>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <!-- delete confirmation -->
  </div>
</template>

<script>
import {mapGetters, mapActions} from 'vuex';

export default {
  name: 'customer',
  data: ()  => ({
    form: {},
    search: '',
    show_password: false,
    
    loader: null,

    trash :null, 
    index: null,
    dialog: false,
    confirmation: false,

    snackbar: false,
    text: null,

    selected_country: null,
    selected_state: null,
    selected_city: null,

    windowSize: {
      y: 0,
    },

    nameRules: [
      v => !!v || 'Name is required'
    ],
    stateRules: [
      v => !!v || 'State is required'
    ],
    cityRules: [
      v => !!v || 'City is required'
    ],
    addressRules: [
      v => !!v || 'Address is required'
    ],
    dniRules: [
      v => !!v || 'DNI is required'
    ],
    phoneRules: [
      v => !!v || 'Phone number is required',
      v => /^\d+$/.test(v) || 'It must be a numeric value',
    ]
  }),
  computed: {
    ...mapGetters({
      customers: 'customer/customers',
      errors:    'customer/errors',
      created:   'customer/created',

      data:      'customer/data',
      edition:   'customer/edition',
      
      states:    'location/states',
      state:     'location/state',
      cities:    'location/cities',
      city:      'location/city',

      config:    'configuration/config'
    }),
    headers () {
      return [
        {
          text: 'Name',
          align: 'start',
          sortable: false,
          value: 'name',
          width: '14.285714286%'
        },
        { 
          text: 'Points', 
          value: 'points', 
          width: '14.285714286%'
        },
        { 
          text: 'DNI', 
          value: 'dni', 
          sortable: false, 
          width: '14.285714286%'
        },
        { 
          text: 'Phone', 
          value: 'phone', 
          sortable: false,
          width: '14.285714286%'
        },
        { 
          text: 'State', 
          value: 'statename', 
          width: '14.285714286%'
        },
        { 
          text: 'City', 
          value: 'cityname', 
          width: '14.285714286%'
        },
        { 
          text: 'Address', 
          value: 'address', 
          width: '14.285714286%'
        },
        { 
          text: 'Actions', 
          value: 'actions', 
          sortable: false, 
          align: 'center', 
          width: '110px' 
        },
      ]
    }
  },
  methods: {
    ...mapActions({
      populate: 'customer/populate',
      register: 'customer/register',
      retrieve: 'customer/retrieve',
      update:   'customer/update',
      delete:   'customer/delete',

      toggle_status:  'customer/toggle_status',
      toggle_edition: 'customer/toggle_edition',
      clear:          'customer/clear',

      get_states:     'location/get_states',
      get_state:      'location/get_state',
      get_cities:     'location/get_cities',
      get_city:       'location/get_city',

      get_config:     'configuration/populate'
    }),

    onResize () {
      this.windowSize = { y: window.innerHeight }
    },

    loading(id) {
      this.loader = id
    },

    set_state: function() {
      this.form.country_id = this.selected_country
      this.form.state_id = this.selected_state
      this.get_cities(this.selected_state)
    },

    set_city: function () {
      this.form.city_id = this.selected_city
    },

    open() {
      this.dialog = true
      this.loader = null
      if(this.$refs.form) {
        this.$refs.form.resetValidation()
      }
    },

    close() {
      this.form = {}
      this.$refs.form.resetValidation()
      this.clear()
      this.form = {}
      this.selected_state = null
      this.selected_city = null
      this.dialog = false
    },

    close_dialog() {
      this.form = {}
      this.selected_state = null
      this.selected_city = null
      this.confirmation = false
    },

    submit_record() {
      const valid = this.$refs.form.validate()
      if(valid) {
        this.register(this.form)
        .then(() => {
          if(this.created === true) {
            this.text = `Customer registered!`
            this.snackbar = true
            this.close()
          }
          this.toggle_status(false)
        })
      }
    },

    async edit(id) {
      await this.retrieve(id)
      this.form = this.data
      await this.get_state(this.data.state_id)
      this.selected_state = this.state
      await this.get_cities(this.selected_state.id)
      await this.get_city(this.data.city_id)
      this.selected_city = this.city
      this.open()
    },

    update_record() {
      this.update(this.form)
      .then(() => {
        this.text = `Registry updated!`
        this.snackbar = true
        this.close()
      })
    },

    remove(item) {
      this.index = this.customers.indexOf(item)
      this.trash = item
      this.confirmation = true
    },

    delete_item() {
      this.customers.splice(this.index, 1)
      this.delete(this.trash)
      this.trash = null
      this.close_dialog()
    },

    filterOnlyCapsText (value, search) {
      return value != null &&
        search != null &&
        typeof value === 'string' &&
        value.toString().toLocaleUpperCase().indexOf(search) !== -1
    }
  },
  
  async created() {
    await this.populate()
    await this.get_config()
    this.get_states(this.config.country_id)
    this.selected_country = this.config.country_id
  },
}
</script>
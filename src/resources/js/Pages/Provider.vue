<template>
  <div class="provider">   
    <!-- Table -->
    <div class="table-container">
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
          :items="providers"
          :height="windowSize.y - 64 - 24 - 59 - 36 - 28"
          fixed-header
          :search="search"
          :items-per-page="10"
          :sort-desc="true"
          class="mt-n3">
          <template v-slot:[`item.actions`]="{ item }">
            <v-btn
              icon 
              @click="loading(item.id), edit(item.id)" 
              :loading="loader === item.id">
              <v-icon small class="edit-icon">mdi-pencil</v-icon>
            </v-btn>
            <v-btn
              @click="remove(item)" 
              icon>
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
          <span v-if="!edition">New provider</span>
          <span v-if="edition">Edit provider</span>
        </v-card-title>
        <v-card-text>
          <v-form ref="form" lazy-validation>
            <v-row class="mb-n8">
              <v-col cols="12" md="6">
                <v-text-field
                  outlined
                  label="Name"
                  :rules="companyRules"
                  :error-messages="errors.company"
                  v-model="form.company">
                  <v-icon
                    slot="prepend-inner"
                    color="#9c27b0">
                    mdi-form-textbox
                  </v-icon>
                </v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-select
                  outlined
                  :items="states"
                  color="white"
                  item-text="name"
                  item-value="id"
                  label="Select state"
                  :rules="stateRules"
                  return-object
                  @input="trigger_retrieve"
                  v-model="selected_state">
                  <v-icon
                    slot="prepend-inner"
                    color="#e64a19">
                    mdi-city
                  </v-icon>
                </v-select>
              </v-col>
            </v-row>
            <v-row class="mb-n8">
              <v-col cols="12" md="6">            
                <v-select
                  outlined
                  :items="cities"
                  item-text="name"
                  item-value="id"
                  label="Select city"
                  :disabled="!form.state_id"
                  :rules="cityRules"
                  @input="set_city"
                  v-model="selected_city">
                  <v-icon
                    slot="prepend-inner"
                    color="#ff5722">
                    mdi-home-group
                  </v-icon>
                </v-select>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  outlined
                  type="text"
                  label="Address"
                  :rules="addressRules"
                  v-model="form.address">
                  <v-icon
                    slot="prepend-inner"
                    color="#ffc107">
                    mdi-map-marker
                  </v-icon>
                </v-text-field>
              </v-col>
            </v-row>
            <v-row class="mb-n8">
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
              <v-col cols="12" md="6">
                <v-text-field
                  outlined
                  type="email"
                  label="E-Mail (optional)"
                  v-model="form.email">
                  <v-icon
                    slot="prepend-inner"
                    color="white">
                    mdi-email
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
  name: 'provider',
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

    companyRules: [
      v => !!v || 'Provider\'s name is required'
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
    emailRules: [
      v => !!v || 'Email is required',
      v => (v.match(/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/)) || 'Invalid Email address'
    ],
    phoneRules: [
      v => !!v || 'Phone number is required',
      v => /^\d+$/.test(v) || 'It has to be a numeric value',
    ]
  }),
  computed: {
    ...mapGetters({
      providers: 'provider/providers',
      errors:    'provider/errors',
      created:   'provider/created',

      editmode:  'provider/editmode',
      edition:   'provider/edition',
      states:    'location/states',
      cities:    'location/cities',

      config:     'configuration/config'
    }),

    headers () {
      return [
        {
          text: 'Name',
          align: 'start',
          sortable: false,
          value: 'company',
          width: '16.666666667%'
        },
        { 
          text: 'State', 
          value: 'state',
          width: '16.666666667%'
        },
        { 
          text: 'City', 
          value: 'city',
          width: '16.666666667%'
        },
        { 
          text: 'Address', 
          value: 'address', 
          sortable: false,
          width: '16.666666667%' 
        },
        { 
          text: 'E-Mail', 
          value: 'email', 
          sortable: false, 
          width: '16.666666667%' 
        },
        { 
          text: 'Phone', 
          value: 'phone', 
          sortable: false,
          width: '16.666666667%'
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
      populate: 'provider/populate',
      register: 'provider/register',
      retrieve: 'provider/retrieve',
      update:   'provider/update',
      delete:   'provider/delete',

      toggle_status:  'provider/toggle_status',
      toggle_edition: 'provider/toggle_edition',
      clear:          'provider/clear',

      get_states:     'location/get_states',
      get_cities:     'location/get_cities',
      get_id:         'location/get_id',

      get_config:     'configuration/populate'
    }),

    onResize () {
      this.windowSize = { y: window.innerHeight }
    },

    loading(id) {
      this.loader = id
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

    submit_record() {
      console.log(this.form)
      const valid = false//this.$refs.form.validate()
      if(valid) {
        this.register(this.form)
        .then(() => {
          if(this.created === true) {
            this.text = `Se ha registrado un nuevo proveedor!`
            this.snackbar = true
            this.close()
          }
          this.toggle_status(false)
        })
      }
    },

    async edit(id) {
      await this.retrieve(id)
      await this.get_id(this.editmode.state)
      this.form = this.editmode
      this.selected_state = this.editmode.state
      this.selected_city = this.editmode.city
      this.open()
    },

    update_record() {
      this.update(this.form)
      .then(() => {
        this.text = `Se ha actualizado un registro!`
        this.snackbar = true
        this.close()
      })
    },

    remove(item) {
      this.index = this.providers.indexOf(item)
      this.trash = item
      this.confirmation = true
    },

    delete_item() {
      this.providers.splice(this.index, 1)
      this.delete(this.trash)
      this.trash = null
      this.close_dialog()
    },

    close_dialog() {
      this.form = {}
      this.selected_state = null
      this.selected_city = null
      this.confirmation = false
    },

    trigger_retrieve: function() {
      this.form.state_id = this.selected_state.id
      this.get_cities(this.selected_state.id)
    },

    set_city: function () {
      this.form.city_id = this.selected_city
    },

    filterOnlyCapsText (value, search) {
      return value != null &&
        search != null &&
        typeof value === 'string' &&
        value.toString().toLocaleUpperCase().indexOf(search) !== -1
    },
  },
  created() {
    this.populate()
    this.get_config()
    .then(() => {
      this.get_states(this.config.country_id)
      this.selected_country = this.config.country_id
    })
  },
}
</script>
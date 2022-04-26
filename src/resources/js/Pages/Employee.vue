<template>
  <div class="employee">   
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
            class="mt-n2"
          ></v-text-field>
          <v-btn @click.stop="dialog = true, toggle_edition(false)"
            color="teal darken-1 mb-2 float-right ml-4 mt-4">
            <v-icon>mdi-plus</v-icon>
            Add</v-btn>
        </v-card-title>
        <v-data-table
          :headers="headers"
          :items="employees"
          :height="windowSize.y - 64 - 24 - 59 - 36 - 28"
          fixed-header
          :search="search"
          :items-per-page="10"
          :sort-desc="true"
          class="mt-n3">
          <template v-slot:[`item.created_at`]="{ item }">
            {{ item.created_at | dateParse('YYYY-MM-DD') | dateFormat('DD-MM-YYYY')  }}
          </template>

          <template v-slot:[`item.actions`]="{ item }">
            <v-btn
              icon 
              @click="loading(item.id), 
              edit(item.id)" 
              :loading="loader === item.id">
              <v-icon small class="edit-icon">
                mdi-pencil
              </v-icon>
            </v-btn>
            <v-btn
              @click="remove(item)" 
              icon>
              <v-icon small class="delete-icon">
                mdi-delete
              </v-icon>
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
        <span v-if="!edition">New employee</span>
        <span v-if="edition">Edit employee</span>
      </v-card-title>
      <v-card-text>
        <v-form ref="form" lazy-validation>
          <v-row class="mb-n8">
            <v-col cols="6" md="6">
            <v-text-field
              outlined
              label="Username"
              :rules="nameRules"
              :error-messages="errors.username"
              v-model="form.username">
              <v-icon
                slot="prepend-inner"
                color="#9c27b0">
                mdi-account
              </v-icon>
            </v-text-field>
            </v-col>

            <v-col cols="6" md="6">
            <v-text-field
              outlined
              label="Cashier assigned"
              :rules="numberRules"
              v-model="form.terminal">
              <v-icon
                slot="prepend-inner"
                color="#1de9b6">
                mdi-cash-register
              </v-icon>
            </v-text-field>
            </v-col>
          </v-row>
          <v-row class="mb-n8">
            <v-col cols="6" md="6">
              <v-text-field
                outlined
                type="tel"
                label="Phone"
                :rules="phoneRules"
                v-model="form.phone">
                <v-icon
                  slot="prepend-inner"
                  color="#00bcd4">
                  mdi-phone
                </v-icon>
              </v-text-field>
            </v-col>
            <v-col cols="6" md="6">
              <!-- If New Employee -->
              <v-text-field
                outlined
                v-if="!edition"
                label="Password"
                :rules="passwordRules"
                :type="show_password ? 'text' : 'password'"
                :append-icon="show_password ? 'mdi-eye' : 'mdi-eye-off'"
                @click:append="show_password = !show_password"
                v-model="form.password">
                <v-icon
                  slot="prepend-inner"
                  color="#ff9800">
                  mdi-lock
                </v-icon>
              </v-text-field>
              <!-- /If New Employee -->
              
              <!-- If EditMode -->
              <v-text-field
                outlined
                v-if="edition"
                label="New password"
                :type="show_password ? 'text' : 'password'"
                :append-icon="show_password ? 'mdi-eye' : 'mdi-eye-off'"
                @click:append="show_password = !show_password"
                color="yellow"
                v-model="form.password">
                <v-icon
                  slot="prepend-inner"
                  color="yellow">
                  mdi-lock
                </v-icon>
              </v-text-field>
              <!-- /If EditMode -->
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
  name: 'employee',
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
    windowSize: {
      y: 0,
    },

    nameRules: [
      v => !!v || 'Username is required'
    ],
    passwordRules: [
      v => !!v || 'Password is required'
    ],
    numberRules: [
      v => !!v || 'You must assign a cashier number',
      v => /^\d+$/.test(v) || 'It has to be a numeric value',
    ],
    phoneRules: [
      v => !!v || 'Contact info is required',
      v => /^\d+$/.test(v) || 'It has to be a numeric value',
    ]
  }),
  computed: {
    ...mapGetters({
      employees:  'employee/employees',
      errors:     'employee/errors',
      created:    'employee/created',
      data:       'employee/data',
      edition:    'employee/edition'
    }),
    headers () {
      return [
        {
          text:     'Name',
          align:    'start',
          sortable: false,
          value:    'username',
          width:    '25%'
        },
        {
          text:  'Cashier',
          value: 'terminal',
          align: 'left', 
          width: '25%'
        },
        { 
          text:  'Date of admission', 
          value: 'created_at', 
          align: 'left', 
          width: '25%'
        },
        { 
          text:     'Contact', 
          value:    'phone', 
          sortable: false, 
          width:    '25%' 
        },
        { 
          text:     'Actions', 
          value:    'actions', 
          sortable: false, 
          align:    'center', 
          width:    '110px' 
        },
      ]
    }
  },
  methods: {
    ...mapActions({
      populate: 'employee/populate',
      register: 'employee/register',
      retrieve: 'employee/retrieve',
      update:   'employee/update',
      delete:   'employee/delete',

      toggle_status:  'employee/toggle_status',
      toggle_edition: 'employee/toggle_edition',
      clear:          'employee/clear'
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
      this.$refs.form.reset()
      this.$refs.form.resetValidation()
      this.clear()
      this.dialog = false
    },

    submit_record() {
      const valid = this.$refs.form.validate()
      if(valid) {
        this.register(this.form)
        .then(() => {
          if(this.created === true) {
            this.text = `A new registry was succesful added!`
            this.snackbar = true
            this.close()
          }
          this.toggle_status(false)
        })
      }
    },

    edit(id) {
      this.retrieve(id)
      .then(() => {
        this.form = this.data
        this.open()
      })
    },

    update_record() {
      this.update(this.form)
      .then(() => {
        this.text = `A registry was updated!`
        this.snackbar = true
        this.close()
      })
    },

    remove(item) {
      this.index = this.employees.indexOf(item)
      this.trash = item
      this.confirmation = true
    },

    delete_item() {
      this.employees.splice(this.index, 1)
      this.delete(this.trash)
      this.trash = null
      this.close_dialog()
    },

    close_dialog() {
      this.confirmation = false
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
  },
}
</script>
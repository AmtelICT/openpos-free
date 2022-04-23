<template>
  <div class="employee">   
    <v-container fluid v-resize="onResize" class="mt-30" style="position:relative">
      <!-- Table -->
      <v-card>     
        <v-card-title>
          <v-text-field
            v-model="search"
            append-icon="mdi-magnify"
            label="Buscar"
            single-line
            hide-details
            class="mt-n2"
          ></v-text-field>
          <v-btn @click.stop="dialog = true, toggle_edition(false)"
            color="success mb-2 float-right ml-4 mt-4">
            <v-icon>mdi-plus</v-icon>
            Agregar</v-btn>
        </v-card-title>
        <!-- :height="windowSize.y - 64 - 24 - 59 - 36 - 88" -->
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
    </v-container>
    <!-- /Table -->

    <!-- dialog -->
    <v-dialog v-model="dialog" width="600" persistent>    
      <v-card>
      <v-card-title>
        <span v-if="!edition">Nuevo empleado</span>
        <span v-if="edition">Editar empleado</span>
      </v-card-title>
      <v-card-text>
      <v-form ref="form" lazy-validation>
        <v-row>
          <v-col cols="12" md="6">
          <v-text-field
            label="Nombre de usuario"
            :rules="nameRules"
            :error-messages="errors.username"
            v-model="form.username">
            <v-icon
              slot="prepend"
              color="#9c27b0">
              mdi-account
            </v-icon>
          </v-text-field>
          </v-col>

          <v-col cols="12" md="6">
          <v-text-field
            label="Número de caja"
            :rules="numberRules"
            v-model="form.terminal">
            <v-icon
              slot="prepend"
              color="#1de9b6">
              mdi-cash-register
            </v-icon>
          </v-text-field>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" md="6">
            <!-- If New Employee -->
            <v-text-field
              v-if="!edition"
              label="Contraseña"
              :rules="passwordRules"
              :type="show_password ? 'text' : 'password'"
              :append-icon="show_password ? 'mdi-eye' : 'mdi-eye-off'"
              @click:append="show_password = !show_password"
              v-model="form.password">
              <v-icon
                slot="prepend"
                color="#ff9800">
                mdi-lock
              </v-icon>
            </v-text-field>
            <!-- /If New Employee -->
            
            <!-- If EditMode -->
            <v-text-field
              v-if="edition"
              label="Nueva contraseña"
              :type="show_password ? 'text' : 'password'"
              :append-icon="show_password ? 'mdi-eye' : 'mdi-eye-off'"
              @click:append="show_password = !show_password"
              color="yellow"
              v-model="form.password">
              <v-icon
                slot="prepend"
                color="yellow">
                mdi-lock
              </v-icon>
            </v-text-field>
            <!-- /If EditMode -->

          </v-col>
          <v-col cols="12" md="6">
          <v-text-field
            type="tel"
            label="Teléfono"
            :rules="phoneRules"
            v-model="form.phone">
            <v-icon
              slot="prepend"
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
        <v-btn color="red" text @click="close">Cancelar</v-btn>

        <!-- If New Employee -->
        <v-btn v-if="!edition" 
          color="green" text 
          @click="submit_record"
          ref="save_btn">
          Guardar
        </v-btn>
        <!-- /If New Employee -->

        <!-- If EditMode -->
        <v-btn v-if="edition"
          color="green" text 
          @click="update_record"
          ref="save_btn">
          Guardar cambios
        </v-btn>
        <!-- /If EditMode -->

      </v-card-actions>
      </v-card>
    </v-dialog>
    <!-- /dialog -->

    <!-- snackbar -->
    <v-snackbar v-model="snackbar">
      {{ text }}
      <template v-slot:action="{ attrs }">
        <v-btn
          color="pink"
          text
          v-bind="attrs"
          @click="snackbar = false">
          Cerrar
        </v-btn>
      </template>
    </v-snackbar>
    <!-- /snackbar -->

    <!-- delete confirmation -->
    <v-dialog v-model="confirmation" max-width="435px">
      <v-card>
        <v-card-title class="text-h5">Seguro quieres borrar este registro?</v-card-title>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="red" text @click="close_dialog">Cancelar</v-btn>
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
      v => !!v || 'Se requiere un nombre de usuario'
    ],
    passwordRules: [
      v => !!v || 'Se requiere una contraseña'
    ],
    numberRules: [
      v => !!v || 'Se requiere asignar una caja',
      v => /^\d+$/.test(v) || 'Debe ser un valor númerico',
    ],
    phoneRules: [
      v => !!v || 'Se requiere un número de contacto',
      v => /^\d+$/.test(v) || 'Debe ser un valor númerico',
    ]
  }),
  computed: {
    ...mapGetters({
      employees: 'employee/employees',
      errors:    'employee/errors',
      created:   'employee/created',

      editmode:  'employee/editmode',
      edition:   'employee/edition'
    }),
    headers () {
      return [
        {
          text: 'Nombre',
          align: 'start',
          sortable: false,
          value: 'username',
          width: '25%'
        },
        {
          text: 'Caja',
          value: 'terminal',
          align: 'left', 
          width: '25%'
        },
        { 
          text: 'Fecha de ingreso', 
          value: 'created_at', 
          align: 'left', 
          width: '25%'
        },
        { 
          text: 'Contacto', 
          value: 'phone', 
          sortable: false, 
          width: '25%' 
        },
        { 
          text: 'Acciónes', 
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
            this.text = `Se ha registrado un nuevo empleado!`
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
        this.form = this.editmode
        this.open()
      })
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
<template>
  <div class="stock">   
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
          <v-btn @click.stop="open(), toggle_edition(false)"
            color="teal darken-1 mb-2 float-right ml-4 mt-4">
            <v-icon>mdi-plus</v-icon>
            Add
          </v-btn>
        </v-card-title>
        <v-data-table
          :headers="headers"
          :items="stocks"
          :height="windowSize.y - 64 - 24 - 59 - 36 - 28"
          fixed-header
          :search="search"
          :items-per-page="10"
          :sort-desc="true"
          class="mt-n3">
          <template v-slot:[`item.price`]="{ item }">
            {{ item.price | toUYU }}
          </template>
          <template v-slot:[`item.points`]="{ item }">
            {{ item.points | plus }}
          </template>
          <template v-slot:[`item.actions`]="{ item }">
            <v-btn
              icon 
              @click="loading(item.id), edit(item.id)" 
              :loading="loader === item.id">
              <v-icon small class="edit-icon">
                mdi-archive-edit
              </v-icon>
            </v-btn>

            <v-btn
              icon>
              <v-icon small class="sale-icon">
                mdi-sale
              </v-icon>
            </v-btn>

            <v-btn
              icon>
              <v-icon small class="points-icon">
                mdi-diamond-stone
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
          <span v-if="!barcode && !found">Scan or type a barcode</span>
          <span v-if="!edition && barcode && !found">New article</span>
          <span v-if="!edition && barcode && found">Add stock</span>
          <span v-if="edition && barcode && !found">Edit stock</span>
        </v-card-title>

        <v-card-text>
          <!-- Scanner -->
          <v-row>
            <v-col cols="12" md="12" v-if="!barcode && !edition">
              <v-text-field
                type="text"
                color="success"
                ref="inputcode"
                class="mb-n8"
                loading
                :error-messages="errors.barcode"
                @keyup.enter.prevent="scan"
                v-model="form.barcode">
                <v-icon
                  slot="prepend-inner"
                  color="success">
                  mdi-barcode
                </v-icon>
              </v-text-field>
            </v-col>
          </v-row>
          <!-- /Scanner -->

          <v-form ref="form" lazy-validation class="mt-4">
            <!-- Add stock -->
            <v-row> 
              <v-col cols="12" md="6" v-if="found">
                <v-text-field
                  outlined
                  label="Name"
                  :rules="nameRules"
                  :error-messages="errors.name"
                  disabled
                  v-model="form.name">
                  <v-icon
                    slot="prepend-inner"
                    color="#9c27b0">
                    mdi-form-textbox
                  </v-icon>
                </v-text-field>
              </v-col>
              <v-col cols="12" md="6" v-if="found">
                <v-autocomplete
                  outlined
                  return-object
                  :items="providers"
                  item-text="company"
                  item-value="id"
                  label="Select provider"
                  :rules="providerRules"
                  :error-messages="errors.provider_id"
                  @input="set_provider"
                  v-model="selected_provider">
                  <v-icon
                    slot="prepend-inner"
                    color="#795548">
                    mdi-truck
                  </v-icon>
                </v-autocomplete>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" md="6" v-if="found">
                <v-text-field
                  outlined
                  label="Buy price"
                  :rules="costRules"
                  :error-messages="errors.buy_price"
                  v-model="form.buy_price">
                  <v-icon
                    slot="prepend-inner"
                    color="#d32f2f">
                    mdi-currency-usd-off
                  </v-icon>
                </v-text-field>
              </v-col>

              <v-col cols="12" md="6" v-if="found">
                <v-text-field
                  outlined
                  label="Quantity"
                  :rules="quantityRules"
                  :error-messages="errors.quantity"
                  v-model="form.quantity">
                  <v-icon
                    slot="prepend-inner"
                    color="#ffc107">
                    mdi-archive-plus
                  </v-icon>
                </v-text-field>
              </v-col>
            </v-row>
            <!-- /Add stock -->

            <!-- /New Article -->
            <v-row v-if="barcode && !found" class="mb-n8">
              <v-col cols="12" md="6">
                <v-text-field
                  outlined
                  label="Name"
                  :rules="nameRules"
                  v-model="form.name">
                  <v-icon
                    slot="prepend-inner"
                    color="#9c27b0">
                    mdi-form-textbox
                  </v-icon>
                </v-text-field>
              </v-col>

              <v-col cols="12" md="6" v-if="!found">
                <v-text-field
                  outlined
                  label="Measure" 
                  v-model="form.measure"
                  :rules="measureRules"
                  :error-messages="errors.measure">
                  <v-icon
                    slot="prepend-inner"
                    color="#2196F3">
                    mdi-scale-unbalanced
                  </v-icon>
                </v-text-field>
              </v-col>
            </v-row>

            <v-row v-if="barcode && !edition && !found" class="mb-n8"> 
              <v-col cols="12" md="6">
                <v-autocomplete
                  outlined
                  return-object
                  :items="providers"
                  item-text="company"
                  label="Provider"
                  return-object
                  :rules="providerRules"
                  :error-messages="errors.provider_id"
                  @input="set_provider"
                  v-model="selected_provider">
                  <v-icon
                    slot="prepend-inner"
                    color="#795548">
                    mdi-truck
                  </v-icon>
                </v-autocomplete>
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  outlined
                  label="Buy price"
                  :rules="costRules"
                  :error-messages="errors.buy_price"
                  v-model="form.buy_price">
                  <v-icon
                    slot="prepend-inner"
                    color="#d32f2f">
                    mdi-currency-usd-off
                  </v-icon>
                </v-text-field>
              </v-col>
            </v-row>

            <v-row v-if="barcode && !found" class="mb-n8">
              <v-col cols="12" md="6" v-if="!found">
                <v-text-field
                  outlined
                  label="Sell price"
                  v-model="form.price"
                  :rules="priceRules"
                  :error-messages="errors.price">
                  <v-icon
                    slot="prepend-inner"
                    color="#4caf50">
                    mdi-currency-usd
                  </v-icon>
                </v-text-field>
              </v-col>
              <v-col cols="12" md="6" v-if="!found">
                <v-text-field
                  outlined
                  label="Quantity"
                  :rules="quantityRules"
                  :error-messages="errors.quantity"
                  v-model="form.quantity">
                  <v-icon
                    slot="prepend-inner"
                    color="#ffc107">
                    mdi-archive-plus
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
          <v-btn v-if="!edition && barcode && !found" 
            color="teal darken-2"
            @click="submit_record"
            ref="save_btn">
            Save
          </v-btn>
          <!-- /If New Employee -->

          <!-- If EditMode -->
          <v-btn v-if="edition && barcode && !found"
            color="teal darken-2"
            @click="update_record"
            ref="save_btn">
            Save changes
          </v-btn>
          <!-- /If EditMode -->

          <!-- If found -->
          <v-btn v-if="!edition && barcode && found"
            color="teal darken-2"
            @click="update_stock"
            ref="save_btn">
            Save
          </v-btn>
          <!-- /If found -->
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
  name: 'stock',
  data: ()  => ({
    form: {},
    search: '',
    show_password: false,
    selected_provider: null,
    
    loader: null,

    index: null,
    dialog: false,
    confirmation: false,

    snackbar: false,

    barcode: false,
    found: false,

    text: null,

    windowSize: {
      y: 0,
    },

    barcodeRules: [
      v => !!v || 'Barcode is required'
    ],
    nameRules: [
      v => !!v || 'Username is required'
    ],
    providerRules: [
      v => !!v || 'Please select a provider'
    ],
    costRules: [
      v => !!v || 'Buy price is required',
      v => /^\d+$/.test(v) || 'It must be a numeric value'
    ],
    priceRules: [
      v => !!v || 'Sell price is required',
      v => /^\d+$/.test(v) || 'It must be a numeric value'
    ],
    measureRules: [
      v => !!v || 'You must especify a measure'
    ],
    quantityRules: [
      v => !!v || 'Quantity is required',
      v => /^\d+$/.test(v) || 'It must be a numeric value'
    ],
  }),
  computed: {
    ...mapGetters({
      providers:'provider/providers',
      stocks:   'stock/stocks',
      
      stock:    'stock/stock',

      errors:   'stock/errors',
      created:  'stock/created',

      data:     'stock/data',
      edition:  'stock/edition'
    }),
    headers () {
      return [
        {
          text: 'Article',
          align: 'start',
          sortable: false,
          value: 'name',
          width: '16.6666667%'
        },
        {
          text: 'Measure',
          value: 'measure',
          width: '16.6666667%'
        },
        { 
          text: 'Price', 
          value: 'price',  
          width: '16.6666667%'
        },
        { 
          text: 'Stock', 
          value: 'stock', 
          width: '16.6666667%'
        },
        { 
          text: 'Discount', 
          value: 'discount', 
          width: '16.6666667%'
        }, 
        { 
          text: 'Points', 
          value: 'points', 
          width: '16.6666667%'
        }, 
        { 
          text: 'Actions', 
          value: 'actions', 
          sortable: false, 
          align: 'center', 
          width: '190px' },
      ]
    }
  },
  methods: {
    ...mapActions({
      get_providers:  'provider/populate',
      populate:       'stock/populate',
      register:       'stock/register',
      retrieve:       'stock/retrieve',
      update:         'stock/update',
      delete:         'stock/delete',

      toggle_status:  'stock/toggle_status',
      toggle_edition: 'stock/toggle_edition',
      clear:          'stock/clear',
      barcode_scan:   'stock/barcode_scan',
      buy:            'stock/buy'
    }),

    onResize () {
      this.windowSize = { y: window.innerHeight }
    },

    loading(id) {
      this.loader = id
    },

    set_provider: function () {
      if(this.selected_provider) {
        this.form.provider_id = this.selected_provider.id
      }
    },

    scan() {
      if(!this.form.barcode) {
        this.$refs.form.validate()
      } else {
        this.barcode_scan(this.form.barcode)
        .then(() => {
          if(this.stock) {
            this.form.barcode = this.stock.barcode
            this.form.name = this.stock.name
            this.form.id = this.stock.id
            this.selected_provider = null
            this.found = true
            this.barcode = true
          } else {
            this.barcode = true
          }
        })
      }
    },

    open() {
      if(!this.barcode) {
        if(this.providers != '') {
          setTimeout(() => this.$refs.inputcode.$refs.input.focus(), 100)
        } else {
          this.text = `You must have at least 1 registered provider`
          this.snackbar = true
        }
      }
      if(this.providers != '') { 
        this.dialog = true 
      }
      this.loader = null
      if(this.$refs.form) { this.$refs.form.resetValidation() }
    },

    close() {
      this.$refs.form.reset()
      this.$refs.form.resetValidation()
      this.clear()
      this.barcode = false
      this.form.barcode = null
      this.selected_provider = {}
      this.found = false
      this.dialog = false
    },

    close_dialog() {
      this.confirmation = false
    },

    submit_record() {
      const valid = this.$refs.form.validate()
      if(valid) {
        this.register(this.form)
        .then(() => {
          if(this.created === true) {
            this.text = `New article added to stock!`
            this.snackbar = true
            this.close()
          }
          this.toggle_status(false)
        })
      }
    },

    async edit(id) {
      this.barcode = true
      await this.retrieve(id)
      .then(() => {
        this.toggle_edition(true)
        this.form = this.data
        this.form.quantity = this.data.stock
        this.open()
      })
    },

    update_record() {
      this.form.stock = this.form.quantity
      this.update(this.form)
      .then(() => {
        this.text = `Registry updated!`
        this.snackbar = true
        this.close()
      })
    },

    update_stock() {
      const valid = this.$refs.form.validate()
      if(valid) {
        this.buy(this.form)
        .then(() => {
          this.text = `Registry update!`
          this.snackbar = true
          this.close()
        })
      }
    },

    remove(item) {
      this.index = this.stocks.indexOf(item)
      this.trash = item
      this.confirmation = true
    },

    delete_item() {
      this.stocks.splice(this.index, 1)
      this.delete(this.trash)
      this.trash = null
      this.close_dialog()
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
    this.get_providers()
  },
}
</script>
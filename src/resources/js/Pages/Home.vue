<template>
  <div class="home">
    <navbar></navbar>
    <v-container fluid class="main-container">
      <div class="left-container">
        <div class="main">
          <div class="inner">
            <v-row class="order-list ma-0 pa-0" v-for="(order) in orders" :key="order.id">
              <v-col cols="12" md="4" class="ma-0 pa-1">
                <div class="order-item">
                  <div class="article-name">
                    {{ order.name }}
                  </div>
                  <div class="article-measure">
                    {{ order.measure }}
                  </div>
                </div>
              </v-col>

              <v-col cols="12" md="4" class="ma-0 pa-1">
                <div class="article-quantity">
                  {{ order.quantity | toQuantity }}
                </div>
              </v-col>

              <v-col cols="12" md="4" class="ma-0 pa-1">
                <div class="article-discount" v-if="order.discount">
                  {{ order.discount | minus }}
                </div>
                
                <div class="article-price" :class="order.discount ? '': 'mt-2'">
                  {{ order.amount | toUYU }}
                </div>
              </v-col>
              <hr class="bottom-line"></hr>
            </v-row>
          </div>          
        </div>

        <div class="last-item text-center" v-if="last_item">
          <span class="mr-1">
            {{ last_item.name }} 
          </span>
          <span class="li-measure mr-1">
            {{ last_item.measure }} 
          </span>
          <span class="li-quantity mr-1">
            {{ last_item.quantity | toQuantity }} 
          </span>
          <span class="li-price mr-1">
            {{ last_item.price | toUYU }}
          </span>
        </div>

        <div class="invoice text-center" v-if="invoice">
          <div class="invoice-price">
           TOTAL {{ invoice.total | toUYU }}
          </div>

          <div class="invoice-ticket">
            {{ invoice.order_number }}
          </div>

          <div class="invoice-discount">
            {{ invoice.discount | minus }}
          </div>

          <div class="invoice-subtotal">
            Subtotal {{ invoice.subtotal | toUYU }}
          </div>

          <div class="invoice-items">
            Articles {{ invoice.items | toWrap }}
          </div>
        </div>
      </div>

      <div class="right-container">
        <v-text-field type="text" 
          v-click-outside="set_focus"
          v-model="barcode" 
          ref="inputcode"
          color="transparent"
          class="barcodeinput"
          @keyup.enter.prevent="scan">
        </v-text-field>
        <!-- /Barcode Scanner -->

        <div class="actions-container">
          <v-row>
            <v-col cols="12" md="6" class="custom-padding">
              <div class="action-button">
                <v-btn
                  icon
                  rounded
                  outlined
                  color="#F44336">
                  <v-icon>mdi-archive-remove</v-icon>
                </v-btn>
                <div class="action-text">
                  <div class="noselect return-btn">
                    Return
                  </div>
                </div>
              </div>
            </v-col>

            <v-col cols="12" md="6" class="custom-padding">
              <div class="action-button">
                <v-btn
                  icon
                  rounded
                  outlined
                  color="#00BCD4">
                  <v-icon>mdi-sale</v-icon>
                </v-btn>
                <div class="action-text">
                  <div style="text-align:center !important" class="noselect">
                    Discount
                  </div>
                </div>
              </div>
            </v-col>
          </v-row>

          <v-row class="mt-1">
            <v-col cols="12" md="6" class="custom-padding">
              <div class="action-button">
                <v-btn
                  icon
                  rounded
                  outlined
                  color="#C0CA33">
                  <v-icon>mdi-refresh</v-icon>
                </v-btn>
                <div class="action-text">
                  <div style="text-align:center !important" class="noselect">
                    Restart Sale
                  </div>
                </div>
              </div>
            </v-col>

            <v-col cols="12" md="6" class="custom-padding">
              <div class="action-button">
                <v-btn
                  icon
                  rounded
                  outlined
                  color="#FF9800">
                  <v-icon>mdi-magnify</v-icon>
                </v-btn>
                <div class="action-text">
                  <div style="text-align:center !important" class="noselect">
                    Search
                  </div>
                </div>
              </div>
            </v-col>
          </v-row>

          <v-row class="mt-1">
            <v-col cols="12" md="6" class="custom-padding">
              <div class="action-button">
                <v-btn
                  icon
                  rounded
                  outlined
                  color="#AA00FF">
                  <v-icon>mdi-calculator</v-icon>
                </v-btn>
                <div class="action-text">
                  <div style="text-align:center !important" class="noselect">
                    Quantity
                  </div>
                </div>
              </div>
            </v-col>

            <v-col cols="12" md="6" class="custom-padding">
              <div class="action-button">
                <v-btn
                  icon
                  rounded
                  outlined
                  color="#607D8B">
                  <v-icon>mdi-account-plus</v-icon>
                </v-btn>
                <div class="action-text">
                  <div style="text-align:center !important" class="noselect">
                    Customer
                  </div>
                </div>
              </div>
            </v-col>
          </v-row>

          <v-row class="mt-1">
            <v-col cols="12" md="12" class="custom-padding">
              <div class="action-button">
                <v-btn
                  icon
                  rounded
                  outlined
                  x-large
                  color="#4DB6AC"
                  ripple="false">
                  <v-icon>mdi-cash-multiple</v-icon>
                </v-btn>
                <div class="action-text">
                  <div class="noselect charge-btn">
                    Charge
                  </div>
                </div>
              </div>
            </v-col>
          </v-row>
        </div>
      </div>
    </v-container>
  </div>
</template>

<script>
import navbar from '../Components/Navbar'
import {mapActions, mapGetters} from 'vuex'

export default {
  name: 'home',
  components: {
    navbar
  },

  data:() => ({
    barcode: null,
    form: [],
    remove: false,
  }),

  computed: {
    ...mapGetters({
      orders:       'order/orders',
      order_number: 'order/order_number',
      invoice:      'invoice/invoice'
    }),

    last_item() {
      return this.orders[this.orders.length - 1];
    }
  },

  methods: {
    ...mapActions({
      getcode:    'order/getcode',
      setcode:    'order/setcode',
      setinvoice: 'invoice/retrieve'
    })
  },

  async created() {
    if(this.order_number == null) {
      await this.getcode()
    }
  }
}
</script>
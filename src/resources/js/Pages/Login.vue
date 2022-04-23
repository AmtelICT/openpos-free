<template>
  <div class="login">
    <v-container>
      <v-card class="login-container internal">
        <v-row class="vlogin">
          <v-col cols="12" md="4" class="brand-login">
            <v-img
              src="img/pos.png"
              class="img-login">
            </v-img>
          </v-col>

          <v-col cols="12" md="8">
            <v-card-text>
              <div class="brand-title login-title">
                OpenPOS
              </div>

              <p class="login-subtitle">
                Sistema de punto de venta y control de stock 
              </p>
            </v-card-text>

            <v-card-text class="form-container">
              <v-form ref="form" lazy-validation>
                <v-text-field
                  label="Nombre de usuario"
                  :rules="nameRules"
                  :error-messages="errors.username"
                  v-model="form.username">
                  <v-icon
                    slot="prepend"
                    color="primary">
                    mdi-account
                  </v-icon>
                </v-text-field>

                <v-text-field
                  label="Contraseña"
                  :type="show_password ? 'text' : 'password'"
                  :append-icon="show_password ? 'mdi-eye' : 'mdi-eye-off'"
                  @click:append="show_password = !show_password"
                  :rules="passwordRules"
                  :error-messages="errors.password"
                  v-model="form.password">
                  <v-icon
                    slot="prepend"
                    color="primary">
                    mdi-lock
                  </v-icon>
                </v-text-field>
              </v-form>
            </v-card-text>

            <v-card-actions class="login-actions">
              <p class="password-link">
                Olvidaste tu contraseña?
              </p>  
              <v-btn
                color="green accent-4"
                text
                class="login-button"
                @click.prevent="submit">
                Login
              </v-btn>
            </v-card-actions>
          </v-col>
        </v-row>
      </v-card>
    </v-container>
  </div>
</template>

<script>
import {mapGetters, mapActions} from 'vuex'

export default {
  name: 'login',

  data: () => {
    return {
      form: {},
      show_password: false,
      nameRules: [
        v => !!v || 'Username is required'
      ],
      passwordRules: [
        v => !!v || 'Password is required'
      ]
    }
  },

  computed: {
    ...mapGetters({
      authenticated: 'auth/authenticated',
      errors:        'auth/errors'
    })
  },

  methods: {
    ...mapActions({
      login:        'auth/login',
      clear:        'auth/clear'
    }),

    submit() {
      const valid = this.$refs.form.validate()
      if(valid) {
        this.login(this.form)
        .then(() => {
          if(this.authenticated) {
            this.$router.push('/')
          }
        })
      }
    }
  }
}
</script>

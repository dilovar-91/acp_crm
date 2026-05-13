<template>
  <div>
    <v-card class="text-center pa-1">
      <v-card-title class="justify-center display-1 mb-2"
        >Добро пожаловать</v-card-title
      >
      <v-card-subtitle>Войдите в свой аккаунт</v-card-subtitle>

      <!-- sign in form -->
      <v-card-text>
        <v-form ref="form" v-model="isFormValid" lazy-validation>
          <v-text-field
            v-model="email"
            :rules="[rules.required]"
            :validate-on-blur="false"
            :error="error"
            label="Email"
            name="email"
            autocomplete="email"
            outlined
            @keyup.enter="submit"
            @change="resetErrors"
          ></v-text-field>

          <v-text-field
            v-model="password"
            :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
            :rules="[rules.required]"
            :type="showPassword ? 'text' : 'password'"
            :error="error"
            :error-messages="errorMessages"
            label="Пароль"
            name="password"
            outlined
            autocomplete="current-password"
            @change="resetErrors"
            @keyup.enter="submit"
            @click:append="showPassword = !showPassword"
          ></v-text-field>

          <v-otp-input
            length="6"
            v-if="false"
            v-model="code"
            :rules="[rules.required]"
            :error="error"
            :error-messages="errorMessages"
            label="Код"
            name="password"
            type="password"
            autocomplete="current-password"
            outlined
            @change="resetErrors"
            @keyup.enter="submit"
          ></v-otp-input>

          <v-btn
            :loading="isLoading"
            :disabled="isSignInDisabled"
            block
            x-large
            color="primary"
            @click="submit"
            >Войти
          </v-btn>

          <!--<div class="mt-5">
            <router-link :to="'/auth/forgot-password'">
              Забылы пароль
            </router-link>
          </div>-->
        </v-form>
      </v-card-text>
    </v-card>
  </div>
</template>

<script>
/*
|---------------------------------------------------------------------
| Sign In Page Component
|---------------------------------------------------------------------
|
| Sign in template for user authentication into the application
|
*/
export default {
  name: 'AuthSignIn',
  layout: 'auth',
  data() {
    return {
      // sign in buttons
      isLoading: false,
      isSignInDisabled: false,

      // form
      isFormValid: true,
      email: '',
      password: '',
      code: '',
      domain: '',

      // form error
      error: false,
      errorMessages: '',

      errorProvider: false,
      errorProviderMessages: '',

      // show password field
      showPassword: false,

      providers: [
        {
          id: 'google',
          label: 'Google',
          isLoading: false,
        },
        {
          id: 'facebook',
          label: 'Facebook',
          isLoading: false,
        },
      ],

      // input rules
      rules: {
        required: (value) => (value && Boolean(value)) || 'Required',
      },
    }
  },
  methods: {
    submit() {
      if (this.$refs.form.validate()) {
        this.isLoading = true
        this.isSignInDisabled = true
        this.signIn()
      }
    },
    async signIn() {
      await this.$auth
        .loginWith('laravelSanctum', {
          data: {
            email: this.email,
            password: this.password,
            code: this.code,
            domain: window.location.hostname,
          },
        })
        .then(() => {
          this.$toast.success('Вы успешно вошли')
        })
        .catch((error) => {
          const code = parseInt(error.response && error.response.status)
          if (code === 401) {
            this.$toast.error('Вы ввели неправильный логин или пароль или код')
          } else this.$toast.error('Ошибка в сервере: ' + error)
          this.isLoading = false
          this.isSignInDisabled = false
        })
    },
    signInProvider(provider) {},
    resetErrors() {
      this.error = false
      this.errorMessages = ''
      this.errorProvider = false
      this.errorProviderMessages = ''
    },
  },
  computed: {
    isAccasDomain() {
      console.log(window.location.hostname)
      return window.location.hostname !== 'a-c77.ru'
    },
  },
}
</script>

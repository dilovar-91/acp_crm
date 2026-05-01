<template>
  <div>
    <BreadCrumb :items="links" />

    <v-row no-gutters align="start" class="d-flex">
      <v-col cols="12">
        <v-img
          src="/images/background-user.jpg"
          height="180px"
        >
          <v-row
            align="center"
            justify="center"
            class="lightbox white--text pa-2"
          >
            <v-col align="center">
              <v-avatar size="80">
                <img
                  src="/user-icon.jpg"
                  alt="Avatar"
                >
              </v-avatar>
              <div class="subheading mt-2">
                {{ user.firs_tname || "" }} ({{ $auth.user.role }})
              </div>
              <div class="body-1">
                {{ user.email }}
              </div>
            </v-col>
          </v-row>
        </v-img>
      </v-col>
    </v-row>

    <v-container fluid>
      <v-row no-gutters align="start" class="d-flex">
        <v-col cols="12">
          <v-card class="mx-auto">
            <v-app-bar class="grey lighten-5" elevate-on-scroll dense>
              <v-toolbar-title>Данные пользователя</v-toolbar-title>
              <v-spacer />
            </v-app-bar>
            <v-card-text>
              <v-form ref="form" v-model="valid" :lazy-validation="false">
                <v-container>
                  <v-row>
                    <v-col cols="12" md="6">
                      <v-text-field v-model="form.name" label="Имя" prepend-icon="mdi-guy-fawkes-mask" placeholder="Введите имя" />
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field v-model="form.email" label="Email" prepend-icon="mdi-email" placeholder="Введите Email" />
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-menu
                        v-model="menu1"
                        :close-on-content-click="false"
                        :nudge-right="40"
                        transition="scale-transition"
                        offset-y
                        min-width="290px"
                      >
                        <template #activator="{ on, attrs }">
                          <v-text-field
                            v-model="form.birthday"
                            label="День рождение"
                            prepend-icon="mdi-calendar"
                            readonly
                            v-bind="attrs"
                            v-on="on"
                          />
                        </template>
                        <v-date-picker
                          v-model="form.birthday"
                          locale="ru-RU"
                          @input="menu1 = false"
                        />
                      </v-menu>
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-file-input
                        :rules="rules"
                        show-size
                        accept="image/png, image/jpeg, image/bmp"
                        placeholder="Выберите рисунка"
                        prepend-icon="mdi-camera"
                        label="Аватар"
                      />
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field v-model="form.password" label="Пароль" prepend-icon="mdi-lock" type="password" autocomplete="new-password" />
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field v-model="form.password_confirmation" label="Повторите пароль" prepend-icon="mdi-lock" type="password" autocomplete="new-password" :rules="[((form.password === '' ) || form.password === form.password_confirmation) || 'Пароли должны совпадать!']" />
                    </v-col>
                    <v-col cols="12" md="12">
                      <v-btn
                        :disabled="!valid"
                        color="success"
                        class="mr-4"
                        @click="save()"
                      >
                        Сохранить
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-container>
              </v-form>
            </v-card-text>
          </v-card>
        </v-col>
        <v-snackbar
          v-model="snackbar"
          :color="color"
          :right="'right'"
          :timeout="timeout"
          :top="'top'"
          outlined
        >
          {{ text }}
          <template #action="{ attrs }">
            <v-btn icon color="deep-orange" @click="snackbar = false">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </template>
        </v-snackbar>
      </v-row>
    </v-container>
  </div>
</template>
<script>
import BreadCrumb from '~/components/BreadCrumb'
export default {
  name: 'Prism',
  components: { BreadCrumb },
  layout ({$auth}) {return $auth.user.role_id === 4 ? 'agency' : 'user' },
  middleware: 'auth',
  data: () => ({
    valid: true,
    menu1: false,
    menu2: false,
    search_page: '',
    search_number: '',
    form: {
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
      birthday: '',
      pic: ''
    },
    rules: [
      value => !value || value.size < 1000000 || 'Размер аватара не должен превышать 1 МБ.'
    ]
  }),

  computed: {
    user () {
      return this.$store.state.auth.user
    },

    role () {
      return this.$store.state.auth.role
    },
    links () {
      return [
        {
          text: 'Главная',
          disabled: false,
          href: '/'
        },
        {
          text: 'Профиль',
          disabled: true,
          href: '/'
        }
      ]
    }
  },

  created () {
    this.form.id = this.user.id
    this.form.name = this.user.name
    this.form.email = this.user.email
    this.form.pic = this.user.pic
    this.form.birthday = this.user.birthday
  },

  methods: {
    save () {
      if (this.valid) {
        this.$store.dispatch('auth/saveUser', {
          item: this.form
        }).catch((error) => {
          this.$notify('error', 'Ошибка: ' + error)
        })
        this.$notify('success', 'Данные изменены')
      } else { this.$notify('error', 'Заполните обязательные поля') }
    },
    validate () {
      this.$refs.form.validate()
    },
    reset () {
      this.$refs.form.reset()
    },
    resetValidation () {
      this.$refs.form.resetValidation()
    }
  }
}
</script>

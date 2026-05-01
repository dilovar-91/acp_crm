<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links"/>

      <v-row no-gutters align="start" class="d-flex">
        <v-col cols="12" class="mt-1">
          <v-app-bar elevate-on-scroll dense>
            <v-toolbar-title>Активность пользователей</v-toolbar-title>
            <v-spacer/>
            <v-col cols="12" sm="6" md="2" class="hidden-sm-and-down">
              <date-picker
                v-model="filter_from"
                value-type="YYYY-MM-DD HH:mm"
                format="DD.MM.Y HH:mm"
                type="datetime"
                placeholder="Дата с"
                style="width: 100%; margin-top: 4px;"
                @clear="clearFilter()"
              />
            </v-col>
            <v-col cols="12" sm="6" md="2" class="hidden-sm-and-down">
              <date-picker
                v-model="filter_to"
                type="datetime"
                value-type="YYYY-MM-DD HH:mm"
                format="DD.MM.Y HH:mm"
                placeholder="Дата по"
                style="width: 100%; margin-top: 4px;"
                @clear="clearFilter()"
              />
            </v-col>
            <v-col cols="12" sm="6" md="2" class="hidden-sm-and-down">
              <v-autocomplete
                v-model="showroom_id"
                :items="showrooms"
                hide-details
                item-text="name"
                item-value="id"
                label="Шоурум"
                @change="reloadUsers()"
                menu-props="auto"
                outlined
                clearable
                required
                class="mr-2"
                dense
                @click:clear="
                          $nextTick(() => (showroom_id = null))
                        "
              >
              </v-autocomplete>
            </v-col>
            <v-col cols="12" sm="6" md="2" class="hidden-sm-and-down">
              <v-autocomplete
                v-model="user_id"
                :items="users"
                hide-details
                :item-text="item => item.first_name +' '+ item.last_name + '(' + item.role?.title + ')' +' -  '+ item.email  "
                item-value="id"
                label="Пользователи"
                menu-props="auto"
                outlined
                clearable
                required
                class="mr-2"
                dense
                @click:clear="
                          $nextTick(() => (user_id = null))
                        "
              >
              </v-autocomplete>
            </v-col>
            <v-col cols="12" sm="6" md="1" class="hidden-sm-and-down">
              <v-btn color="success" dark class="mb-2 mt-1" @click="execute_filter()">
                Применить
              </v-btn>
            </v-col>
            <v-col cols="12" sm="6" md="1" class="hidden-sm-and-down">
              <v-btn color="error" dark class="mb-2 mt-1" @click="clearFilter()">
                Сбросить
              </v-btn>
            </v-col>


          </v-app-bar>
        </v-col>
      </v-row>
      <v-row dense>
        <v-col cols="12" md="12" sm="12">
          <v-data-table
            :key="componentKey"
            class="elevation-1 myTable  grey lighten-3"
            :headers="headers"
            fixed-header
            :items-per-page="activities.length"
            height="80vh"
            :hide-default-footer="true"
            dense
          >
            <template v-slot:body>
              <tbody>
              <template v-for="(item) in activities.data">
                <tr
                  :key="'row-' + item.id"
                >
                  <td>{{ item.id }}</td>
                  <td>
                    {{ item.user?.first_name }} {{ item.user?.last_name }}
                  </td>
                  <td>
                    {{ item.user?.email }}
                  </td>
                  <td>
                    {{ item.user?.showroom?.name }}
                  </td>
                  <td class="text-center">
                    <a :href="'https://dnschecker.org/ip-location.php?ip=' + item.ip_address" target="_blank" rel="noreferrer">
                      <v-chip
                        v-if="!known_ips.includes(item.ip_address)"
                        class="ma-2"
                        color="red"
                        text-color="white"
                        style="cursor: pointer !important;"
                      >
                        {{ item.ip_address }}
                      </v-chip>
                      <v-chip
                        v-else
                        class="ma-2"
                        color="green"
                        outlined
                      >

                        {{ item.ip_address }}
                      </v-chip>

                    </a>


                  </td>
                  <td>
                    Платформа: {{ item.user_agent?.platform }} <br>
                    Браузер: {{ item.user_agent?.browser }}
                  </td>
                  <td>
                    <a target="_blank"
                       :href="'https://yandex.ru/maps/?ll='+item.location?.lon+',' + item.location?.lat +'&z=12'" rel="noreferrer">
                      <template>
                        <v-icon color="success">mdi-map-marker</v-icon>
                        {{ item.location?.city }}
                      </template>
                    </a>
                  </td>
                  <td>
                    <template v-if="item.login_at !== null">
                      <v-icon color="success">mdi-login</v-icon>
                      Вход: {{ $moment(item.login_at).format('DD.MM.YYYY HH:mm') }}
                    </template>
                  </td>
                  <td>
                    <template v-if="item.logout_at !== null">
                      <v-icon color="error">mdi-logout</v-icon>
                      Выход: {{ $moment(item.logout_at).format('DD.MM.YYYY HH:mm') }}
                    </template>
                  </td>
                </tr>
              </template>
              </tbody>
            </template>
          </v-data-table>
          <div class="text-center pt-2">
            <span class="font-weight-bold">Всего: {{ itemCount }}</span>

            <v-row dense justify="center">
              <v-pagination
                v-model="page"
                color="blue"
                @input="changedPage"
                :length="pageCount"
                :total-visible="10"
              />
            </v-row>
          </div>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>
<script>
import BreadCrumb from '~/components/BreadCrumb'

export default {
  layout: 'crm',
  middleware: 'permission',
  components: {BreadCrumb},
  data: () => ({
    dialog: false,
    editedIndex: -1,
    componentKey: 0,
    showroom_id: null,
    user_id: null,
    filter_from: null,
    filter_to: null,
    showCode: false,
    valid: false,
    deleteId: '',
    search: null,
    page: 1,
    limit: null,
    known_ips: [
      '127.0.0.1',
      '185.180.126.130',
      '195.133.224.210',
      '178.176.75.136',
      '185.222.153.230',
      '5.35.41.162'
    ],
    per_pages: [
      10,
      30,
      50,
      100,
      150
    ],
    deleteDialog: false,
    headers: [
      {
        text: 'ID',
        value: 'number',
        sortable: false,
        fixed: true,
        width: '10px'
      },
      {
        text: 'Пользователь',
        align: 'start',
        sortable: false,
        value: 'date',
        fixed: true,
        width: '30px'
      },
      {
        text: 'Логин',
        align: 'start',
        sortable: false,
        value: 'date',
        fixed: true,
        width: '30px'
      },
      {
        text: 'Салон',
        align: 'start',
        sortable: false,
        value: 'date',
        fixed: true,
        width: '30px'
      },
      {
        text: 'Телефон',
        align: 'start',
        sortable: false,
        value: 'phone',
        fixed: true,
        width: '150px'
      },

      {text: 'Платформа', value: 'agency?.name', width: '300px', sortable: false},
      {
        text: 'Местоположение',
        sortable: false,
        width: '300px',
        value: 'client_name',
        fixed: true
      },
      {text: 'Время входа', value: 'showroom?.name', width: '300px', sortable: false},

      {
        text: 'Время выхода',
        sortable: false,
        width: '300px',
        value: 'active',
        fixed: true
      },
    ],
    editedItem: {
      id: '',
      title: '',
      url: '',
      description: '',
      showroom_id: '',
      phone: '',
      agency_id: '',
      post_url: '',
      token: '',
      type_id: '',
    },
    defaultItem: {
      id: '',
      title: '',
      url: '',
      description: '',
      showroom_id: '',
      phone: '',
      agency_id: '',
      post_url: '',
      token: '',
      type_id: '',
    },
    phoneNumberRules: [
      v => !!v || 'Телефон объязательно',
      v => /^7\d{10}$/.test(v) || 'Введите номер в таком формате 79999999999',
    ],
  }),
  computed: {
    agencies() {
      return this.$store.state.showroom.agencies
    },
    users() {
      return this.$store.state.user.showroom_users
    },
    activities() {
      return this.$store.state.user.auth_activities
    },
    site_types() {
      return this.$store.state.showroom.site_types
    },
    showrooms() {
      return this.$store.state.showroom.showrooms
    },
    role() {
      return this.$store.state.auth.role
    },
    links() {
      return [
        {
          text: 'Главная',
          disabled: false,
          href: '/'
        },
        {
          text: 'Администрирование',
          disabled: false,
          href: '/admin'
        },
        {
          text: 'Активность пользователей',
          disabled: true,
          href: '/admin/auth-activities'
        }
      ]
    },
    formTitle() {
      return this.editedIndex === -1 ? 'Добавление' : 'Изменение'
    },

    perPage() {
      if (this.activities()) {
        return this.activities.per_page
      } else {
        return this.activities.per_page
      }
    },
    pageCount() {
      return this.activities.last_page
    },
    itemCount() {
      if (this.activities) {
        return this.activities.total
      } else {
        return this.activities.total
      }
    }
  },
  async fetch({store, $route, params: {id},},) {
    await store.dispatch('showroom/fetchShowrooms', {id})
    await store.dispatch('user/fetchUsersByShowroom', {})
  },
  created() {

    if (this.$route.query?.showroom_id) {
      this.showroom_id = parseInt(this.$route.query.showroom_id)
    }

    if (this.$route.query?.page) {
      this.page = parseInt(this.$route.query.page)
    }

    if (this.$route.query?.user_id) {
      this.user_id = parseInt(this.$route.query.user_id)
    }
    this.filter_from = this.$route.query.filter_from
    this.filter_to = this.$route.query.filter_to

    this.refresh_page();
  },
  methods: {

    async refresh_page() {
      if (this.onProcess === true || document.hidden) {
        console.log('refresh on process or tab hidden')
        return
      }
      await this.$store.dispatch('user/fetchAuthActivities', {query: this.$route.query});
    },


    async changedPage() {
      try {
        await this.$router.push({query: {...query, page: this.page}});
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') {
          //
        }
      }
      await this.$store.dispatch('user/fetchAuthActivities', {query: this.$route.query});
    },

    async reloadUsers() {
      if (this.showroom_id !== 0) {
        await this.$store.dispatch('user/fetchUsersByShowroom', {showroom_id: this.showroom_id})
      }
    },

    async execute_filter() {
      try {
        await this.$router.push({
          query: {
            ...(this.showroom_id && {showroom_id: this.showroom_id}),
            ...(this.filter_from && {filter_from: this.filter_from}),
            ...(this.filter_to && {filter_to: this.filter_to}),
            ...(this.user_id && {user_id: this.user_id}),
            page: 1
          }
        });
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') {
        }
      } finally {
        await this.$store.dispatch('user/fetchAuthActivities', {query: this.$route.query});
        this.page = 1;
      }
    },
    async clearFilter() {

      this.filter_from = null
      this.filter_to = null
      this.showroom_id = null
      this.user_id = null
      this.page = 1
      try {
        await this.$router.push({
          query: {}
        });
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') {
        }
      } finally {
        await this.$store.dispatch('user/fetchAuthActivities', {
          query: {}
        })
      }
    },


  }
}
</script>

<style scoped>
table {
  border: none;
  border-collapse: collapse;
}

table td {
  border: 1px solid rgb(204, 200, 200);
}

table tfoot tr th {
  border: 1px solid rgb(204, 200, 200);
  padding: 0 12px !important;
}

.myTable > table > tfoot > tr > th {
  border: 1px solid rgb(204, 200, 200);
  padding: 0 12px !important;
}

pre {
  background: #333;
  white-space: pre;
  word-wrap: break-word;
  overflow: auto;
}

pre.code {
  margin: 5px 2px;
  border-radius: 4px;
  border: 1px solid #292929;
  position: relative;
}

pre.code label {
  font-family: sans-serif;
  font-weight: bold;
  font-size: 13px;
  color: #ddd;
  position: absolute;
  left: 1px;
  top: 15px;
  text-align: center;
  width: 60px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  pointer-events: none;
}

pre.code code {
  font-family: "Inconsolata", "Monaco", "Consolas", "Andale Mono", "Bitstream Vera Sans Mono", "Courier New", Courier, monospace;
  display: block;
  margin: 0 0 0 60px;
  padding: 5px 6px 4px;
  border-left: 1px solid #555;
  overflow-x: auto;
  font-size: 13px;
  line-height: 15px;
  color: #ddd;
}

pre.css-code code {
  color: #ff4242;
}

pre.html-code code {
  color: #00ca02;
}

pre.javascript-code code {
  color: #ff8000;
}

pre.jquery-code code {
  color: #1da1f2;
}

@media (max-width: 750px) {
  pre::after {
    content: '';
  }
}
</style>

<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links"/>
      <v-row justify="center" align="center" class="mt-0">
        <v-col cols="12">
          <v-card>
            <v-data-table
              :key="componentKey"
              ref="my-table"
              class="elevation-1 myTable grey lighten-3"
              :headers="headers"
              :items="items"
              :items-per-page="items.length"
              fixed-header
              height="80vh"
              :hide-default-footer="true"
              item-key="id"
            >
              <template #body="{ items }">
                <tbody>
                <tr
                  v-for="(item, i) in items"
                  :key="item.id"
                  @dblclick="editItem(item)"
                >
                  <td>{{ i + 1 }}</td>
                  <td>
                    {{ item.first_name }} {{ item.last_name }}
                  </td>
                  <td>
                    {{ item.email }}
                  </td>
                  <td>
                    {{ item.showroom?.name }}
                  </td>
                  <td>
                    {{ item.role?.title }}
                  </td>
                  <td>
                    {{ $moment(item.created_at).format("DD.MM.YYYY HH:mm") }}
                  </td>
                  <td>
                    {{ $moment(item.updated_at).format("DD.MM.YYYY HH:mm") }}
                  </td>
                  <td>
                    <v-icon color="warning" class="mr-2" @click="editItem(item)">
                      mdi-pencil
                    </v-icon>
                    <v-icon color="error" @click="deleteItem(item)">
                      mdi-delete
                    </v-icon>
                  </td>
                </tr>
                <span class="d-none scroll"/>
                </tbody>
              </template>
              <template #top>
                <v-toolbar flat dense>
                  <v-toolbar-title>
                    Список пользователей (Всего
                    {{ items.length || 0 }})
                  </v-toolbar-title>
                  <v-spacer/>
                  <v-dialog v-model="dialog" max-width="900px">
                    <template #activator="{ on, attrs }">
                      <v-btn
                        color="primary"
                        dark
                        v-bind="attrs"
                        v-on="on"
                      >
                        Новый пользователь
                      </v-btn>
                    </template>
                    <v-card>
                      <v-card-title>
                        <span class="headline">{{ formTitle }}</span>
                      </v-card-title>
                      <v-card-text>
                        <v-container>
                          <v-form ref="form" v-model="valid">
                            <v-row>
                              <v-col cols="12" sm="6" md="4">
                                <v-text-field
                                  v-model="editedItem.first_name"
                                  label="Имя"
                                  dense
                                  hide-details
                                  outlined
                                  single-line
                                />
                              </v-col>
                              <v-col cols="12" sm="6" md="4">
                                <v-text-field
                                  v-model="editedItem.last_name"
                                  label="Фамилия"
                                  dense
                                  hide-details
                                  outlined
                                  single-line
                                />
                              </v-col>
                              <v-col cols="12" sm="6" md="4">
                                <v-text-field
                                  v-model="editedItem.email"
                                  label="Email"
                                  type="email"
                                  dense
                                  hide-details
                                  outlined
                                  single-line
                                />
                              </v-col>
                              <v-col cols="12" sm="6" md="6">
                                <v-select
                                  v-model="editedItem.role_id"
                                  :items="filtered_roles"
                                  item-text="title"
                                  item-value="id"
                                  menu-props="auto"
                                  label="Роль"
                                  hide-details
                                  outlined
                                  dense
                                  single-line
                                />
                              </v-col>
                              <v-col cols="12" sm="6" md="6">
                                <v-select
                                  v-model="editedItem.showroom_id"
                                  :items="showrooms"
                                  item-text="name"
                                  item-value="id"
                                  menu-props="auto"
                                  label="Автосалон"
                                  hide-details
                                  single-line
                                  outlined
                                  dense
                                />
                              </v-col>

                              <v-col cols="12" md="6">
                                <v-text-field
                                  v-model="password"
                                  :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                  :type="showPassword ? 'text' : 'password'"
                                  label="Пароль"
                                  :rules="passwordRules"
                                  outlined
                                  hide-details
                                  dense
                                  @click:append="showPassword = !showPassword"
                                ></v-text-field>
                              </v-col>

                              <v-col cols="12" md="6">
                                <v-text-field
                                  v-model="confirmPassword"
                                  :append-icon="showConfirmPassword ? 'mdi-eye' : 'mdi-eye-off'"
                                  :type="showConfirmPassword ? 'text' : 'password'"
                                  label="Подтвердите пароль"
                                  :rules="confirmPasswordRules"
                                  outlined
                                  hide-details
                                  dense
                                  @click:append="showConfirmPassword = !showConfirmPassword"
                                ></v-text-field>
                              </v-col>
                            </v-row>
                          </v-form>
                        </v-container>
                      </v-card-text>

                      <v-card-actions>
                        <v-btn color="warning darken-1" text @click="openPermissions()">
                          Доступы
                        </v-btn>
                        <v-spacer/>
                        <v-switch v-model="editedItem.active" class="text-right ml-12" label="Активность"></v-switch>
                        <v-btn color="blue darken-1" text @click="close">
                          Отмена
                        </v-btn>
                        <v-btn color="blue darken-1" text @click="save()">
                          Сохранить
                        </v-btn>
                      </v-card-actions>
                    </v-card>
                  </v-dialog>


                  <v-dialog v-model="dialogPermission" max-width="900px">
                    <template #activator="{ on, attrs }">
                      <v-btn
                        class="ml-4"
                        color="warning"
                        dark
                        v-bind="attrs"
                        v-on="on"
                      >
                        Синхронизация доступа
                      </v-btn>
                    </template>
                    <v-card>
                      <v-card-title>
                        <span class="headline">Доступы пользователя</span>
                      </v-card-title>
                      <v-card-text>
                        <v-container>
                          <v-form ref="form" v-model="valid">
                            <v-row>
                              <v-col cols="12" sm="6" md="4">
                                <v-select
                                  v-model="sync_showroom_id"
                                  :items="showrooms"
                                  item-text="name"
                                  item-value="id"
                                  menu-props="auto"
                                  label="Салон"
                                  hide-details
                                  outlined
                                  dense
                                  single-line
                                />
                              </v-col>
                              <v-col cols="12" sm="6" md="4">
                                <v-autocomplete
                                  v-model="user_from"
                                  :items="filtered_users"
                                  :item-text="item => item.first_name +' '+ item.last_name "
                                  item-value="id"
                                  menu-props="auto"
                                  label="Пользователь от"
                                  hide-details
                                  outlined
                                  dense
                                  single-line
                                />
                              </v-col>
                              <v-col cols="12" sm="6" md="4">
                                <v-autocomplete
                                  v-model="user_to"
                                  :items="filtered_users"
                                  :item-text="item => item.first_name +' '+ item.last_name "
                                  item-value="id"
                                  menu-props="auto"
                                  label="Пользователь к"
                                  hide-details
                                  single-line
                                  outlined
                                  dense
                                />
                              </v-col>
                              <v-col cols="12" sm="12" md="12">
                                <v-text-field
                                  v-model="code"
                                  :append-icon="codePassword ? 'mdi-eye' : 'mdi-eye-off'"
                                  :type="codePassword ? 'text' : 'password'"
                                  label="Код"
                                  :rules="codeRules"
                                  outlined
                                  hide-details
                                  dense
                                  @click:append="codePassword = !codePassword"
                                ></v-text-field>
                              </v-col>
                            </v-row>
                          </v-form>
                        </v-container>
                      </v-card-text>

                      <v-card-actions>
                        <v-spacer/>
                        <v-btn color="blue darken-1" text @click="closePermission()">
                          Закрыть
                        </v-btn>
                        <v-btn color="blue darken-1" text @click="syncUser()">
                          Синхронизация
                        </v-btn>
                      </v-card-actions>
                    </v-card>
                  </v-dialog>
                </v-toolbar>
              </template>
            </v-data-table>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>
<script>
import BreadCrumb from '~/components/BreadCrumb'

export default {
  name: 'UserList',
  components: {BreadCrumb},
  layout: 'default',
  middleware: "permission",
  data: () => ({
    dialog: false,
    valid: false,
    componentKey: 0,
    headers: [
      {
        text: '№',
        value: 'id',
        align: 'start',
        sortable: true,
        width: 100
      },
      {
        text: 'Пользователь',
        value: 'fio',
        align: 'start',
        sortable: true,
        width: 100
      },
      {
        text: 'Email',
        value: 'email',
        align: 'start',
        sortable: false,
        width: 100
      },
      {
        text: 'Салон',
        value: 'showroom.name',
        align: 'start',
        sortable: true,
        width: 100
      },
      {
        text: 'Роль',
        value: 'role.title',
        align: 'center',
        sortable: true,
        width: 100
      },
      {
        text: 'Создан',
        value: 'created_at',
        align: 'start',
        sortable: true,
        width: 100
      },
      {
        text: 'Изменён',
        value: 'updated_at',
        align: 'start',
        sortable: true,
        width: 100
      },
      {
        text: 'Действия',
        value: 'actions',
        align: 'start',
        sortable: false,
        width: 100
      }
    ],
    confirmPassword: '',
    password: '',
    showPassword: false,
    showConfirmPassword: false,
    passwordRules: [
      (v) => !!v || 'Необходимо ввести пароль',
      (v) => (v && v.length >= 6) || 'Пароль должен состоять минимум из 6 символов',
    ],
    codeRules: [
      (v) => !!v || 'Необходимо ввести код',
    ],
    editedIndex: -1,
    dialogPermission: false,
    user_from: null,
    sync_showroom_id: null,
    user_to: null,
    code: null,
    codePassword: false,
    editedItem: {
      id: '',
      first_name: '',
      email: '',
      role_id: null,
      showroom_id: null,
      password: null,
      update_at: null,
      active: true
    },
    defaultItem: {
      id: '',
      first_name: '',
      email: '',
      role_id: null,
      showroom_id: null,
      password: null,
      update_at: null,
      active: true
    }
  }),

  async fetch({store, error}) {
    await store.dispatch('user/fetchUserRoles')
    await store.dispatch('user/fetchUsers')
    await store.dispatch('permission/fetchPages')
    await store.dispatch('showroom/fetchShowrooms')

  },

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? 'Добавить пользователя' : 'Изменить'
    },
    users() {
      return this.$store.state.user.users
    },
    filtered_users() {
      let users = this.users
      if (this.$auth?.user?.role_id !== 1) {
        users = users.filter(user => (![1, 12].includes(user.role_id) && user.showroom_id === this.sync_showroom_id))
      }
      return users
    },
    items() {
      if (this.$auth?.user?.role_id !== 1) {
        return this.users.filter(user => ![1, 12].includes(user.role_id))
      } else return this.users

    },
    filtered_roles() {
      if (this.$auth?.user?.role_id !== 1) {
        return this.roles.filter(user => ![1, 12].includes(user.id))
      } else return this.roles

    },
    roles() {
      return this.$store.state.user.roles
    },
    permission() {
      return this.$store.state.permission.user_permissions
    },
    pages() {
      return this.$store.state.permission.pages
    },
    role() {
      return this.$store.state.auth.role
    },
    showrooms() {
      return this.$store.state.showroom.showrooms
    },
    links() {
      return [
        {
          text: 'Администратор',
          disabled: false,
          href: '/'
        },
        {
          text: 'Пользователи',
          disabled: true,
          href: '/users'
        }
      ]
    },
    confirmPasswordRules() {
      return [
        (v) => !!v || 'Подтвердите пароль',
        (v) => v === this.editedItem.password || 'Пароль не совпадает',
      ];
    },
  },
  watch: {
    dialog(val) {
      val || this.close()
    },
    dialogPermission(val) {
      val || this.closePermission()
    },
    password(newPassword) {
      this.editedItem.password = newPassword;
    },
  },
  methods: {
    save() {
      if (this.valid) {
        if (this.editedIndex > -1) {
          this.$store.dispatch('user/update', this.editedItem)
          this.$toast.success("Данные изменены");
        } else {
          this.$store.dispatch('user/save',
            this.editedItem
          )
          this.$toast.success("Данные добавлены");
        }
        this.close()
      } else {
        this.$toast.error("Заполните обязательные поля");
      }
    },

    editItem(item) {
      this.editedIndex = this.items.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },

    deleteItem(item) {
      confirm('Вы уверены, что хотите удалить этого пользователя?') &&
      this.$store
        .dispatch('user/delete', {
          id: item.id
        })
        .then(() => {
          this.$toast.success("Пользователь удалён");
        })
      this.deleteId = ''
      this.deleteDialog = false
    },
    close() {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },
    closePermission() {
      this.user_to = null
      this.user_to = null
      this.user_from = null
      this.dialogPermission = false
      this.sync_showroom_id = null
      this.code = null
    },
    syncUser() {
      const postData  = {
        user_from: this.user_from,
        user_to: this.user_to
      }
      this.$axios.post('sync-user', postData)
        .then(response => {
          this.$toast.success("Доступы синхронизированы");
          this.dialogPermission = false
        })
        .catch(error => {
          // Handle error
          this.$toast.error('Произошла ошибка ' + error);
        });

    },
    openPermissions() {
      this.dialog = false
      this.dialogPermission = true
      this.user_to = null
      this.user_from = null

    },
    validate() {
      this.$refs.form.validate()
    }
  }
}
</script>

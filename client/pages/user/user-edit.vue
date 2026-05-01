<template>
  <v-container fluid>
    <BreadCrumb :items="links"/>

    <v-row no-gutters align="start" class="d-flex">
      <v-col cols="12" class="mt-2">
        <v-card>
          <v-toolbar flat dense>
            <v-toolbar-title class="mb-0">
              Доступы пользователя {{ permission_user.name }}
            </v-toolbar-title>
            <v-spacer/>
            <v-dialog v-model="dialog" max-width="700px">
              <template #activator="{ on, attrs }">
                <v-btn
                  color="primary"
                  dark
                  class="mb-2"
                  v-bind="attrs"
                  v-on="on"
                >
                  Добавить
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
                        <v-col cols="12" sm="12" md="12">
                          <v-autocomplete
                            v-model="editedItem.page_id"
                            :items="pages"
                            item-text="name"
                            item-value="id"
                            menu-props="auto"
                            label="Страница"
                            hide-details
                            single-line
                            :rules="[(v) => !!v || 'Выберите страницу']"
                            required
                          />
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                          <v-checkbox
                            v-model="editedItem.create"
                            label="Создание"
                            hide-details
                          />
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                          <v-checkbox
                            v-model="editedItem.edit"
                            label="Изменение"
                            hide-details
                          />
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                          <v-checkbox
                            v-model="editedItem.delete"
                            label="Удаление"
                            hide-details
                          />
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                          <v-checkbox
                            v-model="editedItem.trash"
                            label="Корзина"
                            hide-details
                          />
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                          <v-checkbox
                            v-model="editedItem.download"
                            label="Скачивание"
                            hide-details
                          />
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                          <v-checkbox
                            v-model="editedItem.view"
                            label="Просмотр"
                            hide-details
                          />
                        </v-col>
                      </v-row>
                    </v-form>
                  </v-container>
                </v-card-text>
                <v-card-actions>
                  <v-spacer/>
                  <v-btn color="blue darken-1" text @click="close">
                    Отмена
                  </v-btn>
                  <v-btn color="blue darken-1" text @click="save()">
                    Сохранить
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
          </v-toolbar>

          <v-card flat>
            <v-treeview
              open-all
              :items="pages"
              transition
              open-on-click
              :selection-type="'independent'"
            >

              <template #prepend="{item}">
                <div class="d-flex ">
                <v-icon
                  color="success"
                  class="mr-4"
                  v-text="`${item.icon}`"
                ></v-icon>
                <v-checkbox :key="item.id" :value="checkPermission(item.slug, 'see_')"/>
                </div>
              </template>
              <template #label="{ item, value }">
                {{item.name}} <v-icon v-if="checkPermission(item.slug, 'see_')" color="green">mdi-check</v-icon>
              </template>
            </v-treeview>
            <v-data-table
               v-if="false"
              dense
              :headers="headers"
              :items="pages"
              item-key="name"
              class="elevation-1"
              :items-per-page="pages.length"
              hide-default-footer
            >
              <template #item.name="{ item }">
               {{item.id }} {{item.name }} {{item.slug }}
              </template>
              <template #item.create="{ item }">
                <v-icon v-if="checkPermission(item.slug, 'create_')" color="success">
                  mdi-check
                </v-icon>
                <v-icon v-else color="error">
                  mdi-close
                </v-icon>
              </template>
              <template #item.edit="{ item }">
                <v-icon v-if="checkPermission(item.slug, 'edit_')" color="success">
                  mdi-check
                </v-icon>
                <v-icon v-else color="error">
                  mdi-close
                </v-icon>
              </template>
              <template #item.delete="{ item }">
                <v-icon v-if="checkPermission(item.slug, 'delete_')" color="success">
                  mdi-check
                </v-icon>
                <v-icon v-else color="error">
                  mdi-close
                </v-icon>
              </template>
              <template #item.view="{ item }">
                <v-icon v-if="checkPermission(item.slug, 'see_')" color="success">
                  mdi-check i
                </v-icon>
                <v-icon v-else color="error">
                  mdi-close
                </v-icon>
                <v-checkbox
                  :label="checkPermission(item.slug, 'see_')"
                  color="green"
                  :value="!checkPermission(item.slug, 'see_')"
                  hide-details
                  input-value="checkPermission(item.slug, 'see_')"
                ></v-checkbox>
              </template>
              <template #item.trash="{ item }">
                <v-icon v-if="checkPermission(item.slug, 'trash_')" color="success">
                  mdi-check
                </v-icon>
                <v-icon v-else color="error">
                  mdi-close
                </v-icon>
              </template>
              <template #item.download="{ item }">
                <v-icon v-if="checkPermission(item.slug, 'download_')" color="success">
                  mdi-check
                </v-icon>
                <v-icon v-else color="error">
                  mdi-close
                </v-icon>
              </template>
              <template #item.actions="{ item }">
                <v-icon color="success" class="mr-2" @click="editItem(item)">
                  mdi-pencil
                </v-icon>
                <v-icon color="error" @click="deleteItem(item)">
                  mdi-delete
                </v-icon>
              </template>
            </v-data-table>
          </v-card>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>
<script>
import BreadCrumb from '~/components/BreadCrumb'
export default {
  components: {BreadCrumb},
  layout: 'user',
  middleware: 'auth',
  data() {
    return {
      tab: null,
      current_role: 1,
      dialog: false,
      valid: false,
      snackbar: false,
      text: '',
      color: '',
      timeout: 6000,
      editedIndex: -1,
      editedItem: {
        id: '',
        page_id: null,
        user_id: '',
        create: '',
        edit: '',
        delete: '',
        trash: '',
        download: '',
        view: ''
      },
      defaultItem: {
        id: '',
        page_id: null,
        user_id: '',
        create: '',
        edit: '',
        delete: '',
        trash: '',
        download: '',
        view: ''
      }
    }
  },
  async fetch({store, error, params: {id}}) {
    // await store.dispatch('permission/fetchRoles')
    // await store.dispatch('permission/fetchPages')
    await store.dispatch('permission/fetchPages')
    await store.dispatch('permission/fetchUserPermissions', {id})
    await store.dispatch('permission/fetchUser', {id}).catch((e) => {
      error({statusCode: 404, message: 'Пользователь не найден'})
    })
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? 'Добавить' : 'Изменить'
    },
    headers() {
      return [
        {
          text: 'Страница',
          align: 'start',
          sortable: false,
          value: 'name'
        },
        {
          text: 'Создание',
          align: 'start',
          sortable: false,
          value: 'create'
        },
        {
          text: 'Изменение',
          value: 'edit',
          align: 'start',
          sortable: false
        },
        {text: 'Удаление', value: 'delete', align: 'start', sortable: false},
        {text: 'Корзина', value: 'trash', align: 'start', sortable: false},
        {text: 'Скачивание', value: 'download', align: 'start', sortable: false},
        {text: 'Просмотр', value: 'view', align: 'start', sortable: false},
        {text: 'Действия', value: 'actions'}
      ]
    },
    permissions() {
      return this.$store.state.permission.user_permissions
    },
    permission_user() {
      return this.$store.state.permission.user
    },
    roles() {
      return this.$store.state.permission.roles
    },
    pages() {
      return this.$store.state.permission.pages
    },
    filteredData() {
      return this.permissions
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
          text: 'Привилегия пользователей',
          disabled: true,
          href: '/permission'
        }
      ]
    }
  },
  methods: {
    filterByRole(roleId) {
      this.current_role = roleId
    },
    checkPermission(slug, prefix) {
      console.log(prefix + slug)
      return (this.permissions.includes(prefix + slug) ||   this.permissions.includes(prefix + '/'+  slug) ||  this.permissions.includes(prefix + 'tab_'+  slug))
    },
    save() {
      if (this.valid) {
        console.log(Number(this.$route.params.id))
        this.editedItem.user_id = Number(this.$route.params.id)
        if (this.editedIndex > -1) {
          this.$store
            .dispatch('permission/save', {
              item: this.editedItem
            })
            .then(() => {
              this.$toast.success("Привилегия изменён");
            })
        } else {
          this.$store
            .dispatch('permission/save', {
              item: this.editedItem
            })
            .then(() => {
              this.$toast.success("Привилегия изменён");
            })
            .catch((error) => {
              this.$toast.error('Эта привилегия существует' + error);
            })
        }
        this.close()
      } else {
        this.$toast.error("Заполните обязательные поля");
      }

    },

    editItem(item) {
      this.editedIndex = this.filteredData.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },

    deleteItem(item) {
      // const index = this.usedcars.indexOf(item);
      confirm('Вы согласны удалить эту привилегию??') &&
      this.$store
        .dispatch('permission/delete', {
          id: item.id
        })
        .then(() => {
          this.$toast.success("Привилегии удалены");
        })
      this.deleteId = ''
    },
    close() {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },
    validate() {
      this.$refs.form.validate()
    }
  }
}
</script>

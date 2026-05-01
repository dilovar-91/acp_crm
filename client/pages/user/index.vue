<template>
  <v-container fluid>
    <BreadCrumb :items="links" />

    <v-row no-gutters align="start" class="d-flex">
      <v-col cols="12" class="mt-2">
        <v-card>
          <v-toolbar flat dense>
            <v-toolbar-title class="mb-0">
              Привилегия пользователей
            </v-toolbar-title>

            <v-spacer />
            <v-dialog v-model="dialog" max-width="600px">
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
                        <v-col cols="12" sm="6" md="6">
                          <v-select
                            v-model="editedItem.role_id"
                            :items="roles"
                            item-text="title"
                            item-value="id"
                            menu-props="auto"
                            label="Тип пользователя"
                            hide-details
                            single-line
                            :rules="[(v) => !!v || 'Выберите тип пользователя']"
                            required
                          />
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                          <v-select
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
                          />
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                          <v-checkbox
                            v-model="editedItem.update"
                            label="Изменение"
                          />
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                          <v-checkbox
                            v-model="editedItem.delete"
                            label="Удаление"
                          />
                        </v-col>
                        <v-col cols="12" sm="6" md="6">
                          <v-checkbox
                            v-model="editedItem.view"
                            label="Просмотр"
                          />
                        </v-col>
                      </v-row>
                    </v-form>
                  </v-container>
                </v-card-text>

                <v-card-actions>
                  <v-spacer />
                  <v-btn color="blue darken-1" text @click="close">
                    Отмена
                  </v-btn>
                  <v-btn color="blue darken-1" text @click="save()">
                    Сохранить
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
            <template #extension>
              <v-tabs v-model="tab" align-with-title>
                <v-tabs-slider color="yellow" />

                <v-tab
                  v-for="item in roles"
                  :key="item.id"
                  ripple
                  @click="filterByRole(item.id)"
                >
                  {{ item.title }}
                </v-tab>
              </v-tabs>
            </template>
          </v-toolbar>

          <v-tabs-items v-model="tab">
            <v-tab-item v-for="item in roles" :key="item.id">
              <v-card flat>
                <v-data-table
                  dense
                  :headers="headers"
                  :items="filteredData"
                  item-key="name"
                  class="elevation-1"
                  :items-per-page="filteredData.length"
                  hide-default-footer
                >
                  <template #item.create="{ item }">
                    <v-icon v-if="item.create === 1" color="success">
                      mdi-check
                    </v-icon>
                    <v-icon v-else color="error">
                      mdi-close
                    </v-icon>
                  </template>
                  <template #item.update="{ item }">
                    <v-icon v-if="item.update === 1" color="success">
                      mdi-check
                    </v-icon>
                    <v-icon v-else color="error">
                      mdi-close
                    </v-icon>
                  </template>
                  <template #item.delete="{ item }">
                    <v-icon v-if="item.delete === 1" color="success">
                      mdi-check
                    </v-icon>
                    <v-icon v-else color="error">
                      mdi-close
                    </v-icon>
                  </template>
                  <template #item.view="{ item }">
                    <v-icon v-if="item.view === 1" color="success">
                      mdi-check
                    </v-icon>
                    <v-icon v-else color="error">
                      mdi-close
                    </v-icon>
                  </template>
                  <template #item.actions="{ item }">
                    <v-icon small class="mr-2" @click="editItem(item)">
                      mdi-pencil
                    </v-icon>
                    <v-icon small @click="deleteItem(item)">
                      mdi-delete
                    </v-icon>
                  </template>
                </v-data-table>
              </v-card>
            </v-tab-item>
          </v-tabs-items>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import BreadCrumb from '~/components/BreadCrumb'
export default {
  components: { BreadCrumb },
  layout: 'user',
  middleware: 'permission',
  data () {
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
        role_id: '',
        create: '',
        update: '',
        delete: '',
        view: ''
      },
      defaultItem: {
        id: '',
        page_id: null,
        role_id: '',
        create: '',
        update: '',
        delete: '',
        view: ''
      }
    }
  },
  async fetch ({ store }) {
    await store.dispatch('permission/fetchRoles')
    await store.dispatch('permission/fetchNestPages')
    await store.dispatch('permission/fetchPermissions')
  },
  computed: {
    formTitle () {
      return this.editedIndex === -1 ? 'Добавить' : 'Изменить'
    },
    headers () {
      return [
        {
          text: 'Страница',
          align: 'start',
          sortable: false,
          value: 'page.name'
        },
        {
          text: 'Создание',
          align: 'start',
          sortable: false,
          value: 'create'
        },
        {
          text: 'Изменение',
          value: 'update',
          align: 'start',
          sortable: false
        },
        { text: 'Удаление', value: 'delete', align: 'start', sortable: false },
        { text: 'Просмотр', value: 'view', align: 'start', sortable: false },
        { text: 'Действия', value: 'actions' }
      ]
    },

    permissions () {
      return this.$store.state.permission.permissions
    },
    roles () {
      return this.$store.state.permission.roles
    },
    pages () {
      return this.$store.state.permission.nest_pages
    },
    filteredData () {
      return this.permissions.filter(l => l.role_id === this.current_role)
    },
    role () {
      return this.$store.state.auth.role
    },
    links () {
      return [
        {
          text: this.role.title,
          disabled: false,
          href: '/' + this.role.name
        },
        {
          text: 'Привилегия пользователей',
          disabled: true,
          href: '/' + this.role.name + '/permission'
        }
      ]
    }
  },
  methods: {
    filterByRole (role_id) {
      this.current_role = role_id
    },
    save () {
      if (this.valid) {
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
              this.$toast.success("Привилегия добавлён");
            })
            .catch((error) => {
              this.$toast.error("Эта привилегия уже существует");
            })
        }
        this.close()
      } else { this.$toast.error("Заполните обязательные поля"); }
    },

    editItem (item) {
      this.editedIndex = this.filteredData.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },

    deleteItem (item) {
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
    close () {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },
    validate () {
      this.$refs.form.validate()
    }
  }
}
</script>

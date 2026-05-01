<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links" />

      <v-row no-gutters align="start" class="d-flex">
        <v-col cols="12" class="mt-1">
          <v-data-table
            dense
            :headers="headers"
            :items="managers"
            :items-per-page="15"
            item-key="id"
            class="elevation-1"
          >
            <template #body="{ items }">
              <tbody>
                <tr
v-for="(item) in items"
                    :key="item.id"

                    @dblclick="editItem(item)"
                >
                  <td class="text-center">
                    {{ item.name }}
                  </td>
                  <td class="text-center">
                    {{ item.showroom && item.showroom.name }}
                  </td>
                  <td class="text-center">
                    <v-icon color="warning" class="mr-2" @click="editItem(item)">
                      mdi-pencil
                    </v-icon>
                    <v-icon color="error" @click="deleteItem(item)">
                      mdi-delete
                    </v-icon>
                  </td>
                </tr>
              </tbody>
            </template>

            <template #top>
              <v-toolbar flat dense>
                <v-toolbar-title>
                  Список операторов (Всего
                  {{ managers.length || 0 }})
                </v-toolbar-title>
                <v-spacer />
                <v-dialog v-model="dialog" max-width="900px">
                  <template #activator="{ on, attrs }">
                    <v-btn
                      color="primary"
                      dark
                      class="mb-2"
                      v-bind="attrs"
                      v-on="on"
                    >
                      Добавить новую
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
                              <v-text-field
                                v-model="editedItem.name"
                                label="Имя"
                              />
                            </v-col>
                            <v-col cols="12" sm="12" md="12">
                              <v-select
                                v-model="editedItem.showroom_id"
                                :items="showrooms"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                                label="Роль"
                                hide-details
                                single-line
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
              </v-toolbar>
            </template>
          </v-data-table>
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
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>
<script>
import BreadCrumb from '~/components/BreadCrumb'
export default {
  components: { BreadCrumb },
  layout: 'user',
  middleware: 'permission',
  data: () => ({
    dialog: false,
    valid: false,
    snackbar: false,
    text: '',
    color: '',
    timeout: 6000,
    expanded: [],
    singleExpand: false,
    headers: [
      {
        text: 'Пользователь',
        value: 'name',
        align: 'center',
        sortable: true,
        width: 100
      },
      {
        text: 'Салон',
        value: 'showroom.title',
        align: 'center',
        sortable: true,
        width: 100
      },
      {
        text: 'Действия',
        value: 'actions',
        align: 'center',
        sortable: false,
        width: 100
      }
    ],
    editedIndex: -1,
    editedItem: {
      id: '',
      name: '',
      showroom_id: ''
    },
    defaultItem: {
      id: '',
      name: '',
      showroom_id: ''
    }
  }),

  async fetch ({ store, error }) {
    await store.dispatch('permission/fetchRoles')
    await store.dispatch('showroom/fetchShowrooms')
    await store.dispatch('showroom/fetchManagers')
    await store.dispatch('user/fetchUsers')
  },

  computed: {
    formTitle () {
      return this.editedIndex === -1 ? 'Добавить менеджера' : 'Изменить менеджера'
    },
    roles () {
      return this.$store.state.permission.roles
    },
    operators () {
      return this.$store.state.showroom.managers
    },
    showrooms () {
      return this.$store.state.showroom.showrooms
    },
    role () {
      return this.$store.state.auth.role
    },
    links () {
      return [
        {
          text: this.role.title || 'Администратор',
          disabled: false,
          href: '/' + (this.role?.name || 'admin')
        },
        {
          text: 'Менеджеры',
          disabled: true,
          href: '/' + this.role?.name + '/managers'
        }
      ]
    }
  },
  watch: {
    dialog (val) {
      val || this.close()
    }
  },
  methods: {
    save () {
      if (this.valid) {
        if (this.editedIndex > -1) {
          this.$store.dispatch('showroom/saveManager', {
            item: this.editedItem
          })
          this.$notify('success', 'Запись изменен')
        } else {
          this.$store.dispatch('showroom/saveManager', {
            item: this.editedItem
          })
          this.$notify('success', 'Запись добавлен')
        }
        this.close()
      } else { this.$notify('error', 'Заполните обязательные поля') }
    },

    editItem (item) {
      this.editedIndex = this.managers.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },

    deleteItem (item) {
      confirm('Вы уверены, что хотите удалить этого пользователя?') &&
        this.$store
          .dispatch('showroom/deleteManager', {
            id: item.id
          })
          .then(() => {
            this.$notify('success', 'Менеджер удалён')
          })
      this.deleteId = ''
      this.deleteDialog = false
    },
    close () {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },

    showSnack (type, text) {
      this.text = text
      this.color = type
      this.snackbar = true
    },
    validate () {
      this.$refs.form.validate()
    }
  }
}
</script>

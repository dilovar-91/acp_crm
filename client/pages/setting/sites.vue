<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links" />

      <v-row no-gutters align="start" class="d-flex">
        <v-col cols="12" class="mt-1">
          <v-app-bar class="grey lighten-5" elevate-on-scroll dense>
            <v-toolbar-title>Список сайтов</v-toolbar-title>
            <v-spacer />
            <v-btn
              v-if="role && role.name"
              color="primary"
              dark
              class="hidden-sm-and-down mr-1"
              @click="dialog = true"
            >
              <v-icon>mdi-plus</v-icon> Добавить сайт
            </v-btn>
          </v-app-bar>
        </v-col>
      </v-row>
      <v-row dense>
        <v-col cols="12" md="12" sm="12">
          <v-data-table
            :key="componentKey"
            class="elevation-1 myTable  grey lighten-3"
            :search="search"
            :headers="headers"
            fixed-header
            :items="sites"
            :items-per-page="sites.length"
            height="80vh"
            :hide-default-footer="true"
            dense
          >
            <template v-slot:body="{items}">
              <tbody>
              <template v-for="(item, i) in items">
                <tr
                  :key="'row-' + item.id"
                  @dblclick="editItem(item)"
                >
                  <td>{{ i + 1 }}</td>
                  <td>
                    {{ item.name }}
                  </td>
                  <td>
                    <a :href="item.url"  target="_blank" rel="noreferrer">{{ item.url }}</a>
                  </td>
                  <td>{{ item.description }}</td>
                  <td>
                    {{ item.showroom?.name }}
                  </td>
                </tr>
              </template>
              </tbody>
            </template>
          </v-data-table>
        </v-col>
        <v-dialog v-model="dialog" max-width="400">
          <v-card>
            <v-card-title class="headline">
              {{ formTitle }}
            </v-card-title>
            <v-card-text>
              <v-form ref="form" v-model="valid" class="pt-2">
                <v-text-field
                  v-model="editedItem.title"
                  label="Название"
                  outlined
                  :rules="[(v) => !!v || 'Введите название сайта']"
                  required
                  dense
                />
                <v-text-field
                  v-model="editedItem.url"
                  label="Ссылка"
                  outlined
                  dense
                  append-icon="mdi-link"
                  required
                  :rules="[(v) => !!v || 'Введите ссылка сайта']"
                  @click:append="redirect(editedItem.url)"
                />
                <v-select
                  v-model="editedItem.showroom_id"
                  :items="showrooms"
                  item-text="name"
                  item-value="id"
                  menu-props="auto"
                  label="Шоурум"
                  hide-details
                  class="mb-3"
                  required
                  :rules="[(v) => !!v || 'Выберите салона']"
                  dense
                  outlined
                  clearable
                  @click:clear="$nextTick(() => editedItem.showroom_id=null)"
                />
                <v-textarea v-model="editedItem.description" rows="2" outlined
                            dense label="Комментарии"
                />
              </v-form>
            </v-card-text>
            <v-divider />
            <v-card-actions>
              <v-btn text @click="dialog = false">
                Отмена
              </v-btn>
              <v-btn v-if="this.editedIndex !== -1"
                     :disabled="role && role.name !== 'admin'"
                     color="red darken-1"
                     class="mr-3"
                     text
                     @click="confirmDelete(editedItem.id)"
              >
                Удалить
              </v-btn>
              <v-spacer />
              <v-btn
                color="primary"
                text
                @click="save()"
              >
                Сохранить
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="deleteDialog" max-width="290">
          <v-card>
            <v-card-title class="headline">
              Вы хотите удалить?
            </v-card-title>

            <v-card-text>
              После удаления вы не можете восстановить эту строку.
            </v-card-text>

            <v-card-actions>
              <v-spacer />

              <v-btn color="green darken-1" text @click="deleteItem()">
                Да
              </v-btn>
              <v-btn color="green darken-1" text @click="deleteDialog = false">
                Нет
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-row>
    </v-container>
  </div>
</template>
<script>
import BreadCrumb from '~/components/BreadCrumb'
export default {
  layout: 'crm',
  middleware: 'permission',
  components: { BreadCrumb },
  data: () => ({
    dialog: false,
    editedIndex: -1,
    componentKey: 0,
    valid: false,
    deleteId: '',
    search: null,
    deleteDialog: false,
    headers: [
      {
        text: '№',
        value: 'number',
        sortable: false,
        fixed: true,
        width: '10px'
      },
      {
        text: 'Сайт',
        align: 'start',
        sortable: false,
        value: 'date',
        fixed: true,
        width: '30px'
      },
      {
        text: 'Ссылка',
        sortable: false,
        width: '180px',
        value: 'client_name',
        fixed: true
      },
      { text: 'Описание', value: 'description', sortable: false, width: '100px' },
      { text: 'Салон', value: 'showroom?.name', width: '200px', sortable: false }
    ],
    editedItem: {
      id: '',
      title: '',
      url: '',
      description: '',
      showroom_id: '',
    },
    defaultItem: {
      id: '',
      title: '',
      url: '',
      description: '',
      showroom_id: '',
    }
  }),
  computed: {
    sites () {
      return this.$store.state.showroom.sites
    },
    showrooms () {
      return this.$store.state.showroom.showrooms
    },
    agencies () {
      return this.$store.state.showroom.agencies
    },
    role () {
      return this.$store.state.auth.role
    },
    links () {
      return [
        {
          text: this.role.title,
          disabled: false,
          href: '/' + this.role?.name
        },
        {
          text: 'Настройки',
          disabled: false,
          href: '/' + this.role?.name + '/settings'
        },
        {
          text: 'Список сайтов',
          disabled: true,
          href: '/' + this.role?.name + '/sites'
        }
      ]
    },
    formTitle () {
      return this.editedIndex === -1 ? 'Добавлеение' : 'Изменение'
    },
  },
  async fetch ({ store }) {
    await store.dispatch('showroom/fetchSites')
    await store.dispatch('showroom/fetchShowrooms')
  },
  watch: {
    dialog(val) {
      val || this.close()
    },
  },
  methods: {
    redirect (url) {
      window.open(url, '_blank')
    },
    async save () {
      await this.validate()
      if (this.valid) {
        this.$store
          .dispatch('showroom/saveSite', {
            item: this.editedItem
          })
          .then((res) => {
            if (this.editedIndex > -1) {
              this.$toast.success("Сайт изменён!", {
                position: "top-right",
                timeout: 2000,
                closeOnClick: true,
                pauseOnFocusLoss: true,
                pauseOnHover: true,
                draggable: true,
                draggablePercent: 0.6,
                showCloseButtonOnHover: false,
                hideProgressBar: true,
                closeButton: "button",
                icon: true,
                rtl: false
              });
            } else {
              this.$toast.success("Сайт добавлен!", {
                position: "top-right",
                timeout: 2000,
                closeOnClick: true,
                pauseOnFocusLoss: true,
                pauseOnHover: true,
                draggable: true,
                draggablePercent: 0.6,
                showCloseButtonOnHover: false,
                hideProgressBar: true,
                closeButton: "button",
                icon: true,
                rtl: false
              });
            }
            this.close()
          })
          .catch((error) => {
            this.$toast.error("Заполните обязательные поля!"+ error, {
              position: "top-right",
              timeout: 2000,
              closeOnClick: true,
              pauseOnFocusLoss: true,
              pauseOnHover: true,
              draggable: true,
              draggablePercent: 0.6,
              showCloseButtonOnHover: false,
              hideProgressBar: true,
              closeButton: "button",
              icon: true,
              rtl: false
            });
          })
      } else {
        this.$toast.error("Заполните обязательные поля!", {
          position: "top-right",
          timeout: 2000,
          closeOnClick: true,
          pauseOnFocusLoss: true,
          pauseOnHover: true,
          draggable: true,
          draggablePercent: 0.6,
          showCloseButtonOnHover: false,
          hideProgressBar: true,
          closeButton: "button",
          icon: true,
          rtl: false
        });
      }
    },
    editItem (item) {
      this.editedIndex = item.id
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },

    deleteItem () {
      this.$store
        .dispatch('showroom/deleteSite', {
          id: this.deleteId
        })
        .then((resp) => {
          console.log(resp.data)
          this.$toast.success("Сайт удалён", {
            position: "top-right",
            timeout: 2000,
            closeOnClick: true,
            pauseOnFocusLoss: true,
            pauseOnHover: true,
            draggable: true,
            draggablePercent: 0.6,
            showCloseButtonOnHover: false,
            hideProgressBar: true,
            closeButton: "button",
            icon: true,
            rtl: false
          });
        })
      this.deleteId = ''
      this.dialog = false
      this.deleteDialog = false
      this.componentKey += 1
    },

    confirmDelete (id) {
      this.deleteId = id
      this.deleteDialog = true

    },


    validate () {
      this.$refs.form.validate()
    },

    close () {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1

        this.componentKey += 1
        this.$refs.form.resetValidation()
        this.valid = false
      })
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
</style>

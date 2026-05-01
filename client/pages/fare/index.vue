<template>
  <div>
    <BreadCrumb :items="links" />
    <v-container fluid>
      <v-row no-gutters align="start" class="d-flex">
        <v-col cols="12">
          <v-card class="mx-auto pt-0">
            <v-app-bar class="grey lighten-5" elevate-on-scroll dense>
              <v-toolbar-title>Оплата проезда</v-toolbar-title>
              <v-row class="ml-5 mt-3">
                <v-col cols="12" sm="6" md="3" class="mr-2 hidden-sm-and-down">
                  <v-text-field
                    v-model="search"
                    label="Поиск"
                    outlined
                    dense
                  />
                </v-col>
                <v-col cols="12" sm="6" md="3" class="mr-2 hidden-sm-and-down">
                  <date-picker
                    v-model="filter_from"
                    format="DD.MM.Y"
                    placeholder="Дата с"
                  />
                </v-col>
                <v-col cols="12" sm="6" md="3" class="mr-2 hidden-sm-and-down">
                  <date-picker
                    v-model="filter_to"
                    format="DD.MM.Y"
                    placeholder="Дата до"
                  />
                </v-col>
                <v-col cols="12" sm="6" md="2" class="hidden-sm-and-down">
                  <v-btn color="error" dark class="mb-2" @click="clearFilter()">
                    Сбросить
                  </v-btn>
                </v-col>
              </v-row>
              <v-spacer />
              <div class="hidden-sm-and-up">
                <v-menu
                  v-model="menu"
                  :close-on-content-click="true"
                  :nudge-width="200"
                  offset-x
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn color="indigo" dark v-bind="attrs" v-on="on">
                      <v-icon class="mr-2">
                        mdi-menu
                      </v-icon>
                      Меню
                    </v-btn>
                  </template>

                  <v-card>
                    <v-list>
                      <v-list-item v-if="false">
                        <v-list-item-action>
                          <v-btn
                            :to="'/fares/'+ $route.params.id + '/create'"
                            color="primary"
                            dark
                            class="mb-2"
                          >
                            Добавить
                          </v-btn>
                        </v-list-item-action>
                      </v-list-item>

                      <v-list-item class="hidden-md-and-up">
                        <v-list-item-action>
                          <date-picker
                            v-model="filter_from"
                            format="DD.MM.Y"
                            placeholder="Дата с"
                          />
                        </v-list-item-action>
                      </v-list-item>
                      <v-list-item class="hidden-md-and-up">
                        <v-list-item-action>
                          <date-picker
                            v-model="filter_to"
                            format="DD.MM.Y"
                            placeholder="Дата до"
                          />
                        </v-list-item-action>
                      </v-list-item>
                      <v-list-item class="hidden-md-and-up">
                        <v-list-item-action>
                          <v-btn color="error" dark class="mb-2 " @click="clearFilter()">
                            Сбросить
                          </v-btn>
                        </v-list-item-action>
                      </v-list-item>
                    </v-list>
                    <v-card-actions>
                      <v-spacer />

                      <v-btn color="primary" text class="hidden-sm-and-down" @click="menu = false">
                        Закрыть
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-menu>
              </div>
              <v-btn
                v-if="false"
                color="primary"
                dark
                class="mb-2 hidden-sm-and-down"
                :to="'/fares/'+ $route.params.id + '/create'"
              >
                Добавить
              </v-btn>
            </v-app-bar>
            <v-card-text class="pa-0 py-0">
              <v-data-table
                :key="componentKey"
                class="elevation-1 myTable  grey lighten-3"
                :search="search"
                :headers="headers"
                :items="filtered"
                :items-per-page="filtered.length"
                fixed-header
                height="80vh"
                :hide-default-footer="true"
                dense
              >
                <template v-slot:body="{items}">
                  <tbody>
                  <template v-for="(item, i) in items">
                    <tr
                      :key="item.id"
                      :class="row_classes(item)"
                      @dblclick="editItem(item)"
                    >
                      <td>{{ i + 1 }}</td>
                      <td>
                        {{ $moment(new Date(item.created_at)).format('DD.MM.YYYY') }}
                      </td>
                      <td>{{ item.client_name }}</td>
                      <td>{{ item.region }}</td>
                      <td>
                        {{ item.phone }}
                      </td>
                      <td>{{ type(item.type) }}</td>
                      <td>
                        <v-btn v-if="item.pictures.length" color="success" x-small @click="showSlider(item)">
                          Посмотреть
                        </v-btn>
                      </td>
                    </tr>
                  </template>
                  </tbody>
                </template>
              </v-data-table>
            </v-card-text>
          </v-card>
        </v-col>
        <v-dialog v-model="dialog" max-width="800">
          <v-card>
            <v-card-text>
              <v-container>
                <v-form ref="form" v-model="valid">
                  <v-row>
                    <v-col cols="12" sm="12" md="12" class="px-6">
                      <v-text-field
                        v-model="editedItem.client_name"
                        hide-details
                        outlined
                        placeholder="Введие ФИО клиента"
                        label="ФИО клиента"
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="12" class="px-6">
                      <v-text-field
                        v-model="editedItem.phone"
                        hide-details
                        outlined
                        v-mask="'+7 ### ###-##-##'"
                        placeholder="Телефон"
                        label="Телефон"
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="12" class="px-6">
                      <v-select
                        v-model="editedItem.type"
                        :items="types"
                        item-text="type"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Транспорт"
                        hide-details
                        outlined
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="12" class="px-6">
                      <v-text-field
                        v-model="editedItem.region"
                        hide-details
                        outlined
                        placeholder="Регион"
                        label="Регион"
                      />
                    </v-col>
                  </v-row>
                </v-form>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer />
              <v-btn
                v-if="editedIndex !== -1"
                color="red darken-1"
                class="mr-3"
                text
                @click="confirmDelete(editedItem.id)"
              >
                Удалить
              </v-btn>
              <v-btn color="green darken-1" text @click="dialog = false">
                Отменить
              </v-btn>
              <v-btn color="green darken-1" text @click="save()">
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
        <FsLightbox
          :key="componentKey"
          :toggler="toggler"
          :sources="addUrl(pictures)"
          :slide="slide"
          :source-index="slide"
        />
      </v-row>
    </v-container>
  </div>
</template>
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
<script>
import FsLightbox from 'fslightbox-vue'
import BreadCrumb from '~/components/BreadCrumb'
export default {
  middleware: 'permission',
  components: { BreadCrumb, FsLightbox },
  data: () => ({
    toggler: false,
    slide: 0,
    menu: false,
    componentKey: 0,
    date: '',
    dialog: false,
    pictures: [],
    search: '',
    filter_from: '',
    filter_to: '',
    menu2: false,
    menu3: false,
    menu4: false,
    isLoading: false,
    valid: false,
    // deleteDialog
    deleteId: '',
    deleteDialog: false,
    x: null,
    y: 'bottom',
    types:[
      {
        id: 1,
        type: 'Авиа'
      },
      {
        id: 2,
        type: 'Ж/Д'
      },
      {
        id: 3,
        type: 'Авто'
      },
      {
        id: 4,
        type: 'Автобус'
      },
    ],
    headers: [
      {
        text: '№',
        value: 'number',
        sortable: false,
        fixed: true,
        width: '10px'
      },
      {
        text: 'Дата',
        value: 'created_at',
        sortable: false,
        fixed: true,
        width: '10px'
      },
      {
        text: 'Клиент',
        align: 'center',
        sortable: false,
        value: 'client_name',
        fixed: true,
        width: '140px'
      },
      {
        text: 'Телефон',
        sortable: false,
        align: 'center',
        width: '130px',
        value: 'phone',
        fixed: true
      },
      {
        text: 'Регион',
        sortable: false,
        align: 'center',
        width: '130px',
        value: 'region',
        fixed: true
      },
      {
        text: 'Транспорт',
        align: 'center',
        sortable: false,
        value: 'client_name',
        fixed: true,
        width: '140px'
      },
      {
        text: 'Фотографии',
        value: 'pictures',
        align: 'center',
        sortable: false,
        width: '30px'
      }
    ],
    editedIndex: -1,
    editedItem: {
      id: '',
      client_name: '',
      showroom_id: '',
      type: '',
      pictures: '',
      phone: '',
    },
    defaultItem: {
      id: '',
      client_name: '',
      showroom_id: '',
      type: '',
      pictures: '',
      phone: '',
    }
  }),

  computed: {
    showroom () {
      return this.$store.state.showroom.showroom
    },
    user () {
      return this.$store.state.auth.user
    },
    filtered () {
      const clients = this.$store.state.fare.fares
      if (this.filter_from === '' || this.filter_to === '') {
        return clients
      } else {
        return clients.filter(
          l => l.date >= this.$date(new Date(this.filter_from), 'Y-MM-dd') &&
            l.date <= this.$date(new Date(this.filter_to), 'Y-MM-dd')
        )
      }
    },
    role () {
      return this.$store.state.auth.role
    },
    links () {
      return [
        {
          text: 'Главная',
          disabled: false,
          href: '/' + this.role?.name + '/tablet/1'
        },
        {
          text: 'СРМ',
          disabled: false,
          href: '/crm'
        },
        {
          text: this.showroom?.name,
          disabled: false,
          href: '/crm/' + this.showroom?.id
        },
        {
          text: 'Оплата проезда',
          disabled: true,
          href: '/'
        }
      ]
    }
  },

  watch: {
    dialog (val) {
      val || this.close()
    }
  },

  async fetch ({ store, params: { id } }) {
    await store.dispatch('fare/fetchFares', {showroom_id:id})
    await store.dispatch('showroom/fetchShowroom', {id})
  },
  mounted () {
    this.handleLoading()
  },
  methods: {
    editItem (item) {
      this.editedIndex = item.id
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },
    type (id=null) {
      return this.types.find(l=>l.id === id)?.type || null
    },
    showSlider (item) {
      this.slide = 0
      this.componentKey = this.componentKey + 1
      this.pictures = item.pictures
      this.$mount()
      this.toggler = !this.toggler
    },
    addUrl (pictures) {
      const result = []
      for (const x in pictures) {
        if (x) {
          result.push(
            '/images/fares/' + pictures[x]
          )
        }
      }
      return result
    },
    confirmDelete (id) {
      this.deleteId = id
      this.deleteDialog = true
    },
    deleteItem (id) {
      this.$store
        .dispatch('fare/delete', { item: this.editedItem }).catch((error) => {
        this.$toast.error( 'Ошибка: ' + error);
      })
        .then(() => {
          this.$toast.success("Заявка удалена");
        })
      this.deleteId = ''
      this.dialog = false
      this.deleteDialog = false
    },
    close () {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
        this.componentKey += 1
      })
    },
    save () {
      if (this.valid) {
        this.$store.dispatch('fare/save', {
          item: this.editedItem
        }).catch((error) => {
          this.$toast.error('Ошибка: ' + error);
        }).then((res) => {
            this.$toast.success("Заявка изменена");
          }
        )
        this.close()
      } else {
        this.$toast.error("Заполните обязательные поля");
      }
    },
    validate () {
      this.$refs.form.validate()
    },
    handleLoading () {
      const loader = this.$loading.show({
        // Optional parameters
        container: null,
        canCancel: false,
        onCancel: null,
        color: '#42a5f6'
      })
      this.isLoading = !this.isLoading
      setTimeout(() => {
        loader.hide()
      }, 300)
    },
    row_classes (item) {
      if (item.visited === 1) {
        return 'yellow' // can also return multiple classes e.g ["orange","disabled"]
      } else if (item.not_coming === 1) {
        return 'red'
      }
    },
    clearFilter () {
      this.filter_from = ''
      this.filter_to = ''
    }
  }
}
</script>

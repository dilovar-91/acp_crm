<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links"/>
      <v-row justify="center" align="center" class="mt-0">
        <v-col cols="12">
          <v-data-table
            :headers="headers"
            :items="shipments"
            :single-expand="true"
            item-key="name"
            no-data-text="Список пуст"
            show-expand
            :items-per-page="shipments.length"
            width="100%"
            hide-default-footer
            class="elevation-1 myTable lighten-4"
            height="80vh"
            fixed-header
            :mobile-breakpoint="0"
            @dblclick:row="editRow"
          >
            <template #item.id="{ item }">
              {{ item.id }}
            </template>
            <template #item.from_showroom="{ item }">
              <v-chip
                color="yellow"
                small
              >
                {{ item.from_showroom.name }}
              </v-chip>
            </template>
            <template #item.showroom="{ item }">
              <v-chip
                color="primary"
                small
              >
                {{ item.showroom.name }}
              </v-chip>
            </template>
            <template #item.estimated_date="{ item }">
              <v-chip
                color="primary"
                small
              >{{
                  !item.estimated_date ? '' : $moment(item.estimated_date).format('DD-MM-YYYY')
                }}
              </v-chip>
            </template>
            <template #item.shipment_price="{ item }">
              <template > {{ item.shipment_price | formatCurrency({
                label: 'RUR',
                decimalDigits: 0,
                decimalSeparator: '.',
                thousandsSeparator: ' ',
                currencySymbol: ' ₽',
                currencySymbolNumberOfSpaces: 0,
                currencySymbolPosition: 'right'
              }) }}</template>
            </template>
            <template #item.actions="{ item }">
              <v-icon color="success" class="mr-2" @click="editItem(item)">
                mdi-pencil
              </v-icon>
              <v-icon color="error" @click="deleteItem(item)">
                mdi-delete
              </v-icon>
            </template>
            <template #item.status_id="{ item }">
              <v-progress-linear
                :color="getColor(item)"
                height="25"
                striped
              >
                <template #default="{ value }">
                  <strong>{{ parseInt(item.status_id) * 20 }}%</strong>
                </template>
              </v-progress-linear>
            </template>


            <template #top>
              <v-toolbar flat>
                <v-toolbar-title>
                  Транспортировки машин (Всего {{ shipments.length || 0 }})
                </v-toolbar-title>
                <v-spacer></v-spacer>
                <v-dialog v-model="dialog" max-width="800px">
                  <template #activator="{ on, attrs }">
                    <v-btn
                      color="primary"
                      dark
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
                            <v-col cols="12" sm="6" md="6">
                              <v-text-field
                                v-model="editedItem.name"
                                label="Название"
                                hide-details
                                outlined
                                dense
                              />
                            </v-col>

                            <v-col cols="12" sm="6" md="6">
                              <v-select
                                v-model="editedItem.sender_id"
                                :items="users"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                                hide-details
                                label="Отправитель"
                                outlined
                                dense
                              />
                            </v-col>

                            <v-col cols="12" sm="6" md="4">
                              <v-select
                                v-model="editedItem.receiver_id"
                                :items="users"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                                label="Получатель"
                                hide-details
                                dense
                                outlined
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="4">
                              <v-select
                                v-model="editedItem.from"
                                :items="showrooms"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                                label="Откуда"
                                dense
                                hide-details
                                outlined
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="4">
                              <v-select
                                v-model="editedItem.showroom_id"
                                :items="showrooms"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                                label="Куда"
                                hide-details
                                dense
                                outlined
                              />
                            </v-col>
                            <v-col
                              md="6"
                              cols="12"
                            >
                              <v-text-field
                                v-model="editedItem.driver"
                                label="Водитель"
                                outlined
                                dense
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" md="6" sm="6">
                              <v-menu
                                v-model="menu1"
                                :close-on-content-click="true"
                                max-width="290"
                              >
                                <template #activator="{ on, attrs }">
                                  <v-text-field
                                    v-bind="attrs"
                                    :value="dateFormatted"
                                    clearable
                                    label="Приблизительная дата доставки"
                                    outlined
                                    dense
                                    readonly
                                    hide-details
                                    v-on="on"
                                    @blur="date = $parseDate(dateFormatted)"
                                    @click:clear="editedItem.estimated_date = null"
                                  />
                                </template>
                                <v-date-picker
                                  v-model="editedItem.estimated_date"
                                  locale="ru-RU"
                                  @change="menu1 = false"
                                />
                              </v-menu>
                            </v-col>
                            <v-col
                              md="6"
                              cols="12"
                            >
                              <v-text-field v-model="editedItem.shipment_price" outlined type="number" label="Цена" hide-details dense />
                            </v-col>
                            <v-col
                              md="6"
                              cols="12"
                            >
                              <v-select
                                v-model="editedItem.status_id"
                                :items="statuses"
                                item-text="name"
                                item-value="id"
                                :menu-props="{ maxHeight: '650' }"
                                label="Статус"
                                persistent-hint
                                hide-details
                                outlined
                                dense
                              />
                            </v-col>
                            <v-col
                              md="12"
                              cols="12"
                            >
                              <v-textarea v-model="editedItem.comment" hide-details dense outlined label="Комментарий" rows="1" />
                            </v-col>
                          </v-row>
                        </v-form>
                        <div v-if="editedItem.id" class="">Новые машины <br> <v-autocomplete

                          v-model="editedItem.car_id"
                          :items="cars"
                          :item-text="item => item.mark.name +' '+ item.model.name+' '+ item.showroom.name+' '+ item.vin_number"
                          item-value="id"
                          label="Выбериту машину"
                          persistent-hint
                          hide-details
                          append-outer-icon="mdi-plus"
                          outlined
                          dense
                          @click:append-outer="addCar()"
                        > </v-autocomplete>

                        </div>
                        <v-data-table
                          v-if="editedItem.id"
                          dense
                          :headers="headers2"
                          :items="editedItem.cars"
                          :items-per-page="editedItem.cars && editedItem.cars.length"
                          class="elevation-1 mt-2"
                          hide-default-footer
                          no-data-text="Список пуст"
                        ></v-data-table>
                        <div v-if="editedItem.id" class="text-overline">Поддержанные <br> <v-autocomplete
                          v-model="editedItem.usedcar_id"
                          :items="used_cars"
                          :item-text="item => item.mark.name +' '+ item.model.name+' '+ item.showroom.name+' '+ (item.vin_number ?? '')"
                          item-value="id"
                          label="Выбериту машину"
                          persistent-hint
                          hide-details
                          append-outer-icon="mdi-plus"
                          outlined
                          dense
                          @click:append-outer="addCar(false)"
                        > </v-autocomplete>
                        </div>
                        <v-data-table
                          v-if="editedItem.id"
                          dense
                          :headers="headers2"
                          :items="editedItem.used_cars"
                          :items-per-page="editedItem.used_cars && editedItem.used_cars.length"
                          class="elevation-1 mt-2"
                          no-data-text="Список пуст"
                          hide-default-footer
                        ></v-data-table>

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
            </template>
            <template #expanded-item="{ headers, item }">
              <td :colspan="headers.length" @dblclick="editItem(item)">
                <v-card class="p-3 mt-2" >
                  <v-card-title>Новые машины</v-card-title>
                  <v-card-text>
                    <v-data-table
                      color="grey lighten-1"
                      dense
                      :headers="headers2"
                      :items="item.cars"
                      :items-per-page="3"
                      class="elevation-1"
                      no-data-text="Список пуст"
                      hide-default-footer
                    ></v-data-table>
                  </v-card-text>
                </v-card>
                <v-card class="p-3 mt-2" >
                  <v-card-title>Бу машины</v-card-title>
                  <v-card-text>
                    <v-data-table
                      dense
                      :headers="headers3"
                      :items="item.used_cars"
                      :items-per-page="item.used_cars.length"
                      class="elevation-1"
                      color="grey lighten-1"
                      no-data-text="Список пуст"
                      hide-default-footer
                    ></v-data-table>
                  </v-card-text>
                </v-card>
              </td>
            </template>
          </v-data-table>
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
  middleware: 'permission',
  data: () => ({
    dialog: false,
    valid: false,
    menu1: false,
    headers: [
      {
        text: '№',
        value: 'id',
        align: 'center',
        sortable: true,
        width: 100
      },
      {
        text: 'Название',
        value: 'name',
        align: 'center',
        sortable: true,
        width: 100
      },
      {
        text: 'Оправитель',
        value: 'sender.name',
        align: 'center',
        sortable: false,
        width: 100
      },
      {
        text: 'Получатель',
        value: 'receiver.name',
        align: 'center',
        sortable: false,
        width: 100
      },
      {
        text: 'От куда',
        value: 'from_showroom',
        align: 'center',
        sortable: true,
        width: 100
      },
      {
        text: 'Куда',
        value: 'showroom',
        align: 'center',
        sortable: true,
        width: 100
      },
      {
        text: 'Водитель',
        value: 'driver',
        align: 'center',
        sortable: true,
        width: 100
      },
      {
        text: 'Приблизительная дата доставки',
        value: 'estimated_date',
        align: 'center',
        sortable: true,
        width: 100
      },
      {
        text: 'Цена доставки',
        value: 'shipment_price',
        align: 'center',
        sortable: true,
        width: 100
      },
      {
        text: 'Статус',
        value: 'status.name',
        align: 'center',
        sortable: true,
        width: 160
      },
      {
        text: 'Прогресс',
        value: 'status_id',
        align: 'center',
        sortable: true,
        width: 160
      },
      {
        text: 'Комментрарии',
        value: 'comment',
        align: 'center',
        sortable: true,
        width: 160
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
      sender_id: '',
      receiver_id: '',
      showroom_id: '',
      from: '',
      driver: '',
      estimated_date: '',
      shipment_price: '',
      company_id: '',
      car_id: '',
      usedcar_id: '',
      comment: '',
      status_id: ''
    },
    defaultItem: {
      id: '',
      name: '',
      sender_id: '',
      receiver_id: '',
      showroom_id: '',
      from: '',
      driver: '',
      estimated_date: '',
      shipment_price: '',
      company_id: '',
      car_id: '',
      usedcar_id: '',
      comment: '',
      status_id: ''
    },
    detailItem: {
      car_id: '',
      shipment_id: '',
      is_used: '',
      quantity: '',
    },
    headers2: [
      {
        text: 'ID',
        align: 'start',
        sortable: false,
        value: 'id'
      },
      { text: 'Марка', value: 'car.mark.name' },
      { text: 'Модель', value: 'car.model.name' },
      { text: 'Vin', value: 'car.vin_number' },
    ],
    headers3: [
      {
        text: 'ID',
        align: 'start',
        sortable: false,
        value: 'id'
      },
      { text: 'Марка', value: 'car.mark.name' },
      { text: 'Модель', value: 'car.model.name' },
      { text: 'Vin', value: 'car.vin_number' },
    ],
  }),

  async fetch({store}) {
    await store.dispatch('shipment/fetchShipments', {})
    await store.dispatch('shipment/fetchCompanies')
    await store.dispatch('shipment/fetchDrivers')
    await store.dispatch('shipment/fetchStatuses')
    await store.dispatch('showroom/fetchShowrooms')
    await store.dispatch('car/fetchCars', {})
    await store.dispatch('usedcar/fetchCars', {})
    await store.dispatch('user/fetchUsers')
  },

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? 'Добавить пользователя' : 'Изменить'
    },
    shipments() {
      return this.$store.state.shipment.shipments
    },
    showrooms() {
      return this.$store.state.showroom.showrooms
    },
    companies() {
      return this.$store.state.shipment.companies
    },
    drivers() {
      return this.$store.state.shipment.drivers
    },
    statuses() {
      return this.$store.state.shipment.statuses
    },
    users() {
      return this.$store.state.user.users
    },
    cars() {
      return this.$store.state.car.cars
    },
    used_cars() {
      return this.$store.state.usedcar.cars
    },
    links() {
      return [
        {
          text: 'Администратор',
          disabled: false,
          href: '/'
        },
        {
          text: 'Транзиты',
          disabled: true,
          href: '/shipments'
        }
      ]
    },

    dateFormatted () {
      return this.editedItem.estimated_date ? this.$moment(this.editedItem.estimated_date).format('DD.MM.Y') : null
    },
  },
  watch: {
    dialog(val) {
      val || this.close()
    }
  },
  methods: {
    save() {
      if (this.valid) {
        this.editedItem.estimated_date  =  this.editedItem.estimated_date ? this.$moment(this.editedItem.estimated_date).format('YYYY-MM-DD') : null
        if (this.editedIndex > -1) {
          this.$store.dispatch('shipment/save', {
            item: this.editedItem
          })

          this.$toast.success("Данные изменены");
        } else {
          this.$store.dispatch('shipment/save', {
            item: this.editedItem
          })

          this.$toast.success("Данные добавлены");
        }
        this.close()
      } else {
        this.$toast.error("Заполните обязательные поля");
      }
    },

    editRow(event, {item}) {
      this.editedIndex = this.shipments.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },

    editItem(item) {
      this.editedIndex = this.shipments.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },

    row_classes(item) {
    },

    async addCar(isNew=true) {
      this.detailItem.is_used = isNew
      this.detailItem.shipment_id = this.editedItem.id
      if (isNew && !this.editedItem.car_id) {
        this.$toast.error("Выберите новую машину");
        return
      }
      if (!isNew && !this.editedItem.usedcar_id) {
        this.$toast.error("Выберите БУ машину");
        return
      }

      if (isNew) {
          this.detailItem.car_id = this.editedItem.car_id
        await this.$axios.post('/shipment/car/add', {item: this.detailItem})
          .then((response) => {
            if (response.data) {
              this.$store.dispatch('shipment/fetchShipments', {})
              this.editedItem = Object.assign({}, this.shipments[this.editedIndex])
            }
          })
          .catch((error) => {
            console.log(error)
          })

          this.$toast.success("Данные изменены");
        } else {
           this.detailItem.car_id = this.editedItem.usedcar_id
          this.$axios.post('/shipment/used-car/add', {item: this.detailItem})
          .then((response) => {
            if (response.data) {
              this.$store.dispatch('shipment/fetchShipments', {})
              this.editedItem = Object.assign({}, this.shipments[this.editedIndex])
            }
          })
          .catch((error) => {
            console.log(error)
          })

          this.$toast.success("Данные добавлены");
        }
      this.close()
    },

    getColor(val) {
      if (val.status_id === 1) {
        return 'error' // can also return multiple classes e.g ["orange","disabled"]
      }
      else if (val.status_id === 2) {
        return 'blue' // can also return multiple classes e.g ["orange","disabled"]
      }
      else if (val.status_id === 3) {
        return 'yellow' // can also return multiple classes e.g ["orange","disabled"]
      }
      else if (val.status_id === 4) {
        return 'orange' // can also return multiple classes e.g ["orange","disabled"]
      }
      else if (val.status_id === 5) {
        return 'success' // can also return multiple classes e.g ["orange","disabled"]
      }
    },
    deleteItem(item) {
      confirm('Вы уверены, что хотите удалить?') &&
      this.$store
        .dispatch('shipment/delete', {
          id: item.id
        })
        .then(() => {
          this.$toast.success("Запись удалена");
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
    validate() {
      this.$refs.form.validate()
    },
    getCars (item, isNew=true) {
      if(item instanceof Array) {
        let cars
        if (isNew) {
          cars = item.details.filter(l=>l.is_used !== 1);
        }
        else {
          cars = item.details.filter(l=>l.is_used === 1);
        }
        return cars;
      } else return

    },
    goTo(url) {
      try {
        this.$router.push(url).catch(e => console.log(e))
      } catch {
        console.log(123)
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
  border: 1px solid black;
  padding: 0 4px !important;
  text-align: center;
}
.td_style {
  border: 1px solid black;
  padding: 0 4px !important;
  text-align: center;
}

table th {
  border: 1px solid rgb(204, 200, 200);
  padding: 0 !important;
  font-weight: 900;
  color: black;
}

.v-data-table > .v-data-table__wrapper > table > thead > tr > th {
  font-weight: 900;
  color: black !important;
}

.v-data-table > .v-data-table__wrapper > table > tfoot > tr > th {
  padding: 0 4px;
}

.v-data-table > .v-data-table__wrapper > table > tbody > tr > td {
  font-size: 0.875rem;
  height: 0;
}

table.v-table tbody td:first-child,
table.v-table tbody td:not(:first-child),
table.v-table tbody th:first-child,
table.v-table tbody th:not(:first-child),
table.v-table thead td:first-child,
table.v-table thead td:not(:first-child),
table.v-table thead th:first-child,
table.v-table thead th:not(:first-child) {
  padding: 0 4px;
  font-size: 0.875rem;
}

</style>

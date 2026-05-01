<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links" />
      <v-row align="start" class="d-flex" no-gutters>
        <v-col cols="12">
          <v-data-table
            :key="componentKey"
            :headers="headers"
            :items="filtered"
            :items-per-page="usedcars.length"
            :options="options"
            class="elevation-1  grey lighten-4"
            fixed-header
            height="80vh"
            hide-default-footer
            item-key="id"
          >
            <template v-slot:body="{ items }">
              <tbody>
                <tr v-for="(item, i)  in items"
                    :key="item.name" :class="row_classes(item)"
                >
                  <td>
                   {{(i+1)}}
                  </td>
                  <td>
                    <span v-if="item.auto_ru !==null"><a :href="item.auto_ru" class="link" target="_blank">
                      Перейти
                    </a></span>
                  </td>

                  <td>
                    {{ item.mark && item.mark.name }}
                  </td>
                  <td>
                    {{ item.model && item.model.name }}
                  </td>
                  <td>{{ item.year }}</td>
                  <td>{{ item.bodytype && item.bodytype.name }}</td>
                  <td>{{ item.owner_count }}</td>
                  <td :style="(item.is_ready ? 'background-color:#4CAF50 !important; color: white;': '')">
                    {{ item.milage }}
                  </td>
                  <td>{{ item.wheeltype && item.wheeltype.name }}</td>
                  <td>{{ item.transmission && item.transmission.name }}</td>
                  <td>{{ item.volume }} ({{ item.power }} ЛС)</td>
                  <td>{{ item.enginetype && item.enginetype.name }}</td>
                  <td>{{ item.salon }}</td>
                  <td>{{ item.color }}</td>
                  <td>{{ item.vin_number }}</td>
                </tr>
              </tbody>
            </template>

            <template v-slot:top>
              <v-toolbar dense>
                <v-row class="mt-4 hidden-sm-and-down">
                  <v-col cols="3" md="5" xs="6">
                    <v-row>
                      <v-col cols="3" md="3" xs="6">
                        <v-select
                          v-model="filter.mark_id"
                          :items="marks"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Марка"
                          outlined
                          @change="getModels(filter.mark_id)"
                          @click="filter.model_id=null"
                          @click:clear="$nextTick(() => filter.mark_id=null)"
                        />
                      </v-col>
                      <v-col cols="3" md="3" xs="6">
                        <v-select
                          v-model="filter.model_id"
                          :items="models"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Модель"
                          outlined
                          @click="deleted = false"
                          @click:clear="$nextTick(() => filter.model_id=null)"
                        >
                          <template v-slot:no-data>
                            <span class="small">Выберите марку</span>
                          </template>
                        </v-select>
                      </v-col>
                      <v-col cols="3" md="3" xs="6">
                        <v-select
                          v-model="filter.transmission_id"
                          :items="transmissions"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Коробка"
                          outlined
                          @click="deleted = false"
                          @click:clear="$nextTick(() => filter.transmission_id=null)"
                        />
                      </v-col>
                      <v-col cols="3" md="3" xs="6">
                        <v-select
                          v-model="filter.wheeltype_id"
                          :items="wheeltypes"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Привод"
                          outlined
                          @click="deleted = false"
                          @click:clear="$nextTick(() => filter.wheeltype_id=null)"
                        />
                      </v-col>
                    </v-row>
                  </v-col>
                  <v-col cols="3" md="7" xs="6">
                    <v-row>
                      <v-col cols="3" md="2" xs="4">
                        <v-select
                          v-model="filter.year_from"
                          :items="range(2023, 1960)"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Год от"
                          outlined
                          @click="deleted = false"
                          @click:clear="$nextTick(() => filter.year_from=null)"
                        />
                      </v-col>
                      <v-col cols="3" md="2" xs="6">
                        <v-select
                          v-model="filter.year_to"
                          :items="range(2023, 1960)"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Год до"
                          outlined
                          @click="deleted = false"
                          @click:clear="$nextTick(() => filter.year_to=null)"
                        />
                      </v-col>
                      <v-col cols="3" md="2" xs="6">
                        <v-text-field v-model="filter.milage_from" clearable dense label="Пробег от" outlined
                                      @keypress.native="isNumber($event)"
                        />
                      </v-col>
                      <v-col cols="3" md="2" xs="6">
                        <v-text-field v-model="filter.milage_to" clearable dense label="Пробег до" outlined
                                      @keypress.native="isNumber($event)"
                        />
                      </v-col>
                      <v-col cols="3" md="2" xs="6">
                        <v-text-field v-model="filter.incomePriceFrom" clearable dense label="Приход от" outlined @keypress.native="isNumber($event)" />
                      </v-col>
                      <v-col cols="3" md="2" xs="6">
                        <v-text-field v-model="filter.incomePriceTo" clearable dense label="Приход до" outlined />
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>
                <v-btn color="error" dark @click="clearFilter()">
                  Сбросить
                </v-btn>
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
            <template v-slot:action="{ attrs }">
              <v-btn color="deep-orange" icon @click="snackbar = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </template>
          </v-snackbar>
        </v-col>
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
  border: 1px solid black;
  padding: 0 1px !important;
  text-align: center;
}

table th {
  border: 1px solid rgb(204, 200, 200);
  padding: 0 4px !important;
  font-weight: 700;
  color: black;
}

.v-data-table > .v-data-table__wrapper > table > thead > tr > th {
  padding: 0 4px !important;

  font-weight: 700;
  font-size: 0.7rem;
  color: black !important;
}

table.v-data-table__wrapper > table > thead > tr > th {
  padding: 0 6px;
}

.v-data-table > .v-data-table__wrapper > table > tfoot > tr > th {
  padding: 0 2px;
  font-size: 0.7rem;

}

.v-data-table > .v-data-table__wrapper > table > tbody > tr > td {
  font-size: 0.7rem;
  font-weight: 600;
  height: 0px;
}

table.v-table tbody td:first-child,
table.v-table tbody td:not(:first-child),
table.v-table tbody th:first-child,
table.v-table tbody th:not(:first-child),
table.v-table thead td:first-child,
table.v-table thead td:not(:first-child),
table.v-table thead th:first-child,
table.v-table thead th:not(:first-child) {
  padding: 0 2px !important;
  font-size: 0.7rem;
  font-weight: 500;
}

.vertical {
  writing-mode: tb-rl;
  font-size: 0.70rem;
  -webkit-transform: rotate(180deg);
  -moz-transform: rotate(180deg);
  -o-transform: rotate(180deg);
  -ms-transform: rotate(180deg);
  transform: rotate(180deg);
  white-space: nowrap;
  display: block;
  bottom: 0;
  height: 60px;
  margin-bottom: 5px;
  align-content: center;
  padding: 0;
}

.v-data-table
/deep/
tbody
/deep/
tr:hover:not(.v-data-table__expanded__content) {
  background: yellow !important;
}
</style>
<script>
import BreadCrumb from '~/components/BreadCrumb'

export default {
  layout: 'user',
  middleware: 'permission',
  components: { BreadCrumb },
  data: () => ({
    menu1: null,
    componentKey: 0,
    filter: {
      mark_id: null,
      model_id: null,
      transmission_id: null,
      wheeltype_id: null,
      year_from: null,
      year_to: null,
      milage_from: null,
      milage_to: null,
      incomePriceFrom: null,
      incomePriceTo: null
    },
    deleted: false,
    menu: null,
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
        text: '№',
        value: 'id',
        align: 'center',
        sortable: false,
        width: '40px'
      },
      {
        text: 'Авто.ру',
        value: 'auto_ru',
        align: 'center',
        sortable: false,
        width: '30px'
      },
      {
        text: 'Салон',
        align: 'center',
        sortable: false,
        value: 'showroom.name',
        width: '30px'
      },
      {
        text: 'Машина',
        align: 'start',
        value: 'mark.name',
        width: '100px'
      },
      {
        text: 'Год',
        value: 'year',
        sortable: false,
        width: '50px',
        align: 'center'
      },
      {
        text: 'Кузов',
        value: 'bodytype.name',
        sortable: false,
        width: '50px',
        align: 'center'
      },
      {
        text: 'Соб.',
        value: 'owner_count',
        sortable: false,
        width: '10px',
        align: 'center'
      },
      {
        text: 'Пробег',
        value: 'milage',
        sortable: false,
        width: '20px',
        align: 'center'
      },
      {
        text: 'Привод',
        value: 'wheeltype.name',
        sortable: false,
        width: '25px',
        align: 'center'
      },
      {
        text: 'КПП',
        value: 'transmission.name',
        width: '25px',
        sortable: false,
        align: 'center'
      },
      {
        text: 'Объем',
        value: 'volume',
        sortable: false,
        width: '70px',
        align: 'center'
      },

      {
        text: 'Двиг.',
        value: 'enginetype.name',
        sortable: false,
        width: '20px',
        align: 'center'
      },
      {
        text: 'Салон',
        value: 'salon',
        sortable: false,
        width: '20px',
        align: 'center'
      },
      {
        text: 'Цвет',
        value: 'color',
        align: 'center',
        sortable: false,
        width: '100px'
      },
      {
        text: 'Vin',
        value: 'vin_number',
        align: 'center',
        sortable: false,
        width: '120px'
      },
      {
        text: 'Vin',
        value: 'vin_number',
        align: 'center',
        sortable: false,
        width: '120px'
      },
      {
        text: 'Комментарий оператор',
        value: 'comment_operator',
        align: 'center',
        sortable: false,
        width: '120px'
      },
      {
        text: 'Фото',
        value: 'pic',
        align: 'center',
        sortable: false,
        width: '120px'
      }
    ],

    options: {
      sortBy: ['mark.name', 'model.name'],
      multiSort: true
    }

  }),

  computed: {
    dateFormatted () {
      return this.editedItem.income_date ? this.moment(this.editedItem.income_date).format('DD.MM.Y') : null
    },
    showroom_id () {
      return Number(this.$route.params.id) || null
    },
    showroom () {
      return this.$store.state.showroom.showroom
    },
    formTitle () {
      return this.editedIndex === -1 ? 'Добавление' : 'Изменить'
    },
    usedcars () {
      return this.$store.state.usedcar.light_cars
    },
    filtered () {
      let cars = this.usedcars
      if (this.deleted) {
        cars = cars.filter(item => item.deleted_at !== null)
      } else {
        cars = cars.filter(item => item.deleted_at === null)
      }
      if (this.filter.mark_id) {
        cars = cars.filter(item => item.mark_id === this.filter.mark_id && item.deleted_at === null)
      }
      if (this.filter.model_id) {
        cars = cars.filter(item => item.model_id === this.filter.model_id && item.deleted_at === null)
      }
      if (this.filter.transmission_id) {
        cars = cars.filter(item => item.transmission_id === this.filter.transmission_id && item.deleted_at === null)
      }
      if (this.filter.wheeltype_id) {
        cars = cars.filter(item => item.wheeltype_id === this.filter.wheeltype_id && item.deleted_at === null)
      }
      if (this.filter.year_from && this.filter.year_to) {
        cars = cars.filter(item => item.year >= this.filter.year_from && item.year <= this.filter.year_to)
      } else if (this.filter.year_from) {
        cars = cars.filter(item => item.year >= this.filter.year_from)
      } else if (this.filter.year_to) {
        cars = cars.filter(item => item.year <= this.filter.year_to)
      }
      if (this.filter.milage_from && this.filter.milage_to) {
        cars = cars.filter(item => item.milage >= this.filter.milage_from && item.milage <= this.filter.milage_to)
      } else if (this.filter.milage_from) {
        cars = cars.filter(item => item.milage >= this.filter.milage_from)
      } else if (this.filter.milage_to) {
        cars = cars.filter(item => item.milage <= this.filter.milage_to)
      }
      return cars
    },
    marks () {
      return this.$store.state.car.marks
    },
    models () {
      return this.$store.state.car.models
    },
    enginetypes () {
      return this.$store.state.car.enginetypes
    },
    transmissions () {
      return this.$store.state.car.transmissions
    },
    wheeltypes () {
      return this.$store.state.car.wheeltypes
    },
    user () {
      return this.$store.state.auth.user
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
          text: 'Список БУ для руководителей',
          disabled: false,
          href: '/' + this.role?.name + '/used-cars'
        },
        {
          text: this.showroom.name || '',
          disabled: true,
          href: '/' + this.role?.name + '/used-cars/cars/' + (this.showroom.id || '')
        }
      ]
    }
  },
  watch: {
    deleted(val) {
      if (val === true) {
        this.$store.dispatch('usedcar/fetchLightCars', {
          showroom_id: this.showroom_id,
          withTrashed: true
        }).catch((error) => {
        })
      } else {
        this.$store.dispatch('usedcar/fetchLightCars', {}).catch((error) => {

        })
        this.clearFilter()
      }
    }
  },

  async fetch ({ store, error, params: { id } }) {
    await store.dispatch('usedcar/fetchLightCars', {showroom_id: id})
    await store.dispatch('car/fetchMarks')
    await store.dispatch('car/fetchBodyTypes')
    await store.dispatch('car/fetchEngineTypes')
    await store.dispatch('car/fetchTransmissions')
    await store.dispatch('car/fetchWheelTypes')
    await store.dispatch('showroom/fetchShowroom', { id })
    await store.dispatch('user/toggle', false)
  },
  mounted () {
    this.$echo.channel('used-cars')
      .listen('UsedCarAdded', (e) => {
        console.log('usedcar added')
        this.$store.dispatch('usedcar/fetchCars').catch((error) => {
          console.log(error)
          this.reload()
        })
      })
  },
  methods: {
    save () {
      if (this.valid) {
        this.$store.dispatch('usedcar/ready', {
          id: this.editedItem.id,
          diagnostic_before: this.editedItem.diagnostic_before,
          diagnostic_after: this.editedItem.diagnostic_after,
          dry_cleaning: this.editedItem.dry_cleaning,
          polish: this.editedItem.polish,
          painting: this.editedItem.painting,
          isReady: this.editedItem.is_ready,
          comment: this.editedItem.comment
        })
        this.showSnack('success', 'Статус изменён')

        this.close()
      } else {
        this.showSnack('error', 'Заполните обязательные поля')
      }
    },
    clearFilter () {
      this.filter.mark_id = null
      this.filter.model_id = null
      this.filter.transmission_id = null
      this.filter.wheeltype_id = null
      this.filter.milage_from = null
      this.filter.milage_to = null
      this.filter.year_from = null
      this.filter.year_to = null
      this.$store.dispatch('car/emptyModels')
    },

    range (start, end, step = 1) {
      const years = []
      if (start > end) {
        for (let i = start; i >= end; i = i - step) {
          if (step === 1) {
            years.push(i)
          } else {
            years.push(i.toFixed(1))
          }
        }
      } else {
        for (let i = start; i <= end; i = i + step) {
          if (step === 1) {
            years.push(i)
          } else {
            years.push(i.toFixed(1))
          }
        }
      }
      return years
    },
    getModels (markId = null) {
      if (markId > 0) {
        this.$store.dispatch('car/fetchModels', { markId })
      }
    },
    row_classes (item) {
      if (item.status_id === 5) {
        return 'primary white--text'
      } else if (item.transit === 1 || item.transit === 2) {
        return 'orange lighten-2' // can also return multiple classes e.g ["orange","disabled"]
      } else if (item.deleted_at !== null) {
        return 'black lighten-4 white--text'
      }
    },

    getColor (transit) {
      if (transit === 1) {
        return 'primary' // can also return multiple classes e.g ["orange","disabled"]
      } else if (transit === 2) {
        return 'error'
      } else {
        return 'success'
      }
    },
    transitStatus (item) {
      if (item.transit === 1 && item.showroom_id === this.showroom_id) {
        return 'На выезде (Отменить)' // can also return multiple classes e.g ["orange","disabled"]
      } else if (item.transit === 1) {
        return 'Принять транзит'
      } else {
        return 'Передать в другой салон'
      }
    },
    isNumber (evt) {
      evt = (evt) || window.event
      const charCode = (evt.which) ? evt.which : evt.keyCode
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        evt.preventDefault()
      } else {
        return true
      }
    },
    oldStyle (date) {
      return (date !== null && this.moment().diff(this.moment(date), 'days') > 90) ? 'background-color: red; color: white;' : ''
    },
    reload () {
      this.intervalid1 = setInterval(() => {
        console.log('reload')
        this.$store.dispatch('usedcar/fetchLightCars', {showroom_id: this.showroom_id})
      }, 15000)
    },
    close () {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem.is_ready = null
        this.componentKey += 1
      })
    },
    showSnack (type, text) {
      this.text = text
      this.color = type
      this.snackbar = true
    }

  },
  beforeRouteLeave (to, from, next) {
    this.$store.dispatch('user/toggle', true)
    next()
  }
}
</script>

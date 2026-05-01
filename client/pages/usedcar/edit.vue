<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links" />
      <v-row no-gutters align="start" class="d-flex">
        <v-col cols="12">
          <v-data-table
            :key="componentKey"
            dense
            :headers="headers"
            :items="filtered"
            :options="options"
            :items-per-page="filtered.length"
            item-key="id"
            hide-default-footer
            class="elevation-1 mytable grey lighten-4"
            height="80vh"
            fixed-header
          >
            <template v-slot:body="{ items }">
              <tbody>
                <tr
                  v-for="item in items"
                  :key="item.name"
                  :class="row_classes(item)"
                  @dblclick="editItem(item)"
                >
                  <td>
                    <v-chip
                      v-if="item.showroom && item.showroom.id % 2 !== 1"
                      color="primary"
                      x-small
                    >
                      {{ item.showroom && item.showroom.name }}
                    </v-chip>
                    <v-chip v-else color="green" text-color="white" x-small>
                      {{ item.showroom && item.showroom.name }}
                    </v-chip>
                  </td>
                  <td class="text-left">
                    {{ item.mark && item.mark.name }}
                  </td>
                  <td class="text-left">
                    {{ item.model && item.model.name }}
                  </td>
                  <td>{{ item.year }}</td>
                  <td>{{ item.milage }}</td>
                  <td>{{ item.wheeltype && item.wheeltype.name }}</td>
                  <td>{{ item.transmission && item.transmission.name }}</td>
                  <td>{{ item.volume }} ({{ item.power }} ЛС)</td>
                  <td>{{ item.enginetype && item.enginetype.name }}</td>
                  <td>{{ item.color }}</td>
                  <td>{{ item.price }}</td>
                </tr>
              </tbody>
            </template>
            <template v-slot:top>
              <v-toolbar flat>
                <v-row class="mt-8 hidden-sm-and-down" dense>
                  <v-col cols="6" md="6" xs="12">
                    <v-row class="mb-1" dense>
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
                          @click="filter.model_id = null"
                          @click:clear="
                            $nextTick(() => (filter.mark_id = null))
                          "
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
                          @click:clear="
                            $nextTick(() => (filter.model_id = null))
                          "
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
                          @click:clear="
                            $nextTick(() => (filter.transmission_id = null))
                          "
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
                          @click:clear="
                            $nextTick(() => (filter.wheeltype_id = null))
                          "
                        />
                      </v-col>
                    </v-row>
                  </v-col>
                  <v-col cols="6" md="6" xs="12">
                    <v-row dense>
                      <v-col cols="3" md="3" xs="6">
                        <v-text-field
                          v-model="filter.milage_from"
                          clearable
                          dense
                          label="Пробег от"
                          outlined
                          @keypress.native="isNumber($event)"
                        />
                      </v-col>
                      <v-col cols="3" md="3" xs="6">
                        <v-text-field
                          v-model="filter.milage_to"
                          clearable
                          dense
                          label="Пробег до"
                          outlined
                          @keypress.native="isNumber($event)"
                        />
                      </v-col>
                      <v-col cols="3" md="3" xs="6">
                        <v-text-field
                          v-model="filter.incomePriceFrom"
                          clearable
                          dense
                          label="Приход от"
                          outlined
                        />
                      </v-col>
                      <v-col cols="3" md="3" xs="6">
                        <v-text-field
                          v-model="filter.incomePriceTo"
                          clearable
                          dense
                          label="Приход до"
                          outlined
                        />
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>
                <v-dialog v-model="dialog" max-width="1100px" persistent>
                  <v-card>
                    <v-card-title>
                      <span class="headline">{{ formTitle }}</span>
                      <v-spacer />
                      <v-btn
                        icon
                        rounded
                        large
                        color="red"
                        @click="dialog = false"
                      >
                        <v-icon>mdi-close</v-icon>
                      </v-btn>
                    </v-card-title>
                    <v-card-text>
                      <v-container>
                        <v-form ref="form" v-model="valid">
                          <v-row>
                            <v-col cols="12" sm="6" md="3">
                              <v-select
                                v-model="editedItem.mark_id"
                                :items="marks"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                                label="Марка"
                                hide-details
                                :rules="[(v) => !!v || 'Выберите марку']"
                                required
                                @change="getModels(editedItem.mark_id)"
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-select
                                v-model="editedItem.model_id"
                                :items="models"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                                label="Модель"
                                hide-details
                                :rules="[(v) => !!v || 'Выберите модель']"
                                required
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-select
                                v-model="editedItem.showroom_id"
                                :items="[
                                  { id: 1, name: 'Склад' },
                                  { id: 2, name: 'КомфортАвто' },
                                ]"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                                label="Шоурум"
                                hide-details
                                disabled
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-select
                                v-model="editedItem.year"
                                :items="range(2023, 1960)"
                                menu-props="auto"
                                label="Год выпуска"
                                :rules="[(v) => !!v || 'Выберите год выпуска']"
                                hide-details
                                required
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="2">
                              <v-select
                                v-model="editedItem.bodytype_id"
                                :items="bodytypes"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                                label="Кузов"
                                :rules="[(v) => !!v || 'Выберите тип кузова']"
                                hide-details
                                required
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="2">
                              <v-select
                                v-model="editedItem.wheeltype_id"
                                :items="wheeltypes"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                                label="Привод"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="2">
                              <v-select
                                v-model="editedItem.transmission_id"
                                :items="transmissions"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                                label="КПП"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="2">
                              <v-select
                                v-model="editedItem.volume"
                                :items="range(0.8, 5, 0.1)"
                                menu-props="auto"
                                label="Объем"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="2">
                              <v-select
                                v-model="editedItem.enginetype_id"
                                :items="enginetypes"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                                label="Двигатель"
                                hide-details
                              />
                            </v-col>

                            <v-col cols="12" sm="6" md="2">
                              <v-text-field
                                v-model="editedItem.color"
                                label="Цвет"
                                hide-details
                              />
                            </v-col>

                            <v-col cols="12" sm="6" md="3">
                              <v-text-field
                                v-model="editedItem.price"
                                label="Продажа"
                                hide-details
                                @keypress.native="isNumber($event)"
                              />
                            </v-col>

                            <v-col cols="12" sm="6" md="3">
                              <v-select
                                v-model="editedItem.status_id"
                                :items="statuses"
                                item-text="status"
                                item-value="id"
                                menu-props="auto"
                                label="Статус"
                                hide-details
                              />
                            </v-col>

                            <v-col cols="12" sm="6" md="6">
                              <v-text-field
                                v-model="editedItem.comment"
                                label="Коментарии"
                              />
                            </v-col>
                            <v-col cols="12" md="12">
                              <vue-dropzone
                                id="dropzone"
                                ref="myVueDropzone"
                                accepted-file-types=".jpg,.png,.jpeg"
                                :options="dropzoneOptions"
                                :destroy-dropzone="false"
                                @vdropzone-removed-file="vFileRemoved"
                                @vdropzone-success="vFileAdded"
                              />
                            </v-col>
                          </v-row>
                        </v-form>
                      </v-container>
                    </v-card-text>

                    <v-card-actions>
                      <v-spacer />
                      <v-switch
                        v-model="editedItem.expose"
                        label="Выставить"
                        color="success"
                        hide-details
                        class="mb-5 mr-2"
                      />
                      <v-btn
                        color="error darken-1"
                        :disabled="editedItem.id === ''"
                        text
                        @click="deleteItem(editedItem.id)"
                      >
                        {{
                          editedItem.deleted_at === null
                            ? 'Удалить'
                            : 'Удалить из корзины'
                        }}
                      </v-btn>
                      <v-btn color="blue darken-1" text @click="close">
                        Отмена
                      </v-btn>
                      <v-btn
                        color="blue darken-1"
                        :disabled="!canDownload()"
                        text
                        @click="save()"
                      >
                        Сохранить
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-dialog>
                <v-btn color="error" dark class="ml-1" @click="clearFilter()">
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
<style scoped>
table {
  border: none;
  border-collapse: collapse;
}
table td {
  border: 1px solid black;
  padding: 0 2px !important;
  text-align: center;
}
table th {
  border: 1px solid rgb(204, 200, 200);
  padding: 0 !important;
  font-weight: 900;
  color: black;
}

.theme--light.v-data-table > .v-data-table__wrapper > table > thead > tr > th {
  font-weight: 900;
  color: black !important;
}
.v-data-table > .v-data-table__wrapper > table > tfoot > tr > th {
  padding: 0 2px;
}
.v-data-table > .v-data-table__wrapper > table > tbody > tr > td {
  font-size: 0.8rem;
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
  padding: 0 2px;
  font-size: 0.8rem;
  font-weight: 500;
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
import vue2Dropzone from 'vue2-dropzone'
import BreadCrumb from '~/components/BreadCrumb'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
import axios from 'axios'
export default {
  layout: 'user',
  middleware: 'permission',
  components: { BreadCrumb, vueDropzone: vue2Dropzone },
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
      incomePriceTo: null,
    },
    dropzoneOptions: {
      url: '/api/usedcar/upload',
      thumbnailWidth: 300,
      maxFilesize: 3,
      // parallelUploads: 1,
      maxFiles: 8,
      headers: {},
      addRemoveLinks: true,
      dictDefaultMessage:
        "<i class='v-icon mdi mdi-camera theme--light text-h3'></i> Выберите фото",
      dictMaxFilesExceeded: 'Вы не можете загружать более 8 файлов',
      dictRemoveFile: 'Удалить',
      uploadMultiple: false,
      removeType: 'server',
      destroyDropzone: false,
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
        text: 'Салон',
        align: 'center',
        sortable: false,
        value: 'showroom.name',
        width: '30px',
      },
      {
        text: 'Марка',
        align: 'center',
        value: 'mark.name',
        width: '100px',
      },
      { text: 'Модель', value: 'model.name', width: '110px', align: 'center' },
      {
        text: 'Год',
        value: 'year',
        sortable: false,
        width: '50px',
        align: 'center',
      },
      {
        text: 'Пробег',
        value: 'milage',
        sortable: false,
        width: '20px',
        align: 'center',
      },
      {
        text: 'Привод',
        value: 'wheeltype.name',
        sortable: false,
        width: '25px',
        align: 'center',
      },
      {
        text: 'КПП',
        value: 'transmission.name',
        width: '25px',
        sortable: false,
        align: 'center',
      },
      {
        text: 'Объем',
        value: 'volume',
        sortable: false,
        width: '70px',
        align: 'center',
      },

      {
        text: 'Двиг.',
        value: 'enginetype.name',
        sortable: false,
        width: '20px',
        align: 'center',
      },
      {
        text: 'Цвет',
        value: 'color',
        align: 'center',
        sortable: false,
        width: '100px',
      },
      {
        text: 'Продажа',
        value: 'price',
        align: 'center',
        sortable: false,
        width: '125px',
      },
    ],
    editedIndex: -1,
    editedItem: {
      id: '',
      showroom_id: null,
      income_date: '',
      mark_id: '',
      model_id: '',
      year: '',
      auto_ru: '',
      bodytype_id: '',
      transmission_id: '',
      enginetype_id: '',
      salon: '',
      wheeltype_id: '',
      owner_count: 1,
      power: '',
      color: '',
      milage: '',
      volume: '',
      vin_number: '',
      number: '',
      sts: '',
      ptc: 1,
      income_price: '',
      price: 0,
      key_number: '',
      manager: '',
      status_id: '',
      registered: '',
      transit: '',
      pictures: [],
      expose: 0,
      comment: null,
      deleted_at: null,
    },
    defaultItem: {
      id: '',
      showroom_id: null,
      income_date: '',
      mark_id: '',
      model_id: '',
      year: '',
      auto_ru: '',
      bodytype_id: '',
      transmission_id: '',
      enginetype_id: '',
      salon: '',
      wheeltype_id: '',
      owner_count: 1,
      power: '',
      color: '',
      milage: '',
      volume: '',
      vin_number: '',
      number: '',
      sts: '',
      ptc: 1,
      income_price: '',
      price: 0,
      key_number: '',
      manager: '',
      status_id: '',
      registered: '',
      transit: '',
      pictures: [],
      expose: 0,
      comment: null,
      deleted_at: null,
    },
    options: {
      sortBy: ['mark.name', 'model.name'],
      multiSort: true,
    },
    pictures: [],
  }),

  computed: {
    dateFormatted() {
      return this.editedItem.income_date
        ? this.moment(this.editedItem.income_date).format('DD.MM.Y')
        : null
    },
    formTitle() {
      return this.editedIndex === -1 ? 'Новая запись' : 'Изменить'
    },
    usedcars() {
      return this.$store.state.usedcar.cars
    },
    filtered() {
      let cars = this.usedcars
      if (this.deleted) {
        cars = cars.filter((item) => item.deleted_at !== null)
      } else {
        cars = cars.filter((item) => item.deleted_at === null)
      }
      if (this.filter.mark_id) {
        cars = cars.filter(
          (item) =>
            item.mark_id === this.filter.mark_id && item.deleted_at === null
        )
      }
      if (this.filter.model_id) {
        cars = cars.filter(
          (item) =>
            item.model_id === this.filter.model_id && item.deleted_at === null
        )
      }
      if (this.filter.transmission_id) {
        cars = cars.filter(
          (item) =>
            item.transmission_id === this.filter.transmission_id &&
            item.deleted_at === null
        )
      }
      if (this.filter.wheeltype_id) {
        cars = cars.filter(
          (item) =>
            item.wheeltype_id === this.filter.wheeltype_id &&
            item.deleted_at === null
        )
      }
      if (this.filter.year_from && this.filter.year_to) {
        cars = cars.filter(
          (item) =>
            item.year >= this.filter.year_from &&
            item.year <= this.filter.year_to
        )
      } else if (this.filter.year_from) {
        cars = cars.filter((item) => item.year >= this.filter.year_from)
      } else if (this.filter.year_to) {
        cars = cars.filter((item) => item.year <= this.filter.year_to)
      }
      if (this.filter.milage_from && this.filter.milage_to) {
        cars = cars.filter(
          (item) =>
            item.milage >= this.filter.milage_from &&
            item.milage <= this.filter.milage_to
        )
      } else if (this.filter.milage_from) {
        cars = cars.filter((item) => item.milage >= this.filter.milage_from)
      } else if (this.filter.milage_to) {
        cars = cars.filter((item) => item.milage <= this.filter.milage_to)
      }

      if (this.filter.incomePriceFrom && this.filter.incomePriceTo) {
        cars = cars.filter(
          (item) =>
            item.income_price >= this.filter.incomePriceFrom &&
            item.income_price <= this.filter.incomePriceTo
        )
      } else if (this.filter.incomePriceFrom) {
        cars = cars.filter(
          (item) => item.income_price >= this.filter.incomePriceFrom
        )
      } else if (this.filter.incomePriceTo) {
        cars = cars.filter(
          (item) => item.income_price <= this.filter.incomePriceTo
        )
      }
      return cars
    },
    statuses() {
      return this.$store.state.usedcar.statuses
    },
    marks() {
      return this.$store.state.car.marks
    },
    models() {
      return this.$store.state.car.models
    },
    enginetypes() {
      return this.$store.state.car.enginetypes
    },
    bodytypes() {
      return this.$store.state.car.bodytypes
    },
    transmissions() {
      return this.$store.state.car.transmissions
    },
    wheeltypes() {
      return this.$store.state.car.wheeltypes
    },

    role() {
      return this.$store.state.auth.role
    },
    links() {
      return [
        {
          text: this.role.title,
          disabled: false,
          href: '/' + this.role?.name,
        },
        {
          text: 'Автомобили БУ',
          disabled: false,
          href: '/' + this.role?.name + '/used-cars',
        },
        {
          text: 'Загрузка фото',
          disabled: true,
          href: '/' + this.role?.name + '/admin/used-cars/edit',
        },
      ]
    },
    coefficient: {
      get() {
        return this.$store.state.usedcar.price_coefficient
      },
      set(value) {
        this.$store.commit('usedcar/SET_COEFFICIENT', value)
      },
    },
  },
  watch: {
    dialog(val) {
      val || this.close()
    },
    deleted(val) {
      val === false || this.clearFilter()
    },
  },
  async fetch({ store }) {
    await store.dispatch('car/fetchMarks')
    await store.dispatch('car/fetchBodyTypes')
    await store.dispatch('car/fetchEngineTypes')
    await store.dispatch('car/fetchTransmissions')
    await store.dispatch('car/fetchWheelTypes')
    await store.dispatch('usedcar/fetchCars', {})
    await store.dispatch('usedcar/fetchCoefficient')
    await store.dispatch('usedcar/fetchStatuses')
    await store.dispatch('user/toggle', false)
  },
  mounted() {
    this.$echo.channel('used-cars').listen('UsedCarAdded', (e) => {
      console.log('usedcar added')
      this.$store.dispatch('usedcar/fetchCars', {}).catch((error) => {
        console.log(error)
        this.reload()
      })
    })
  },
  methods: {
    canDownload() {
      const users = [
        'admin',
        'director',
        'coordinator',
        'coordinator2',
        'manager_acp',
        'petr',
        'manager_light',
      ]
      return users.includes(this.role?.name)
    },
    parseDate(date) {
      if (!date) {
        return null
      }
      return this.moment(date).format('YY-MM-dd')
    },
    isNumber(evt) {
      evt = evt || window.event
      const charCode = evt.which ? evt.which : evt.keyCode
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        evt.preventDefault()
      } else {
        return true
      }
    },
    save() {
      if (this.valid) {
        this.editedItem.pictures = [...this.pictures]
        this.$store.dispatch('usedcar/saveStorage', { item: this.editedItem })
        this.showSnack('success', 'Данные изменены')
        this.close()
      } else {
        this.showSnack('error', 'Заполните обязательные поля')
      }
    },
    saveCoefficient() {
      // console.log(this.coefficient)
      this.$store.dispatch('usedcar/saveCoefficient', {
        coefficient: this.coefficient,
      })
      this.showSnack('success', 'Данные изменены')
    },
    async editItem(item) {
      this.editedIndex = this.usedcars.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
      if (item.mark_id !== 0) {
        await this.$store.dispatch('car/fetchModels', { markId: item.mark_id })
      }
      if (this.editedItem.pictures.length > 0) {
        for (let i = 0; i < this.editedItem.pictures.length; i++) {
          const file = { size: 123, name: this.editedItem.pictures[i] }
          // console.log(this.editedItem.pictures[i])
          const url = '/images/usedcar/' + this.editedItem.pictures[i]
          this.pictures.push(this.editedItem.pictures[i])
          this.$refs.myVueDropzone.manuallyAddFile(file, url)
        }
      }
    },
    deleteItem(id) {
      confirm('Вы уверены, что хотите удалить этот запись?') &&
        this.$store
          .dispatch('usedcar/delete', { id })
          .then(() => {
            this.showSnack('success', 'Данные удалены')
          })
          .catch((error) => {
            this.showSnack('error', 'Ошибка: ' + error)
          })
      this.dialog = false
    },
    transitItem(item) {
      confirm('Вы уверены, что хотите переводить этот авто в другой салон?') &&
        this.$store
          .dispatch('usedcar/approveTransit', { item })
          .then(() => {
            this.showSnack('success', 'Автомобиль переведён')
          })
          .catch((error) => {
            this.showSnack('error', 'Ошибка: ' + error)
          })
    },
    close() {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
        this.$store.dispatch('car/emptyModels')
        this.clicks = 0
        this.componentKey += 1
        this.pictures = []
      })
    },
    closeMenu() {
      this.menu = false
      if (
        this.$store.state.usedcar.price_coefficient !==
        this.$store.state.usedcar.old_coefficient
      ) {
        this.$store.commit(
          'usedcar/SET_COEFFICIENT',
          this.$store.state.usedcar.old_coefficient
        )
      }
    },
    clearFilter() {
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
    showSnack(type, text) {
      this.text = text
      this.color = type
      this.snackbar = true
    },
    validate() {
      this.$refs.form.validate()
    },
    range(start, end, step = 1) {
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
    row_classes(item) {
      if (item.status_id === 5) {
        return 'primary white--text'
      } else if (item.transit === 1 || item.transit === 2) {
        return 'orange lighten-2' // can also return multiple classes e.g ["orange","disabled"]
      } else if (item.deleted_at !== null) {
        return 'black lighten-4 white--text'
      } else if (item.status_id === 1) {
        return 'success white--text'
      } else if (item.status_id === 2) {
        return 'red white--text'
      } else if (item.status_id === 3) {
        return 'yellow'
      } else if (item.status_id === 4) {
        return 'warning white--text'
      } else if (item.status_id === 7) {
        return 'black white--text'
      }
    },
    getModels(markId = null) {
      this.editedItem.model_id = []
      if (markId !== 0) {
        this.$store.dispatch('car/fetchModels', { markId })
      }
    },
    oldStyle(date) {
      return date !== null &&
        this.moment().diff(this.moment(date), 'months') > 3
        ? 'background-color: red; color: white;'
        : ''
    },
    reload() {
      this.intervalid1 = setInterval(() => {
        console.log('reload')
        this.$store.dispatch('usedcar/fetchUsedCars')
      }, 15000)
    },
    vFileAdded(file, response) {
      file.upload.filename = response.file
      this.pictures.push(response.file)
    },
    async vFileRemoved(file) {
      console.log(file)
      const filename =
        file.manuallyAdded === true ? file.name : file.upload.filename
      const index = this.pictures.indexOf(filename)
      // console.log(filename)
      // console.log(index)
      if (index > -1) {
        this.pictures.splice(index, 1)
      }
      await axios
        .post('/usedcar/deletePhoto/', filename)
        .then((response) => {
          this.showSnack('success', 'Фото удалён')
        })
        .catch((error) => {
          this.showSnack('error', error)
        })
    },
    redirect(id) {
      this.$router.push('/' + this.role?.name + '/used-cars/upload/' + id)
    },
  },
  beforeRouteLeave(to, from, next) {
    this.$store.dispatch('user/toggle', true)
    next()
  },
}
</script>

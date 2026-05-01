<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links" />

      <v-row no-gutters align="start" class="d-flex">
        <v-col cols="12">
          <v-data-table
            :headers="headers"
            :items="filtered"
            :items-per-page="filtered.length"
            :options="options"
            item-key="id"
            hide-default-footer
            class="elevation-1 grey lighten-4"
            height="80vh"
            fixed-header
            :mobile-breakpoint="0"
          >
            <template #body="{ items }">
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
                      {{ item.showroom.name }}
                    </v-chip>
                    <v-chip v-else color="green" text-color="white" x-small>
                      {{ item.showroom && item.showroom.name }}
                    </v-chip>
                  </td>
                  <td>{{ item.mark && item.mark.name }}</td>
                  <td>{{ item.model && item.model.name }}</td>
                  <td>{{ item.complectation }}</td>
                  <td>{{ item.year }}</td>
                  <td>{{ item.volume }}</td>
                  <td>{{ item.power }}</td>
                  <td>{{ item.wheeltype && item.wheeltype.name }}</td>
                  <td>{{ item.transmission && item.transmission.name }}</td>
                  <td>{{ item.color }}</td>
                  <td>{{ item.vin_number }}</td>
                  <td>{{ item.body_number }}</td>
                  <td>{{ item.price }}</td>
                  <td>
                    <span v-if="item.ptc_type === 1" key="pts">ПТС</span>
                    <span v-else-if="item.ptc_type === 2" key="epts">ЭПТС</span>
                    <span v-else-if="item.ptc_type === 3" key="epts"
                      >КОПИЯ</span
                    >
                    <span v-else key="no">НЕТ</span>
                  </td>
                  <td>{{ item.key_number }}</td>
                  <td>
                    <v-chip
                      v-if="item.is_sold === 1 || item.is_sold === true"
                      color="green"
                      text-color="white"
                      x-small
                    >
                      Продана
                    </v-chip>
                    <v-chip
                      v-else-if="item.status_id === 1"
                      color="green"
                      text-color="white"
                      x-small
                    >
                      Продана
                    </v-chip>
                    <v-chip
                      v-else-if="item.status_id === 2"
                      color="green"
                      text-color="white"
                      x-small
                    >
                      В продаже
                    </v-chip>
                  </td>
                </tr>
              </tbody>
            </template>
            <template #top>
              <v-toolbar flat dense>
                <v-row dense class="mt-12 hidden-sm-and-down">
                  <v-col cols="4" md="9" xs="6">
                    <v-row dense>
                      <v-col cols="3" md="3" xs="6">
                        <v-select
                          v-model="filter.showroom_id"
                          :items="showrooms"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Салон"
                          outlined
                          clearable
                          multiple
                          persistent-hint
                          @click:clear="
                            $nextTick(() => (filter.showroom_id = []))
                          "
                        >
                          <template #selection="{ item, index }">
                            <template v-if="index === 0">
                              <span>{{ item && item.name }}</span>
                            </template>
                            <span
                              v-if="index === 1"
                              class="grey--text text-caption"
                            >
                              &nbsp;(+{{ filter.showroom_id.length - 1 }})
                            </span>
                          </template>
                        </v-select>
                      </v-col>
                      <v-col cols="3" md="3" xs="6">
                        <v-select
                          v-model="filter.mark_id"
                          :items="marks"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Марка"
                          outlined
                          clearable
                          @change="
                            getModels(filter.mark_id)
                            deleted = false
                          "
                          @click="filter.model_id = null"
                          @click:clear="
                            $nextTick(() => (filter.mark_id = null))
                          "
                        />
                      </v-col>
                      <v-col cols="3" md="2" xs="6">
                        <v-select
                          v-model="filter.model_id"
                          :items="models"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Модель"
                          outlined
                          clearable
                          @click="deleted = false"
                          @click:clear="
                            $nextTick(() => (filter.model_id = null))
                          "
                        >
                          <template #no-data>
                            <span class="small">Выберите марку</span>
                          </template>
                        </v-select>
                      </v-col>
                      <v-col cols="3" md="2" xs="6">
                        <v-select
                          v-model="filter.transmission_id"
                          :items="transmissions"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Коробка"
                          outlined
                          clearable
                          @click="deleted = false"
                          @click:clear="
                            $nextTick(() => (filter.transmission_id = null))
                          "
                        />
                        6
                      </v-col>
                      <v-col cols="3" md="2" xs="6">
                        <v-select
                          v-model="filter.wheeltype_id"
                          :items="wheeltypes"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Привод"
                          outlined
                          clearable
                          @click="deleted = false"
                          @click:clear="
                            $nextTick(() => (filter.wheeltype_id = null))
                          "
                        />
                      </v-col>
                    </v-row>
                  </v-col>
                  <v-col cols="4" md="3" xs="6">
                    <v-row dense>
                      <v-col cols="3" md="4" xs="6">
                        <v-select
                          v-model="filter.year_from"
                          :items="range(2022, 1960)"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Год от"
                          outlined
                          clearable
                          @click="deleted = false"
                          @click:clear="
                            $nextTick(() => (filter.year_from = null))
                          "
                        />
                      </v-col>
                      <v-col cols="3" md="4" xs="6">
                        <v-select
                          v-model="filter.year_to"
                          :items="range(2022, 1960)"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Год до"
                          outlined
                          clearable
                          @click="deleted = false"
                          @click:clear="
                            $nextTick(() => (filter.year_to = null))
                          "
                        />
                      </v-col>
                      <v-col cols="3" md="4" xs="6">
                        <v-btn color="error" dark @click="clearFilter()">
                          Сброс
                        </v-btn>
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>
                <v-dialog v-model="dialog" max-width="900px">
                  <v-card>
                    <v-card-title>
                      <span class="headline">{{ formTitle }}</span>
                    </v-card-title>
                    <v-card-text>
                      <v-container>
                        <v-form ref="form" v-model="valid">
                          <v-row>
                            <v-col cols="12" sm="4" md="4">
                              <v-autocomplete
                                v-model="editedItem.mark_id"
                                :items="marks"
                                :value="marks[editedItem.mark_id]"
                                item-text="name"
                                no-data-text="Нету данных"
                                item-value="id"
                                menu-props="auto"
                                label="Марка"
                                hide-details
                                :rules="[(v) => !!v || 'Выберите марку']"
                                required
                                @change="getModels(editedItem.mark_id)"
                              />
                            </v-col>
                            <v-col cols="12" sm="4" md="4">
                              <v-autocomplete
                                v-model="editedItem.model_id"
                                :items="models"
                                :value="models[editedItem.model_id]"
                                item-text="name"
                                no-data-text="Нету данных"
                                item-value="id"
                                menu-props="auto"
                                label="Модель"
                                hide-details
                                :rules="[(v) => !!v || 'Выберите модель']"
                                required
                              />
                            </v-col>
                            <v-col cols="12" sm="4" md="4">
                              <v-select
                                v-model="editedItem.showroom_id"
                                :items="[
                                  { id: 2, name: 'Комфорт' },
                                  { id: 4, name: 'АвтоПремиум' },
                                  { id: 5, name: 'Авангард Юг' },
                                  { id: 7, name: 'Форсаж' },
                                  { id: 8, name: 'АвтоПремьер' },
                                ]"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                                label="Шоурум"
                                :rules="[(v) => !!v || 'Выберите шоурум']"
                                required
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="4">
                              <v-text-field
                                v-model="editedItem.complectation"
                                label="Комплектация"
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="4">
                              <v-select
                                v-model="editedItem.year"
                                :items="range(2022, 2018)"
                                menu-props="auto"
                                label="Год"
                                :rules="[(v) => !!v || 'Выберите год выпуска']"
                                hide-details
                                required
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="4">
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
                            <v-col cols="12" sm="6" md="4">
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
                            <v-col cols="12" sm="6" md="4">
                              <v-select
                                v-model="editedItem.volume"
                                :items="range(0.8, 8, 0.1)"
                                menu-props="auto"
                                item-value=""
                                label="Объем"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="4">
                              <v-text-field
                                v-model="editedItem.power"
                                type="number"
                                label="Л.С."
                              />
                            </v-col>
                            <v-col cols="12" sm="4" md="4">
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

                            <v-col cols="12" sm="4" md="4">
                              <v-text-field
                                v-model="editedItem.color"
                                label="Цвет"
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="4">
                              <v-text-field
                                v-model="editedItem.vin_number"
                                label="Вин"
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-text-field
                                v-model="editedItem.body_number"
                                label="№ кузова"
                              />
                            </v-col>

                            <v-col cols="12" sm="6" md="3">
                              <v-text-field
                                v-model="editedItem.price"
                                type="number"
                                label="Цена"
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-select
                                v-model="editedItem.ptc_type"
                                :items="[
                                  { id: 1, name: 'ПТС' },
                                  { id: 2, name: 'ЭПТС' },
                                  { id: 3, name: 'КОПИЯ' },
                                  { id: 0, name: 'Нет' },
                                ]"
                                label="ПТС"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-text-field
                                v-model="editedItem.key_number"
                                label="№ ключа"
                              />
                            </v-col>
                            <v-col cols="12" md="3" sm="6">
                              <v-select
                                v-model="editedItem.status_id"
                                :items="[
                                  { id: null, name: '' },
                                  { id: 1, name: 'Продана' },
                                  { id: 2, name: 'В продаже' },
                                ]"
                                hide-details
                                item-text="name"
                                item-value="id"
                                label="Статус"
                                menu-props="auto"
                              />
                            </v-col>
                            <v-col cols="12" sm="12" md="9">
                              <v-text-field
                                v-model="editedItem.comment"
                                label="Коментарии"
                              />
                            </v-col>
                          </v-row>
                        </v-form>
                      </v-container>
                    </v-card-text>

                    <v-card-actions>
                      <v-spacer />
                      <v-btn
                        color="error darken-1"
                        :disabled="
                          editedItem.id === '' ||
                          $checkAccess('delete', 'cars/all')
                        "
                        text
                        @click="deleteItem(editedItem.id)"
                      >
                        {{
                          editedItem.deleted_at === null
                            ? 'Удалить'
                            : 'Удалить из корзины'
                        }}
                      </v-btn>
                      <v-switch
                        v-if="editedItem.id !== ''"
                        v-model="editedItem.is_sold"
                        class="mr-2"
                        label="Продана"
                        color="success"
                      />
                      <v-btn color="blue darken-1" text @click="close">
                        Отмена
                      </v-btn>
                      <v-btn
                        v-if="editedItem.id === ''"
                        color="blue darken-1"
                        text
                        :disabled="$checkAccess('create', 'cars/all')"
                        @click="save()"
                      >
                        Сохранить
                      </v-btn>
                      <v-btn
                        v-else
                        color="blue darken-1"
                        text
                        :disabled="$checkAccess('update', 'cars/all')"
                        @click="save()"
                      >
                        Сохранить
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-dialog>
                <v-menu
                  v-model="menu"
                  :close-on-content-click="false"
                  :nudge-width="200"
                  offset-x
                >
                  <template #activator="{ on, attrs }">
                    <v-btn
                      color="indigo"
                      dark
                      v-bind="attrs"
                      class="mb-2 ml-1"
                      v-on="on"
                    >
                      <v-icon class="mr-2"> mdi-menu </v-icon>
                      Меню
                    </v-btn>
                  </template>

                  <v-card>
                    <v-list>
                      <v-list-item>
                        <v-list-item-action>
                          <v-btn
                            color="primary"
                            dark
                            class="mb-2"
                            @click="dialog = true"
                          >
                            Добавить
                          </v-btn>
                        </v-list-item-action>
                      </v-list-item>
                      <v-list-item>
                        <v-list-item-action>
                          <v-btn
                            color="error"
                            dark
                            class="mb-2"
                            @click="clearFilter()"
                          >
                            Сбросить
                          </v-btn>
                        </v-list-item-action>
                      </v-list-item>
                      <v-list-item>
                        <v-list-item-action>
                          <v-switch
                            v-model="deleted"
                            :disabled="$checkAccess('trash', 'cars/all')"
                            label="Корзина"
                            color="red"
                            class="mt-1"
                            hide-details
                          />
                        </v-list-item-action>
                      </v-list-item>
                    </v-list>
                    <v-card-actions>
                      <v-spacer />

                      <v-btn color="primary" text @click="menu = false">
                        Закрыть
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-menu>
              </v-toolbar>
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
  name: 'CarAll',
  components: { BreadCrumb },
  beforeRouteLeave(to, from, next) {
    this.$store.dispatch('user/toggle', true)
    next()
  },
  layout: 'user',
  middleware: 'permission',
  data: () => ({
    filter: {
      mark_id: null,
      model_id: null,
      transmission_id: null,
      wheeltype_id: null,
      year_from: null,
      showroom_id: [],
      year_to: null,
    },
    menu: null,
    deleted: false,
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
        value: 'showroom.name',
        width: '90px',
      },
      {
        text: 'Марка',
        align: 'center',
        value: 'mark.name',
        width: '150px',
      },
      { text: 'Модель', value: 'model.name', align: 'center', width: '150px' },
      {
        text: 'Комплектация',
        value: 'complectation',
        sortable: true,
        width: '150px',
        align: 'center',
      },
      {
        text: 'Выпуск',
        value: 'year',
        sortable: false,
        align: 'center',
        width: '25px',
      },
      {
        text: 'Объем',
        value: 'volume',
        sortable: false,
        align: 'center',
        width: '25px',
      },
      {
        text: 'Л.С.',
        value: 'power',
        sortable: false,
        align: 'center',
        width: '25px',
      },
      {
        text: 'Привод',
        value: 'wheeltype.name',
        width: '100px',
        align: 'center',
      },
      {
        text: 'КПП',
        value: 'transmission.name',
        sortable: false,
        align: 'center',
        width: '30px',
      },
      {
        text: 'Цвет',
        value: 'color',
        sortable: false,
        align: 'center',
        width: '30px',
      },
      {
        text: 'Vin',
        value: 'vin_number',
        sortable: false,
        align: 'center',
        width: '50px',
      },
      {
        text: '№ кузова',
        value: 'body_number',
        sortable: false,
        align: 'center',
        width: '50px',
      },
      {
        text: 'Цена',
        value: 'price',
        sortable: false,
        align: 'center',
        width: '20px',
      },
      {
        text: 'ПТС',
        value: 'ptc_type',
        sortable: true,
        width: '80px',
        align: 'center',
      },
      {
        text: '№ ключа',
        value: 'key_number',
        sortable: false,
        align: 'center',
        width: '100px',
      },
      {
        text: 'Статус',
        value: 'status_id',
        sortable: false,
        align: 'center',
        width: '100px',
      },
    ],
    editedIndex: -1,
    editedItem: {
      id: '',
      showroom_id: null,
      mark_id: '',
      model_id: '',
      year: '',
      transmission_id: '',
      complectaion: '',
      enginetype_id: '',
      wheeltype_id: '',
      power: '',
      color: '',
      volume: '3',
      vin_number: '',
      body_number: '',
      ptc_type: null,
      price: 0,
      key_number: '',
      transit: '',
      status_id: '',
      is_sold: '',
      comment: null,
    },
    defaultItem: {
      id: '',
      showroom_id: null,
      mark_id: '',
      model_id: '',
      year: '',
      transmission_id: '',
      complectaion: '',
      enginetype_id: '',
      wheeltype_id: '',
      power: '',
      color: '',
      volume: '3',
      vin_number: '',
      body_number: '',
      ptc_type: null,
      price: 0,
      key_number: '',
      transit: '',
      status_id: '',
      is_sold: '',
      comment: null,
    },
    options: {
      sortBy: ['mark.name', 'model.name'],
      multiSort: true,
    },
  }),

  async fetch({ store }) {
    await store.dispatch('car/fetchCars', {})
    await store.dispatch('property/fetchMarks')
    await store.dispatch('showroom/fetchShowrooms')
    await store.dispatch('property/fetchEngineTypes')
    await store.dispatch('property/fetchTransmissions')
    await store.dispatch('property/fetchWheelTypes')
    await store.dispatch('user/toggle', false)
  },

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? 'Новая запись' : 'Изменить'
    },
    cars() {
      return this.$store.state.car.cars
    },
    filtered() {
      let cars = this.$store.state.car.cars
      if (this.deleted) {
        cars = cars.filter((item) => item.deleted_at !== null)
      } else {
        cars = cars.filter((item) => item.deleted_at === null)
      }
      if (this.filter.mark_id) {
        cars = cars.filter((item) => item.mark_id === this.filter.mark_id)
      }
      if (this.filter.model_id) {
        cars = cars.filter((item) => item.model_id === this.filter.model_id)
      }
      if (this.filter.transmission_id) {
        cars = cars.filter(
          (item) => item.transmission_id === this.filter.transmission_id
        )
      }
      if (this.filter.wheeltype_id) {
        cars = cars.filter(
          (item) => item.wheeltype_id === this.filter.wheeltype_id
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

      if (this.filter.showroom_id.length > 0) {
        cars = cars.filter((item) =>
          this.filter.showroom_id.includes(item.showroom_id)
        )
      }
      return cars
    },
    marks() {
      return this.$store.state.property.marks
    },
    models() {
      return this.$store.state.property.models
    },
    enginetypes() {
      return this.$store.state.property.enginetypes
    },
    transmissions() {
      return this.$store.state.property.transmissions
    },
    showrooms() {
      return this.$store.state.showroom.showrooms
    },
    wheeltypes() {
      return this.$store.state.property.wheeltypes
    },
    role() {
      return this.$store.state.auth.role
    },
    links() {
      return [
        {
          text: 'Главная',
          disabled: false,
          href: '/',
        },
        {
          text: 'Новые Автомобили',
          disabled: false,
          href: '/cars',
        },
        {
          text: 'Все салоны',
          disabled: true,
          href: '/cars/all',
        },
      ]
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
  mounted() {
    this.$echo.channel('cars').listen('CarAdded', (e) => {
      console.log('car added')
      this.$store.dispatch('car/fetchCars', {}).catch((error) => {
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
        'manager_light',
      ]
      return users.includes(this.role && this.role.name)
    },
    canChange() {
      const users = [
        'admin',
        'custom',
        'director',
        'coordinator',
        'coordinator2',
        'manager_acp',
        'manager_light',
      ]
      return !users.includes(this.role.name)
    },
    save() {
      if (this.valid) {
        if (this.editedIndex > -1) {
          this.$store.dispatch('car/save', {
            item: this.editedItem,
            showroom_id: null,
          })
          this.$toast.success('Данные изменены')
        } else {
          this.$store.dispatch('car/save', {
            item: this.editedItem,
          })
          this.$toast.success('Данные добавлены')
        }
        this.close()
      } else {
        this.$toast.error('Заполните обязательные поля')
      }
    },

    editItem(item) {
      this.editedItem = Object.assign({}, item)
      this.dialog = true
      if (item.mark_id !== 0) {
        this.$store.dispatch('property/fetchModels', { markId: item.mark_id })
      }
    },
    deleteItem(id) {
      confirm('Вы уверены, что хотите удалить этот запись?') &&
        this.$store
          .dispatch('car/delete', {
            id,
          })
          .then(() => {
            this.$toast.success('Данные удалены')
          })
      this.deleteId = ''
      this.dialog = false
    },
    close() {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
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
      this.filter.showroom_id = []
      this.$store.dispatch('car/emptyModels')
    },
    row_classes(item) {
      if (item.transit === 1 || item.transit === 2) {
        return 'orange lighten-2' // can also return multiple classes e.g ["orange","disabled"]
      } else if (item.deleted_at !== null) {
        return 'black lighten-4 white--text'
      }
      if (item.is_sold === 1 || item.status_id === 1) {
        return 'error white--text' // can also return multiple classes e.g ["orange","disabled"]
      }
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
    getModels(markId = null) {
      this.editedItem.model_id = []
      if (markId !== null) {
        this.$store.dispatch('property/fetchModels', { markId })
      }
    },
    reload() {
      this.intervalid1 = setInterval(() => {
        console.log('reload')
        this.$store.dispatch('car/fetchCars')
      }, 15000)
    },
  },
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
  padding: 0 6px;
}
.v-data-table > .v-data-table__wrapper > table > tbody > tr > td {
  font-size: 0.875rem;
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
  padding: 0 4px;
  font-size: 0.875rem;
}
.v-data-table
  /deep/
  tbody
  /deep/
  tr:hover:not(.v-data-table__expanded__content) {
  background: yellow !important;
}
</style>

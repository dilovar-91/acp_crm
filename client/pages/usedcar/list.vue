<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links" />
      <v-row align="start" class="d-flex">
        <v-toolbar flat>
          <v-row class="mt-8 hidden-sm-and-down">
            <v-col cols="6" md="6" xs="12">
              <v-row class="mb-1">
                <v-col cols="6" md="2" xs="6">
                  <v-select
                    v-model="filter.mark_id"
                    :items="marks"
                    dense
                    item-text="name"
                    item-value="id"
                    label="Марка"
                    placeholder="Марка"
                    outlined
                    @change="getModels(filter.mark_id)"
                    @click="filter.model_id = null"
                    @click:clear="$nextTick(() => (filter.mark_id = null))"
                  />
                </v-col>
                <v-col cols="6" md="2" xs="6">
                  <v-select
                    v-model="filter.model_id"
                    :items="models"
                    dense
                    item-text="name"
                    item-value="id"
                    label="Модель"
                    placeholder="Модель"
                    outlined
                    @click="deleted = false"
                    @click:clear="$nextTick(() => (filter.model_id = null))"
                  >
                    <template v-slot:no-data>
                      <span class="small">Выберите марку</span>
                    </template>
                  </v-select>
                </v-col>
                <v-col cols="6" md="2" xs="6">
                  <v-select
                    v-model="filter.transmission_id"
                    :items="transmissions"
                    dense
                    item-text="name"
                    item-value="id"
                    label="Коробка"
                    placeholder="Коробка"
                    outlined
                    @click="deleted = false"
                    @click:clear="
                      $nextTick(() => (filter.transmission_id = null))
                    "
                  />
                </v-col>
                <v-col cols="6" md="2" xs="6">
                  <v-select
                    v-model="filter.wheeltype_id"
                    :items="wheeltypes"
                    dense
                    item-text="name"
                    item-value="id"
                    label="Привод"
                    placeholder="Привод"
                    outlined
                    @click="deleted = false"
                    @click:clear="$nextTick(() => (filter.wheeltype_id = null))"
                  />
                </v-col>
                <v-col cols="6" md="2" xs="6">
                  <v-text-field
                    v-model="filter.year_from"
                    name="year_from"
                    clearable
                    dense
                    label="Год от"
                    placeholder="Год от"
                    outlined
                    @keypress.native="isNumber($event)"
                  />
                </v-col>
                <v-col cols="6" md="2" xs="6">
                  <v-text-field
                    v-model="filter.year_to"
                    name="year_to"
                    clearable
                    dense
                    label="Год до"
                    placeholder="Год до"
                    outlined
                    @keypress.native="isNumber($event)"
                  />
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="6" md="6" xs="12">
              <v-row>
                <v-col cols="6" md="2" xs="6">
                  <v-text-field
                    v-model="filter.milage_from"
                    clearable
                    dense
                    label="Пробег от"
                    placeholder="Пробег от"
                    outlined
                    @keypress.native="isNumber($event)"
                  />
                </v-col>

                <v-col cols="6" md="2" xs="6">
                  <v-text-field
                    v-model="filter.milage_to"
                    clearable
                    dense
                    label="Пробег до"
                    placeholder="Пробег до"
                    outlined
                    @keypress.native="isNumber($event)"
                  />
                </v-col>
                <v-col cols="6" md="2" xs="6">
                  <v-text-field
                    v-model="filter.price_from"
                    clearable
                    dense
                    label="Цена от"
                    placeholder="Приход от"
                    outlined
                  />
                </v-col>
                <v-col cols="6" md="2" xs="6">
                  <v-text-field
                    v-model="filter.price_to"
                    clearable
                    dense
                    label="Цена до"
                    placeholder="Приход от"
                    outlined
                  />
                </v-col>
                <v-col cols="6" md="2" xs="6">
                  <v-btn
                    color="success"
                    dark
                    class="ml-2"
                    @click="changeFilter"
                  >
                    Применить
                  </v-btn>
                </v-col>
                <v-col cols="6" md="2" xs="6">
                  <v-btn color="error" dark class="ml-2" @click="clearFilter()">
                    Сбросить
                  </v-btn>
                </v-col>
              </v-row>
            </v-col>
          </v-row>
          <v-menu
            v-if="['admin', 'manager_acp', 'petr', 'manager_light']"
            v-model="menu"
            :close-on-content-click="false"
            :nudge-width="200"
            offset-x
          >
            <template v-slot:activator="{ on, attrs }">
              <v-btn color="indigo" dark v-bind="attrs" class="ml-2" v-on="on">
                <v-icon class="mr-2"> mdi-menu </v-icon> Меню
              </v-btn>
            </template>
            <v-card>
              <v-list>
                <v-list-item>
                  <v-list-item-action>
                    <v-row>
                      <v-col cols="8">
                        <v-text-field
                          v-model="coefficient"
                          outlined
                          dense
                          label="Коэффициент"
                          value="50"
                          prefix="+"
                        />
                      </v-col>
                      <v-col cols="4">
                        <v-btn color="primary" @click="saveCoefficient()">
                          Сохранить
                        </v-btn>
                      </v-col>
                    </v-row>
                  </v-list-item-action>
                </v-list-item>
              </v-list>
              <v-card-actions>
                <v-spacer />
                <v-btn color="primary" text @click="closeMenu()">
                  Закрыть
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-menu>
        </v-toolbar>
      </v-row>
      <v-row class="infinate">
        <template v-for="(item, i) in filtered">
          <v-col :key="i" cols="12" md="3" xl="3">
            <v-hover v-slot:default="{ hover }">
              <v-card elevation="24" class="mx-auto">
                <v-carousel
                  :continuous="false"
                  :cycle="true"
                  hide-delimiter-background
                  delimiter-icon="mdi-minus"
                  height="300"
                >
                  <template v-if="item.pictures && item.pictures.length > 0">
                    <v-carousel-item
                      v-for="(picture, i) in item.pictures"
                      :key="i"
                      :src="'/images/usedcar/' + picture"
                    />
                  </template>
                  <template v-else>
                    <v-carousel-item :src="'/images/car_no.jpg'" />
                  </template>
                </v-carousel>
                <v-list dense class="py-0">
                  <v-list-item-group color="indigo">
                    <v-list-item dense>
                      <v-list-item-content class="my-0">
                        <v-list-item-title
                          class="font-weight-bold success--text text-h6"
                        >
                          {{ item.mark && item.mark.name }}
                          {{ item.model && item.model.name }}
                        </v-list-item-title>
                      </v-list-item-content>
                      <v-list-item-action class="my-0">
                        <v-chip
                          v-if="item.price > 0"
                          color="red"
                          class="white--text"
                        >
                          {{ item.price + parseInt(coefficient) }}
                        </v-chip>
                        <v-chip v-else color="blue" class="white--text">
                          Цена не указана
                        </v-chip>
                      </v-list-item-action>
                    </v-list-item>
                  </v-list-item-group>
                </v-list>
                <v-row dense align="center">
                  <v-col cols="6" md="6" xs="6" class="pl-4">
                    <p class="text-secondary mb-1">
                      Выпуск: <b>{{ item.year }}</b>
                    </p>
                    <p class="text-secondary mb-1">
                      Пробег: <b>{{ item.milage }}</b>
                    </p>
                    <p class="text-secondary mb-1">
                      Привод: <b>{{ item.wheeltype && item.wheeltype.name }}</b>
                    </p>
                    <p class="text-secondary mb-0">
                      Кузов: <b>{{ item.bodytype && item.bodytype.name }}</b>
                    </p>
                  </v-col>
                  <v-col cols="6" md="6" xs="6">
                    <p class="text-secondary mb-1">
                      КПП:
                      <b>{{ item.transmission && item.transmission.name }}</b>
                    </p>
                    <p class="text-secondary mb-1">
                      Мощность: <b>{{ item.power }} Л.С.</b>
                    </p>
                    <p class="text-secondary mb-0">
                      Объем: <b>{{ item.volume }}</b>
                    </p>
                    <p class="text-secondary mb-1">
                      Склад: <b v-if="item.showroom_id === 1">З</b>
                      <b v-if="item.showroom_id === 2">Р</b>
                      <b v-if="item.showroom_id === 3">М</b>
                    </p>
                  </v-col>
                </v-row>
              </v-card>
            </v-hover>
          </v-col>
        </template>
        <v-col cols="12" class="mt-6">
          <infinite-loading
            ref="infiniteLoading"
            :identifier="infiniteId"
            spinner="bubbles"
            @infinite="infiniteHandler"
          >
            <div slot="no-more">
              <div class="sharing-area">
                <h2 class="legend text-center">Вы достигли конца списка</h2>
              </div>
            </div>
            <div slot="no-results" />
          </infinite-loading>
          <div v-if="filter.length <= 0" class="sharing-area">
            <div class="big-icon">
              <img
                height="200px"
                class="text--success"
                src="/images/filter.png"
              />
            </div>
            <h2 class="legend text-center">
              Нету результатов по выбранным критериям!
            </h2>
          </div>
        </v-col>

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
      </v-row>
    </v-container>
  </div>
</template>
<style scoped>
.v-card {
  transition: opacity 0.4s ease-in-out;
}

.v-card:not(.on-hover) {
  opacity: 5;
}

.show-btns {
  color: rgba(255, 255, 255, 1) !important;
}
.shadow {
  color: grey;
  /*border white with light shadow*/
  text-shadow: 2px 0 0 #000, -2px 0 0 #000, 0 2px 0 #000, 0 -2px 0 #000,
    1px 1px 0 #000, -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000,
    1px 1px 5px #000;
}
</style>
<script>
import { mapGetters } from 'vuex'
import BreadCrumb from '~/components/BreadCrumb'
export default {
  layout: 'user',
  middleware: 'permission',
  components: { BreadCrumb },
  data: () => ({
    menu1: null,
    componentKey: 0,
    page: 1,
    infiniteId: +new Date(),
    filter: {
      mark_id: null,
      model_id: null,
      transmission_id: null,
      wheeltype_id: null,
      year_from: null,
      year_to: null,
      milage_from: null,
      milage_to: null,
      price_from: null,
      price_to: null,
    },
    menu: null,
    dialog: false,
    snackbar: false,
    text: null,
    color: null,
    timeout: 6000,
    pictures: [],
  }),

  computed: {
    usedcars() {
      return this.$store.state.usedcar.cars
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
          text: this.role?.title,
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
    ...mapGetters({
      filtered: 'filter/filtered',
      current_page: 'filter/current_page',
      total: 'filter/total',
      per_page: 'filter/per_page',
      last_page: 'filter/last_page',
    }),
  },
  async asyncData({ params }) {
    const showroomId = await (parseInt(params.id) === 1 ||
    parseInt(params.id) === 2
      ? parseInt(params.id)
      : null)
    return { showroomId }
  },
  async fetch({ store, params }) {
    await store.dispatch('car/fetchMarks')
    await store.dispatch('car/fetchBodyTypes')
    await store.dispatch('car/fetchEngineTypes')
    await store.dispatch('car/fetchTransmissions')
    await store.dispatch('car/fetchWheelTypes')
    await store.dispatch('usedcar/fetchCars', {})
    await store.dispatch('usedcar/fetchCoefficient')
    await store.dispatch('usedcar/fetchStatuses')
    await store.dispatch('user/toggle', false)
    const showroomId = await (parseInt(params.id) === 1 ||
    parseInt(params.id) === 2
      ? parseInt(params.id)
      : null)
    await store.dispatch('filter/getFiltered', { page: 1, showroomId })
  },
  mounted() {
    this.$echo.channel('used-cars').listen('UsedCarAdded', (e) => {
      console.log('usedcar added')
      this.$store.dispatch('filter/getFiltered', {
        page: 1,
        showroomId: this.showroomId,
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

    saveCoefficient() {
      // console.log(this.coefficient)
      this.$store.dispatch('usedcar/saveCoefficient', {
        coefficient: this.coefficient,
      })
      this.showSnack('success', 'Данные изменены')
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

    getModels(markId = null) {
      if (markId !== 0) {
        this.$store.dispatch('car/fetchModels', { markId })
      }
    },

    redirect(id) {
      this.$router.push('/' + this.role?.name + '/used-cars/upload/' + id)
    },
    infiniteHandler($state) {
      if (this.page < this.last_page) {
        setTimeout(() => {
          this.page += 1
          this.$store
            .dispatch('filter/getFiltered', {
              page: this.page,
              showroomId: this.showroomId,
              filter: this.filter,
              load: true,
            })
            .then((res) => {})
          $state.loaded()
          // this.$refs.infiniteLoading.$emit('$InfiniteLoading:loaded')
        }, 1500)
      } else {
        $state.complete()
      }

      console.log('loaded inflate')
    },
    changeFilter() {
      this.$store.dispatch('filter/emptyFiltered')
      this.page = 1
      this.infiniteId++
      this.$store
        .dispatch('filter/getFiltered', {
          page: this.page,
          showroomId: this.showroomId,
          filter: this.filter,
          load: true,
        })
        .then((res) => {})
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
      this.filter.price_from = null
      this.filter.price_to = null
      this.$store.dispatch('car/emptyModels')
      this.$store.dispatch('filter/getFiltered', {
        page: 1,
        showroomId: this.showroomId,
      })
    },
  },
  beforeRouteLeave(to, from, next) {
    this.$store.dispatch('user/toggle', true)
    next()
  },
}
</script>

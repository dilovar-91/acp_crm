<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links" />
      <v-row align="start" class="d-flex" no-gutters>
        <v-col cols="12">
          <v-data-table
            :headers="headers"
            :items="filtered"
            :items-per-page="filtered.length"

            class="elevation-1  grey lighten-4"
            fixed-header
            height="80vh"
            hide-default-footer
            item-key="id"
          >
            <template #header.income_date="{ header }">
              <span class="vertical">Дата <br> прихода</span>
            </template>
            <template #header.created_at="{ header }">
              <span class="vertical">Дата <br> транзита</span>
            </template>
            <template #header.owner_count="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.sender.name="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.receiver.name="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.volume="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.milage="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.showroom.name="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.bodytype.name="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.year="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.wheeltype.name="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.auto_ru="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.transmission.name="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.ptc="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.income_price="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.price="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.enginetype.name="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.salon="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.color="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.vin_number="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.number="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.sts="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.key_number="{ header }">
              <span class="vertical">Номер <br> ключа</span>
            </template>
            <template #header.status.status="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #body="{ items }">
              <tbody>
                <tr
v-for="item in items"
                    :key="item.id"
                >
                  <td>
                    <template v-if="item.created_at !== null">
                      {{ $moment(new Date(item.created_at)).format('DD.MM.Y') }}
                    </template>
                  </td>
                  <td>{{ item.sender && item.sender.name }} <br> <template v-if="item.sender">({{ item.sender.role && item.sender.role.title }})</template></td>
                  <td>{{ item.receiver && item.receiver.name }} <br> <template v-if="item.receiver">({{ item.receiver && item.receiver.role && item.receiver.role.title }})</template></td>
                  <td>
                    <template v-if="item.usedcar && item.usedcar.income_date">
                      {{ $moment(new Date(item.usedcar.income_date)).format('DD.MM.Y') }}
                    </template>
                  </td>
                  <td>
                    <span v-if="item.usedcar && item.usedcar.auto_ru"><a :href="item.usedcar.auto_ru" class="link" target="_blank">
                      Перейти
                    </a></span>
                  </td>
                  <td>
                    <v-chip
                      v-if="item.from_showroom"
                      color="error"
                      x-small
                    >
                      {{ item.showroom.name }}
                    </v-chip>
                  </td>
                  <td>
                    <v-chip
                      v-if="item.showroom.id % 2 !== 1"
                      color="primary"
                      x-small
                    >
                      {{ item.showroom.name }}
                    </v-chip>
                    <v-chip v-else color="green" text-color="white" x-small>
                      {{
                        item.showroom.name
                      }}
                    </v-chip>
                  </td>
                  <td>{{ item.usedcar.mark && item.usedcar.mark.name }}</td>
                  <td>{{ item.usedcar.model && item.usedcar.model.name }}</td>
                  <td>{{ item.usedcar.year }}</td>
                  <td>{{ item.usedcar.bodytype && item.usedcar.bodytype.name }}</td>
                  <td>{{ item.usedcar.owner_count }}</td>
                  <td>{{ item.usedcar.milage }}</td>
                  <td>{{ item.usedcar.wheeltype && item.usedcar.wheeltype.name }}</td>
                  <td>{{ item.usedcar.transmission && item.usedcar.transmission.name }}</td>
                  <td>{{ item.usedcar.volume }} ({{ item.usedcar.power }} ЛС)</td>
                  <td>{{ item.usedcar.enginetype && item.usedcar.enginetype.name }}</td>
                  <td>{{ item.usedcar.salon }}</td>
                  <td>{{ item.usedcar.color }}</td>
                  <td>{{ item.usedcar.vin_number }}</td>
                  <td>{{ item.usedcar.number }}</td>
                  <td>{{ item.usedcar.sts }}</td>
                  <td>{{ item.usedcar.income_price }}</td>
                  <td>{{ item.usedcar.price }}</td>

                  <td>
                    <span v-if="item.usedcar.ptc === 1">Есть</span>
                    <span v-else-if="item.usedcar.ptc === 2">Дубликат</span>
                    <span v-else>Нет</span>
                  </td>
                  <td>
                    <span v-if="item.usedcar.registered === 1" key="da">Да</span>
                    <span v-else key="net">Нет</span>
                  </td>
                  <td>{{ item.usedcar.key_number }}</td>
                  <td>
                    <v-chip
                      v-if="item.usedcar.status && item.usedcar.status.status !== null"
                      :color="item.usedcar.status.color"
                      :text-color="item.usedcar.status.text_color"
                      x-small
                    >
                      {{ item.usedcar.status.status }}
                    </v-chip>
                  </td>
                </tr>
              </tbody>
            </template>
            <template #top>
              <v-toolbar dense>
                <v-toolbar-title>История транзитов</v-toolbar-title>
                <v-spacer />
                <v-col cols="3" md="2" xs="6" class="mt-4">
                  <v-select
                    v-model="filter.mark_id"
                    :items="marks"
                    dense
                    item-text="name"
                    item-value="id"
                    label="Марка"
                    class="mt-4"
                    outlined
                    clearable
                    @click:clear="
                            $nextTick(() => (filter.mark_id = null))
                          "
                  />
                </v-col>
                <v-col cols="3" md="2" xs="6" class="mt-4">
                  <v-select
                    v-model="filter.showroom_id"
                    :items="showrooms"
                    dense
                    item-text="name"
                    item-value="id"
                    label="Салон"
                    class="mt-4"
                    outlined
                    clearable
                  >
                    <template #no-data>
                      <span class="small">Выберите салона</span>
                    </template>
                  </v-select>
                </v-col>
                <v-col cols="3" md="2" xs="6">
                  <date-picker
                    v-model="filter.date_from"
                    format="DD.MM.Y"
                    placeholder="Дата до"
                    style="width: 100%; margin-top: 2px;"
                    @clear="filter.date_from = null"
                  />
                </v-col>
                <v-col cols="3" md="2" xs="6">
                  <date-picker
                    v-model="filter.date_to"
                    format="DD.MM.Y"
                    placeholder="Дата до"
                    style="width: 100%; margin-top: 2px;"
                    @clear="filter.date_to = null"
                  />
                </v-col>

                <v-btn dark color="success" class="mr-4" @click="apply_filter()">
                  Применить
                </v-btn>

                <v-btn dark color="error" @click="clear()">
                  Сбросить
                </v-btn>
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
  components: { BreadCrumb },
  beforeRouteLeave (to, from, next) {
    this.$store.dispatch('user/toggle', true)
    next()
  },
  layout: 'user',
  middleware: 'permission',
  data: () => ({
    menu1: null,
    menu: null,
    filter: {
      mark_id: null,
      date_from: null,
      date_to: null,
      showroom_id: null,
    },
    expanded: [],
    singleExpand: false,
    headers: [
      {
        text: 'Дата транзита',
        value: 'created_at',
        align: 'center',
        sortable: false,
        width: '40px'
      },
      {
        text: 'Отправил',
        value: 'sender.name',
        align: 'center',
        sortable: false,
        width: '30px'
      },
      {
        text: 'Получил',
        value: 'receiver.name',
        align: 'center',
        sortable: false,
        width: '30px'
      },
      {
        text: 'Дата прихода',
        value: 'income_date',
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
        text: 'Откуда',
        align: 'center',
        sortable: false,
        value: 'from_showroom.name',
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
        text: 'Марка',
        align: 'start',
        value: 'mark.name',
        width: '100px',
        sortable: false
      },
      {
        text: 'Модель',
        value: 'model.name',
        width: '110px',
        align: 'center',
        sortable: false
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
        text: 'Гос.ном.',
        value: 'number',
        sortable: false,
        width: '60px',
        align: 'center'
      },
      {
        text: 'СТС',
        value: 'sts',
        sortable: false,
        width: '90px',
        align: 'center'
      },
      {
        text: 'Приход',
        value: 'income_price',
        sortable: false,
        width: '30px',
        align: 'center'
      },
      {
        text: 'Продажа',
        value: 'price',
        align: 'center',
        sortable: false,
        width: '20px'
      },
      {
        text: 'ПТС',
        value: 'ptc',
        sortable: false,
        align: 'center',
        width: '30px'
      },
      {
        text: 'На учете',
        value: 'registered',
        width: '10px',
        align: 'center',
        sortable: false
      },
      {
        text: '№-ключа',
        value: 'key_number',
        width: '30px',
        align: 'center',
        sortable: false
      },
      {
        text: 'Статус',
        value: 'status.status',
        align: 'center',
        sortable: false,
        width: '100px'
      }
    ]
  }),

  async fetch ({ store, error, params: { id } }) {
    await store.dispatch('transit/fetchUsedTransits', {})
    await store.dispatch('car/fetchMarks')
    await store.dispatch('car/fetchBodyTypes')
    await store.dispatch('car/fetchEngineTypes')
    await store.dispatch('car/fetchTransmissions')
    await store.dispatch('car/fetchWheelTypes')
    await store.dispatch('showroom/fetchShowrooms')
    await store.dispatch('usedcar/fetchStatuses')
    await store.dispatch('user/toggle', false)
  },

  computed: {
    showroom () {
      return this.$store.state.showroom.showroom
    },
    showrooms () {
      return this.$store.state.showroom.showrooms
    },
    transits () {
      return this.$store.state.transit.used_transits
    },
    filtered () {
      return this.transits.filter( l => l.usedcar !== null)
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
    bodytypes () {
      return this.$store.state.car.bodytypes
    },
    transmissions () {
      return this.$store.state.car.transmissions
    },
    wheeltypes () {
      return this.$store.state.car.wheeltypes
    },
    statuses () {
      return this.$store.state.usedcar.statuses
    },
    links () {
      return [
        {
          text: 'Главная',
          disabled: false,
          href: '/'
        },
        {
          text: 'Автомобили БУ',
          disabled: false,
          href: '/used-cars'
        },
        {
          text: 'История транзитов',
          disabled: true,
          href: '/'
        }
      ]
    }
  },
  methods: {
    row_classes (item) {
      if (item.transit === 1 || item.transit === 2) {
        return 'orange lighten-2' // can also return multiple classes e.g ["orange","disabled"]
      } else if (item.deleted_at !== null) {
        return 'black lighten-4 white--text'
      }
    },
    async apply_filter() {
      if (this.filter.date_from !== null && this.filter.date_to !== null) {
        await this.$store.dispatch('transit/fetchUsedTransits', {
          mark_id: this.filter.mark_id,
          created_between: this.$moment(this.filter.date_from).format('YYYY-MM-DD') + ',' + this.$moment(this.filter.date_to).format('YYYY-MM-DD'),
          showroom_id: this.filter.showroom_id
        })
      } else {
        await this.$store.dispatch('transit/fetchUsedTransits', {
          mark_id: this.filter.mark_id,
          showroom_id: this.filter.showroom_id
        })
      }

    },
    async clear() {
      await this.$store.dispatch('transit/fetchUsedTransits', {})
      this.filter.date_from = null
      this.filter.date_to = null
      this.filter.mark_id = null
      this.filter.showroom_id = null
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
</style>

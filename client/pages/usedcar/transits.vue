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
            class="elevation-1 grey lighten-4"
            fixed-header
            height="80vh"
            hide-default-footer
            item-key="id"
          >
            <template v-slot:header.income_date="{ header }">
              <span class="vertical"
                >Дата <br />
                прихода</span
              >
            </template>
            <template v-slot:header.created_at="{ header }">
              <span class="vertical"
                >Дата <br />
                транзита</span
              >
            </template>
            <template v-slot:header.owner_count="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.sender.name="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.receiver.name="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.volume="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.milage="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.showroom.name="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.bodytype.name="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.year="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.wheeltype.name="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.auto_ru="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.transmission.name="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.ptc="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.income_price="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.price="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.enginetype.name="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.salon="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.color="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.vin_number="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.number="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.sts="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.key_number="{ header }">
              <span class="vertical"
                >Номер <br />
                ключа</span
              >
            </template>
            <template v-slot:header.status.status="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:body="{ items }">
              <tbody>
                <tr v-for="item in items" :key="item.id" d>
                  <td>
                    <template v-if="item.created_at !== null">
                      {{ moment(new Date(item.created_at)).format('DD.MM.Y') }}
                    </template>
                  </td>
                  <td>
                    {{ item.sender && item.sender.name }} <br />
                    ({{ item.sender.role && item.sender.role.title }})
                  </td>
                  <td>
                    {{ item.receiver && item.receiver.name }} <br />
                    ({{ item.receiver.role && item.receiver.role.title }})
                  </td>
                  <td>
                    <template v-if="item.usedcar.income_date !== null">
                      {{
                        moment(new Date(item.usedcar.income_date)).format(
                          'DD.MM.Y'
                        )
                      }}
                    </template>
                  </td>
                  <td>
                    <span v-if="item.usedcar.auto_ru !== null"
                      ><a
                        :href="item.usedcar.auto_ru"
                        class="link"
                        target="_blank"
                      >
                        Перейти
                      </a></span
                    >
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
                      {{ item.showroom.name }}
                    </v-chip>
                  </td>
                  <td>{{ item.usedcar.mark && item.usedcar.mark.name }}</td>
                  <td>{{ item.usedcar.model && item.usedcar.model.name }}</td>
                  <td>{{ item.usedcar.year }}</td>
                  <td>
                    {{ item.usedcar.bodytype && item.usedcar.bodytype.name }}
                  </td>
                  <td>{{ item.usedcar.owner_count }}</td>
                  <td>{{ item.usedcar.milage }}</td>
                  <td>
                    {{ item.usedcar.wheeltype && item.usedcar.wheeltype.name }}
                  </td>
                  <td>
                    {{
                      item.usedcar.transmission &&
                      item.usedcar.transmission.name
                    }}
                  </td>
                  <td>
                    {{ item.usedcar.volume }} ({{ item.usedcar.power }} ЛС)
                  </td>
                  <td>
                    {{
                      item.usedcar.enginetype && item.usedcar.enginetype.name
                    }}
                  </td>
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
                    <span v-if="item.usedcar.registered === 1" key="da"
                      >Да</span
                    >
                    <span v-else key="net">Нет</span>
                  </td>
                  <td>{{ item.usedcar.key_number }}</td>
                  <td>
                    <v-chip
                      v-if="
                        item.usedcar.status &&
                        item.usedcar.status.status !== null
                      "
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
            <template v-slot:top>
              <v-toolbar dense>
                <v-toolbar-title>История транзитов</v-toolbar-title>
                <v-spacer />
              </v-toolbar>
            </template>
          </v-data-table>
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
  font-size: 0.7rem;
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
<script>
import JsonExcel from 'vue-json-excel'
import BreadCrumb from '~/components/BreadCrumb'

export default {
  layout: 'user',
  middleware: 'permission',
  components: { BreadCrumb, JsonExcel },
  data: () => ({
    menu1: null,
    menu: null,
    expanded: [],
    singleExpand: false,
    headers: [
      {
        text: 'Дата транзита',
        value: 'created_at',
        align: 'center',
        sortable: false,
        width: '40px',
      },
      {
        text: 'Отправил',
        value: 'sender.name',
        align: 'center',
        sortable: false,
        width: '30px',
      },
      {
        text: 'Получил',
        value: 'receiver.name',
        align: 'center',
        sortable: false,
        width: '30px',
      },
      {
        text: 'Дата прихода',
        value: 'income_date',
        align: 'center',
        sortable: false,
        width: '40px',
      },
      {
        text: 'Авто.ру',
        value: 'auto_ru',
        align: 'center',
        sortable: false,
        width: '30px',
      },
      {
        text: 'Салон',
        align: 'center',
        sortable: false,
        value: 'showroom.name',
        width: '30px',
      },
      {
        text: 'Марка',
        align: 'start',
        value: 'mark.name',
        width: '100px',
        sortable: false,
      },
      {
        text: 'Модель',
        value: 'model.name',
        width: '110px',
        align: 'center',
        sortable: false,
      },
      {
        text: 'Год',
        value: 'year',
        sortable: false,
        width: '50px',
        align: 'center',
      },
      {
        text: 'Кузов',
        value: 'bodytype.name',
        sortable: false,
        width: '50px',
        align: 'center',
      },
      {
        text: 'Соб.',
        value: 'owner_count',
        sortable: false,
        width: '10px',
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
        text: 'Салон',
        value: 'salon',
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
        text: 'Vin',
        value: 'vin_number',
        align: 'center',
        sortable: false,
        width: '120px',
      },
      {
        text: 'Гос.ном.',
        value: 'number',
        sortable: false,
        width: '60px',
        align: 'center',
      },
      {
        text: 'СТС',
        value: 'sts',
        sortable: false,
        width: '90px',
        align: 'center',
      },
      {
        text: 'Приход',
        value: 'income_price',
        sortable: false,
        width: '30px',
        align: 'center',
      },
      {
        text: 'Продажа',
        value: 'price',
        align: 'center',
        sortable: false,
        width: '20px',
      },
      {
        text: 'ПТС',
        value: 'ptc',
        sortable: false,
        align: 'center',
        width: '30px',
      },
      {
        text: 'На учете',
        value: 'registered',
        width: '10px',
        align: 'center',
        sortable: false,
      },
      {
        text: '№-ключа',
        value: 'key_number',
        width: '30px',
        align: 'center',
        sortable: false,
      },
      {
        text: 'Статус',
        value: 'status.status',
        align: 'center',
        sortable: false,
        width: '100px',
      },
    ],
  }),

  computed: {
    showroom() {
      return this.$store.state.showroom.showroom
    },
    transits() {
      return this.$store.state.usedcar.transits
    },
    filtered() {
      return this.transits.filter((item) => item.usedcar !== null)
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
    statuses() {
      return this.$store.state.usedcar.statuses
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
          text: 'История транзитов',
          disabled: true,
          href: '/' + this.role?.name + '/used-cars/history',
        },
      ]
    },
  },

  async fetch({ store, error, params: { id } }) {
    await store.dispatch('usedcar/fetchTransits')
    await store.dispatch('car/fetchMarks')
    await store.dispatch('car/fetchBodyTypes')
    await store.dispatch('car/fetchEngineTypes')
    await store.dispatch('car/fetchTransmissions')
    await store.dispatch('car/fetchWheelTypes')
    await store.dispatch('showroom/fetchShowroom', { id })
    await store.dispatch('usedcar/fetchStatuses')
    await store.dispatch('user/toggle', false)
  },
  methods: {
    canDownload() {
      const users = [
        'admin',
        'director',
        'coordinator',
        'coordinator1',
        'manager_acp',
        'manager_light',
      ]
      return users.includes(this.role?.name)
    },
    parseDate(date) {
      if (!date) {
        return null
      }
      // const [day, month, year] = date.split('.')
      return this.moment(date).format('YY-MM-dd')
    },
    row_classes(item) {
      if (item.transit === 1 || item.transit === 2) {
        return 'orange lighten-2' // can also return multiple classes e.g ["orange","disabled"]
      } else if (item.deleted_at !== null) {
        return 'black lighten-4 white--text'
      }
    },
  },
  beforeRouteLeave(to, from, next) {
    this.$store.dispatch('user/toggle', true)
    next()
  },
}
</script>

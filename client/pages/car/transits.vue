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
            <template v-slot:body="{ items }">
              <tbody>
                <tr v-for="item in items" :key="item.id">
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
                    <template v-if="item.car.income_date !== null">
                      {{
                        moment(new Date(item.car.income_date)).format('DD.MM.Y')
                      }}
                    </template>
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
                  <td>{{ item.car.mark && item.car.mark.name }}</td>
                  <td>{{ item.car.model && item.car.model.name }}</td>
                  <td>{{ item.car.year }}</td>
                  <td>{{ item.car.complectation }}</td>
                  <td>{{ item.car.wheeltype && item.car.wheeltype.name }}</td>
                  <td>
                    {{ item.car.transmission && item.car.transmission.name }}
                  </td>
                  <td>{{ item.car.volume }} ({{ item.car.power }} ЛС)</td>
                  <td>{{ item.car.color }}</td>
                  <td>{{ item.car.vin_number }}</td>
                  <td>{{ item.car.price }}</td>

                  <td>
                    <span v-if="item.car.ptc === 1">Есть</span>
                    <span v-else-if="item.car.ptc === 2">Дубликат</span>
                    <span v-else>Нет</span>
                  </td>
                  <td>{{ item.car.key_number }}</td>
                  <td>{{ item.comment }}</td>
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
</style>
<script>
import BreadCrumb from '~/components/BreadCrumb'
export default {
  layout: 'user',
  middleware: 'permission',
  components: { BreadCrumb },
  data: () => ({
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
        text: 'Комплектация',
        value: 'complectation',
        sortable: false,
        width: '50px',
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
        text: 'Цена',
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
        text: '№-ключа',
        value: 'key_number',
        width: '30px',
        align: 'center',
        sortable: false,
      },
      {
        text: 'Комментарий',
        value: 'comment',
        width: '30px',
        align: 'center',
        sortable: false,
      },
    ],
  }),

  computed: {
    showroom() {
      return this.$store.state.showroom.showroom
    },
    transits() {
      return this.$store.state.car.transits
    },
    filtered() {
      return this.transits.filter((item) => item.car !== null)
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
          text: 'Новые Автомобили',
          disabled: false,
          href: '/' + this.role?.name + '/cars',
        },
        {
          text: 'История транзитов',
          disabled: true,
          href: '/',
        },
      ]
    },
  },

  async fetch({ store }) {
    await store.dispatch('car/fetchTransits')
    await store.dispatch('user/toogle', false)
  },
  methods: {
    canDownload() {
      const users = [
        'admin',
        'director',
        'coordinator',
        'aidar',
        'coord',
        'coordinator1',
        'manager_acp',
        'manager_light',
      ]
      return users.includes(this.role?.name)
    },
  },
  beforeRouteLeave(to, from, next) {
    this.$store.dispatch('user/toogle', true)
    next()
  },
}
</script>

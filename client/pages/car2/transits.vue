<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links"/>
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
                <td>
                  <template v-if="item.sender">
                    {{ item.sender && item.sender.name }} <br> ({{ item.sender.role && item.sender.role.title }})
                  </template>
                </td>
                <td>
                  <template v-if="item.receiver">
                    {{ item.receiver && item.receiver.name }} <br> ({{
                      item.receiver.role && item.receiver.role.title
                    }})
                  </template>
                </td>
                <td>
                  <template v-if="item.car.income_date !== null">
                    {{ $moment(new Date(item.car.income_date)).format('DD.MM.Y') }}
                  </template>
                </td>
                <td>
                  <v-chip v-if="item.from_showroom" color="error" text-color="white" x-small>
                    {{
                      item.from_showroom.name
                    }}
                  </v-chip>
                </td>
                <td>
                  <v-chip
                    color="primary"
                    x-small
                  >
                    {{
                      item.showroom.name
                    }}
                  </v-chip>
                </td>
                <td>{{ item.car.mark && item.car.mark.name }}</td>
                <td>{{ item.car.model && item.car.model.name }}</td>
                <td>{{ item.car.year }}</td>
                <td>{{ item.car.complectation }}</td>
                <td>{{ item.car.wheeltype && item.car.wheeltype.name }}</td>
                <td>{{ item.car.transmission && item.car.transmission.name }}</td>
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
            <template #top>
              <v-toolbar dense>
                <v-toolbar-title>История транзитов</v-toolbar-title>
                <v-spacer/>
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
  name: 'CarTransits',
  components: {BreadCrumb},
  beforeRouteLeave(to, from, next) {
    this.$store.dispatch('user/toggle', true)
    next()
  },
  layout: 'user',
  middleware: 'permission',
  data: () => ({
    filter: {
      mark_id: null,
      date_from: null,
      date_to: null,
      showroom_id: null,
    },
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
        text: 'Откуда',
        align: 'center',
        sortable: false,
        value: 'from',
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
        text: 'Комплектация',
        value: 'complectation',
        sortable: false,
        width: '50px',
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
        text: 'Цена',
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
        text: '№-ключа',
        value: 'key_number',
        width: '30px',
        align: 'center',
        sortable: false
      },
      {
        text: 'Комментарий',
        value: 'comment',
        width: '30px',
        align: 'center',
        sortable: false
      }

    ]
  }),

  async fetch({store}) {
    await store.dispatch('transit/fetchTransits', {})
    await store.dispatch('user/toggle', false)
    await store.dispatch('showroom/fetchShowrooms')
    await store.dispatch('property/fetchMarks')
  },

  computed: {
    showroom() {
      return this.$store.state.showroom.showroom
    },
    showrooms() {
      return this.$store.state.showroom.showrooms
    },
    transits() {
      return this.$store.state.transit.transits
    },
    marks() {
      return this.$store.state.property.marks
    },
    filtered() {
      return this.transits.filter(
        item => item.car !== null
      )
    },
    role() {
      return this.$store.state.auth.role
    },
    links() {
      return [
        {
          text: 'Главная',
          disabled: false,
          href: '/'
        },
        {
          text: 'Новые Автомобили',
          disabled: false,
          href: '/cars'
        },
        {
          text: 'История транзитов',
          disabled: true,
          href: '/'
        }
      ]
    }
  },
  mounted () {
    this.$echo.channel('cars').listen('CarAdded', (e) => {
      this.$store.dispatch('transit/fetchTransits', this.filter)
    })
  },
  methods: {
    async apply_filter() {
      if (this.filter.date_from !== null && this.filter.date_to !== null) {
        await this.$store.dispatch('transit/fetchTransits', {
          mark_id: this.filter.mark_id,
          created_between: this.$moment(this.filter.date_from).format('YYYY-MM-DD') + ',' + this.$moment(this.filter.date_to).format('YYYY-MM-DD'),
          showroom_id: this.filter.showroom_id
          ,
        })
      } else {
        await this.$store.dispatch('transit/fetchTransits', {
          mark_id: this.filter.mark_id,
          showroom_id: this.filter.showroom_id
          ,
        })
      }

    },


    async clear() {
      await this.$store.dispatch('transit/fetchTransits', {})
      this.filter.date_from = null
      this.filter.date_to = null
      this.filter.mark_id = null
      this.filter.showroom_id = null
      this.$nextTick(() => {

      })
    },
  }
}
</script>

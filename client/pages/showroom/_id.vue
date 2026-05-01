<template>
  <v-container>
    <BreadCrumb :items="links"/>
    <v-row dense>
      <v-col v-if="false" cols="12">
        <apexchart  ref="sale_show" type="line" height="350" :options="chartOptions" :series="series"></apexchart>
      </v-col>

        <template v-for="(item, i) in items">
          <v-col
            :key="i"
            cols="12"
            md="3"
          >
            <v-hover v-slot="{ hover }">
              <v-card
                :elevation="hover ? 12 : 2"
                :class="{ 'on-hover': hover }"
                :to="item.link + showroom_id"
              >
                <div height="225px" class="bg-blue">
                  <v-card-title class="title white--text">
                    <v-row
                      class="fill-height flex-column pl-2"
                      justify="space-between"
                    >
                      <p class="mt-4 subheading text-center">
                        <v-icon color="yellow" x-large>
                          {{ item.icon }}
                        </v-icon>
                      </p>
                      <div>
                        <p class="ma-0 body-1 font-weight-bold text-center text-h6">
                          {{ item.title }}
                        </p>
                      </div>
                    </v-row>
                  </v-card-title>
                </div>
              </v-card>
            </v-hover>
          </v-col>
        </template>
    </v-row>
  </v-container>
</template>
<script>
import BreadCrumb from '~/components/BreadCrumb'
export default {
  components:{
    BreadCrumb
  },
  data() {
    return {
      items: [
        {
          title: 'Завяки',
          text: 'Таблица приезд клиентов ',
          icon: 'mdi-checkbox-marked-circle-outline',
          count: 800,
          link: '/crm/orders/'
        },
        {
          title: 'Приезд',
          text: 'Таблица приезд клиентов ',
          icon: 'mdi-login',
          count: 800,
          link: '/tablet/arrivals/'
        },
        {
          title: 'Кредитный отдел',
          text: 'Раздел кредитного отдела',
          count: 450,
          icon: 'mdi-margin',
          link: '/tablet/credit-request/'
        }
      ],
      showChart: false,
      series: [

        {
          name: 'Приезды',
          data: [25, 24, 22, 27, 28, 22, 31, 28, 26, 24, 27]
        },
        {
          name: 'Продажи',
          data: [6, 5, 4, 3, 19, 11, 12, 8, 11, 6, 9]
        },
        {
          name: 'Заявки на кредит',
          data: [18, 19, 17, 22, 23, 18, 26, 23, 19, 19, 23]
        }],
      chartOptions: {
        chart: {
          height: 350,
          type: 'area',
          defaultLocale: 'ru',
          locales: [{
            name: 'ru',
            options: {
              months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
              shortMonths: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
              days: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
              shortDays: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
              toolbar: {
                download: 'Скачать SVG',
                selection: 'Выбор',
                selectionZoom: 'Выбор масштаб',
                zoomIn: 'Увеличить',
                zoomOut: 'Уменьшить',
                pan: 'Панорамирование',
                reset: 'Сбросить масштаб',
              }
            }
          }]
        },
        dataLabels: {
          enabled: true
        },
        stroke: {
          curve: 'smooth'
        },
        labels: ['01/01/2003', '01/02/2003', '01/03/2003', '01/04/2003', '01/05/2003', '01/06/2003', '01/06/2003',
          '01/07/2003', '01/08/2003', '01/09/2003', '01/10/2003'
        ],
        markers: {
          size: 3
        },
        xaxis: {
          type: 'datetime'
        },
        yaxis: {
          title: {
            text: 'Аналитика'
          },
          min: 0
        },
        tooltip: {
          shared: true,
          intersect: false,
          y: {
            formatter (y) {
              if (typeof y !== 'undefined') {
                return y.toFixed(0) + ''
              }
              return y
            }
          }
        },

      }
    }
  },
  async fetch ({ store, error, params: { id } }) {
    await store.dispatch('car/fetchCars', {is_used: false, showroom_id: id})
    await store.dispatch('property/fetchMarks')
    await store.dispatch('property/fetchEngineTypes')
    await store.dispatch('property/fetchTransmissions')
    await store.dispatch('property/fetchWheelTypes')
    await store.dispatch('showroom/fetchShowroom', { id })
    await store.dispatch('showroom/fetchShowrooms')
    await store.dispatch('user/toggle', false)
  },

  computed: {
    showroom_id () {
      return Number(this.$route.params.id) || null
    },
    showroom () {
      return this.$store.state.showroom.showroom?.name
    },
    links() {
      return [
        {
          text: 'Главная',
          disabled: false,
          href: '/'
        },
        {
          text: 'Автосалоны',
          disabled: false,
          href: '/showrooms'
        },
        {
          text: this.showroom,
          disabled: true,
          href: '/showrooms/'+this.$route.params.id
        }
      ]
    },
  },
  mounted() {
    this.showChart = true
  }
}
</script>
<style scoped>
.v-card {
  transition: opacity 0.4s ease-in-out;
}

.v-card:not(.on-hover) {
  opacity: 1.2;
}

.bg-blue {
  background-color: #4caf50 !important;
  caret-color: #4caf50 !important;
}
</style>

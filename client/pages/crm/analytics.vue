<template>
  <v-container>
    <BreadCrumb :items="links"/>
    <v-row>
      <v-col cols="12">
        <apexchart ref="sale_show" type="line" height="350" :options="chartOptions" :series="series"></apexchart>
      </v-col>
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
  computed:{
    links() {
      return [
        {
          text: 'Главная',
          disabled: false,
          href: '/'
        },
        {
          text: 'Автосалоны',
          disabled: true,
          href: '/showrooms'
        },
        {
          text: this.$route.params.id,
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

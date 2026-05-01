<template>
  <div v-if="data">
    <v-data-table
      :headers="headers"
      :items="data"
      sort-by="calories"
      class="elevation-1"
      hide-default-footer
      dense
    >
      <template #top>
        <v-toolbar
          flat
        >
          <v-toolbar-title class="mr-3">
            Отчеты по агенству <span v-if="date_from && date_to">с {{ date_from }} по {{ date_to }}</span>
          </v-toolbar-title>
          <v-spacer/>
        </v-toolbar>
      </template>
      <template #body>
        <tbody>
        <tr
          v-for="item in data"
          :key="item.id"
        >
          <td>{{ item.JustWe }}</td>
          <td>{{ item.Victory }}</td>
          <td>{{ item.Classified }}</td>
          <td>{{ item.Seo }}</td>
          <td>{{ item.Agency1 }}</td>
          <td>{{ item.VA }}</td>
          <td>{{ item.Whatsapp }}</td>
          <td>{{ item.Trash }}</td>
          <td>{{ item.Others }}</td>
          <td>{{ item.total }}</td>
        </tr>
        </tbody>
      </template>
      <template #no-data>
        Пусто
      </template>
    </v-data-table>

    <apexchart
      type="pie"
      height="420"
      :options="chartOptions"
      :series="series"
    ></apexchart>
  </div>
</template>
<script>
import sumBy from 'lodash.sumby'

export default {
  props: {
    date_from: {
      type: String,
      required: false
    },
    date_to: {
      type: String,
      required: false
    },
    data: {
      type: Array,
      required: true
    },
  },
  data: () => ({
    headers: [
      {
        text: 'JustWe',
        align: 'start',
        sortable: false,
        value: 'title'
      },
      {
        text: 'Victory',
        align: 'start',
        sortable: false,
        value: 'title'
      },
      {
        text: 'Классифайд',
        align: 'start',
        sortable: false,
        value: 'title'
      },
      {
        text: 'SEO',
        align: 'start',
        sortable: false,
        value: 'title'
      },
      {
        text: 'Agency1',
        align: 'start',
        sortable: false,
        value: 'title'
      },
      {
        text: 'VA',
        align: 'start',
        sortable: false,
        value: 'title'
      },

      {
        text: 'Whatsapp',
        align: 'start',
        sortable: false,
        value: 'title'
      },
      {
        text: 'Корзина',
        align: 'start',
        sortable: false,
        value: 'title'
      },
      {
        text: 'Другое',
        align: 'start',
        sortable: false,
        value: 'title'
      },
      {text: 'Всего', value: 'total', sortable: false}
    ],

    chartOptions: {
      chart: {
        width: 380,
        type: 'pie',
      },
      labels: ['JustWe', 'Victory','Classified','Seo', 'Agency1','Whatsapp',  'Корзина', 'Другие'],
      responsive: [{
        breakpoint: 480,
        options: {
          chart: {
            width: 200
          },
          legend: {
            position: 'bottom'
          }
        }
      }]
    },
  }),
  computed: {
    totalAmount() {
      return sumBy(this.data, function (item) {
        return parseInt(item.total);
      });
    },
    series() {
      console.log(this.data[0])
      return [parseInt(this.data[0].JustWe), parseInt(this.data[0].Victory), parseInt(this.data[0].Classified), parseInt(this.data[0].Seo), parseInt(this.data[0].Agency1), parseInt(this.data[0].StoUp), parseInt(this.data[0].Trash), parseInt(this.data[0].Others)]
    },
  },
  created() {
    this.$nuxt.$on('export-report', () => {
      console.log(123)
      this.export()
    })
  }

}

</script>

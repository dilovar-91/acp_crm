<template>
  <v-data-table
    v-if="data"
    :headers="headers"
    :items="data"
    sort-by="calories"
    class="elevation-1"
    hide-default-footer
  >
    <template #top>
      <v-toolbar flat>
        <v-toolbar-title class="mr-3">
          Отчеты по сайтам <span v-if="date_from && date_to">с {{ date_from }} по {{ date_to }}</span>
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
        <td>{{ item.title }}</td>
        <td class="font-weight-bold"><a class="link-header" target="_blank" :href="getLink(item, 1)">{{ item.NewCount }}</a></td>
        <td class="font-weight-bold"><a class="link-header" target="_blank" :href="getLink(item, 2)">{{ item.OnProccessCount }}</a></td>
        <td class="font-weight-bold"><a class="link-header" target="_blank" :href="getLink(item, 3)">{{ item.NoAnswerCount }}</a></td>
        <td class="font-weight-bold"><a class="link-header" target="_blank" :href="getLink(item, 4)">{{ item.ApprovedCount }}</a></td>
        <td class="font-weight-bold"><a class="link-header" target="_blank" :href="getLink(item, 5)">{{ item.WillArriveCount }}</a></td>


        <td>{{ item.TrashCount }}</td>
        <td>{{ item.UnliquidCount }}</td>
        <td>{{ item.RetryCount }}</td>
        <td>{{ item.ArrivedCount }}</td>
        <td>{{ item.SellCount }}</td>
        <td>{{ item.BreakCount }}</td>
        <td>{{ item.NewRegionCount }}</td>
        <td>{{ getEfficiency(item) }}</td>
        <td><a class="link-header" target="_blank" :href="getLink(item)">{{ item.total }}</a></td>
      </tr>

      <tr>
        <td>Итого</td>
        <td>{{ getSum('NewCount') }}</td>
        <td>{{ getSum('OnProccessCount') }}</td>
        <td>{{ getSum('NoAnswerCount') }}</td>
        <td>{{ getSum('ApprovedCount') }}</td>
        <td>{{ getSum('WillArriveCount') }}</td>
        <td>{{ getSum('TrashCount') }}</td>
        <td>{{ getSum('UnliquidCount') }}</td>
        <td>{{ getSum('RetryCount') }}</td>
        <td>{{ getSum('ArrivedCount') }}</td>
        <td>{{ getSum('SellCount') }}</td>
        <td>{{ getSum('BreakCount') }}</td>
        <td>{{ getSum('NewRegionCount') }}</td>
        <td>{{ getTotal(null, 'efficiency') }}</td>
        <td>{{ getSum('total') }}</td>
      </tr>
      </tbody>
    </template>
    <template #no-data>
      Пусто
    </template>
  </v-data-table>
</template>
<style scoped>
.link-header {
  color: #000000;
  text-decoration-color: #000000;
  font-weight: 600;
}
</style>
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
    agency_id: {
      type: Number,
      default: null,
      required: false
    },
    showroom_id: {
      type: Number,
      default: null,
      required: false
    }

  },
  data: () => ({
    headers: [
      {
        text: 'Сайт',
        align: 'start',
        sortable: false,
        value: 'name'
      },
      {text: 'Новая', value: 'calories', sortable: false},
      {text: 'В работе', value: 'fat', sortable: false},
      {text: 'Не отвечает', value: 'fat', sortable: false},
      {text: 'Одобрить', value: 'carbs', sortable: false},
      {text: 'Приедет', value: 'carbs', sortable: false},
      {text: 'Корзина', value: 'protein', sortable: false},
      {text: 'Не ликвид', value: 'protein', sortable: false},
      {text: 'Повтор', value: 'actions', sortable: false},
      {text: 'Приехал', value: 'actions', sortable: false},
      {text: 'Продажа', value: 'actions', sortable: false},
      {text: 'Соскок', value: 'actions', sortable: false},
      {text: 'Прочие', value: 'actions', sortable: false},
      {text: 'Эффективность', value: 'actions', sortable: false},
      {text: 'Всего', value: 'actions', sortable: false}
    ]

  }),
  methods: {
    getTotal(field, type = null) {
      switch (type) {
        case 'efficiency':
          const total = sumBy(this.data, function (item) {
            return item.total;
          });
          return ((((parseInt(this.getSum('WillArriveCount')) || 0) + (parseInt(this.getSum('ArrivedCount')) || 0)) * 100) / (total - (parseInt(this.getSum('RetryCount')) || 0) - (parseInt(this.getSum('UnliquidCount')) || 0) - (parseInt(this.getSum('NewCount')) || 0) - (parseInt(this.getSum('NoAnswerCount')) || 0) )).toFixed(2) + '%'
        default:
          return this.getSum('NewCount')
      }
    },
    getSum(field) {
      return sumBy(this.data, function (item) {
        return parseInt(item[field]);
      });
    },
    getEfficiency(item) {
      return ((((parseInt(item.WillArriveCount) || 0)  + (parseInt(item.ArrivedCount) || 0)) * 100) / ((parseInt(item.total)  - (parseInt(this.getSum('RetryCount')) || 0) - (parseInt(this.getSum('UnliquidCount')) || 0) - (parseInt(item.NewCount) || 0) - (parseInt(item.NoAnswerCount) || 0)) || 1)).toFixed(2) + '%'
    },

    getLink(item, status = null, agency_id = null, payment_id = null, approved = null, site_id = null) {
      let status_query = ''
      let payment_query = ''
      let agency_query = ''
      let approved_query = ''
      if (status) {
        status_query = 'status=' + status
      }
      if (payment_id) {
        payment_query = '&payment_method=' + payment_id
      }

      if (approved) {
        approved_query = '&approved=' + approved
      }
      return 'https://' + window.location.hostname + '/crm/' + item.showroom_id + '/orders-mini/' + item.agency_id+ '?' + status_query  + payment_query + approved_query + '&site_id=' + item.id + '&from=' + this.$moment(this.date_from, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') + '&to=' + this.$moment(this.date_to, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss')
    }
  }
}

</script>

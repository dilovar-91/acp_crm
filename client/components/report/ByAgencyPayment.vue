<template>
  <v-data-table
    v-if="data"
    :headers="headers"
    :items="data"
    sort-by="calories"
    class="elevation-1"
    fixed-header
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
        <td>{{ item.CashCount }}</td>
        <td>{{ item.CreditCount }}</td>
        <td>{{ item.CreditSaleCount }}</td>
        <td>{{ item.LeasingCount }}</td>
        <td>{{ item.NotDialingCount }}</td>
        <td>{{ item.RetryCount }}</td>
        <td>{{ item.UndefinedCount }}</td>
        <td>{{ item.LdnrCount }}</td>
        <td>{{ item.total }}</td>
      </tr>

      <tr>
        <td>Итого</td>
        <td>{{ getSum('CashCount') }}</td>
        <td>{{ getSum('CreditCount') }}</td>
        <td>{{ getSum('CreditSaleCount') }}</td>
        <td>{{ getSum('LeasingCount') }}</td>
        <td>{{ getSum('NotDialingCount') }}</td>
        <td>{{ getSum('RetryCount') }}</td>
        <td>{{ getSum('UndefinedCount') }}</td>
        <td>{{ getSum('LdnrCount') }}</td>
        <td>{{ getSum('total') }}</td>
      </tr>
      </tbody>
    </template>
    <template #no-data>
      Пусто
    </template>
  </v-data-table>
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
      {text: 'Наличными', value: 'calories', sortable: false},
      {text: 'В кредит', value: 'fat', sortable: false},
      {text: 'Кредит(Скидка)', value: 'fat', sortable: false},
      {text: 'Лизинг', value: 'carbs', sortable: false},
      {text: 'Не дозвон', value: 'carbs', sortable: false},
      {text: 'Повтор', value: 'actions', sortable: false},
      {text: 'Не определено', value: 'actions', sortable: false},
      {text: 'ЛНР/ДНР', value: 'actions', sortable: false},
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
          //return ((((this.getSum('OnProccessCount') * 100) / total) || 0) + (((this.getSum('WillArriveCount') * 100) / total) || 0) + (((this.getSum('NotArrivedCount') * 100) / total) || 0)).toFixed(2) + '%'
          return ((((parseInt(this.getSum('WillArriveCount')) || 0) + (parseInt(this.getSum('ArrivedCount')) || 0)) * 100) / (total - (parseInt(this.getSum('RetryCount')) || 0) - (parseInt(this.getSum('NewCount')) || 0) - (parseInt(this.getSum('NoAnswerCount')) || 0) )).toFixed(2) + '%'
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
      return ((((parseInt(item.WillArriveCount) || 0)  + (parseInt(item.ArrivedCount) || 0)) * 100) / ((parseInt(item.total)  - (parseInt(this.getSum('RetryCount')) || 0) - (parseInt(item.NewCount) || 0) - (parseInt(item.NoAnswerCount) || 0)) || 1)).toFixed(2) + '%'
    }
  }
}

</script>

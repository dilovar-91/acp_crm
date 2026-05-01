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
          Отчеты по UTM Medium<span v-if="date_from && date_to"> с {{ date_from }} по {{ date_to }}</span>
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
        <td>{{ item.utm_medium }}</td>
        <td>{{ item.NewCount }}</td>
        <td>{{ item.OnProccessCount }}</td>
        <td>{{ item.NoAnswerCount }}</td>
        <td>{{ item.ApprovedCount }}</td>
        <td>{{ item.WillArriveCount }}</td>
        <td>{{ item.TrashCount }}</td>
        <td>{{ item.RetryCount }}</td>
        <td>{{ item.ArrivedCount }}</td>
        <td>{{ item.SellCount }}</td>
        <td>{{ item.BreakCount }}</td>
        <td>{{ item.NewRegionCount }}</td>
        <td>{{ getEfficiency(item) }}</td>
        <td>{{ item.order_counts }}</td>
      </tr>

      <tr>
        <td >Итого</td>
        <td ></td>
        <td>{{ getSum('NewCount') }}</td>
        <td>{{ getSum('OnProccessCount') }}</td>
        <td>{{ getSum('NoAnswerCount') }}</td>
        <td>{{ getSum('ApprovedCount') }}</td>
        <td>{{ getSum('WillArriveCount') }}</td>
        <td>{{ getSum('TrashCount') }}</td>
        <td>{{ getSum('RetryCount') }}</td>
        <td>{{ getSum('ArrivedCount') }}</td>
        <td>{{ getSum('SellCount') }}</td>
        <td>{{ getSum('BreakCount') }}</td>
        <td>{{ getSum('NewRegionCount') }}</td>
        <td>{{ getTotal(null, 'efficiency') }}</td>
        <td>{{ getSum('order_counts') }}</td>
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
      {
        text: ' UTM Medium',
        align: 'start',
        sortable: false,
        value: 'campaign'
      },
      {text: 'Новая', value: 'calories', sortable: false},
      {text: 'В работе', value: 'fat', sortable: false},
      {text: 'Не отвечает', value: 'fat', sortable: false},
      {text: 'Одобрить', value: 'carbs', sortable: false},
      {text: 'Приедет', value: 'carbs', sortable: false},
      {text: 'Корзина', value: 'protein', sortable: false},
      {text: 'Повтор', value: 'actions', sortable: false},
      {text: 'Приехал', value: 'actions', sortable: false},
      {text: 'Продажа', value: 'actions', sortable: false},
      {text: 'Соскок', value: 'actions', sortable: false},
      {text: 'ЛНР/ДНР', value: 'actions', sortable: false},
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
      return ((((parseInt(item.WillArriveCount) || 0)  + (parseInt(item.ArrivedCount) || 0)) * 100) / ((parseInt(item.order_counts)  - (parseInt(this.getSum('RetryCount')) || 0) - (parseInt(item.NewCount) || 0) - (parseInt(item.NoAnswerCount) || 0)) || 1)).toFixed(2) + '%'
    }
  }
}

</script>

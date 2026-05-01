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
          Отчеты по операторам PV <span v-if="date_from && date_to">с {{ date_from }} по {{ date_to }}</span>
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
        <td>{{ item.name }}</td>
        <td>{{ item.showroom }}</td>
        <td>{{ item.NewCount }}</td>
        <td>{{ item.NoAnswerCount }}</td>
        <td>{{ item.TrashCount }}</td>
        <td>{{ item.AutoAnswerCount }}</td>
        <td>{{ item.RetryCount }}</td>
        <td>{{ item.CopiedCount }}</td>
        <td>{{ item.ZeroCount }}</td>
        <td>{{ item.OneCount }}</td>
        <td>{{ item.TwoCount }}</td>
        <td>{{ item.ThreeCount }}</td>
        <td>{{ item.FourCount }}</td>
        <td>{{ item.FiveCount }}</td>
        <td>{{ getEfficiency(item) }}</td>
        <td>{{ item.total }}</td>
      </tr>

      <tr>
        <td colspan="2">Итого</td>
        <td>{{ getSum('NewCount') }}</td>
        <td>{{ getSum('NoAnswerCount') }}</td>
        <td>{{ getSum('TrashCount') }}</td>
        <td>{{ getSum('AutoAnswerCount') }}</td>
        <td>{{ getSum('RetryCount') }}</td>
        <td>{{ getSum('CopiedCount') }}</td>
        <td>{{ getSum('ZeroCount') }}</td>
        <td>{{ getSum('OneCount') }}</td>
        <td>{{ getSum('TwoCount') }}</td>
        <td>{{ getSum('ThreeCount') }}</td>
        <td>{{ getSum('FourCount') }}</td>
        <td>{{ getSum('FiveCount') }}</td>
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
        text: 'Оператор',
        align: 'start',
        sortable: false,
        value: 'name'
      },
      {
        text: 'Салон',
        align: 'start',
        sortable: false,
        value: 'showroom'
      },
      {text: 'Новая', value: 'calories', sortable: false},
      {text: 'Не отвечает', value: 'fat', sortable: false},
      {text: 'Корзина', value: 'protein', sortable: false},
      {text: 'Корзина АО', value: 'aa_count', sortable: false},
      {text: 'Повтор', value: 'actions', sortable: false},
      {text: 'Передано', value: 'actions', sortable: false},
      {text: '0 раз', value: 'actions', sortable: false},
      {text: '1 раз', value: 'actions', sortable: false},
      {text: '2 раз', value: 'actions', sortable: false},
      {text: '3 раз', value: 'actions', sortable: false},
      {text: '4 раз', value: 'actions', sortable: false},
      {text: '5 раз', value: 'actions', sortable: false},
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
          return ((((parseInt(this.getSum('CopiedCount')) || 1)) * 100) / ((parseInt(this.getSum('total')) || 0) - (parseInt(this.getSum('RetryCount')) || 0) - (parseInt(this.getSum('NewCount')) || 0))).toFixed(2) + '%'
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
      return ((( (parseInt(item.CopiedCount) || 0) ) * 100) / (((parseInt(item.total) || 0) - (parseInt(item.NewCount) || 0) - (parseInt(item.RetryCount) || 0))) || 0).toFixed(2) + '%'
    },


    getEfficiencyTest(item) {
      return "(" + String(parseInt(item.CopiedCount)) + ") *100/(" +  String((parseInt(item.total) || 0) - (parseInt(item.NewCount) || 0) - (parseInt(item.RetryCount) || 0)) +" )"
    }
  }
}

</script>

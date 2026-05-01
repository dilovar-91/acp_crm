<template>
  <div>
    <v-data-table
      v-if="data"
      :items="data"
      :headers="headers"
      sort-by="calories"
      class="elevation-1"
      hide-default-footer
      fixed-header
      height="68vh"
    >
      <template #top>
        <v-toolbar flat>
          <v-toolbar-title class="mr-3">
            Отчеты Victory <span v-if="date_from && date_to">с {{ date_from }} по {{ date_to }}</span>
          </v-toolbar-title>
          <v-spacer/>
        </v-toolbar>


      </template>
      <template #body>
        <tbody>
          <tr
            v-for="(item) in data"
            :key="item.id">
            <td></td>
            <td>{{ item.title }}</td>
            <td>{{ item.NewCount }}</td>
            <td>{{ item.NoAnswerCount }}</td>
            <td>{{ item.TrashCount }}</td>
            <td>{{ item.RetryCount }}</td>
            <td>{{ item.PassedCount }}</td>
            <td>{{ item.ZeroCount }}</td>
            <td>{{ item.OneCount }}</td>
            <td>{{ item.TwoCount }}</td>
            <td>{{ item.ThreeCount }}</td>
            <td>{{ item.FourCount }}</td>
            <td>{{ item.FiveCount }}</td>
            <td>{{ item.total }}</td>
          </tr>


        <tr class="row-head">
          <td colspan="2">Итого</td>
          <td>{{ getSum('NewCount') }}</td>
          <td>{{ getSum('NoAnswerCount') }}</td>
          <td>{{ getSum('TrashCount') }}</td>
          <td>{{ getSum('RetryCount') }}</td>
          <td>{{ getSum('PassedCount') }}</td>
          <td>{{ getSum('ZeroCount') }}</td>
          <td>{{ getSum('OneCount') }}</td>
          <td>{{ getSum('TwoCount') }}</td>
          <td>{{ getSum('ThreeCount') }}</td>
          <td>{{ getSum('FourCount') }}</td>
          <td>{{ getSum('FiveCount') }}</td>
          <td>{{ getSum('total') }}</td>
        </tr>
        </tbody>
      </template>
      <template #no-data>
        Пусто
      </template>
    </v-data-table>
  </div>
</template>
<style scoped>
.row-head {
  background: #f5f5f5;
  color: black;
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
    }

  },
  data: () => ({
    headers: [
      {
        text: 'Салон',
        align: 'start',
        sortable: false,
        value: 'name'
      },
      {text: 'Сайт', value: 'calories', sortable: false},
      {text: 'Новая', value: 'calories', sortable: false},
      {text: 'Не отвечает', value: 'fat', sortable: false},
      {text: 'Корзина', value: 'protein', sortable: false},
      {text: 'Повтор', value: 'actions', sortable: false},
      {text: 'Передано', value: 'actions', sortable: false},
      {text: '0 раз', value: 'actions', sortable: false},
      {text: '1 раз', value: 'actions', sortable: false},
      {text: '2 раз', value: 'actions', sortable: false},
      {text: '3 раз', value: 'actions', sortable: false},
      {text: '4 раз', value: 'actions', sortable: false},
      {text: '5 раз', value: 'actions', sortable: false},
      {text: 'Всего', value: 'actions', sortable: false}
    ]

  }),
  methods: {
    getSum(field) {
      return sumBy(this.data, function (item) {
        return parseInt(item[field]);
      });
    },
  }
}

</script>

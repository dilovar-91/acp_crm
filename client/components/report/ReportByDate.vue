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
          Отчеты по датам от <span v-if="date_from && date_to">с {{ date_from }} по {{ date_to }}</span>
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
        <td>{{ $moment(item.created).format('DD.MM.YYYY') }}</td>
        <td>{{ item.CallCount }}</td>
        <td>{{ item.CreditCount }}</td>
        <td>{{ item.total }}</td>
      </tr>

      <tr>
        <td>Итого</td>
        <td>{{ getSum('CallCount') }}</td>
        <td>{{ getSum('CreditCount') }}</td>
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
      {text: 'Звонок', value: 'calories', sortable: false},
      {text: 'Кредит', value: 'fat', sortable: false},
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

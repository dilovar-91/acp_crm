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
        <td><a target="_blank" :href="getLink(item, 1)">{{ item.NewCount }}</a></td>
        <td><a target="_blank" :href="getLink(item, 3)">{{ item.NoAnswerCount }}</a></td>
        <td><a target="_blank" :href="getLink(item, 7)">{{ item.TrashCount }}</a></td>
        <td><a target="_blank" :href="getLink(item, 8)">{{ item.RetryCount }}</a></td>
        <td><a target="_blank" :href="getLink(item, 1000)">{{ item.PassedCount }}</a></td>
        <td>{{ item.ZeroCount }}</td>
        <td>{{ item.OneCount }}</td>
        <td>{{ item.TwoCount }}</td>
        <td>{{ item.ThreeCount }}</td>
        <td>{{ item.FourCount }}</td>
        <td>{{ item.FiveCount }}</td>
        <td>{{ getEfficiency(item) }}</td>
        <td><a target="_blank" :href="getLink(item, null)">{{ item.total }}</a></td>
      </tr>

      <tr>
        <td>Итого</td>
        <td><a target="_blank" :href="getLink(null, 1, true)">{{ getSum('NewCount') }}</a></td>
        <td><a target="_blank" :href="getLink(null, 3, true)">{{ getSum('NoAnswerCount') }}</a></td>
        <td><a target="_blank" :href="getLink(null, 7, true)">{{ getSum('TrashCount') }}</a></td>
        <td><a target="_blank" :href="getLink(null, 8, true)">{{ getSum('RetryCount') }}</a></td>
        <td><a target="_blank" :href="getLink(null, 1000, true)">{{ getSum('PassedCount') }}</a></td>
        <td>{{ getSum('ZeroCount') }}</td>
        <td>{{ getSum('OneCount') }}</td>
        <td>{{ getSum('TwoCount') }}</td>
        <td>{{ getSum('ThreeCount') }}</td>
        <td>{{ getSum('FourCount') }}</td>
        <td>{{ getSum('FiveCount') }}</td>
        <td>{{ getTotal(null, 'efficiency') }}</td>
        <td><a target="_blank" :href="getLink(null, null, true)">{{ getSum('total') }}</a></td>
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
      {text: 'Эффективность', value: 'actions', sortable: false},
      {text: 'Всего', value: 'actions', sortable: false}
    ]

  }),
  methods: {
    getLink(item, status=null, isTotal= false) {
      if (isTotal && status){
        return 'https://'+window.location.hostname+'/crm/' + this.$route.params?.id + '/light-orders?status=' + status +'&from='+this.$moment(this.date_from, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') + '&to='+this.$moment(this.date_to, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss')
      }
      else if (isTotal && !status){
        return 'https://'+window.location.hostname+'/crm/' + this.$route.params?.id + '/light-orders?from='+this.$moment(this.date_from, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') + '&to='+this.$moment(this.date_to, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss')
      }
      if (!status){
        return 'https://'+window.location.hostname+'/crm/' + item.showroom_id + '/light-orders?operator_id='+item.id + '&from='+this.$moment(this.date_from, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') + '&to='+this.$moment(this.date_to, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss')
      }
      return 'https://'+window.location.hostname+'/crm/' + item.showroom_id + '/light-orders?status=' + status +'&operator_id='+item.id + '&from='+this.$moment(this.date_from, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') + '&to='+this.$moment(this.date_to, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss')
    },
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
  }
}

</script>

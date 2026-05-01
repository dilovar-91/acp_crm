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
          Отчеты по операторам <span v-if="date_from && date_to">с {{ date_from }} по {{ date_to }}</span>
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
        <td><a target="_blank" :href="getLink(item, 1)">{{ item.NewCount }}</a></td>
        <td><a target="_blank" :href="getLink(item, 2)">{{ item.OnProccessCount }}</a></td>
        <td><a target="_blank" :href="getLink(item, 3)">{{ item.NoAnswerCount }}</a></td>
        <td><a target="_blank" :href="getLink(item, 4)">{{ item.ApprovedCount }}</a></td>
        <td><a target="_blank" :href="getLink(item, 5)">{{ item.WillArriveCount }}</a></td>
        <td><a target="_blank" :href="getLink(item, 7)">{{ item.TrashCount }}</a></td>
        <td><a target="_blank" :href="getLink(item, 15)">{{ item.UnliquidCount }}</a></td>
        <td><a target="_blank" :href="getLink(item, 8)">{{ item.RetryCount }}</a></td>
        <td><a target="_blank" :href="getLink(item, 6)">{{ item.ArrivedCount }}</a></td>
        <td>{{ item.SellCount }}</td>
        <td>{{ item.BreakCount }}</td>
        <td><a target="_blank" :href="getLink(item, 11)">{{ item.NewRegionCount }}</a></td>
        <td>{{ item.UndefindedCount }}</td>
        <td>{{ item.CashCount }}</td>
        <td>{{ item.CreditCount }}</td>
        <td>{{ item.CreditSaleCount }}</td>
        <td>{{ item.LDNRCount }}</td>
        <td>{{ item.LisingCount }}</td>
        <td>{{ item.NoCallCount }}</td>
        <td>{{ item.RepeatCount }}</td>
        <td>{{ getEfficiency(item) }}</td>
        <td><a target="_blank" :href="getLink(item, null)">{{ item.total }}</a></td>
      </tr>

      <tr>
        <td>Итого</td>
        <td><a target="_blank" :href="getLink(null, 1, true)">{{ getSum('NewCount') }}</a></td>
        <td><a target="_blank" :href="getLink(null, 2, true)">{{ getSum('OnProccessCount') }}</a></td>
        <td><a target="_blank" :href="getLink(null, 3, true)">{{ getSum('NoAnswerCount') }}</a></td>
        <td><a target="_blank" :href="getLink(null, 4, true)">{{ getSum('ApprovedCount') }}</a></td>
        <td><a target="_blank" :href="getLink(null, 5, true)">{{ getSum('WillArriveCount') }}</a></td>
        <td><a target="_blank" :href="getLink(null, 7, true)">{{ getSum('TrashCount') }}</a></td>
        <td><a target="_blank" :href="getLink(null, 15, true)">{{ getSum('UnliquidCount') }}</a></td>
        <td><a target="_blank" :href="getLink(null, 8, true)">{{ getSum('RetryCount') }}</a></td>
        <td><a target="_blank" :href="getLink(null, 6, true)">{{ getSum('ArrivedCount') }}</a></td>
        <td>{{ getSum('SellCount') }}</td>
        <td>{{ getSum('BreakCount') }}</td>
        <td><a target="_blank" :href="getLink(null, 11, true)">{{ getSum('NewRegionCount') }}</a></td>
        <td>{{ getSum('UndefindedCount') }}</td>
        <td>{{ getSum('CashCount') }}</td>
        <td>{{ getSum('CreditCount') }}</td>
        <td>{{ getSum('CreditSaleCount') }}</td>
        <td>{{ getSum('LDNRCount') }}</td>
        <td>{{ getSum('LisingCount') }}</td>
        <td>{{ getSum('NoCallCount') }}</td>
        <td>{{ getSum('RepeatCount') }}</td>

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
        text: 'Оператор',
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
      {text: 'ЛНР/ДНР', value: 'actions', sortable: false},

      {text: 'Не определено', value: 'actions', sortable: false},
      {text: 'Наличка', value: 'actions', sortable: false},
      {text: 'Кредит', value: 'actions', sortable: false},
      {text: 'Кредит(C)', value: 'actions', sortable: false},
      {text: 'ЛНР/ДНР(СО)', value: 'actions', sortable: false},
      {text: 'Лизинг', value: 'actions', sortable: false},
      {text: 'Не дозвон', value: 'actions', sortable: false},
      {text: 'Повтор', value: 'actions', sortable: false},
      {text: 'Эффективность', value: 'actions', sortable: false},
      {text: 'Всего', value: 'actions', sortable: false}
    ]

  }),
  methods: {
    getLink(item, status=null, isTotal= false) {
      if (isTotal && status){
        return 'https://'+window.location.hostname+'/crm/' + this.$route.params?.id + '/orders-mini/'+ this.$route.params?.agency+'?status=' + status +'&from='+this.$moment(this.date_from, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') + '&to='+this.$moment(this.date_to, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss')
      }
      else if (isTotal && !status){
        return 'https://'+window.location.hostname+'/crm/' + this.$route.params?.id + '/orders-mini/'+ this.$route.params?.agency+'?from='+this.$moment(this.date_from, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') + '&to='+this.$moment(this.date_to, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss')
      }
      if (!status){
        return 'https://'+window.location.hostname+'/crm/' + item.showroom_id + '/orders-mini/'+ this.$route.params?.agency+'?operator_id='+item.id + '&from='+this.$moment(this.date_from, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') + '&to='+this.$moment(this.date_to, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss')
      }
      return 'https://'+window.location.hostname+'/crm/' + item.showroom_id + '/orders-mini/'+ this.$route.params?.agency+'?status=' + status +'&operator_id='+item.id + '&from='+this.$moment(this.date_from, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') + '&to='+this.$moment(this.date_to, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss')
    },
    getTotal(field, type = null) {
      switch (type) {
        case 'efficiency':
          const total = sumBy(this.data, function (item) {
            return item.total;
          });
          //return ((((this.getSum('OnProccessCount') * 100) / total) || 0) + (((this.getSum('WillArriveCount') * 100) / total) || 0) + (((this.getSum('NotArrivedCount') * 100) / total) || 0)).toFixed(2) + '%'
          return ((((parseInt(this.getSum('WillArriveCount')) || 0) + (parseInt(this.getSum('ArrivedCount')) || 0) + (parseInt(this.getSum('SellCount')) || 0) + (parseInt(this.getSum('BreakCount')) || 0)) * 100) / ((parseInt(this.getSum('OnProccessCount')) || 0) + (parseInt(this.getSum('NoAnswerCount')) || 0) + (parseInt(this.getSum('ApprovedCount')) || 0)+ (parseInt(this.getSum('WillArriveCount')) || 0)+ (parseInt(this.getSum('ArrivedCount')) || 0)+ (parseInt(this.getSum('TrashCount')) || 0)+ (parseInt(this.getSum('SellCount')) || 0)+ (parseInt(this.getSum('BreakCount')) || 0) )).toFixed(2) + '%'
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
      return ((( (parseInt(item.WillArriveCount) || 0) + (parseInt(item.ArrivedCount) || 0) + (parseInt(item.SellCount) || 0) + (parseInt(item.BreakCount) || 0)) * 100) / (((parseInt(item.OnProccessCount) || 0) + (parseInt(item.NoAnswerCount) || 0) + (parseInt(item.ApprovedCount) || 0) + (parseInt(item.WillArriveCount) || 0) + (parseInt(item.ArrivedCount) || 0) + (parseInt(item.TrashCount) || 0) + (parseInt(item.SellCount) || 0) + (parseInt(item.BreakCount) || 0)) || 1)).toFixed(2) + '%'
    }
  }
}

</script>

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
            Отчеты одобренные <span v-if="date_from && date_to">с {{ date_from }} по {{ date_to }}</span>
          </v-toolbar-title>
          <v-spacer/>
        </v-toolbar>
      </template>
      <template #body>
        <tbody>

        <tr
          v-for="(item) in data"
          :key="item.id">

          <td>{{ item.name }}</td>
          <td><a target="_blank" :href="getLink(item, 'CreditCount')">{{ item.CreditCount }}</a></td>
          <td>{{ item.CreditSaleCount }}</td>
          <td><a target="_blank" :href="getLink(item, 'ApprovedCreditCount')">{{ item.ApprovedCreditCount }}</a></td>
          <td>{{ item.ApprovedCreditSaleCount }}</td>
          <td class="text-center">
            <v-chip
              class="ma-2 white--text"
              :color="getColor(getPercentage(item))"
            >
              {{ getPercentage(item) + '%' }}
            </v-chip>
          </td>
        </tr>
        <tr class="row-head">
          <td>Итого</td>
          <td>{{ getSum('CreditCount') }}</td>
          <td>{{ getSum('CreditSaleCount') }}</td>
          <td>{{ getSum('ApprovedCreditCount') }}</td>
          <td>{{ getSum('ApprovedCreditSaleCount') }}</td>
          <td class="text-center">
            <v-chip
              class="ma-2 white--text"
              :color="getColor(getTotal(null, 'percentage'))"
            >
              {{ getTotal(null, 'percentage') + '%' }}
            </v-chip>
          </td>
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
      {text: 'Оператор', value: 'calories', sortable: false},
      {text: 'Заявка(К)', value: 'fat', sortable: false},
      {text: 'Заявка(КС)', value: 'fat', sortable: false},
      {text: 'Одобрен(К)', value: 'carbs', sortable: false},
      {text: 'Одобрен(КС)', value: 'protein', sortable: false},
      {text: 'Эффективность', value: 'actions', sortable: false, align: 'center'}
    ]

  }),
  computed: {},
  methods: {
    getTotal(field, type = null) {
      switch (type) {
        case 'percentage':
          return (
            (((parseInt(this.getSum('ApprovedCreditCount')) || 0)
                + (parseInt(this.getSum('ApprovedCreditSaleCount')) || 0))
              / ((parseInt(this.getSum('CreditCount')) || 0)
                + (parseInt(this.getSum('CreditSaleCount')) || 0))) * 100).toFixed(2)
        default:
          return 0
      }
    },
    getColor(value) {
      if (value >= 50) {
        return 'success'
      } else return 'error'
    },

    getSumShowroom(field, showroomId) {
      return sumBy(this.data, (item) => item.showroom_id === showroomId ? parseInt(item[field]) : 0);
    },
    getSum(field) {
      return sumBy(this.data, function (item) {
        return parseInt(item[field]);
      });
    },
    getPercentage(item, isHeader = false) {
      if (isHeader) {
        const value = (
          (((
              (this.getSumShowroom('ApprovedCreditCount', item.showroom_id)
                + this.getSumShowroom('ApprovedCreditSaleCount', item.showroom_id))
              / (this.getSumShowroom('CreditCount', item.showroom_id)
                + this.getSumShowroom('CreditSaleCount', item.showroom_id))
            )
            * 100) || 0).toFixed(2)
        )
        if (value % 1 === 0.00) {
          return Math.floor(value); // Remove the decimal part
        } else return value
      }

      const val = ((((
            (((parseInt(item.ApprovedCreditSaleCount) || 0)
              + (parseInt(item.ApprovedCreditCount) || 0))) / (
              (parseInt(item.CreditSaleCount) || 0)
              + (parseInt(item.CreditCount) || 0)
            ))
          * 100) || 0).toFixed(2)
      )

      if (val % 1 === 0.00) {
        return Math.floor(val); // Remove the decimal part
      } else return val
    },
    getLink(item, type) {
      switch (type) {
        case 'CreditCount':
          return 'https://' + window.location.hostname + '/crm/' + this.$route.params?.id + '/orders?operator_id=' + item?.id + '&creditCount=true&from=' + this.$moment(this.date_from, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') + '&to=' + this.$moment(this.date_to, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss')
        case 'ApprovedCreditCount':
          return 'https://' + window.location.hostname + '/crm/' + this.$route.params?.id + '/orders?operator_id=' + item?.id + '&creditCount=true&from=' + this.$moment(this.date_from, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') + '&to=' + this.$moment(this.date_to, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss')
        default:

      }
    },
  }
}

</script>

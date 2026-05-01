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
        <template v-for="(group) in groupedData()">
          <tr class="row-head">
            <td class="font-weight-bold text-center">{{ group?.showroom }}</td>
            <td class="font-weight-bold">Все</td>
            <td class="font-weight-bold">{{ getSumShowroom('CreditCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('CreditSaleCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('ApprovedCreditCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('ApprovedCreditSaleCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold text-center" >
              <v-chip
                class="ma-2 white--text"
                :color="getColor(getPercentage(group, true))"
              >
                {{ getPercentage(group, true) + '%' }}
              </v-chip>
            </td>
          </tr>
          <tr
            v-for="(item) in data.filter(y=>y.showroom_id === group.showroom_id)"
            :key="item.id">
            <td></td>
            <td>{{ item.name }}</td>
            <td>{{ item.CreditCount }}</td>
            <td>{{ item.CreditSaleCount }}</td>
            <td>{{ item.ApprovedCreditCount }}</td>
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
        </template>
        <tr class="row-head">
          <td colspan="2">Итого</td>
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
import {reduce, sortBy} from "lodash";

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
        text: 'Шоурум',
        align: 'center',
        sortable: false,
        colspan: 2,
        value: 'name'
      },
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

    groupedData() {
      const order = [4, 2, 10, 5, 8, 15, 7, 14, 13, 17];
      const uniqueData = Object.values(
        this.data.reduce((result, item) => {
          const showroomId = item.showroom_id;
          if (!result[showroomId]) {
            result[showroomId] = item;
          }
          return result;
        }, {})
      );
      const orderMap = reduce(order, (map, id, index) => {
        map[id] = index;
        return map;
      }, {});
      return sortBy(uniqueData, item => orderMap[item.showroom_id]);
    },
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
      const sum = _.sumBy(this.data, (item) => item.showroom_id === showroomId ? parseInt(item[field]) : 0);
      return sum;
    },
    getSum(field) {
      return sumBy(this.data, function (item) {
        return parseInt(item[field]);
      });
    },
    getPercentage(item, isHeader = false) {
      if (isHeader) {
        let value =  (
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

      let val =  ((((
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
    }
  }
}

</script>

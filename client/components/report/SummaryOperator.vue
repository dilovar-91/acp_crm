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

            <td class="font-weight-bold">{{ getSumShowroom('NewCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('OnProccessCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('NoAnswerCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('ApprovedCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('WillArriveCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('TrashCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('RetryCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('ArrivedCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('SellCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('BreakCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('NewRegionCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('UndefindedCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('CashCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('CreditCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('CreditSaleCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('LisingCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold text-center">
              <v-chip
                class="ma-2 white--text"
                :color="getColor(getEfficiency(group, true))"
              >
                {{ getEfficiency(group, true) }}
              </v-chip>
            </td>
          </tr>
          <tr
            v-for="(item) in data.filter(y=>y.showroom_id === group.showroom_id)"
            :key="item.id">
            <td></td>
            <td>{{ item.name }}</td>
            <td class="font-weight-bold">{{ item.NewCount }}</td>
            <td class="font-weight-bold">{{ item.OnProccessCount }}</td>
            <td class="font-weight-bold">{{ item.NoAnswerCount }}</td>
            <td class="font-weight-bold">{{ item.ApprovedCount  }}</td>
            <td class="font-weight-bold">{{ item.WillArriveCount  }}</td>
            <td class="font-weight-bold">{{ item.TrashCount  }}</td>
            <td class="font-weight-bold">{{ item.RetryCount  }}</td>
            <td class="font-weight-bold">{{ item.ArrivedCount }}</td>
            <td class="font-weight-bold">{{ item.SellCount  }}</td>
            <td class="font-weight-bold">{{ item.BreakCount }}</td>
            <td class="font-weight-bold">{{ item.NewRegionCount }}</td>
            <td class="font-weight-bold">{{ item.UndefindedCount  }}</td>
            <td class="font-weight-bold">{{ item.CashCount  }}</td>
            <td class="font-weight-bold">{{ item.CreditCount  }}</td>
            <td class="font-weight-bold">{{ item.CreditSaleCount  }}</td>
            <td class="font-weight-bold">{{ item.LisingCount }}</td>
            <td class="font-weight-bold text-center">
              <v-chip
                class="ma-2 white--text"
                :color="getColor(getEfficiency(item, false))"
              >
                {{ getEfficiency(item, false) }}
              </v-chip>
            </td>

          </tr>
        </template>
        <tr class="row-head">
          <td colspan="2">Итого</td>

          <td class="font-weight-bold">{{ getSum('NewCount') }}</td>
          <td class="font-weight-bold">{{ getSum('OnProccessCount') }}</td>
          <td class="font-weight-bold">{{ getSum('NoAnswerCount') }}</td>
          <td class="font-weight-bold">{{ getSum('ApprovedCount') }}</td>
          <td class="font-weight-bold">{{ getSum('WillArriveCount') }}</td>
          <td class="font-weight-bold">{{ getSum('TrashCount') }}</td>
          <td class="font-weight-bold">{{ getSum('RetryCount') }}</td>
          <td class="font-weight-bold">{{ getSum('ArrivedCount') }}</td>
          <td class="font-weight-bold">{{ getSum('SellCount') }}</td>
          <td class="font-weight-bold">{{ getSum('BreakCount') }}</td>
          <td class="font-weight-bold">{{ getSum('NewRegionCount') }}</td>
          <td class="font-weight-bold">{{ getSum('UndefindedCount') }}</td>
          <td class="font-weight-bold">{{ getSum('CashCount') }}</td>
          <td class="font-weight-bold">{{ getSum('CreditCount') }}</td>
          <td class="font-weight-bold">{{ getSum('CreditSaleCount') }}</td>
          <td class="font-weight-bold">{{ getSum('LisingCount') }}</td>

          <td class="text-center">
            <v-chip
              class="ma-2 white--text"
              :color="getColor(getTotal(null, 'efficiency'))"
            >
              {{ getTotal(null, 'efficiency') }}
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
      {text: 'Не определено', value: 'actions', sortable: false},
      {text: 'Наличка', value: 'actions', sortable: false},
      {text: 'Кредит', value: 'actions', sortable: false},
      {text: 'Кредит(C)', value: 'actions', sortable: false},
      {text: 'Лизинг', value: 'actions', sortable: false},
      {text: 'Эффективность', value: 'actions', sortable: false},
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
        case 'efficiency':return ((
          ((parseInt(this.getSum('WillArriveCount')) || 0)
            + (parseInt(this.getSum('ArrivedCount')) || 0)
            + (parseInt(this.getSum('SellCount')) || 0)
            + (parseInt(this.getSum('BreakCount')) || 0))
          * 100) /
          ((parseInt(this.getSum('OnProccessCount')) || 0)
            + (parseInt(this.getSum('NoAnswerCount')) || 0)
            + (parseInt(this.getSum('ApprovedCount')) || 0)
            + (parseInt(this.getSum('TrashCount')) || 0)
            + (parseInt(this.getSum('WillArriveCount')) || 0)
            + (parseInt(this.getSum('ArrivedCount')) || 0)
            + (parseInt(this.getSum('SellCount')) || 0)
            + (parseInt(this.getSum('BreakCount')) || 0))).toFixed(2) + '%'
        default:
          return 0
      }
    },
    getColor(value) {
      if (parseInt(value) >= 50) {
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
    getEfficiency(item, isHeader = false) {
      if (isHeader) {
        return (((
          this.getSumShowroom('WillArriveCount', item.showroom_id) +
          this.getSumShowroom('ArrivedCount', item.showroom_id) +
          this.getSumShowroom('SellCount', item.showroom_id) +
          this.getSumShowroom('BreakCount', item.showroom_id))
          * 100)
          / ((this.getSumShowroom('OnProccessCount', item.showroom_id) +
            this.getSumShowroom('NoAnswerCount', item.showroom_id) +
            this.getSumShowroom('ApprovedCount', item.showroom_id) +
            this.getSumShowroom('WillArriveCount', item.showroom_id) +
            this.getSumShowroom('TrashCount', item.showroom_id) +
            this.getSumShowroom('ArrivedCount', item.showroom_id) +
            this.getSumShowroom('SellCount', item.showroom_id) +
            this.getSumShowroom('BreakCount', item.showroom_id))
            || 1)).toFixed(2) + '%'
      } else {

        return (((
          (parseInt(item.WillArriveCount) || 0)
          + (parseInt(item.ArrivedCount) || 0)
          + (parseInt(item.SellCount) || 0)
          + (parseInt(item.BreakCount) || 0))
          * 100)
          / (((parseInt(item.OnProccessCount) || 0)
            + (parseInt(item.NoAnswerCount) || 0)
            + (parseInt(item.ApprovedCount) || 0)
            + (parseInt(item.WillArriveCount) || 0)
            + (parseInt(item.TrashCount) || 0)
            + (parseInt(item.ArrivedCount) || 0)
            + (parseInt(item.SellCount) || 0)
            +(parseInt(item.BreakCount) || 0)) || 1)).toFixed(2) + '%'

      }
    }
  }
}

</script>

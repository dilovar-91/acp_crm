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
            Отчеты сливы <span v-if="date_from && date_to">с {{ date_from }} по {{ date_to }}</span>
          </v-toolbar-title>
          <v-spacer/>
        </v-toolbar>


      </template>
      <template #body>


        <tbody>
        <template v-for="(group) in groupedData()">
          <tr class="row-head">
            <td class="font-weight-bold">{{ group?.showroom }}</td>
            <td class="font-weight-bold">Все</td>
            <td class="font-weight-bold">{{ getSumShowroom('ReviewCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('ChangedPaymentCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('NotLeaveJobCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('FoundCheapCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('DontBelieveCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('GoDealerCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('AlreadyBoughtCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('BoughtHistoryCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('ChangedMindCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('FamilySituationCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('NoDialingCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('NoMoneyCount', group?.showroom_id) }}</td>
            <td class="font-weight-bold">{{ getSumShowroom('total', group?.showroom_id) }}</td>


          </tr>
          <tr
            v-for="(item) in data.filter(y=>y.showroom_id === group.showroom_id)"
            :key="item.id">
            <td></td>
            <td>{{ item.title }}</td>
            <td>{{ item.ReviewCount }}</td>
            <td>{{ item.ChangedPaymentCount }}</td>
            <td>{{ item.NotLeaveJobCount }}</td>
            <td>{{ item.FoundCheapCount }}</td>
            <td>{{ item.DontBelieveCount }}</td>
            <td>{{ item.GoDealerCount }}</td>
            <td>{{ item.AlreadyBoughtCount }}</td>
            <td>{{ item.BoughtHistoryCount }}</td>
            <td>{{ item.ChangedMindCount }}</td>
            <td>{{ item.FamilySituationCount }}</td>
            <td>{{ item.NoMoneyCount }}</td>
            <td>{{ item.NoDialingCount }}</td>
            <td>{{ item.total }}</td>
          </tr>

        </template>


        <tr class="row-head">
          <td colspan="2">Итого</td>
          <td>{{ getSum('ReviewCount') }}</td>
          <td>{{ getSum('ChangedPaymentCount') }}</td>
          <td>{{ getSum('NotLeaveJobCount') }}</td>
          <td>{{ getSum('FoundCheapCount') }}</td>
          <td>{{ getSum('DontBelieveCount') }}</td>
          <td>{{ getSum('GoDealerCount') }}</td>
          <td>{{ getSum('AlreadyBoughtCount') }}</td>
          <td>{{ getSum('BoughtHistoryCount') }}</td>
          <td>{{ getSum('ChangedMindCount') }}</td>
          <td>{{ getSum('FamilySituationCount') }}</td>
          <td>{{ getSum('NoMoneyCount') }}</td>
          <td>{{ getSum('NoDialingCount') }}</td>
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
        text: 'Салон',
        align: 'start',
        sortable: false,
        value: 'name'
      },
      {text: 'Сайт', value: 'calories', sortable: false},
      {text: 'Отзывы', value: 'calories', sortable: false},
      {text: 'Изменился способ оплаты ', value: 'fat', sortable: false},
      {text: 'Не отпустили с работы', value: 'fat', sortable: false},
      {text: 'Нашел дешевле', value: 'carbs', sortable: false},
      {text: 'Не верит (Цена / Наличие)', value: 'protein', sortable: false},
      {text: 'Поедет к дилеру ', value: 'actions', sortable: false},
      {text: 'Уже купил', value: 'actions', sortable: false},
      {text: 'Купил автотеку', value: 'actions', sortable: false},
      {text: 'Передумал покупать', value: 'actions', sortable: false},
      {text: 'Семейные обстоятельства ', value: 'actions', sortable: false},
      {text: 'Нет денег ', value: 'actions', sortable: false},
      {text: 'Не дозвон ', value: 'actions', sortable: false},
      {text: 'Всего', value: 'actions', sortable: false}
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
        case 'efficiency':
          const total = sumBy(this.data, function (item) {
            return item.total;
          });
          //return ((((this.getSum('OnProccessCount') * 100) / total) || 0) + (((this.getSum('WillArriveCount') * 100) / total) || 0) + (((this.getSum('NotArrivedCount') * 100) / total) || 0)).toFixed(2) + '%'
          return ((
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
        case 'percentage':
         return (
            (((parseInt(this.getSum('ApprovedCreditCount')) || 0)
              + (parseInt(this.getSum('ApprovedCreditSaleCount')) || 0))
              / ((parseInt(this.getSum('CreditCount')) || 0)
              + (parseInt(this.getSum('CreditSaleCount')) || 0))) * 100).toFixed(2) + '%'
        default:
          return this.getSum('NewCount')
      }
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
    getEfficiencyTest(item) {
      return "   (" + ((this.getSumShowroom('WillArriveCount', item.showroom_id)
        + this.getSumShowroom('ArrivedCount', item.showroom_id) +
        this.getSumShowroom('SellCount', item.showroom_id) +
        this.getSumShowroom('BreakCount', item.showroom_id)) || 1) +
        "*100) /" +
        (this.getSumShowroom('OnProccessCount', item.showroom_id)
          + this.getSumShowroom('NoAnswerCount', item.showroom_id)
          + this.getSumShowroom('ApprovedCount', item.showroom_id)
          + this.getSumShowroom('WillArriveCount', item.showroom_id)
          + this.getSumShowroom('TrashCount', item.showroom_id)
          + this.getSumShowroom('ArrivedCount', item.showroom_id)
          + this.getSumShowroom('SellCount', item.showroom_id)
          + this.getSumShowroom('BreakCount', item.showroom_id))
    },
    getEfficiency(item, isHeader = false) {
      if (isHeader) {
        console.log(this.getSumShowroom('total', item.showroom_id))
        return (
          (((
            this.getSumShowroom('WillArriveCount', item.showroom_id)
            + this.getSumShowroom('ArrivedCount', item.showroom_id)
            + this.getSumShowroom('SellCount', item.showroom_id)
            + this.getSumShowroom('BreakCount', item.showroom_id))
            * 100
            )
            / (this.getSumShowroom('OnProccessCount', item.showroom_id)
              + this.getSumShowroom('NoAnswerCount', item.showroom_id)
              + this.getSumShowroom('ApprovedCount', item.showroom_id)
              + this.getSumShowroom('TrashCount', item.showroom_id)
              + this.getSumShowroom('WillArriveCount', item.showroom_id)
              + this.getSumShowroom('ArrivedCount', item.showroom_id)
              + this.getSumShowroom('SellCount', item.showroom_id)
              + this.getSumShowroom('BreakCount', item.showroom_id)) || 1)).toFixed(2) + '%'
      }
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
    },
    getPercentage(item, isHeader = false) {
      if (isHeader) {
        console.log(this.getSumShowroom('total', item.showroom_id))
        return (
          ((((
            (this.getSumShowroom('ApprovedCreditCount', item.showroom_id)
            + this.getSumShowroom('ApprovedCreditSaleCount', item.showroom_id))
            / (this.getSumShowroom('CreditCount', item.showroom_id)
              + this.getSumShowroom('CreditSaleCount', item.showroom_id))
            ) || 1)
            * 100)).toFixed(2) + '%'
        )
      }

      return ((((
        (((parseInt(item.ApprovedCreditSaleCount) || 0)
        + (parseInt(item.ApprovedCreditCount) || 0))) / (
          (parseInt(item.CreditSaleCount) || 0)
          + (parseInt(item.CreditCount) || 0)
        )) || 1)
        * 100).toFixed(2) + '%'
      )
    }
  }
}

</script>

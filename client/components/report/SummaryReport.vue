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
            Отчеты по агенствам <span v-if="date_from && date_to">с {{ date_from }} по {{ date_to }}</span>
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
            <td class="font-weight-bold"><a class="link-header" target="_blank" :href="getLink(group, 1)">{{
                getSumShowroom('NewCount', group?.showroom_id)
              }}</a></td>
            <td class="font-weight-bold"><a class="link-header" target="_blank" :href="getLink(group, 2)">{{
                getSumShowroom('OnProccessCount', group?.showroom_id)
              }}</a></td>
            <td class="font-weight-bold"><a class="link-header" target="_blank" :href="getLink(group, 3)">{{
                getSumShowroom('NoAnswerCount', group?.showroom_id)
              }}</a></td>
            <td class="font-weight-bold"><a class="link-header" target="_blank" :href="getLink(group, 4)">{{
                getSumShowroom('ApprovedCount', group?.showroom_id)
              }}</a></td>
            <td class="font-weight-bold"><a class="link-header" target="_blank" :href="getLink(group, 12)">{{
                getSumShowroom('ApprovalCount', group?.showroom_id)
              }}</a></td>
            <td class="font-weight-bold"><a class="link-header" target="_blank" :href="getLink(group, 5)">{{
                getSumShowroom('WillArriveCount', group?.showroom_id)
              }}</a></td>
            <td class="font-weight-bold"><a class="link-header" target="_blank" :href="getLink(group, 7)">{{
                getSumShowroom('TrashCount', group?.showroom_id)
              }}</a></td>
            <td class="font-weight-bold"><a class="link-header" target="_blank" :href="getLink(group, 15)">{{
                getSumShowroom('UnliquidCount', group?.showroom_id)
              }}</a></td>

            <td class="font-weight-bold"><a class="link-header" target="_blank" :href="getLink(group, 6)">{{
                getSumShowroom('ArrivedCount', group?.showroom_id)
              }}</a></td>

            <td class="font-weight-bold"><a class="link-header" target="_blank" :href="getLink(group, null, null, 7)">{{
                getSumShowroom('UndefindedCount', group?.showroom_id)
              }}</a></td>
            <td class="font-weight-bold"><a class="link-header" target="_blank" :href="getLink(group, 11, null, 1)">{{
                getSumShowroom('CashCount', group?.showroom_id)
              }}</a></td>
            <td class="font-weight-bold"><a class="link-header" target="_blank" :href="getLink(group, 11, null, 2)">{{
                getSumShowroom('CreditCount', group?.showroom_id)
              }}</a></td>

            <td class="font-weight-bold"><a class="link-header" target="_blank" :href="getLink(group, 11, null, 2, 1)">{{
                getSumShowroom('ApprovedCreditCount', group?.showroom_id)
              }}</a></td>
            <td class="font-weight-bold"><a class="link-header" target="_blank" :href="getLink(group, 11, null, 3)">{{
                getSumShowroom('LisingCount', group?.showroom_id)
              }}</a></td>
            <td class="font-weight-bold"><a class="link-header" target="_blank" :href="getLink(group, 8)">{{
                getSumShowroom('RetryCount', group?.showroom_id)
              }}</a></td>
            <td class="font-weight-bold"><a class="link-header" target="_blank" :href="getLink(group, 16)">{{
                getSumShowroom('RetryDiCount', group?.showroom_id)
              }}</a></td>
            <td class="font-weight-bold">{{ getPercentage(group, true) }}</td>
            <td class="font-weight-bold">{{ getEfficiency(group, true) }}</td>
            <td class="font-weight-bold"><a class="link-header" target="_blank" :href="getLink(group, null, null)">{{
                getSumShowroom('total', group?.showroom_id)
              }}</a></td>
          </tr>
          <tr
            v-for="(item) in data.filter(y=>y.showroom_id === group.showroom_id)"
            :key="item.id">
            <td></td>
            <td>{{ item.name }}</td>
            <td><a class="link" target="_blank" :href="getLink(item, 1, item.agency_id)">{{ item.NewCount }}</a></td>
            <td><a class="link" target="_blank" :href="getLink(item, 2, item.agency_id)">{{ item.OnProccessCount }}</a>
            </td>
            <td><a class="link" target="_blank" :href="getLink(item, 3, item.agency_id)">{{ item.NoAnswerCount }}</a>
            </td>
            <td><a class="link" target="_blank" :href="getLink(item, 4, item.agency_id)">{{ item.ApprovedCount }}</a>
            </td>
            <td><a class="link" target="_blank" :href="getLink(item, 12, item.agency_id)">{{ item.ApprovalCount }}</a>
            </td>
            <td><a class="link" target="_blank" :href="getLink(item, 5, item.agency_id)">{{ item.WillArriveCount }}</a>
            </td>
            <td><a class="link" target="_blank" :href="getLink(item, 7, item.agency_id)">{{ item.TrashCount }}</a></td>
            <td><a class="link" target="_blank" :href="getLink(item, 15, item.agency_id)">{{ item.UnliquidCount }}</a>
            </td>

            <td><a class="link" target="_blank" :href="getLink(item, 6, item.agency_id)">{{ item.ArrivedCount }}</a>
            </td>


            <td><a class="link" target="_blank" :href="getLink(item, null, item.agency_id, 7 )">{{
                item.UndefindedCount
              }}</a></td>
            <td><a class="link" target="_blank" :href="getLink(item, null, item.agency_id, 1)">{{ item.CashCount }}</a>
            </td>
            <td><a class="link" target="_blank" :href="getLink(item, null, item.agency_id, 2)">{{
                item.CreditCount
              }}</a></td>

            <td><a class="link" target="_blank"
                   :href="getLink(item, null, item.agency_id, 2, 1)">{{ item.ApprovedCreditCount }}</a></td>

            <td><a class="link" target="_blank" :href="getLink(item, null, item.agency_id, 4)">{{
                item.LisingCount
              }}</a></td>
            <td><a class="link" target="_blank" :href="getLink(item, 8, item.agency_id)">{{ item.RetryCount }}</a></td>
            <td><a class="link" target="_blank" :href="getLink(item, 16, item.agency_id)">{{ item.RetryDiCount }}</a></td>
            <td>{{ getPercentage(item) }}</td>
            <td>{{ getEfficiency(item) }}</td>
            <td><a class="link" target="_blank" :href="getLink(item, null, item.agency_id)">{{ item.total }}</a></td>
          </tr>

        </template>


        <tr class="row-head">
          <td colspan="2">Итого</td>
          <td>{{ getSum('NewCount') }}</td>
          <td>{{ getSum('OnProccessCount') }}</td>
          <td>{{ getSum('NoAnswerCount') }}</td>
          <td>{{ getSum('ApprovedCount') }}</td>
          <td>{{ getSum('ApprovalCount') }}</td>
          <td>{{ getSum('WillArriveCount') }}</td>
          <td>{{ getSum('TrashCount') }}</td>
          <td>{{ getSum('UnliquidCount') }}</td>
          <td>{{ getSum('ArrivedCount') }}</td>
          <td>{{ getSum('UndefindedCount') }}</td>
          <td>{{ getSum('CashCount') }}</td>
          <td>{{ getSum('CreditCount') }}</td>
          <td>{{ getSum('ApprovedCreditCount') }}</td>
          <td>{{ getSum('LisingCount') }}</td>
          <td>{{ getSum('RetryCount') }}</td>
          <td>{{ getSum('RetryDiCount') }}</td>
          <td>{{ getTotal(null, 'percentage') }}</td>
          <td>{{ getTotal(null, 'efficiency') }}</td>
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

.link {
  color: #000000;
  text-decoration-color: #000000;
  font-weight: 400;
}

.link-header {
  color: #000000;
  text-decoration-color: #000000;
  font-weight: 600;
}
</style>
<script>
import sumBy from 'lodash.sumby'
import {sortBy, reduce} from 'lodash'

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
      {text: 'Агенство', value: 'calories', sortable: false},
      {text: 'Новая', value: 'calories', sortable: false},
      {text: 'В работе', value: 'fat', sortable: false},
      {text: 'Не отвечает', value: 'fat', sortable: false},
      {text: 'Одобрить', value: 'carbs', sortable: false},
      {text: 'Одобренный', value: 'carbs', sortable: false},
      {text: 'Приедет', value: 'carbs', sortable: false},
      {text: 'Корзина', value: 'protein', sortable: false},
      {text: 'Не ликвид', value: 'protein', sortable: false},
      {text: 'Приехал', value: 'actions', sortable: false},
      {text: 'Не определено', value: 'actions', sortable: false},
      {text: 'Наличка', value: 'actions', sortable: false},
      {text: 'Кредит', value: 'actions', sortable: false},
      {text: 'Одобрен(К)', value: 'actions', sortable: false},
      {text: 'Лизинг', value: 'actions', sortable: false},
      {text: 'Повтор', value: 'actions', sortable: false},
      {text: 'Повтор ДИ', value: 'actions', sortable: false},
      {text: '% одобрения', value: 'actions', sortable: false},
      {text: 'Эффективность', value: 'actions', sortable: false},
      {text: 'Всего', value: 'actions', sortable: false}
    ]

  }),
  computed: {},
  methods: {

    getLink(item, status = null, agency_id = null, payment_id = null, approved = null) {
      let status_query = ''
      let payment_query = ''
      let agency_query = ''
      let approved_query = ''
      if (status) {
        status_query = 'status=' + status
      }
      if (payment_id) {
        payment_query = '&payment_method=' + payment_id
      }
      if (agency_id) {
        agency_query = '&agency_id=' + agency_id
      }
      if (approved) {
        approved_query = '&approved=' + approved
      }
      return 'https://' + window.location.hostname + '/crm/' + item.showroom_id + '/orders?' + status_query + agency_query + payment_query + approved_query + '&from=' + this.$moment(this.date_from, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') + '&to=' + this.$moment(this.date_to, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss')
    },
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
          return ((
              ((parseInt(this.getSumEfficiency('WillArriveCount')) || 0)
                + (parseInt(this.getSumEfficiency('ArrivedCount')) || 0))
              * 100) /
            ((parseInt(this.getSumEfficiency('OnProccessCount')) || 0)
              + (parseInt(this.getSumEfficiency('NoAnswerCount')) || 0)
              + (parseInt(this.getSumEfficiency('ApprovedCount')) || 0)
              + (parseInt(this.getSumEfficiency('TrashCount')) || 0)
              + (parseInt(this.getSumEfficiency('WillArriveCount')) || 0)
              + (parseInt(this.getSumEfficiency('ArrivedCount')) || 0))).toFixed(2) + '%'
        case 'percentage':
          return (
            (((parseInt(this.getSum('ApprovedCreditCount')) || 0))
              / ((parseInt(this.getSum('CreditCount')) || 0))) * 100).toFixed(2) + '%'
        default:
          return this.getSum('NewCount')
      }
    },

    getSumShowroom(field, showroomId) {
      return sumBy(this.data, (item) => item.showroom_id === showroomId ? parseInt(item[field]) : 0);
    },

    getSumShowroomHeader(field, showroomId) {
      const data = this.data.filter(y => y.agency_id !== 4)
      return sumBy(data, (item) => item.showroom_id === showroomId ? parseInt(item[field]) : 0);
    },
    getSum(field) {
      return sumBy(this.data, function (item) {
        return parseInt(item[field]);
      });
    },
    getSumEfficiency(field) {
      const data = this.data.filter(y => y.agency_id !== 4)
      return sumBy(data, function (item) {
        return parseInt(item[field]);
      });
    },

    getEfficiency(item, isHeader = false) {
      if (isHeader) {
        return (
          (((
                this.getSumShowroomHeader('WillArriveCount', item.showroom_id)
                + this.getSumShowroomHeader('ArrivedCount', item.showroom_id))
              * 100
            )
            / (this.getSumShowroomHeader('OnProccessCount', item.showroom_id)
              + this.getSumShowroomHeader('NoAnswerCount', item.showroom_id)
              + this.getSumShowroomHeader('ApprovedCount', item.showroom_id)
              + this.getSumShowroomHeader('TrashCount', item.showroom_id)
              + this.getSumShowroomHeader('WillArriveCount', item.showroom_id)
              + this.getSumShowroomHeader('ArrivedCount', item.showroom_id)) || 1)).toFixed(2) + '%'
      }
      return (((
            (parseInt(item.WillArriveCount) || 0)
            + (parseInt(item.ArrivedCount) || 0))
          * 100)
        / (((parseInt(item.OnProccessCount) || 0)
          + (parseInt(item.NoAnswerCount) || 0)
          + (parseInt(item.ApprovedCount) || 0)
          + (parseInt(item.WillArriveCount) || 0)
          + (parseInt(item.UnliquidCount) || 0)
          + (parseInt(item.ArrivedCount) || 0)) || 1)).toFixed(2) + '%'
    },
    getPercentage(item, isHeader = false) {
      if (isHeader) {
        console.log(this.getSumShowroom('total', item.showroom_id))
        return (
          ((((
              (this.getSumShowroom('ApprovedCreditCount', item.showroom_id))
              / (this.getSumShowroom('CreditCount', item.showroom_id))
            ) || 0)
            * 100)).toFixed(2) + '%'
        )
      }

      return ((((
            (( (parseInt(item.ApprovedCreditCount) || 0))) / (
              (parseInt(item.CreditCount) || 0)
            )) || 0)
          * 100).toFixed(2) + '%'
      )
    }
  }
}

</script>

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
            Отчеты по приездам <span v-if="date_from && date_to">с {{ date_from }} по {{ date_to }}</span>
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
            <td class="font-weight-bold">{{ getSumShowroom('WillArriveCount', group?.showroom_id) }}</td>


          </tr>
          <tr
            v-for="(item) in data.filter(y=>y.showroom_id === group.showroom_id)"
            :key="item.id">
            <td></td>
            <td>{{ item.name }}</td>
            <td>{{ item.WillArriveCount }} </td>
          </tr>

        </template>
        <tr class="row-head">
          <td colspan="2">Итого</td>
          <td>{{ getSum('WillArriveCount') }}</td>
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
.row-head{
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
      {text: 'Агенство', value: 'calories', sortable: false},
      {text: 'Приедет', value: 'calories', sortable: false}
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
          return ((((parseInt(this.getSum('WillArriveCount')) || 0) + (parseInt(this.getSum('ArrivedCount')) || 0)) * 100) / (total - (parseInt(this.getSum('RetryCount')) || 0) - (parseInt(this.getSum('NewCount')) || 0) - (parseInt(this.getSum('NoAnswerCount')) || 0))).toFixed(2) + '%'
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
    getEfficiency(item) {
      return ((((parseInt(item.WillArriveCount) || 0) + (parseInt(item.ArrivedCount) || 0)) * 100) / ((parseInt(item.total) - (parseInt(this.getSum('RetryCount')) || 0) - (parseInt(item.NewCount) || 0) - (parseInt(item.NoAnswerCount) || 0)) || 1)).toFixed(2) + '%'
      //return ((((parseInt(item.OnProccessCount) * 100) / item.total) || 0) + (((parseInt(item.WillArriveCount) * 100) / item.total) || 0) + (((parseInt(item.NotArrivedCount) * 100) / item.total) || 0)).toFixed(2) + '%'
    }
  }
}

</script>

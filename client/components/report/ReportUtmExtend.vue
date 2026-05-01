<template>
  <v-data-table
    v-if="data"
    :headers="headers"
    :items="data"
    group-by="utm_campaign"
    class="elevation-1"
    :items-per-page="data.length"
    sort-by="name"
    :hide-default-footer="true"
  >
    <template #top>
      <v-toolbar flat>
        <v-toolbar-title class="mr-3">
          Отчеты по UTM меткам (Расширенный) <span v-if="date_from && date_to">с {{ date_from }} по {{ date_to }}</span> <span
          class="text-right"> <v-btn icon color="green" @click="exportFile()">
                <v-icon large>mdi-file-excel</v-icon>
              </v-btn></span>
        </v-toolbar-title>
        <v-spacer/>
      </v-toolbar>
    </template>

    <template slot="body.append">
      <tr>
        <th colspan="3" class="text-center">Всего</th>
        <th>{{getSum('NewCount')}}</th>
        <th>{{getSum('OnProccessCount')}}</th>
        <th>{{getSum('NoAnswerCount')}}</th>
        <th>{{getSum('ApprovedCount')}}</th>
        <th>{{getSum('WillArriveCount')}}</th>
        <th>{{getSum('TrashCount')}}</th>
        <th>{{getSum('ArrivedCount')}}</th>
        <th>{{getSum('SellCount')}}</th>
        <th>{{getSum('BreakCount')}}</th>
        <th>{{getSum('NewRegionCount')}}</th>
        <th>{{getSum('order_counts')}}</th>
      </tr>
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
        text: 'Utm Campaign',
        align: 'start',
        sortable: false,
        value: 'utm_campaign'
      },
      {
        text: 'Utm Content',
        align: 'start',
        sortable: false,
        value: 'utm_content',
        groupable: false
      },
      {text: 'Utm Source', value: 'utm_source', sortable: false, groupable: false},
      {text: 'Источник', value: 'title', sortable: false, groupable: false},
      {text: 'Новая', value: 'NewCount', sortable: false, groupable: false}, ,
      {text: 'В работе', value: 'OnProccessCount', sortable: false, groupable: false},
      {text: 'Не отвечает', value: 'NoAnswerCount', sortable: false, groupable: false},
      {text: 'Одобрить', value: 'ApprovedCount', sortable: false, groupable: false},
      {text: 'Приедет', value: 'WillArriveCount', sortable: false, groupable: false},
      {text: 'Корзина', value: 'TrashCount', sortable: false, groupable: false},
      {text: 'Приехал', value: 'ArrivedCount', sortable: false, groupable: false},
      {text: 'Продажа', value: 'SellCount', sortable: false, groupable: false},
      {text: 'Соскок', value: 'BreakCount', sortable: false, groupable: false},
      {text: 'ЛНР/ДНР', value: 'NewRegionCount', sortable: false, groupable: false},
      {text: 'Всего', value: 'order_counts', sortable: false, groupable: false},
    ]

  }),
  methods: {
    getSum(field) {
      return sumBy(this.data, function (item) {
        return parseInt(item[field]);
      });
    },
    exportFile() {
      const XLSX = require('xlsx');
      const wb = XLSX.utils.book_new();
      let rows = [];
      rows.push({
        'utm_campaign': "utm_campaign",
        'utm_content': "utm_content",
        'utm_source': "utm_source",
        'Новая':  'Новая',
        'В работе': 'В работе',
        'Не отвечает':  'Не отвечает',
        'Одобрить': 'Одобрить',
        'Приедет': 'Приедет',
        'Корзина': 'Корзина',
        'Приехал': 'Приехал',
        'Продажа': 'Продажа',
        'Соскок': 'Соскок',
        'Прочие': 'Прочие',
        'Итого': 'Итого'
      });
      rows = this.data.map((row) => ({
        'utm_campaign': row.utm_campaign,
        'utm_content': row.utm_content,
        'utm_source': row.utm_source,
        'Новая': parseInt(row.NewCount),
        'В работе': parseInt(row.OnProccessCount),
        'Не отвечает': parseInt(row.NoAnswerCount),
        'Одобрить': parseInt(row.ApprovedCount),
        'Приедет': parseInt(row.WillArriveCount),
        'Корзина': parseInt(row.TrashCount),
        'Приехал': parseInt(row.ArrivedCount),
        'Продажа': parseInt(row.SellCount),
        'Соскок': parseInt(row.BreakCount),
        'Прочие': parseInt(row.OtherCount),
        'Итого': row.order_counts
      }));
      rows.push({
        'utm_campaign': "",
        'utm_content': "Всего",
        'utm_source': "",
        'Новая': this.getSum('NewCount'),
        'В работе': this.getSum('OnProccessCount'),
        'Не отвечает': this.getSum('NoAnswerCount'),
        'Одобрить': this.getSum('ApprovedCount'),
        'Приедет': this.getSum('WillArriveCount'),
        'Корзина': this.getSum('ArrivedCount'),
        'Приехал': this.getSum('RetryCount'),
        'Продажа': this.getSum('SellCount'),
        'Соскок': this.getSum('BreakCount'),
        'Прочие': this.getSum('OtherCount'),
        'Итого': this.getSum('order_counts')
      });
      const ws = XLSX.utils.json_to_sheet(rows);

      ws["!cols"] = [
        {
          wch: 60, s: {
            alignment: {
              horizontal: 'center',
              vertical: 'center',
            }
          }
        },
        {
          wch: 90, s: {
            alignment: {
              horizontal: 'center',
              vertical: 'center',
            }
          }
        },
        {
          wch: 18, s: {
            alignment: {
              horizontal: 'center',
              vertical: 'center',
            }
          }
        },
        {
          wch: 13, s: {
            alignment: {
              horizontal: 'center',
              vertical: 'center',
            }
          }
        },
        {
          wch: 13, s: {
            alignment: {
              horizontal: 'center',
              vertical: 'center',
            }
          }
        },
        {
          wch: 13, s: {
            alignment: {
              horizontal: 'center',
              vertical: 'center',
            }
          }
        },
        {
          wch: 13, s: {
            alignment: {
              horizontal: 'center',
              vertical: 'center',
            }
          }
        },
        {
          wch: 13, s: {
            alignment: {
              horizontal: 'center',
              vertical: 'center',
            }
          }
        },
        {
          wch: 13, s: {
            alignment: {
              horizontal: 'center',
              vertical: 'center',
            }
          }
        },
        {
          wch: 13, s: {
            alignment: {
              horizontal: 'center',
              vertical: 'center',
            }
          }
        },
        {
          wch: 13, s: {
            alignment: {
              horizontal: 'center',
              vertical: 'center',
            }
          }
        },
        {
          wch: 13, s: {
            alignment: {
              horizontal: 'center',
              vertical: 'center',
            }
          }
        },
        {
          wch: 13, s: {
            alignment: {
              horizontal: 'center',
              vertical: 'center',
            }
          }
        },
        {
          wch: 13, s: {
            alignment: {
              horizontal: 'center',
              vertical: 'center',
            }
          }
        },
        {
          wch: 13, s: {
            alignment: {
              horizontal: 'center',
              vertical: 'center',
            }
          }
        },
      ];
      XLSX.utils.book_append_sheet(wb, ws, "Книга 1");
      const wbout = XLSX.write(wb, {bookType: 'xlsx', type: 'array'});
      saveAs(new Blob([wbout], {type: "application/octet-stream"}), "Report_utm_extend_" + this.$moment().format('DD_MM_YYYY_HH_mm_ss') + ".xlsx");
    }
  }
}

</script>

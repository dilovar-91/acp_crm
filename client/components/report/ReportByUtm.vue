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
          Отчеты по UTM Меткам <span v-if="date_from && date_to">с {{ date_from }} по {{ date_to }}</span> <span
          class="text-right"> <v-btn icon color="green" @click="exportFile()">
                <v-icon large>mdi-file-excel</v-icon>
              </v-btn></span>
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
        <td>{{ item.id }}</td>
        <td>{{ item.created_at }}</td>
        <td>{{ item.utm_source }}</td>
        <td>{{ item.utm_campaign }}</td>
        <td>{{ item.utm_medium }}</td>
        <td>{{ item.utm_content }}</td>
        <td>{{ item.title }}</td>
        <td>{{ item.name }}</td>
        <td>{{ item.trash }}</td>
      </tr>
      </tbody>
    </template>
    <template #no-data>
      Пусто
    </template>
  </v-data-table>
</template>
<script>
import {saveAs} from 'file-saver';

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
        text: '№',
        align: 'start',
        sortable: false,
        value: 'id'
      },
      {
        text: 'Дата создание',
        align: 'start',
        sortable: false,
        value: 'term'
      },
      {text: 'Source', value: 'utm_source', sortable: false},
      {text: 'Campaign', value: 'campaign', sortable: false},
      {text: 'Medium', value: 'utm_medium', sortable: false},
      {text: 'Content', value: 'utm_content', sortable: false},
      {text: 'Источник', value: 'carbs', sortable: false},
      {text: 'Статус', value: 'protein', sortable: false},
      {text: 'Подстатус', value: 'trash', sortable: false},
    ]
  }),

  methods: {
    exportFile() {
      const XLSX = require('xlsx');
      const wb = XLSX.utils.book_new();
      const rows = this.data.map((row) => ({
        '№': row.id,
        'Создано': row.created_at,
        'utm_source': row.utm_source,
        'utm_campaign': row.utm_campaign,
        'utm_medium': row.utm_medium,
        'utm_content': row.utm_content,
        'Источник': row.title,
        'Статус': row.name,
        'Подстатус': row.trash,
      }));
      const ws = XLSX.utils.json_to_sheet(rows);
      ws["!cols"] = [
        {
          wch: 16, s: {
            alignment: {
              horizontal: 'center',
              vertical: 'center',
            }
          }
        },
        {
          wch: 20, s: {
            alignment: {
              horizontal: 'center',
              vertical: 'center',
            }
          }
        },
        {
          wch: 22, s: {
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
          wch: 42, s: {
            alignment: {
              horizontal: 'center',
              vertical: 'center',
            }
          }
        },
        {
          wch: 22, s: {
            alignment: {
              horizontal: 'center',
              vertical: 'center',
            }
          }
        },
        {
          wch: 22, s: {
            alignment: {
              horizontal: 'center',
              vertical: 'center',
            }
          }
        },
        {
          wch: 14, s: {
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
      ];
      XLSX.utils.book_append_sheet(wb, ws, "Книга 1");
      const wbout = XLSX.write(wb, {bookType: 'xlsx', type: 'array'});
      saveAs(new Blob([wbout], {type: "application/octet-stream"}), "Report_" + this.$moment().format('DD_MM_YYYY_HH_mm_ss') + ".xlsx");
    },
  }
}

</script>

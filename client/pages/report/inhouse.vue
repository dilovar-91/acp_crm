<template>
  <div>
    <v-card
      class="mx-auto"
    >
      <v-toolbar
        flat
        dense
      >
        <v-toolbar-title class="mr-3">
          Отчеты
        </v-toolbar-title>

        <v-spacer/>
      </v-toolbar>
      <div class="px-2 d-flex flex-row">
        <v-sheet class="px-2">
          <v-select
            v-model="type_id"
            item-text="name"
            item-value="id"
            :items="types"
            label="Тип отчета *"
            outlined
            dense
            clearable
            hide-details
          />
        </v-sheet>

        <v-sheet class="px-2">
          <v-select
            v-model="showroom_id"
            item-text="name"
            item-value="id"
            :items="showrooms"
            label="Салон"
            outlined
            dense
            hide-details
            clearable
            @click:clear="
                          $nextTick(() => showroom_id = null)
                        "
          />
        </v-sheet>

        <v-sheet class="px-2">
          <date-picker
            v-model="date_from"
            format="DD.MM.Y HH:mm:ss"
            value-type="format"
            placeholder="Дата с"
            input-class="mx-input dtp"
            style="width: 100%;"
            type="datetime"
          />
        </v-sheet>
        <v-sheet class="px-2">
          <date-picker
            v-model="date_to"
            value-type="format"
            format="DD.MM.Y HH:mm:ss"
            type="datetime"
            placeholder="Дата до"
            input-class="mx-input dtp"
            style="width: 100%;"
          />
        </v-sheet>
      </div>
      <v-row dense class="px-2 mt-2" align="end">
        <v-col cols="12" md="3" align-self="end"/>
        <v-col cols="12" md="9" align-self="end" class="pl-2 text-right">
          <v-btn color="primary" dark class="mb-2" @click="fillDate('today')">
            Сегодня
          </v-btn>
          <v-btn color="lime" class="mb-2" @click="fillDate('tomorrow')">
            Завтра
          </v-btn>
          <v-btn color="primary" dark class="mb-2" @click="fillDate('seven')">
            19:00-19:00
          </v-btn>
          <v-btn color="primary" dark class="mb-2" @click="fillDate('yesterday')">
            Вчера
          </v-btn>
          <v-btn color="primary" dark class="mb-2" @click="fillDate('week')">
            Неделя
          </v-btn>
          <v-btn color="primary" dark class="mb-2" @click="fillDate('month')">
            Месяц
          </v-btn>
          <v-btn color="primary" dark class="mb-2" @click="fillDate('clear')">
            За все время
          </v-btn>
          <v-btn color="success" dark class="mb-2" :loading="loading" @click="fetchReport()">
            Сформировать
          </v-btn>
          <v-btn color="warning" :disabled="!report" class="mb-2 " @click="export2Excel()">
            Экспортировать
          </v-btn>
          <v-btn color="error" class="mb-2 " @click="reset()">
            Сбросить
          </v-btn>
        </v-col>
      </v-row>

      <SummaryReportAgency
        v-if="type_id===1 && reportData.length" :date_from="date_from" :date_to="date_to"
        :data="reportData"/>


      <ByAgencySites
        v-else-if="type_id===4 && reportData.length" :date_from="date_from" :date_to="date_to"
        :agency_id="agency_id"
        :showroom_id="showroom_id"
        :data="reportData"/>

    </v-card>
  </div>
</template>
<script>

import ByAgencySites from "~/components/report/ByAgencySites.vue";
import SummaryReportAgency from "~/components/report/SummaryReportAgency.vue";

export default {
  name: 'CrmReports',
  components: {
    SummaryReportAgency,
    ByAgencySites,
  },
  middleware: 'permission',
  data: () => ({
    dialog: false,
    report: false,
    loading: false,
    dialogDelete: false,
    date_from: null,
    date_to: null,
    order_type: null,
    operators: [],
    reportData: [],
    reportStatus: [],
    reportSites: [],
    reportAgency: [],
    resource: null,
    site_id: null,
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
      {text: 'Приедет', value: 'carbs', sortable: false},
      {text: 'Корзина', value: 'protein', sortable: false},
      {text: 'Повтор', value: 'actions', sortable: false},
      {text: 'Приехал', value: 'actions', sortable: false},
      /*{text: 'Долги ФССП', value: 'actions', sortable: false},
      {text: 'Не подавал', value: 'actions', sortable: false},
      {text: 'Консультация', value: 'actions', sortable: false},
      {text: 'Отказ клиента', value: 'actions', sortable: false},
      {text: 'Отказ по банкам', value: 'actions', sortable: false},*/
      {text: 'Все', value: 'actions', sortable: false},
      {text: 'Эффективность', value: 'actions', sortable: false}
    ],
    type_id: 1,
    types: [
      {
        id: 1,
        name: 'Отчёт по агенствам'
      },
      {
        id: 4,
        name: 'Отчёт по сайтам'
      },
    ]
  }),

  async fetch({store, params: {id}}) {
    await store.dispatch('showroom/fetchSites', {id})
    await store.dispatch('showroom/fetchShowrooms')
    await store.dispatch('user/toggle', false)
  },

  computed: {
    showrooms() {
      return this.$store.state.showroom.showrooms.filter(l => !([1, 6, 9].includes(l.id)))
    },

    agency_id() {
      return this.$route.params.agency
    },

    showroom_id() {
      return this.$route.params.id
    },
    perform() {
      const data = this.reportData
      const arr = []
      data.forEach((item) => {
        arr.push({
          "Оператор": item.name,
          "Новая": parseInt(item.NewCount),
          "В работе": parseInt(item.OnProccessCount),
          "Не отвечает": parseInt(item.NoAnswerCount),
          "Одобренные": parseInt(item.ApprovedCount),
          "Приедет": parseInt(item.WillArriveCount),
          "Приехал": parseInt(item.ArrivedCount),
          "Корзина": parseInt(item.TrashCount),
          "Повтор": parseInt(item.RetryCount),
          "Другие": parseInt(item.OtherCount),
          "Эффективность": ((((parseInt(item.OnProccessCount) * 100) / item.total) || 0) + (((parseInt(item.NoAnswerCount) * 100) / item.total) || 0) + (((parseInt(item.NotArrivedCount) * 100) / item.total) || 0)).toFixed(2) + '%',
          "Итого": parseInt(item.total),
        })
      })
      return arr
    },
  },

  watch: {
    order_type() {
      this.reset()
    },
  },

  methods: {
    execute() {
      if (this.type_id >= 1 && this.type_id <= 12) {
        this.report = true
      }
    },

    fillDate(interval) {
      this.report = false
      let before_time = ' 00:00:00'
      let after_time = ' 23:59:59'
      let seven_time = ' 19:00:00'
      switch (interval) {
        case 'today':
          console.log(interval)
          this.date_from = this.$moment().format('DD.MM.YYYY') + before_time
          this.date_to = this.$moment().format('DD.MM.YYYY') + after_time
          console.log(this.$moment(this.date_from, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss'))
          return
        case 'seven':
          this.date_from = this.$moment().subtract(1, 'days').format('DD.MM.YYYY') + seven_time
          this.date_to = this.$moment().format('DD.MM.YYYY') + seven_time
          return
        case 'yesterday':
          this.date_from = this.$moment().subtract(1, 'days').format('DD.MM.YYYY') + before_time
          this.date_to = this.$moment().subtract(1, 'days').format('DD.MM.YYYY') + after_time
          return
        case 'tomorrow':
          this.date_from = this.$moment().add(1, 'days').format('DD.MM.YYYY') + before_time
          this.date_to = this.$moment().add(1, 'days').format('DD.MM.YYYY') + after_time
          return
        case 'week':
          this.date_from = this.$moment().subtract(7, 'days').format('DD.MM.YYYY HH:mm:ss')
          this.date_to = this.$moment().format('DD.MM.YYYY HH:mm:ss')
          return
        case 'month':
          this.date_from = this.$moment().subtract(1, 'months').format('DD.MM.YYYY HH:mm:ss')
          this.date_to = this.$moment().format('DD.MM.YYYY HH:mm:ss')
          return
        default:
          this.date_from = null
          this.date_to = null
      }
      this.reset()
    },
    async fetchReport() {
      const loader = this.$loading.show({
        // Optional parameters
        container: null,
        canCancel: false
      });

      const endpointMap = {
        2: 'reports/arrivals',
        4: 'https://accas.ru/api/report/sites-agency',
      };
      try {
        const query = this.getData();
        const endpoint = endpointMap[this.type_id] || 'https://accas.ru/api/reports/all';
        const {data} = await this.$axios.post(endpoint, query);

        this.report = true;
        this.reportData = data;

      } catch (error) {
        this.handleError(error);
      }
      loader.hide();
    },
    handleError(error) {
      this.$nuxt.$toast.error('Произошла ошибка при формирование отчёта' + error, {
        position: 'top-right',
        timeout: 5000,
        closeOnClick: true,
        pauseOnFocusLoss: true,
        pauseOnHover: true,
        draggable: true,
        draggablePercent: 0.6,
        showCloseButtonOnHover: false,
        hideProgressBar: true,
        closeButton: 'button',
        icon: true,
        rtl: false
      });
    },
    getData() {
      return {
        type_id: this.order_type,
        showroom_id: this.showroom_id,
        site_id: this.site_id,
        agency_id: 14,
        payment_method: this.payment_method,
        from: this.date_from ? this.$moment(this.date_from, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') : null,
        to: this.date_to ? this.$moment(this.date_to, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') : null
      }
    },
    reset() {
      const loader = this.$loading.show({
        // Optional parameters
        container: null,
        canCancel: false
      });
      setTimeout(test => {
        loader.hide()
      }, 300)
      this.site_id = null
      this.agency_id = null
      this.date_from = null
      this.date_to = null
      this.report = false
      this.order_type = null
      this.payment_method = null
      this.reportData = []
      this.reportStatus = []
      this.reportSites = []
      this.reportAgency = []
    },
    clearReport() {
      const loader = this.$loading.show({
        // Optional parameters
        container: null,
        canCancel: false
      });
      setTimeout(test => {
        loader.hide()
      }, 300)
      this.site_id = null
      this.agency_id = null
      this.date_from = null
      this.date_to = null
      this.report = false
      this.order_type = null
      this.type_id = null
      this.payment_method = null
      this.reportData = []
    },
    export2Excel() {
      const XLSX = require('xlsx');
      if (this.type_id === 2) {
        const wscols = [
          {wch: 8},
          {wch: 35},
          {wch: 10}
        ];

        const Heading = [
          ["№", "Состояние заявки", "Кол-во"],
        ];
        const Footer = [
          ["", "Всего", 1190],
        ];
        const wb = XLSX.utils.book_new();
        let ws = XLSX.WorkSheet
        ws = XLSX.utils.json_to_sheet([]);
        ws['!cols'] = wscols;
        XLSX.utils.sheet_add_aoa(ws, Heading);
        XLSX.utils.sheet_add_json(ws, this.reportStatus, {origin: 'A2', skipHeader: true});
        XLSX.utils.sheet_add_aoa(ws, Footer, {origin: 'A16'});
        XLSX.utils.book_append_sheet(wb, ws, 'Отчёт по статусам');
        XLSX.writeFile(wb, 'report.xlsx');
      } else if (this.type_id === 1) {
        const wsCols = [
          {wch: 35},
          {wch: 10},
          {wch: 10},
          {wch: 10},
          {wch: 10},
          {wch: 10},
          {wch: 10},
          {wch: 10},
          {wch: 13},
          {wch: 13},
          {wch: 13},
          {wch: 13},
          {wch: 13},
          {wch: 13},
          {wch: 13},
        ];
        const Footer = [
          ["Итого"]
        ];
        const count = this.reportData.length
        const wb = XLSX.utils.book_new();
        let ws = XLSX.WorkSheet
        ws = XLSX.utils.json_to_sheet([]);
        ws['!cols'] = wsCols;
        const res = this.perform
        XLSX.utils.sheet_add_json(ws, res);
        XLSX.utils.sheet_add_aoa(ws, Footer, {origin: count + 1});
        let cell_ref = XLSX.utils.encode_cell({c: 1, r: count + 1});
        ws[cell_ref] = {f: "SUM(B2:B" + (count + 1) + ")"}
        cell_ref = XLSX.utils.encode_cell({c: 2, r: count + 1});
        ws[cell_ref] = {f: "SUM(C2:C" + (count + 1) + ")"}
        cell_ref = XLSX.utils.encode_cell({c: 3, r: count + 1});
        ws[cell_ref] = {f: "SUM(D2:D" + (count + 1) + ")"}
        cell_ref = XLSX.utils.encode_cell({c: 4, r: count + 1});
        ws[cell_ref] = {f: "SUM(E2:E" + (count + 1) + ")"}
        cell_ref = XLSX.utils.encode_cell({c: 5, r: count + 1});
        ws[cell_ref] = {f: "SUM(F2:F" + (count + 1) + ")"}
        cell_ref = XLSX.utils.encode_cell({c: 6, r: count + 1});
        ws[cell_ref] = {f: "SUM(G2:G" + (count + 1) + ")"}
        cell_ref = XLSX.utils.encode_cell({c: 7, r: count + 1});
        ws[cell_ref] = {f: "SUM(H2:H" + (count + 1) + ")"}
        cell_ref = XLSX.utils.encode_cell({c: 8, r: count + 1});
        ws[cell_ref] = {f: "SUM(I2:I" + (count + 1) + ")"}
        cell_ref = XLSX.utils.encode_cell({c: 9, r: count + 1});
        ws[cell_ref] = {f: "SUM(J2:J" + (count + 1) + ")"}
        cell_ref = XLSX.utils.encode_cell({c: 10, r: count + 1});
        ws[cell_ref] = {f: "SUM(K2:K" + (count + 1) + ")"}
        cell_ref = XLSX.utils.encode_cell({c: 11, r: count + 1});
        ws[cell_ref] = {f: "SUM(L2:L" + (count + 1) + ")"}
        cell_ref = XLSX.utils.encode_cell({c: 12, r: count + 1});
        ws[cell_ref] = {f: "SUM(M2:M" + (count + 1) + ")"}
        cell_ref = XLSX.utils.encode_cell({c: 13, r: count + 1});
        ws[cell_ref] = {f: "SUM(N2:N" + (count + 1) + ")"}
        cell_ref = XLSX.utils.encode_cell({c: 14, r: count + 1});
        const totalEffiency = ((((this.getTotal('OnProccessCount') * 100) / this.getTotal('total')) || 0) + (((this.getTotal('WillArriveCount') * 100) / this.getTotal('total')) || 0) + (((this.getTotal('NotArrivedCount') * 100) / this.getTotal('total')) || 0)).toFixed(2) + '%';
        ws[cell_ref] = {v: totalEffiency}
        cell_ref = XLSX.utils.encode_cell({c: 15, r: count + 1});
        ws[cell_ref] = {v: this.getTotal('total'), t: 'n'}
        XLSX.utils.book_append_sheet(wb, ws, 'Отчёт по операторам');
        XLSX.writeFile(wb, 'report.xlsx');
      }

    },
  },


}

</script>
<style>
.dtp {
  width: 100%;
  height: 40px !important;
  border-color: #a9a0a0 !important;
}
</style>

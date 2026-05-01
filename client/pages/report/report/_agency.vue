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
          Отчеты  ({{ showroom?.name }})  {{agency_id}}
        </v-toolbar-title>

        <v-spacer/>
      </v-toolbar>
      <v-row class="px-2 d-flex flex-row">
        <v-col md="5" cols="12" class="px-2">
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
        </v-col>
        <v-col md="3" cols="12" class="px-2">
          <v-select
            v-model="site_id"
            item-text="title"
            item-value="id"
            :items="sites"
            label="Сайт "
            outlined
            hide-details
            dense
            clearable
            @click:clear="
                          $nextTick(() => site_id = null)
                        "
          />
        </v-col>
        <v-col md="2" cols="12" class="px-2">
          <date-picker
            v-model="date_from"
            format="DD.MM.Y HH:mm:ss"
            value-type="format"
            placeholder="Дата с"
            input-class="mx-input dtp"
            style="width: 100%;"
            type="datetime"
          />
        </v-col>
        <v-col md="2" cols="12" class="px-2">
          <date-picker
            v-model="date_to"
            value-type="format"
            format="DD.MM.Y HH:mm:ss"
            type="datetime"
            placeholder="Дата до"
            input-class="mx-input dtp"
            style="width: 100%;"
          />
        </v-col>
      </v-row>
      <v-row dense class="px-2 mt-2" align="end">
        <v-col cols="12" md="5" align-self="end"/>
        <v-col cols="12" md="7" align-self="end" class="pl-4">
          <v-btn color="primary" dark class="mb-2" @click="fillDate('today')">
            Сегодня
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
          <v-btn color="error" class="mb-2 " @click="reset()">
            Сбросить
          </v-btn>
        </v-col>
      </v-row>

      <ByOperator
        v-if="type_id===1 && reportData.length" :date_from="date_from" :date_to="date_to"
        :data="reportData"/>
      <ReportByStatus
        v-if="type_id===2 && reportData.length" :date_from="date_from" :date_to="date_to"
        :data="reportData"/>
      <ByAgencySites
        v-else-if="type_id===3 && reportData.length" :date_from="date_from" :date_to="date_to"
        :data="reportData" :agency_id="agency_id"/>
      <ReportByDate
        v-else-if="type_id===4 && reportData.length" :date_from="date_from" :date_to="date_to"
        :data="reportData"/>
      <ReportBySource
        v-else-if="type_id===5 && reportData.length" :date_from="date_from" :date_to="date_to"
        :data="reportData"/>
      <ReportByContent
        v-else-if="type_id===6 && reportData.length" :date_from="date_from" :date_to="date_to"
        :data="reportData"/>
      <ReportByCampaignAgency
        v-else-if="type_id===7 && reportData.length" :date_from="date_from" :date_to="date_to"
        :data="reportData"/>
      <ReportByMedium
        v-else-if="type_id===8 && reportData.length" :date_from="date_from" :date_to="date_to"
        :data="reportData"/>
      <ReportByTerm
        v-else-if="type_id===9 && reportData.length" :date_from="date_from" :date_to="date_to"
        :data="reportData"/>
      <ReportByUtm
        v-else-if="type_id===10 && reportData.length" :date_from="date_from" :date_to="date_to"
        :data="reportData"/>
      <ReportUtmExtend
        v-else-if="type_id===11 && reportData.length" :date_from="date_from" :date_to="date_to"
        :data="reportData"/>
    </v-card>
  </div>
</template>
<script>
import ByAgencySites from '~/components/report/ByAgencySites'
import ReportByStatus from '~/components/report/ReportByStatus'
import ByOperator from '~/components/report/ByOperator'
import ReportByDate from '~/components/report/ReportByDate'
import ReportBySource from '~/components/report/ReportBySource'
import ReportByContent from '~/components/report/ReportByContent'
import ReportByCampaignAgency from '~/components/report/ReportByCampaignAgency'
import ReportByMedium from '~/components/report/ReportByMedium'
import ReportByTerm from '~/components/report/ReportByTerm'
import ReportByUtm from '~/components/report/ReportByUtm'
import ReportUtmExtend from '~/components/report/ReportUtmExtend'

export default {
  name: 'CrmReports',
  middleware: "permission",
  layout: 'agency',
  components: {
    ReportByStatus,
    ByOperator,
    ByAgencySites,
    ReportByDate,
    ReportBySource,
    ReportByContent,
    ReportByCampaignAgency,
    ReportByMedium,
    ReportByTerm,
    ReportByUtm,
    ReportUtmExtend,
  },
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
    payment_method: null,
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
      {text: 'Все', value: 'actions', sortable: false},
      {text: 'Эффективность', value: 'actions', sortable: false}
    ],
    type_id: 1,
    types: [
      {
        id: 1,
        name: 'Отчет по операторам'
      },
      {
        id: 2,
        name: 'Отчет по статусам'
      },
      {
        id: 3,
        name: 'Отчет по сайтам'
      },
      {
        id: 4,
        name: 'Отчет по датам'
      },
      {
        id: 5,
        name: 'Отчет по UTM SOURCE'
      },
      {
        id: 6,
        name: 'Отчет по UTM Content'
      },
      {
        id: 7,
        name: 'Отчет по UTM Campaign'
      },
      {
        id: 8,
        name: 'Отчет по UTM Medium'
      },
      {
        id: 9,
        name: 'Отчет по UTM Term'
      },
      {
        id: 10,
        name: 'Отчет по UTM меткам'
      },
      {
        id: 11,
        name: 'Отчет по UTM (Расширенный)'
      },
    ]
  }),

  async fetch({store, params: {id}}) {
    await store.dispatch('showroom/fetchSites', {id})
    await store.dispatch('showroom/fetchShowroom', {id})
    await store.dispatch('user/toggle', false)
  },

  computed: {
    showroom_id() {
      return this.$route.params.id
    },
    agency_id() {
      console.log(this.$route.params.agency)
      return this.$route.params.agency
    },
    showroom() {
      return this.$store.state.showroom.showroom
    },
    sites() {
      return this.$store.state.showroom.sites.filter(l => l.agency_id == this.$route.params.agency)
    },
  },

  watch: {
    order_type() {
      this.reset()
    },
  },

  methods: {
    execute() {
      if (this.type_id >=1 && this.type_id <= 11) {
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
        2: 'report/status',
        3: 'report/agency/sites',
        4: 'report/agency/dates',
        5: 'report/source',
        6: 'report/content',
        7: 'https://accas.ru/api/report/campaign',
        8: 'report/medium',
        9: 'report/term',
        10: 'report/utm',
        11: 'report/extend',
      };
      try {
        const query = this.getData();
        const endpoint = endpointMap[this.type_id] || 'report/operator';
        const {data} = await this.$axios.post(endpoint, query);
        this.report = true;
        this.reportData = data;
        loader.hide();
      } catch (error) {
        this.handleError(error);
      }
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
        showroom_id: this.showroom_id,
        site_id: this.site_id,
        agency_id: +this.$route.params.agency,
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
      this.order_type = false
      this.payment_method = null
      this.reportData = []
    }
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

<template>
  <v-container class="d-flex flex-grow-1 flex-column">
    <BreadCrumb :items="links"/>
      <v-row class="flex-grow-0 mb-4" dense>

        <v-col cols="6" md="3">
          <showroom-card
            label="Комфорт"
            class="h-full mt-2"
            :color="theme.success"
            :value="178"
            :percentage="2.12"
            percentage-label="Тест 2"
            :loading="isLoading3"
            :series="customersSeries"
            :link="'/showroom/2'"
          />
        </v-col>
        <v-col cols="6" md="3">
          <showroom-card
            label="АвтоПремиум"
            class="h-full mt-2"
            :color="theme.success"
            :value="178"
            :percentage="2.12"
            percentage-label="Тест 2"
            :loading="isLoading3"
            :series="customersSeries"
            :link="'/showroom/4'"
          />
        </v-col>

        <v-col cols="6" md="3">
          <showroom-card
            label="Авангард Юг"
            class="h-full mt-2"
            :color="theme.success"
            :value="178"
            :percentage="2.12"
            percentage-label="Тест 2"
            :loading="isLoading3"
            :series="customersSeries"
            :link="'/showroom/5'"
          />
        </v-col>

        <v-col cols="6" md="3">
          <showroom-card
            label="Форсаж"
            class="h-full mt-2"
            :color="theme.success"
            :value="178"
            :percentage="2.12"
            percentage-label="Тест 2"
            :loading="isLoading3"
            :series="customersSeries"
            :link="'/showroom/7'"
          />
        </v-col>
      </v-row>
  </v-container>
</template>

<script>
// DEMO Cards for dashboard
import SalesCard from '~/components/dashboard/SalesCard'
import ActivityCard from '~/components/dashboard/ActivityCard'
import SourcesCard from '~/components/dashboard/SourcesCard'
import InboxCard from '~/components/dashboard/InboxCard'
import TableCard from '~/components/dashboard/TableCard'
import TrackCard from '~/components/dashboard/TrackCard'
import TicketsCard from '~/components/dashboard/TicketsCard'
import ShowroomCard from '~/components/dashboard/ShowroomCard'
import BreadCrumb from '~/components/BreadCrumb'

export default {
  components: {
    SalesCard,
    ActivityCard,
    SourcesCard,
    InboxCard,
    TableCard,
    TrackCard,
    TicketsCard,
    ShowroomCard,
    BreadCrumb,
  },
  data() {
    return {
      loadingInterval: null,
      isLoading1: true,
      isLoading2: true,
      isLoading3: true,
      ordersSeries: [{
        name: 'Приезды',
        data: [
          ['2020-02-02', 34],
          ['2020-02-03', 43],
          ['2020-02-04', 40],
          ['2020-02-05', 43]
        ]
      }],
      customersSeries: [{
        name: 'Приезды',
        data: [
          ['2020-02-02', 13],
          ['2020-02-03', 11],
          ['2020-02-04', 13],
          ['2020-02-05', 12]
        ]
      }],
      links: [
          {
            text: 'Главная',
            disabled: false,
            href: '/'
          },
          {
            text: 'Автосалоны',
            disabled: true,
            href: '/showroom'
          }
        ],
    }
  },
  computed: {
    theme () {
      return  this.$vuetify.theme.defaults.light
    }
  },
  mounted() {
    let count = 0

    // DEMO delay for loading graphics
    this.loadingInterval = setInterval(() => {
      this[`isLoading${count++}`] = false
      if (count === 4) this.clear()
    }, 100)
  },
  beforeDestroy() {
    this.clear()
  },
  methods: {
    clear() {
      clearInterval(this.loadingInterval)
    }
  }
}
</script>

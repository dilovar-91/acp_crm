<template>
  <v-container class="d-flex flex-grow-1 flex-column">

    <v-row class="flex-grow-0 mb-4" dense>
      <v-col v-can="'see_tab_urus'" cols="6" md="2">
        <showroom-card
          label="Комфорт"
          class="h-full mt-2"
          :color="theme.success"
          :value="178"
          :percentage="2.12"
          percentage-label="Тест 2"
          :loading="isLoading3"
          :series="customersSeries"
          :link="'/showroom/3'"
        />
      </v-col>
      <v-col v-can="'see_tab_urus'" cols="6" md="2">
        <showroom-card
          label="АвтоПремиум"
          class="h-full mt-2"
          :color="theme.success"
          :value="178"
          :percentage="2.12"
          percentage-label="Тест 2"
          :loading="isLoading3"
          :series="customersSeries"
          :link="'/showroom/3'"
        />
      </v-col>

      <v-col v-can="'see_tab_urus'" cols="6" md="2">
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
      <v-col v-can="'see_tab_urus'" cols="6" md="3">
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
    <v-row class="flex-grow-0" dense>

      <template v-for="(item, i) in tabs">
        <v-col
          v-can="'see_tab_'+item.slug"
          :key="i"
          cols="12"
          md="2"
        >
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              :class="{ 'on-hover': hover }"
              :to="'/'  + item.slug"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="title white--text">
                  <v-row
                    class="fill-height flex-column pl-2"
                    justify="space-between"
                  >
                    <p class="mt-4 subheading text-center">
                      <v-icon color="yellow" x-large>
                        {{ item.icon }}
                      </v-icon>
                    </p>
                    <div>
                      <p class="ma-0 body-1 font-weight-bold text-center text-h6">
                        {{ item.name }}
                      </p>
                    </div>
                  </v-row>
                </v-card-title>
              </div>
            </v-card>
          </v-hover>
        </v-col>
      </template>
      <v-col cols="6">
        <sales-card
          class="h-full"
          style="min-height: 380px"
          :value="1837.32"
          :percentage="3.2"
          :loading="isLoading1"
          percentage-label="Прошлая неделя"
          action-label="Отчет"
        ></sales-card>
      </v-col>
      <v-col cols="6">
        <sales-card
          class="h-full"
          style="min-height: 380px"
          :value="1837.32"
          :percentage="3.2"
          :loading="isLoading1"
          percentage-label="Прошлая неделя"
          action-label="Отчет"
        ></sales-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import SalesCard from '~/components/dashboard/SalesCard'
import ShowroomCard from '~/components/dashboard/ShowroomCard'
export default {
  name:"HomePage",
  middleware:"auth",
  components: {
    SalesCard,
    ShowroomCard,
  },
  async asyncData ({ $axios }) {
    const [tabs] = await Promise.all([
      $axios.get('/get-tabs')
    ])

    return {
      tabs: tabs.data,
    }
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
      }]
    }
  },
  computed: {
    theme () {
      return this.$vuetify.theme.isDark
        ? this.$vuetify.theme.defaults.dark
        : this.$vuetify.theme.defaults.light
    }
  },
  mounted() {
    let count = 0

    // DEMO delay for loading graphics
    this.loadingInterval = setInterval(() => {
      this[`isLoading${count++}`] = false
      if (count === 4) this.clear()
    }, 400)
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

<style scoped>
.v-card {
  transition: opacity 0.4s ease-in-out;
}

.v-card:not(.on-hover) {
  opacity: 1.2;
}

.bg-blue {
  background-color: #4caf50 !important;
  caret-color: #4caf50 !important;
}
</style>

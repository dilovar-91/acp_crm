<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links" />

      <v-row class="mt-1">
        <template v-for="(item, i) in items">
          <v-col  :key="i" cols="12" xl="2" md="3">
            <v-hover v-slot="{ hover }">
              <v-card
                :elevation="hover ? 12 : 2"
                dark
                :class="{ 'on-hover': hover }"
                :to="'/' + item.link"
                color="#4caf50"
              >
                <div height="225px" class="bg-blue">
                  <v-card-title class="headline">
                    {{ item.title }}
                  </v-card-title>
                  <v-card-actions>
                    <v-btn :to="item.link" text>
                      Перейти
                    </v-btn>
                  </v-card-actions>
                </div>
              </v-card>
            </v-hover>
          </v-col>
        </template>
        <v-col  cols="12" xl="2" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              :to="'/cars/transits'"
              color="#4caf50"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline">
                  История транзитов
                </v-card-title>
                <v-card-actions>
                  <v-btn :to="'/cars/transits'" text>
                    Перейти
                  </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>
<script>
import BreadCrumb from '~/components/BreadCrumb'
export default {
  name: 'CarIndex',
  components: { BreadCrumb },
  layout: 'default',
  auth: false,
  data: () => ({
    items: [
      {
        title: 'В поставке',
        text: 'Автомобили в поставке',
        count: 300,
        icon: 'mdi-car-estate',
        link: 'cars/6'
      },
      {
        title: 'Все салоны',
        text: 'Список автомобилей всех салонов ',
        icon: 'mdi-cash-usd-outline',
        count: 800,
        link: 'cars/all'
      },
      {
        title: 'Комфорт',
        text: 'Список автомобилей Комфорт (Рязанка)',
        count: 300,
        icon: 'mdi-car-electric',
        link: 'cars/2'
      },
      {
        title: 'Щелковская Склад',
        text: 'Список автомобилей Щелковская',
        count: 300,
        icon: 'mdi-car-electric',
        link: 'cars/3'
      },
      {
        title: 'АвтоПремиум',
        text: 'Список автомобилей АвтоПремиум',
        count: 300,
        icon: 'mdi-car-estate',
        link: 'cars/4'
      },
      {
        title: 'Авангард Юг',
        text: 'Список автомобилей Авангард Юг',
        count: 300,
        icon: 'mdi-car-estate',
        link: 'cars/5'
      }
    ]
  }),
  async fetch ({ store }) {
    await store.dispatch('showroom/fetchShowrooms')
  },
  computed: {
    showrooms () {
      return this.$store.state.showroom.showrooms
    },
    role () {
      return this.$store.state.auth.role
    },
    user () {
      return this.$store.state.auth.user
    },
    links () {
      return [
        {
          text: "Admin",
          disabled: false,
          href: '/'
        },
        {
          text: 'Новые автомобили',
          disabled: false,
          href: '/cars'
        }
      ]
    }
  },
  methods: {
  }
}
</script>

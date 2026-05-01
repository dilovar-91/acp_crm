<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links" />
      <v-row class="mt-2">
        <template>
          <template v-for="(item, i) in items">
            <v-col :key="i" cols="12" md="3">
              <v-hover v-slot="{ hover }">
                <v-card
                  :elevation="hover ? 12 : 2"
                  dark
                  :class="{ 'on-hover': hover }"
                  :to="item.link"
                  color="#4caf50"
                >
                  <div height="225px" class="bg-blue">
                    <v-card-title class="headline">
                      {{ item.title }}
                    </v-card-title>

                    <v-card-subtitle>{{ item.text }}</v-card-subtitle>
                    <v-card-actions>
                      <v-btn :to="'/' + item.link" text> Перейти </v-btn>
                    </v-card-actions>
                  </div>
                </v-card>
              </v-hover>
            </v-col>
          </template>
        </template>
      </v-row>
    </v-container>
  </div>
</template>
<script>
import BreadCrumb from '~/components/BreadCrumb'
export default {
  components: { BreadCrumb },
  middleware: 'permission',
  data: () => ({
    users: [
      'admin',
      'operator_acp',
      'operator_light',
      'operator',
      'operator2',
      'custom',
    ],
    group: ['admin', 'operator_acp', 'operator_light', 'operator', 'operator2'],
    storage: [
      'admin',
      'operator_acp',
      'operator_light',
      'operator',
      'operator2',
      'manager_acp',
      'manager_light',
      'petr',
      'manager',
      'manager2',
    ],
    items: [
      {
        title: 'Планшетка Комфорт',
        text: 'Таблица приезд клиентов ',
        icon: 'mdi-cash-usd-outline',
        count: 800,
        link: '/tablet/2',
      },
      {
        title: 'Планшетка АвтоПремиум',
        text: 'Таблица приезд клиентов ',
        icon: 'mdi-cash-usd-outline',
        count: 800,
        link: '/tablet/4',
      },
      {
        title: 'Планшетка Авангард Юг',
        text: 'Таблица приезд клиентов ',
        icon: 'mdi-cash-usd-outline',
        count: 800,
        link: '/tablet/5',
      },
      {
        title: 'Планшетка Форсаж',
        text: 'Таблица приезд клиентов ',
        icon: 'mdi-cash-usd-outline',
        count: 800,
        link: '/tablet/7',
      },
    ],
  }),
  async fetch({ store, params: { id } }) {
    await store.dispatch('showroom/fetchShowroom', { id })
  },
  computed: {
    showroom() {
      return this.$store.state.showroom.showroom
    },
    showroom_id() {
      return Number(this.$route.params.id) || null
    },
    role() {
      return this.$store.state.auth.role
    },
    pages() {
      return this.$store.state.permission.nest_pages
    },
    user() {
      return this.$store.state.auth.user
    },
    links() {
      return [
        {
          text: 'Главная',
          disabled: false,
          href: '/',
        },
        {
          text: 'Планшетка',
          disabled: false,
          href: '/tablet/',
        },
      ]
    },
  },
  methods: {
    redirect(url) {
      window.open(url, '_blank')
    },
  },
}
</script>
<style scoped>
.v-card {
  transition: opacity 0.4s ease-in-out;
}

.v-card:not(.on-hover) {
  opacity: 5;
}

.show-btns {
  color: rgba(255, 255, 255, 1) !important;
}
</style>

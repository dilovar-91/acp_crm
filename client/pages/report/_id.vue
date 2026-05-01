<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links" />
      <v-row class="mt-2">
        <template>
          <template v-for="(item, i) in items">
            <v-col
              :key="i"
              cols="12"
              md="3"
              v-role-or-permission="'admin|see_tab_crm/' + item.link"
            >
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
                      <v-btn :to="item.link" text> Перейти </v-btn>
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
  layout({ $auth }) {
    return $auth.user.role_id === 4 || $auth.user.role_id === 7
      ? 'agency'
      : 'default'
  },
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
  }),

  async fetch({ store, params: { id } }) {
    await store.dispatch('showroom/fetchShowroom', { id })
  },
  computed: {
    items() {
      return [
        {
          title: 'Заявки',
          text: 'Таблица приезд клиентов ',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/orders',
        },
        {
          title: 'Оплата проезда',
          text: 'Оплата проезда клиентов ',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/fares',
        },
        {
          title: 'Отчёты',
          text: 'Отчёты ',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/reports',
        },
        {
          title: 'Отчёты JustWe',
          text: 'Отчёты ',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/report/1',
        },
        {
          title: 'Отчёты 100UP',
          text: 'Отчёты ',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/report/2',
        },
        {
          title: 'Отчёты Victory',
          text: 'Отчёты ',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/report/3',
        },
        {
          title: 'Отчёты Классифайд',
          text: 'Отчёты ',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/report/4',
        },
        {
          title: 'Отчёты SEO',
          text: 'Отчёты ',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/report/5',
        },
        {
          title: 'Отчёты Agency1',
          text: 'Отчёты ',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/report/6',
        },
        {
          title: 'Отчёты Agency New',
          text: 'Отчёты ',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/report/7',
        },
        {
          title: 'График работы',
          text: 'График работы операторов ',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/schedules',
        },
        {
          title: 'Заявки (JustWe)',
          text: 'Упрощенная',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/orders-mini/1',
        },
        {
          title: 'Заявки (100Up)',
          text: 'Упрощенная',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/orders-mini/2',
        },
        {
          title: 'Заявки (Victory)',
          text: 'Упрощенная',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/orders-mini/3',
        },
        {
          title: 'Заявки (Agency1)',
          text: 'Упрощенная',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/orders-mini/6',
        },
        {
          title: 'Заявки (Agency NEW)',
          text: 'Упрощенная',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/orders-mini/7',
        },
      ]
    },
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
          text: 'CRM',
          disabled: false,
          href: '/crm/',
        },
        {
          text: this.showroom.name || null,
          disabled: true,
          href: '/',
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

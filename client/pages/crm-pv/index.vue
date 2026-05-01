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
              v-role-or-permission="'admin|see_tab_' + item.link"
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
                    <v-card-subtitle></v-card-subtitle>
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
  name: 'CrmIndex',
  layout({ $auth }) {
    return $auth.user.role_id === 4 || $auth.user.role_id === 7
      ? 'agency'
      : 'default'
  },
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
        title: 'PV Автопремиум(Р1)',
        text: 'Таблица приезд клиентов ',
        icon: 'mdi-cash-usd-outline',
        count: 800,
        link: '/crm-pv/4',
      },
      {
        title: 'PV АМК(Р2)',
        text: 'Таблица приезд клиентов ',
        icon: 'mdi-cash-usd-outline',
        count: 800,
        link: '/crm-pv/2',
      },
      {
        title: 'PV Автодом(Р3)',
        text: 'Таблица приезд клиентов ',
        icon: 'mdi-cash-usd-outline',
        count: 800,
        link: '/crm-pv/10',
      },
      {
        title: 'PV Автомаксимум(К1)',
        text: 'Таблица приезд клиентов ',
        icon: 'mdi-cash-usd-outline',
        count: 800,
        link: '/crm-pv/5',
      },
      {
        title: 'PV Автопремьер(К2)',
        text: 'Таблица приезд клиентов ',
        icon: 'mdi-cash-usd-outline',
        count: 800,
        link: '/crm-pv/8',
      },
      {
        title: 'PV Форсаж(В1)',
        text: 'Таблица приезд клиентов ',
        icon: 'mdi-cash-usd-outline',
        count: 800,
        link: '/crm-pv/7',
      },
      {
        title: 'PV Автопорт(Л)',
        text: 'Таблица приезд клиентов ',
        icon: 'mdi-cash-usd-outline',
        count: 800,
        link: '/crm-pv/14',
      },
      {
        title: 'PV Автоплюс(П)',
        text: 'Таблица приезд клиентов ',
        icon: 'mdi-cash-usd-outline',
        count: 800,
        link: '/crm-pv/13',
      },
      {
        title: 'PV Авангард(С1)',
        text: 'Таблица приезд клиентов ',
        icon: 'mdi-cash-usd-outline',
        count: 800,
        link: '/crm-pv/15',
      },
      {
        title: 'PV Автополе(С2)',
        text: 'Таблица приезд клиентов ',
        icon: 'mdi-cash-usd-outline',
        count: 800,
        link: '/crm-pv/17',
      },
      {
        title: 'Общие отчеты',
        text: 'Общие отчеты PV ',
        icon: 'mdi-cash-usd-outline',
        count: 800,
        link: '/crm-pv/all-reports',
      },
    ],
  }),
  computed: {
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
          text: 'PV',
          disabled: false,
          href: '/crm/' + this.$route.params?.id,
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

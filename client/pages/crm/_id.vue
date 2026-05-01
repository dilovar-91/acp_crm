<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links" />
      <v-row class="mt-2">
        <template>
          <template v-for="(item, i) in items">
            <v-col
              :key="i"
              v-role-or-permission="'admin|see_tab_crm/' + item.link"
              cols="12"
              md="3"
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
          title: 'Заявки в ЧС',
          text: 'Оплата проезда клиентов ',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/blacklist-orders',
        },
        {
          title: 'Отчёты',
          text: 'Отчёты ',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/reports',
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
          title: 'Отчёты InHouse',
          text: 'Отчёты ',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/report/14',
        },
        {
          title: 'Отчёты InHouse VK',
          text: 'Отчёты ',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/report/28',
        },
        {
          title: 'График работы',
          text: 'График работы операторов ',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/schedules',
        },
        {
          title: 'Заявки (Victory)',
          text: 'Упрощенная',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/orders-mini/3',
        },

        {
          title: 'Заявки (Классифайд)',
          text: 'Упрощенная',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/orders-mini/4',
        },
        {
          title: 'Заявки (InHouse)',
          text: 'Упрощенная',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/orders-mini/14',
        },
        {
          title: 'Заявки (InHouse VK)',
          text: 'Упрощенная',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/orders-mini/28',
        },
        {
          title: 'Заявки (Я.Бизнес)',
          text: 'Упрощенная',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/report/22',
        },
        {
          title: 'Заявки (Target)',
          text: 'Упрощенная',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/orders-mini/32',
        },
        {
          title: 'Отчёты Target',
          text: 'Отчёты ',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/report/32',
        },
        {
          title: 'Заявки (VDL S)',
          text: 'Упрощенная',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/orders-mini/33',
        },
        {
          title: 'Отчёты VDL S',
          text: 'Отчёты ',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/report/33',
        },
        {
          title: 'Заявки (Авито реальный)',
          text: 'Упрощенная',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/orders-mini/30',
        },
        {
          title: 'Заявки (Авито муляж)',
          text: 'Упрощенная',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/orders-mini/31',
        },

        {
          title: 'Отчёты (Авито реальный)',
          text: 'Отчёты ',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/report/30',
        },
        {
          title: 'Заявки (JS)',
          text: 'Упрощенная',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/orders-mini/36',
        },

        {
          title: 'Отчёты (JS)',
          text: 'Отчёты ',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/report/36',
        },
        {
          title: 'Отчёты (Авито муляж)',
          text: 'Отчёты ',
          icon: 'mdi-cash-usd-outline',
          count: 800,
          link: this.showroom_id + '/report/31',
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

<template>
  <v-app>
    <div v-shortkey="['f5']" class="d-flex flex-grow-1">
      <!-- Toolbar -->
      <v-app-bar app :color="'surface bg-primary'">
        <v-card
          class="flex-grow-1 d-flex"
          :class="[isToolbarDetached ? 'pa-1 mt-3 mx-1' : 'pa-0 ma-0']"
          :flat="!isToolbarDetached"
          style="background-color: #f5f5f5"
        >
          <div class="d-flex flex-grow-1 align-center">
            <!-- search input mobile -->
            <v-text-field
              v-if="showSearch"
              append-icon="mdi-close"
              placeholder="Search"
              prepend-inner-icon="mdi-magnify"
              hide-details
              dark
              solo
              flat
              autofocus
              @click:append="showSearch = false"
            ></v-text-field>

            <div v-else class="d-flex flex-grow-1 align-center">
              <v-app-bar-nav-icon></v-app-bar-nav-icon>

              <div class="title font-weight-bold text-uppercase primary--text">
                <nuxt-link class="text-decoration-none ml-3" to="/">{{
                  product.name
                }}</nuxt-link>
              </div>
              <v-spacer class="d-none d-lg-block"></v-spacer>

              <toolbar-user />
            </div>
          </div>
        </v-card>
      </v-app-bar>
      <v-main>
        <nuxt />
        <v-footer v-if="false" app inset>
          <v-spacer></v-spacer>
          <div class="overline">
            Built with
            <v-icon small color="pink">mdi-heart</v-icon>
            <a
              class="text-decoration-none"
              href="https://a-c77.ru"
              target="_blank"
              >Accas Corp</a
            >
          </div>
        </v-footer>
      </v-main>
    </div>
  </v-app>
</template>

<script>
import { mapState } from 'vuex'

// navigation menu configurations
import config from '../configs'

import MainMenu from '../components/navigation/MainMenu'
import ToolbarUser from '../components/toolbar/ToolbarUser'
import ToolbarApps from '../components/toolbar/ToolbarApps'
import ToolbarCurrency from '../components/toolbar/ToolbarCurrency'

import Sidebar from '~/components/Sidebar'
import ToastComponent from '~/components/ToastComponent'
import { orderBy } from 'lodash'

export default {
  name: 'CrmLayout',
  components: {
    ToolbarUser,
  },
  data() {
    return {
      showSearch: false,
      work_place: null,
      online: false,
      status_id: null,
      drawer: false,
      navigation: config.navigation,
      statuses: [
        {
          id: 1,
          name: 'Онлайн',
          icon: 'mdi-circle',
          color: 'success',
        },
        {
          id: 0,
          name: 'Отошёл',
          icon: 'mdi-circle',
          color: 'error',
        },
      ],

      headers: [
        {
          text: 'Дата',
          align: 'center',
          sortable: false,
          value: 'created_at',
          width: '70px',
        },
        { text: 'Клиент', value: 'client', width: '60px' },
        { text: 'Телефон', value: 'phone', width: '60px' },
        { text: 'Комментарий', value: 'comment', width: '90px' },
        { text: 'Оператор', value: 'operator', width: '60px' },
      ],
      items: [
        {
          action: 'mdi-account-multiple',
          items: [
            { title: 'Список пользователей', link: '/users' },
            { title: 'История входов и выходов', link: '/admin/auth-logs' },
          ],
          title: 'Пользователы',
        },
        {
          action: 'mdi-account-box',
          active: true,
          items: [
            { title: 'Комфорт', link: '/managers/2' },
            { title: 'АвтоПремиум', link: '/managers/4' },
            { title: 'Авангард Юг', link: '/managers/5' },
            { title: 'Форсаж', link: '/managers/7' },
            { title: 'АвтоПремьер', link: '/managers/8' },
          ],
          title: 'Менеджеры',
        },
      ],
    }
  },
  computed: {
    ...mapState('app', [
      'product',
      'isContentBoxed',
      'menuTheme',
      'toolbarTheme',
      'isToolbarDetached',
    ]),
    drawerState: {
      get() {
        return this.$store.state.user.drawerState
      },
      set(v) {
        return this.$store.commit('user/toggleDrawerState', v)
      },
    },
  },

  methods: {
    clickStatus(statusId, isCount = false, isOpen = false) {
      if (isCount) {
        this.getCount(statusId)
      } else this.changeStatus(statusId)
      if (isOpen) {
        this.drawer = isOpen
      }
    },
    changeStatus(status) {
      this.status_id = status
    },
    changeWorkPlace() {
      this.$axios.post('/crm/workplace', { work_place: this.work_place })
    },
    changeOnline() {
      this.$axios.post('/user/change-online')
    },
    getCount(statusId, my = null) {
      let result = 0
      if (my) {
        result = this.todayOrders.filter(
          (l) =>
            l.status_id === statusId && l.operator_id === this.$auth.user?.id
        )
        return result.length
      }
      if (statusId === 5) {
        result = this.todayOrders.filter(
          (l) =>
            l.status_id === statusId ||
            this.$moment().format('YYYY-MM-DD') === l.will_arrive
        )
        return result.length
      }
      result = this.todayOrders.filter((l) => l.status_id == statusId)
      return result.length
    },
    row_classes() {
      if (this.status_id === 1) {
        return 'teal white--text' // can also return multiple classes e.g ["orange","disabled"]
      } else if (this.status_id === 4) {
        return 'green white--text' // can also return multiple classes e.g ["orange","disabled"]
      } else if (this.status_id === 2) {
        return 'teal white--text'
      } else if (this.status_id === 'fired') {
        return 'red white--text'
      } else if (this.status_id === 'urgent') {
        return 'orange white--text'
      } else if (this.status_id === 'not_arrived') {
        return 'blue lighten-2 white--text'
      } else if (this.status_id === 'noAnswer') {
        return 'black lighten-2 white--text'
      } else if (this.status_id === 5) {
        return 'green white--text'
      } else if (this.status_id === 6) {
        return 'primary white--text'
      } else {
        return 'grey lighten-2'
      }
    },
    redirect(item) {
      this.$router.push(
        '/crm/' + item.showroom_id + '/order/' + item.id + '/edit'
      )
    },
    toggle() {
      this.drawer = !this.drawer
    },
    notify() {
      this.$notification.show(
        'Уведомление',
        {
          body: 'Добавлено новая заявка!',
        },
        {}
      )
    },
  },
}
</script>

<style scoped>
.buy-button {
  box-shadow: 1px 1px 18px #e4a;
}
.word-wp {
  word-break: break-word;
}
</style>

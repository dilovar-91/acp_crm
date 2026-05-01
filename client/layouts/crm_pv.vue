<template>
  <v-app>
    <div
      v-shortkey="['f5']"
      class="d-flex flex-grow-1"
    >
      <!-- Navigation -->
      <v-navigation-drawer
        v-model="drawerState"
        app
        floating
        class="elevation-1"
        :right="$vuetify.rtl"
        :light="menuTheme === 'light'"
        :dark="menuTheme === 'dark'"
      >
        <!-- Navigation menu info -->
        <template #prepend>
          <div class="pa-2">
            <div class="title font-weight-bold text-uppercase primary--text">
              <nuxt-link class="text-decoration-none ml-3" to="/crm">{{ product.name }}</nuxt-link>
            </div>
            <div v-if="false" class="overline grey--text">
              <nuxt-link class="text-decoration-none" to="/crm">{{ product.version }}</nuxt-link>
            </div>
          </div>
        </template>

        <!-- Navigation menu -->
        <main-menu :menu="navigation.menu"/>
        <div class="pl-2 mb-0 overline" v-role-or-permission="'admin|see_admin_panel'">Администрирование</div>
        <v-list dense class="mt-0" v-role-or-permission="'admin|see_admin_panel'">
          <v-list-group
            v-for="item in items"
            :key="item.title"
            v-model="item.active"
            :prepend-icon="'mdi-globe'"
            color="success"
            no-action
          >
            <template #activator>
              <v-list-item-content>
                <v-list-item-title v-text="item.title"></v-list-item-title>
              </v-list-item-content>
            </template>
            <v-list-item
              v-for="child in item.items"
              :key="child.title"
              class="pl-4"
              :to="child.link"
            >
              <v-list-item-icon>
                <v-icon color="success">mdi-cash-usd-outline</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title v-text="child.title"></v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list-group>
        </v-list>
        <!-- Navigation menu footer -->
        <template #append>
          <!-- Footer navigation links -->
          <div class="pa-1 text-center">
            <v-btn
              v-for="(item, index) in navigation.footer"
              :key="index"
              :href="item.href"
              :target="item.target"
              small
              text
            >
              {{ item.text }}
            </v-btn>
          </div>

          <!-- REMOVE ME - Shop Demo purposes -->
          <div class="pa-2 pt-1 text-center">
            <v-btn
              class="buy-button"
              dark
              block
              color="#E4A"
              to="/dashboard"
            >
              Дашбоард
            </v-btn>
          </div>
        </template>
      </v-navigation-drawer>

      <!-- Toolbar -->
      <v-app-bar
        app
        :color="'surface bg-primary'"

      >
        <v-card
          class="flex-grow-1 d-flex" :class="[isToolbarDetached ? 'pa-1 mt-3 mx-1' : 'pa-0 ma-0']"
          :flat="!isToolbarDetached" style="background-color: #F5F5F5;">
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
              <v-app-bar-nav-icon @click.stop="drawerState = !drawerState"></v-app-bar-nav-icon>


              <div v-if="drawerState===false" class="title font-weight-bold text-uppercase primary--text">
                <nuxt-link class="text-decoration-none ml-3" to="/">{{ product.name }}</nuxt-link>
              </div>
              <v-spacer class="d-none d-lg-block"></v-spacer>

              <!-- search input desktop -->
              <v-chip
                class="ma-1"
                color="teal"
                text-color="white"
                @click="clickStatus(1, false, true)"
              >
                Все новые
                <v-avatar
                  right
                  class="orange"
                >
                  {{ getCount(1) }}
                </v-avatar>
              </v-chip>
              <v-chip
                class="ma-1"
                color="grey"
                text-color="white"
                @click="clickStatus('myNew', false, true)"
              >
                Новые
                <v-avatar
                  right
                  class="orange"
                >
                  {{ getCount(1, true, true) }}
                </v-avatar>
              </v-chip>
              <v-chip
                class="ma-1"
                color="green"
                text-color="white"
                @click="clickStatus(4, false, true)"
              >
                Одобрить
                <v-avatar
                  right
                  class="orange"
                >
                  {{ getCount(4) }}
                </v-avatar>
              </v-chip>
              <v-chip
                class="ma-1"
                color="green"
                text-color="white"
                @click="clickStatus(12, false, true)"
              >
                Одобрено
                <v-avatar
                  right
                  class="orange"
                >
                  {{ getCount(12) }}
                </v-avatar>
              </v-chip>
              <v-chip
                class="ma-1"
                color="purple"
                text-color="white"
                @click="clickStatus('arrivals', false, true)"
              >
                Приедет
                <v-avatar
                  right
                  class="orange"
                >
                  {{ all_arrivals.length }}
                </v-avatar>
              </v-chip>
              <v-chip
                class="ma-1"
                color="purple"
                text-color="white"
                @click="clickStatus('my_arrivals',false, true)"
              >
                Мой приезд
                <v-avatar
                  right
                  class="orange"
                >
                  {{ my_arrivals.length }}
                </v-avatar>
              </v-chip>
              <v-chip
                class="ma-1"
                color="red"
                text-color="white"
                @click="clickStatus('fired', false, true)"
              >
                Сгоревшие
                <v-avatar
                  right
                  class="green"
                >
                  {{ fired.length }}
                </v-avatar>
              </v-chip>

              <v-chip
                class="ma-1"
                color="blue"
                text-color="white"
                @click="clickStatus('myFired', false, true)"
              >
                Мои сгоревшие
                <v-avatar
                  right
                  class="orange text--white"
                >
                  {{ myFired.length }}
                </v-avatar>
              </v-chip>

              <v-chip
                class="ma-1"
                color="orange"
                text-color="white"
                @click="clickStatus('urgent', false, true)"
              >
                Срочные
                <v-avatar
                  right
                  class="green "
                >
                  {{ urgent.length }}
                </v-avatar>
              </v-chip>
              <v-chip
                class="ma-1"
                color="orange"
                text-color="white"
                @click="clickStatus('myUrgent', false, true)"
              >
                Мои срочные
                <v-avatar
                  right
                  class="green "
                >
                  {{ myUrgent.length }}
                </v-avatar>
              </v-chip>


              <v-chip
                class="ma-1"
                color="error"
                text-color="white"
                @click="clickStatus('missed_calls', false, true)"
              >
                Пропущенные звонки
                <v-avatar
                  right
                  class="green"
                >
                  {{ missed_calls.length }}
                </v-avatar>
              </v-chip>
              <v-chip
                class="ma-1"
                color="black"
                text-color="white"
                @click="clickStatus('noAnswer', false, true)"
              >
                НО
                <v-avatar
                  right
                  class="orange"
                >
                  {{ noAnswer.length }}
                </v-avatar>
              </v-chip>
              <v-chip
                class="ma-1"
                color="black"
                text-color="white"
                @click="clickStatus('MyNoAnswer', false, true)"
              >
                Мои НО
                <v-avatar
                  right
                  class="orange"
                >
                  {{ MyNoAnswer.length }}
                </v-avatar>
              </v-chip>
              <v-btn class="d-block d-sm-none" icon @click="showSearch = true">
                <v-icon dark>mdi-magnify</v-icon>
              </v-btn>
              <v-select v-if="false" hide-details v-model="online" item-text="name" item-value="id"
                        :value="$auth.user?.online" :items="statuses" outlined dense
                        style="max-width: 140px; margin-top: 5px; margin-right: 10px;" @change="changeOnline()">
                <template v-slot:selection="{ item, index }">
                  <v-icon :color="item.color">{{ item.icon }}</v-icon>
                  {{ item.name }}
                </template>
                <template v-slot:item="{ item }">
                  <v-icon :color="item.color">{{ item.icon }}</v-icon>
                  {{ item.name }}
                </template>
              </v-select>
              РМ:
              <v-select v-model="work_place" hide-details dense outlined clearable
                        style="max-width: 120px; margin-top: 5px; margin-left: 10px;" :items="work_places"
                        @change="changeWorkPlace()">
              </v-select>
              <toolbar-user/>
            </div>
          </div>
        </v-card>
      </v-app-bar>

      <v-main>

        <v-navigation-drawer
          v-model="drawer"
          width="950px"
          right
          fixed
          temporary
          class="light"
        >
          <div v-shortkey="['f4']" class="text-center" @shortkey="toggle()">

            <v-chip
              class="ma-1"
              color="teal"
              text-color="white"
              @click="clickStatus(1)"
            >
              Все новые
              <v-avatar
                right
                class="orange"
              >
                {{ getCount(1) }}
              </v-avatar>
            </v-chip>
            <v-chip
              class="ma-1"
              color="grey"
              text-color="white"
              @click="clickStatus('myNew', false)"
            >
              Новые
              <v-avatar
                right
                class="orange"
              >
                {{ getCount(1, true, true) }}
              </v-avatar>
            </v-chip>
            <v-chip
              class="ma-1"
              color="green"
              text-color="white"
              @click="clickStatus(4)"
            >
              Одобрить
              <v-avatar
                right
                class="orange"
              >
                {{ getCount(4) }}
              </v-avatar>
            </v-chip>
            <v-chip
              class="ma-1"
              color="blue"
              text-color="white"
              @click="clickStatus(12)"
            >
              Одобрено
              <v-avatar
                right
                class="orange"
              >
                {{ getCount(12) }}
              </v-avatar>
            </v-chip>
            <v-chip
              class="ma-1"
              color="purple"
              text-color="white"
              @click="clickStatus('arrivals')"
            >
              Приедет
              <v-avatar
                right
                class="orange"
              >
                {{ all_arrivals.length }}
              </v-avatar>
            </v-chip>
            <v-chip
              class="ma-1"
              color="purple"
              text-color="white"
              @click="clickStatus('my_arrivals')"
            >
              Мой приезд
              <v-avatar
                right
                class="orange"
              >
                {{ my_arrivals.length }}
              </v-avatar>
            </v-chip>
            <v-chip
              class="ma-1"
              color="red"
              text-color="white"
              @click="clickStatus('fired')"
            >
              Сгоревшие
              <v-avatar
                right
                class="green"
              >
                {{ fired.length }}
              </v-avatar>
            </v-chip>

            <v-chip
              class="ma-1"
              color="blue"
              text-color="white"
              @click="clickStatus('myFired')"
            >
              Мои сгоревшие
              <v-avatar
                right
                class="orange text--white"
              >
                {{ myFired.length }}
              </v-avatar>
            </v-chip>

            <v-chip
              class="ma-1"
              color="orange"
              text-color="white"
              @click="clickStatus('urgent')"
            >
              Срочные
              <v-avatar
                right
                class="green "
              >
                {{ urgent.length }}
              </v-avatar>
            </v-chip>
            <v-chip
              class="ma-1"
              color="orange"
              text-color="white"
              @click="clickStatus('myUrgent')"
            >
              Мои срочные
              <v-avatar
                right
                class="green "
              >
                {{ myUrgent.length }}
              </v-avatar>
            </v-chip>
            <v-chip
              class="ma-1"
              color="black"
              text-color="white"
              @click="clickStatus('noAnswer')"
            >
              НО
              <v-avatar
                right
                class="orange"
              >
                {{ noAnswer.length }}
              </v-avatar>
            </v-chip>
            <v-chip
              class="ma-1"
              color="black"
              text-color="white"
              @click="clickStatus('MyNoAnswer')"
            >
              Мои НО
              <v-avatar
                right
                class="orange"
              >
                {{ MyNoAnswer.length }}
              </v-avatar>
            </v-chip>
            <v-chip
              class="ma-1"
              color="error"
              text-color="white"
              @click="clickStatus('missed_calls')"
            >
              Пропущенные звонки
              <v-avatar
                right
                class="orange"
              >
                {{ missed_calls.length }}
              </v-avatar>
            </v-chip>
          </div>

          <v-data-table
            v-if="status_id=='missed_calls'"
            :headers="headers"
            :items="missed_calls"
            :items-per-page="missed_calls.length"
            class="elevation-1"
            hide-default-footer
            fixed-header
            dense
          >
            <template
              #body="{ items }"
            >
              <tbody>
              <tr
                v-for="(item, index) in items"
                :key="'foo_'+ index"
                :class="row_classes(item)"
                @dblclick="redirect(item)"
              >
                <td class="px-2 text-center">
                  <a target="_blank" :href="`/crm/${item.showroom_id}/order/${item.id}/edit`">
                    {{ $moment(item.created_at).format('DD.MM.YYYY HH:mm') }}
                  </a>
                </td>
                <td class="px-2">{{ item.client_name }} 123</td>
                <td class="px-2">{{ item.phone }}</td>
                <td class="px-2 word-wp">
                  <v-tooltip bottom max-width="400px" color="primary">
                    <template #activator="{ on, attrs }">
                      <div color="primary" dark v-bind="attrs" v-on="on">
                        {{ item.comment | truncate(120) }}
                      </div>
                    </template>
                    <span>{{ item.comment }}</span>
                  </v-tooltip>

                </td>
                <td class="px-0">{{ item?.first_name }} {{ item?.last_name }}</td>
              </tr>
              </tbody>
            </template>
          </v-data-table>
          <v-data-table
            v-else-if="status_id!='missed_calls'"
            :headers="headers"
            :items="order_items"
            :items-per-page="order_items.length"
            class="elevation-1"
            hide-default-footer
            fixed-header
            dense
          >
            <template
              #body="{ items }"
            >
              <tbody>
              <tr
                v-for="item in items"
                :key="'bar_'+ item.id"
                :class="row_classes(item)"
                @dblclick="redirect(item)"
              >
                <td class="px-2 text-center">
                  <nuxt-link :to="'/crm/'+item.showroom_id+'/order/'+item.id + '/edit'">
                    <span v-if="status_id==='fired'">{{ $moment(item.callback).format('HH:mm') }}</span>
                    <span v-else-if="status_id==='urgent'">{{
                        ($moment().diff($moment(item.callback), 'minutes')) * -1
                      }} минут</span>
                    <span v-else-if="status_id === 'arrivals' || status_id === 'my_arrivals'">{{
                        $moment(item.will_arrive).format('DD.MM.YYYY')
                      }}</span>
                    <span v-else>{{ $moment(item.created_at).format('DD.MM.YYYY HH:mm') }}</span>
                  </nuxt-link>
                </td>
                <td class="px-2">{{ item.client_name }}</td>
                <td class="px-2">{{ item.phone }} {{ item.callback }}</td>
                <td class="px-2 word-wp">
                  <v-tooltip bottom max-width="400px" color="primary">
                    <template #activator="{ on, attrs }">
                      <div color="primary" dark v-bind="attrs" v-on="on">
                        {{ item.comment | truncate(120) }}
                      </div>
                    </template>
                    <span>{{ item.comment }}</span>
                  </v-tooltip>

                </td>
                <td class="px-0">
                  <template v-if="item.operator?.first_name">{{ item.operator?.first_name }} {{
                      item.operator?.last_name
                    }}
                  </template>
                </td>
              </tr>
              </tbody>
            </template>
          </v-data-table>
        </v-navigation-drawer>
        <v-fab-transition>
          <v-btn
            color="success"
            absolute
            open
            fab
            ripple
            fixed
            right
            class="mt-12"
            @click.stop="drawer = !drawer"
          >
            <v-icon>
              {{ (drawer ? 'mdi-telegram' : 'mdi-arrow-left-bold') }}
            </v-icon>
          </v-btn>
        </v-fab-transition>
        <nuxt/>
        <v-footer v-if="false" app inset>
          <v-spacer></v-spacer>
          <div class="overline">
            Built with
            <v-icon small color="pink">mdi-heart</v-icon>
            <a class="text-decoration-none" href="https://indielayer.com" target="_blank">@</a>
          </div>
        </v-footer>
      </v-main>
    </div>
  </v-app>
</template>

<script>
import {mapState} from 'vuex'

// navigation menu configurations
import {orderBy} from "lodash";
import config from '../configs'

import MainMenu from '../components/navigation/MainMenu'
import ToolbarUser from '../components/toolbar/ToolbarUser'

import ToastComponent from '~/components/ToastComponent'

export default {
  name: 'CrmLayout',
  components: {
    MainMenu,
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
      statuses: [{
        id: 1,
        name: 'Онлайн',
        icon: 'mdi-circle',
        color: 'success',
      }, {
        id: 0,
        name: 'Отошёл',
        icon: 'mdi-circle',
        color: 'error',
      }],

      headers: [
        {
          text: 'Дата',
          align: 'center',
          sortable: false,
          value: 'created_at',
          width: '70px'
        },
        {text: 'Клиент', value: 'client', width: '60px'},
        {text: 'Телефон', value: 'phone', width: '60px'},
        {text: 'Комментарий', value: 'comment', width: '90px'},
        {text: 'Оператор', value: 'operator', width: '60px'}
      ],
      items: [
        {
          action: 'mdi-web',
          active: true,
          items: [
            {title: 'Сайты', link: '/admin/sites'},
            {title: 'Черный список', link: '/admin/blacklist'},
            {title: 'Марки', link: '/admin/marks'},
            {title: 'Модели', link: '/admin/models'},
          ],
          title: 'Шоурум',
        },
        {
          action: 'mdi-account-multiple',
          items: [
            {title: 'Список пользователей', link: '/users'},
            {title: 'История входов и выходов', link: '/admin/auth-logs'},
          ],
          title: 'Пользователы',
        },
        {
          action: 'mdi-account-box',
          active: false,
          items: [
            {title: 'Комфорт', link: '/managers/2'},
            {title: 'АвтоПремиум', link: '/managers/4'},
            {title: 'Авангард Юг', link: '/managers/5'},
            {title: 'Форсаж', link: '/managers/7'},
            {title: 'АвтоПремьер', link: '/managers/8'},
          ],
          title: 'Менеджеры',
        },
      ],
    }
  },
  computed: {
    ...mapState('app', ['product', 'menuTheme', 'isToolbarDetached']),
    drawerState: {
      get() {
        return this.$store.state.user.drawerState
      },
      set(v) {
        return this.$store.commit('user/toggleDrawerState', v)
      }
    },
    all_arrivals() {
      return this.$store.state.order.all_arrivals
    },
    my_arrivals() {
      return this.all_arrivals.filter(l => l.operator_id == this.$auth.user?.id)
    },
    missed_calls() {
      return this.$store.state.order.missed_calls
    },
    order_items() {
      if (this.status_id === 'urgent') {
        return orderBy(this.urgent, ['callback'], ['asc']);
      } else if (this.status_id === 'myUrgent') {
        return orderBy(this.myUrgent, ['callback'], ['asc']);
      } else if (this.status_id === 'myNew') {
        return this.myNew;
      } else if (this.status_id === 'all') {
        return this.all;
      } else if (this.status_id === 'fired') {
        return orderBy(this.fired, ['callback'], ['asc']);
      } else if (this.status_id === 'myFired') {
        return orderBy(this.myFired, ['callback'], ['asc']);
      } else if (this.status_id === 'partnerUrgent') {
        return orderBy(this.partnerUrgent, ['callback'], ['asc']);
      } else if (this.status_id === 'not_arrived') {
        return this.not_arrived
      } else if (this.status_id === 'noAnswer') {
        return this.noAnswer
      } else if (this.status_id === 'MyNoAnswer') {
        return this.MyNoAnswer
      } else if (this.status_id === 'arrivals') {
        return this.all_arrivals
      } else if (this.status_id === 'my_arrivals') {
        return this.all_arrivals.filter(l => l.operator_id == this.$auth.user?.id)
      } else if (this.status_id === 'missed_calls') {
        return []
      } else if (this.status_id === null) {
        return this.todayOrders
      } else if (this.status_id === 5) {
        return this.todayOrders.filter(
          l => l.status_id === this.status_id || this.$moment().format('YYYY-MM-DD') === l.will_arrive
        )
      } else if (this.status_id === 'all') {
        return this.todayOrders.filter(l => l.status_id === this.status_id)
      } else if (this.status_id === 1) {

        return this.todayOrders.filter(
          l => l.status_id === this.status_id
        )
      } else {
        return this.todayOrders.filter(
          l => l.status_id == this.status_id
        )
      }
    },
    work_places() {
      return this.$store.state.operator.work_places
    },
    todayOrders() {
      return this.$store.state.order.all_orders
    },
    urgent() {
      return this.todayOrders.filter(
        l => this.$moment().diff(this.$moment(l.callback), 'minutes') <= 0 && this.$moment().diff(this.$moment(l.callback), 'minutes') >= -60)
    },
    myUrgent() {
      return this.todayOrders.filter(
        l => this.$moment().diff(this.$moment(l.callback), 'minutes') <= 0 && this.$moment().diff(this.$moment(l.callback), 'minutes') >= -60 && l.operator_id === this.$auth.user?.id)
    },
    partnerUrgent() {
      return this.todayOrders.filter(
        l => this.$moment().diff(this.$moment(l.callback), 'minutes') <= 0 && this.$moment().diff(this.$moment(l.callback), 'minutes') >= -60 && l.operator_id === this.$auth.user?.partner_id)
    },
    fired() {
      const res = this.todayOrders.filter(
        l => (this.$moment().isAfter(this.$moment(l.callback)) && this.$moment(l.callback).format('YYYY-DD-MM') === this.$moment().format('YYYY-DD-MM'))
      )
      return res.sort(function (left, right) {
        const da = new Date(left.callback);
        const db = new Date(right.callback);
        return da - db;
      });
    },
    myFired() {
      const res = this.todayOrders.filter(
        l => (this.$moment().isAfter(this.$moment(l.callback)) && this.$moment(l.callback).format('YYYY-DD-MM') === this.$moment().format('YYYY-DD-MM') && l.operator_id === this.$auth?.user.id)
      )
      return res.sort(function (left, right) {
        const da = new Date(left.callback);
        const db = new Date(right.callback);
        return da - db;
      });
    },
    not_arrived() {
      return this.todayOrders.filter(
        l => (l.will_arrive !== null && l.will_arrive !== 1 && this.$moment().isSame(l.will_arrive, 'day'))
      )
    },
    noAnswer() {
      return this.todayOrders.filter(
        l => ((l.status_id === 7 && l.trash_id === 25) || l.status_id === 3)
      )
    },
    MyNoAnswer() {
      return this.todayOrders.filter(
        l => ((l.status_id === 7 && l.trash_id === 25 && l.operator_id === this.$auth?.user.id) || (l.status_id === 3 && l.operator_id === this.$auth?.user.id))
      )
    },
    myNew() {
      return this.todayOrders.filter(
        l => l.status_id === 1 && l.operator_id == this.$auth.user?.id
      )
    },
    all() {
      return this.todayOrders.filter(
        l => l.status_id === this.status_id
      )
    },
    sites() {
      return this.$store.state.showroom.sites
    }
  },
  created() {
    this.$store.dispatch('operator/fetchWorkPlaces', {id: this.$auth.user?.showroom_id})
    this.work_place = this.$auth.user?.work_place?.toString() || null
    console.log(typeof (this.$auth.user?.work_place))
    this.online = this.$auth.user?.online

    if ([1, 2, 3, 6].includes(this.$auth.user?.role_id)) {
      this.$echo.channel('calling_' + this.$auth.user?.showroom_id)
        .listen('MangoIncome', (e) => {
          const  arr = ["777", "778"];
          if (arr.includes(e.response?.operator?.work_place)) {
            console.log(e)
            const toastContent = {
              component: ToastComponent,
              props: {
                data: e.response,
                sites: this.sites,
              }
            }
            this.$toast.clear();
            this.$toast(toastContent, {
              position: "bottom-right",
              timeout: 70000,
              closeOnClick: false,
              pauseOnFocusLoss: false,
              pauseOnHover: false,
              draggable: false,
              draggablePercent: 0.6,
              showCloseButtonOnHover: false,
              hideProgressBar: true,
              closeButton: false,
              icon: false,
              rtl: false,
              toastClassName: "call_info",
            });

          }

        })

    this.$echo.channel('clear_' + this.$route.params?.id)
      .listen('ClearNotify', (e) => {
        console.log(e)
        this.$toast.clear();
      })
    }
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
      this.$axios.post('/crm/workplace', {work_place: this.work_place})
    },
    changeOnline() {
      this.$axios.post('/user/change-online')
    },
    getCount(statusId, my = null) {
      let result = 0
      if (my) {
        result = this.todayOrders.filter(
          l => l.status_id === statusId && l.operator_id === this.$auth.user?.id
        )
        return result.length
      }
      if (statusId === 5) {
        result = this.todayOrders.filter(
          l => (
            l.status_id === statusId || this.$moment().format('YYYY-MM-DD') === l.will_arrive
          )
        )
        return result.length
      }
      result = this.todayOrders.filter(
        l => l.status_id == statusId
      )
      return result.length
    },
    row_classes(item) {
      if (item.status_id === 1 && item.type_id === 21) {
        return 'teal white--text'
      }
      else if (this.status_id === 1) {
        return 'teal white--text'
      } else if (this.status_id === 4) {
        return 'green white--text'
      } else if (this.status_id === 2) {
        return 'teal white--text'
      } else if (this.status_id === 12) {
        return 'blue lighten-2 white--text'
      } else if (item?.status_id === 12 && this.status_id === 'urgent') {
        return 'blue lighten-2 white--text'
      } else if (item?.status_id === 12 && this.status_id === 'myUrgent') {
        return 'blue lighten-2 white--text'
      } else if (this.$moment().format('YYYY-MM-DD') == item?.will_arrive) {
        return 'pink darken-4 white--text'
      } else if (this.$moment().add(1, 'days').format('YYYY-MM-DD') === item?.will_arrive) {
        return 'success  white--text'
      } else if (this.status_id === 'myFired' && this.$moment().format('YYYY-MM-DD') != item?.will_arrive && item?.status_id === 12) {
        return 'blue lighten-2 white--text'
      } else if (this.status_id === 'fired' && this.$moment().format('YYYY-MM-DD') != item?.will_arrive && item?.status_id === 12) {
        return 'blue lighten-2 white--text'
      } else if (this.status_id === 'fired' && this.$moment().format('YYYY-MM-DD') != item?.will_arrive) {
        return 'red white--text'
      } else if (this.status_id === 'urgent' || this.status_id === 'myUrgent') {
        return 'orange white--text'
      } else if (this.status_id === 'arrivals') {
        return 'purple white--text'
      } else if (this.status_id === 'not_arrived') {
        return 'blue lighten-2 white--text'
      } else if (this.status_id === 'missed_calls') {
        return 'red lighten-2 white--text'
      } else if (this.status_id === 'noAnswer') {
        return 'black lighten-2 white--text'
      } else if (this.status_id === 5) {
        return 'green white--text'
      } else if (this.status_id === 6) {
        return 'purple white--text'
      } else {
        return 'grey lighten-2'
      }
    },
    redirect(item) {
      this.$router.push('/crm/' + item.showroom_id + '/order/' + item.id + '/edit')
    },
    toggle() {
      this.drawer = !this.drawer
    },
    notify() {
      this.$notification.show('Уведомление', {
        body: 'Добавлено новая заявка!'
      }, {})
    }

  }
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

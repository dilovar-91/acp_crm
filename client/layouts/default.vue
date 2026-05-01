<template>
  <v-app>
    <div
      v-shortkey="['ctrl', '/']"
      class="d-flex flex-grow-1"
      @shortkey="onKeyup"
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
              <nuxt-link class="text-decoration-none ml-3" to="/">{{ product.name }}</nuxt-link>
            </div>
            <div v-if="false" class="overline grey--text">
              <nuxt-link class="text-decoration-none" to="/">{{ product.version }}</nuxt-link>
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
            :prepend-icon="item.action"
            no-action
          >
            <template #activator >
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
                <v-icon>mdi-cash-usd-outline</v-icon>
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
              <div class="d-flex justify-space-around">
                <v-icon light class="mr-2 hidden-sm-and-down">
                  mdi-clock-outline
                </v-icon>
                <span class="hidden-sm-and-down font-weight-bold ml-auto">{{ timestamp }}</span>
              </div>

              <v-spacer class="d-block d-sm-none"></v-spacer>
              <v-btn class="d-block d-sm-none" icon @click="showSearch = true">
                <v-icon dark>mdi-magnify</v-icon>
              </v-btn>
              <toolbar-user/>
            </div>
          </div>
        </v-card>
      </v-app-bar>

      <v-main>
        <nuxt/>
        <v-footer app inset>
          <v-spacer></v-spacer>
          <div v-role-or-permission="'admin|see_dashboard'" class="overline">
            Built with
            <v-icon small color="pink">mdi-heart</v-icon>
          </div>
        </v-footer>
      </v-main>
    </div>
  </v-app>
</template>

<script>
import {mapState} from 'vuex'

// navigation menu configurations
import config from '../configs'

import MainMenu from '../components/navigation/MainMenu'
import ToolbarUser from '../components/toolbar/ToolbarUser'


export default {
  name: 'DefaultLayout',
  components: {
    MainMenu,
    ToolbarUser,
  },
  data() {
    return {
      drawer: null,
      showSearch: false,
      timestamp: this.$moment().format('LLLL:ss'),
      navigation: config.navigation,
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
          action: 'mdi-account-circle',
          active: true,
          items: [
            {title: 'Р1', link: '/admin/operators/4'},
            {title: 'Р2', link: '/admin/operators/2'},
            {title: 'Р3', link: '/admin/operators/10'},
            {title: 'К1', link: '/admin/operators/5'},
            {title: 'В', link: '/admin/operators/7'},
            {title: 'С1', link: '/admin/operators/15'},
            {title: 'С2', link: '/admin/operators/17'},

          ],
          title: 'Операторы',
        },
        {
          action: 'mdi-account-box',
          active: true,
          items: [
            {title: 'Комфорт', link: '/managers/2'},
            {title: 'АвтоПремиум', link: '/managers/4'},
            {title: 'Авангард Юг', link: '/managers/5'},
            {title: 'Форсаж', link: '/managers/7'},
          ],
          title: 'Менеджеры',
        },
      ],
    }
  },
  computed: {
    ...mapState('app', ['product', 'isContentBoxed', 'menuTheme', 'toolbarTheme', 'isToolbarDetached']),
    drawerState: {
      get() {
        return this.$store.state.user.drawerState
      },
      set(v) {
        return this.$store.commit('user/toggleDrawerState', v)
      }
    },
  },
  mounted() {
    setInterval(this.getNow, 1000)
  },
  methods: {
    onKeyup(e) {
      this.$refs.search.focus()
    },
    getNow() {
      this.timestamp = this.$moment().format('LLLL:ss')
    },
  },
}
</script>

<style scoped>
.buy-button {
  box-shadow: 1px 1px 18px #e4a;
}

.custom-icon-color {
  color: rgb(76, 175, 80); /* Use the desired color from your Vuetify theme or a custom color */
}
</style>

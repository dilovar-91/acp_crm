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


            <div class="d-flex flex-grow-1 align-center">
              <v-app-bar-nav-icon @click.stop="drawerState = !drawerState"></v-app-bar-nav-icon>


              <div v-if="drawerState===false" class="title font-weight-bold text-uppercase primary--text">
                <nuxt-link class="text-decoration-none ml-3" to="/">{{ product.name }}</nuxt-link>
              </div>
              <v-spacer class="d-none d-lg-block"></v-spacer>


<toolbar-user/>
</div>
</div>
</v-card>
</v-app-bar>

<v-main>



<nuxt/>
<v-footer v-if="false" app inset>
  <v-spacer></v-spacer>
  <div class="overline">
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

  created() {
    this.$store.dispatch('operator/fetchWorkPlaces', {id: this.$auth.user?.showroom_id})
    this.work_place = this.$auth.user?.work_place?.toString() || null
    console.log(typeof (this.$auth.user?.work_place))
    this.online = this.$auth.user?.online

    if ([1, 2, 3, 6].includes(this.$auth.user?.role_id)) {
      this.$echo.channel('calling_' + this.$auth.user?.showroom_id)
        .listen('MangoIncome', (e) => {
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
        })
      this.$echo.channel('clear_' + this.$route.params?.id)
        .listen('ClearNotify', (e) => {
          console.log(e)
          this.$toast.clear();
        })
    }
  },

  methods: {
    clickStatus(statusId, isCount = false, isOpen=false) {
      if (isCount) {
        this.getCount(statusId)
      } else this.changeStatus(statusId)
      if (isOpen) {
        this.drawer = isOpen
      }
    },
    changeStatus (status) {
      this.status_id = status
    },
    changeWorkPlace() {
      this.$axios.post('/crm/workplace', {work_place: this.work_place})
    },
    changeOnline() {
      this.$axios.post('/user/change-online')
    },
    getCount(statusId, my=null) {
      let result  = 0
      if (my){
        result = this.todayOrders.filter(
          l => l.status_id === statusId && l.operator_id === this.$auth.user?.id
        )
        return result.length
      }
      if(statusId === 5) {
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
    row_classes () {
      if (this.status_id === 1) {
        return 'teal white--text' // can also return multiple classes e.g ["orange","disabled"]
      }else if(this.status_id === 4) {
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
    redirect (item) {
      this.$router.push('/crm/'+item.showroom_id+'/order/'+item.id + '/edit')

    },
    toggle () {
      this.drawer = !this.drawer
    },
    notify () {
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
.word-wp{
  word-break: break-word;
}
</style>

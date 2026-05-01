<template>
  <div>
    <v-navigation-drawer
      v-model="drawer"
      width="850px"
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
          @click="clickStatus('all')"
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
          color="teal"
          text-color="white"
          @click="clickStatus(1)"
        >
          Новые
          <v-avatar
            right
            class="orange"
          >
            {{ getCount(1, true) }}
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
          color="green"
          text-color="white"
          @click="clickStatus(5)"
        >
          Приедет
          <v-avatar
            right
            class="orange"
          >
            {{ getCount(5) }}
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
          color="blue"
          text-color="white"
          @click="clickStatus('myFired')"
        >
          Сгоревшые напарника
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
          color="orange"
          text-color="white"
          @click="clickStatus('partnerUrgent')"
        >
          Срочные напарника
          <v-avatar
            right
            class="green "
          >
            {{ myUrgent.length }}
          </v-avatar>
        </v-chip>
      </div>

      <v-data-table
        :headers="headers"
        :items="items"
        :items-per-page="items.length"
        class="elevation-1"
        hide-default-footer
        fixed-header
      >
        <template
          #body="{ items }"
        >
          <tbody>
          <tr
            v-for="item in items"
            :key="item.id"
            :class="row_classes(item)"
            @dblclick="redirect(item)"
          >
            <td>
              <nuxt-link :to="'/crm/'+item.showroom_id+'/order/'+item.id + '/edit'" :class="row_classes(item)">
                <span v-if="status_id==='fired'">{{ $moment(item.callback).format('HH:mm') }}</span>
                <span v-else-if="status_id==='urgent'">{{ ($moment().diff($moment(item.callback), 'minutes')) * -1 }} минут</span>
                <span v-else>{{ $moment(item.created_at).format('DD.MM.YY HH:mm') }}</span>
              </nuxt-link>
            </td>
            <td>{{ item.client_name }}</td>
            <td>{{ item.phone }} {{ item.callback }}</td>
            <td>
              <v-tooltip bottom max-width="400px" color="primary">
                <template #activator="{ on, attrs }">
                  <div color="primary" dark v-bind="attrs" v-on="on">
                    {{ item.comment | truncate(120) }}
                  </div>
                </template>
                <span>{{ item.comment }}</span>
              </v-tooltip>

            </td>
            <td>{{ item.operator && item.operator.first_name }} {{ item.operator && item.operator.last_name }}</td>
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
  </div>
</template>

<script>
import {orderBy} from "lodash";

export default {
  data: () => ({
    today: '',
    status_id: null,
    drawer: false,
    group: null,
    headers: [
      {
        text: 'Дата',
        align: 'center',
        sortable: false,
        value: 'created_at',
        width: '60px'
      },
      {text: 'Клиент', value: 'client', width: '90px'},
      {text: 'Телефон', value: 'phone', width: '90px'},
      {text: 'Комментарий', value: 'comment', width: '90px'},
      {text: 'Оператор', value: 'operator', width: '90px'}
    ]
  }),

  computed: {
    items() {
      if (this.status_id === 'urgent') {
        return orderBy(this.urgent, ['callback'], ['asc']);
      } else if (this.status_id === 'myUrgent') {
        return orderBy(this.myUrgent, ['callback'], ['asc']);
      } else if (this.status_id === 'fired') {
        return orderBy(this.fired, ['callback'], ['asc']);
      } else if (this.status_id === 'myFired') {
        return orderBy(this.myFired, ['callback'], ['asc']);
      } else if (this.status_id === 'partnerUrgent') {
        return orderBy(this.partnerUrgent, ['callback'], ['asc']);
      } else if (this.status_id === 'not_arrived') {
        return this.not_arrived
      } else if (this.status_id === null) {
        return this.todayOrders
      } else if (this.status_id === 5) {
        return this.todayOrders.filter(
          l => l.status_id === this.status_id || this.$moment().format('YYYY-MM-DD') === l.will_arrive
        )
      } else if (this.status_id === 'all') {
        return this.todayOrders
      } else if (this.status_id === 1) {
        return this.todayOrders.filter(
          l => l.status_id === this.status_id && l.operator_id === this.$auth?.user.id
        )
      } else {
        return this.todayOrders.filter(
          l => l.status_id === this.status_id
        )
      }
    },
    todayOrders() {
      return this.$store.state.order.all_orders
    },
    urgent() {
      let res = this.todayOrders.filter(
        l => this.$moment().diff(this.$moment(l.callback), 'minutes') <= 0 && this.$moment().diff(this.$moment(l.callback), 'minutes') >= -60)
      return orderBy(res, 'id', 'desc');
    },
    myUrgent() {
      let res = this.todayOrders.filter(
        l => this.$moment().diff(this.$moment(l.callback), 'minutes') <= 0 && this.$moment().diff(this.$moment(l.callback), 'minutes') >= -60 && l.operator_id === this.$auth?.user.id)
      return orderBy(res, 'id', 'desc');
    },
    partnerUrgent() {
      return this.todayOrders.filter(
        l => this.$moment().diff(this.$moment(l.callback), 'minutes') <= 0 && this.$moment().diff(this.$moment(l.callback), 'minutes') >= -60 && l.operator_id === this.$auth.user?.partner_id)
    },
    fired() {
      return this.todayOrders.filter(
        l => (this.$moment().isAfter(this.$moment(l.callback)) && this.$moment(l.callback).format('YYYY-DD-MM') === this.$moment().format('YYYY-DD-MM'))
      )
    },
    myFired() {
      let result = this.todayOrders.filter(
        l => (this.$moment().isAfter(this.$moment(l.callback)) && this.$moment(l.callback).format('YYYY-DD-MM') === this.$moment().format('YYYY-DD-MM') && l.operator_id === this.$auth?.user.id)
      )
      return result.sort((a, b) => this.$moment(a.callback).format('YYYY-MM-DD') - this.$moment(b.callback).format('YYYY-MM-DD'))
    }
  },
  watch: {
    group() {
      this.drawer = false
    }
  },

  mounted() {
    this.$echo.channel('orders')
      .listen('OrderProcessed', (e) => {
        console.log('куку')
        this.$apollo.queries.todayOrders.refetch()
      })
    this.$echo.channel('orders')
      .listen('OrderCreated', (e) => {
        this.notify()
      })
  },


  methods: {
    clickStatus(statusId, isCount = false) {
      if (isCount) {
        this.getCount(statusId)
      } else this.changeStatus(statusId)
    },
    changeStatus(status) {
      this.status_id = status
    },
    row_classes(item) {
      if (item.status_id === 1 && item.type_id === 21) {
        return 'teal white--text'
      } else if (this.status_id === 1) {
        return 'green white--text'
      } else if (this.status_id === 2) {
        return 'teal white--text'
      } else if (this.status_id === 'fired') {
        return 'red white--text'
      } else if (this.status_id === 'urgent') {
        return 'orange white--text'
      } else if (this.status_id === 'not_arrived') {
        return 'blue lighten-2 white--text'
      } else if (this.status_id === 5) {
        return 'primary white--text'
      } else if (this.status_id === 6) {
        return 'primary white--text'
      } else {
        return 'grey lighten-2'
      }
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
        l => (
          l.status_id === statusId)
      )
      return result.length
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
<style>
.light::-webkit-scrollbar {
  width: 15px;
}

.light::-webkit-scrollbar-track {
  background: #e6e6e6;
  border-left: 1px solid #dadada;
}

.light::-webkit-scrollbar-thumb {
  background: #b0b0b0;
  border: solid 3px #e6e6e6;
  border-radius: 7px;
}

.light::-webkit-scrollbar-thumb:hover {
  background: black;
}

</style>

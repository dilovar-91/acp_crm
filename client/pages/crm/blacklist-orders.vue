<template>
  <div>
    <BreadCrumb :items="links"/>
    <v-container fluid class="pt-0">
      <v-row no-gutters align="start" class="d-flex">
        <v-col cols="12">
          <v-card class="mx-auto pt-0">
            <v-btn v-model="isFilter" text color="primary" x-small @click="isFilter = !isFilter">Фильтр</v-btn>
            <v-app-bar v-if="isFilter" class="" elevate-on-scroll dense>
              <v-row class="ml-1 mt-2">
                <v-col cols="12" sm="6" md="1" class="hidden-sm-and-down">
                  <date-picker
                    v-model="filter_from"
                    value-type="YYYY-MM-DD HH:mm"
                    format="DD.MM.Y HH:mm"
                    type="datetime"
                    placeholder="Дата с"
                    style="width: 100%; margin-top: 4px;"
                    @clear="clearFilter()"
                  />
                </v-col>
                <v-col cols="12" sm="6" md="1" class="hidden-sm-and-down">
                  <date-picker
                    v-model="filter_to"
                    type="datetime"
                    value-type="YYYY-MM-DD HH:mm"
                    format="DD.MM.Y HH:mm"
                    placeholder="Дата по"
                    style="width: 100%; margin-top: 4px;"
                    @clear="clearFilter()"
                  />
                </v-col>
                <v-col cols="12" sm="6" xl="2" md="2" class="hidden-sm-and-down mt-1">
                  <v-text-field
                    v-model="search" clearable label="Поиск" hide-details outlined
                    dense @keyup.enter="doSearch()"
                  >
                  </v-text-field>
                </v-col>
                <v-col cols="12" sm="6" xl="2" md="2" class="hidden-sm-and-down mt-1">
                  <v-select
                    v-model="filter_site"
                    :items="sites"
                    hide-details
                    item-text="title"
                    item-value="id"
                    label="Сайт"
                    menu-props="auto"
                    style="width: 100%;"
                    outlined
                    clearable
                    required
                    multiple
                    dense
                    @click:clear="
                          $nextTick(() => (clearFilter()))
                        "
                  >
                    <template #selection="{ item, index }">
                      <template v-if="index === 0">
                        <span>{{ $truncate(item.title, 10) }}</span>
                      </template>
                      <span
                        v-if="index === 1"
                        class="grey--text text-caption"
                      >
                           &nbsp;(+{{ filter_site.length - 1 }})
                          </span>
                    </template>
                  </v-select>
                </v-col>
                <v-col cols="12" sm="6" xl="1" md="1" class="hidden-sm-and-down mt-1">
                  <v-select
                    v-model="filter_type"
                    :items="types"
                    hide-details
                    item-text="name"
                    item-value="id"
                    label="Тип заявки"
                    menu-props="auto"
                    style="width: 100%;"
                    outlined
                    clearable
                    required
                    multiple
                    dense
                    @click:clear="
                          $nextTick(() => (clearFilter()))
                        "
                  >
                    <template #selection="{ item, index }">
                      <template v-if="index === 0">
                        <span>{{ $truncate(item.name, 10) }}</span>
                      </template>
                      <span
                        v-if="index === 1"
                        class="grey--text text-caption"
                      >
                           &nbsp;(+{{ filter_type.length - 1 }})
                          </span>
                    </template>
                  </v-select>
                </v-col>

                <v-col cols="12" sm="6" md="1" class="hidden-sm-and-down mt-1">
                  <v-select
                    v-model="filter_status"
                    :items="statuses"
                    hide-details
                    item-text="name"
                    item-value="id"
                    label="Статус"
                    menu-props="auto"
                    style="width: 120%;"
                    outlined
                    clearable
                    required
                    dense
                    @click:clear="
                          $nextTick(() => (clearFilter()))
                        "
                  >

                  </v-select>
                </v-col>

                <v-col cols="12" sm="6" xl="1" md="1" class="hidden-sm-and-down mt-1 ">
                  <v-checkbox
                    v-model="advanced_arrived"
                    label="Приезд потв."
                    outlined
                    dense
                    :size="'small'"
                  />
                </v-col>
                <v-col cols="12" sm="6" xl="1" md="1" class="hidden-sm-and-down mt-1 ">
                  <v-checkbox
                    v-model="not_confirmed"
                    label="Не потв."
                    outlined
                    dense
                    :size="'small'"
                  />
                </v-col>

                <v-col cols="12" sm="1" md="1" class="hidden-sm-and-down">
                  <v-btn small color="success" dark class="mb-2 mt-2" @click="doSearch()">
                    Применить
                  </v-btn>
                </v-col>
                <v-col cols="12" sm="1" md="1" class="hidden-sm-and-down">
                  <v-btn small color="error" dark class="mb-2 mt-2" @click="clearFilter()">
                    Сбросить
                  </v-btn>
                </v-col>


              </v-row>
              <v-btn v-if="false" icon color="green" @click="exportFile()">
                <v-icon large>mdi-file-excel</v-icon>
              </v-btn>
              <v-menu
                v-if="filter_status !== 7"
                v-model="menu"
                :close-on-content-click="false"
                :nudge-width="200"
                offset-x

              >
                <template #activator="{ on, attrs }">
                  <v-btn color="indigo" dark v-bind="attrs" v-on="on">
                    <v-icon class="mr-2">
                      mdi-menu
                    </v-icon>
                    Меню
                  </v-btn>
                </template>

                <v-card>
                  <v-list>
                    <v-list-item>
                      <v-list-item-action>
                        <v-btn
                          v-if="showroom.id || showroom_id === 16"
                          color="primary"
                          dark
                          class="mb-2"
                          @click="dialog = true"
                        >
                          Добавить
                        </v-btn>
                      </v-list-item-action>
                    </v-list-item>


                    <v-list-item>
                      <v-list-item-action>
                        <v-btn
                          color="primary"
                          dark
                          class="mb-2"
                          :to="'/crm/' + showroom_id + '/reports'"
                        >
                          Отчёты
                        </v-btn>
                      </v-list-item-action>
                    </v-list-item>


                    <v-list-item>
                      <v-list-item-action>
                        <v-btn
                          color="primary"
                          dark
                          class="mb-2"
                          @click="exportDialog = true"
                        >
                          Скачать НО
                        </v-btn>
                      </v-list-item-action>
                    </v-list-item>

                    <v-list-item class="hidden-md-and-up">
                      <v-list-item-action>
                        <date-picker
                          v-model="filter_from"
                          value-type="YYYY-MM-DD"
                          format="DD.MM.Y"
                          placeholder="Дата с"
                        />
                      </v-list-item-action>
                    </v-list-item>
                    <v-list-item class="hidden-md-and-up">
                      <v-list-item-action>
                        <date-picker
                          v-model="filter_to"
                          value-type="YYYY-MM-DD HH:mm"
                          format="DD.MM.Y HH:mm"
                          type="datetime"
                          placeholder="Дата по"
                        />
                      </v-list-item-action>
                    </v-list-item>
                    <v-list-item class="hidden-md-and-up">
                      <v-list-item-action>
                        <v-btn
                          color="success"
                          dark
                          class="mb-2"
                          @click="doSearch()"
                        >
                          Применить
                        </v-btn>
                      </v-list-item-action>
                    </v-list-item>
                    <v-list-item class="hidden-md-and-up">
                      <v-list-item-action>
                        <v-btn
                          color="error"
                          dark
                          class="mb-2"
                          @click="clearFilter()"
                        >
                          Сбросить
                        </v-btn>
                      </v-list-item-action>
                    </v-list-item>
                  </v-list>
                  <v-card-actions>
                    <v-spacer/>

                    <v-btn
                      color="primary"
                      text
                      class="hidden-sm-and-down"
                      @click="menu = false"
                    >
                      Закрыть
                    </v-btn>
                  </v-card-actions>
                </v-card>
              </v-menu>
            </v-app-bar>

            <v-card-text class="pa-0 py-0">
              <template v-if="!isSearch">
                <v-btn
                  :color="(filter_status==1 ? 'green darken-1' : 'darken-1')"
                  text
                  @click="changeStatus(1)"
                >
                  Новая
                </v-btn>
                <v-btn
                  :color="(filter_status==2 ? 'green darken-1' : 'darken-1')"
                  text
                  @click="changeStatus(2)"
                >
                  В работе
                </v-btn>
                <v-btn
                  :color="(filter_status==3 ? 'green darken-1' : 'darken-1')"
                  text
                  @click="changeStatus(3)"
                >
                  Не отвечает
                </v-btn>
                <v-btn
                  :color="(filter_status==4 ? 'green darken-1' : 'darken-1')"
                  text
                  @click="changeStatus(4)"
                >
                  Одобрить
                </v-btn>
                <v-btn
                  :color="(filter_status==5 ? 'green darken-1' : 'darken-1')"
                  text
                  @click="changeStatus(5)"
                >
                  Приедет
                </v-btn>
                <v-btn
                  :color="(filter_status==6 ? 'green darken-1' : 'darken-1')"
                  text
                  @click="changeStatus(6)"
                >
                  Приехал
                </v-btn>
                <v-btn
                  :color="(filter_status==7 ? 'green darken-1' : 'darken-1')"
                  text
                  @click="changeStatus(7)"
                >
                  Корзина
                </v-btn>
                <v-btn
                  :color="(filter_status==8 ? 'green darken-1' : 'darken-1')"
                  text
                  @click="changeStatus(8)"
                >
                  Повторы
                </v-btn>
                <v-btn
                  :color="(filter_status==null ? 'green darken-1' : 'darken-1')"
                  text
                  @click="changeStatus()"
                >
                  ВСЕ
                </v-btn>
              </template>
              <v-data-table
                :headers="headers"
                :items="orders.data"
                :single-select="false"
                hide-default-footer
                :items-per-page="perPage"
                :page.sync="page"
                :show-select="false"
                fixed-header
                dense
                class="elevation-1 myTable"
              >
                <template #header.retries="">
                  <div @click="sortRepeat()">Повторы</div>
                </template>
                <template
                  #body="{ items }"
                >
                  <tbody>
                  <tr
                    v-for="item in items"
                    :key="item.id"
                  >
                    <td>
                      <nuxt-link
                        :to="'/crm/' + item.showroom_id + '/order/'+item.id + '/edit'"
                      >

                        <template v-if="item.type_id === 12">
                          WhatsApp
                        </template>
                        <template v-else-if="item.site">
                          {{ item.site?.title }}

                          <v-chip
                            v-if="item.copied_from"
                            class="ma-2"
                            color="success"
                          >
                            Передано с {{ showrooms.find(sh => sh.id === item.copied_from)?.name }}
                          </v-chip>
                        </template>
                        <template v-else-if="item.line_number">
                          {{ item.line_number }}
                        </template>
                        <template v-else-if="item.site_id === null &&  item.source_id > 0">
                          {{ item.source?.name }}
                        </template>
                        <template v-else>
                          Не определено
                        </template>
                      </nuxt-link>

                      <template v-if="item.copied_to">
                        <v-chip
                          v-for="(salon, y) in item.copied_to" :key="'showroom__'+y" dark class="mr-1 mb-1"
                          :color="colors[salon]">
                          Передано в {{ showrooms.find(l => l.id === salon)?.name }}
                        </v-chip>
                      </template>
                    </td>
                    <td>{{ item.type?.name }}</td>
                    <td>
                      {{ item.status?.name }}
                      <p v-if="item.status_id === 7 && item.trash" style="color: orangered;">
                        ({{ item.trash?.name }})
                      </p>


                      <p v-if="item.status_id === 13 && item.drop" style="color: orangered;">
                        ({{ item.drop?.name }})
                      </p>

                    </td>
                    <td>
                      {{ item.operator?.last_name }} {{ item.operator?.first_name }}
                    </td>
                    <td>
                      {{ item.region?.name }}
                    </td>
                    <td>
                      {{ $moment(item.created_at).format('DD.MM.YYYY HH:mm') }}
                    </td>

                    <td>{{ item.client_name }}</td>
                    <td>{{ item.phone | mask('+7 ### ###-##-##') }}</td>
                    <td><a class="font-weight-bold" @click.prevent="openRepeat(item)">{{ item.retries }}</a></td>
                    <td>
                      {{ $moment(item.updated_at).format('DD.MM.YYYY HH:mm') }}
                    </td>

                    <td>
                      <v-tooltip bottom max-width="400px" color="primary">
                        <template #activator="{ on, attrs }">
                          <div color="primary" dark v-bind="attrs" v-on="on">
                            {{ item.comment | truncate(180) }}
                          </div>
                        </template>
                        <span>{{ item.comment }}</span>
                      </v-tooltip>
                    </td>
                    <td>
                      <v-chip
                        v-if="item.blacklist?.status_id === 1"
                        class="ma-1"
                        color="success"
                        small
                      >
                        Номер в черном списке
                      </v-chip>
                      <v-chip
                        v-else-if="item.blacklist?.status_id === 2"
                        class="ma-1"
                        color="error"
                        small
                      >
                        Отказано
                      </v-chip>
                      <v-chip
                        v-else
                        class="ma-1"
                        color="warning"
                        small
                      >
                        В рассмотрение
                      </v-chip>
                    </td>
                    <td>
                      <v-btn
                        icon
                        :disabled="item.blacklist?.status_id === 1 || role_id !== 1"
                        color="success"
                        @click="approveBlacklist(item.id, 1)"
                      >
                        <v-icon>mdi-check</v-icon>
                      </v-btn>
                      <v-btn
                        icon
                        color="error"
                        :disabled="item.blacklist?.status_id === 2  || role_id !== 1"
                        @click="approveBlacklist(item.id, 2)"
                      >
                        <v-icon>mdi-close</v-icon>
                      </v-btn>
                    </td>
                  </tr>
                  </tbody>
                </template>
              </v-data-table>
              <div class="text-center pt-2">
                <span class="font-weight-bold">Всего: {{ itemCount }}</span>

                <v-row dense justify="center">
                  <v-pagination
                    v-model="page"
                    color="blue"
                    :length="pageCount"
                    :total-visible="10"
                    @input="changedPage"
                  />
                </v-row>


              </div>
            </v-card-text>
          </v-card>
        </v-col>


        <v-dialog v-model="deleteDialog" max-width="290">
          <v-card>
            <v-card-title class="headline">
              Вы хотите удалить?
            </v-card-title>

            <v-card-text>
              После удаления вы не можете восстановить эту строку.
            </v-card-text>
            <v-card-actions>
              <v-spacer/>
              <v-btn color="green darken-1" text @click="deleteItem()">
                Да
              </v-btn>
              <v-btn color="green darken-1" text @click="deleteDialog = false">
                Нет
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-dialog v-model="repeatDialog" light max-width="850">
          <v-card flat style="background-color: #fdfdfd;" class="px-3">
            <p class="pt-4 text-center font-weight-bold">Заявка №{{ repeatItem.id }}: повторы</p>
            <template>
              <v-simple-table style="background-color: #fdfdfd;">
                <template #default>
                  <thead style="background-color: #eee;">
                  <tr>
                    <th class="text-center">
                      Номер
                    </th>
                    <th class="text-center">
                      Клиент
                    </th>
                    <th class="text-center">
                      Повторы
                    </th>
                    <th class="text-center">
                      Состояние заявки
                    </th>
                    <th class="text-center">
                      Дата создания
                    </th>
                    <th class="text-center">
                      Комментарий
                    </th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr
                    v-for="item in repeats"
                    :key="item.id"
                  >
                    <td>
                      <nuxt-link :to="'/crm/' + item.showroom_id + '/order/'+item.id + '/edit'">
                        {{ item.id }}
                      </nuxt-link>
                    </td>
                    <td>{{ item.client_name }}</td>
                    <td>{{ item.retries }}</td>
                    <td>{{ item.status?.name }}</td>
                    <td>{{ $moment(item.created_at).format('DD.MM.YYYY HH:mm:ss') }}</td>
                    <td>
                      <v-tooltip bottom max-width="400px" color="primary">
                        <template #activator="{ on, attrs }">
                          <div color="primary" dark v-bind="attrs" v-on="on">
                            {{ item.comment | truncate(220) }}
                          </div>
                        </template>
                        <span>{{ item.comment }}</span>
                      </v-tooltip>
                    </td>
                  </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </template>
          </v-card>
        </v-dialog>
      </v-row>
    </v-container>
  </div>
</template>
<script>
import _ from 'lodash';
import BreadCrumb from '~/components/BreadCrumb'

export default {
  name: 'CrmOrder',
  components: {BreadCrumb},
  beforeRouteLeave(to, from, next) {
    this.$store.dispatch('user/toggle', true)
    clearInterval(this.intervalid)
    next()
  },
  layout: 'crm',
  middleware: 'permission',
  data: () => ({
    menu: false,
    componentKey: 0,
    page: 1,
    dialog: false,
    limit: null,
    isFilter: true,
    toShowroom: null,
    repeatDialog: false,
    approveDialog: false,
    approveId: null,
    callback: null,
    will_arrive: null,
    last_call: null,
    oldCallback: null,
    oldLastCall: null,
    oldWillArrive: null,
    oldArrivedDate: null,
    repeats: [],
    isSearch: false,
    search: null,
    advanced_fio: null,
    campaign: null,
    timeout: null,
    mask: {
      mask: '{7} (000) 000-00-00',
      lazy: false
    },
    filter_from: null,
    filter_to: null,
    filter_site: null,
    filter_type: null,
    filter_operator: null,
    filter_status: null,
    filter_trash: null,
    status_id: null,

    repeat: null,
    // advaced filter
    advanced_search: null,
    advanced_operator: null,
    advanced_type: null,
    advanced_filter_type: null,
    advanced_site: null,
    advanced_status: [],
    advanced_trash: null,
    advanced_from: null,
    advanced_to: null,
    advanced_payment: null,
    advanced_region: [],
    advanced_phone: null,
    advanced_arrived: null,
    not_confirmed: null,
    advanced_mark: null,
    advanced_model: null,
    advanced_drop: null,
    menu2: false,
    menu4: false,
    isLoading: false,
    valid: false,
    lastUpdate: null,
    onProcess: false,
    // deleteDialog
    deleteId: '',
    deleteDialog: false,
    exportDialog: false,
    export_from: null,
    export_to: null,
    nameRules: [v => !!v || 'Введитие ФИО клиента'],
    dateRules: [v => !!v || 'Выберите дату'],
    colors: ['green', 'purple', 'orange', 'indigo', 'red', 'blue', 'success', 'red', 'lime'],
    headers: [
      {
        text: 'Сайт',
        align: 'center',
        width: '120px',
        value: 'id'
      },
      {
        text: 'Тип',
        align: 'center',
        sortable: false,
        width: '60px',
        value: 'type.name'
      },
      {
        text: 'Состояние заявки',
        align: 'center',
        sortable: false,
        width: '90px',
        value: 'order.status'
      },
      {
        text: 'Оператор',
        align: 'center',
        sortable: false,
        width: '80px',
        value: 'operator.first_name'
      },
      {
        text: 'Регион',
        align: 'center',
        sortable: false,
        width: '80px',
        value: 'region.name'
      },
      {
        text: 'Дата создания',
        align: 'center',
        sortable: false,
        width: '90px',
        value: 'created_at'
      },
      {
        text: 'Клиент',
        align: 'center',
        sortable: false,
        width: '140px',
        value: 'client_name'
      },
      {
        text: 'Сотовый',
        align: 'center',
        sortable: false,
        width: '140px',
        value: 'phone'
      },
      {
        text: 'Повторы',
        align: 'center',
        sortable: true,
        width: '8px',
        value: 'retries'
      },
      {
        text: 'Дата изменения',
        align: 'center',
        width: '30px',
        value: 'comment'
      },
      {
        text: 'Комментарий',
        align: 'center',
        width: '250px',
        sortable: false,
        value: 'comment'
      },
      {
        text: 'В черном списке',
        align: 'center',
        sortable: false,
        width: '250px',
        value: 'in_blacklist'
      },
      {
        text: 'Одобрить',
        align: 'center',
        sortable: false,
        width: '250px',
        value: 'approve'
      }
    ],
    editedIndex: -1,
    apiForm: {
      ext_number: null,
      phone: null,
      from_number: null,
      showroom_id: null,
      sip: null,
    },
    repeatItem: {
      id: '',
      phone: '',
      created_at: '',
    },
    repeatDefault: {
      id: '',
      phone: '',
      created_at: '',
    },
    editedItem: {
      id: '',
      first_name: '',
      last_name: '',
      middle_name: '',
      showroom_id: '',
      site_id: null,
      region_id: '',
      mark_id: null,
      model_id: null,
      phone: '',
      price: '',
      payment_method: '',
      initial_fee: '',
      operator_id: null,
      comment: '',
      general_comment: '',
      status_id: '',
      entry_point: '',
      last_call: '',
      callback: '',
      live_region: "",
      ads_source: "",
      will_arrive: "",
      arrived: "",
      arrived_date: "",
      date_of_sale: "",
      call_heard: "",
      type_id: "",
      trash_id: "",
      country: "",
      car_year: "",
      credit_period: "",
    },
    defaultItem: {
      id: '',
      client_name: '',
      showroom_id: '',
      site_id: null,
      region_id: '',
      mark_id: null,
      model_id: null,
      phone: '',
      price: '',
      payment_method: '',
      initial_fee: '',
      operator_id: null,
      comment: '',
      general_comment: '',
      status_id: '',
      entry_point: '',
      last_call: '',
      callback: '',
      live_region: "",
      ads_source: "",
      will_arrive: "",
      arrived: "",
      arrived_date: "",
      date_of_sale: "",
      call_heard: "",
      type_id: "",
      trash_id: "",
      country: "",
      car_year: "",
      credit_period: "",
    },
    payment_methods: [
      {id: 7, name: 'Не определено'},
      {id: 1, name: 'Наличными'},
      {id: 2, name: 'В кредит'},
      {id: 3, name: 'Кредит(Скидка)'},
      {id: 4, name: 'Лизинг'},
      {id: 5, name: 'Не дозвон'},
      {id: 6, name: 'Повтор'},
      {id: 8, name: 'ЛНР/ДНР'},
    ]
  }),

  async fetch({store, params: {id}, $auth}) {
    await store.dispatch('user/toggle', false)
    await store.dispatch('order/fetchTypes')
    await store.dispatch('order/fetchStatuses')
    await store.dispatch('showroom/fetchRegions')
    await store.dispatch('showroom/fetchShowroom', {id})
    await store.dispatch('showroom/fetchShowrooms')
    await store.dispatch('showroom/fetchSites', {id})
    await store.dispatch('showroom/fetchOperators', {showroom_id: (id || $auth.user?.showroom_id)})
    await store.dispatch('order/fetchAllOrders', {id})
    await store.dispatch('order/fetch_arrivals', {id})
    await store.dispatch('order/fetch_missed_calls', {id})
  },

  computed: {

    showroom_id() {
      return Number(this.$route.params.id) || null
    },
    role_id() {
      return this.$auth.user?.role_id;
    },
    showroom() {
      return this.$store.state.showroom.showroom
    },
    showrooms() {
      return this.$store.state.showroom.showrooms
    },
    drops() {
      return this.$store.state.order.drops
    },
    formTitle() {
      return this.editedIndex === -1 ? 'Добавлеение' : 'Изменение'
    },
    orders() {
      return this.$store.state.order.orders
    },
    types() {
      return this.$store.state.order.types
    },
    statuses() {
      const statuses = [9, 10]
      if (this.role_id !== 1) {
        return this.$store.state.order.statuses.filter(l => !statuses.includes(l.id))
      } else return this.$store.state.order.statuses
    },
    role() {
      return this.$store.state.auth.role
    },
    regions() {
      return this.$store.state.showroom.regions
    },
    sites() {
      return this.$store.state.showroom.sites
    },
    operators() {
      return this.$store.state.showroom.operators
    },
    links() {
      return [
        {
          text: 'Главная',
          disabled: false,
          href: '/'
        },
        {
          text: 'CRM',
          disabled: false,
          href: '/crm/' + this.showroom_id
        },
        {
          text: 'Заявки чёрного списка',
          disabled: false,
          href: '/crm/' + this.showroom_id + '/blacklist-orders'
        },
        {
          text: this.showroom.name || null,
          disabled: true,
          href: '/'
        }
      ]
    },

    perPage() {
      if (this.orders) {
        return this.orders.per_page
      } else {
        return this.orders.per_page
      }
    },
    pageCount() {
      return this.orders.last_page
    },
    itemCount() {
      if (this.orders) {
        return this.orders.total
      } else {
        return this.orders.total
      }
    }
  },

  watch: {
    dialog(val) {
      val || this.close()
    },
    repeatDialog(val) {
      val || (this.repeats = [])
    },
  },
  mounted() {
    this.handleLoading()

  },
  destroyed() {
    this.$echo.leave('orders_' + this.showroom_id);
  },


  created() {
    if (this.$route.query.from) {
      this.filter_from = this.$route.query.from
      this.advanced_from = this.$route.query.from
    }
    if (this.$route.query.repeat) {
      this.repeat = this.$route.query.repeat
    }
    if (!this.$route.query.blacklist) {
      this.$route.query.blacklist = true
    }
    if (this.$route.query.to) {
      this.filter_to = this.$route.query.to
      this.advanced_to = this.$route.query.to
    }
    if (this.$route.query.limit) {
      this.limit = parseInt(this.$route.query.limit)
    }
    if (this.$route.query.payment_method) {
      this.advanced_payment = this.$route.query.payment_method
    }
    if (this.$route.query.drop_id) {
      this.drop_id = this.$route.query.drop_id
    }
    if (this.$route.query.date_type) {
      this.advanced_filter_type = this.$route.query.date_type
      this.isFilter = !this.isFilter
    }
    if (this.$route.query.search) {
      this.search = this.$route.query.search
      this.advanced_search = this.$route.query.search
    }
    if (this.$route.query.status) {
      const str = this.$route.query.status;
      if (str.includes(",")) {
        const arr = str.split(",");
        this.filter_status = arr.map((x) => parseInt(x.trim()));
        this.advanced_status = arr.map((x) => parseInt(x.trim()));
      } else {
        this.filter_status = this.$route.query.status
        this.advanced_status = this.$route.query.status
      }
    }
    if (this.$route.query.trash) {
      this.filter_trash = this.$route.query.trash
      this.advanced_trash = this.$route.query.trash
    }
    if (this.$route.query.arrived) {
      this.advanced_arrived = this.$route.query.arrived
    }
    if (this.$route.query.phone) {
      this.advanced_phone = this.$route.query.phone
    }
    if (this.$route.query.region_id) {
      this.region_id = parseInt(this.$route.query.region_id)
      this.advanced_region = parseInt(this.$route.query.region_id)
    }
    if (this.$route.query.site_id) {
      const str = this.$route.query.site_id;
      const arr = str.split(",");
      this.filter_site = arr.map((x) => parseInt(x.trim()));
      this.advanced_site = arr.map((x) => parseInt(x.trim()));
    }
    if (this.$route.query.type_id) {
      const str = this.$route.query.type_id;
      if (str.includes(",")) {
        const arr = str.split(",");
        this.filter_type = arr.map((x) => parseInt(x.trim()));
        this.advanced_type = arr.map((x) => parseInt(x.trim()));
      } else {
        this.filter_type = this.$route.query.type_id
        this.advanced_type = this.$route.query.type_id
      }
    }
    if (this.$route.query.page) {
      this.page = parseInt(this.$route.query.page) || null;
    }
    this.$store.dispatch('order/fetchOrders2', {id: this.$route.params.id, query: this.$route.query, blacklist: true});

  },

  methods: {
    refresh_page: _.debounce(async function () {
      if (this.onProcess === true || document.hidden) {
        console.log('refresh on process or tab hidden')
        return;
      }
      if (this.lastUpdate && this.$moment().diff(this.lastUpdate, 'seconds') <= 10) {
        this.onProcess = true;
        const ago = this.$moment().diff(this.lastUpdate, 'seconds');

        setTimeout(async () => {
          await this.$store.dispatch('order/fetchOrders2', {id: this.$route.params.id, query: this.$route.query, blacklist: true});
          this.lastUpdate = this.$moment();
          this.onProcess = false;
          console.log('8.5 sec');
        }, 8500 - ago);
      } else {
        await this.$store.dispatch('order/fetchOrders2', {id: this.$route.params.id, query: this.$route.query, blacklist: true});
        this.lastUpdate = this.$moment();
        console.log('moment');
      }
    }, 400),
    async openRepeat(item) {
      if (item.callback) {
        this.repeatItem.callback = this.$moment(item.callback).format('DD.MM.YYYY HH:mm')
      }
      this.repeatItem.id = item.id
      this.repeatItem.phone = item.phone
      this.repeatItem.status = item.status
      this.repeatItem.showroom_id = this.$route.params.id
      this.repeatItem.created_at = item.created_at
      this.repeatDialog = true
      const {data} = await this.$axios.post("orders/repeats", {item: this.repeatItem})
      this.repeats = data

    },

    changedPage: _.debounce(async function () {
      const {query} = this.$route;
      try {
        await this.$router.push({query: {...query, page: this.page}});
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') { /* empty */ }
      }
      await this.$store.dispatch('order/fetchOrders2', {id: this.$route.params.id, query: this.$route.query, blacklist: true});
    }, 300),

    async sortRepeat() {
      const {query} = this.$route;
      this.repeat = !this.repeat;
      try {
        await this.$router.push({query: {...query, page: 1, repeat: this.repeat ? this.repeat : undefined}});
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') {
          /// /this.$sentry.captureException(error);
        }
      }

      await this.$store.dispatch('order/fetchOrders2', {id: this.$route.params.id, query: this.$route.query, blacklist: true});
    },
    doSearch: _.debounce(async function () {
      await this.$router.push({
        query: {
          ...(this.search && {search: this.search}),
          ...(this.filter_operator && {operator_id: this.filter_operator}),
          ...(this.filter_type?.length && {type_id: this.filter_type.join(",")}),
          ...(this.filter_site?.length && {site_id: this.filter_site.join(",")}),
          ...(this.filter_status && {status: this.filter_status}),
          ...(this.filter_trash && {trash: this.filter_trash}),
          ...(this.campaign && {campaign: this.campaign}),
          ...(this.filter_status?.length && {status: this.filter_status.join(",")}),
          ...(this.filter_from && {from: this.filter_from + ":00"}),
          ...(this.not_confirmed && {not_confirmed: this.not_confirmed}),
          ...(this.paymentUndefined && {paymentUndefined: this.paymentUndefined}),
          ...(this.filter_to && {to: this.filter_to + ":59"}),
          page: 1
        }
      }).catch(err => {
        // Ignore the vuex err regarding  navigating to the page they are already on.
        if (
          err.name !== 'NavigationDuplicated' &&
          !err.message.includes('Avoided redundant navigation to current location')
        ) {
          // But print any other errors to the console
          console.log(err);
        }
      });
      await this.$store.dispatch('order/fetchOrders2', {id: this.$route.params.id, query: this.$route.query, blacklist: true});
      this.page = 1;
    }, 500),


    confirmDelete(id) {
      this.deleteId = id
      this.deleteDialog = true
    },
    setLinks(text) {
      if (text === null) return
      const Rexp = /(\b(https?|ftp|file):\/\/([-A-Z0-9+&@#%?=~_|!:,.;]*)([-A-Z0-9+&@#%?\/=~_|!:,.;]*)[-A-Z0-9+&@#\/%=~_|])/ig;
      return text.replace(Rexp, "<a href='$1' target='_blank' rel='noreferrer'>$1</a>");
    },
    deleteItem(id) {
      this.$store
        .dispatch('order/delete', {
          id: this.deleteId,
          showroom_id: this.$route.params.id
        })
        .then(() => {
          this.$toast.success("Заявка удалёна");
        })
        .catch((error) => {
          this.$toast.error('Произошла ошибка при удалёние заявки: ' + error);
        })
      this.deleteId = ''
      this.dialog = false
      this.deleteDialog = false
    },

    approveBlacklist(id, status) {
      this.$store
        .dispatch('order/blacklist', {
          id,
          showroomId: this.showroom_id,
          status
        })
        .then(() => {
          if(status === 1) {
            this.$toast.success("Номер отправлен в ЧС");
          } else if(status === 2) {
            this.$toast.success("Успешно отклонено!!!");
          }
        })
        .catch((error) => {
          this.$toast.error('Произошла ошибка при отправки в ЧС: ' + error);
        })
    },
    close() {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
        this.last_call = null
        this.callback = null
        this.componentKey += 1
        this.phone = null
        this.oldLastCall = null
        this.oldCallback = null
        this.oldWillArrive = null
        this.oldArrivedDate = null
        this.$refs.form.resetValidation()
        this.valid = false
      })
    },
    async toArrive() {
      this.editedItem.car_name = `${this.editedItem.mark?.name != null ? this.editedItem.mark?.name + ' ' : ''}${this.editedItem.model?.name != null ? this.editedItem.model?.name : ''}`
      await this.$store
        .dispatch('order/toArrive', {
          item: this.editedItem
        })
        .then((res) => {
          if (this.editedIndex > -1) {
            this.$toast.success("Передано в приезд");
          }
          this.close()
        })
        .catch((error) => {
          this.$toast.error('Произошла ошибка ' + error);
        })
    },
    validate() {
      this.$refs.form.validate()
    },
    handleLoading() {
      const loader = this.$loading.show({
        container: null,
        canCancel: false,
        onCancel: null,
        color: '#42a5f6'
      })
      this.isLoading = !this.isLoading
      setTimeout(() => {
        loader.hide()
      }, 300)
    },
    getModels(markId = null) {
      this.editedItem.model_id = null
      if (markId !== 0) {
        this.$store.dispatch('property/fetchModels', {markId})
      }
    },
    async clearFilter() {
      this.filter_from = null
      this.filter_to = null
      this.filter_site = null
      this.filter_type = null
      this.filter_status = null
      this.filter_trash = null
      this.filter_operator = null
      this.advanced_arrived = null
      this.not_confirmed = null
      this.search = null
      this.page = 1
      try {
        await this.$router.push({
          query: {}
        });
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') {
          /// /this.$sentry.captureException(error);
        }
      } finally {
        await this.$store.dispatch('order/fetchOrders2', {
          id: this.$route.params.id,
          query: {},
          blacklist: true
        })
      }
    }
    ,
    async reset() {
      this.filter_from = null
      this.filter_to = null
      this.filter_site = null
      this.filter_type = null
      this.filter_status = null
      this.filter_trash = null
      this.filter_operator = null
      this.search = null
      this.page = 1
      try {
        await this.$router.push({
          query: {}
        });
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') {
          /// /this.$sentry.captureException(error);
        }
      }
    },

    onValidate(value) {
      this.editedItem.phone = value?.number
    },

    async call() {
      this.apiForm.phone = this.editedItem?.phone;
      this.apiForm.ext_number = this.$auth.user?.work_place;
      this.apiForm.showroom_id = this.$route.params?.id;
      await this.$axios
        .post("/call", this.apiForm, {
          headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
        })
        .then((response) => {
          if (response.status === 200) {
            this.$toast.success("Ожидайте идёт звонок...");
          }
          console.log(response.data);
        })
        .catch((error) => {
          this.$toast.error("Произошла ошибка проверьте правильность телефона!!!" + error,);
        });
    },


    async changeStatus(id = null) {
      const {query} = this.$route;
      this.filter_status = id
      this.page = 1
      try {
        await this.$router.push({query: {...query, page: 1, status: id}});
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') { /* empty */ }

      }
      await this.refresh_page();

    },
    resetValidation() {
      this.$refs.form.resetValidation();
    },
  }
}
</script>
<style scoped>

table {
  border: none;
  border-collapse: collapse;
}

table td {
  border: 1px solid rgb(204, 200, 200);
  padding: 4px !important;
  text-align: center !important;
}

table tfoot tr th {
  border: 1px solid rgb(204, 200, 200);
  padding: 0 4px !important;
}

.myTable > table > tfoot > tr > th {
  border: 1px solid rgb(204, 200, 200);
  padding: 0 4px !important;
}
</style>

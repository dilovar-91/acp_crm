<template>
  <div>
    <BreadCrumb :items="links" />
    <v-container fluid>
      <v-row no-gutters align="start" class="d-flex">
        <v-col cols="12">
          <v-card class="mx-auto pt-0">
            <v-app-bar class="grey lighten-5" elevate-on-scroll dense>
              <v-row class="ml-3 1">
                <v-col cols="12" sm="6" md="7">
                  <v-row>
                    <v-col cols="12" sm="6" md="3" class="hidden-sm-and-down">
                      <date-picker
                        v-model="filter_from"
                        format="DD.MM.Y"
                        placeholder="Дата с"
                        value-type="YYYY-MM-DD"
                        style="width: 100%"
                        @clear="clearFilter()"
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="3" class="hidden-sm-and-down">
                      <date-picker
                        v-model="filter_to"
                        format="DD.MM.Y"
                        placeholder="Дата до"
                        value-type="YYYY-MM-DD"
                        style="width: 100%"
                        @clear="clearFilter()"
                      />
                    </v-col>
                    <v-col
                      cols="12"
                      sm="6"
                      xl="4"
                      md="4"
                      class="hidden-sm-and-down"
                    >
                      <v-select
                        v-model="filter_site"
                        :items="sites"
                        hide-details
                        item-text="title"
                        item-value="id"
                        label="Сайт"
                        menu-props="auto"
                        style="width: 100%"
                        outlined
                        clearable
                        required
                        multiple
                        dense
                        @click:clear="$nextTick(() => clearFilter())"
                      >
                        <template v-slot:selection="{ item, index }">
                          <template v-if="index === 0">
                            <span>{{ item.title }}</span>
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
                  </v-row>
                </v-col>
                <v-col cols="12" sm="6" md="5">
                  <v-row>
                    <v-col cols="12" sm="6" md="3" class="hidden-sm-and-down">
                      <v-btn
                        color="success"
                        dark
                        class="mb-2"
                        @click="tomorrow()"
                      >
                        Завтра
                      </v-btn>
                    </v-col>

                    <v-col cols="12" sm="6" md="4" class="hidden-sm-and-down">
                      <v-btn
                        color="success"
                        dark
                        class="mb-2"
                        @click="filter()"
                      >
                        Применить
                      </v-btn>
                    </v-col>
                    <v-col cols="12" sm="6" md="3" class="hidden-sm-and-down">
                      <v-btn
                        color="error"
                        dark
                        class="mb-2"
                        @click="clearFilter()"
                      >
                        Сбросить
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-col>
              </v-row>

              <v-chip class="ma-2">
                {{
                  arrivals.length -
                  not_responding_count.length -
                  visit_count.length -
                  not_coming_count.length
                }}
              </v-chip>
              <v-chip class="ma-2" color="orange">
                {{ not_responding_count.length }}
              </v-chip>
              <v-chip class="ma-2" color="yellow">
                {{ visit_count.length }}
              </v-chip>
              <v-chip class="ma-2" color="red" text-color="white">
                {{ not_coming_count.length }}
              </v-chip>
              <v-spacer />
              <v-btn icon color="green" @click="exportFile()">
                <v-icon large>mdi-file-excel</v-icon>
              </v-btn>
              <v-btn
                color="primary"
                dark
                class="mr-2"
                :to="'/tablet/credit-requests/' + showroom_id"
              >
                <v-icon>mdi-keyboard-return</v-icon>
                Кр. отдел
              </v-btn>
              <v-btn
                v-if="showroom.id"
                color="primary"
                dark
                class="hidden-sm-and-down mr-1"
                @click="dialog = true"
              >
                Добавить
              </v-btn>
              <v-menu
                v-model="menu"
                :close-on-content-click="false"
                :nudge-width="200"
                offset-x
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-btn color="indigo" dark v-bind="attrs" v-on="on">
                    <v-icon class="mr-2"> mdi-menu</v-icon>
                    Меню
                  </v-btn>
                </template>

                <v-card>
                  <v-list>
                    <v-list-item>
                      <v-list-item-action>
                        <v-btn
                          v-if="showroom.id"
                          color="primary"
                          dark
                          class="mb-2"
                          @click="dialog = true"
                        >
                          Добавить
                        </v-btn>
                      </v-list-item-action>
                    </v-list-item>

                    <v-list-item class="hidden-md-and-up">
                      <v-list-item-action>
                        <date-picker
                          v-model="filter_from"
                          format="DD.MM.Y"
                          placeholder="Дата с"
                        />
                      </v-list-item-action>
                    </v-list-item>
                    <v-list-item class="hidden-md-and-up">
                      <v-list-item-action>
                        <date-picker
                          v-model="filter_to"
                          format="DD.MM.Y"
                          placeholder="Дата до"
                        />
                      </v-list-item-action>
                    </v-list-item>
                    <v-list-item class="hidden-md-and-up">
                      <v-list-item-action>
                        <v-btn
                          color="success"
                          dark
                          class="mb-2"
                          @click="filter()"
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
                    <v-spacer />

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
              <v-data-table
                :key="componentKey"
                class="elevation-1 myTable grey lighten-3"
                :search="search"
                :headers="headers"
                fixed-header
                height="80vh"
                :hide-default-footer="true"
                dense
              >
                <template v-slot:body>
                  <tbody>
                    <template v-for="(item, i) in arrivals">
                      <tr
                        :key="'row-' + item.id"
                        :class="row_classes(item)"
                        @dblclick="editItem(item)"
                      >
                        <td>{{ i + 1 }}</td>
                        <td>
                          {{ $moment(item.date).format('DD.MM') }}
                        </td>
                        <td :class="jumpClass(item.jump)">
                          {{ item.client_name }}
                        </td>
                        <td>{{ item.region && item.region.name }}</td>
                        <td>
                          {{ item.car_name }}
                        </td>
                        <td>
                          {{ item.phone_number | cutPhone }}
                        </td>
                        <td>{{ item.price | currency }}</td>
                        <td>{{ item.initial_fee }}</td>
                        <td>{{ item.payment }}</td>
                        <td class="text-center">
                          <template v-if="item.payment_method === 1">
                            <v-chip
                              class="ma-2"
                              color="success"
                              text-color="white"
                            >
                              Наличными
                            </v-chip>
                          </template>
                          <template v-else-if="item.payment_method === 2">
                            <v-chip class="ma-2" color="red" text-color="white">
                              Кредит
                            </v-chip>
                          </template>
                        </td>
                        <td>
                          {{ item.operator?.first_name }}
                          {{ item.operator?.last_name }}
                        </td>
                        <td :class="siteClass(item.site)">
                          {{ item.site && item.site.title }}
                        </td>
                        <td
                          class="word-wrp"
                          v-html="setLinks(item.comments)"
                        ></td>
                        <td
                          class="word-wrp"
                          v-html="setLinks(item.sd_comment)"
                        ></td>
                        <td>
                          <v-chip
                            v-if="item.is_consultation === 1"
                            class="ma-2 white--text"
                            color="purple"
                          >
                            {{ item.manager && item.manager.name }}
                          </v-chip>
                          <template v-else>
                            {{ item.manager && item.manager.name }}
                          </template>
                        </td>
                      </tr>
                    </template>
                  </tbody>
                </template>
              </v-data-table>
            </v-card-text>
          </v-card>
        </v-col>

        <v-dialog v-model="dialog" max-width="950">
          <v-card>
            <v-card-text>
              <v-container>
                <v-form ref="form" v-model="valid" lazy-validation>
                  <v-row dense>
                    <v-col cols="12" sm="6" md="5">
                      <v-menu
                        v-model="menu2"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        max-width="290px"
                        min-width="290px"
                      >
                        <template v-slot:activator="{ on, attrs }">
                          <v-text-field
                            v-model="computedDateFormatted"
                            label="Дата"
                            outlined
                            v-bind="attrs"
                            dense
                            type="fa"
                            hide-details
                            readonly
                            v-on="on"
                          />
                        </template>
                        <v-date-picker
                          v-model="date"
                          locale="ru"
                          no-title
                          @input="menu2 = false"
                        />
                      </v-menu>
                    </v-col>
                    <v-col cols="12" sm="6" md="7">
                      <v-autocomplete
                        v-model="editedItem.region_id"
                        :items="regions"
                        :value="regions[editedItem.region_id]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Регион"
                        hide-details
                        :rules="[(v) => !!v || 'Выберите регион']"
                        required
                        outlined
                        dense
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-text-field
                        v-model="editedItem.car_name"
                        label="Авто"
                        outlined
                        dense
                        hide-details
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-text-field
                        v-model="editedItem.price"
                        label="Стоимость"
                        outlined
                        hide-details
                        dense
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-text-field
                        v-model="editedItem.payment"
                        label="Платёж"
                        outlined
                        hide-details
                        dense
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-autocomplete
                        v-model="editedItem.operator_id"
                        :items="operators"
                        :value="operators[editedItem.operator_id]"
                        :item-text="
                          (item) => item.last_name + ' ' + item?.first_name
                        "
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Оператор"
                        :disabled="isManager()"
                        hide-details
                        outlined
                        dense
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="2">
                      <v-text-field
                        v-model="editedItem.initial_fee"
                        label="ПВ"
                        outlined
                        dense
                        hide-details
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-select
                        v-model="editedItem.payment_method"
                        :items="[
                          { id: 1, name: 'Наличными' },
                          { id: 2, name: 'В кредит' },
                        ]"
                        :disabled="isManager()"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Способ оплаты"
                        hide-details
                        outlined
                        dense
                        clearable
                        @click:clear="
                          $nextTick(() => (editedItem.payment_method = null))
                        "
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-autocomplete
                        v-model="editedItem.manager_id"
                        :items="managers"
                        :item-text="
                          (item) =>
                            item.last_name
                              ? item.first_name + ' ' + item.last_name
                              : item.first_name
                        "
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Менеджер"
                        hide-details
                        outlined
                        dense
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="4">
                      <v-text-field
                        v-model="editedItem.client_name"
                        label="ФИО клиента"
                        outlined
                        :rules="nameRules"
                        dense
                        required
                        hide-details
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-text-field
                        v-model="editedItem.phone_number"
                        label="Телефон"
                        outlined
                        hide-details
                        dense
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-select
                        v-model="editedItem.site_id"
                        :items="sites"
                        hide-details
                        item-text="title"
                        item-value="id"
                        label="Сайт"
                        menu-props="auto"
                        outlined
                        clearable
                        required
                        dense
                        @click:clear="
                          $nextTick(() => (editedItem.site_id = null))
                        "
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-checkbox
                        v-model="editedItem.is_avito"
                        label="Авито"
                        outlined
                        dense
                        hide-details
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-checkbox
                        v-model="editedItem.is_cash"
                        label="Наличка"
                        outlined
                        dense
                        hide-details
                      />
                    </v-col>

                    <v-col cols="12" sm="6" md="6">
                      <v-textarea
                        v-model="editedItem.comments"
                        rows="3"
                        label="Комментарии"
                        outlined
                        hide-details
                        dense
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="6" class="my-0">
                      <v-textarea
                        v-model="editedItem.sd_comment"
                        rows="3"
                        label="Контроль ОП"
                        outlined
                        hide-details
                        dense
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="3" class="my-0">
                      <v-checkbox
                        v-model="editedItem.visited"
                        label="Приехал"
                        outlined
                        dense
                        hide-details
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="3" class="my-0">
                      <v-checkbox
                        v-model="editedItem.not_coming"
                        label="Не приедет"
                        outlined
                        dense
                        hide-details
                      />
                    </v-col>

                    <v-col cols="12" sm="6" md="3" class="my-0">
                      <v-checkbox
                        v-model="editedItem.not_responding"
                        label="Не отвечает"
                        outlined
                        hide-details
                        dense
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="3" class="my-0">
                      <v-checkbox
                        v-model="editedItem.consultation"
                        label="Консультация с другого салона"
                        outlined
                        hide-details
                        dense
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="3" class="my-0">
                      <v-checkbox
                        v-model="editedItem.jump"
                        label="Соскок с другого салона"
                        outlined
                        hide-details
                        dense
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="3" class="my-0">
                      <v-checkbox
                        v-model="editedItem.is_consultation"
                        label="Консультация"
                        outlined
                        hide-details
                        dense
                      />
                    </v-col>
                  </v-row>
                </v-form>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer />
              <v-btn
                color="warning darken-1"
                class="mr-3"
                text
                v-if="
                  isManager() || role?.name == 'admin' || editedIndex !== -1
                "
                @click="copyToRequest(editedItem.id)"
              >
                Передать в кредитный отдел
              </v-btn>
              <v-btn
                v-if="editedIndex !== -1"
                color="red darken-1"
                class="mr-3"
                text
                :disabled="canDelete()"
                @click="confirmDelete(editedItem.id)"
              >
                Удалить
              </v-btn>
              <v-btn color="green darken-1" text @click="dialog = false">
                Отменить
              </v-btn>
              <v-btn
                color="green darken-1"
                text
                v-role="'admin'"
                @click="save()"
              >
                Сохранить
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="deleteDialog" max-width="290">
          <v-card>
            <v-card-title class="headline"> Вы хотите удалить?</v-card-title>

            <v-card-text>
              После удаления вы не можете восстановить эту строку.
            </v-card-text>

            <v-card-actions>
              <v-spacer />

              <v-btn color="green darken-1" text @click="deleteItem()">
                Да
              </v-btn>
              <v-btn color="green darken-1" text @click="deleteDialog = false">
                Нет
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-row>
    </v-container>
  </div>
</template>
<style scoped>
table {
  border: none;
  border-collapse: collapse;
}

table td {
  border: 1px solid rgb(204, 200, 200);
}

table tfoot tr th {
  border: 1px solid rgb(204, 200, 200);
  padding: 0 12px !important;
}

.myTable > table > tfoot > tr > th {
  border: 1px solid rgb(204, 200, 200);
  padding: 0 12px !important;
}

.word-wrp {
  word-wrap: break-word;
}
</style>
<script>
import { saveAs } from 'file-saver'
import BreadCrumb from '~/components/BreadCrumb'

export default {
  layout: 'user',
  middleware: 'permission',
  components: { BreadCrumb },
  data: (vm) => ({
    manager_roles: [
      'admin',
      'manager',
      'manager2',
      'manager_acp',
      'petr',
      'manager_light',
    ],
    operator_roles: [
      'admin',
      'operator',
      'operator2',
      'operator_acp',
      'operator_light',
    ],
    date: new Date().toISOString().substr(0, 10),
    dateFormatted: vm.formatDate(new Date().toISOString().substr(0, 10)),
    menu: false,
    componentKey: 0,
    intervalid: '',
    dialog: false,
    search: '',
    filter_from: null,
    filter_to: null,
    filter_site: null,
    menu2: false,
    menu3: false,
    menu4: false,
    isLoading: false,
    isSaving: false,
    valid: false,
    // deleteDialog
    deleteId: '',
    deleteDialog: false,
    nameRules: [(v) => !!v || 'Введитие ФИО клиента'],
    dateRules: [(v) => !!v || 'Выберите дату'],
    headers: [
      {
        text: '№',
        value: 'number',
        sortable: false,
        fixed: true,
        width: '30px',
      },
      {
        text: 'Дата',
        align: 'start',
        sortable: false,
        value: 'date',
        fixed: true,
        width: '30px',
      },
      {
        text: 'ФИО',
        sortable: false,
        width: '180px',
        value: 'client_name',
        fixed: true,
      },
      { text: 'Регион', value: 'region.name', sortable: false, width: '100px' },
      { text: 'Авто', value: 'mark.name', width: '200px', sortable: false },
      { text: 'Телефон', value: 'protein', width: '20px', sortable: false },
      { text: 'Стоимость', value: 'protein', width: '100px', sortable: false },
      { text: 'ПВ', value: 'initial_fee', width: '55px', sortable: false },
      { text: 'Платёж', value: 'payment', width: '55px', sortable: false },
      {
        text: 'Способ оплаты',
        value: 'payment_method',
        width: '55px',
        sortable: false,
      },
      {
        text: 'Оператор',
        value: 'operator',
        width: '50px',
        sortable: false,
      },
      {
        text: 'Сайт',
        value: 'site_id',
        width: '40px',
        sortable: false,
      },
      {
        text: 'Комментарий КЦ',
        value: 'comments',
        width: '340px',
        sortable: false,
      },
      {
        text: 'Комментарий ОП',
        value: 'sd_comment',
        width: '340px',
        sortable: false,
      },
      {
        text: 'Менеджер',
        value: 'manager',
        width: '50px',
        sortable: false,
      },
    ],
    editedIndex: -1,
    editedItem: {
      id: '',
      date: '',
      client_name: '',
      showroom_id: '',
      site_id: null,
      region_id: '',
      car_name: '',
      phone_number: '',
      price: '',
      payment: '',
      payment_method: '',
      initial_fee: '',
      operator_id: '',
      comments: '',
      sd_comment: '',
      manager_id: '',
      showroom_comment: '',
      visited: '',
      not_coming: '',
      not_responding: '',
      consultation: '',
      is_avito: '',
      is_cash: '',
      jump: '',
      transit: '',
      transit_confirmed: '',
      transit_consultation: '',
    },
    defaultItem: {
      id: '',
      date: '',
      client_name: '',
      showroom_id: '',
      site_id: null,
      region_id: '',
      car_name: '',
      phone_number: '',
      price: '',
      payment: '',
      payment_method: '',
      initial_fee: '',
      operator_id: '',
      comments: '',
      sd_comment: '',
      manager_id: '',
      showroom_comment: '',
      visited: '',
      not_coming: '',
      not_responding: '',
      consultation: '',
      is_avito: '',
      is_cash: '',
      jump: '',
      transit: '',
      transit_confirmed: '',
      transit_consultation: '',
    },
  }),

  computed: {
    showroom_id() {
      return Number(this.$route.params.id) || null
    },
    showroom() {
      return this.$store.state.showroom.showroom
    },
    formTitle() {
      return this.editedIndex === -1 ? 'Добавлеение' : 'Изменение'
    },
    arrivals() {
      return this.$store.state.arrival.arrivals
    },
    role() {
      return this.$store.state.auth.role
    },
    regions() {
      return this.$store.state.showroom.regions
    },
    managers() {
      return this.$store.state.showroom.managers
    },
    sites() {
      return this.$store.state.showroom.sites.filter(
        (l) => l.showroom_id === Number(this.$route.params.id)
      )
    },
    operators() {
      return this.$store.state.showroom.operators.filter(
        (l) => l.showroom_id === Number(this.$route.params.id)
      )
    },
    visit_count() {
      return this.$store.state.showroom.arrivals.filter((l) => l.visited === 1)
    },
    not_coming_count() {
      return this.$store.state.showroom.arrivals.filter(
        (l) => l.not_coming === 1
      )
    },
    not_responding_count() {
      return this.$store.state.showroom.arrivals.filter(
        (l) => l.not_responding === 1
      )
    },
    visited() {
      return this.editedItem.visited
    },
    not_coming() {
      return this.editedItem.not_coming
    },
    not_responding() {
      return this.editedItem.not_responding
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
        {
          text: 'Приезды',
          disabled: false,
          href: '/tablet/arrivals/',
        },
        {
          text: this.showroom?.name,
          disabled: true,
          href: '/tablet/arrivals/' + this.showroom?.id,
        },
      ]
    },
    computedDateFormatted() {
      return this.formatDate(this.date)
    },
  },

  watch: {
    dialog(val) {
      val || this.close()
    },
    visited(val) {
      if (val === true) {
        this.editedItem.not_coming = false
        this.editedItem.not_responding = false
      }
    },
    not_coming(val) {
      if (val === true) {
        this.editedItem.visited = false
        this.editedItem.not_responding = false
      }
    },
    not_responding(val) {
      if (val === true) {
        this.editedItem.visited = false
        this.editedItem.not_coming = false
      }
    },
    date(val) {
      this.dateFormatted = this.formatDate(this.date)
    },
  },

  async fetch({ store, params: { id } }) {
    await store.dispatch('user/toggle', false)
    await store.dispatch('showroom/fetchManagers', { showroom_id: id })
    await store.dispatch('showroom/fetchOperators', { showroom_id: id })
    await store.dispatch('arrival/fetchArrivals', { id })
    await store.dispatch('showroom/fetchRegions')
    await store.dispatch('showroom/fetchShowroom', { id })
    await store.dispatch('showroom/fetchSites', { id })
  },
  mounted() {
    if (
      this.filter_from === null &&
      this.filter_from === null &&
      this.isSaving == false
    ) {
      this.handleLoading()
      this.$echo.channel('arrivals').listen('ArrivalCreated', (e) => {
        this.filter()
      })
    }
  },
  methods: {
    async copyToRequest(id) {
      this.$axios
        .post('/arrivals/copy', { id })
        .then((response) => {
          this.$toast.success('Приезд передан кредитному отделу')
          this.close()
          //resolve(response)
          //this.$router.push('/' + this.role.name + '/tablet/credit-department/' + this.showroom_id);
        })
        .catch((error) => {
          this.$toast.error('Произошла ошибка:' + error)
          //reject(error)
        })
    },
    editItem(item) {
      this.editedIndex = item.id
      this.date = this.$moment(item.date).format('YYYY-MM-DD')
      this.editedItem = Object.assign({}, item)
      if (this.editedItem.manager_id) {
        this.editedItem.manager_id = parseInt(this.editedItem.manager_id)
      }
      this.dialog = true
    },
    tomorrow() {
      const tmrw = this.$moment().add(1, 'days').format('YYYY-MM-DD')
      this.filter_from = tmrw
      this.filter_to = tmrw
      this.$store.dispatch('arrival/fetchArrivals', {
        id: this.$route.params.id,
        from: tmrw,
        to: tmrw,
        site_id: this.filter_site,
      })
      const date = new Date()
      date.setTime(date.getTime() + 3600 * 1000 * 24)
      this.filter_from = this.filter_to = date
      this.filter_from = this.filter_to = date
    },
    filter() {
      this.$store.dispatch('arrival/fetchArrivals', {
        id: this.$route.params.id,
        from: this.filter_from
          ? this.filter_from
          : this.$moment().format('YYYY-MM-DD'),
        to: this.filter_to
          ? this.filter_to
          : this.$moment().format('YYYY-MM-DD'),
        site_id: this.filter_site,
      })
    },
    confirmDelete(id) {
      this.deleteId = id
      this.deleteDialog = true
    },
    setLinks(text) {
      if (text === null) return
      const Rexp =
        /(\b(https?|ftp|file):\/\/([-A-Z0-9+&@#%?=~_|!:,.;]*)([-A-Z0-9+&@#%?\/=~_|!:,.;]*)[-A-Z0-9+&@#\/%=~_|])/gi
      return text.replace(
        Rexp,
        "<a href='$1' target='_blank' rel='noreferrer'>$1</a>"
      )
    },
    deleteItem(id) {
      this.$store
        .dispatch('showroom/deleteArrival', {
          id: this.deleteId,
          showroom_id: this.$route.params.id,
        })
        .then(() => {
          this.$toast.success('Приезд удалён')
        })
      this.deleteId = ''
      this.dialog = false
      this.deleteDialog = false
    },
    close() {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
        this.date = ''
        this.componentKey += 1
        this.$refs.form.resetValidation()
        this.valid = false
      })
    },

    async save() {
      await this.validate()
      this.isSaving = true
      if (this.valid) {
        if (this.editedItem.transit_consultation === true) {
          this.$axios
            .post('/telebot/send', { data: this.editedItem })
            .then((response) => {
              console.log('Notify sent')
            })
            .catch((error) => {
              console.log('error: ' + error)
            })
        }
        this.editedItem.showroom_id = this.showroom.id
        this.editedItem.date = this.date
        this.$store
          .dispatch('arrival/saveArrival', {
            item: this.editedItem,
          })
          .then((res) => {
            if (this.editedIndex > -1) {
              this.$toast.success('Приезд изменён')
            } else {
              this.$toast.success('Приезд добавлён')
            }
            this.close()
          })
          .catch((error) => {
            this.$toast.error('Заполните обязательные поля' + error)
          })
      } else {
        this.$toast.error('Заполните обязательные поля')
      }
      this.isSaving = false
    },
    validate() {
      this.$refs.form.validate()
    },
    handleLoading() {
      const loader = this.$loading.show({
        // Optional parameters
        container: null,
        canCancel: false,
        onCancel: null,
        color: '#42a5f6',
      })
      this.isLoading = !this.isLoading
      setTimeout(() => {
        loader.hide()
      }, 300)
    },

    row_classes(item) {
      const classes = {
        visited: 'yellow',
        not_coming: 'red white--text',
        not_responding: 'amber darken-2 white--text',
        consultation: 'blue darken-2 white--text',
      }

      return item.visited
        ? classes.visited
        : item.not_coming
        ? classes.not_coming
        : item.not_responding
        ? classes.not_responding
        : item.consultation
        ? classes.consultation
        : ''
    },
    jumpClass(jump) {
      if (jump === 1) {
        return 'lime'
      }
    },
    siteClass(site) {
      if (site && site.id === 28) {
        return 'yellow lighten-1'
      }
      if (site && site.id === 29) {
        return 'yellow darken-4 white--text'
      }
    },
    clearFilter() {
      this.filter_from = null
      this.filter_to = null
      this.filter_site = null
      this.$store.dispatch('arrival/fetchArrivals', {
        id: this.$route.params.id,
        from: this.filter_from
          ? this.filter_from
          : this.$moment().format('YYYY-MM-DD'),
        to: this.filter_to
          ? this.filter_to
          : this.$moment().format('YYYY-MM-DD'),
      })
    },

    formatDate(date) {
      if (!date) {
        return null
      }

      const [year, month, day] = date.split('-')
      return `${day}.${month}.${year}`
    },
    parseDate(date) {
      if (!date) {
        return null
      }
      const [month, day, year] = date.split('/')
      return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`
    },
    canDelete() {
      const users = [
        'admin',
        'director',
        'coordinator',
        'coordinator1',
        'operator',
        'operator1',
        'operator2',
        'operator3',
        'operator4',
        'operator5',
        'operator7',
        'operator8',
        'operator10',
        'operator11',
        'operator_acp',
        'operator_light',
        'operator_avrora',
        'operator_alarm',
        'operator_autodom',
        'operator_avangard',
        'operator_atlant',
        'operator_accent',
        'misha',
      ]
      return !users.includes(this.role?.name)
    },
    isManager() {
      const users = [
        'manager_acp',
        'manager_light',
        'manager_avrora',
        'manager_avangard',
        'manager_atlant',
        'manager_alarm',
        'manager_autodom',
        'manager_accent',
        'manager',
        'manager2',
        'manager4',
        'manager5',
        'manager7',
        'manager8',
        'manager9',
        'manager10',
        'manager11',
      ]
      return users.includes(this.role?.name)
    },

    canChange() {
      // const users = ['admin', 'custom', 'director', 'coordinator', 'coordinator2', 'manager_acp', 'manager_light']
      const users = [
        'admin',
        'custom',
        'director',
        'operator',
        'operator1',
        'operator_avrora',
        'operator2',
        'operator3',
        'operator4',
        'operator5',
        'operator7',
        'operator8',
        'operator10',
        'operator11',
        'operator_light',
        'operator_acp',
        'operator_avangard',
        'operator_atlant',
        'operator_alarm',
        'operator_autodom',
        'operator_accent',
        'manager_acp',
        'manager_light',
        'manager_avrora',
        'manager_avangard',
        'manager_atlant',
        'manager_alarm',
        'manager_autodom',
        'manager_accent',
        'manager',
        'manager2',
        'petr',
        'mark',
        'misha',
        'manager4',
        'custom2',
        'manager5',
        'manager7',
        'manager8',
        'manager10',
        'manager11',
      ]
      return !users.includes(this.role?.name)
    },
    exportFile() {
      const XLSX = require('xlsx')
      let wb = XLSX.utils.book_new()
      const rows = this.arrivals.map((row, index) => ({
        '№': index + 1,
        Дата: row.date,
        'ФИО клиента': row.client_name,
        Шоурум: row.showroom?.name,
        Телефон: row.phone_number,
        Регион: row.region?.name,
        Цена: row.price,
        ПВ: row.initial_fee,
        'Способ оплаты':
          row.payment_method === 1
            ? 'Наличными'
            : row.payment_method === 2
            ? 'Кредит'
            : '',
        Оператор: row.operator?.first_name + ' ' + row.operator?.last_name,
        Сайт: row.site?.title,
        'Комментарий КЦ': row.comments,
        'Комментарий ОП': row.sd_comment,
        Менеджер: row.manager?.first_name + ' ' + row.manager?.last_name,
        'Комментарий Салон': row.showroom_comment,
        Приехал: this.getStatus(row.visited, 'visited'),
        'Не приедет': this.getStatus(row.not_coming, 'not_coming'),
        'Не отвечает': this.getStatus(row.not_responding, 'not_responding'),
      }))
      let ws = XLSX.utils.json_to_sheet(rows)
      ws['!cols'] = [
        { wch: 3 },
        { wch: 10 },
        { wch: 30 },
        { wch: 11 },
        { wch: 14 },
        { wch: 14 },
        { wch: 14 },
        { wch: 14 },
        { wch: 12 },
        { wch: 12 },
        { wch: 55 },
        { wch: 55 },
        { wch: 14 },
        { wch: 35 },
        { wch: 10 },
        { wch: 10 },
        { wch: 10 },
      ]
      XLSX.utils.book_append_sheet(wb, ws, 'Приезды - ' + this.showroom?.name)
      let wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' })
      saveAs(
        new Blob([wbout], { type: 'application/octet-stream' }),
        'Приезды_' +
          this.showroom?.name +
          '_' +
          this.$moment().format('DD-MM-YYYY') +
          '.xlsx'
      )
    },
    getStatus(value, type) {
      let status = ''
      switch (type) {
        case 'visited':
          status = value === 1 ? 'Приехал' : ''
          break
        case 'not_coming':
          status = value === 1 ? 'Не приедет' : ''
          break
        case 'not_responding':
          status = value === 1 ? 'Не отвечает' : ''
          break
        default:
          status = ''
      }
      return status
    },
  },

  beforeRouteLeave(to, from, next) {
    this.$store.dispatch('user/toggle', true)
    clearInterval(this.intervalid)
    next()
  },
}
</script>

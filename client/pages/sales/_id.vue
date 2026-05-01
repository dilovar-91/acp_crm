<template>
  <div>
    <BreadCrumb :items="links" />

    <v-container fluid>
      <v-row no-gutters align="start" class="d-flex">
        <v-col cols="12">
          <v-card class="mx-auto pt-0">
            <v-app-bar class="grey lighten-5" elevate-on-scroll>
              <v-toolbar-title />

              <v-row class="ml-5" dense>
                <v-col cols="6" sm="2" md="2" class="hidden-sm-and-down">
                  <v-text-field
                    id="styled-input"
                    v-model="search"
                    label="ФИО клиента"
                    outlined
                    class="styled-input mt-2"
                    dense
                  />
                </v-col>
                <v-col cols="6" sm="6" md="2" class="hidden-sm-and-down">
                  <date-picker
                    v-model="filter_from"
                    format="DD.MM.Y"
                    placeholder="Дата с"
                    class="mt-3"
                    clear="filter_from=''"
                  />
                </v-col>
                <v-col cols="6" sm="3" md="2" class="hidden-sm-and-down">
                  <date-picker
                    v-model="filter_to"
                    format="DD.MM.Y"
                    placeholder="Дата до"
                    class="mt-3 mx-3"
                    clear="filter_to=''"
                  />
                </v-col>
                <v-col cols="6" sm="6" md="2">
                  <v-select
                    v-model="filter_status"
                    :items="votes"
                    item-text="value"
                    no-data-text="Нету данных"
                    item-value="id"
                    menu-props="auto"
                    class="ml-6 mt-2"
                    label="Статус"
                    multiple
                    hide-details
                    clearable
                    outlined
                    dense
                    @click:clear="$nextTick(() => (filter_status = []))"
                  >
                    <template v-slot:selection="{ item, index }">
                      <template v-if="index === 0">
                        <span style="font-size: 13px">{{ item.value }}</span>
                      </template>
                      <span v-if="index === 1" class="grey--text text-caption">
                        &nbsp;(+{{ filter_status.length - 1 }})
                      </span>
                    </template>
                  </v-select>
                </v-col>
                <v-col cols="6" sm="2" md="2" class="mt-2 hidden-sm-and-down">
                  <v-btn color="success" dark @click="filter()">
                    Применить
                  </v-btn>
                </v-col>
                <v-col cols="6" sm="3" md="2" class="mt-2 hidden-sm-and-down">
                  <v-btn color="error" dark @click="clearFilter()">
                    Сбросить
                  </v-btn>
                </v-col>
              </v-row>
              <v-spacer />
              <v-btn icon color="green" @click="scroll('up')">
                <v-icon>mdi-arrow-up</v-icon>
              </v-btn>
              <v-btn icon color="green" @click="scroll('down')">
                <v-icon>mdi-arrow-down</v-icon>
              </v-btn>
              <v-btn
                color="primary"
                dark
                class="mr-2"
                :to="'/' + '/tablet/credit-department/' + showroom_id"
              >
                <v-icon>mdi-keyboard-return</v-icon> Кредитный отдел
              </v-btn>
              <v-menu
                v-model="menu"
                :close-on-content-click="false"
                :nudge-width="200"
                offset-x
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-btn color="indigo" dark v-bind="attrs" v-on="on">
                    <v-icon class="mr-2"> mdi-menu </v-icon> Меню
                  </v-btn>
                </template>

                <v-card>
                  <v-list>
                    <v-list-item class="hidden-md-and-up">
                      <v-list-item-action>
                        <v-text-field
                          v-model="search"
                          clearable
                          label="ФИО клиента"
                          outlined
                          dense
                        />
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
                    <v-list-item>
                      <v-list-item-action>
                        <v-btn color="success" dark @click="filter()">
                          Применить
                        </v-btn>
                      </v-list-item-action>
                    </v-list-item>
                    <v-list-item>
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

                    <v-list-item>
                      <v-list-item-action>
                        <v-switch
                          v-show="
                            this.role &&
                            (this.role?.name === 'admin' ||
                              this.role?.name === 'mark' ||
                              this.role?.name === 'director')
                          "
                          v-model="deleted"
                          label="Корзина"
                          color="red"
                          class="mt-1"
                          hide-details
                        />
                      </v-list-item-action>
                    </v-list-item>
                  </v-list>
                  <v-card-actions>
                    <v-spacer />

                    <v-btn color="primary" text @click="menu = false">
                      Закрыть
                    </v-btn>
                  </v-card-actions>
                </v-card>
              </v-menu>
            </v-app-bar>
            <v-card-text class="pa-0 py-0">
              <v-data-table
                :key="componentKey"
                ref="my-table"
                class="elevation-1 myTable grey lighten-3"
                :search="search"
                :headers="headers"
                :items="sales"
                :items-per-page="sales.length"
                fixed-header
                height="80vh"
                :hide-default-footer="true"
                item-key="id"
              >
                <template
                  v-for="(h, index) in headers"
                  v-slot:[`header.${h.value}`]="{ header }"
                >
                  <span
                    v-if="h.vertical"
                    :key="'vertical-' + index"
                    class="vertical"
                    >{{ h.text }}</span
                  >
                  <span
                    v-else
                    :key="'horizontal-' + index"
                    class="black--text font-weight-bold"
                    >{{ h.text }}</span
                  >
                </template>

                <template v-slot:body="{ items }">
                  <tbody>
                    <tr
                      v-for="(item, i) in items"
                      :key="item.id"
                      :class="row_classes(item.sale_vote_id)"
                      @dblclick="editItem(item)"
                    >
                      <td>{{ i + 1 }}</td>
                      <td>
                        {{ moment(item.date).format('DD.MM') }} <br />
                        {{ moment(item.created_at).format('HH:mm') }}
                      </td>
                      <td>
                        {{ moment(item.sale_date).format('DD.MM.YYYY') }}
                      </td>
                      <td>
                        {{ item.client_name }}
                      </td>
                      <td>
                        <template v-if="item.car_name !== null">
                          {{ item.car_name }}
                        </template>
                        <template v-else>
                          {{ item.mark && item.mark.name }}
                          {{ item.model }}
                        </template>
                      </td>
                      <td>
                        {{ item.sale_vin }}
                      </td>
                      <td>
                        {{ item.phone_number }}
                      </td>
                      <td>{{ item.bank && item.bank.name }}</td>
                      <td>{{ item.sale_manager && item.sale_manager.name }}</td>
                      <td>
                        {{ item.oformitel && item.oformitel.name }}
                      </td>
                      <td>
                        <v-icon v-if="item.sale_repeat === 1" color="success"
                          >mdi-check</v-icon
                        >
                      </td>
                      <td>
                        <template v-if="item.sale_call_date !== null">
                          {{ moment(item.sale_call_date).format('DD.MM.YYYY') }}
                        </template>
                      </td>
                      <td>
                        <template v-if="item.sale_recall_date !== null">
                          {{
                            moment(item.sale_recall_date).format(
                              'DD.MM.YYYY HH:mm:ss'
                            )
                          }}
                        </template>
                      </td>
                      <td>
                        {{
                          votes.find((r) => r.id === item.sale_vote_id)?.value
                        }}
                      </td>
                    </tr>
                    <span class="d-none scroll" />
                  </tbody>
                </template>
              </v-data-table>
            </v-card-text>
          </v-card>
        </v-col>
        <v-snackbar
          v-model="snackbar"
          :color="color"
          :right="'right'"
          :timeout="timeout"
          :top="'top'"
          outlined
        >
          {{ text }}
          <template v-slot:action="{ attrs }">
            <v-btn icon color="deep-orange" @click="snackbar = false">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </template>
        </v-snackbar>
        <v-dialog v-model="dialog" max-width="850">
          <v-card>
            <v-card-text>
              <v-container>
                <v-form ref="form" v-model="valid">
                  <v-row>
                    <v-col cols="12" sm="12" md="5" class="py-1">
                      <date-picker
                        v-model="saleItem.sale_date"
                        format="DD.MM.Y"
                        value-type="YYYY-MM-DD"
                        type="date"
                        placeholder="Дата продажи"
                        class="mt-3"
                        clear="saleItem.sale_date=''"
                      />
                    </v-col>

                    <v-col cols="12" sm="12" md="7" class="py-1">
                      <v-text-field
                        v-model="saleItem.client_name"
                        label="Клиент"
                        outlined
                        class="mt-2"
                        dense
                        hide-details
                      />
                    </v-col>

                    <v-col cols="12" sm="12" md="6" class="py-1">
                      <v-text-field
                        v-model="saleItem.phone_number"
                        label="Телефон"
                        outlined
                        class="mt-2"
                        dense
                        hide-details
                      />
                    </v-col>

                    <v-col cols="12" sm="12" md="6" class="py-1">
                      <v-select
                        v-model="saleItem.sale_bank_id"
                        :items="banks"
                        class="mt-2"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Банк"
                        hide-details
                        outlined
                        clearable
                        dense
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="6" class="py-1">
                      <v-text-field
                        v-model="saleItem.car_name"
                        label="Машина"
                        outlined
                        class="mt-2"
                        dense
                        hide-details
                      />
                    </v-col>

                    <v-col cols="12" sm="12" md="6" class="py-1">
                      <v-autocomplete
                        v-model="saleItem.sale_manager_id"
                        :items="managers"
                        class="mt-2"
                        :value="managers[saleItem.sale_manager_id]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Менеджер"
                        hide-details
                        outlined
                        clearable
                        dense
                        @click:clear="
                          $nextTick(() => (saleItem.sale_manager_id = null))
                        "
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="6" class="py-1">
                      <v-autocomplete
                        v-model="saleItem.sale_oformitel_id"
                        :items="preparers"
                        class="mt-2"
                        :value="preparers[saleItem.sale_oformitel_id]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Оформитель"
                        hide-details
                        outlined
                        clearable
                        dense
                        @click:clear="
                          $nextTick(() => (saleItem.sale_oformitel_id = null))
                        "
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="6" class="py-1">
                      <v-text-field
                        v-model="saleItem.sale_vin"
                        label="Вин"
                        outlined
                        class="mt-2"
                        dense
                        hide-details
                      />
                    </v-col>

                    <v-col cols="12" sm="12" md="6" class="py-1">
                      <v-checkbox
                        v-model="saleItem.is_sold"
                        label="Продана"
                        outlined
                        dense
                        hide-details
                      />
                    </v-col>

                    <v-col cols="12" sm="12" md="6" class="py-1">
                      <v-checkbox
                        v-model="saleItem.sale_repeat"
                        label="Повторная покупка"
                        outlined
                        dense
                        :disabled="hasAccess()"
                        hide-details
                      />
                    </v-col>

                    <v-col cols="12" sm="12" md="4">
                      Дата прозвона
                      <date-picker
                        v-model="saleItem.sale_call_date"
                        format="DD.MM.Y"
                        value-type="YYYY-MM-DD"
                        type="date"
                        placeholder="Дата прозвона"
                        class="mt-1"
                        clear="saleItem.sale_call_date=''"
                      />
                    </v-col>

                    <v-col cols="12" sm="12" md="4">
                      Перезвонить
                      <DTPicker
                        v-model="saleItem.sale_recall_date"
                        value-type="DD.MM.YYYY HH:mm"
                        type="datetime"
                        class="mt-1"
                        format="DD.MM.YYYY HH:mm"
                        :time-picker-options="{
                          start: '07:00',
                          step: '00:30',
                          end: '23:59',
                          format: 'HH:mm',
                        }"
                        @setNow="setNow('sale_recall_date', true)"
                      />
                    </v-col>

                    <v-col cols="12" sm="12" md="4">
                      Статус
                      <v-select
                        v-model="saleItem.sale_vote_id"
                        :items="votes"
                        item-text="value"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        hide-details
                        outlined
                        clearable
                        dense
                      />
                    </v-col>

                    <v-col cols="12" sm="12" md="12" class="py-1">
                      <v-textarea
                        v-model="saleItem.sale_comment"
                        label="Комментарий"
                        outlined
                        dense
                        hide-details
                      />
                    </v-col>
                  </v-row>
                </v-form>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer />
              <v-btn
                color="red darken-1"
                class="mr-3"
                text
                @click="confirmDelete(saleItem.id, true)"
              >
                Удалить
              </v-btn>
              <v-btn color="green darken-1" text @click="dialog = false">
                Отменить
              </v-btn>
              <v-btn
                color="green darken-1"
                :loading="loading"
                text
                @click="save()"
              >
                Сохранить
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="deleteDialog" max-width="290">
          <v-card>
            <v-card-title class="headline"> Вы хотите удалить? </v-card-title>

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
  padding: 0 4px !important;
  text-align: center;
}
table th {
  border: 1px solid rgb(204, 200, 200);
  padding: 0 !important;
  font-weight: 900;
  color: black;
}

.theme--light.v-data-table > .v-data-table__wrapper > table > thead > tr > th {
  font-weight: 900;
  color: black !important;
}
.vertical {
  writing-mode: tb-rl;
  font-size: 12px;
  font-weight: bold;
  color: black;
  -webkit-transform: rotate(180deg);
  -moz-transform: rotate(180deg);
  -o-transform: rotate(180deg);
  -ms-transform: rotate(180deg);
  transform: rotate(180deg);
  white-space: nowrap;
  display: block;
  bottom: 0;
  height: 65px;
  margin-bottom: 10px;
  align-content: center;
  padding: 0;
}
.vertical2 {
  -ms-writing-mode: tb-rl;
  writing-mode: tb-rl;
  font-size: 12px;
  font-weight: bold;
  transform: rotate(180deg);
  white-space: nowrap;
  display: block;
  bottom: 0;
  height: 85px;
  margin-bottom: 5px;
  align-content: center;
  padding: 0;
}
table.v-table tbody td:first-child,
table.v-table tbody td:not(:first-child),
table.v-table tbody th:first-child,
table.v-table tbody th:not(:first-child),
table.v-table thead td:first-child,
table.v-table thead td:not(:first-child),
table.v-table thead th:first-child,
table.v-table thead th:not(:first-child) {
  padding: 0 4px;
  font-size: 0.875rem;
}
#styled-input {
  height: 20px;
}
.dpfield input {
  height: 40px !important;
}
.borderix {
  border-color: #c7c2c2 !important;
}
</style>
<script>
import { mapState } from 'vuex'
import BreadCrumb from '~/components/BreadCrumb'
import DTPicker from '~/components/DTPicker'
export default {
  layout: 'user',
  middleware: 'permission',
  components: { BreadCrumb, DTPicker },
  data: (vm) => ({
    loading: false,
    date: new Date().toISOString().substr(0, 10),
    manager_roles: [
      'admin',
      'manager',
      'manager2',
      'manager4',
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
    dateFormatted: vm.formatDate(new Date().toISOString().substr(0, 10)),
    deleted: false,
    menu: false,
    componentKey: 0,
    intervalid1: '',
    dialog: false,
    search: null,
    filter_from: null,
    filter_to: null,
    filter_status: [],
    menu2: false,
    menu3: false,
    menu4: false,
    isLoading: false,
    valid: false,
    // deleteDialog
    deleteId: '',
    deleteDialog: false,
    fromTrash: false,
    // snackbar
    color: '',
    snackbar: false,
    text: '',
    timeout: 6000,
    x: null,
    y: 'bottom',
    nameRules: [
      (v) => !!v || 'Введитие ФИО клиента',
      (v) =>
        (v && v.length >= 10) || 'ФИО клиента должно быть более 10 символов',
    ],
    dateRules: [(v) => !!v || 'Выберите дату'],
    headers: [
      {
        text: '№',
        value: 'number',
        sortable: false,
        fixed: true,
        width: '30px',
        vertical: false,
      },
      {
        text: 'Дата заявки',
        align: 'start',
        sortable: false,
        value: 'date',
        fixed: true,
        width: '100px',
        vertical: false,
      },
      {
        text: 'Дата продажи',
        align: 'start',
        sortable: false,
        value: 'sale_date',
        fixed: true,
        width: '100px',
        vertical: false,
      },
      {
        text: 'ФИО',
        sortable: false,
        align: 'center',
        width: '210px',
        value: 'client_name',
        fixed: true,
        vertical: false,
      },
      {
        text: 'Авто',
        value: 'mark.name',
        width: '180px',
        sortable: false,
        align: 'center',
        vertical: false,
      },
      {
        text: 'Vin номер',
        value: 'sale_vin',
        sortable: false,
        align: 'center',
        width: '90px',
        vertical: false,
      },
      {
        text: 'Телефон',
        value: 'phone_number',
        sortable: false,
        align: 'center',
        width: '90px',
        vertical: false,
      },
      {
        text: 'Банк',
        value: 'bank.name',
        sortable: false,
        align: 'center',
        width: '90px',
        vertical: false,
      },
      {
        text: 'Менеджер',
        value: 'sale_manager',
        width: '55px',
        sortable: false,
        align: 'center',
        vertical: false,
      },
      {
        text: 'Оформитель',
        value: 'sale_oformitel',
        width: '24px',
        sortable: false,
        vertical: false,
        class: 'px-0 mx-0',
      },
      {
        text: 'Повторная',
        value: 'sale_repeat',
        width: '24px',
        sortable: false,
        vertical: false,
        class: 'px-0 mx-0',
      },
      {
        text: 'Дата прозвона',
        value: 'sale_call_date',
        width: '24px',
        sortable: false,
        vertical: false,
        class: 'px-0 mx-0',
      },
      {
        text: 'Перезвонить',
        value: 'sale_recall_date',
        width: '24px',
        sortable: false,
        vertical: false,
        class: 'px-0 mx-0',
      },
      {
        text: 'Статус',
        value: 'sale_vote_id',
        width: '24px',
        sortable: false,
        vertical: false,
        class: 'px-0 mx-0',
      },
    ],
    votes: [
      {
        id: 1,
        value: 'Хорошо',
      },
      {
        id: 2,
        value: 'Не отвечает',
      },
      {
        id: 3,
        value: 'Кредит',
      },
      {
        id: 4,
        value: 'Авто',
      },
      {
        id: 5,
        value: 'Кредит/Авто',
      },
      {
        id: 6,
        value: 'Рассмотрение в салоне',
      },
      {
        id: 7,
        value: 'Юрист',
      },
      {
        id: 8,
        value: 'Недозвон',
      },
    ],
    saleItem: {
      id: '',
      sale_date: '',
      sale_bank_id: '',
      sale_vin: '',
      car_name: '',
      client_name: '',
      sale_manager_id: '',
      sale_creditor_id: '',
      sale_oformitel_id: '',
      sale_repeat: '',
      sale_call_date: '',
      sale_recall_date: '',
      sale_vote: '',
      sale_vote_id: '',
      sale_control: '',
      sale_comment: '',
    },
    saleDefault: {
      id: '',
      sale_date: '',
      sale_bank_id: '',
      sale_vin: '',
      car_name: '',
      client_name: '',
      sale_manager_id: '',
      sale_creditor_id: '',
      sale_oformitel_id: '',
      sale_repeat: '',
      sale_call_date: '',
      sale_recall_date: '',
      sale_vote: '',
      sale_vote_id: '',
      sale_control: '',
      sale_comment: '',
    },
  }),

  async fetch({ store, params: { id } }) {
    await store.dispatch('user/toggle', false)
    await store.dispatch('property/fetchMarks')
    await store.dispatch('credit/fetchBanks')
    await store.dispatch('showroom/fetchManagers', { showroom_id: id })
    await store.dispatch('credit/fetchSales', { id, trashed: true })
    await store.dispatch('showroom/fetchRegions')
    await store.dispatch('showroom/fetchShowroom', { id })
    //await store.dispatch('credit/fetchPreparers', { id })
  },

  computed: {
    showroom_id() {
      return Number(this.$route.params.id) || null
    },
    preparers() {
      return this.$store.state.credit.preparers
    },

    showroom() {
      return this.$store.state.showroom.showroom
    },
    formTitle() {
      return this.editedIndex === -1 ? 'Добавлеение' : 'Изменение'
    },
    ...mapState({
      credit_requests: (state) => state.credit.sales,
      banks: (state) => state.credit.banks,
    }),
    sales() {
      return this.credit_requests.filter(
        (l) => l.is_sold === 1 || l.is_sold === true
      )
    },

    user() {
      return this.$store.state.auth.user
    },
    role() {
      return this.$store.state.auth.role
    },

    regions() {
      return this.$store.state.showroom.regions
    },
    marks() {
      return this.$store.state.property.marks
    },
    managers() {
      return this.$store.state.showroom.managers.filter(
        (l) =>
          l.showroom_id === Number(this.$route.params.id) &&
          l.second_manager === null
      )
    },
    managers2() {
      return this.$store.state.showroom.managers.filter(
        (l) =>
          l.showroom_id === Number(this.$route.params.id) &&
          l.second_manager === 1
      )
    },
    links() {
      return [
        {
          text: this.role.title,
          disabled: false,
          href: '/' + this.role?.name,
        },
        {
          text: 'Продажи',
          disabled: false,
          href: '/' + this.role?.name + '/sales',
        },
        {
          text: this.showroom.name || null,
          disabled: true,
          href: '/',
        },
      ]
    },
    computedDateFormatted() {
      return this.formatDate(this.date)
    },
  },

  mounted() {
    this.handleLoading()
    /*if (this.filter_status === null && this.filter_from === null && this.filter_to === null && this.search === null ) {
      this.$echo.channel('sale').listen('SaleCreditProcessed', (e) => {
        console.log('sale')
        this.filter().catch((error) => {
          this.reload()
          console.log(error)
        })
      })
      this.$echo.channel('credits').listen('CreditCreated', (e) => {
        console.log('reloaded-mounted')
        this.filter().catch((error) => {
          this.reload()
          console.log(error)
        })
      })
    } */
  },

  watch: {
    dialog(val) {
      val || this.close()
    },
    deleted(val) {
      this.filter_from = null
      this.filter_to = null
      this.search = ''
      this.filter()
    },
  },
  methods: {
    canDelete() {
      const users = [
        'admin',
        'director',
        'coordinator',
        'coordinator1',
        'coordinator4',
        'manager4',
        'manager_avrora',
        'manager_acp',
        'manager_light',
        'petr',
        'custom',
        'mark',
      ]
      return users.includes(this.role?.name)
    },
    row_classes(status) {
      if (status === 1) {
        return 'success white--text'
      } else if (status === 2) {
        return 'red lighten-2 white--text'
      } else if (status === 3) {
        return 'light-green lighten-3'
      } else if (status === 4) {
        return 'orange darken-2 white--text'
      } else if (status === 5) {
        return 'blue lighten-2 white--text'
      } else if (status === 6) {
        return 'yellow'
      } else if (status === 8) {
        return 'red accent-4 white--text'
      } else if (status === 7) {
        return 'purple lighten-3'
      }
    },
    editItem(item) {
      this.editedIndex = item.id
      this.date = this.moment(item.date).format('YYYY-MM-DD')
      this.saleItem = Object.assign({}, item)
      if (item.sale_recall_date !== null) {
        this.saleItem.sale_recall_date = this.$moment(
          item.sale_recall_date
        ).format('DD.MM.YYYY HH:mm')
      }
      this.dialog = true
    },
    confirmDelete(id, fromTrash = false) {
      this.deleteId = id
      this.deleteDialog = true
      this.fromTrash = fromTrash
    },
    deleteItem() {
      if (this.fromTrash) {
        this.$store
          .dispatch('credit/deleteSoldCreditRequest', {
            id: this.deleteId,
            showroom_id: this.showroom_id,
          })
          .then((res) => {
            this.showSnack('success', 'Продажа удалёна из корзины')
          })
          .catch((error) => {
            this.showSnack('error', 'Ошибка: ' + error)
          })
      } else {
        this.$store
          .dispatch('credit/deleteCreditRequest', {
            id: this.deleteId,
            showroomId: this.showroom_id,
          })
          .then((res) => {
            this.showSnack('success', 'Продажа удалёна')
          })
          .catch((error) => {
            this.showSnack('error', 'Ошибка: ' + error)
          })
      }
      this.deleteId = ''
      this.dialog = false
      this.deleteDialog = false
      this.fromTrash = false
    },
    close() {
      this.dialog = false
      this.$nextTick(() => {
        this.saleItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
        this.date = ''
        this.componentKey += 1
      })
    },

    setNow(field, isDateTime = false) {
      console.log('fired')
      this.saleItem[field] = isDateTime
        ? this.$moment().format('DD.MM.YYYY HH:mm')
        : this.$moment().format('YYYY-MM-DD')
    },

    async save() {
      if (this.valid) {
        //this.loading = true
        console.log(this.saleItem)
        this.saleItem.showroom_id = this.showroom.id
        // this.saleItem.date = this.date
        await this.$store
          .dispatch('credit/saleCreditRequest', {
            item: this.saleItem,
          })
          .then((res) => {
            console.log(res)
            if (this.editedIndex > -1) {
              this.showSnack('success', 'Заявка изменён')
            } else {
              this.showSnack('success', 'Заявка добавлён')
            }
            this.close()
          })
          .catch((error) => {
            this.showSnack('error', 'Ошибка: ' + error)
          })
        //this.loading = false
      } else {
        this.showSnack('error', 'Заполните обязательные поля')
      }
    },

    async filter() {
      this.loading = true
      await this.$store.dispatch('credit/fetchSales', {
        id: this.$route.params.id,
        from: this.filter_from
          ? this.moment(this.filter_from).format('YYYY-MM-DD')
          : null,
        to: this.filter_from
          ? this.moment(this.filter_to).format('YYYY-MM-DD')
          : null,
        search: this.search,
        status: this.filter_status,
        trashed: 'true',
      })
      this.loading = false
    },

    showSnack(type, text) {
      this.text = text
      this.color = type
      this.snackbar = true
    },
    validate() {
      this.$refs.form.validate()
    },
    getManager(id) {
      const man =
        this.managers2.find((l) => l.id === id) &&
        this.managers2.find((l) => l.id === id).name
      if (man !== undefined) {
        return '(' + man + ')'
      } else {
        return ''
      }
    },
    scroll(to) {
      const table = this.$refs['my-table']
      const wrapper = table.$el.querySelector('div.v-data-table__wrapper')
      if (to === 'up') {
        this.$vuetify.goTo(table, { container: wrapper }) // to header
      } else if (to === 'down') {
        wrapper.scrollTop = wrapper.scrollHeight
      }
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
      }, 600)
    },
    reload() {
      this.intervalid1 = setTimeout(() => {
        console.log('reload')
        this.filter()
      }, 4000)
    },
    clearFilter() {
      this.filter_from = null
      this.filter_to = null
      this.filter_status = []
      this.search = null
      this.deleted = false
      this.$store.dispatch('credit/fetchSales', {
        id: this.$route.params.id,
      })
    },
    hasAccess() {
      const roles = [
        'admin',
        'director',
        'manager_acp',
        'manager_light',
        'manager_avrora',
        'manager_avangard',
        'manager_atlant',
        'manager_alarm',
        'manager_autodom',
        'manager_accent',
      ]
      return !roles.includes(this.role?.name)
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
  },

  beforeRouteLeave(to, from, next) {
    this.$store.dispatch('user/toggle', true)
    clearInterval(this.intervalid1)
    next()
  },
}
</script>

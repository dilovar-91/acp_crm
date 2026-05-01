<template>
  <div>
    <BreadCrumb :items="links"/>
    <v-container fluid class="pt-0">
      <v-row no-gutters align="start" class="d-flex">
        <v-col cols="12">
          <v-card class="mx-auto pt-0">
            <v-app-bar class="" elevate-on-scroll dense>
              <v-row class="ml-3 1">
                <v-col cols="12" sm="6" md="10">
                  <v-row>
                    <v-col cols="12" sm="6" md="2" class="hidden-sm-and-down">
                      <date-picker
                        v-model="filter_from"
                        format="DD.MM.Y"
                        placeholder="Дата с"
                        style="width: 100%; margin-top: 4px;"
                        @clear="clearFilter()"
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="2" class="hidden-sm-and-down">
                      <date-picker
                        v-model="filter_to"
                        format="DD.MM.Y"
                        placeholder="Дата до"
                        style="width: 100%; margin-top: 4px;"
                        @clear="clearFilter()"
                      />
                    </v-col>
                    <v-col cols="12" sm="6" xl="2" md="2" class="hidden-sm-and-down mt-1">
                      <v-text-field v-model="search" v-on:keyup.enter="doSearch()" clearable label="Поиск" hide-details
                                    outlined dense
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
                    <v-col cols="12" sm="6" xl="2" md="2" class="hidden-sm-and-down mt-1">
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

                    <v-col cols="12" sm="6" xl="2" md="2" class="hidden-sm-and-down mt-1">
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
                        multiple
                        dense
                        @click:clear="
                          $nextTick(() => (clearFilter()))
                        "
                      >
                        <template #selection="{ item, index }">
                          <template v-if="index === 0">
                            <span>{{ $truncate(item.name, 12) }}</span>
                          </template>
                          <span
                            v-if="index === 1"
                            class="grey--text text-caption"
                          >
                           &nbsp;(+{{ filter_status.length - 1 }})
                          </span>
                        </template>
                      </v-select>
                    </v-col>

                  </v-row>
                </v-col>
                <v-col cols="12" sm="6" md="2">
                  <v-row>
                    <v-col cols="12" sm="6" md="6" class="hidden-sm-and-down">
                      <v-btn color="success" dark class="mb-2 mt-1" @click="doSearch()">
                        Применить
                      </v-btn>
                    </v-col>
                    <v-col cols="12" sm="6" md="6" class="hidden-sm-and-down">
                      <v-btn color="error" dark class="mb-2 mt-1" @click="clearFilter()">
                        Сбросить
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-col>
              </v-row>
              <v-spacer/>
              <v-btn icon color="green" @click="exportFile()" v-if="false">
                <v-icon large>mdi-file-excel</v-icon>
              </v-btn>
              <v-menu
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
                  :color="(status_id===1 ? 'green darken-1' : 'darken-1')"
                  text
                  @click="changeStatus(1)"
                >
                  Новая
                </v-btn>
                <v-btn
                  :color="(status_id===2 ? 'green darken-1' : 'darken-1')"
                  text
                  @click="changeStatus(2)"
                >
                  В работе
                </v-btn>
                <v-btn
                  :color="(status_id===3 ? 'green darken-1' : 'darken-1')"
                  text
                  @click="changeStatus(3)"
                >
                  Не отвечает
                </v-btn>
                <v-btn
                  :color="(status_id===4 ? 'green darken-1' : 'darken-1')"
                  text
                  @click="changeStatus(4)"
                >
                  Одобрить
                </v-btn>
                <v-btn
                  :color="(status_id===5 ? 'green darken-1' : 'darken-1')"
                  text
                  @click="changeStatus(5)"
                >
                  Приедет
                </v-btn>
                <v-btn
                  :color="(status_id===6 ? 'green darken-1' : 'darken-1')"
                  text
                  @click="changeStatus(6)"
                >
                  Приехал
                </v-btn>
                <v-btn
                  :color="(status_id===7 ? 'green darken-1' : 'darken-1')"
                  text
                  @click="changeStatus(7)"
                >
                  Корзина
                </v-btn>
                <v-btn
                  :color="(status_id===8 ? 'green darken-1' : 'darken-1')"
                  text
                  @click="changeStatus(8)"
                >
                  Повторы
                </v-btn>
                <v-btn
                  :color="(status_id===null ? 'green darken-1' : 'darken-1')"
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
                <template
                  #body="{ items }"
                >
                  <tbody>
                  <tr
                    v-for="item in items"
                    :key="item.id"
                    :class="row_classes(item)"
                  >
                    <td>
                      <nuxt-link :to="'/crm/' + item.showroom_id + '/order/'+item.id + '/edit'"
                                 :class="row_classes(item)">

                        <template v-if="item.type_id === 12">
                          WhatsApp
                        </template>
                        <template v-else-if="item.site">
                          {{ item.site?.title }}
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
                    </td>
                    <td>{{ item.type?.name }}</td>
                    <td>
                      {{ item.status?.name }}
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
                    <td>{{ item.mark?.name }} {{ item.model?.name }} {{ item.complectation }}</td>

                    <td>{{ item.price | currency }}</td>
                    <td>{{ item.client_name }}</td>
                    <td>{{ item.phone | mask('+7 ### ###-##-##') }}</td>
                    <td>
                      {{ item.will_arrive ? $moment(item.will_arrive).format('DD.MM.YYYY') : '' }}
                    </td>
                    <td>{{ item.retries }}</td>
                    <td>
                      {{ $moment(item.updated_at).format('DD.MM.YYYY HH:mm') }}
                    </td>
                    <td>
                      <span v-if="item.callback !== null">{{ $moment(item.callback).format('DD.MM.YYYY HH:mm') }}</span>
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
                      <v-tooltip bottom max-width="400px" color="primary">
                        <template #activator="{ on, attrs }">
                          <div color="primary" dark v-bind="attrs" v-on="on">
                            {{ item.general_comment | truncate(50) }}
                          </div>
                        </template>
                        <span>{{ item.general_comment }}</span>
                      </v-tooltip>
                    </td>
                  </tr>
                  </tbody>
                </template>
              </v-data-table>
              <div class="text-center pt-2">
                <span class="font-weight-bold">Всего: {{ itemCount }}</span>
                <v-pagination
                  v-model="page"
                  color="blue"
                  @input="changedPage"
                  :length="pageCount"
                  :total-visible="10"
                />
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-dialog v-model="dialog" max-width="1000">
          <v-card>
            <v-card-text>
              <v-container>
                <v-form ref="form" v-model="valid" lazy-validation>
                  <v-row dense>
                    <v-col cols="12" sm="6" md="5">
                      <v-text-field
                        v-model="editedItem.client_name"
                        label="Клиент"
                        outlined
                        :rules="[v => !!v || 'Введитие имя клиента']"
                        dense
                        required
                        hide-details
                      />
                    </v-col>

                    <v-col cols="12" sm="6" md="4">
                      <v-text-field
                        v-model="editedItem.phone"
                        label="Сотовый"
                        v-mask="'+7 ### ###-##-##'"
                        outlined
                        dense
                        required
                        hide-details
                      >
                        <template
                          v-if="
                            $auth.user?.work_place > 0
                          "
                          slot="append"
                        >
                          <v-icon color="primary" @click="call()"
                          >mdi-phone
                          </v-icon
                          >
                          <v-icon color="primary" @click="dialog = true"
                          >mdi-email-outline
                          </v-icon>
                        </template>
                      </v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-select
                        v-model="editedItem.type_id"
                        :items="types"
                        hide-details
                        item-text="name"
                        item-value="id"
                        label="Тип заявки"
                        menu-props="auto"
                        outlined
                        clearable
                        required
                        dense
                        @click:clear="
                          $nextTick(() => (editedItem.type_id = null))
                        "
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
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
                        outlined
                        dense
                      />
                    </v-col>

                    <v-col cols="12" sm="6" md="3">
                      <v-autocomplete
                        v-model="editedItem.operator_id"
                        :items="operators"
                        :value="operators[editedItem.operator_id]"
                        :item-text="item => item.last_name +' '+ item.first_name "
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Оператор"
                        hide-details
                        outlined
                        dense
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="2">
                      <v-autocomplete
                        v-model="editedItem.mark_id"
                        :items="marks"
                        :value="marks[editedItem.mark_id]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Марка"
                        hide-details
                        dense
                        outlined
                        @change="getModels(editedItem.mark_id)"
                      />
                    </v-col>

                    <v-col cols="12" sm="6" md="2">
                      <v-autocomplete
                        v-model="editedItem.model_id"
                        :items="models"
                        :value="models[editedItem.model_id]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Модель"
                        hide-details
                        outlined
                        dense
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="2">
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
                        v-model="editedItem.initial_fee"
                        label="ПВ"
                        outlined
                        dense
                        hide-details
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="2">
                      <v-select
                        v-model="editedItem.payment_method"
                        :items="[
                          { id: 1, name: 'Наличными' },
                          { id: 2, name: 'В кредит' }
                        ]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Способ оплаты"
                        hide-details
                        outlined
                        dense
                        clearable
                        @click:clear="$nextTick(() => editedItem.payment_method=null)"
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="2">
                      <v-text-field
                        v-model="editedItem.entry_point"
                        label="Точква входа"
                        outlined
                        hide-details
                        readonly
                        disabled
                        dense
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="2">
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
                      <v-select
                        v-model="editedItem.status_id"
                        :items="statuses"
                        hide-details
                        item-text="name"
                        item-value="id"
                        label="Статус"
                        menu-props="auto"
                        outlined
                        clearable
                        required
                        dense
                        @click:clear="
                          $nextTick(() => (editedItem.status_id = null))
                        "
                      />
                    </v-col>
                    <v-col cols="12" sm="12" xl="3" md="3">
                      Последный прозвон
                      <DTPicker
                        v-model="editedItem.last_call"
                        value-type="DD.MM.YYYY HH:mm"
                        type="datetime"
                        format="DD.MM.YYYY HH:mm"
                        :time-picker-options="{
                                                        start: '08:00',
                                                        step: '00:15',
                                                        end: '20:00',
                                                        format: 'HH:mm',

                                                      }"
                        @setNow="setNow('last_call', true)"
                      />

                    </v-col>

                    <v-col cols="12" sm="12" xl="3" md="3" class="py-0">
                      Перезвонить
                      <DTPicker
                        v-model="editedItem.callback"
                        value-type="DD.MM.YYYY HH:mm"
                        type="datetime"
                        format="DD.MM.YYYY HH:mm"
                        :time-picker-options="{
                                                        start: '08:00',
                                                        step: '00:15',
                                                        end: '20:00',
                                                        format: 'HH:mm',
                                                      }"
                        @setNow="setNow('callback', true)"
                      />
                    </v-col>
                    <v-col cols="12" sm="12" xl="3" md="3" class="py-0">
                      Приедет
                      <date-picker
                        v-model="editedItem.will_arrive"
                        format="DD.MM.YYYY"
                        type="date"
                        value-type="YYYY-MM-DD"
                        placeholder="Приедет"
                      >
                        <template #footer>
                          <button
                            class="mx-btn mx-btn-text"
                            @click="setNow('will_arrive')"
                          >
                            Сегодня
                          </button>
                        </template>
                      </date-picker>
                    </v-col>
                    <v-col cols="12" sm="12" xl="3" md="3" class="py-0">
                      Приехал
                      <date-picker
                        v-model="editedItem.arrived_date"
                        format="DD.MM.YYYY"
                        placeholder="Приехал"
                        type="date"
                        value-type="YYYY-MM-DD"
                      >
                        <template #footer>
                          <button
                            class="mx-btn mx-btn-text"
                            @click="setNow('arrived_date')"
                          >
                            Сегодня
                          </button>
                        </template>
                      </date-picker>
                    </v-col>


                    <v-col cols="12" sm="6" md="6">
                      <v-textarea
                        v-model="editedItem.comment"
                        rows="3"
                        label="Комментарии"
                        outlined
                        hide-details
                        dense
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                      <v-textarea
                        v-model="editedItem.general_comment"
                        rows="3"
                        label="Общий комментарий"
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
              <v-btn
                color="green darken-1"
                text
                @click="toArrive()"
              >
                Передать в приезд
              </v-btn>
              <v-spacer/>
              <v-btn
                v-if="editedIndex !== -1 && $auth?.user.role_id === 1"
                color="red darken-1"
                class="mr-3"
                text
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
                @click="save()"
              >
                Сохранить
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
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
      </v-row>
    </v-container>
  </div>
</template>
<script>
import {saveAs} from 'file-saver';
import BreadCrumb from '~/components/BreadCrumb'
import DTPicker from '~/components/DTPicker'
import PhoneMask from '~/components/PhoneMask'

export default {
  name: 'CrmOrder',
  components: {BreadCrumb, DTPicker, PhoneMask},
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
    isSearch: false,
    search: '',
    mask: {
      mask: '{7} (000) 000-00-00',
      lazy: false
    },
    filter_from: null,
    filter_to: null,
    filter_site: null,
    filter_type: null,
    filter_status: null,
    status_id: null,
    menu2: false,
    menu4: false,
    isLoading: false,
    valid: false,
    // deleteDialog
    deleteId: '',
    deleteDialog: false,
    nameRules: [v => !!v || 'Введитие ФИО клиента'],
    dateRules: [v => !!v || 'Выберите дату'],
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
        text: 'Марка и модель',
        align: 'center',
        sortable: false,
        width: '90px',
        value: 'mark.name'
      },
      {
        text: 'Цена',
        align: 'start',
        width: '80px',
        sortable: false,
        value: 'price'
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
        text: 'Приедет',
        align: 'center',
        sortable: false,
        width: '70px',
        value: 'will_arrive'
      },
      {
        text: 'Повторы',
        align: 'center',
        sortable: false,
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
        text: 'Перезвонить',
        align: 'center',
        width: '30px',
        value: 'callback'
      },
      {
        text: 'Комментарий',
        align: 'center',
        width: '250px',
        sortable: false,
        value: 'comment'
      },
      {
        text: 'Общие комментарии',
        align: 'center',
        sortable: false,
        width: '250px',
        value: 'general_comment'
      }
    ],
    editedIndex: -1,
    apiForm: {
      ext_number: null,
      phone: null,
      from_number: null,
      sip: null,
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
      operator_id: '',
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
      operator_id: '',
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
      country: "",
      car_year: "",
      credit_period: "",
    },
  }),

  async fetch({store, params: {id}, $auth}) {
    await store.dispatch('user/toggle', false)
    await store.dispatch('order/fetchOrders', {id})
    await store.dispatch('order/fetchTypes')
    await store.dispatch('order/fetchStatuses')
    await store.dispatch('property/fetchMarks')
    await store.dispatch('showroom/fetchRegions')
    await store.dispatch('showroom/fetchShowroom', {id})
    await store.dispatch('showroom/fetchSites', {id})
    await store.dispatch('showroom/fetchManagers', {id})
    await store.dispatch('showroom/fetchOperators', {showroom_id: (id || $auth.user?.showroom_id)})
    //await store.dispatch('order/fetchAllOrders', {id})
    await store.dispatch('order/fetch_arrivals', {id})
  },

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
    orders() {
      return this.$store.state.order.orders
    },
    types() {
      return this.$store.state.order.types
    },
    statuses() {
      return this.$store.state.order.statuses
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
        l => l.showroom_id === Number(this.$route.params.id)
      )
    },
    marks() {
      return this.$store.state.property.marks
    },
    models() {
      return this.$store.state.property.models
    },
    operators() {
      return this.$store.state.showroom.operators.filter(
        l => l.showroom_id === Number(this.$route.params.id)
      )
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
          text: 'Заявки',
          disabled: false,
          href: '/crm/' + this.showroom_id + '/orders'
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
    },
  },

  watch: {
    dialog(val) {
      val || this.close()
    },
    /*
    tabs(value) {
      if(this.isSearch === true) return
      if(value == 8 ) {
        this.$store.dispatch('order/fetchOrders', {
          id: this.$route.params.id,
        })
      } else {
        this.$store.dispatch('order/fetchOrders', {
          id: this.$route.params.id,
          status: value,
        })
      }
    },*/
  },
  mounted() {
    this.handleLoading()
    this.$echo.channel('orders_' + this.showroom_id).listen('OrderCreated', (e) => {
      console.log('rr - ', this.isSearch === false && this.status_id <= 1 && this.page === 1)
      if (this.isSearch === false && this.status_id === null && this.page === 1) {
        setTimeout(() => {
          console.log('reload on change')
          this.$store.dispatch('order/fetchOrders', {
            id: this.$route.params.id,
            status: this.status_id
          })
        }, 900);
      } else if (this.isSearch === false && this.status_id <= 1 && this.page === 1) {
        setTimeout(() => {
          console.log('reload on change')
          this.$store.dispatch('order/fetchOrders', {
            id: this.$route.params.id,
            from: this.$moment().format('YYYY-MM-DD'),
            to: this.$moment().format('YYYY-MM-DD'),
            status: this.status_id
          })
        }, 900);
      } else {
        console.log('not reload')
      }
    })
    /*
    this.$echo.channel('orders_' + this.showroom_id).listen('OrderProcessed3434', (e) => {
      if(!this.isSearch && this.page !== 1 && this.status_id === null) {
        console.log('order proceed ---')
        this.filter()
      }
    })*/
  },
  methods: {
    editItem(item) {
      this.editedIndex = item.id
      this.date = this.$moment(item.date).format('YYYY-MM-DD')

      if (item.mark_id !== 0) {
        this.$store.dispatch('property/fetchModels', {markId: item.mark_id})
      }

      this.editedItem = Object.assign({}, item)
      if (this.editedItem.callback) {
        this.editedItem.callback = this.$moment(this.editedItem.callback).format('DD.MM.YYYY HH:mm')
      }
      if (this.editedItem.last_call) {
        this.editedItem.last_call = this.$moment(this.editedItem.last_call).format('DD.MM.YYYY HH:mm')
      }
      this.dialog = true
    },
    changedPage() {
      if (this.isSearch === true && this.filter_status == 5) {
        this.$store.dispatch('order/fetchOrders', {
          id: this.$route.params.id,
          from_arrive: this.filter_from
            ? this.$moment(this.filter_from).format('YYYY-MM-DD')
            : null,
          to_arrive: this.filter_to
            ? this.$moment(this.filter_to).format('YYYY-MM-DD')
            : null,
          site: this.filter_site,
          type: this.filter_type,
          status: this.filter_status,
          page: this.page,
        })
      } else if (this.isSearch === true) {
        console.log('this.isSearch === true')
        this.$store.dispatch('order/fetchOrders', {
          id: this.$route.params.id,
          from: this.filter_from
            ? this.$moment(this.filter_from).format('YYYY-MM-DD')
            : null,
          to: this.filter_to
            ? this.$moment(this.filter_to).format('YYYY-MM-DD')
            : null,
          site: this.filter_site,
          search: this.search,
          type: this.filter_type,
          status: this.filter_status,
          page: this.page,
        })
      } else {
        this.$store.dispatch('order/fetchOrders', {
          id: this.$route.params.id,
          from: this.filter_from
            ? this.$moment(this.filter_from).format('YYYY-MM-DD')
            : null,
          to: this.filter_to
            ? this.$moment(this.filter_to).format('YYYY-MM-DD')
            : null,
          site: this.filter_site,
          type: this.filter_type,
          status: this.status_id,
          page: this.page,
        })
      }
    },
    doSearch() {
      this.isSearch = true
      this.page = 1
      this.status_id = 1
      if (this.filter_status == 5) {
        console.log('status will arrive')
        this.$store.dispatch('order/fetchOrders', {
          id: this.$route.params.id,
          from_arrive: this.filter_from
            ? this.$moment(this.filter_from).format('YYYY-MM-DD')
            : null,
          to_arrive: this.filter_to
            ? this.$moment(this.filter_to).format('YYYY-MM-DD')
            : null,
          site: this.filter_site,
          search: this.search,
          status: this.filter_status,
          operator_id: `${this.$auth.user?.role_id === 2 ? this.$auth.user?.id : null}`,
          page: this.page,
          type: this.filter_type,
        })
      } else {
        console.log('else dosearch'),
          this.$store.dispatch('order/fetchOrders', {
            id: this.$route.params.id,
            from: this.filter_from
              ? this.$moment(this.filter_from).format('YYYY-MM-DD')
              : null,
            to: this.filter_to
              ? this.$moment(this.filter_to).format('YYYY-MM-DD')
              : null,
            site: this.filter_site,
            search: this.search,
            status: this.filter_status,
            type: this.filter_type,
          })
      }
    },
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
    close() {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
        this.componentKey += 1
        this.phone = null
        this.$refs.form.resetValidation()
        this.valid = false
      })
    },
    async save() {
      await this.validate()
      if (this.valid) {
        this.editedItem.showroom_id = this.showroom_id
        if (this.editedItem.last_call !== null) {
          const st = this.editedItem.last_call + ':00'
          this.editedItem.last_call = this.$moment(st, 'DD.MM.YYYY HH:mm:ss').isValid() ? this.$moment(st, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') : null;
        }
        if (this.editedItem.callback !== null) {
          const cb = this.editedItem.callback + ':00'
          this.editedItem.callback = this.$moment(cb, 'DD.MM.YYYY HH:mm:ss').isValid() ? this.$moment(cb, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss') : null;
        }
        this.$store
          .dispatch('order/saveOrder', {
            item: this.editedItem
          })
          .then((res) => {
            if (this.editedIndex > -1) {
              this.$toast.success("Заявка изменён");
            } else {
              this.$toast.success("Заявка добавлён");
            }
            this.close()
          })
          .catch((error) => {
            this.$toast.error('Заполните обязательные поля' + error);
          })
      } else {
        this.$toast.error('Заполните обязательные поля');
      }
    },
    async toArrive() {
      console.log(this.editedItem.model?.name != null)
      this.editedItem.car_name = `${this.editedItem.mark?.name != null ? this.editedItem.mark?.name + ' ' : ''}${this.editedItem.model?.name != null ? this.editedItem.model?.name : ''}`
      this.$store
        .dispatch('order/toArrive', {
          item: this.editedItem
        })
        .then((res) => {
          if (this.editedIndex > -1) {
            this.$toast.success("Передано в приез");
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
        // Optional parameters
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
    row_classes(item) {
      if (item.arrived === 1 && item.status_id === 5) {
        return 'yellow'
      } else {
        return
      }
    },
    getModels(markId = null) {
      this.editedItem.model_id = null
      if (markId !== 0) {
        this.$store.dispatch('property/fetchModels', {markId})
      }
    },
    clearFilter() {
      this.filter_from = null
      this.filter_to = null
      this.filter_site = null
      this.filter_type = null
      this.filter_status = null
      this.search = null
      this.isSearch = false
      this.status_id = 1
      this.page = 1
      this.$store.dispatch('order/fetchOrders', {
        id: this.$route.params.id,
        status: this.status_id,
        from: this.filter_from
          ? this.$moment(this.filter_from).format('YYYY-MM-DD')
          : null,
        to: this.filter_from
          ? this.$moment(this.filter_to).format('YYYY-MM-DD')
          : null,
      })
    },
    exportFile() {
      const XLSX = require('xlsx');
      const wb = XLSX.utils.book_new();
      const rows = this.orders?.data.map((row, index) => ({
        '№': (index + 1),
        'Дата': this.$moment(row.created_at).format('DD.MM.YYYY'),
        'Клиент': row.client_name,
        'Шоурум': row.showroom?.name,
        'Телефон': row.phone,
        'Цена': row.price,
        'ПВ': row.initial_fee,
        'Оператор': row.operator?.name,
        'Регион': row.region?.name,
        'Сайт': row.site?.title,
        'Комментарий КЦ': row.comment,
        'Общий комментарий': row.general_comment,
        'Источник рекламы': row.entry_point
      }));
      const ws = XLSX.utils.json_to_sheet(rows);
      ws["!cols"] = [
        {wch: 3},
        {wch: 14},
        {wch: 30},
        {wch: 14},
        {wch: 14},
        {wch: 12},
        {wch: 18},
        {wch: 18},
        {wch: 20},
        {wch: 20},
        {wch: 20},
        {wch: 20},
        {wch: 65},
        {wch: 65},
        {wch: 35},
      ];
      XLSX.utils.book_append_sheet(wb, ws, "Завки - " + this.showroom?.name);
      const wbout = XLSX.write(wb, {bookType: 'xlsx', type: 'array'});
      saveAs(new Blob([wbout], {type: "application/octet-stream"}), "Заявки_" + this.showroom?.name + '_' + this.$moment().format('DD-MM-YYYY') + ".xlsx");
    },
    setNow(field, isDateTime = false) {
      this.editItem[field] = isDateTime
        ? this.$moment().format("DD.MM.YYYY HH:mm")
        : this.$moment().format("YYYY-MM-DD");
    },
    onValidate(value) {
      this.editedItem.phone = value?.number
      console.log(value)
    },

    async call() {
      this.apiForm.phone = this.editedItem?.phone;
      this.apiForm.ext_number = this.$auth.user?.work_place;
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
    changeStatus(status = null) {
      this.status_id = status
      //let today = this.$moment().format('YYYY-MM-DD')
      this.page = 1
      this.$store.dispatch('order/fetchOrders', {
        id: this.$route.params.id,
        status: status,
        page: 1,
      })
    }
  }
}
</script>
<style scoped>
.wrraper-example {
  color: rgba(0, 0, 0, 0.87);
  border: 1px solid #1a202c;
  flex: 1 1 auto;
  line-height: 20px;
  max-width: 100%;
  min-width: 0px;
  width: 100%;
}

.phone-input {
  padding: 0 2.5em 0 0.5em;
  outline: none !important;
}

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

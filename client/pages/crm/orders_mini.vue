<template>
  <div>
    <BreadCrumb :items="links" />
    <v-container fluid class="pt-0">
      <v-row no-gutters align="start" class="d-flex">
        <v-col cols="12">
          <v-card class="mx-auto pt-0">
            <v-btn
              v-model="isFilter"
              text
              color="primary"
              x-small
              @click="isFilter = !isFilter"
              >Фильтр</v-btn
            >
            <v-app-bar
              v-if="isFilter"
              elevate-on-scroll
              dense
              flat
              class="py-1 px-2"
            >
              <v-row align="center" class="px-4">
                <!-- Даты -->
                <v-col cols="12" sm="2" md="1">
                  <date-picker
                    v-model="filter_from"
                    value-type="YYYY-MM-DD HH:mm"
                    format="DD.MM.Y HH:mm"
                    type="datetime"
                    placeholder="С"
                    style="width: 100%"
                    @clear="clearFilter()"
                  />
                </v-col>

                <v-col cols="12" sm="2" md="1">
                  <date-picker
                    v-model="filter_to"
                    value-type="YYYY-MM-DD HH:mm"
                    format="DD.MM.Y HH:mm"
                    type="datetime"
                    placeholder="По"
                    style="width: 100%"
                    @clear="clearFilter()"
                  />
                </v-col>

                <!-- Поиск -->
                <v-col cols="12" sm="2" md="2">
                  <v-text-field
                    v-model="search"
                    clearable
                    label="Поиск"
                    hide-details
                    dense
                    outlined
                    @keyup.enter="doSearch()"
                  />
                </v-col>

                <!-- UTM -->
                <v-col cols="12" sm="2" md="1">
                  <v-text-field
                    v-model="filter_utm"
                    clearable
                    label="UTM"
                    hide-details
                    dense
                    outlined
                    @keyup.enter="doSearch()"
                  />
                </v-col>

                <!-- Сайт -->
                <v-col cols="12" sm="2" md="1">
                  <v-autocomplete
                    v-model="filter_site"
                    :items="sites"
                    item-text="title"
                    item-value="id"
                    label="Сайт"
                    hide-details
                    dense
                    outlined
                    clearable
                    multiple
                    @click:clear="$nextTick(() => clearFilter())"
                  />
                </v-col>

                <!-- Тип -->
                <v-col cols="12" sm="2" md="1">
                  <v-select
                    v-model="filter_type"
                    :items="types"
                    item-text="name"
                    item-value="id"
                    label="Тип"
                    hide-details
                    dense
                    outlined
                    clearable
                    multiple
                    @click:clear="$nextTick(() => clearFilter())"
                  />
                </v-col>

                <!-- Статус -->
                <v-col cols="12" sm="2" md="1" class="p-2">
                  <v-select
                    v-model="filter_status"
                    :items="statuses"
                    item-text="name"
                    item-value="id"
                    label="Статус"
                    hide-details
                    dense
                    outlined
                    clearable
                    @click:clear="$nextTick(() => clearFilter())"
                  />
                </v-col>

                <v-col
                  v-if="filter_status === 6"
                  cols="12"
                  sm="12"
                  xl="1"
                  md="1"
                  class="py-0 mb-2 mt-1"
                >
                  <v-select
                    v-model="filter_arrival"
                    :items="arrival_statuses"
                    item-text="name"
                    item-value="id"
                    label="Тип"
                    dense
                    outlined
                    clearable
                    hide-details="auto"
                  />
                </v-col>

                <!-- Кнопки -->
                <v-col
                  cols="12"
                  sm="2"
                  md="2"
                  class="flex justify-end space-x-2"
                >
                  <v-btn small color="success" dark @click="doSearch()"
                    >Применить</v-btn
                  >
                  <v-btn small color="error" dark @click="clearFilter()"
                    >Сброс</v-btn
                  >
                  <v-btn
                    v-if="role_id === 7 || role_id === 1"
                    icon
                    color="warning"
                    dark
                    @click="exportOrdersToExcel()"
                  >
                    <v-icon small>mdi-file-excel</v-icon>
                  </v-btn>
                </v-col>
              </v-row>
            </v-app-bar>

            <template v-else>
              <v-app-bar class="indigo lighten-5" elevate-on-scroll dense>
                <v-row class="ml-3 1">
                  <v-col cols="12" sm="6" md="12">
                    <v-row>
                      <v-col cols="12" sm="6" md="2" class="hidden-sm-and-down">
                        <date-picker
                          v-model="advanced_from"
                          value-type="YYYY-MM-DD HH:mm"
                          format="DD.MM.Y HH:mm"
                          type="datetime"
                          placeholder="Дата с"
                          style="width: 100%; margin-top: 4px"
                          @clear="clearFilter()"
                        />
                      </v-col>
                      <v-col cols="12" sm="6" md="2" class="hidden-sm-and-down">
                        <date-picker
                          v-model="advanced_to"
                          value-type="YYYY-MM-DD HH:mm"
                          format="DD.MM.Y HH:mm"
                          type="datetime"
                          placeholder="Дата по"
                          style="width: 100%; margin-top: 4px"
                          @clear="clearFilter()"
                        />
                      </v-col>
                      <v-col
                        cols="12"
                        sm="6"
                        xl="2"
                        md="2"
                        class="hidden-sm-and-down mt-1"
                      >
                        <v-select
                          v-model="advanced_filter_type"
                          :items="[
                            { id: 1, name: 'Дата создание' },
                            { id: 2, name: 'Дата изменение' },
                            { id: 3, name: 'Приедет' },
                            { id: 4, name: 'Приехал' },
                            { id: 5, name: 'Перезвонить' },
                            { id: 6, name: 'Последный прозвон' },
                          ]"
                          hide-details
                          item-text="name"
                          item-value="id"
                          label="Тип фильтра"
                          menu-props="auto"
                          style="width: 100%"
                          outlined
                          clearable
                          required
                          dense
                          @click:clear="$nextTick(() => clearFilter())"
                        >
                        </v-select>
                      </v-col>

                      <v-col
                        cols="12"
                        sm="6"
                        xl="2"
                        md="2"
                        class="hidden-sm-and-down mt-1"
                      >
                        <v-text-field
                          v-model="advanced_search"
                          clearable
                          label="Поиск"
                          hide-details
                          outlined
                          dense
                          @keyup.enter="doAdvancedSearch()"
                        >
                        </v-text-field>
                      </v-col>

                      <v-col
                        cols="12"
                        sm="6"
                        xl="2"
                        md="2"
                        class="hidden-sm-and-down mt-1"
                      >
                        <v-select
                          v-model="advanced_site"
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
                          <template #selection="{ item, index }">
                            <template v-if="index === 0">
                              <span>{{ $truncate(item.title, 10) }}</span>
                            </template>
                            <span
                              v-if="index === 1"
                              class="grey--text text-caption"
                            >
                              &nbsp;(+{{ advanced_site.length - 1 }})
                            </span>
                          </template>
                        </v-select>
                      </v-col>

                      <v-col
                        cols="12"
                        sm="6"
                        xl="2"
                        md="2"
                        class="hidden-sm-and-down mt-1"
                      >
                        <v-select
                          v-model="advanced_status"
                          :items="statuses"
                          hide-details
                          item-text="name"
                          item-value="id"
                          label="Статус"
                          menu-props="auto"
                          style="width: 120%"
                          outlined
                          clearable
                          required
                          multiple
                          dense
                          @click:clear="$nextTick(() => clearFilter())"
                        >
                          <template #selection="{ item, index }">
                            <template v-if="index === 0">
                              <span>{{ $truncate(item.name, 12) }}</span>
                            </template>
                            <span
                              v-if="index === 1"
                              class="grey--text text-caption"
                            >
                              &nbsp;(+{{ advanced_status?.length - 1 }})
                            </span>
                          </template>
                        </v-select>
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>
                <v-spacer />
              </v-app-bar>
              <v-app-bar class="indigo lighten-5" elevate-on-scroll dense>
                <v-row class="ml-3 1">
                  <v-col cols="12" sm="6" md="10">
                    <v-row>
                      <v-col
                        v-if="role_id !== 2"
                        cols="12"
                        sm="6"
                        xl="2"
                        md="2"
                        class="hidden-sm-and-down mt-1"
                      >
                        <v-select
                          v-model="advanced_operator"
                          :items="operators"
                          hide-details
                          :item-text="
                            (item) =>
                              item.last_name
                                ? item.first_name + ' ' + item.last_name
                                : item.first_name
                          "
                          item-value="id"
                          label="Оператор"
                          menu-props="auto"
                          style="width: 120%"
                          outlined
                          clearable
                          required
                          dense
                          @click:clear="$nextTick(() => clearFilter())"
                        >
                        </v-select>
                      </v-col>
                      <v-col
                        cols="12"
                        sm="6"
                        xl="2"
                        md="2"
                        class="hidden-sm-and-down mt-1"
                      >
                        <v-select
                          v-model="advanced_type"
                          :items="types"
                          hide-details
                          item-text="name"
                          item-value="id"
                          label="Тип заявки"
                          menu-props="auto"
                          style="width: 100%"
                          outlined
                          clearable
                          required
                          multiple
                          dense
                          @click:clear="$nextTick(() => clearFilter())"
                        >
                          <template #selection="{ item, index }">
                            <template v-if="index === 0">
                              <span>{{ $truncate(item.name, 10) }}</span>
                            </template>
                            <span
                              v-if="index === 1"
                              class="grey--text text-caption"
                            >
                              &nbsp;(+{{ advanced_type.length - 1 }})
                            </span>
                          </template>
                        </v-select>
                      </v-col>
                      <v-col
                        cols="12"
                        sm="6"
                        md="2"
                        class="hidden-sm-and-down mt-1"
                      >
                        <v-select
                          v-model="advanced_payment"
                          :items="[
                            { id: 7, name: 'Не определено' },
                            { id: 1, name: 'Наличными' },
                            { id: 2, name: 'В кредит' },
                            { id: 3, name: 'Кредит(Скидка)' },
                            { id: 4, name: 'Лизинг' },
                            { id: 5, name: 'Не дозвон' },
                            { id: 6, name: 'Повтор' },
                            { id: 8, name: 'ЛНР/ДНР' },
                          ]"
                          item-text="name"
                          no-data-text="Нету данных"
                          item-value="id"
                          menu-props="auto"
                          label="Способ оплаты"
                          outlined
                          hide-details
                          dense
                          clearable
                          @click:clear="
                            $nextTick(() => (advanced_payment_method = null))
                          "
                        />
                      </v-col>

                      <v-col
                        cols="12"
                        sm="6"
                        xl="2"
                        md="2"
                        class="hidden-sm-and-down mt-1"
                      >
                        <v-select
                          v-model="advanced_mark"
                          :items="marks"
                          hide-details
                          item-text="name"
                          item-value="id"
                          label="Марка"
                          menu-props="auto"
                          style="width: 100%"
                          outlined
                          clearable
                          required
                          multiple
                          dense
                          @click:clear="$nextTick(() => clearFilter())"
                        >
                          <template #selection="{ item, index }">
                            <template v-if="index === 0">
                              <span>{{ $truncate(item.name, 10) }}</span>
                            </template>
                            <span
                              v-if="index === 1"
                              class="grey--text text-caption"
                            >
                              &nbsp;(+{{ advanced_mark?.length - 1 }})
                            </span>
                          </template>
                        </v-select>
                      </v-col>
                      <v-col
                        v-if="false"
                        cols="12"
                        sm="6"
                        xl="2"
                        md="2"
                        class="hidden-sm-and-down mt-1"
                      >
                        <v-select
                          v-model="advanced_mark"
                          :items="models"
                          hide-details
                          item-text="name"
                          item-value="id"
                          label="Модель"
                          menu-props="auto"
                          style="width: 100%"
                          outlined
                          clearable
                          required
                          multiple
                          dense
                          @click:clear="$nextTick(() => clearFilter())"
                        >
                        </v-select>
                      </v-col>
                      <v-col
                        v-if="false"
                        cols="12"
                        sm="6"
                        xl="2"
                        md="2"
                        class="hidden-sm-and-down mt-1"
                      >
                        <v-select
                          v-model="advanced_type"
                          :items="types"
                          hide-details
                          item-text="name"
                          item-value="id"
                          label="Регион"
                          menu-props="auto"
                          style="width: 100%"
                          outlined
                          clearable
                          required
                          multiple
                          dense
                          @click:clear="$nextTick(() => clearFilter())"
                        >
                          <template #selection="{ item, index }">
                            <template v-if="index === 0">
                              <span>{{ $truncate(item.name, 10) }}</span>
                            </template>
                            <span
                              v-if="index === 1"
                              class="grey--text text-caption"
                            >
                              &nbsp;(+{{ advanced_type.length - 1 }})
                            </span>
                          </template>
                        </v-select>
                      </v-col>

                      <v-col
                        cols="12"
                        sm="6"
                        xl="2"
                        md="2"
                        class="hidden-sm-and-down mt-1"
                      >
                        <v-btn
                          color="success"
                          dark
                          class="mb-2 mt-1"
                          @click="doAdvancedSearch()"
                        >
                          Применить
                        </v-btn>
                      </v-col>
                      <v-col
                        cols="12"
                        sm="6"
                        xl="2"
                        md="2"
                        class="hidden-sm-and-down mt-1"
                      >
                        <v-btn
                          class="mb-2 mt-1"
                          dark
                          color="red"
                          @click="clearAdvanced()"
                        >
                          Сбросить
                        </v-btn>
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>
                <v-spacer />
              </v-app-bar>
            </template>
            <v-card-text class="pa-0 py-0">
              <template v-if="!isSearch">
                <v-btn
                  :color="filter_status == 1 ? 'green darken-1' : 'darken-1'"
                  text
                  @click="changeStatus(1)"
                >
                  Новая
                </v-btn>
                <v-btn
                  :color="filter_status == 2 ? 'green darken-1' : 'darken-1'"
                  text
                  @click="changeStatus(2)"
                >
                  В работе
                </v-btn>
                <v-btn
                  :color="filter_status == 3 ? 'green darken-1' : 'darken-1'"
                  text
                  @click="changeStatus(3)"
                >
                  Не отвечает
                </v-btn>
                <v-btn
                  :color="filter_status == 4 ? 'green darken-1' : 'darken-1'"
                  text
                  @click="changeStatus(4)"
                >
                  Одобрить
                </v-btn>
                <v-btn
                  :color="filter_status == 5 ? 'green darken-1' : 'darken-1'"
                  text
                  @click="changeStatus(5)"
                >
                  Приедет
                </v-btn>
                <v-btn
                  :color="filter_status == 6 ? 'green darken-1' : 'darken-1'"
                  text
                  @click="changeStatus(6)"
                >
                  Приехал
                </v-btn>
                <v-btn
                  :color="filter_status == 7 ? 'green darken-1' : 'darken-1'"
                  text
                  @click="changeStatus(7)"
                >
                  Корзина
                </v-btn>
                <v-btn
                  :color="filter_status == 8 ? 'green darken-1' : 'darken-1'"
                  text
                  @click="changeStatus(8)"
                >
                  Повторы
                </v-btn>
                <v-btn
                  :color="filter_status == null ? 'green darken-1' : 'darken-1'"
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
                <template #body="{ items }">
                  <tbody>
                    <tr
                      v-for="item in items"
                      :key="item.id"
                      :class="row_classes(item)"
                    >
                      <td>
                        <nuxt-link
                          :to="
                            '/crm/' +
                            item.showroom_id +
                            '/order/' +
                            item.id +
                            '/detail'
                          "
                          :class="row_classes(item)"
                        >
                          <template v-if="item.type_id === 12">
                            WhatsApp
                          </template>
                          <template v-else-if="item.site">
                            {{ item.site?.title }}
                          </template>
                          <template v-else-if="item.line_number">
                            {{ item.line_number }}
                          </template>
                          <template
                            v-else-if="
                              item.site_id === null && item.source_id > 0
                            "
                          >
                            {{ item.source?.name }}
                          </template>
                          <template v-else> Не определено </template>
                        </nuxt-link>
                      </td>

                      <td>{{ item.type?.name }}</td>
                      <td>
                        {{ item.status?.name }}

                        <p
                          v-if="item.status_id === 7 && item.trash"
                          style="color: orangered"
                        >
                          ({{ item.trash?.name }})
                        </p>

                        <p
                          v-if="item.status_id === 6 && item.arrival_status"
                          style="color: #26c6da"
                        >
                          ({{ item.arrival_status?.name }})
                        </p>
                      </td>
                      <td style="white-space: normal; word-break: break-word">
                        <div v-if="item.utm_source" style="color: #e74c3c">
                          source: {{ item.utm_source }}
                        </div>
                        <div v-if="item.utm_medium" style="color: #2980b9">
                          medium: {{ item.utm_medium }}
                        </div>
                        <div v-if="item.utm_campaign" style="color: #27ae60">
                          campaign: {{ item.utm_campaign }}
                        </div>
                        <div v-if="item.utm_content" style="color: #8e44ad">
                          content: {{ item.utm_content }}
                        </div>
                        <div v-if="item.utm_term" style="color: #f39c12">
                          term: {{ item.utm_term }}
                        </div>
                      </td>
                      <td>
                        {{ item.region?.name }}
                      </td>
                      <td>
                        {{
                          $moment(item.created_at).format('DD.MM.YYYY HH:mm')
                        }}
                      </td>
                      <td>
                        {{ item.mark?.name }} {{ item.model?.name }}
                        {{ item.complectation }}
                      </td>

                      <td>{{ item.client_name }}</td>
                      <td>{{ item.phone | mask('+7 ### ###-##-##') }}</td>
                      <td>
                        {{
                          item.will_arrive
                            ? $moment(item.will_arrive).format('DD.MM.YYYY')
                            : ''
                        }}
                      </td>
                      <td>{{ item.retries }}</td>
                      <td>
                        {{
                          $moment(item.updated_at).format('DD.MM.YYYY HH:mm')
                        }}
                      </td>
                      <td>
                        <span v-if="item.callback !== null">{{
                          $moment(item.callback).format('DD.MM.YYYY HH:mm')
                        }}</span>
                      </td>
                      <td>
                        <v-tooltip bottom max-width="400px" color="primary">
                          <template #activator="{ on, attrs }">
                            <div color="primary" dark v-bind="attrs" v-on="on">
                              {{ item.comment | truncate(100) }}
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
                  :length="pageCount"
                  :total-visible="10"
                  @input="changedPage"
                />
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>
<script>
import BreadCrumb from '~/components/BreadCrumb'
import { exportToExcel } from '~/utils/exportExcel'
export default {
  name: 'CrmOrdersMini',
  components: { BreadCrumb },
  beforeRouteLeave(to, from, next) {
    this.$store.dispatch('user/toggle', true)
    clearInterval(this.intervalid)
    next()
  },
  layout: 'agency',
  middleware: 'permission',
  data: () => ({
    menu: false,
    componentKey: 0,
    page: 1,
    isFilter: true,
    toShowroom: null,
    callback: null,
    last_call: null,
    repeats: [],
    isSearch: false,
    search: '',
    mask: {
      mask: '{7} (000) 000-00-00',
      lazy: false,
    },
    filter_from: null,
    filter_to: null,
    filter_site: null,
    filter_type: null,
    filter_operator: null,
    filter_status: null,
    filter_arrival: null,
    filter_utm: null,
    status_id: null,
    // advanced filter
    advanced_search: null,
    advanced_operator: null,
    advanced_type: null,
    advanced_filter_type: null,
    advanced_site: null,
    advanced_status: null,
    advanced_from: null,
    advanced_to: null,
    advanced_payment: null,
    advanced_region: null,
    advanced_mark: null,
    advanced_model: null,
    menu2: false,
    menu4: false,
    isLoading: false,
    valid: false,
    nameRules: [(v) => !!v || 'Введитие ФИО клиента'],
    dateRules: [(v) => !!v || 'Выберите дату'],
    headers: [
      {
        text: 'Сайт',
        align: 'center',
        width: '120px',
        value: 'id',
      },

      {
        text: 'Тип',
        align: 'center',
        sortable: false,
        width: '60px',
        value: 'type.name',
      },
      {
        text: 'Состояние заявки',
        align: 'center',
        sortable: false,
        width: '90px',
        value: 'order.status',
      },
      {
        text: 'UTM метки',
        align: 'center',
        width: '160px',
        value: 'utm',
      },
      {
        text: 'Регион',
        align: 'center',
        sortable: false,
        width: '80px',
        value: 'region.name',
      },
      {
        text: 'Дата создания',
        align: 'center',
        sortable: false,
        width: '90px',
        value: 'created_at',
      },
      {
        text: 'Марка и модель',
        align: 'center',
        sortable: false,
        width: '90px',
        value: 'mark.name',
      },
      {
        text: 'Клиент',
        align: 'center',
        sortable: false,
        width: '140px',
        value: 'client_name',
      },
      {
        text: 'Сотовый',
        align: 'center',
        sortable: false,
        width: '140px',
        value: 'phone',
      },
      {
        text: 'Приедет',
        align: 'center',
        sortable: false,
        width: '70px',
        value: 'will_arrive',
      },
      {
        text: 'Повторы',
        align: 'center',
        sortable: false,
        width: '8px',
        value: 'retries',
      },
      {
        text: 'Дата изменения',
        align: 'center',
        width: '30px',
        value: 'comment',
      },
      {
        text: 'Перезвонить',
        align: 'center',
        width: '30px',
        value: 'callback',
      },
      {
        text: 'Комментарий',
        align: 'center',
        width: '250px',
        sortable: false,
        value: 'comment',
      },
      {
        text: 'Общие комментарии',
        align: 'center',
        sortable: false,
        width: '250px',
        value: 'general_comment',
      },
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
      operator_id: '',
      comment: '',
      general_comment: '',
      status_id: '',
      entry_point: '',
      last_call: '',
      callback: '',
      live_region: '',
      ads_source: '',
      will_arrive: '',
      arrived: '',
      arrived_date: '',
      date_of_sale: '',
      call_heard: '',
      type_id: '',
      country: '',
      car_year: '',
      credit_period: '',
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
      live_region: '',
      ads_source: '',
      will_arrive: '',
      arrived: '',
      arrived_date: '',
      date_of_sale: '',
      call_heard: '',
      type_id: '',
      country: '',
      car_year: '',
      credit_period: '',
    },
  }),

  async fetch({ store, params: { id }, $auth }) {
    await store.dispatch('user/toggle', false)
    // await store.dispatch('order/fetchOrders', {id})

    await store.dispatch('order/fetchTypes')
    await store.dispatch('order/fetchStatuses')
    await store.dispatch('property/fetchMarks')
    await store.dispatch('showroom/fetchRegions')
    await store.dispatch('showroom/fetchShowroom', { id })
    await store.dispatch('showroom/fetchShowrooms')
    await store.dispatch('showroom/fetchSites', { id })
    await store.dispatch('showroom/fetchManagers', { id })
    await store.dispatch('order/fetchArrivalStatuses')
    await store.dispatch('showroom/fetchOperators', {
      showroom_id: id || $auth.user?.showroom_id,
    })
    await store.dispatch('order/fetchAllOrders', { id })
    await store.dispatch('order/fetch_arrivals', { id })
    await store.dispatch('order/fetch_missed_calls', { id })
  },

  computed: {
    showroom_id() {
      return Number(this.$route.params.id) || null
    },
    agency() {
      return Number(this.$route.params.agency) || null
    },
    role_id() {
      return this.$auth.user?.role_id
    },
    showroom() {
      return this.$store.state.showroom.showroom
    },
    showrooms() {
      return this.$store.state.showroom.showrooms
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
      return this.$store.state.showroom.sites
    },
    marks() {
      return this.$store.state.property.marks
    },
    models() {
      return this.$store.state.property.models
    },
    operators() {
      return this.$store.state.showroom.operators
    },

    arrival_statuses() {
      return this.$store.state.order.arrival_statuses
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
          href: '/crm/' + this.showroom_id,
        },
        {
          text: 'Заявки',
          disabled: false,
          href: '/crm/' + this.showroom_id + '/orders',
        },
        {
          text: this.showroom.name || null,
          disabled: true,
          href: '/',
        },
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
    repeatDialog(val) {
      val || (this.repeats = [])
    },
  },
  mounted() {
    this.handleLoading()
    this.$echo
      .channel('orders_' + this.showroom_id)
      .listen('OrderCreated', (e) => {
        if (
          this.filter_status === null &&
          this.filter_from === null &&
          this.filter_to === null &&
          this.page === 1 &&
          this.dialog !== true
        ) {
          setTimeout(async () => {
            console.log('reload on create')
            await this.refresh_page()
          }, 900)
        } else {
          console.log('not reload')
        }
      })
    this.$echo
      .channel('orders_' + this.showroom_id)
      .listen('OrderProcessed', (e) => {
        if (
          this.filter_status === null &&
          this.filter_from === null &&
          this.filter_to === null &&
          this.page === 1 &&
          this.dialog !== true
        ) {
          setTimeout(async () => {
            console.log('reload on change')
            await this.refresh_page()
          }, 900)
        } else {
          console.log('not reload')
        }
      })
  },
  destroyed() {
    this.$echo.leave('orders_' + this.showroom_id)
  },

  created() {
    if (this.$route.query.from) {
      this.filter_from = this.$route.query.from
      this.advanced_from = this.$route.query.from
    }
    if (this.$route.query.to) {
      this.filter_to = this.$route.query.to
      this.advanced_to = this.$route.query.to
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
      const str = this.$route.query.status
      if (str.includes(',')) {
        const arr = str.split(',')
        this.filter_status = arr.map((x) => parseInt(x.trim()))
        this.advanced_status = arr.map((x) => parseInt(x.trim()))
      } else {
        this.filter_status = this.$route.query.status
        this.advanced_status = this.$route.query.status
      }
    }
    if (this.$route.query.site_id) {
      const str = this.$route.query.site_id
      const arr = str.split(',')
      this.filter_site = arr.map((x) => parseInt(x.trim()))
      this.advanced_site = arr.map((x) => parseInt(x.trim()))
    }
    if (this.$route.query.type_id) {
      const str = this.$route.query.type_id
      if (str.includes(',')) {
        const arr = str.split(',')
        this.filter_type = arr.map((x) => parseInt(x.trim()))
        this.advanced_type = arr.map((x) => parseInt(x.trim()))
      } else {
        this.filter_type = this.$route.query.type_id
        this.advanced_type = this.$route.query.type_id
      }
    }
    if (this.$route.query.utm) {
      this.filter_utm = this.$route.query.utm
    }
    if (this.$route.query.page) {
      this.page = parseInt(this.$route.query.page) || null
    }
    this.refresh_page()
  },

  methods: {
    async refresh_page() {
      await this.$store.dispatch('order/fetchOrders2', {
        id: this.$route.params.id,
        query: this.$route.query,
        agency: this.agency,
      })
    },

    async changedPage() {
      const { query } = this.$route
      try {
        await this.$router.push({ query: { ...query, page: this.page } })
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') {
          //
        }
      }
      await this.$store.dispatch('order/fetchOrders2', {
        id: this.$route.params.id,
        query: this.$route.query,
        agency: this.agency,
      })
    },
    async doSearch() {
      const { query } = this.$route
      try {
        console.log(this.filter_arrival)
        await this.$router.push({
          query: {
            // ...query,
            ...(this.search && { search: this.search }),
            ...(this.filter_operator && { operator_id: this.filter_operator }),
            ...(this.filter_type?.length && {
              type_id: this.filter_type.join(','),
            }),
            ...(this.filter_site?.length && {
              site_id: this.filter_site.join(','),
            }),
            ...(this.filter_status && { status: this.filter_status }),
            ...(this.filter_arrival && { arrived_type: this.filter_arrival }),
            ...(this.filter_utm && { utm: this.filter_utm }),
            ...(this.filter_status?.length && {
              status: this.filter_status.join(','),
            }),
            ...(this.filter_from && { from: this.filter_from + ':00' }),
            ...(this.filter_to && { to: this.filter_to + ':59' }),
            page: 1,
          },
        })
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') {
          //
        }
      } finally {
        await this.$store.dispatch('order/fetchOrders2', {
          id: this.$route.params.id,
          query: this.$route.query,
          agency: this.agency,
        })
        this.page = 1
      }
    },
    async doAdvancedSearch() {
      try {
        console.log(this.advanced_payment)
        if (this.advanced_status) {
          this.filter_status = this.advanced_status
        }
        await this.$router.push({
          query: {
            ...(this.advanced_search && { search: this.advanced_search }),
            ...(this.advanced_operator && {
              operator_id: this.advanced_operator,
            }),
            ...(this.advanced_filter_type && {
              date_type: this.advanced_filter_type,
            }),
            ...(this.advanced_type?.length && {
              type_id: this.advanced_type.join(','),
            }),
            ...(this.advanced_site?.length && {
              site_id: this.advanced_site.join(','),
            }),
            ...(this.advanced_status && { status: this.advanced_status }),
            ...(this.advanced_status?.length && {
              status: this.advanced_status.join(','),
            }),
            ...(this.advanced_from && { from: this.advanced_from }),
            ...(this.advanced_to && { to: this.advanced_to }),
            ...(this.advanced_payment && {
              payment_method: this.advanced_payment,
            }),
            ...(this.advanced_mark && { mark_id: this.advanced_mark }),
            ...(this.advanced_model && { model_id: this.advanced_model }),
            ...(this.advanced_region && { region_id: this.advanced_region }),
            ...(this.advanced_phone && { phone: this.advanced_phone }),
            ...(this.advanced_arrived && { arrived: this.advanced_arrived }),
            ...(this.advanced_drop && { drop_id: this.advanced_drop }),
            ...(this.not_confirmed && { not_confirmed: this.not_confirmed }),
            page: 1,
          },
        })
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') {
        }
      } finally {
        await this.$store.dispatch('order/fetchOrders2', {
          id: this.$route.params.id,
          query: this.$route.query,
          agency: this.agency,
        })
        this.page = 1
      }
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
      if (item.arrived === 1 && item.status_id === 5) {
        return 'yellow'
      } else {
      }
    },
    async clearFilter() {
      this.filter_from = null
      this.filter_to = null
      this.filter_site = null
      this.filter_type = null
      this.filter_status = null
      this.filter_operator = null
      this.search = null
      this.page = 1
      const { query } = this.$route
      try {
        await this.$router.push({
          query: {},
        })
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') {
        }
      } finally {
        await this.$store.dispatch('order/fetchOrders2', {
          id: this.$route.params.id,
          query: {},
          agency: this.agency,
        })
      }
    },
    async reset() {
      this.filter_from = null
      this.filter_to = null
      this.filter_site = null
      this.filter_type = null
      this.filter_status = null
      this.filter_operator = null
      this.search = null
      this.page = 1
      try {
        await this.$router.push({
          query: {},
        })
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') {
        }
      }
    },
    async clearAdvanced() {
      this.advanced_from = null
      this.advanced_to = null
      this.advanced_site = null
      this.advanced_type = null
      this.advanced_search = null
      this.advanced_status = null
      this.advanced_operator = null
      this.advanced_filter_type = null
      this.filter_status = null

      this.page = 1
      try {
        await this.$router.push({
          query: { page: 1 },
        })
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') {
        }
      }
    },

    onValidate(value) {
      this.editedItem.phone = value?.number
      console.log(value)
    },

    async call() {
      this.apiForm.phone = this.editedItem?.phone
      this.apiForm.ext_number = this.$auth.user?.work_place
      this.apiForm.showroom_id = this.$route.params?.id
      await this.$axios
        .post('/call', this.apiForm, {
          headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
        })
        .then((response) => {
          if (response.status === 200) {
            this.$toast.success('Ожидайте идёт звонок...')
          }
          console.log(response.data)
        })
        .catch((error) => {
          this.$toast.error(
            'Произошла ошибка проверьте правильность телефона!!!' + error
          )
        })
    },
    async changeStatus(id = null) {
      // await this.reset()
      const { query } = this.$route
      this.filter_status = id
      this.page = 1
      try {
        await this.$router.push({ query: { ...query, page: 1, status: id } })
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') {
        }
      }
      // console.log(this.$route.query)
      await this.refresh_page()
    },
    validatePayment(value) {
      if (
        value !== null ||
        this.editedItem?.status_id === 3 ||
        this.editedItem?.status_id === 1 ||
        this.editedItem?.status_id === 8
      ) {
        return true
      } else {
        return 'Выберите способ оплаты'
      }
    },

    exportOrdersToExcel() {
      exportToExcel({
        from: this.filter_from,
        to: this.filter_to,
        site_id: this.filter_site,
        type_id: this.filter_type,
        status_id: this.filter_status,
        agency_id: this.agency,
        showroom_id: this.showroom_id,
      })
    },
  },
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

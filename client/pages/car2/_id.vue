<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links" />
      <v-row no-gutters align="start" class="d-flex">
        <v-col cols="12">
          <v-data-table
            :headers="headers"
            :items="filtered"
            :items-per-page="filtered.length"
            :options="options"
            item-key="id"
            hide-default-footer
            class="elevation-1 grey myTable lighten-4"
            height="80vh"
            fixed-header
            :mobile-breakpoint="0"
          >
            <template #body="{ items }">
              <tbody>
                <tr
                  v-for="item in items"
                  :key="item.name"
                  :class="row_classes(item)"
                  @dblclick="editItem(item)"
                >
                  <td>
                    <v-chip
                      v-if="item.showroom && item.showroom.id === 1"
                      color="yellow"
                      small
                    >
                      {{ item.showroom.name }}
                    </v-chip>
                    <v-chip
                      v-else-if="item.showroom && item.showroom.id === 2"
                      color="primary"
                      text-color="white"
                      small
                    >
                      {{ item.showroom.name }}
                    </v-chip>
                    <v-chip v-else color="green" text-color="white" x-small>
                      {{ item.showroom && item.showroom.name }}
                    </v-chip>
                  </td>
                  <td>
                    <span v-if="item.link !== null"><a :href="item.link" class="link" rel="noreferrer" target="_blank">
                      Перейти
                    </a></span>
                  </td>
                  <td>
                    {{ item.avito_price }}
                  </td>
                  <td>{{ item.mark && item.mark.name }}</td>
                  <td>{{ item.model && item.model.name }}</td>
                  <td>{{ item.complectation }}</td>
                  <td>{{ item.year }}</td>
                  <td>{{ item.volume }}</td>
                  <td>{{ item.power }}</td>
                  <td>{{ item.wheeltype && item.wheeltype.name }}</td>
                  <td>{{ item.transmission && item.transmission.name }}</td>
                  <td>{{ item.color }}</td>
                  <td>{{ item.vin_number }}</td>
                  <td>{{ item.body_number }}</td>
                  <td>{{ item.price }}</td>
                  <td>
                    <span v-if="item.ptc_type === 1" key="pts">ПТС</span>
                    <span v-else-if="item.ptc_type === 2" key="epts">ЭПТС</span>
                    <span v-else-if="item.ptc_type === 3" key="epts">КОПИЯ</span>
                    <span v-else key="no">НЕТ</span>
                  </td>
                  <td>{{ item.supplier }}</td>
                  <td>{{ item.key_number }}</td>
                  <td>
                    <v-chip
                      v-if="item.is_sold === 1 || item.is_sold === true"
                      color="green"
                      text-color="white"
                      x-small
                    >
                      Продана
                    </v-chip>
                    <v-chip
                      v-else-if="item.status_id === 1"
                      color="green"
                      text-color="white"
                      x-small
                    >
                      Продана
                    </v-chip>
                    <v-chip
                      v-else-if="item.status_id === 2"
                      color="green"
                      text-color="white"
                      x-small
                    >
                      В продаже
                    </v-chip>
                  </td>
                </tr>
              </tbody>
            </template>

            <template #top>
              <v-toolbar flat dense>
                <v-row class="mt-2 hidden-sm-and-down">
                  <v-col cols="4" md="8" xs="6">
                    <v-row>
                      <v-col cols="3" md="4" xs="6">
                        <v-select
                          v-model="filter.mark_id"
                          :items="marks"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Марка"
                          outlined
                          hide-details
                          clearable
                          @change="
                            getModels(filter.mark_id);
                            deleted = false;
                          "
                          @click="filter.model_id = null"
                          @click:clear="
                            $nextTick(() => (filter.mark_id = null))
                          "
                        />
                      </v-col>
                      <v-col cols="3" md="4" xs="6">
                        <v-select
                          v-model="filter.model_id"
                          :items="models"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Модель"
                          hide-details
                          outlined
                          clearable
                          @click="deleted = false"
                          @click:clear="
                            $nextTick(() => (filter.model_id = null))
                          "
                        >
                          <template #no-data>
                            <span class="small">Выберите марку</span>
                          </template>
                        </v-select>
                      </v-col>
                      <v-col cols="3" md="2" xs="6">
                        <v-select
                          v-model="filter.transmission_id"
                          :items="transmissions"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Коробка"
                          hide-details
                          outlined
                          clearable
                          @click="deleted = false"
                          @click:clear="
                            $nextTick(() => (filter.transmission_id = null))
                          "
                        />
                      </v-col>
                      <v-col cols="3" md="2" xs="6">
                        <v-select
                          v-model="filter.wheeltype_id"
                          :items="wheeltypes"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Привод"
                          hide-details
                          outlined
                          clearable
                          @click="deleted = false"
                          @click:clear="
                            $nextTick(() => (filter.wheeltype_id = null))
                          "
                        />
                      </v-col>
                    </v-row>
                  </v-col>
                  <v-col cols="4" md="4" xs="6">
                    <v-row>
                      <v-col cols="3" md="4" xs="6">
                        <v-select
                          v-model="filter.year_from"
                          :items="$range(2022, 1960)"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Год от"
                          hide-details
                          outlined
                          clearable
                          @click="deleted = false"
                          @click:clear="
                            $nextTick(() => (filter.year_from = null))
                          "
                        />
                      </v-col>
                      <v-col cols="3" md="4" xs="6">
                        <v-select
                          v-model="filter.year_to"
                          :items="$range(2022, 1960)"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Год до"
                          hide-details
                          outlined
                          clearable
                          @click="deleted = false"
                          @click:clear="
                            $nextTick(() => (filter.year_to = null))
                          "
                        />
                      </v-col>
                      <v-col cols="3" md="4" xs="6">
                        <v-btn color="error" dark @click="clearFilter()">
                          Сброс
                        </v-btn>
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>

                <v-dialog v-model="dialog" max-width="900px">
                  <v-card>
                    <v-card-title>
                      <span class="headline">{{ formTitle }}</span>
                    </v-card-title>
                    <v-card-text>
                      <v-container>
                        <v-form ref="form" v-model="valid">
                          <v-row>
                            <v-col cols="12" sm="4" md="4">
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
                                :rules="[(v) => !!v || 'Выберите марку']"
                                required
                                @change="getModels(editedItem.mark_id)"
                              />
                            </v-col>
                            <v-col cols="12" sm="4" md="4">
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
                                dense
                                :rules="[(v) => !!v || 'Выберите модель']"
                                required
                              />
                            </v-col>
                            <v-col cols="12" sm="4" md="4">
                              <v-select
                                v-model="editedItem.showroom_id"
                                :items="showrooms"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                                hide-details
                                dense
                                label="Шоурум"
                                :rules="[(v) => !!v || 'Выберите шоурум']"
                                required
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-text-field
                                v-model="editedItem.link"
                                label="Ссылка на сайт"
                                hide-details
                                dense
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-text-field
                                v-model="editedItem.complectation"
                                label="Комплектация"
                                hide-details
                                dense
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-select
                                v-model="editedItem.year"
                                :items="$range(2022, 2018)"
                                menu-props="auto"
                                label="Год"
                                :rules="[(v) => !!v || 'Выберите год выпуска']"
                                hide-details
                                dense
                                required
                              />
                            </v-col>

                            <v-col cols="12" sm="6" md="3">
                              <v-select
                                v-model="editedItem.wheeltype_id"
                                :items="wheeltypes"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                                hide-details
                                dense
                                label="Привод"
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-select
                                v-model="editedItem.transmission_id"
                                :items="transmissions"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                                label="КПП"
                                dense
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-select
                                v-model="editedItem.volume"
                                :items="$range(0.8, 5, 0.1)"
                                menu-props="auto"
                                item-value=""
                                label="Объем"
                                dense
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-text-field
                                v-model="editedItem.power"
                                type="number"
                                label="Л.С."
                                hide-details
                                dense
                              />
                            </v-col>
                            <v-col cols="12" sm="4" md="3">
                              <v-select
                                v-model="editedItem.enginetype_id"
                                :items="enginetypes"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                                label="Двигатель"
                                hide-details
                                dense
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-text-field
                                v-model="editedItem.salon"
                                label="Тип салона"
                                dense
                              />
                            </v-col>
                            <v-col cols="12" sm="4" md="3">
                              <v-text-field
                                v-model="editedItem.color"
                                label="Цвет"
                                hide-details
                                dense

                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-text-field
                                v-model="editedItem.vin_number"
                                label="Вин"
                                hide-details
                                dense
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-text-field
                                v-model="editedItem.body_number"
                                label="№ кузова"
                                hide-details
                                dense
                              />
                            </v-col>

                            <v-col cols="12" sm="6" md="3">
                              <v-text-field
                                v-model="editedItem.price"
                                type="number"
                                label="Цена"
                                hide-details
                                dense
                              />
                            </v-col>

                            <v-col cols="12" sm="6" md="3">
                              <v-text-field
                                v-model="editedItem.avito_price"
                                type="number"
                                label="Цена авито"
                                hide-details
                                dense
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-select
                                v-model="editedItem.ptc_type"
                                :items="[
                                  { id: 1, name: 'ПТС' },
                                  { id: 2, name: 'ЭПТС' },
                                  { id: 3, name: 'КОПИЯ' },
                                  { id: 0, name: 'Нет' },
                                ]"
                                label="ПТС"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                                hide-details
                                dense
                              />
                            </v-col>

                            <v-col cols="12" sm="6" md="3">
                              <v-text-field
                                v-model="editedItem.supplier"
                                label="Поставщик"
                                hide-details
                                dense
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-text-field
                                v-model="editedItem.key_number"
                                label="№ ключа"
                                hide-details
                                dense
                              />
                            </v-col>
                            <v-col cols="12" md="3" sm="6">
                              <v-select
                                v-model="editedItem.status_id"
                                :items="[
                                  { id: null, name: '' },
                                  { id: 1, name: 'Продана' },
                                  { id: 2, name: 'В продаже' },
                                ]"
                                hide-details
                                dense
                                item-text="name"
                                item-value="id"
                                label="Статус"
                                menu-props="auto"
                              />
                            </v-col>
                            <v-col cols="12" sm="12" md="6">
                              <v-text-field
                                v-model="editedItem.comment"
                                label="Коментарии"
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
                      <v-tooltip
                        v-if="editedItem.deleted_at === null"
                        bottom
                      >
                        <template #activator="{ on, attrs }">
                          <v-btn
                            v-if="
                              editedItem.transit === 1 &&
                                editedItem.showroom_id === showroom_id
                            "
                            v-bind="attrs"
                            x-small
                            color="error"
                            v-on="on"
                            @click="cancelTransit(editedItem)"
                          >
                            <span key="cancel">Отменить</span>
                          </v-btn>
                          <v-btn
                            v-else-if="
                              editedItem.transit === 1 ||
                                editedItem.showroom_id !== showroom_id
                            "
                            v-bind="attrs"
                            x-small
                            color="warning"
                            v-on="on"
                            @click="approveTransit(editedItem)"
                          >
                            <span key="approve">Принять</span>
                          </v-btn>
                          <v-btn
                            v-else
                            v-bind="attrs"
                            x-small
                            color="success"
                            dark
                            v-on="on"
                            @click="transitItem(editedItem)"
                          >
                            <span key="transit">Транзит</span>
                          </v-btn>
                        </template>
                        <span v-text="transitStatus(editedItem)" />
                      </v-tooltip>

                      <v-btn
                        :disabled="editedItem.id ===''"
                        color="error darken-1"
                        text
                        @click="deleteItem(editedItem.id)"
                      >
                        {{
                          editedItem.deleted_at === null
                            ? "Удалить"
                            : "Удалить из корзины"
                        }}
                      </v-btn>
                      <v-switch
                        v-if="editedItem.id !== ''"
                        v-model="editedItem.is_sold"
                        class="mr-2"
                        label="Продана"
                        color="success"
                      />

                      <v-btn color="blue darken-1" text @click="close">
                        Отмена
                      </v-btn>

                      <v-btn
                        v-if="editedItem.id === ''"
                        color="blue darken-1"
                        text
                        @click="save()"
                      >
                        Сохранить
                      </v-btn>
                      <v-btn
                        v-else
                        color="blue darken-1"
                        text
                        @click="save()"
                      >
                        Сохранить
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-dialog>
                <v-dialog v-model="dialogTransit" max-width="500px">
                  <v-card>
                    <v-card-title>
                      <span class="headline">Транзит</span>
                    </v-card-title>
                    <v-card-text>
                      <v-container>
                        <v-form ref="form">
                          <v-row>
                            <v-col cols="12">
                              <v-select
                                v-model="transit_to"
                                :items="receivers"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                                label="Выберите салон транзита"
                              />
                            </v-col>
                            <v-col cols="12">
                              <v-textarea
                                v-model="transit_comment"
                                rows="2"
                                label="Комментарий"
                                outlined
                              />
                            </v-col>
                          </v-row>
                        </v-form>
                      </v-container>
                    </v-card-text>

                    <v-card-actions>
                      <v-btn
                        color="blue darken-1"
                        text
                        @click="dialogTransit = false"
                      >
                        Отмена
                      </v-btn>
                      <v-btn
                        color="blue darken-1"
                        :disabled="transit_to === null"
                        text
                        @click="transit()"
                      >
                        Сохранить
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-dialog>
                <v-menu
                  v-model="menu"
                  :close-on-content-click="false"
                  :nudge-width="200"
                  offset-x
                >
                  <template #activator="{ on, attrs }">
                    <v-btn
                      color="indigo"
                      dark
                      v-bind="attrs"
                      class="mb-2 ml-1"
                      v-on="on"
                    >
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
              </v-toolbar>
            </template>
          </v-data-table>
          <v-snackbar
            v-model="snackbar"
            :color="color"
            :right="'right'"
            :timeout="timeout"
            :top="'top'"
            outlined
          >
            {{ text }}
            <template #action="">
              <v-btn icon color="deep-orange" @click="snackbar = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </template>
          </v-snackbar>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>
<script>
import BreadCrumb from '~/components/BreadCrumb'

export default {
  name: 'CarId',
  auth: false,
  components: { BreadCrumb },
  beforeRouteLeave (to, from, next) {
    this.$store.dispatch('user/toggle', true)
    next()
  },
  layout: 'default',
  middleware: 'permission',
  data: () => ({
    filter: {
      mark_id: null,
      model_id: null,
      transmission_id: null,
      wheeltype_id: null,
      year_from: null,
      year_to: null
    },
    menu: null,
    deleted: false,
    dialog: false,
    dialogTransit: false,
    transit_comment: null,
    transit_to: null,
    valid: false,
    validTransit: false,
    snackbar: false,
    text: '',
    color: '',
    timeout: 6000,
    expanded: [],
    headers: [
      {
        text: 'Салон',
        align: 'center',
        value: 'showroom.name',
        width: '90px'
      },
      {
        text: 'Ссылка',
        align: 'center',
        value: 'link',
        width: '20px'
      },
      {
        text: 'Цена авито',
        align: 'center',
        value: 'avito_price',
        width: '30px'
      },
      {
        text: 'Марка',
        align: 'center',
        value: 'mark.name',
        width: '150px'
      },
      { text: 'Модель', value: 'model.name', align: 'center', width: '150px' },
      {
        text: 'Комплектация',
        value: 'complectation',
        sortable: true,
        width: '150px',
        align: 'center'
      },
      {
        text: 'Выпуск',
        value: 'year',
        sortable: false,
        align: 'center',
        width: '25px'
      },
      {
        text: 'Объем',
        value: 'volume',
        sortable: false,
        align: 'center',
        width: '25px'
      },
      {
        text: 'Л.С.',
        value: 'power',
        sortable: false,
        align: 'center',
        width: '25px'
      },
      {
        text: 'Привод',
        value: 'wheeltype.name',
        width: '100px',
        align: 'center'
      },
      {
        text: 'КПП',
        value: 'transmission.name',
        sortable: false,
        align: 'center',
        width: '30px'
      },
      {
        text: 'Цвет',
        value: 'color',
        sortable: false,
        align: 'center',
        width: '30px'
      },
      {
        text: 'Vin',
        value: 'vin_number',
        sortable: false,
        align: 'center',
        width: '50px'
      },
      {
        text: '№ кузова',
        value: 'body_number',
        sortable: false,
        align: 'center',
        width: '50px'
      },
      {
        text: 'Цена',
        value: 'price',
        sortable: false,
        align: 'center',
        width: '20px'
      },
      {
        text: 'ПТС',
        value: 'ptc_type',
        sortable: true,
        width: '80px',
        align: 'center'
      },
      {
        text: 'Поставщик',
        value: 'supplier',
        sortable: true,
        width: '80px',
        align: 'center'
      },
      {
        text: '№ ключа',
        value: 'key_number',
        sortable: false,
        align: 'center',
        width: '100px'
      },
      {
        text: 'Статус',
        value: 'is_sold',
        sortable: false,
        align: 'center',
        width: '100px'
      }
    ],
    editedIndex: -1,
    editedItem: {
      id: '',
      showroom_id: null,
      income_date: '',
      link: '',
      mark_id: '',
      model_id: '',
      year: '',
      transmission_id: '',
      complectaion: '',
      enginetype_id: '',
      salon: '',
      wheeltype_id: '',
      power: '',
      color: '',
      volume: '3',
      vin_number: '',
      body_number: '',
      ptc_type: null,
      income_price: '',
      price: 0,
      supplier: '',
      key_number: '',
      is_sold: '',
      transit: '',
      status_id: '',
      comment: null
    },
    defaultItem: {
      id: '',
      showroom_id: null,
      income_date: '',
      link: '',
      mark_id: '',
      model_id: '',
      year: '',
      transmission_id: '',
      complectaion: '',
      enginetype_id: '',
      salon: '',
      wheeltype_id: '',
      power: '',
      color: '',
      volume: '3',
      vin_number: '',
      body_number: '',
      ptc_type: null,
      income_price: '',
      price: 0,
      supplier: '',
      key_number: '',
      is_sold: '',
      comment: null,
      transit: '',
      status_id: ''
    },
    options: {
      sortBy: ['mark.name', 'model.name'],
      multiSort: true
    }
  }),

  async fetch ({ store, error, params: { id } }) {
    await store.dispatch('car/fetchCars', {is_used: false, showroom_id: id})
    await store.dispatch('property/fetchMarks')
    await store.dispatch('property/fetchEngineTypes')
    await store.dispatch('property/fetchTransmissions')
    await store.dispatch('property/fetchWheelTypes')
    await store.dispatch('showroom/fetchShowroom', { id })
    await store.dispatch('showroom/fetchShowrooms')
    await store.dispatch('user/toggle', false)
  },

  computed: {
    showroom_id () {
      return Number(this.$route.params.id) || null
    },
    showroom () {
      return this.$store.state.showroom.showroom
    },
    showrooms () {
      return this.$store.state.showroom.showrooms
    },
    formTitle () {
      return this.editedIndex === -1 ? 'Добавление' : 'Изменить'
    },
    filtered () {
      let cars = this.$store.state.car.cars
      if (this.deleted) {
        cars = cars.filter(item => item.deleted_at !== null)
      } else {
        cars = cars.filter(item => item.deleted_at === null)
      }
      if (this.$route.params.id) {
        cars = cars.filter(
          item =>
            item.showroom_id === Number(this.$route.params.id) ||
            (item.transit === 1 &&
              item.transits.filter(
                i =>
                  i.status === 0 &&
                  (i.showroom_id === this.showroom_id ||
                    i.from === this.showroom_id)
              ).length > 0)
        )
      }
      if (this.filter.mark_id) {
        cars = cars.filter(item => item.mark_id === this.filter.mark_id)
      }
      if (this.filter.model_id) {
        cars = cars.filter(item => item.model_id === this.filter.model_id)
      }
      if (this.filter.transmission_id) {
        cars = cars.filter(
          item => item.transmission_id === this.filter.transmission_id
        )
      }
      if (this.filter.wheeltype_id) {
        cars = cars.filter(
          item => item.wheeltype_id === this.filter.wheeltype_id
        )
      }
      if (this.filter.year_from && this.filter.year_to) {
        cars = cars.filter(
          item =>
            item.year >= this.filter.year_from &&
            item.year <= this.filter.year_to
        )
      } else if (this.filter.year_from) {
        cars = cars.filter(item => item.year >= this.filter.year_from)
      } else if (this.filter.year_to) {
        cars = cars.filter(item => item.year <= this.filter.year_to)
      }
      return cars
    },
    cars () {
      return this.$store.state.car.cars
    },

    marks () {
      return this.$store.state.property.marks
    },
    models () {
      return this.$store.state.property.models
    },
    enginetypes () {
      return this.$store.state.property.enginetypes
    },
    transmissions () {
      return this.$store.state.property.transmissions
    },
    wheeltypes () {
      return this.$store.state.property.wheeltypes
    },
    user () {
      return this.$auth.user
    },
    links () {
      return [
        {
          text: 'Главная',
          disabled: false,
          href: '/'
        },
        {
          text: 'Новые втомобили',
          disabled: false,
          href: '/cars/all'
        },
        {
          text: this.showroom.name || '',
          disabled: true,
          href: '/cars/' + (this.showroom.id || '')
        }
      ]
    },
    receivers () {
      return this.showrooms.filter(item => item.id !== this.showroom_id)
    }
  },
  watch: {
    deleted (val) {
      val === false || this.clearFilter()
    },
    dialog (val) {
      val || this.close()
    }
  },
  mounted () {
    this.$echo.channel('cars').listen('CarAdded', (e) => {
      console.log('car added')
      this.$store.dispatch('car/fetchCars', {}).catch((error) => {
        console.log(error)
        this.reload()
      })
    })
  },
  methods: {
    save () {
      if (this.valid) {
        if (this.editedIndex > -1) {
          this.$store
            .dispatch('car/save', {
              item: this.editedItem
            })
            .then((res) => {
              this.$toast.success("Данные изменены" );
            })
            .catch((error) => {
              this.$toast.error("Ошибка" + error);
            })
        } else {
          this.$store
            .dispatch('car/save', {
              item: this.editedItem
            })
            .then((res) => {
              this.$toast.success("Данные добавлены" );
            })
            .catch((error) => {
              this.$toast.error('Ошибка: ' + error);
            })
        }
        this.close()
      } else {
        this.$toast.error('Заполните обязательные поля');
      }
    },

    editItem (item) {
      this.editedItem = Object.assign({}, item)
      this.dialog = true
      if (item.mark_id !== 0) {
        this.$store.dispatch('property/fetchModels', { markId: item.mark_id })
      }
    },

    deleteItem (id) {
      confirm('Вы уверены, что хотите удалить этот запись?') &&
        this.$store
          .dispatch('car/delete', {
            id
          })
          .then(() => {
            this.$toast.success("Данные удалены");
          })
          .catch((error) => {
            this.$toast.error("Ошибка в сервере: " + error);
            this.$toast.error('Ошибка: ' + error);
          })
      this.deleteId = ''
      this.dialog = false
    },

    transitItem (item) {
      item.sender_id = this.user && this.user.id
      this.dialogTransit = true
    },

    transit () {
      const item = this.editedItem
      item.sender_id = this.user && this.user.id
      item.showroom_id = this.transit_to || null
      item.transit_comment = this.transit_comment
      item.from = this.showroom_id
      this.$store
        .dispatch('car/transit', { item })
        .then(() => {
          this.$toast.success("Автомобиль переведён");
        })
        .catch((error) => {
          this.$toast.error("Ошибка в сервере: " + error);
        })
      this.transit_to = null
      this.dialogTransit = false
      this.transit_comment = null
      this.close()
    },
    cancelTransit (item) {
      confirm('Вы уверены, что хотите отменть транзит?') &&
        this.$store
          .dispatch('car/cancelTransit', { item })
          .then(() => {
            this.$toast.success("Транзит отменён");
          })
          .catch((error) => {
            this.$toast.error("Ошибка в сервере: " + error);
          })
      this.close()
    },
    approveTransit (item) {
      item.receiver_id = this.user && this.user.id
      confirm('Вы уверены, что хотите принять транзит?') &&
        this.$store
          .dispatch('car/approveTransit', { item })
          .then(() => {
            this.$toast.success("Транзит принять");
          })
          .catch((error) => {
            this.$toast.error("Ошибка в сервере: " + error);
          })
      this.close()
    },

    getColor (transit) {
      if (transit === 1) {
        return 'primary' // can also return multiple classes e.g ["orange","disabled"]
      } else if (transit === 2) {
        return 'error'
      } else {
        return 'success lighten-1'
      }
    },
    transitStatus (item) {
      if (item.transit === 1 && item.showroom_id === this.showroom_id) {
        return 'На выезде (Отменить)' // can also return multiple classes e.g ["orange","disabled"]
      } else if (item.transit === 1) {
        return 'Принять транзит'
      } else {
        return 'Передать в другой салон'
      }
    },
    clearFilter () {
      this.filter.mark_id = null
      this.filter.model_id = null
      this.filter.transmission_id = null
      this.filter.wheeltype_id = null
      this.filter.milage_from = null
      this.filter.milage_to = null
      this.filter.year_from = null
      this.filter.year_to = null
      this.$store.dispatch('car/emptyModels')
    },
    row_classes (item) {
      if (item.transit === 1) {
        return 'success white--text' // can also return multiple classes e.g ["orange","disabled"]
      } else if (item.deleted_at !== null) {
        return 'black lighten-4 white--text'
      }
      if (item.is_sold === 1 || item.status_id === 1) {
        return 'error white--text' // can also return multiple classes e.g ["orange","disabled"]
      }
    },

    close () {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },

    validate () {
      this.$refs.form.validate()
    },

    getModels (markId = null) {
      this.editedItem.model_id = []
      if (markId !== 0) {
        this.$store.dispatch('property/fetchModels', { markId })
      }
    },
    isNumber (evt) {
      evt = evt || window.event
      const charCode = evt.which ? evt.which : evt.keyCode
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        evt.preventDefault()
      } else {
        return true
      }
    },
    reload () {
      this.intervalid1 = setInterval(() => {
        console.log('reload')
        this.$store.dispatch('car/fetchCars')
      }, 15000)
    }
  }
}
</script>
<style scoped>
.v-data-table > .v-data-table__wrapper > table > tbody > tr > td,
.v-data-table > .v-data-table__wrapper > table > thead > tr > td,
.v-data-table > .v-data-table__wrapper > table > tfoot > tr > td {
  height: 20px;
}
</style>
<style scoped>
table {
  border: none;
  border-collapse: collapse;
}

table td {
  border: 1px solid black;
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

.v-data-table > .v-data-table__wrapper > table > tfoot > tr > th {
  padding: 0 4px;
}

.v-data-table > .v-data-table__wrapper > table > tbody > tr > td {
  font-size: 0.875rem;
  height: 0px;
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

.v-data-table
/deep/
tbody
/deep/
tr:hover:not(.v-data-table__expanded__content) {
  background: yellow !important;
}
</style>

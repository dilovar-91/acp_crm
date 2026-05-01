<template>
  <div>
    <BreadCrumb :items="links" />

    <v-container fluid>
      <v-row no-gutters align="start" class="d-flex">
        <v-col cols="12">
          <v-card class="mx-auto pt-0">
            <v-app-bar class="grey lighten-5" elevate-on-scroll dense>
              <v-toolbar-title>Приезд</v-toolbar-title>

              <v-row class="ml-5 mt-3">
                <v-col cols="6" sm="3" md="2" class="hidden-sm-and-down">
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
                <v-col cols="6" sm="3" md="2" class="hidden-sm-and-down mr-3">
                  <date-picker
                    v-model="filter_to"
                    format="DD.MM.Y"
                    placeholder="Дата до"
                    class="mt-3 mx-3"
                    clear="filter_to=''"
                  />
                </v-col>
                <v-col
                  cols="6"
                  sm="3"
                  md="2"
                  class="mt-2 ml-5 hidden-sm-and-down"
                >
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
              <v-btn icon color="green" @click="exportFile()">
                <v-icon>mdi-file-excel</v-icon>
              </v-btn>
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
                :to="'/tablet/arrivals/' + showroom_id"
              >
                <v-icon>mdi-keyboard-return</v-icon>
                Приезд
              </v-btn>
              <v-menu
                v-model="menu"
                :close-on-content-click="false"
                :nudge-width="200"
                offset-x
              >
                <template #activator="{ on, attrs }">
                  <v-btn color="indigo" dark v-bind="attrs" v-on="on">
                    <v-icon class="mr-2"> mdi-menu </v-icon>
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
                            role &&
                            (role.name === 'admin' ||
                              role.name === 'mark' ||
                              role.name === 'vova' ||
                              role.name === 'director')
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
                :items="credit_requests"
                :items-per-page="credit_requests.length"
                fixed-header
                height="80vh"
                :hide-default-footer="true"
                item-key="id"
              >
                <template
                  v-for="(h, index) in headers"
                  #[`header.${h.value}`]="{ header }"
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

                <template #body="{ items }">
                  <tbody>
                    <tr
                      v-for="(item, i) in items"
                      :key="item.id"
                      :class="row_classes(item)"
                      @dblclick="editItem(item)"
                    >
                      <td>{{ i + 1 }}</td>
                      <td>
                        {{ $moment(item.date).format('DD.MM') }} <br />
                        {{ $moment(item.created_at).format('HH:mm') }}
                      </td>
                      <td>
                        {{ item.manager && item.manager.name }} <br />
                        {{ item.manager2 && item.manager2.name }}
                      </td>
                      <td>
                        {{ item.client_name }}
                      </td>
                      <td>
                        {{ item.region && item.region.name }}
                      </td>
                      <td>
                        {{ item.region_live && item.region_live.name }}
                      </td>
                      <td>
                        {{ item.mark && item.mark.name }}
                        {{ item.car_model }}
                      </td>
                      <td>{{ item.price | currency }}</td>
                      <td>
                        {{ item.initial_fee | currency }}
                      </td>
                      <td :class="setBackground(item.absolute)">
                        <div class="vertical2">
                          <p>
                            <StatusComponent :status-id="item.absolute" />
                          </p>
                          <p>{{ getManager(item.absolute_manager) }}</p>
                        </div>
                      </td>
                      <td :class="setBackground(item.expo)">
                        <div class="vertical2">
                          <p>
                            <StatusComponent :status-id="item.expo" />
                          </p>
                          <p>{{ getManager(item.expo_manager) }}</p>
                        </div>
                      </td>
                      <td :class="setBackground(item.sovkom)">
                        <div class="vertical2">
                          <p>
                            <StatusComponent :status-id="item.sovkom" />
                          </p>
                          <p>{{ getManager(item.sovkom_manager) }}</p>
                        </div>
                      </td>
                      <td :class="setBackground(item.oranzh)">
                        <div class="vertical2">
                          <p>
                            <StatusComponent :status-id="item.oranzh" />
                          </p>
                          <p>{{ getManager(item.oranzh_manager) }}</p>
                        </div>
                      </td>
                      <td :class="setBackground(item.tinkoff)">
                        <div class="vertical2">
                          <p>
                            <StatusComponent :status-id="item.tinkoff" />
                          </p>
                          <p>{{ getManager(item.tinkoff_manager) }}</p>
                        </div>
                      </td>
                      <td :class="setBackground(item.kvant)">
                        <div class="vertical2">
                          <p>
                            <StatusComponent :status-id="item.kvant" />
                          </p>
                          <p>{{ getManager(item.kvant_manager) }}</p>
                        </div>
                      </td>
                      <td :class="setBackground(item.zenit)">
                        <div class="vertical2">
                          <p>
                            <StatusComponent :status-id="item.zenit" />
                          </p>
                          <p>{{ getManager(item.zenit_manager) }}</p>
                        </div>
                      </td>
                      <td :class="setBackground(item.loco_bank)">
                        <div class="vertical2">
                          <p>
                            <StatusComponent :status-id="item.loco_bank" />
                          </p>
                          <p>{{ getManager(item.loco_manager) }}</p>
                        </div>
                      </td>
                      <td :class="setBackground(item.cetelem)">
                        <div class="vertical2">
                          <p>
                            <StatusComponent :status-id="item.cetelem" />
                          </p>
                          <p>{{ getManager(item.cetelem_manager) }}</p>
                        </div>
                      </td>
                      <td :class="setBackground(item.vtb)">
                        <div class="vertical2">
                          <p>
                            <StatusComponent :status-id="item.vtb" />
                          </p>
                          <p>{{ getManager(item.vtb_manager) }}</p>
                        </div>
                      </td>

                      <td :class="setBackground(item.bistro_bank)">
                        <div class="vertical2">
                          <p>
                            <StatusComponent :status-id="item.bistro_bank" />
                          </p>
                          <p>{{ getManager(item.bistro_bank_manager) }}</p>
                        </div>
                      </td>
                      <td :class="setBackground(item.rnb)">
                        <div class="vertical2">
                          <p>
                            <StatusComponent :status-id="item.rnb" />
                          </p>
                          <p>{{ getManager(item.rnb_manager) }}</p>
                        </div>
                      </td>
                      <td :class="setBackground(item.uralsib)">
                        <div class="vertical2">
                          <p>
                            <StatusComponent :status-id="item.uralsib" />
                          </p>
                          <p>{{ getManager(item.uralsib_manager) }}</p>
                        </div>
                      </td>
                      <td :class="setBackground(item.primsots)">
                        <div class="vertical2">
                          <p>
                            <StatusComponent :status-id="item.primsots" />
                          </p>
                          <p>{{ getManager(item.vtb_manager) }}</p>
                        </div>
                      </td>
                      <td :class="setBackground(item.otp_bank)">
                        <div class="vertical2">
                          <p>
                            <StatusComponent :status-id="item.otp_bank" />
                          </p>
                          <p>{{ getManager(item.otp_bank_manager) }}</p>
                        </div>
                      </td>

                      <td :class="setBackground(item.otkritie)">
                        <div class="vertical2">
                          <p>
                            <StatusComponent :status-id="item.otkritie" />
                          </p>
                          <p>{{ getManager(item.otkritie_manager) }}</p>
                        </div>
                      </td>
                      <td :class="setBackground(item.alfa_bank)">
                        <div class="vertical2">
                          <p>
                            <StatusComponent :status-id="item.alfa_bank" />
                          </p>
                          <p>{{ getManager(item.alfa_bank_manager) }}</p>
                        </div>
                      </td>

                      <td :class="setBackground(item.keb)">
                        <div class="vertical2">
                          <p>
                            <StatusComponent :status-id="item.keb" />
                          </p>
                          <p>{{ getManager(item.keb_manager) }}</p>
                        </div>
                      </td>
                      <td>
                        <v-tooltip bottom max-width="400px" color="primary">
                          <template #activator="{ on, attrs }">
                            <div color="primary" dark v-bind="attrs" v-on="on">
                              {{ item.comments | truncate(100) }}
                            </div>
                          </template>
                          <span>{{ item.comments }}</span>
                        </v-tooltip>
                      </td>
                      <td>
                        <v-tooltip bottom max-width="400px" color="primary">
                          <template #activator="{ on, attrs }">
                            <div color="primary" dark v-bind="attrs" v-on="on">
                              {{ item.sd_comment | truncate(50) }}
                            </div>
                          </template>
                          <span>{{ item.sd_comment }}</span>
                        </v-tooltip>
                      </td>
                    </tr>
                    <span class="d-none scroll" />
                  </tbody>
                </template>
              </v-data-table>
            </v-card-text>
          </v-card>
        </v-col>
        <v-dialog v-model="dialog" max-width="1100">
          <v-card>
            <v-card-text>
              <v-container>
                <v-form ref="form" v-model="valid">
                  <v-row>
                    <v-col cols="12" sm="12" md="5" class="mt-1">
                      <v-menu
                        v-model="menu2"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        max-width="290px"
                        min-width="290px"
                      >
                        <template #activator="{ on, attrs }">
                          <v-text-field
                            v-model="computedDateFormatted"
                            label="Дата"
                            outlined
                            v-bind="attrs"
                            dense
                            type="fa"
                            readonly
                            hide-details
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
                    <v-col cols="12" sm="12" md="7">
                      <v-text-field
                        v-model="editedItem.client_name"
                        label="ФИО клиента"
                        :rules="nameRules"
                        outlined
                        hide-details
                        dense
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="6" class="py-1">
                      <v-autocomplete
                        v-model="editedItem.region_id"
                        :items="regions"
                        :value="regions[editedItem.region_id]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Регион (прописка)"
                        hide-details
                        :rules="[(v) => !!v || 'Выберите регион']"
                        required
                        outlined
                        dense
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="6" class="py-1">
                      <v-autocomplete
                        v-model="editedItem.live_region"
                        :items="regions"
                        :value="regions[editedItem.live_region]"
                        item-text="name"
                        item-value="id"
                        no-data-text="Нету данных"
                        menu-props="auto"
                        label="Регион (проживание)"
                        hide-details
                        outlined
                        dense
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="4" class="py-1">
                      <v-text-field
                        v-model="editedItem.price"
                        label="Стоимость"
                        outlined
                        dense
                        hide-details
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="4" class="py-1">
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
                        outlined
                        dense
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="4" class="py-1">
                      <v-text-field
                        v-model="editedItem.car_model"
                        label="Модель"
                        outlined
                        hide-details
                        dense
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="3" class="py-1">
                      <v-autocomplete
                        v-model="editedItem.manager_id"
                        :items="managers"
                        :value="managers[editedItem.manager_id]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Менеджер"
                        hide-details
                        outlined
                        dense
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="3" class="py-1">
                      <v-autocomplete
                        v-model="editedItem.manager2_id"
                        :items="managers2"
                        :value="managers2[editedItem.manager2_id]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Менеджер 2 (КО)"
                        hide-details
                        outlined
                        clearable
                        dense
                        @click:clear="
                          $nextTick(() => (editedItem.manager2_id = null))
                        "
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="3" class="py-1">
                      <v-text-field
                        v-model="editedItem.initial_fee"
                        label="ПВ"
                        outlined
                        dense
                        hide-details
                      />
                    </v-col>

                    <v-col cols="12" sm="6" md="3" class="py-1">
                      <v-text-field
                        v-model="editedItem.phone_number"
                        label="Телефон"
                        outlined
                        dense
                        hide-details
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="3" class="py-1">
                      <v-select
                        v-model="editedItem.absolute"
                        :items="statuses"
                        item-text="name"
                        item-value="id"
                        menu-props="auto"
                        label="Абсолют"
                        hide-details
                      />
                      <v-autocomplete
                        v-model="editedItem.absolute_manager"
                        :items="managers2"
                        class="mt-2"
                        :value="managers2[editedItem.absolute_manager]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Менеджер Абсолют"
                        hide-details
                        outlined
                        clearable
                        dense
                        @click:clear="
                          $nextTick(() => (editedItem.absolute_manager = null))
                        "
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="3" class="py-1">
                      <v-select
                        v-model="editedItem.expo"
                        :items="statuses"
                        item-text="name"
                        item-value="id"
                        menu-props="auto"
                        label="Экспо"
                        hide-details
                      />
                      <v-autocomplete
                        v-model="editedItem.expo_manager"
                        :items="managers2"
                        :value="managers2[editedItem.expo_manager]"
                        class="mt-2"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Менеджер Экспо"
                        hide-details
                        outlined
                        clearable
                        dense
                        @click:clear="
                          $nextTick(() => (editedItem.expo_manager = null))
                        "
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="3" class="py-1">
                      <v-select
                        v-model="editedItem.sovkom"
                        :items="statuses"
                        item-text="name"
                        item-value="id"
                        menu-props="auto"
                        label="Совком"
                        hide-details
                      />
                      <v-autocomplete
                        v-model="editedItem.sovkom_manager"
                        :items="managers2"
                        class="mt-2"
                        :value="managers2[editedItem.sovkom_manager]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Менеджер Совком"
                        hide-details
                        outlined
                        clearable
                        dense
                        @click:clear="
                          $nextTick(() => (editedItem.sovkom_manager = null))
                        "
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="3" class="py-1">
                      <v-select
                        v-model="editedItem.oranzh"
                        :items="statuses"
                        item-text="name"
                        item-value="id"
                        menu-props="auto"
                        label="Оранж"
                        hide-details
                      />
                      <v-autocomplete
                        v-model="editedItem.oranzh_manager"
                        :items="managers2"
                        class="mt-2"
                        :value="managers2[editedItem.oranzh_manager]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Менеджер Оранж"
                        hide-details
                        outlined
                        clearable
                        dense
                        @click:clear="
                          $nextTick(() => (editedItem.oranzh_manager = null))
                        "
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="3" class="py-1">
                      <v-select
                        v-model="editedItem.tinkoff"
                        :items="statuses"
                        item-text="name"
                        item-value="id"
                        menu-props="auto"
                        label="Тинькофф"
                        hide-details
                      />
                      <v-autocomplete
                        v-model="editedItem.tinkoff_manager"
                        :items="managers2"
                        class="mt-2"
                        :value="managers2[editedItem.tinkoff_manager]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Менеджер Тинькофф"
                        hide-details
                        outlined
                        clearable
                        dense
                        @click:clear="
                          $nextTick(() => (editedItem.tinkoff_manager = null))
                        "
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="2" class="py-1">
                      <v-select
                        v-model="editedItem.kvant"
                        :items="statuses"
                        item-text="name"
                        item-value="id"
                        menu-props="auto"
                        label="АТБ"
                        hide-details
                      />

                      <v-autocomplete
                        v-model="editedItem.kvant_manager"
                        :items="managers2"
                        class="mt-2"
                        :value="managers2[editedItem.kvant_manager]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Менеджер АТБ"
                        hide-details
                        outlined
                        clearable
                        dense
                        @click:clear="
                          $nextTick(() => (editedItem.kvant_manager = null))
                        "
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="2" class="py-1">
                      <v-select
                        v-model="editedItem.zenit"
                        :items="statuses"
                        item-text="name"
                        item-value="id"
                        menu-props="auto"
                        label="Зенит"
                        hide-details
                      />
                      <v-autocomplete
                        v-model="editedItem.zenit_manager"
                        :items="managers2"
                        class="mt-2"
                        :value="managers2[editedItem.zenit_manager]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Менеджер Зенит"
                        hide-details
                        outlined
                        clearable
                        dense
                        @click:clear="
                          $nextTick(() => (editedItem.zenit_manager = null))
                        "
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="2" class="py-1">
                      <v-select
                        v-model="editedItem.loco_bank"
                        :items="statuses"
                        item-text="name"
                        item-value="id"
                        menu-props="auto"
                        label="Локо"
                        hide-details
                      />
                      <v-autocomplete
                        v-model="editedItem.loco_manager"
                        :items="managers2"
                        class="mt-2"
                        :value="managers2[editedItem.loco_manager]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Менеджер Локо"
                        hide-details
                        outlined
                        clearable
                        dense
                        @click:clear="
                          $nextTick(() => (editedItem.loco_manager = null))
                        "
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="3" class="py-1">
                      <v-select
                        v-model="editedItem.cetelem"
                        :items="statuses"
                        item-text="name"
                        item-value="id"
                        menu-props="auto"
                        label="Сетелем"
                        hide-details
                      />
                      <v-autocomplete
                        v-model="editedItem.cetelem_manager"
                        :items="managers2"
                        class="mt-2"
                        :value="managers2[editedItem.cetelem_manager]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Менеджер Сетелем"
                        hide-details
                        outlined
                        clearable
                        dense
                        @click:clear="
                          $nextTick(() => (editedItem.cetelem_manager = null))
                        "
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="3" class="py-1">
                      <v-select
                        v-model="editedItem.vtb"
                        :items="statuses"
                        item-text="name"
                        item-value="id"
                        menu-props="auto"
                        label="ВТБ"
                        hide-details
                      />
                      <v-autocomplete
                        v-model="editedItem.vtb_manager"
                        class="mt-2"
                        :items="managers2"
                        :value="managers2[editedItem.vtb_manager]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Менеджер ВТБ"
                        hide-details
                        outlined
                        clearable
                        dense
                        @click:clear="
                          $nextTick(() => (editedItem.vtb_manager = null))
                        "
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="2" class="py-1">
                      <v-select
                        v-model="editedItem.keb"
                        :items="statuses"
                        item-text="name"
                        item-value="id"
                        menu-props="auto"
                        label="КЕБ"
                        hide-details
                      />
                      <v-autocomplete
                        v-model="editedItem.keb_manager"
                        :items="managers2"
                        class="mt-2"
                        :value="managers2[editedItem.keb_manager]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Менеджер КЕБ"
                        hide-details
                        outlined
                        clearable
                        dense
                        @click:clear="
                          $nextTick(() => (editedItem.keb_manager = null))
                        "
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="2" class="py-1">
                      <v-select
                        v-model="editedItem.rnb"
                        :items="statuses"
                        item-text="name"
                        item-value="id"
                        menu-props="auto"
                        label="РНБ"
                        hide-details
                      />
                      <v-autocomplete
                        v-model="editedItem.rnb_manager"
                        class="mt-2"
                        :items="managers2"
                        :value="managers2[editedItem.rnb_manager]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Менеджер РНБ"
                        hide-details
                        outlined
                        clearable
                        dense
                        @click:clear="
                          $nextTick(() => (editedItem.rnb_manager = null))
                        "
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="2" class="py-1">
                      <v-select
                        v-model="editedItem.uralsib"
                        :items="statuses"
                        item-text="name"
                        item-value="id"
                        menu-props="auto"
                        label="УралСиб"
                        hide-details
                      />
                      <v-autocomplete
                        v-model="editedItem.uralsib_manager"
                        class="mt-2"
                        :items="managers2"
                        :value="managers2[editedItem.uralsib_manager]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Менеджер УралСиб"
                        hide-details
                        outlined
                        clearable
                        dense
                        @click:clear="
                          $nextTick(() => (editedItem.uralsib_manager = null))
                        "
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="3" class="py-1">
                      <v-select
                        v-model="editedItem.primsots"
                        :items="statuses"
                        item-text="name"
                        item-value="id"
                        menu-props="auto"
                        label="ПримСоц"
                        hide-details
                      />

                      <v-autocomplete
                        v-model="editedItem.primsots_manager"
                        class="mt-2"
                        :items="managers2"
                        :value="managers2[editedItem.primsots_manager]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Менеджер Примсотс"
                        hide-details
                        outlined
                        clearable
                        dense
                        @click:clear="
                          $nextTick(() => (editedItem.primsots_manager = null))
                        "
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="3" class="py-1">
                      <v-select
                        v-model="editedItem.otp_bank"
                        :items="statuses"
                        item-text="name"
                        item-value="id"
                        menu-props="auto"
                        label="ОТП Банк"
                        hide-details
                      />

                      <v-autocomplete
                        v-model="editedItem.otp_bank_manager"
                        class="mt-2"
                        :items="managers2"
                        :value="managers2[editedItem.otp_bank_manager]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Менеджер ОТП"
                        hide-details
                        outlined
                        clearable
                        dense
                        @click:clear="
                          $nextTick(() => (editedItem.otp_bank_manager = null))
                        "
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="3" class="py-1">
                      <v-select
                        v-model="editedItem.otkritie"
                        :items="statuses"
                        item-text="name"
                        item-value="id"
                        menu-props="auto"
                        label="Открытие"
                        hide-details
                      />

                      <v-autocomplete
                        v-model="editedItem.otkritie_manager"
                        class="mt-2"
                        :items="managers2"
                        :value="managers2[editedItem.otkritie_manager]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Менеджер Открытие"
                        hide-details
                        outlined
                        clearable
                        dense
                        @click:clear="
                          $nextTick(() => (editedItem.otkritie_manager = null))
                        "
                      />
                    </v-col>

                    <v-col cols="12" sm="12" md="3" class="py-1">
                      <v-select
                        v-model="editedItem.alfa_bank"
                        :items="statuses"
                        item-text="name"
                        item-value="id"
                        menu-props="auto"
                        label="Альфа банк"
                        hide-details
                      />
                      <v-autocomplete
                        v-model="editedItem.alfa_bank_manager"
                        :items="managers2"
                        class="mt-2"
                        :value="managers2[editedItem.alfa_bank_manager]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Менеджер Альфа банк"
                        hide-details
                        outlined
                        clearable
                        dense
                        @click:clear="
                          $nextTick(() => (editedItem.alfa_bank_manager = null))
                        "
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="3" class="py-1">
                      <v-select
                        v-model="editedItem.bistro_bank"
                        :items="statuses"
                        item-text="name"
                        item-value="id"
                        menu-props="auto"
                        label="БыстроБанк"
                        hide-details
                      />
                      <v-autocomplete
                        v-model="editedItem.bistro_bank_manager"
                        :items="managers2"
                        class="mt-2"
                        :value="managers2[editedItem.bistro_bank_manager]"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Менеджер БыстроБанк"
                        hide-details
                        outlined
                        clearable
                        dense
                        @click:clear="
                          $nextTick(
                            () => (editedItem.bistro_bank_manager = null)
                          )
                        "
                      />
                    </v-col>
                    <v-col cols="12" md="6" class="py-1">
                      <v-textarea
                        v-model="editedItem.comments"
                        outlined
                        rows="2"
                        label="Комментарий КО"
                        hide-details
                      />
                    </v-col>
                    <v-col cols="12" md="6" class="py-1">
                      <v-textarea
                        v-model="editedItem.sd_comment"
                        outlined
                        rows="2"
                        label="Комментарий ОП"
                        hide-details
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="2" class="py-1">
                      <v-checkbox
                        v-model="editedItem.canceled"
                        label="Отказано"
                        outlined
                        dense
                        hide-details
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="2" class="py-1">
                      <v-checkbox
                        v-model="editedItem.client_canceled"
                        label="Отказ клиента"
                        outlined
                        dense
                        hide-details
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="2" class="py-1">
                      <v-checkbox
                        v-model="editedItem.fssp_canceled"
                        label="Отказ ФССП"
                        outlined
                        dense
                        hide-details
                      />
                    </v-col>

                    <v-col cols="12" sm="6" md="2" class="py-1">
                      <v-checkbox
                        v-model="editedItem.jump"
                        label="Соскок с другого салона"
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
                v-if="editedIndex !== -1"
                color="light-green lighten-3"
                class="mr-3"
                text
                @click="confirmSale(saleItem)"
              >
                Продана
              </v-btn>
              <v-btn
                v-if="
                  editedItem.deleted_at !== null &&
                  editedIndex !== -1 &&
                  (role.name === 'admin' || role.name === 'director')
                "
                color="red darken-1"
                class="mr-3"
                text
                @click="confirmDelete(editedItem, true)"
              >
                Удалить из корзины
              </v-btn>
              <v-btn
                v-if="editedIndex !== -1"
                color="red darken-1"
                class="mr-3"
                text
                :disabled="editedItem.deleted_at !== null"
                @click="confirmDelete(saleItem, false)"
              >
                Удалить
              </v-btn>
              <v-btn color="green darken-1" text @click="dialog = false">
                Отменить
              </v-btn>
              <v-btn
                color="green darken-1"
                :loading="loading"
                :disabled="
                  editedItem.deleted_at && editedItem.deleted_at !== null
                "
                text
                @click="save()"
              >
                Сохранить
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="deleteDialog" max-width="600">
          <v-card>
            <v-card-title class="headline"> Вы хотите удалить? </v-card-title>

            <v-card-text>
              После удаления вы не можете восстановить эту строку.
            </v-card-text>
            <v-row class="mx-4">
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
                  v-model="saleItem.car_name"
                  label="Авто"
                  outlined
                  dense
                  hide-details
                  class="mt-2"
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
                  dense
                  hide-details
                  class="mt-2"
                />
              </v-col>
              <v-col cols="12" sm="12" md="6" class="py-1">
                <v-text-field
                  v-model="saleItem.sale_source"
                  label="Источник (Артём)"
                  outlined
                  dense
                  hide-details
                  class="mt-2"
                />
              </v-col>
            </v-row>

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
        <v-dialog v-model="saleDialog" max-width="600">
          <v-card>
            <v-card-title class="headline"> Заполните форму? </v-card-title>

            <v-row class="mx-4">
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
                  v-model="saleItem.car_name"
                  label="Авто"
                  outlined
                  dense
                  hide-details
                  class="mt-2"
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
                  :value="managers2[saleItem.sale_oformitel_id]"
                  item-text="name"
                  no-data-text="Нету данных"
                  item-value="id"
                  menu-props="auto"
                  label="Оформитель"
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
                  dense
                  hide-details
                  class="mt-2"
                />
              </v-col>
              <v-col cols="12" sm="12" md="6" class="py-1">
                <v-text-field
                  v-model="saleItem.sale_source"
                  label="Источник (Артём)"
                  outlined
                  dense
                  hide-details
                  class="mt-2"
                />
              </v-col>
            </v-row>

            <v-card-actions>
              <v-spacer />

              <v-btn color="green darken-1" text @click="saledItem()">
                Да
              </v-btn>
              <v-btn color="green darken-1" text @click="saleDialog = false">
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
import { mapState } from 'vuex'
import { saveAs } from 'file-saver'
import BreadCrumb from '~/components/BreadCrumb'
import StatusComponent from '~/components/StatusComponent'

export default {
  components: { BreadCrumb, StatusComponent },
  layout: 'user',
  middleware: 'permission',
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
    saleDialog: false,
    search: '',
    filter_from: null,
    filter_to: null,
    menu2: false,
    menu3: false,
    menu4: false,
    menu5: false,
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
      (v) => (v && v.length >= 4) || 'ФИО клиента должно быть более 4 символов',
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
        text: 'Дата',
        align: 'start',
        sortable: false,
        value: 'date',
        fixed: true,
        width: '30px',
        vertical: false,
      },
      {
        text: 'Менеджер',
        value: 'manager',
        width: '45px',
        sortable: false,
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
        text: 'Регион (прописка)',
        value: 'region.name',
        sortable: false,
        align: 'center',
        width: '90px',
        vertical: false,
      },
      {
        text: 'Регион (проживание)',
        value: 'region_live.name',
        sortable: false,
        align: 'center',
        width: '90px',
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
        text: 'Стоимость',
        value: 'protein',
        width: '55px',
        sortable: false,
        align: 'center',
        vertical: false,
      },
      {
        text: 'ПВ',
        value: 'initial_fee',
        width: '75px',
        sortable: false,
        align: 'center',
        vertical: false,
      },
      {
        text: 'Абсолют',
        value: 'absolute',
        width: '24px',
        sortable: false,
        vertical: true,
        class: 'px-0 mx-0',
      },
      {
        text: 'Экспо',
        value: 'expo',
        width: '24px',
        sortable: false,
        vertical: true,
        class: 'px-0 mx-0',
      },
      {
        text: 'Совком',
        value: 'sovkom',
        width: '24px',
        height: '100px',
        sortable: false,
        vertical: true,
        class: 'px-0 mx-0',
      },
      {
        text: 'Оранж',
        value: 'oranzh',
        width: '24px',
        sortable: false,
        vertical: true,
        class: 'px-0 mx-0',
      },
      {
        text: 'Тинькофф',
        value: 'tinkoff',
        width: '24px',
        sortable: false,
        vertical: true,
        class: 'px-0 mx-0',
      },
      {
        text: 'АТБ',
        value: 'kvant',
        width: '24px',
        sortable: false,
        vertical: true,
        class: 'px-0 mx-0',
      },
      {
        text: 'Зенит',
        value: 'zenit',
        width: '24px',
        sortable: false,
        vertical: true,
        class: 'px-0 mx-0',
      },
      {
        text: 'Локо Банк',
        value: 'loco_bank',
        width: '24px',
        sortable: false,
        vertical: true,
        class: 'px-0 mx-0',
      },
      {
        text: 'Сетелем',
        value: 'cetelem',
        width: '24px',
        sortable: false,
        vertical: true,
        class: 'px-0 mx-0',
      },
      {
        text: 'ВТБ',
        value: 'vtb',
        width: '24px',
        sortable: false,
        vertical: true,
        class: 'px-0 mx-0',
      },

      {
        text: 'БыстроБанк',
        value: 'bistro_bank',
        width: '24px',
        sortable: false,
        vertical: true,
        class: 'px-0 mx-0',
      },

      {
        text: 'РНБ',
        value: 'rnb',
        width: '24px',
        sortable: false,
        vertical: true,
        class: 'px-0 mx-0',
      },

      {
        text: 'УралСиб',
        value: 'uralsib',
        width: '24px',
        sortable: false,
        vertical: true,
        class: 'px-0 mx-0',
      },
      {
        text: 'ПримСоц',
        value: 'primsots',
        width: '24px',
        sortable: false,
        vertical: true,
        class: 'px-0 mx-0',
      },
      {
        text: 'ОТП Банк',
        value: 'otp_bank',
        width: '24px',
        sortable: false,
        vertical: true,
        class: 'px-0 mx-0',
      },
      {
        text: 'Открытие',
        value: 'otkritie',
        width: '24px',
        sortable: false,
        vertical: true,
        class: 'px-0 mx-0',
      },
      {
        text: 'Альфа банк',
        value: 'alfa_bank',
        width: '24px',
        sortable: false,
        vertical: true,
        class: 'px-0 mx-0',
      },
      {
        text: 'КЕБ',
        value: 'keb',
        width: '24px',
        sortable: false,
        vertical: true,
        class: 'px-0 mx-0',
      },

      {
        text: 'Комментарий КО',
        value: 'comments',
        align: 'center',
        sortable: false,
        vertical: false,
      },
      {
        text: 'Комментарий ОП',
        value: 'sd_comment',
        align: 'center',
        sortable: false,
        vertical: false,
        width: '10%',
      },
    ],
    editedIndex: -1,
    editedItem: {
      id: '',
      date: '',
      client_name: '',
      region_id: '',
      live_region: '',
      mark_id: '',
      model: '',
      showroom_id: '',
      phone_number: '',
      price: '',
      initial_fee: '',
      manager_id: '',
      manager2_id: '',
      comments: '',
      sd_comment: '',
      absolute: '',
      expo: '',
      sovkom: '',
      oranzh: '',
      tinkoff: '',
      kvant: '',
      zenit: '',
      loco_bank: '',
      cetelem: '',
      vtb: '',
      keb: '',
      rnb: '',
      uralsib: '',
      primsots: '',
      otp_bank: '',
      otkritie: '',
      alfa_bank: '',
      bistro_bank: '',
      absolute_manager: '',
      expo_manager: '',
      sovkom_manager: '',
      oranzh_manager: '',
      tinkoff_manager: '',
      kvant_manager: '',
      zenit_manager: '',
      loco_manager: '',
      cetelem_manager: '',
      vtb_manager: '',
      keb_manager: '',
      rnb_manager: '',
      uralsib_manager: '',
      primsots_manager: '',
      otp_bank_manager: '',
      otkritie_manager: '',
      alfa_bank_manager: '',
      bistro_bank_manager: '',
      canceled: '',
      client_canceled: '',
      fssp_canceled: '',
      is_sold: '',
      jump: '',
      transit: '',
      transit_confirmed: '',
    },
    defaultItem: {
      id: '',
      date: '',
      client_name: '',
      region_id: '',
      live_region: '',
      mark_id: '',
      model: '',
      showroom_id: '',
      phone_number: '',
      price: '',
      initial_fee: '',
      manager_id: '',
      manager2_id: '',
      comments: '',
      sd_comment: '',
      absolute: '',
      expo: '',
      sovkom: '',
      oranzh: '',
      tinkoff: '',
      kvant: '',
      zenit: '',
      loco_bank: '',
      cetelem: '',
      vtb: '',
      keb: '',
      rnb: '',
      uralsib: '',
      primsots: '',
      otp_bank: '',
      otkritie: '',
      alfa_bank: '',
      bistro_bank: '',
      absolute_manager: '',
      expo_manager: '',
      sovkom_manager: '',
      oranzh_manager: '',
      tinkoff_manager: '',
      kvant_manager: '',
      zenit_manager: '',
      cetelem_manager: '',
      vtb_manager: '',
      keb_manager: '',
      rnb_manager: '',
      uralsib_manager: '',
      primsots_manager: '',
      otp_bank_manager: '',
      otkritie_manager: '',
      alfa_bank_manager: '',
      bistro_bank_manager: '',
      loco_manager: '',
      canceled: '',
      client_canceled: '',
      fssp_canceled: '',
      is_sold: '',
      jump: '',
      transit: '',
      transit_confirmed: '',
    },
    saleItem: {
      id: '',
      car_name: '',
      sale_date: '',
      sale_bank_id: '',
      sale_vin: '',
      is_sold: '',
      sale_manager_id: '',
      sale_oformitel_id: '',
      sale_source: '',
    },
    saleDefault: {
      id: '',
      car_name: '',
      sale_date: '',
      sale_bank_id: '',
      sale_vin: '',
      is_sold: '',
      sale_manager_id: '',
      sale_oformitel_id: '',
      sale_source: '',
    },
    statuses: [
      { id: 0, name: '' },
      { id: 1, name: 'Не подходит' },
      { id: 2, name: 'Рассмотрение' },
      { id: 3, name: 'Отказан' },
      { id: 4, name: 'Одобрен' },
      { id: 5, name: 'Лимит' },
      { id: 6, name: 'Не работает' },
      { id: 7, name: 'Отложен' },
    ],
  }),
  async fetch({ store, params: { id } }) {
    await store.dispatch('user/toggle', false)
    await store.dispatch('property/fetchMarks')
    await store.dispatch('showroom/fetchManagers', { showroom_id: id })
    await store.dispatch('credit/fetchCreditRequests', { id })
    await store.dispatch('credit/fetchBanks')
    await store.dispatch('showroom/fetchRegions')
    await store.dispatch('showroom/fetchShowroom', { id })
    await store.dispatch('credit/fetchPreparers', { id })
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
    ...mapState({
      credit_requests: (state) => state.credit.credit_requests,
      banks: (state) => state.credit.banks,
    }),

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
    preparers() {
      return this.$store.state.credit.preparers
    },
    canceled() {
      return this.editedItem.canceled
    },
    client_canceled() {
      return this.editedItem.client_canceled
    },
    cfssp_canceled() {
      return this.editedItem.fssp_canceled
    },
    links() {
      return [
        {
          text: 'Главная',
          disabled: false,
          href: '/',
        },
        {
          text: 'Кредитный отдел',
          disabled: false,
          href: '/tablet/credit-requests',
        },
        {
          text: this.showroom?.name,
          disabled: true,
          href: '/tablet/credit-requests' + this.showroom_id,
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
    canceled(val) {
      if (val === true) {
        this.editedItem.client_canceled = false
      }
    },
    client_canceled(val) {
      if (val === true) {
        this.editedItem.canceled = false
      }
    },
    fssp_canceled(val) {
      if (val === true) {
        this.editedItem.client_canceled = false
        this.editedItem.canceled = false
      }
    },
    date(val) {
      this.dateFormatted = this.formatDate(this.date)
    },
    deleted(val) {
      this.filter_from = null
      this.filter_to = null
      this.search = ''
      this.filter()
    },
  },

  mounted() {
    this.$echo.channel('credits').listen('CreditCreated', (e) => {
      console.log('reloaded-mounted')
      this.filter().catch((error) => {
        this.reload()
        console.log(error)
      })
    })
  },
  methods: {
    editItem(item) {
      this.editedIndex = item.id
      this.date = this.$moment(item.date).format('YYYY-MM-DD')
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },
    exportFile() {
      const XLSX = require('xlsx')
      const wb = XLSX.utils.book_new()
      const rows = this.credit_requests.map((row, index) => ({
        '№': index + 1,
        Дата: row.date,
        Менеджер: row.manager?.name,
        ФИО: row.client_name,
        'Регион (прописка)': row.region.name,
        Шоурум: row.showroom?.name,
        'Регион (проживание)': row.region_live?.name,
        Марка: row.mark?.name,
        Модель: row.car_model,
        Телефон: row.phone_number,
        Цена: row.price,
        ПВ: row.initial_fee,
        Абсолют: this.getStatus(row.absolute),
        Экспо: this.getStatus(row.expo),
        Совком: this.getStatus(row.sovkom),
        Оранж: this.getStatus(row.oranzh),
        Тинькофф: this.getStatus(row.tinkoff),
        Квант: this.getStatus(row.kvant),
        Зенит: this.getStatus(row.zenit),
        Сетелем: this.getStatus(row.cetelem),
        ВТБ: this.getStatus(row.vtb),
        КЕБ: this.getStatus(row.keb),
        РНБ: this.getStatus(row.rnb),
        Уралсиб: this.getStatus(row.uralsib),
        Примсотс: this.getStatus(row.primsots),
        Быстробанк: this.getStatus(row.bistro_bank),
        Альфа: this.getStatus(row.alfa_bank),
        'ОТП Банк': this.getStatus(row.otp_bank),
        Открытие: this.getStatus(row.otkritie),
        'Локо Банк': this.getStatus(row.loco_bank),
      }))
      const ws = XLSX.utils.json_to_sheet(rows)
      ws['!cols'] = [
        { wch: 3 },
        { wch: 10 },
        { wch: 10 },
        { wch: 31 },
        { wch: 22 },
        { wch: 7 },
        { wch: 22 },
        { wch: 12 },
        { wch: 22 },
        { wch: 12 },
        { wch: 8 },
        { wch: 6 },
        { wch: 11 },
        { wch: 11 },
        { wch: 11 },
        { wch: 11 },
        { wch: 11 },
        { wch: 11 },
        { wch: 11 },
        { wch: 11 },
        { wch: 11 },
        { wch: 11 },
        { wch: 11 },
        { wch: 11 },
        { wch: 11 },
        { wch: 11 },
        { wch: 11 },
        { wch: 11 },
        { wch: 11 },
        { wch: 11 },
      ]
      XLSX.utils.book_append_sheet(
        wb,
        ws,
        'Кредитный отдел - ' + this.showroom?.name
      )
      const wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' })
      saveAs(
        new Blob([wbout], { type: 'application/octet-stream' }),
        'Кредитный_отдел_' + this.$moment().format('DD-MM-YYYY') + '.xlsx'
      )
    },
    getStatus(value) {
      let status = ''
      switch (value) {
        case 1:
          status = 'Не подходит'
          break
        case 2:
          status = 'Рассмотрение'
          break
        case 3:
          status = 'Отказан'
          break
        case 4:
          status = 'Одобрен'
          break
        case 5:
          status = 'Лимит'
          break
        case 6:
          status = 'Не работает'
          break
        case 7:
          status = 'Отложен'
          break
        default:
          status = ''
      }
      return status
    },
    confirmDelete(item, fromTrash = false) {
      // console.log(item, 'item')
      this.saleItem.id = this.editedItem.id

      this.deleteDialog = true
      this.fromTrash = fromTrash
      this.saleItem.car_name = item.car_name
      this.saleItem.sale_bank_id = item.sale_bank_id
      this.saleItem.sale_manager_id = item.sale_manager_id
      this.saleItem.sale_source = item.sale_source
      this.saleItem.sale_oformitel_id = item.sale_oformitel_id
      this.saleItem.sale_date = this.$moment().format('YYYY-MM-DD')
      this.saleItem.showroom_id = this.showroom_id
      console.log(this.saleItem, 'saleItem')
    },
    confirmSale(item) {
      console.log(item, 'item')
      this.saleItem.id = this.editedItem.id

      if (item.sale_date !== null) {
        this.saleItem.sale_date = this.$moment(item.sale_date).format(
          'YYYY-MM-DD'
        )
      }

      this.saleDialog = true
      // this.saleItem = Object.assign({}, item)
      // this.saleItem.sale_date = this.$moment().format('YYYY-MM-DD')
      this.saleItem.showroom_id = this.showroom_id
      console.log(this.saleItem, 'saleItem')
    },
    deleteItem() {
      if (this.fromTrash) {
        this.$store
          .dispatch('showroom/deleteTrashCreditRequest', {
            id: this.saleItem && this.saleItem.id,
          })
          .then((res) => {
            this.$notify('success', 'Приезд удалён из корзины')
          })
          .catch((error) => {
            this.$notify('error', 'Ошибка: ' + error)
          })
      } else {
        this.$store
          .dispatch('credit/deleteCreditRequest', {
            item: this.saleItem,
          })
          .then((res) => {
            console.log(this.saleItem)
            this.$notify('success', 'Приезд удалён')
          })
          .catch((error) => {
            this.$notify('error', 'Ошибка: ' + error)
          })
      }
      this.deleteId = ''
      this.dialog = false
      this.deleteDialog = false
      this.fromTrash = false
    },
    saledItem() {
      this.$store
        .dispatch('credit/saleCreditRequest', {
          item: this.saleItem,
        })
        .then((res) => {
          console.log(this.saleItem)
          this.$notify('success', 'Запись изменён')
        })
        .catch((error) => {
          this.$notify('error', 'Ошибка: ' + error)
        })

      this.deleteId = ''
      this.dialog = false
      this.saleDialog = false
    },
    close() {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.saleItem = Object.assign({}, this.saleDefault)
        this.editedIndex = -1
        this.date = ''
        this.componentKey += 1
      })
    },

    async save() {
      if (this.valid) {
        this.loading = true
        this.editedItem.showroom_id = this.showroom.id
        this.editedItem.date = this.date
        this.approvedNotify()
        await this.$store
          .dispatch('credit/saveCreditRequest', {
            item: this.editedItem,
          })
          .then((res) => {
            if (this.editedIndex > -1) {
              this.$notify('success', 'Заявка изменён')
            } else {
              this.$notify('success', 'Заявка добавлён')
            }
            this.close()
          })
          .catch((error) => {
            this.$notify('error', 'Ошибка: ' + error)
          })
        this.loading = false
      } else {
        this.$notify('error', 'Заполните обязательные поля')
      }
    },

    async filter() {
      this.loading = true
      await this.$store.dispatch('credit/fetchCreditRequests', {
        id: this.$route.params.id,
        from: this.filter_from
          ? this.$moment(this.filter_from).format('YYYY-MM-DD')
          : this.$moment().startOf('month').format('YYYY-MM-DD'),
        to: this.filter_from
          ? this.$moment(this.filter_to).format('YYYY-MM-DD')
          : this.$moment().endOf('month').format('YYYY-MM-DD'),
        search: this.search,
        trashed: this.deleted,
      })
      this.loading = false
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
    row_classes(item) {
      if (item.deleted_at !== null) {
        return 'black white--text'
      }
      if (item.canceled === 1) {
        return 'red white--text' // can also return multiple classes e.g ["orange","disabled"]
      } else if (item.client_canceled === 1) {
        return 'grey lighten-1'
      } else if (item.fssp_canceled === 1) {
        return 'purple darken-1 white--text'
      }
    },
    setBackground(value) {
      let bgclass = ''
      switch (value) {
        case 1:
          bgclass = 'blue white--text  borderix'
          break
        case 2:
          bgclass = 'white black--text borderix'
          break
        case 3:
          bgclass = 'red white--text borderix'
          break
        case 4:
        case 5:
          bgclass = 'yellow black--text  borderix'
          break
        case 6:
          bgclass = 'purple white--text  borderix'
          break
        case 7:
          bgclass = 'blue darken-4 white--text  borderix'
          break
        default:
          bgclass = ''
      }
      return bgclass
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
      this.search = null
      this.deleted = false
      this.$store.dispatch('credit/fetchCreditRequests', {
        id: this.$route.params.id,
      })
    },
    approvedNotify() {
      const banks = this.editedItem
      if (
        banks.absolute === 4 ||
        banks.absolute === 5 ||
        banks.expo === 4 ||
        banks.expo === 5 ||
        banks.sovkom === 4 ||
        banks.sovkom === 5 ||
        banks.oranzh === 4 ||
        banks.oranzh === 5 ||
        banks.tinkoff === 4 ||
        banks.tinkoff === 5 ||
        banks.kvant === 4 ||
        banks.kvant === 5 ||
        banks.zenit === 4 ||
        banks.zenit === 5 ||
        banks.cetelem === 4 ||
        banks.cetelem === 5 ||
        banks.vtb === 4 ||
        banks.vtb === 5 ||
        banks.keb === 4 ||
        banks.keb === 5 ||
        banks.rnb === 4 ||
        banks.rnb === 5 ||
        banks.uralsib === 4 ||
        banks.uralsib === 5 ||
        banks.primsots === 4 ||
        banks.primsots === 5 ||
        banks.bistro_bank === 4 ||
        banks.bistro_bank === 5 ||
        banks.alfa_bank === 4 ||
        banks.alfa_bank === 5 ||
        banks.otp_bank === 4 ||
        banks.otp_bank === 5 ||
        banks.otkritie === 4 ||
        banks.otkritie === 5 ||
        banks.loco_bank === 4 ||
        banks.loco_bank === 5
      ) {
        this.$store.dispatch('credit/sendApprovedNotify', {
          item: this.editedItem,
        })
      }
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

<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links" />

      <v-row align="start" class="d-flex" no-gutters>
        <v-col cols="12">
          <v-data-table
            :key="componentKey"
            :headers="headers"
            :items="filtered"
            :items-per-page="usedcars.length"
            :options="options"
            class="elevation-1 grey lighten-4"
            height="80vh"
            fixed-header
            :mobile-breakpoint="0"
            hide-default-footer
            item-key="id"
          >
            <template v-slot:header.income_date="{ header }">
              <span class="vertical"
                >Дата <br />
                прихода</span
              >
            </template>
            <template v-slot:header.owner_count="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.volume="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.milage="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.showroom.name="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.bodytype.name="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.year="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.wheeltype.name="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.auto_ru="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.transmission.name="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.ptc="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.price="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.enginetype.name="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.salon="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.color="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.vin_number="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.number="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.sts="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.key_number="{ header }">
              <span class="vertical"
                >Номер <br />
                ключа</span
              >
            </template>

            <template v-slot:header.status.status="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:body="{ items }">
              <tbody>
                <tr
                  v-for="item in items"
                  :key="item.name"
                  :class="row_classes(item)"
                  @dblclick="editItem(item)"
                >
                  <td>
                    <span v-if="item.auto_ru !== null"
                      ><a :href="item.auto_ru" class="link" target="_blank">
                        Перейти
                      </a></span
                    >
                  </td>
                  <td>
                    <v-chip
                      v-if="item.showroom.id % 2 !== 1"
                      color="primary"
                      x-small
                    >
                      {{ item.showroom.name }}
                    </v-chip>
                    <v-chip v-else color="green" text-color="white" x-small>
                      {{ item.showroom.name }}
                    </v-chip>
                  </td>
                  <td>{{ item.mark && item.mark.name }}</td>
                  <td>{{ item.model && item.model.name }}</td>
                  <td>{{ item.year }}</td>
                  <td>{{ item.bodytype && item.bodytype.name }}</td>
                  <td>{{ item.owner_count }}</td>
                  <td>{{ item.transmission && item.transmission.name }}</td>
                  <td>{{ item.volume }} ({{ item.power }} ЛС)</td>
                  <td>{{ item.enginetype && item.enginetype.name }}</td>
                  <td>{{ item.salon }}</td>
                  <td>{{ item.color }}</td>
                  <td>{{ item.vin_number }}</td>
                  <td>{{ item.number }}</td>
                  <td>{{ item.sts }}</td>

                  <td>{{ item.price }}</td>

                  <td>
                    <span v-if="item.ptc === 1">ПТС</span>
                    <span v-else-if="item.ptc === 2" key="copy">Дубликат</span>
                    <span v-else-if="item.ptc === 3" key="epts">ЭПТС</span>
                    <span v-else key="no">НЕТ</span>
                  </td>
                  <td>{{ item.key_number }}</td>
                  <td>
                    <v-chip
                      v-if="item.status && item.status.status !== null"
                      :color="item.status.color"
                      :text-color="item.status.text_color"
                      x-small
                    >
                      {{ item.status.status }}
                    </v-chip>
                  </td>
                </tr>
              </tbody>
            </template>

            <template v-slot:top>
              <v-toolbar dense>
                <v-row class="mt-8 hidden-sm-and-down">
                  <v-col cols="3" md="5" xs="6">
                    <v-row>
                      <v-col cols="3" md="3" xs="6">
                        <v-select
                          v-model="filter.mark_id"
                          :items="marks"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Марка"
                          outlined
                          @change="getModels(filter.mark_id)"
                          @click="filter.model_id = null"
                          @click:clear="
                            $nextTick(() => (filter.mark_id = null))
                          "
                        />
                      </v-col>
                      <v-col cols="3" md="3" xs="6">
                        <v-select
                          v-model="filter.model_id"
                          :items="models"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Модель"
                          outlined
                          @click="deleted = false"
                          @click:clear="
                            $nextTick(() => (filter.model_id = null))
                          "
                        >
                          <template v-slot:no-data>
                            <span class="small">Выберите марку</span>
                          </template>
                        </v-select>
                      </v-col>
                      <v-col cols="3" md="3" xs="6">
                        <v-select
                          v-model="filter.transmission_id"
                          :items="transmissions"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Коробка"
                          outlined
                          @click="deleted = false"
                          @click:clear="
                            $nextTick(() => (filter.transmission_id = null))
                          "
                        />
                      </v-col>
                      <v-col cols="3" md="3" xs="6">
                        <v-select
                          v-model="filter.wheeltype_id"
                          :items="wheeltypes"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Привод"
                          outlined
                          @click="deleted = false"
                          @click:clear="
                            $nextTick(() => (filter.wheeltype_id = null))
                          "
                        />
                      </v-col>
                    </v-row>
                  </v-col>
                  <v-col cols="3" md="7" xs="6">
                    <v-row>
                      <v-col cols="3" md="2" xs="4">
                        <v-select
                          v-model="filter.year_from"
                          :items="range(2023, 1960)"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Год от"
                          outlined
                          @click="deleted = false"
                          @click:clear="
                            $nextTick(() => (filter.year_from = null))
                          "
                        />
                      </v-col>
                      <v-col cols="3" md="2" xs="6">
                        <v-select
                          v-model="filter.year_to"
                          :items="range(2023, 1960)"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Год до"
                          outlined
                          @click="deleted = false"
                          @click:clear="
                            $nextTick(() => (filter.year_to = null))
                          "
                        />
                      </v-col>
                      <v-col cols="3" md="2" xs="6">
                        <v-text-field
                          v-model="filter.milage_from"
                          clearable
                          dense
                          label="Пробег от"
                          outlined
                          @keypress.native="isNumber($event)"
                        />
                      </v-col>
                      <v-col cols="3" md="2" xs="6">
                        <v-text-field
                          v-model="filter.milage_to"
                          clearable
                          dense
                          label="Пробег до"
                          outlined
                          @keypress.native="isNumber($event)"
                        />
                      </v-col>
                      <v-col cols="3" md="2" xs="6">
                        <v-text-field
                          v-model="filter.incomePriceFrom"
                          clearable
                          dense
                          label="Приход от"
                          outlined
                          @keypress.native="isNumber($event)"
                        />
                      </v-col>
                      <v-col cols="3" md="2" xs="6">
                        <v-text-field
                          v-model="filter.incomePriceTo"
                          clearable
                          dense
                          label="Приход до"
                          outlined
                        />
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
                            <v-col cols="12" md="4" sm="4">
                              <v-select
                                v-model="editedItem.mark_id"
                                :items="marks"
                                :rules="[(v) => !!v || 'Выберите марку']"
                                hide-details
                                item-text="name"
                                item-value="id"
                                label="Марка"
                                menu-props="auto"
                                :disabled="true"
                                required
                                @change="getModels(editedItem.mark_id)"
                              />
                            </v-col>
                            <v-col cols="12" md="4" sm="4">
                              <v-select
                                v-model="editedItem.model_id"
                                :items="models"
                                :rules="[(v) => !!v || 'Выберите модель']"
                                hide-details
                                item-text="name"
                                item-value="id"
                                label="Модель"
                                menu-props="auto"
                                :disabled="true"
                                required
                              />
                            </v-col>
                            <v-col cols="12" md="4" sm="4">
                              <v-select
                                v-model="editedItem.showroom_id"
                                :items="[
                                  { id: 1, name: 'Урус' },
                                  { id: 2, name: 'КомфортАвто' },
                                  { id: 3, name: 'Склад' },
                                  { id: 4, name: 'АвтоПремиум' },
                                  { id: 5, name: 'Авангард Юг' },
                                  { id: 7, name: 'Форсаж' },
                                ]"
                                hide-details
                                item-text="name"
                                item-value="id"
                                label="Шоурум"
                                menu-props="auto"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="3" sm="6">
                              <v-select
                                v-model="editedItem.year"
                                :items="range(2023, 1960)"
                                :rules="[(v) => !!v || 'Выберите год выпуска']"
                                hide-details
                                label="Год выпуска"
                                menu-props="auto"
                                required
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="3" sm="6">
                              <v-menu
                                v-model="menu1"
                                :close-on-content-click="false"
                                max-width="290"
                              >
                                <template v-slot:activator="{ on, attrs }">
                                  <v-text-field
                                    v-bind="attrs"
                                    :value="dateFormatted"
                                    clearable
                                    label="Приход"
                                    readonly
                                    :disabled="true"
                                    v-on="on"
                                    @blur="date = parseDate(dateFormatted)"
                                    @click:clear="editedItem.income_date = null"
                                  />
                                </template>
                                <v-date-picker
                                  v-model="editedItem.income_date"
                                  locale="ru-RU"
                                  :disabled="true"
                                  @change="menu1 = false"
                                />
                              </v-menu>
                            </v-col>
                            <v-col cols="12" md="4" sm="6">
                              <v-text-field
                                v-model="editedItem.auto_ru"
                                label="Ссылка Авто.ру"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="4">
                              <v-select
                                v-model="editedItem.bodytype_id"
                                :items="bodytypes"
                                :rules="[(v) => !!v || 'Выберите тип кузова']"
                                hide-details
                                item-text="name"
                                item-value="id"
                                label="Кузов"
                                menu-props="auto"
                                required
                                :disabled="true"
                              />
                            </v-col>

                            <v-col cols="12" md="4" sm="6">
                              <v-text-field
                                v-model="editedItem.owner_count"
                                label="Собственик"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="4" sm="6">
                              <v-text-field
                                v-model="editedItem.milage"
                                label="Пробег"
                                type="number"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="4" sm="6">
                              <v-select
                                v-model="editedItem.wheeltype_id"
                                :items="wheeltypes"
                                hide-details
                                item-text="name"
                                item-value="id"
                                label="Привод"
                                menu-props="auto"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="3" sm="6">
                              <v-select
                                v-model="editedItem.transmission_id"
                                :items="transmissions"
                                hide-details
                                item-text="name"
                                item-value="id"
                                label="КПП"
                                menu-props="auto"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="3" sm="6">
                              <v-select
                                v-model="editedItem.volume"
                                :items="range(0.8, 5, 0.1)"
                                hide-details
                                label="Объем"
                                menu-props="auto"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="3" sm="6">
                              <v-text-field
                                v-model="editedItem.power"
                                label="Л.С."
                                type="number"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="3" sm="4">
                              <v-select
                                v-model="editedItem.enginetype_id"
                                :items="enginetypes"
                                hide-details
                                item-text="name"
                                item-value="id"
                                label="Двигатель"
                                menu-props="auto"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="3" sm="6">
                              <v-text-field
                                v-model="editedItem.salon"
                                label="Тип салона"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="3" sm="4">
                              <v-text-field
                                v-model="editedItem.color"
                                label="Цвет"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="3" sm="6">
                              <v-text-field
                                v-model="editedItem.vin_number"
                                label="Вин"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="3" sm="6">
                              <v-text-field
                                v-model="editedItem.number"
                                label="Гос. номер"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="6">
                              <v-text-field
                                v-model="editedItem.sts"
                                label="СТС"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="3" sm="6">
                              <v-text-field
                                v-model="editedItem.price"
                                label="Продажа"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="3" sm="6">
                              <v-select
                                v-model="editedItem.ptc"
                                :items="[
                                  { id: 1, name: 'ПТС' },
                                  { id: 0, name: 'Нет' },
                                  { id: 2, name: 'Дубликат' },
                                  { id: 3, name: 'ЭПТС' },
                                ]"
                                item-text="name"
                                item-value="id"
                                label="ПТС"
                                menu-props="auto"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="4" sm="6">
                              <v-text-field
                                v-model="editedItem.key_number"
                                label="№ ключа"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="3" sm="6">
                              <v-text-field
                                v-model="editedItem.manager"
                                label="Менеджер"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="3" sm="6">
                              <v-select
                                v-model="editedItem.status_id"
                                :items="statuses"
                                hide-details
                                item-text="status"
                                item-value="id"
                                label="Статус"
                                menu-props="auto"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="6">
                              <v-select
                                v-model="editedItem.registered"
                                :items="[
                                  { id: null, name: ' ' },
                                  { id: 1, name: 'Да' },
                                  { id: 0, name: 'Нет' },
                                ]"
                                item-text="name"
                                item-value="id"
                                label="На учете"
                                menu-props="auto"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="4" sm="6">
                              <v-text-field
                                v-model="editedItem.comment"
                                label="Коментарии"
                                :disabled="true"
                              />
                            </v-col>
                          </v-row>
                        </v-form>
                      </v-container>
                    </v-card-text>

                    <v-card-actions>
                      <v-btn v-print="printObj" color="primary">
                        <v-icon class="mr-2"> mdi-printer </v-icon> Печать
                      </v-btn>
                      <v-spacer />
                      <v-btn
                        :disabled="true"
                        color="error"
                        text
                        @click="deleteItem(editedItem.id)"
                      >
                        {{
                          editedItem.deleted_at === null
                            ? 'Удалить'
                            : 'Удалить из корзины'
                        }}
                      </v-btn>
                      <v-btn color="blue darken-1" text @click="close">
                        Отмена
                      </v-btn>
                      <v-btn
                        color="blue darken-1"
                        text
                        :disabled="true"
                        @click="save()"
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
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      v-bind="attrs"
                      class="ml-2 mt-1"
                      color="indigo"
                      dark
                      v-on="on"
                    >
                      <v-icon class="mr-2"> mdi-menu </v-icon>
                      Меню
                    </v-btn>
                  </template>

                  <v-card>
                    <v-list>
                      <v-list-item>
                        <v-list-item-action>
                          <v-btn
                            class="mb-2"
                            color="primary"
                            dark
                            @click="dialog = true"
                          >
                            Добавить
                          </v-btn>
                        </v-list-item-action>
                      </v-list-item>

                      <v-list-item>
                        <v-list-item-action>
                          <v-btn
                            class="mb-2"
                            color="error"
                            dark
                            @click="clearFilter()"
                          >
                            Сбросить
                          </v-btn>
                        </v-list-item-action>
                      </v-list-item>
                      <v-list-item v-if="canDownload()">
                        <v-list-item-action>
                          <JsonExcel
                            :data="filtered"
                            :fields="json_fields"
                            class="ml-2 mr-2 mb-1"
                            name="Авто БУ.xls"
                            worksheet="My Worksheet"
                          >
                            <v-btn color="success" outlined>
                              <v-icon color="success" left>
                                mdi-file-excel
                              </v-icon>
                              Экспорт в Excel
                            </v-btn>
                          </JsonExcel>
                        </v-list-item-action>
                      </v-list-item>
                      <v-list-item>
                        <v-list-item-action>
                          <v-switch
                            v-show="
                              role.name === 'admin' ||
                              role.name === 'coordinator' ||
                              role.name === 'coordinator1'
                            "
                            v-model="deleted"
                            class="mt-1"
                            color="red"
                            hide-details
                            label="Корзина"
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
            <template v-slot:action="{ attrs }">
              <v-btn color="deep-orange" icon @click="snackbar = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </template>
          </v-snackbar>
          <div id="printMe" class="print">
            <div class="container">
              <h4 class="text-center mt-3">Инфо об авто</h4>
              <div class="table-responsive">
                <table class="table table-bordered" width="100%">
                  <thead>
                    <tr>
                      <th>Марка и модель</th>
                      <th>Шоурум</th>
                      <th>Год выпуска</th>
                      <th>Привод</th>
                      <th>КПП</th>
                      <th>Объем (Л.С.)</th>
                      <th>Цвет</th>
                      <th>Вин</th>
                      <th>Гос. номер</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>
                        {{ getValue('mark', editedItem.mark_id) }}
                        {{ getValue('model', editedItem.model_id) }}
                      </td>
                      <td>
                        <span v-if="editedItem.showroom_id === 1">Урус</span>
                        <span v-else-if="editedItem.showroom_id === 2"
                          >КомфортАвто</span
                        >
                        <span v-else-if="editedItem.showroom_id === 3"
                          >Склад</span
                        >
                        <span v-else-if="editedItem.showroom_id === 4"
                          >АвтоПремиум</span
                        >
                        <span v-else-if="editedItem.showroom_id === 5"
                          >Авангард Юг</span
                        >
                        <span v-else-if="editedItem.showroom_id === 7"
                          >Форсаж</span
                        >
                        <span v-else-if="editedItem.showroom_id === 8"
                          >АвтоПремьер</span
                        >
                        <span v-else>Не определено</span>
                      </td>
                      <td>{{ editedItem.year }}</td>
                      <td>
                        {{ getValue('wheeltype', editedItem.transmission_id) }}
                      </td>
                      <td>
                        {{
                          getValue('transmission', editedItem.transmission_id)
                        }}
                      </td>
                      <td>{{ editedItem.volume }} ({{ editedItem.power }})</td>
                      <td>{{ editedItem.color }}</td>
                      <td>{{ editedItem.vin_number }}</td>
                      <td>{{ editedItem.number }}</td>
                    </tr>
                    <tr>
                      <th>Комментарии:</th>
                      <td colspan="8">
                        {{ editedItem.comment }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<style scoped>
@media print {
  @page {
    size: landscape;
  }
}

@media screen {
  .print {
    display: none;
  }
}

@media print {
  body * {
    visibility: hidden;
  }
  .print,
  .print * {
    visibility: visible;
  }
  .print {
    position: absolute;
    left: 0;
    top: 0;
  }
}

table {
  border: none;
  border-collapse: collapse;
}

table td {
  border: 1px solid black;
  padding: 0 1px !important;
  text-align: center;
}

table th {
  border: 1px solid rgb(204, 200, 200);
  padding: 0 4px !important;
  font-weight: 700;
  color: black;
}

.v-data-table > .v-data-table__wrapper > table > thead > tr > th {
  padding: 0 4px !important;

  font-weight: 700;
  font-size: 0.7rem;
  color: black !important;
}

table.v-data-table__wrapper > table > thead > tr > th {
  padding: 0 6px;
}

.v-data-table > .v-data-table__wrapper > table > tfoot > tr > th {
  padding: 0 2px;
  font-size: 0.7rem;
}

.v-data-table > .v-data-table__wrapper > table > tbody > tr > td {
  font-size: 0.7rem;
  font-weight: 600;
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
  padding: 0 2px !important;
  font-size: 0.7rem;
  font-weight: 500;
}

.vertical {
  writing-mode: tb-rl;
  font-size: 0.7rem;
  -webkit-transform: rotate(180deg);
  -moz-transform: rotate(180deg);
  -o-transform: rotate(180deg);
  -ms-transform: rotate(180deg);
  transform: rotate(180deg);
  white-space: nowrap;
  display: block;
  bottom: 0;
  height: 60px;
  margin-bottom: 5px;
  align-content: center;
  padding: 0;
}

.v-data-table
  /deep/
  tbody
  /deep/
  tr:hover:not(.v-data-table__expanded__content) {
  background: yellow !important;
}
</style>
<script>
import JsonExcel from 'vue-json-excel'
import BreadCrumb from '~/components/BreadCrumb'

export default {
  layout: 'user',
  middleware: 'permission',
  components: { BreadCrumb, JsonExcel },
  data: () => ({
    menu1: null,
    componentKey: 0,
    filter: {
      mark_id: null,
      model_id: null,
      transmission_id: null,
      wheeltype_id: null,
      year_from: null,
      year_to: null,
      milage_from: null,
      milage_to: null,
      incomePriceFrom: null,
      incomePriceTo: null,
    },
    deleted: false,
    transit_to: null,
    transit_comment: null,
    menu: null,
    dialog: false,
    dialogTransit: false,
    valid: false,
    snackbar: false,
    text: '',
    color: '',
    timeout: 6000,
    expanded: [],
    singleExpand: false,
    headers: [
      {
        text: 'Авто.ру',
        value: 'auto_ru',
        align: 'center',
        sortable: false,
        width: '30px',
      },
      {
        text: 'Салон',
        align: 'center',
        sortable: false,
        value: 'showroom.name',
        width: '30px',
      },
      {
        text: 'Марка',
        align: 'start',
        value: 'mark.name',
        width: '100px',
      },
      {
        text: 'Модель',
        value: 'model.name',
        width: '110px',
        align: 'center',
      },
      {
        text: 'Год',
        value: 'year',
        sortable: false,
        width: '50px',
        align: 'center',
      },
      {
        text: 'Кузов',
        value: 'bodytype.name',
        sortable: false,
        width: '50px',
        align: 'center',
      },
      {
        text: 'Соб.',
        value: 'owner_count',
        sortable: false,
        width: '10px',
        align: 'center',
      },
      {
        text: 'КПП',
        value: 'transmission.name',
        width: '25px',
        sortable: false,
        align: 'center',
      },
      {
        text: 'Объем',
        value: 'volume',
        sortable: false,
        width: '70px',
        align: 'center',
      },

      {
        text: 'Двиг.',
        value: 'enginetype.name',
        sortable: false,
        width: '20px',
        align: 'center',
      },
      {
        text: 'Салон',
        value: 'salon',
        sortable: false,
        width: '20px',
        align: 'center',
      },
      {
        text: 'Цвет',
        value: 'color',
        align: 'center',
        sortable: false,
        width: '100px',
      },
      {
        text: 'Vin',
        value: 'vin_number',
        align: 'center',
        sortable: false,
        width: '120px',
      },
      {
        text: 'Гос.ном.',
        value: 'number',
        sortable: false,
        width: '60px',
        align: 'center',
      },
      {
        text: 'СТС',
        value: 'sts',
        sortable: false,
        width: '90px',
        align: 'center',
      },
      {
        text: 'Продажа',
        value: 'price',
        align: 'center',
        sortable: false,
        width: '125px',
      },
      {
        text: 'ПТС',
        value: 'ptc',
        sortable: false,
        align: 'center',
        width: '30px',
      },
      {
        text: '№-ключа',
        value: 'key_number',
        width: '30px',
        align: 'center',
        sortable: false,
      },
      {
        text: 'Статус',
        value: 'status.status',
        align: 'center',
        sortable: false,
        width: '100px',
      },
    ],
    editedIndex: -1,
    editedItem: {
      id: '',
      showroom_id: 2,
      income_date: '',
      mark_id: '',
      model_id: '',
      year: '',
      auto_ru: '',
      bodytype_id: '',
      transmission_id: '',
      enginetype_id: '',
      salon: '',
      wheeltype_id: '',
      owner_count: 1,
      power: '',
      color: '',
      milage: 0,
      volume: '',
      vin_number: '',
      number: '',
      sts: '',
      ptc: 1,
      income_price: '',
      price: 0,
      key_number: '',
      manager: '',
      status_id: '',
      registered: '',
      transit: '',
      comment: null,
      deleted_at: null,
    },
    defaultItem: {
      id: '',
      showroom_id: '',
      income_date: '',
      mark_id: '',
      model_id: '',
      year: '',
      auto_ru: '',
      bodytype_id: '',
      transmission_id: '',
      enginetype_id: '',
      salon: '',
      wheeltype_id: '',
      owner_count: 1,
      power: '',
      color: '',
      milage: 0,
      volume: '',
      vin_number: '',
      number: '',
      sts: '',
      ptc: 1,
      income_price: '',
      price: 0,
      key_number: '',
      manager: '',
      status_id: '',
      registered: '',
      transit: '',
      comment: null,
      deleted_at: null,
    },
    options: {
      sortBy: ['mark.name', 'model.name'],
      multiSort: true,
    },
    json_fields: {
      'Дата прихода': 'income_date',
      'Авто.ру': 'auto_ru',
      Шоурум: 'showroom.name',
      Марка: 'mark.name',
      Модель: 'model.name',
      Год: 'year',
      Кузов: 'bodytype.name',
      'Собств.': 'owner_count',
      Пробег: 'milage',
      Привод: 'wheeltype.name',
      КПП: 'transmission.name',
      Объем: {
        field: 'volume',
        callback: (value) => {
          if (value) {
            return value + ' л.'
          }
        },
      },
      'Л.С.': 'power',
      Цвет: 'color',
      Vin: 'vin_number',
      'Гос.ном': 'number',
      СТС: 'sts',
      Продажа: 'price',
      ПТС: {
        field: 'ptc',
        callback: (value) => {
          if (value === 1) {
            return 'ПТС'
          } else if (value === 2) {
            return 'Дубликать'
          } else if (value === 3) {
            return 'ЭПТС'
          } else {
            return 'Нет'
          }
        },
      },
      'На учете': {
        field: 'registered',
        callback: (value) => {
          if (value === 1) {
            return 'Да'
          } else {
            return 'Нет'
          }
        },
      },
      '№ ключа': 'key_number',
      Статус: 'status.status',
      Менеджер: 'manager',
      Коментарии: 'comment',
    },
    json_meta: [
      [
        {
          key: 'charset',
          value: 'utf-8',
        },
      ],
    ],
    printLoading: false,
    printObj: {
      id: 'printMe',
      popTitle: ' ',
      extraCss:
        'https://cdn.bootcdn.net/ajax/libs/animate.css/4.1.1/animate.compat.css, https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css',
      extraHead: '<meta http-equiv="Content-Language" content="ru-ru"/>',
      beforeOpenCallback(vue) {
        vue.printLoading = true
      },
      openCallback(vue) {
        vue.printLoading = false
      },
      closeCallback(vue) {
        vue.printLoading = false
      },
    },
  }),

  computed: {
    dateFormatted() {
      return this.editedItem.income_date
        ? this.moment(this.editedItem.income_date).format('DD.MM.Y')
        : null
    },
    showroom_id() {
      return Number(this.$route.params.id) || null
    },
    showroom() {
      return this.$store.state.showroom.showroom
    },
    formTitle() {
      return this.editedIndex === -1 ? 'Новая запись' : 'Изменить'
    },
    usedcars() {
      return this.$store.state.usedcar.cars
    },
    filtered() {
      let cars = this.usedcars
      if (this.deleted) {
        cars = cars.filter((item) => item.deleted_at !== null)
      } else {
        cars = cars.filter((item) => item.deleted_at === null)
      }
      if (this.$route.params.id) {
        cars = cars.filter(
          (item) =>
            item.showroom_id === Number(this.$route.params.id) ||
            (item.transit === 1 &&
              item.transits.filter(
                (i) =>
                  i.status === 0 &&
                  (i.showroom_id === this.showroom_id ||
                    i.from === this.showroom_id)
              ).length > 0)
        )
      }
      if (this.filter.mark_id) {
        cars = cars.filter(
          (item) =>
            item.mark_id === this.filter.mark_id && item.deleted_at === null
        )
      }
      if (this.filter.model_id) {
        cars = cars.filter(
          (item) =>
            item.model_id === this.filter.model_id && item.deleted_at === null
        )
      }
      if (this.filter.transmission_id) {
        cars = cars.filter(
          (item) =>
            item.transmission_id === this.filter.transmission_id &&
            item.deleted_at === null
        )
      }
      if (this.filter.wheeltype_id) {
        cars = cars.filter(
          (item) =>
            item.wheeltype_id === this.filter.wheeltype_id &&
            item.deleted_at === null
        )
      }
      if (this.filter.year_from && this.filter.year_to) {
        cars = cars.filter(
          (item) =>
            item.year >= this.filter.year_from &&
            item.year <= this.filter.year_to
        )
      } else if (this.filter.year_from) {
        cars = cars.filter((item) => item.year >= this.filter.year_from)
      } else if (this.filter.year_to) {
        cars = cars.filter((item) => item.year <= this.filter.year_to)
      }
      if (this.filter.milage_from && this.filter.milage_to) {
        cars = cars.filter(
          (item) =>
            item.milage >= this.filter.milage_from &&
            item.milage <= this.filter.milage_to
        )
      } else if (this.filter.milage_from) {
        cars = cars.filter((item) => item.milage >= this.filter.milage_from)
      } else if (this.filter.milage_to) {
        cars = cars.filter((item) => item.milage <= this.filter.milage_to)
      }
      if (this.filter.incomePriceFrom && this.filter.incomePriceTo) {
        cars = cars.filter(
          (item) =>
            item.income_price >= this.filter.incomePriceFrom &&
            item.income_price <= this.filter.incomePriceTo
        )
      } else if (this.filter.incomePriceFrom) {
        cars = cars.filter(
          (item) => item.income_price >= this.filter.incomePriceFrom
        )
      } else if (this.filter.incomePriceTo) {
        cars = cars.filter(
          (item) => item.income_price <= this.filter.incomePriceTo
        )
      }
      return cars
    },
    marks() {
      return this.$store.state.car.marks
    },
    models() {
      return this.$store.state.car.models
    },
    enginetypes() {
      return this.$store.state.car.enginetypes
    },
    bodytypes() {
      return this.$store.state.car.bodytypes
    },
    transmissions() {
      return this.$store.state.car.transmissions
    },
    wheeltypes() {
      return this.$store.state.car.wheeltypes
    },
    statuses() {
      return this.$store.state.usedcar.statuses
    },
    user() {
      return this.$store.state.auth.user
    },
    role() {
      return this.$store.state.auth.role
    },
    links() {
      return [
        {
          text: this.role.title,
          disabled: false,
          href: '/' + this.role?.name,
        },
        {
          text: 'Автомобили БУ',
          disabled: false,
          href: '/' + this.role?.name + '/used-cars',
        },
        {
          text: this.showroom.name || '',
          disabled: true,
          href:
            '/' + this.role?.name + '/used-cars/' + (this.showroom.id || ''),
        },
      ]
    },
    receivers() {
      return [
        { id: 1, name: 'Урус' },
        { id: 2, name: 'КомфортАвто' },
        { id: 3, name: 'Склад' },
        { id: 4, name: 'АвтоПремиум' },
      ].filter((item) => item.id !== this.showroom_id)
    },
  },
  watch: {
    dialog(val) {
      val || this.close()
    },
    deleted(val) {
      val === false || this.clearFilter()
    },
  },

  async fetch({ store, error, params: { id } }) {
    await store.dispatch('usedcar/fetchCars', {})
    await store.dispatch('car/fetchMarks')
    await store.dispatch('car/fetchBodyTypes')
    await store.dispatch('car/fetchEngineTypes')
    await store.dispatch('car/fetchTransmissions')
    await store.dispatch('car/fetchWheelTypes')
    await store.dispatch('showroom/fetchShowroom', { id })
    await store.dispatch('usedcar/fetchStatuses')
    await store.dispatch('user/toggle', false)
  },
  mounted() {
    this.$echo.channel('used-cars').listen('UsedCarAdded', (e) => {
      console.log('usedcar added')
      this.$store.dispatch('usedcar/fetchCars', {}).catch((error) => {
        console.log(error)
        this.reload()
      })
    })
  },
  methods: {
    canDownload() {
      const users = [
        'admin',
        'director',
        'coordinator',
        'coordinator2',
        'coordinator4',
        'manager_acp',
        'manager_light',
        'petr',
        'manager_avrova',
        'mark',
      ]
      return users.includes(this.role?.name)
    },
    isManagers() {
      const users = ['manager', 'manager1', 'manager_avrova', 'mark']
      return users.includes(this.role?.name)
    },
    seePrice() {
      const users = [
        'admin',
        'director',
        'manager_avrova',
        'mark',
        'manager_acp',
        'petr',
        'manager_light',
        'plan',
      ]
      return users.includes(this.role?.name)
    },
    parseDate(date) {
      if (!date) {
        return null
      }
      return this.moment(date).format('YY-MM-dd')
    },
    save() {
      if (this.valid) {
        if (this.editedIndex > -1) {
          this.$store.dispatch('usedcar/save', {
            item: this.editedItem,
          })
          this.showSnack('success', 'Данные изменены')
        } else {
          this.$store.dispatch('usedcar/save', {
            item: this.editedItem,
          })
          this.showSnack('success', 'Данные добавлены')
        }
        this.close()
      } else {
        this.showSnack('error', 'Заполните обязательные поля')
      }
    },

    editItem(item) {
      this.editedIndex = this.usedcars.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
      if (item.mark_id !== 0) {
        this.$store.dispatch('car/fetchModels', { markId: item.mark_id })
      }
    },
    deleteItem(id) {
      confirm('Вы уверены, что хотите удалить этот запись?') &&
        this.$store
          .dispatch('usedcar/delete', { id })
          .then(() => {
            this.showSnack('success', 'Данные удалены')
          })
          .catch((error) => {
            this.showSnack('error', 'Ошибка: ' + error)
          })
      this.dialog = false
    },
    transitItem(item) {
      item.sender_id = this.user && this.user.id
      this.dialogTransit = true
    },

    transit() {
      const item = this.editedItem
      item.sender_id = this.user && this.user.id
      item.showroom_id = this.transit_to || null
      item.transit_comment = this.transit_comment
      item.from = this.showroom_id
      this.$store
        .dispatch('usedcar/transit', { item })
        .then(() => {
          this.showSnack('success', 'Автомобиль переведён')
        })
        .catch((error) => {
          this.showSnack('error', 'Ошибка: ' + error)
        })
      this.transit_to = null
      this.dialogTransit = false
      this.transit_comment = null
      this.close()
    },
    cancelTransit(item) {
      confirm('Вы уверены, что хотите отменть транзит?') &&
        this.$store
          .dispatch('usedcar/cancelTransit', { item })
          .then(() => {
            this.showSnack('success', 'Транзит отменён')
          })
          .catch((error) => {
            this.showSnack('error', 'Ошибка: ' + error)
          })
      this.close()
    },
    approveTransit(item) {
      item.receiver_id = this.user && this.user.id
      confirm('Вы уверены, что хотите принять транзит?') &&
        this.$store.dispatch('usedcar/approveTransit', { item }).then(() => {
          this.showSnack('success', 'Транзит принять')
        })
      this.close()
    },

    close() {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
        this.componentKey += 1
      })
    },

    showSnack(type, text) {
      this.text = text
      this.color = type
      this.snackbar = true
    },
    validate() {
      this.$refs.form.validate()
    },
    clearFilter() {
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

    range(start, end, step = 1) {
      const years = []
      if (start > end) {
        for (let i = start; i >= end; i = i - step) {
          if (step === 1) {
            years.push(i)
          } else {
            years.push(i.toFixed(1))
          }
        }
      } else {
        for (let i = start; i <= end; i = i + step) {
          if (step === 1) {
            years.push(i)
          } else {
            years.push(i.toFixed(1))
          }
        }
      }
      return years
    },
    getModels(markId = null) {
      this.editedItem.model_id = []
      if (markId !== 0) {
        this.$store.dispatch('car/fetchModels', { markId })
      }
    },
    row_classes(item) {
      if (item.status_id === 1) {
        return 'success white--text'
      }
      if (item.status_id === 2) {
        return 'error white--text'
      }
      if (item.status_id === 3) {
        return 'yellow black--text'
      }
      if (item.status_id === 4) {
        return 'warning white--text'
      }
      if (item.status_id === 5) {
        return 'primary white--text'
      }
      if (item.status_id === 7) {
        return 'blue darken-4 white--text'
      } else if (item.transit === 1 || item.transit === 2) {
        return 'orange lighten-2' // can also return multiple classes e.g ["orange","disabled"]
      } else if (item.deleted_at !== null) {
        return 'black lighten-4 white--text'
      }
    },

    getColor(transit) {
      if (transit === 1) {
        return 'primary' // can also return multiple classes e.g ["orange","disabled"]
      } else if (transit === 2) {
        return 'error'
      } else {
        return 'success'
      }
    },
    transitStatus(item) {
      if (item.transit === 1 && item.showroom_id === this.showroom_id) {
        return 'На выезде (Отменить)' // can also return multiple classes e.g ["orange","disabled"]
      } else if (item.transit === 1) {
        return 'Принять транзит'
      } else {
        return 'Передать в другой салон'
      }
    },
    isNumber(evt) {
      evt = evt || window.event
      const charCode = evt.which ? evt.which : evt.keyCode
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        evt.preventDefault()
      } else {
        return true
      }
    },
    oldStyle(date) {
      return date !== null && this.moment().diff(this.moment(date), 'days') > 90
        ? 'background-color: red; color: white;'
        : ''
    },
    reload() {
      this.intervalid1 = setInterval(() => {
        console.log('reload')
        this.$store.dispatch('usedcar/fetchUsedCars')
      }, 15000)
    },
    getValue(type, id) {
      let res
      switch (type) {
        case 'mark':
          res = this.marks.find((l) => l.id === id)
          return res && res.name
        case 'model':
          res = this.models.find((l) => l.id === id)
          return res && res.name
        case 'transmission':
          res = this.transmissions.find((l) => l.id === id)
          return res && res.name
        case 'wheeltype':
          res = this.wheeltypes.find((l) => l.id === id)
          return res && res.name
        default:
      }
    },
  },
  beforeRouteLeave(to, from, next) {
    this.$store.dispatch('user/toggle', true)
    next()
  },
}
</script>

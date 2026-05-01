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
            <template v-slot:header.price_avito="{ header }">
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
            <template v-slot:header.income_price="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template v-slot:header.price="{ header }">
              <span class="">{{ header.text }}</span>
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
                  class="purple-bg"
                  :class="row_classes(item)"
                  @dblclick="editItem(item)"
                >
                  <td :style="oldStyle(item.income_date)">
                    <template v-if="item.income_date !== null">
                      {{
                        $moment(new Date(item.income_date)).format('DD.MM.Y')
                      }}
                    </template>
                  </td>
                  <td>
                    <span v-if="item.auto_ru !== null"
                      ><a
                        :href="item.auto_ru"
                        :class="row_classes(item)"
                        target="_blank"
                      >
                        Перейти
                      </a></span
                    >
                  </td>
                  <td>
                    {{ item.price_avito }}
                  </td>
                  <td>
                    <v-chip
                      :color="$getChipColor(item.showroom)"
                      :text-color="$getChipTextColor(item.showroom)"
                      small
                    >
                      {{ item.showroom?.name }}
                    </v-chip>
                  </td>
                  <td>{{ item.mark && item.mark.name }}</td>
                  <td>{{ item.model && item.model.name }}</td>
                  <td>{{ item.year }}</td>
                  <td>{{ item.bodytype && item.bodytype.name }}</td>
                  <td>{{ item.owner_count }}</td>
                  <td
                    :style="
                      item.is_ready
                        ? 'background-color:#4CAF50 !important; color: white;'
                        : item.milage > 130000
                        ? 'background-color:red; color: white;'
                        : ''
                    "
                  >
                    {{ item.milage }}
                  </td>
                  <td>{{ item.wheeltype && item.wheeltype.name }}</td>
                  <td>{{ item.transmission && item.transmission.name }}</td>
                  <td>{{ item.volume }} ({{ item.power }} ЛС)</td>
                  <td>{{ item.enginetype && item.enginetype.name }}</td>
                  <td>{{ item.salon }}</td>
                  <td>{{ item.color }}</td>
                  <td>{{ item.vin_number }}</td>
                  <td>{{ item.number }}</td>
                  <td>{{ item.sts }}</td>

                  <td v-if="seePrice()">
                    {{ parseFloat(item.income_price) + total_item_price(item) }}
                  </td>
                  <td v-else />
                  <td>{{ item.price }}</td>

                  <td>
                    <span v-if="item.ptc === 1">ПТС</span>
                    <span v-else-if="item.ptc === 2" key="copy">Дубликат</span>
                    <span v-else-if="item.ptc === 3" key="epts">ЭПТС</span>
                    <span v-else key="no">НЕТ</span>
                  </td>
                  <td>
                    <span v-if="item.registered === 1" key="da">Да</span>
                    <span v-else-if="item.registered === 0" key="net">Нет</span>
                  </td>

                  <td>
                    {{ item.manager && item.manager.name }}
                  </td>
                  <td>{{ item.key_number }}</td>
                  <td>{{ total_item_price(item) }}</td>
                  <td>{{ item.entity }}</td>
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
                <v-row class="mt-4 hidden-sm-and-down">
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
                      <v-col cols="3" md="1" xs="6">
                        <v-text-field
                          v-model="filter.incomePriceTo"
                          clearable
                          dense
                          label="Приход до"
                          outlined
                        />
                      </v-col>
                      <v-col cols="3" md="1" xs="6" class="mt-0">
                        <v-btn
                          class="mt-n1"
                          icon
                          color="green"
                          @click="exportFile()"
                        >
                          <v-icon large>mdi-file-excel</v-icon>
                        </v-btn>
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>

                <v-dialog v-model="dialog" max-width="1070px">
                  <v-card>
                    <v-card-title class="mt-0">
                      <span class="headline">{{ formTitle }}</span>
                      <v-chip
                        class="ml-10"
                        v-if="editedItem.user !== null && editedIndex !== -1"
                        color="error"
                      >
                        Удалил
                        {{ editedItem.user && editedItem.user.name }}
                      </v-chip>
                    </v-card-title>
                    <v-card-text class="mt-0">
                      <v-container class="mt-0">
                        <v-form ref="form" v-model="valid">
                          <v-row dense>
                            <v-col cols="12" md="3" sm="6">
                              <v-select
                                v-model="editedItem.mark_id"
                                :items="marks"
                                :rules="[(v) => !!v || 'Выберите марку']"
                                hide-details
                                item-text="name"
                                item-value="id"
                                label="Марка"
                                menu-props="auto"
                                required
                                @change="getModels(editedItem.mark_id)"
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="6">
                              <v-select
                                v-model="editedItem.model_id"
                                :items="models"
                                :rules="[(v) => !!v || 'Выберите модель']"
                                hide-details
                                item-text="name"
                                item-value="id"
                                label="Модель"
                                menu-props="auto"
                                required
                              />
                            </v-col>

                            <v-col cols="12" md="2" sm="6">
                              <v-select
                                v-model="editedItem.year"
                                :items="range(2023, 1960)"
                                :rules="[(v) => !!v || 'Выберите год выпуска']"
                                hide-details
                                label="Год выпуска"
                                menu-props="auto"
                                required
                              />
                            </v-col>

                            <v-col cols="12" md="2" sm="6" class="mx-0">
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
                                    hide-details
                                    v-on="on"
                                    @blur="date = parseDate(dateFormatted)"
                                    @click:clear="editedItem.income_date = null"
                                  />
                                </template>
                                <v-date-picker
                                  v-model="editedItem.income_date"
                                  locale="ru-RU"
                                  @change="menu1 = false"
                                />
                              </v-menu>
                            </v-col>
                            <v-col cols="12" md="3" sm="6" class="mx-0">
                              <v-select
                                v-model="editedItem.showroom_id"
                                :items="showrooms"
                                hide-details
                                item-text="name"
                                item-value="id"
                                label="Шоурум"
                                menu-props="auto"
                              />
                            </v-col>
                            <v-col cols="12" md="12" sm="12" class="mx-0">
                              <v-row dense>
                                <v-col cols="12" md="6" sm="6" class="mx-0">
                                  <v-row dense>
                                    <v-col cols="12" md="3" sm="6" class="mx-0">
                                      <v-text-field
                                        v-model="editedItem.milage"
                                        label="Пробег"
                                        type="number"
                                        hide-details
                                      />
                                    </v-col>
                                    <v-col cols="12" md="2" sm="6" class="mx-0">
                                      <v-select
                                        v-model="editedItem.wheeltype_id"
                                        :items="wheeltypes"
                                        hide-details
                                        item-text="name"
                                        item-value="id"
                                        label="Привод"
                                        menu-props="auto"
                                      />
                                    </v-col>
                                    <v-col cols="12" md="2" sm="6" class="mx-0">
                                      <v-select
                                        v-model="editedItem.transmission_id"
                                        :items="transmissions"
                                        hide-details
                                        item-text="name"
                                        item-value="id"
                                        label="КПП"
                                        menu-props="auto"
                                      />
                                    </v-col>
                                    <v-col cols="12" md="2" sm="6" class="mx-0">
                                      <v-select
                                        v-model="editedItem.volume"
                                        :items="range(0.8, 5, 0.1)"
                                        hide-details
                                        label="Объем"
                                        menu-props="auto"
                                      />
                                    </v-col>
                                    <v-col cols="12" md="3" sm="6" class="mx-0">
                                      <v-text-field
                                        v-model="editedItem.power"
                                        label="Л.С."
                                        type="number"
                                        hide-details
                                      />
                                    </v-col>
                                  </v-row>
                                </v-col>
                                <v-col cols="12" md="6" sm="6" class="mx-0">
                                  <v-row dense>
                                    <v-col cols="12" md="3" sm="6" class="mx-0">
                                      <v-select
                                        v-model="editedItem.bodytype_id"
                                        :items="bodytypes"
                                        :rules="[
                                          (v) => !!v || 'Выберите тип кузова',
                                        ]"
                                        hide-details
                                        item-text="name"
                                        item-value="id"
                                        label="Кузов"
                                        menu-props="auto"
                                        required
                                      />
                                    </v-col>

                                    <v-col cols="12" md="3" sm="6" class="mx-0">
                                      <v-text-field
                                        v-model="editedItem.owner_count"
                                        label="Собственик"
                                        hide-details
                                      />
                                    </v-col>

                                    <v-col cols="12" md="3" sm="4" class="mx-0">
                                      <v-select
                                        v-model="editedItem.enginetype_id"
                                        :items="enginetypes"
                                        hide-details
                                        item-text="name"
                                        item-value="id"
                                        label="Двигатель"
                                        menu-props="auto"
                                      />
                                    </v-col>
                                    <v-col cols="12" md="3" sm="6" class="mx-0">
                                      <v-text-field
                                        v-model="editedItem.salon"
                                        label="Тип салона"
                                        hide-details
                                      />
                                    </v-col>
                                  </v-row>
                                </v-col>
                              </v-row>
                            </v-col>
                            <v-col cols="12" md="3" sm="4" class="mx-0">
                              <v-text-field
                                v-model="editedItem.color"
                                label="Цвет"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" md="3" sm="6" class="mx-0">
                              <v-text-field
                                v-model="editedItem.vin_number"
                                label="Вин"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="6" class="mx-0">
                              <v-text-field
                                v-model="editedItem.number"
                                label="Гос. номер"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="6" class="mx-0">
                              <v-text-field
                                v-model="editedItem.sts"
                                label="СТС"
                                hide-details
                              />
                            </v-col>

                            <v-col cols="12" md="2" sm="6">
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
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="6">
                              <v-text-field
                                v-model="editedItem.entity"
                                label="Юр.лицо"
                                hide-details
                                :disabled="hasAccess()"
                              />
                              {{ entity }}
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
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="6">
                              <v-text-field
                                v-model="editedItem.key_number"
                                label="№ ключа"
                                hide-details
                              />
                            </v-col>

                            <v-col cols="12" md="2" sm="6">
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
                                clearable
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="2">
                              <v-checkbox
                                class="ml-2 mt-5"
                                v-model="editedItem.is_tradein"
                                label="Trade In"
                                outlined
                                dense
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="6">
                              <v-select
                                v-model="editedItem.status_id"
                                :items="statuses"
                                hide-details
                                item-text="status"
                                item-value="id"
                                label="Статус"
                                menu-props="auto"
                              />
                            </v-col>
                            <v-col cols="12" md="7" sm="6" class="mx-0">
                              <v-row dense>
                                <v-col
                                  v-if="seePrice()"
                                  cols="12"
                                  md="3"
                                  sm="6"
                                  class="mx-0"
                                >
                                  <v-text-field
                                    v-model="editedItem.income_price"
                                    label="Заход"
                                    type="number"
                                    hide-details
                                  />
                                </v-col>

                                <v-col
                                  v-if="seePrice()"
                                  cols="12"
                                  md="3"
                                  sm="6"
                                  class="mx-0"
                                >
                                  <v-text-field
                                    label="Заход с расходами"
                                    type="number"
                                    readonly
                                    :value="
                                      total_price() +
                                      parseFloat(editedItem.income_price)
                                    "
                                    hide-details
                                  />
                                </v-col>
                                <v-col cols="12" md="3" sm="6">
                                  <v-text-field
                                    v-model="editedItem.price"
                                    label="Продажа"
                                    hide-details
                                  />
                                </v-col>

                                <v-col cols="12" md="3" class="mx-0">
                                  <v-text-field
                                    :value="repair_total()"
                                    label="Ремонт"
                                    readonly
                                    hide-details
                                  />
                                </v-col>
                              </v-row>
                            </v-col>
                            <v-col cols="12" md="5" sm="6" class="mx-0">
                              <v-row dense>
                                <v-col cols="12" md="4" class="mx-0">
                                  <v-text-field
                                    append-icon="mdi-currency-usd"
                                    v-model="editedItem.transport_price"
                                    label="Автовоз"
                                    hide-details
                                  />
                                </v-col>

                                <v-col cols="12" md="4" class="mx-0">
                                  <v-text-field
                                    append-icon="mdi-currency-usd"
                                    v-model="editedItem.removal_price"
                                    label="Снятие с учета"
                                    hide-details
                                  />
                                </v-col>

                                <v-col cols="12" md="4" sm="6" class="mx-0">
                                  <v-text-field
                                    append-icon="mdi-currency-usd"
                                    :value="total_price()"
                                    label="Общий"
                                    readonly
                                    hide-details
                                  />
                                </v-col>
                              </v-row>
                            </v-col>

                            <v-col cols="12" md="8" sm="12">
                              <v-textarea
                                v-model="editedItem.comment"
                                rows="1"
                                label="Комментарий"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" md="4" sm="6">
                              <v-text-field
                                v-model="editedItem.auto_ru"
                                label="Ссылка Авито"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" md="12" sm="12" class="mt-1">
                              <v-btn
                                small
                                color="error"
                                @click="collapsed = !collapsed"
                              >
                                <span>Сервис</span>
                                <v-icon>
                                  {{
                                    collapsed
                                      ? 'mdi-chevron-up'
                                      : 'mdi-chevron-down'
                                  }}
                                </v-icon>
                              </v-btn>
                            </v-col>

                            <v-col cols="12" md="12" sm="12" v-show="collapsed">
                              <v-row dense>
                                <v-col
                                  cols="12"
                                  md="2"
                                  sm="12"
                                  class="justify-center mt-1"
                                >
                                  Кузовные работы
                                </v-col>
                                <v-col cols="12" md="2" sm="12" class="mt-1">
                                  <v-select
                                    v-model="editedItem.body_status"
                                    :items="repair_statuses"
                                    hide-details
                                    item-text="value"
                                    item-value="id"
                                    label="Статус"
                                    dense
                                    menu-props="auto"
                                  />
                                </v-col>
                                <v-col cols="12" md="2" sm="12" class="mt-1">
                                  <v-text-field
                                    v-model="editedItem.repair_price"
                                    label="Ремонт"
                                    dense
                                    hide-details
                                  />
                                </v-col>
                                <v-col cols="12" md="6" sm="12" class="mt-1">
                                  <v-text-field
                                    v-model="editedItem.body_comment"
                                    label="Коментарии"
                                    hide-details
                                    dense
                                  />
                                </v-col>
                                <v-col
                                  cols="12"
                                  md="2"
                                  sm="12"
                                  class="justify-center mt-1"
                                >
                                  Малярные работы
                                </v-col>
                                <v-col cols="12" md="2" sm="12" class="mt-1">
                                  <v-select
                                    v-model="editedItem.painting_status"
                                    :items="repair_statuses"
                                    hide-details
                                    item-text="value"
                                    item-value="id"
                                    label="Статус"
                                    menu-props="auto"
                                    dense
                                  />
                                </v-col>
                                <v-col cols="12" md="2" sm="12" class="mt-1">
                                  <v-text-field
                                    v-model="editedItem.painting_price"
                                    label="Расход"
                                    hide-details
                                    dense
                                  />
                                </v-col>
                                <v-col cols="12" md="6" sm="12" class="mt-1">
                                  <v-text-field
                                    v-model="editedItem.painting_comment"
                                    label="Коментарии"
                                    hide-details
                                    dense
                                  />
                                </v-col>
                                <v-col
                                  cols="12"
                                  md="2"
                                  sm="12"
                                  class="justify-center mt-1"
                                >
                                  Слесарные работы
                                </v-col>
                                <v-col cols="12" md="2" sm="12" class="mt-1">
                                  <v-select
                                    v-model="editedItem.locksmith_status"
                                    :items="repair_statuses"
                                    hide-details
                                    item-value="id"
                                    item-text="value"
                                    label="Статус"
                                    menu-props="auto"
                                    dense
                                  />
                                </v-col>
                                <v-col cols="12" md="2" sm="12" class="mt-1">
                                  <v-text-field
                                    v-model="editedItem.repair_locksmith"
                                    label="Расход"
                                    hide-details
                                    dense
                                  />
                                </v-col>
                                <v-col cols="12" md="6" sm="12" class="mt-1">
                                  <v-text-field
                                    v-model="editedItem.locksmith_comment"
                                    label="Коментарии"
                                    hide-details
                                    dense
                                  />
                                </v-col>
                                <v-col
                                  cols="12"
                                  md="2"
                                  sm="12"
                                  class="justify-center mt-1"
                                >
                                  Электроника
                                </v-col>
                                <v-col cols="12" md="2" sm="12" class="mt-1">
                                  <v-select
                                    v-model="editedItem.electric_status"
                                    :items="repair_statuses"
                                    hide-details
                                    item-value="id"
                                    item-text="value"
                                    label="Статус"
                                    menu-props="auto"
                                    dense
                                  />
                                </v-col>
                                <v-col cols="12" md="2" sm="12" class="mt-1">
                                  <v-text-field
                                    v-model="editedItem.repair_electric"
                                    label="Расход"
                                    hide-details
                                    dense
                                  />
                                </v-col>
                                <v-col cols="12" md="6" sm="12" class="mt-1">
                                  <v-text-field
                                    v-model="editedItem.electric_comment"
                                    label="Коментарии"
                                    hide-details
                                    dense
                                  />
                                </v-col>
                                <v-col
                                  cols="12"
                                  md="2"
                                  sm="12"
                                  class="justify-center mt-1"
                                >
                                  Прочие работы
                                </v-col>
                                <v-col cols="12" md="2" sm="12" class="mt-1">
                                  <v-select
                                    v-model="editedItem.others_status"
                                    :items="repair_statuses"
                                    hide-details
                                    item-value="id"
                                    item-text="value"
                                    label="Статус"
                                    menu-props="auto"
                                    dense
                                  />
                                </v-col>
                                <v-col cols="12" md="2" sm="12" class="mt-1">
                                  <v-text-field
                                    v-model="editedItem.repair_others"
                                    label="Расход"
                                    hide-details
                                    dense
                                  />
                                </v-col>
                                <v-col cols="12" md="6" sm="12" class="mt-1">
                                  <v-text-field
                                    v-model="editedItem.others_comment"
                                    label="Коментарии"
                                    hide-details
                                    dense
                                  />
                                </v-col>
                                <v-col
                                  cols="12"
                                  md="2"
                                  sm="12"
                                  class="justify-center mt-1"
                                >
                                  Химчистка
                                </v-col>
                                <v-col cols="12" md="2" sm="12" class="mt-1">
                                  <v-select
                                    v-model="editedItem.dry_cleaning_status"
                                    :items="repair_statuses"
                                    hide-details
                                    item-value="id"
                                    item-text="value"
                                    label="Статус"
                                    menu-props="auto"
                                    dense
                                  />
                                </v-col>
                                <v-col cols="12" md="2" sm="12" class="mt-1">
                                  <v-text-field
                                    v-model="editedItem.repair_dry"
                                    label="Расход"
                                    hide-details
                                    dense
                                  />
                                </v-col>
                                <v-col cols="12" md="6" sm="12" class="mt-1">
                                  <v-text-field
                                    v-model="editedItem.dry_cleaning_comment"
                                    label="Коментарии"
                                    hide-details
                                    dense
                                  />
                                </v-col>
                                <v-col
                                  cols="12"
                                  md="2"
                                  sm="12"
                                  class="justify-center mt-1"
                                >
                                  Полировка
                                </v-col>
                                <v-col cols="12" md="2" sm="12" class="mt-1">
                                  <v-select
                                    v-model="editedItem.polishing_status"
                                    :items="repair_statuses"
                                    hide-details
                                    item-text="value"
                                    item-value="id"
                                    label="Статус"
                                    menu-props="auto"
                                    dense
                                  />
                                </v-col>
                                <v-col cols="12" md="2" sm="12" class="mt-1">
                                  <v-text-field
                                    v-model="editedItem.repair_polishing"
                                    label="Расход"
                                    hide-details
                                    dense
                                  />
                                </v-col>
                                <v-col cols="12" md="6" sm="12" class="mt-1">
                                  <v-text-field
                                    v-model="editedItem.polishing_comment"
                                    label="Коментарии"
                                    hide-details
                                    dense
                                  />
                                </v-col>
                                <v-col
                                  cols="12"
                                  md="2"
                                  sm="12"
                                  class="justify-center mt-1"
                                >
                                  Пробег
                                </v-col>
                                <v-col cols="12" md="2" sm="12" class="mt-1">
                                  <v-select
                                    v-model="editedItem.milage_status"
                                    :items="repair_statuses"
                                    hide-details
                                    item-text="value"
                                    item-value="id"
                                    label="Статус"
                                    menu-props="auto"
                                    dense
                                  />
                                </v-col>
                                <v-col cols="12" md="2" sm="12" class="mt-1">
                                  <v-text-field
                                    v-model="editedItem.milage_price"
                                    label="Расход"
                                    hide-details
                                    dense
                                  />
                                </v-col>
                                <v-col cols="12" md="6" sm="12" class="mt-1">
                                  <v-text-field
                                    v-model="editedItem.millage_comment"
                                    label="Коментарии"
                                    hide-details
                                    dense
                                  />
                                </v-col>
                                <v-col
                                  cols="12"
                                  md="2"
                                  sm="12"
                                  class="justify-center mt-1"
                                >
                                  Лобовое
                                </v-col>
                                <v-col cols="12" md="2" sm="12" class="mt-1">
                                  <v-select
                                    v-model="editedItem.windshield_status"
                                    :items="repair_statuses"
                                    hide-details
                                    item-text="value"
                                    item-value="id"
                                    label="Статус"
                                    menu-props="auto"
                                    dense
                                  />
                                </v-col>
                                <v-col cols="12" md="2" sm="12" class="mt-1">
                                  <v-text-field
                                    v-model="editedItem.repair_windshield"
                                    label="Расход"
                                    hide-details
                                    dense
                                  />
                                </v-col>
                                <v-col cols="12" md="6" sm="12" class="mt-1">
                                  <v-text-field
                                    v-model="editedItem.windshield_comment"
                                    label="Коментарии"
                                    hide-details
                                    dense
                                  />
                                </v-col>
                                <v-col
                                  cols="12"
                                  md="2"
                                  sm="12"
                                  class="justify-center mt-1"
                                >
                                  Подготовка
                                </v-col>
                                <v-col cols="12" md="2" sm="12" class="mt-1">
                                  <v-select
                                    v-model="editedItem.preparation_status"
                                    :items="repair_statuses"
                                    hide-details
                                    item-text="value"
                                    item-value="id"
                                    label="Статус"
                                    menu-props="auto"
                                    dense
                                  />
                                </v-col>
                                <v-col cols="12" md="2" sm="6" class="mt-1">
                                  <v-text-field
                                    v-model="editedItem.preparation_price"
                                    label="Расход"
                                    hide-details
                                    dense
                                  />
                                </v-col>

                                <v-col cols="12" md="6" sm="12" class="mt-1">
                                  <v-text-field
                                    v-model="editedItem.preparation_comment"
                                    label="Коментарии"
                                    hide-details
                                    dense
                                  />
                                </v-col>
                              </v-row>
                            </v-col>
                          </v-row>
                        </v-form>
                      </v-container>
                    </v-card-text>

                    <v-card-actions class="mt-n4">
                      <v-btn v-print="printObj" color="primary">
                        <v-icon class="mr-2"> mdi-printer</v-icon>
                        Печать
                      </v-btn>
                      <v-btn
                        @click="loadHistory(editedItem.id)"
                        color="success"
                      >
                        <v-icon class="mr-2"> mdi-retry</v-icon>
                        История изменений
                      </v-btn>

                      <v-spacer />
                      <v-col cols="12" sm="6" md="4" class="mt-n2">
                        <v-select
                          v-if="showroom_id === 1"
                          v-model="editedItem.repair"
                          :items="repairers"
                          item-text="name"
                          item-value="id"
                          label="Передал на ремонт"
                          menu-props="auto"
                          hide-details
                          clearable
                          dense
                          @click:clear="
                            $nextTick(() => (editedItem.repair = null))
                          "
                        />
                      </v-col>

                      <v-tooltip
                        v-if="editedItem.deleted_at === null && canDownload()"
                        bottom
                      >
                        <template
                          v-slot:activator="{ on, attrs }"
                          v-if="canTransit()"
                        >
                          <v-btn
                            v-if="
                              editedItem.transit === 1 &&
                              editedItem.showroom_id === showroom_id
                            "
                            v-bind="attrs"
                            color="error"
                            x-small
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
                            color="warning"
                            v-on="on"
                            @click="approveTransit(editedItem)"
                          >
                            <span key="approve">Принять</span>
                          </v-btn>
                          <v-btn
                            v-else
                            v-bind="attrs"
                            color="success"
                            dark
                            x-small
                            v-on="on"
                            @click="transitItem(editedItem)"
                          >
                            <span key="transit">Транзит</span>
                          </v-btn>
                        </template>
                        <span v-text="transitStatus(editedItem)" />
                      </v-tooltip>
                      <template v-if="editedIndex !== -1">
                        <v-btn
                          :disabled="editedItem.id === ''"
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
                      </template>
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
                      <v-btn v-else color="blue darken-1" text @click="save()">
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
                      <v-icon class="mr-2"> mdi-menu</v-icon>
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
                            @click="openDialog()"
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
                      <v-list-item>
                        <v-list-item-action>
                          <v-switch
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
                      <td></td>
                      <td>
                        <span v-if="editedItem.showroom_id === 2"
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
                      <td></td>
                      <td></td>
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
                        <v-text-field
                          v-model="transit_code"
                          label="Код транзита"
                          outlined
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
                  v-if="transit_code === '404'"
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
          <v-dialog v-model="dialogHistory" max-width="1200px">
            <v-card>
              <v-card-title>
                <span class="headline">История изменений</span>
              </v-card-title>
              <v-card-text>
                <v-container fluid>
                  <v-data-table
                    dense
                    v-if="history_active"
                    :headers="history_headers"
                    :items="histories"
                    :items-per-page="histories.length"
                    item-key="name"
                    class="elevation-1"
                    hide-default-footer
                  >
                    <template #body="{ items }">
                      <tbody>
                        <tr v-for="item in items" :key="item.id">
                          <td style="vertical-align: top">
                            <span class="mt-3">{{
                              $moment(item.created_at).format(
                                'DD.MM.YYYY HH:mm:ss'
                              )
                            }}</span>
                          </td>
                          <td style="vertical-align: top">
                            <span class="mt-3"
                              >{{ item.user?.name }} ({{
                                item.user?.email
                              }})</span
                            >
                          </td>
                          <td style="vertical-align: top">
                            <div v-if="item.description === 'created'">
                              <v-chip
                                class="ma-2"
                                color="success"
                                text-color="white"
                              >
                                Создана
                              </v-chip>
                            </div>
                            <div v-else-if="item.description === 'view'">
                              <v-chip
                                class="ma-2"
                                color="warning"
                                text-color="white"
                              >
                                Просмотр
                              </v-chip>
                            </div>
                            <div v-else-if="item.description === 'updated'">
                              <v-chip
                                class="ma-2"
                                color="success"
                                text-color="white"
                              >
                                Изменена
                              </v-chip>
                            </div>
                            <div v-else-if="item.description === 'deleted'">
                              <v-chip
                                class="ma-2"
                                color="error"
                                text-color="white"
                              >
                                Удалена
                              </v-chip>
                            </div>
                          </td>
                          <td style="vertical-align: top">
                            <table
                              class="activities"
                              width="100%"
                              v-if="
                                item.properties?.attributes &&
                                Array.isArray(item.properties?.attributes) ==
                                  false
                              "
                            >
                              <thead>
                                <tr>
                                  <th>Поле</th>
                                  <th>Было</th>
                                  <th>Стало</th>
                                </tr>
                              </thead>
                              <tbody>
                                <template
                                  v-for="(row, i) in item.properties &&
                                  item.properties.attributes"
                                >
                                  <tr>
                                    <td class="text-center">
                                      {{ activity[i] || i }}
                                    </td>
                                    <td>
                                      <template
                                        v-if="
                                          item.properties.old?.hasOwnProperty(i)
                                        "
                                      >
                                        <template v-if="i == 'status_id'">
                                          {{
                                            statuses.find(
                                              (l) =>
                                                l.id == item.properties.old?.i
                                            )?.name
                                          }}
                                        </template>
                                        <template
                                          v-else-if="i == 'showroom_id'"
                                        >
                                          {{
                                            showrooms.find(
                                              (l) =>
                                                l.id == item.properties.old?.i
                                            )?.name
                                          }}
                                        </template>
                                        <template
                                          v-else-if="
                                            i == 'sts' &&
                                            item.properties.old?.i === null
                                          "
                                        >
                                        </template>
                                        <template v-else-if="i == 'site_id'">
                                          {{
                                            sites.find(
                                              (l) =>
                                                l.id == item.properties.old?.i
                                            )?.title
                                          }}
                                        </template>
                                        <template v-else-if="i == 'type_id'">
                                          {{
                                            types.find(
                                              (l) =>
                                                l.id == item.properties.old?.i
                                            )?.name
                                          }}
                                        </template>
                                        <template
                                          v-else-if="i == 'operator_id'"
                                        >
                                          {{
                                            operators.find(
                                              (l) =>
                                                l.id == item.properties.old?.i
                                            )?.first_name
                                          }}
                                        </template>
                                        <template
                                          v-else-if="i == 'payment_method'"
                                        >
                                          {{
                                            payment_methods.find(
                                              (l) =>
                                                l.id == item.properties.old?.i
                                            )?.name
                                          }}
                                        </template>
                                        <template v-else-if="i == 'region_id'">
                                          {{
                                            regions.find(
                                              (l) =>
                                                l.id == item.properties.old?.i
                                            )?.name
                                          }}
                                        </template>
                                        <template v-else-if="i == 'mark_id'">
                                          {{
                                            marks.find(
                                              (l) =>
                                                l.id == item.properties.old?.i
                                            )?.name
                                          }}
                                        </template>
                                        <template v-else-if="i == 'model_id'">
                                          {{
                                            models.find(
                                              (l) =>
                                                l.id == item.properties.old?.i
                                            )?.name
                                          }}
                                        </template>
                                        <template v-else-if="i === 'approved'">
                                          <span
                                            v-if="item.properties?.old[i] == 1"
                                            >Да</span
                                          >
                                          <span
                                            v-if="item.properties?.old[i] == 0"
                                            >Нет</span
                                          >
                                        </template>

                                        <template v-else-if="i == 'canceled'">
                                          <span
                                            v-if="item.properties.old[i] == 1"
                                            >Да</span
                                          >
                                          <span
                                            v-if="item.properties.old[i] == 0"
                                            >Нет</span
                                          >
                                        </template>
                                        <template
                                          v-else-if="i === 'commercial_offer'"
                                        >
                                          <span
                                            v-if="item.properties.old[i] == 1"
                                            >Да</span
                                          >
                                          <span
                                            v-if="item.properties.old[i] == 0"
                                            >Нет</span
                                          >
                                        </template>
                                        <template v-else-if="i === 'arrived'">
                                          <span
                                            v-if="item.properties.old[i] == 1"
                                            >Да</span
                                          >
                                          <span
                                            v-if="item.properties.old[i] == 0"
                                            >Нет</span
                                          >
                                        </template>
                                        <template
                                          v-else-if="
                                            i === 'callback' &&
                                            item.properties.old[i] !== null
                                          "
                                        >
                                          {{
                                            $moment(
                                              item.properties.old[i]
                                            ).format('DD.MM.YYYY HH:mm:ss')
                                          }}
                                        </template>
                                        <template
                                          v-else-if="
                                            i === 'last_call' &&
                                            item.properties.old[i] !== null
                                          "
                                        >
                                          {{
                                            $moment(
                                              item.properties.old[i]
                                            ).format('DD.MM.YYYY HH:mm:ss')
                                          }}
                                        </template>
                                        <template
                                          v-else-if="
                                            i === 'will_arrive' &&
                                            item.properties.old[i] !== null
                                          "
                                        >
                                          {{
                                            $moment(
                                              item.properties.old[i]
                                            ).format('DD.MM.YYYY')
                                          }}
                                        </template>
                                        <template
                                          v-else-if="
                                            i === 'arrived_date' &&
                                            item.properties.old[i] !== null
                                          "
                                        >
                                          {{
                                            $moment(
                                              item.properties.old[i]
                                            ).format('DD.MM.YYYY')
                                          }}
                                        </template>
                                        <template v-else>
                                          {{ item.properties.old[i] }}
                                        </template>
                                      </template>
                                    </td>
                                    <td>
                                      <template v-if="i == 'status_id'">
                                        {{
                                          statuses.find((l) => l.id === row)
                                            ?.name
                                        }}
                                      </template>
                                      <template v-else-if="i == 'showroom_id'">
                                        {{
                                          showrooms.find((l) => l.id === row)
                                            ?.name
                                        }}
                                      </template>
                                      <template v-else-if="i == 'site_id'">
                                        {{
                                          sites.find((l) => l.id === row)?.title
                                        }}
                                      </template>
                                      <template v-else-if="i == 'type_id'">
                                        {{
                                          types.find((l) => l.id === row)?.name
                                        }}
                                      </template>
                                      <template v-else-if="i == 'operator_id'">
                                        {{
                                          operators.find((l) => l.id === row)
                                            ?.first_name
                                        }}
                                      </template>
                                      <template
                                        v-else-if="i == 'payment_method'"
                                      >
                                        {{
                                          payment_methods.find(
                                            (l) => l.id === row
                                          )?.name
                                        }}
                                      </template>
                                      <template v-else-if="i == 'region_id'">
                                        {{
                                          regions.find((l) => l.id === row)
                                            ?.name
                                        }}
                                      </template>
                                      <template v-else-if="i == 'mark_id'">
                                        {{
                                          marks.find((l) => l.id === row)?.name
                                        }}
                                      </template>
                                      <template
                                        v-else-if="i == 'sts' && row === null"
                                      >
                                      </template>
                                      <template v-else-if="i == 'model_id'">
                                        {{
                                          models.find((l) => l.id === row)
                                            ?.name || row
                                        }}
                                      </template>
                                      <template v-else-if="i === 'approved'">
                                        <span v-if="row === 1">Да</span>
                                        <span v-if="row === 0">Нет</span>
                                      </template>
                                      <template v-else-if="i === 'canceled'">
                                        <span v-if="row === 1">Да</span>
                                        <span v-if="row === 0">Нет</span>
                                      </template>
                                      <template
                                        v-else-if="i === 'commercial_offer'"
                                      >
                                        <span v-if="row === 1">Да</span>
                                        <span v-if="row === 0">Нет</span>
                                      </template>
                                      <template v-else-if="i === 'arrived'">
                                        <span v-if="row === 1">Да</span>
                                        <span v-if="row === 0">Нет</span>
                                      </template>
                                      <template
                                        v-else-if="
                                          i === 'callback' && row !== null
                                        "
                                      >
                                        {{
                                          $moment(row).format(
                                            'DD.MM.YYYY HH:mm:ss'
                                          )
                                        }}
                                      </template>
                                      <template
                                        v-else-if="
                                          i === 'last_call' && row !== null
                                        "
                                      >
                                        {{
                                          $moment(row).format(
                                            'DD.MM.YYYY HH:mm:ss'
                                          )
                                        }}
                                      </template>
                                      <template
                                        v-else-if="
                                          i === 'will_arrive' && row !== null
                                        "
                                      >
                                        {{ $moment(row).format('DD.MM.YYYY') }}
                                      </template>
                                      <template
                                        v-else-if="
                                          i === 'arrived_date' && row !== null
                                        "
                                      >
                                        {{ $moment(row).format('DD.MM.YYYY') }}
                                      </template>
                                      <template v-else>
                                        {{ row }}
                                      </template>
                                    </td>
                                  </tr>
                                </template>
                              </tbody>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                    </template>
                  </v-data-table>
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-btn
                  color="blue darken-1"
                  text
                  @click="dialogHistory = false"
                >
                  Отмена
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
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
import BreadCrumb from '~/components/BreadCrumb'
import { activity } from '~/pages/usedcar/activity.json'
import { saveAs } from 'file-saver'

export default {
  layout: 'user',
  middleware: 'permission',
  components: { BreadCrumb },
  data: () => ({
    activity,
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
    transit_code: null,
    transit_comment: null,
    menu: null,
    dialog: false,
    entity: false,
    dialogTransit: false,
    dialogHistory: false,
    history_active: false,
    valid: false,
    snackbar: false,
    text: '',
    color: '',
    timeout: 6000,
    expanded: [],
    singleExpand: false,
    headers: [
      {
        text: 'Дата прихода',
        value: 'income_date',
        align: 'center',
        sortable: false,
        width: '40px',
      },
      {
        text: 'Авито',
        value: 'auto_ru',
        align: 'center',
        sortable: false,
        width: '30px',
      },
      {
        text: 'Авито цена',
        value: 'price_avito',
        align: 'center',
        sortable: false,
        width: '35px',
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
        text: 'Пробег',
        value: 'milage',
        sortable: false,
        width: '20px',
        align: 'center',
      },
      {
        text: 'Привод',
        value: 'wheeltype.name',
        sortable: false,
        width: '25px',
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
        text: 'Приход',
        value: 'income_price',
        sortable: true,
        width: '30px',
        align: 'center',
      },
      {
        text: 'Продажа',
        value: 'price',
        align: 'center',
        sortable: true,
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
        text: 'На учете',
        value: 'registered',
        width: '10px',
        align: 'center',
        sortable: false,
      },
      {
        text: 'Trade-IN',
        value: 'is_tradein',
        width: '10px',
        align: 'center',
        sortable: false,
      },
      {
        text: '№-ключа',
        value: 'key_number',
        width: '30px',
        align: 'center',
        sortable: false,
      },
      {
        text: 'Расходы',
        value: 'repair_price',
        width: '30px',
        align: 'center',
        sortable: false,
      },
      {
        text: 'Юр.лицо',
        value: 'entity',
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

    history_headers: [
      {
        text: 'Дата и время',
        align: 'start',
        sortable: false,
        value: 'created_at',
      },
      { text: 'Пользователь', value: 'user.first_name' },
      { text: 'Операция', value: 'fat' },
      { text: 'Изменено', value: 'fat' },
    ],
    repairers: [
      { id: 1, name: 'Р' },
      { id: 2, name: 'В' },
    ],
    collapsed: false,
    repair_v: false,
    repair_r: false,
    editedIndex: -1,
    editedItem: {
      id: null,
      showroom_id: null,
      income_date: null,
      mark_id: null,
      model_id: null,
      year: null,
      auto_ru: null,
      bodytype_id: null,
      transmission_id: null,
      enginetype_id: null,
      salon: null,
      wheeltype_id: null,
      owner_count: 1,
      power: null,
      color: null,
      milage: null,
      volume: null,
      vin_number: null,
      number: null,
      sts: null,
      ptc: 1,
      income_price: null,
      price: null,
      price_avito: null,
      key_number: null,
      manager_id: null,
      status_id: null,
      registered: null,
      transit: null,
      is_tradein: null,
      repair_price: null,
      preparation_price: null,
      painting_price: null,
      frontal_price: null,
      milage_price: null,
      transport_price: null,
      removal_price: null,
      comment: null,
      deleted_at: null,
      repair: null,
      entity: null,
      repair_painting: null,
      repair_locksmith: null,
      repair_electric: null,
      repair_others: null,
      repair_dry: null,
      repair_polishing: null,
      repair_windshield: null,
      body_status: null,
      painting_status: null,
      locksmith_status: null,
      electric_status: null,
      others_status: null,
      dry_cleaning_status: null,
      polishing_status: null,
      milage_status: null,
      windshield_status: null,
      preparation_status: null,
      body_comment: null,
      painting_comment: null,
      locksmith_comment: null,
      electric_comment: null,
      others_comment: null,
      dry_cleaning_comment: null,
      polishing_comment: null,
      millage_comment: null,
      windshield_comment: null,
      preparation_comment: null,
    },
    defaultItem: {
      id: null,
      showroom_id: null,
      income_date: null,
      mark_id: null,
      model_id: null,
      year: null,
      auto_ru: null,
      bodytype_id: null,
      transmission_id: null,
      enginetype_id: null,
      salon: null,
      wheeltype_id: null,
      owner_count: 1,
      power: null,
      color: null,
      milage: null,
      volume: null,
      vin_number: null,
      number: null,
      sts: null,
      ptc: 1,
      income_price: null,
      price: null,
      price_avito: null,
      key_number: null,
      manager_id: null,
      status_id: null,
      registered: null,
      transit: null,
      is_tradein: null,
      comment: null,
      repair_price: null,
      preparation_price: null,
      painting_price: null,
      frontal_price: null,
      milage_price: null,
      transport_price: null,
      removal_price: null,
      deleted_at: null,
      repair: null,
      entity: null,
      repair_painting: null,
      repair_locksmith: null,
      repair_electric: null,
      repair_others: null,
      repair_dry: null,
      repair_polishing: null,
      repair_windshield: null,
      body_status: null,
      painting_status: null,
      locksmith_status: null,
      electric_status: null,
      others_status: null,
      dry_cleaning_status: null,
      polishing_status: null,
      milage_status: null,
      windshield_status: null,
      body_comment: null,
      painting_comment: null,
      locksmith_comment: null,
      electric_comment: null,
      others_comment: null,
      dry_cleaning_comment: null,
      polishing_comment: null,
      millage_comment: null,
      windshield_comment: null,
    },
    options: {
      sortBy: ['mark.name', 'model.name', 'income_price'],
      multiSort: false,
    },
    repair_statuses: [
      {
        id: 1,
        value: 'Ремонт',
      },
      {
        id: 2,
        value: 'Готов',
      },
      {
        id: 3,
        value: 'Не требуется',
      },
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
        ? this.$moment(this.editedItem.income_date).format('DD.MM.Y')
        : null
    },
    showroom_id() {
      return Number(this.$route.params.id) || null
    },

    showrooms() {
      return this.$store.state.showroom.showrooms
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
    histories() {
      return this.$store.state.usedcar.histories
    },
    permissions() {
      return this.$store.state.permission.user_permissions
    },
    managers() {
      return this.$store.state.showroom.managers.filter(
        (l) =>
          l.showroom_id === Number(this.$route.params.id) &&
          l.second_manager === null
      )
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
    page_permission() {
      return this.permissions.find(
        (p) => (p.page && p.page.slug) === 'used-cars/' + this.$route.params.id
      )
    },
    links() {
      return [
        {
          text: 'Главная',
          disabled: false,
          href: '/',
        },
        {
          text: 'Автомобили БУ',
          disabled: false,
          href: '/used-cars',
        },
        {
          text: this.showroom.name || '',
          disabled: true,
          href: '/used-cars/' + (this.showroom.id || ''),
        },
      ]
    },
    receivers() {
      return this.showrooms.filter((item) => item.id !== this.showroom_id)
    },
  },
  watch: {
    dialog(val) {
      val || this.close()
    },
    dialogHistory(val) {
      if (val === false) {
        this.history_active = false
      }
    },
    deleted(val) {
      if (val === true) {
        this.$store
          .dispatch('usedcar/fetchCars', {
            showroom_id: this.showroom_id,
            withTrashed: true,
          })
          .catch((error) => {})
      } else {
        this.$store
          .dispatch('usedcar/fetchCars', { showroom_id: this.showroom_id })
          .catch((error) => {})
        this.clearFilter()
      }
    },
  },

  async fetch({ store, error, params: { id } }) {
    await store.dispatch('usedcar/fetchCars', { showroom_id: id })
    await store.dispatch('car/fetchMarks')
    await store.dispatch('showroom/fetchManagers', { showroom_id: id })
    await store.dispatch('car/fetchBodyTypes')
    await store.dispatch('car/fetchEngineTypes')
    await store.dispatch('car/fetchTransmissions')
    await store.dispatch('car/fetchWheelTypes')
    await store.dispatch('showroom/fetchShowroom', { id })
    await store.dispatch('showroom/fetchShowrooms')
    await store.dispatch('usedcar/fetchStatuses')
    await store.dispatch('user/toggle', false)
  },
  mounted() {
    this.$echo
      .channel('used-cars', { showroom_id: this.showroom_id })
      .listen('UsedCarAdded', (e) => {
        console.log('usedcar added')
        this.$store
          .dispatch('usedcar/fetchCars', { showroom_id: this.showroom_id })
          .catch((error) => {
            console.log(error)
            this.reload()
          })
      })
  },
  methods: {
    today() {
      return this.$moment()
    },

    async loadHistory(id) {
      this.dialogHistory = true

      if (!this.history_active) {
        await this.$store.dispatch('usedcar/fetchHistories', {
          id,
        })
      }
      this.history_active = !this.history_active
    },

    seeHistory() {
      const users = ['admin', 'director', 'coordinator', 'custom']
      return users.includes(this.user?.role)
    },

    canDownload() {
      const users = [
        'admin',
        'director',
        'coordinator',
        'coordinator2',
        'coordinator4',
        'coordinator5',
        'coordinator7',
        'coordinator8',
        'coordinator10',
        'coordinator11',
        'manager_acp',
        'manager_light',
        'manager_avrova',
        'manager_avangard',
        'manager_atlant',
        'manager_alarm',
        'manager_autodom',
        'manager_accent',
        'mark',
        'content',
        'custom',
        'petr',
      ]
      return users.includes(this.role?.name)
    },

    canTransit() {
      const users = [
        'admin',
        'director',
        'coordinator',
        'aidar',
        'mark',
        'coordinator7',
        'coordinator2',
        'coordinator4',
        'coordinator5',
        'coordinator8',
        'coordinator10',
        'coordinator11',
      ]
      return users.includes(this.role?.name)
    },
    total_price() {
      return (
        (parseFloat(this.editedItem.painting_price) || 0) +
        (parseFloat(this.editedItem.preparation_price) || 0) +
        (parseFloat(this.editedItem.repair_price) || 0) +
        (parseFloat(this.editedItem.transport_price) || 0) +
        (parseFloat(this.editedItem.milage_price) || 0) +
        (parseFloat(this.editedItem.frontal_price) || 0) +
        (parseFloat(this.editedItem.removal_price) || 0) +
        (parseFloat(this.editedItem.repair_locksmith) || 0) +
        (parseFloat(this.editedItem.repair_electric) || 0) +
        (parseFloat(this.editedItem.repair_others) || 0) +
        (parseFloat(this.editedItem.repair_dry) || 0) +
        (parseFloat(this.editedItem.repair_polishing) || 0) +
        (parseFloat(this.editedItem.repair_windshield) || 0)
      )
    },
    repair_total() {
      return (
        (parseFloat(this.editedItem.repair_price) || 0) +
        (parseFloat(this.editedItem.painting_price) || 0) +
        (parseFloat(this.editedItem.preparation_price) || 0) +
        (parseFloat(this.editedItem.milage_price) || 0) +
        (parseFloat(this.editedItem.frontal_price) || 0) +
        (parseFloat(this.editedItem.repair_locksmith) || 0) +
        (parseFloat(this.editedItem.repair_electric) || 0) +
        (parseFloat(this.editedItem.repair_others) || 0) +
        (parseFloat(this.editedItem.repair_dry) || 0) +
        (parseFloat(this.editedItem.repair_polishing) || 0) +
        (parseFloat(this.editedItem.repair_windshield) || 0)
      )
    },
    total_item_price(item) {
      return (
        (parseFloat(item.painting_price) || 0) +
        (parseFloat(item.preparation_price) || 0) +
        (parseFloat(item.repair_price) || 0) +
        (parseFloat(item.transport_price) || 0) +
        (parseFloat(item.milage_price) || 0) +
        (parseFloat(item.frontal_price) || 0) +
        (parseFloat(item.removal_price) || 0) +
        (parseFloat(item.repair_locksmith) || 0) +
        (parseFloat(item.repair_electric) || 0) +
        (parseFloat(item.repair_others) || 0) +
        (parseFloat(item.repair_dry) || 0) +
        (parseFloat(item.repair_polishing) || 0) +
        (parseFloat(item.repair_windshield) || 0)
      )
    },
    isManagers() {
      const users = [
        'manager',
        'manager1',
        'manager_avrova',
        'petr',
        'mark',
        'content',
      ]
      return users.includes(this.role?.name)
    },
    seePrice() {
      const users = [
        'admin',
        'director',
        'petr',
        'custom',
        'content',
        'natasha',
        'mark',
        'manager_alarm',
        'manager_avrora',
        'manager_accent',
        'manager_autodom',
        'coordinator2',
        'coordinator4',
        'coordinator',
        'coordinator5',
        'coordinator7',
        'coordinator8',
        'coordinator10',
        'coordinator11',
      ]
      return users.includes(this.role?.name)
    },
    parseDate(date) {
      if (!date) {
        return null
      }
      // const [day, month, year] = date.split('.')
      return this.$moment(date).format('YY-MM-dd')
    },
    save() {
      if (this.valid) {
        if (this.editedIndex > -1) {
          this.$store
            .dispatch('usedcar/save', {
              item: this.editedItem,
            })
            .then(() => {
              this.showSnack('success', 'Данные изменены')
            })
            .catch((e) => {
              this.showSnack('error', 'Произошла ошибка: ' + e)
            })
        } else {
          if (this.editedItem.showroom_id === null) {
            this.editedItem.showroom_id = this.$route.params?.id
          }
          this.$store
            .dispatch('usedcar/save', {
              item: this.editedItem,
            })
            .then(() => {
              this.showSnack('success', 'Данные добавлены')
            })
            .catch((e) => {
              this.showSnack('error', 'Произошла ошибка: ' + e)
            })
        }
        this.close()
      } else {
        this.showSnack('error', 'Заполните обязательные поля')
      }
    },

    editItem(item) {
      this.editedIndex = this.usedcars.indexOf(item)
      this.editedItem = Object.assign({}, item)
      console.log(this.editedItem.income_date)
      if (this.editedIndex < 0) {
        this.editedItem.income_date = this.$moment()
      }
      this.entity = this.editedItem.entity
      console.log(this.editedItem.income_date)
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
    openDialog() {
      this.dialog = true
      if (this.editedIndex === -1) {
        this.editedItem.income_date = this.$moment().format('YYYY-MM-DD')
      }
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
        this.repair_v = false
        this.repair_r = false
        this.collapsed = false
        this.entity = false
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
        return 'success  lighten-1 white--text'
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
        return 'success white--text' // can also return multiple classes e.g ["orange","disabled"]
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
      return date !== null &&
        this.$moment().diff(this.$moment(date), 'days') > 90
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
    exportFile() {
      const XLSX = require('xlsx')
      let wb = XLSX.utils.book_new()
      const rows = this.filtered.map((row, index) => ({
        '№': index + 1,
        'Дата прихода': row.income_date,
        'Авто.ру': row.auto_ru,
        Шоурум: row.showroom.name,
        Марка: row.mark?.name,
        Модель: row.model?.name,
        Год: row.year,
        Кузов: row.bodytype?.name,
        'Собств.': row.owner_count,
        Пробег: row.milage,
        Привод: row.wheeltype?.name,
        КПП: row.transmission?.name,
        Объем: row.volume ? row.volume + ' л' : '',
        'Л.С.': row.power,
        Цвет: row.color,
        Vin: row.vin_number,
        'Гос.ном': row.number,
        СТС: row.sts,
        Приход: row.income_price,
        Продажа: row.price,
        'Цена авито': row.price_avito,
        ПТС: this.getStatus(row.ptc),
        'На учёте': row.registered === 1 ? 'Да' : 'Нет',
        '№ ключа': row.key_number,
        Статус: row.status?.status,
        Менеджер: row.manager,
        Коментарии: row.comment,
      }))
      let ws = XLSX.utils.json_to_sheet(rows)
      ws['!cols'] = [
        { wch: 3 },
        { wch: 10 },
        { wch: 10 },
        { wch: 9 },
        { wch: 12 },
        { wch: 14 },
        { wch: 7 },
        { wch: 12 },
        { wch: 7 },
        { wch: 7 },
        { wch: 6 },
        { wch: 5 },
        { wch: 5 },
        { wch: 5 },
        { wch: 16 },
        { wch: 19 },
        { wch: 11 },
        { wch: 13 },
        { wch: 8 },
        { wch: 13 },
        { wch: 13 },
        { wch: 11 },
        { wch: 7 },
        { wch: 15 },
        { wch: 13 },
        { wch: 12 },
        { wch: 50 },
      ]
      XLSX.utils.book_append_sheet(wb, ws, 'БУ авто - ' + this.showroom?.name)
      let wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' })
      saveAs(
        new Blob([wbout], { type: 'application/octet-stream' }),
        'Список_БУ_машин_' + this.showroom?.name + '.xlsx'
      )
    },
    getStatus(value) {
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
    hasAccess() {
      const roles = [
        'admin',
        'director',
        'coordinator',
        'coordinator',
        'coordinator2',
        'coordinator4',
        'coordinator5',
        'coordinator7',
        'coordinator8',
        'coordinator10',
        'coordinator11',
        'manager_alarm',
      ]
      return !roles.includes(this.user?.role) || this.entity?.length <= 0
    },
  },
  beforeRouteLeave(to, from, next) {
    this.$store.dispatch('user/toggle', true)
    next()
  },
}
</script>

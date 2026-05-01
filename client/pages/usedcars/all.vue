<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links" />
      <v-row no-gutters align="start" class="d-flex">
        <v-col cols="12">
          <v-data-table
            :key="componentKey"
            dense
            :headers="headers"
            :items="filtered"
            :options="options"
            :items-per-page="filtered.length"
            item-key="id"
            hide-default-footer
            class="elevation-1 mytable grey lighten-4"
            height="80vh"
            fixed-header
            :mobile-breakpoint="0"
          >
            <template #header.income_date="{ header }">
              <span class="vertical"
                >Дата <br />
                прихода</span
              >
            </template>
            <template #header.owner_count="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.volume="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.milage="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.showroom.name="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.bodytype.name="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.year="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.wheeltype.name="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.auto_ru="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.transmission.name="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.ptc="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.income_price="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.price="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.enginetype.name="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.salon="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.color="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.vin_number="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.number="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.sts="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #header.registered="{ header }">
              <span class="vertical">На <br />учете</span>
            </template>
            <template #header.key_number="{ header }">
              <span class="vertical"
                >Номер <br />
                ключа</span
              >
            </template>
            <template #header.status.status="{ header }">
              <span class="vertical">{{ header.text }}</span>
            </template>
            <template #body="{ items }">
              <tbody>
                <tr
                  v-for="item in items"
                  :key="item.name"
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
                    {{ item.avito_price }}
                  </td>
                  <td>
                    <v-chip
                      v-if="item.showroom && item.showroom.id % 2 !== 1"
                      color="primary"
                      x-small
                    >
                      {{ item.showroom && item.showroom.name }}
                    </v-chip>
                    <v-chip v-else color="green" text-color="white" x-small>
                      {{ item.showroom && item.showroom.name }}
                    </v-chip>
                  </td>
                  <td>{{ item.mark && item.mark.name }}</td>
                  <td>{{ item.model && item.model.name }}</td>
                  <td>{{ item.year }}</td>
                  <td>{{ item.bodytype && item.bodytype.name }}</td>
                  <td>{{ item.owner_count }}</td>
                  <td>{{ item.milage }}</td>
                  <td>{{ item.wheeltype && item.wheeltype.name }}</td>
                  <td>{{ item.transmission && item.transmission.name }}</td>
                  <td>{{ item.volume }} ({{ item.power }} ЛС)</td>
                  <td>{{ item.enginetype && item.enginetype.name }}</td>
                  <td>{{ item.salon }}</td>
                  <td>{{ item.color }}</td>
                  <td>{{ item.vin_number }}</td>
                  <td>{{ item.number }}</td>
                  <td>{{ item.sts }}</td>
                  <td>{{ item.income_price + total_item_price(item) }}</td>
                  <td>{{ item.price }}</td>

                  <td>
                    <span v-if="item.ptc === 1">ПТС</span>
                    <span v-else-if="item.ptc === 2">Дубликат</span>
                    <span v-else-if="item.ptc === 3">ЭПТС</span>
                    <span v-else>Нет</span>
                  </td>
                  <td>
                    <span v-if="item.registered === 1" key="da">Да</span>
                    <span v-else-if="item.registered === 0" key="net">Нет</span>
                  </td>
                  <td>{{ item.key_number }}</td>
                  <td>{{ total_item_price(item) }}</td>
                  <td>
                    <v-chip
                      v-if="item.status && item.status.status !== null"
                      :text-color="item.status.text_color"
                      :color="item.status.color"
                      x-small
                    >
                      {{ item.status.status }}
                    </v-chip>
                  </td>
                </tr>
              </tbody>
            </template>
            <template #top>
              <v-toolbar flat dense>
                <v-row class="mt-8 hidden-sm-and-down">
                  <v-col cols="3" md="5" xs="6">
                    <v-row>
                      <v-col cols="3" md="3" xs="6">
                        <v-select
                          v-model="filter.showroom_id"
                          :items="showrooms"
                          dense
                          item-text="name"
                          item-value="id"
                          label="Салон"
                          outlined
                          clearable
                          multiple
                          persistent-hint
                          @click:clear="
                            $nextTick(() => (filter.showroom_id = []))
                          "
                        >
                          <template #selection="{ item, index }">
                            <template v-if="index === 0">
                              <span>{{ item && item.name }}</span>
                            </template>
                            <span
                              v-if="index === 1"
                              class="grey--text text-caption"
                            >
                              &nbsp;(+{{ filter.showroom_id.length - 1 }})
                            </span>
                          </template>
                        </v-select>
                      </v-col>
                      <v-col cols="3" md="2" xs="6">
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
                      <v-col cols="3" md="2" xs="6">
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
                          label="КПП"
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
                          :items="range(2022, 1960)"
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
                          :items="range(2022, 1960)"
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
                            <v-col cols="12" sm="4" md="4">
                              <v-select
                                v-model="editedItem.mark_id"
                                :items="marks"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                                label="Марка"
                                hide-details
                                :rules="[(v) => !!v || 'Выберите марку']"
                                required
                                @change="getModels(editedItem.mark_id)"
                              />
                            </v-col>
                            <v-col cols="12" sm="4" md="4">
                              <v-select
                                v-model="editedItem.model_id"
                                :items="models"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                                label="Модель"
                                hide-details
                                :rules="[(v) => !!v || 'Выберите модель']"
                                required
                              />
                            </v-col>
                            <v-col cols="12" sm="4" md="4">
                              <v-select
                                v-model="editedItem.showroom_id"
                                :items="[
                                  { id: 1, name: 'Урус' },
                                  { id: 2, name: 'Лайт' },
                                  { id: 3, name: 'Марьино' },
                                ]"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                                label="Шоурум"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-select
                                v-model="editedItem.year"
                                :items="range(2022, 1960)"
                                menu-props="auto"
                                label="Год выпуска"
                                :rules="[(v) => !!v || 'Выберите год выпуска']"
                                hide-details
                                required
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-menu
                                v-model="menu1"
                                :close-on-content-click="false"
                                max-width="290"
                              >
                                <template #activator="{ on, attrs }">
                                  <v-text-field
                                    :value="dateFormatted"
                                    clearable
                                    label="Приход"
                                    readonly
                                    v-bind="attrs"
                                    hide-details
                                    @blur="date = $parseDate(dateFormatted)"
                                    v-on="on"
                                    @click:clear="editedItem.income_date = null"
                                  />
                                </template>
                                <v-date-picker
                                  v-model="editedItem.income_date"
                                  locale="ru-RU"
                                  hide-details
                                  @change="menu1 = false"
                                />
                              </v-menu>
                            </v-col>
                            <v-col cols="12" sm="6" md="4">
                              <v-text-field
                                v-model="editedItem.auto_ru"
                                label="Ссылка Авто.ру"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" sm="4" md="2">
                              <v-select
                                v-model="editedItem.bodytype_id"
                                :items="bodytypes"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                                label="Кузов"
                                :rules="[(v) => !!v || 'Выберите тип кузова']"
                                hide-details
                                required
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="4">
                              <v-text-field
                                v-model="editedItem.owner_count"
                                label="Собственик"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="4">
                              <v-text-field
                                v-model="editedItem.milage"
                                type="number"
                                label="Пробег"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="4">
                              <v-select
                                v-model="editedItem.wheeltype_id"
                                :items="wheeltypes"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                                label="Привод"
                                hide-details
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
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-select
                                v-model="editedItem.volume"
                                :items="range(0.8, 5, 0.1)"
                                menu-props="auto"
                                label="Объем"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-text-field
                                v-model="editedItem.power"
                                type="number"
                                label="Л.С."
                                hide-details
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
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-text-field
                                v-model="editedItem.salon"
                                label="Тип салона"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" sm="4" md="3">
                              <v-text-field
                                v-model="editedItem.color"
                                label="Цвет"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-text-field
                                v-model="editedItem.vin_number"
                                label="Вин"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-text-field
                                v-model="editedItem.number"
                                label="Гос. номер"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="2">
                              <v-text-field
                                v-model="editedItem.sts"
                                label="СТС"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="2">
                              <v-text-field
                                v-model="editedItem.income_price"
                                type="number"
                                label="Заход"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="6">
                              <v-text-field
                                label="Заход с расходами"
                                type="number"
                                :value="
                                  total_price() +
                                  parseInt(editedItem.income_price || 0)
                                "
                                hide-details
                                readonly
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-text-field
                                v-model="editedItem.price"
                                label="Продажа"
                                hide-details
                                @keypress.native="isNumber($event)"
                              />
                            </v-col>
                            <v-col cols="12" md="3" sm="6">
                              <v-text-field
                                v-model="editedItem.avito_price"
                                label="Цена авито"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="6" class="mx-0">
                              <v-text-field
                                v-model="editedItem.preparation_price"
                                append-icon="mdi-currency-usd"
                                label="Подготовка"
                                hide-details
                                class="mx-0"
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="6" class="mx-0">
                              <v-text-field
                                v-model="editedItem.painting_price"
                                append-icon="mdi-currency-usd"
                                label="Покраска"
                                hide-details
                                class="mx-0"
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="6" class="mx-0">
                              <v-text-field
                                v-model="editedItem.repair_price"
                                append-icon="mdi-currency-usd"
                                label="Ремонт"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="6" class="mx-0">
                              <v-text-field
                                v-model="editedItem.milage_price"
                                append-icon="mdi-currency-usd"
                                label="Пробег"
                                hide-details
                              />
                            </v-col>

                            <v-col cols="12" md="2" sm="6" class="mx-0">
                              <v-text-field
                                v-model="editedItem.frontal_price"
                                append-icon="mdi-currency-usd"
                                label="Лобовое"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="6" class="mx-0">
                              <v-text-field
                                append-icon="mdi-currency-usd"
                                :value="total_price()"
                                label="Общий"
                                readonly
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="2">
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
                                menu-props="auto"
                                label="ПТС"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="2">
                              <v-text-field
                                v-model="editedItem.key_number"
                                label="№ ключа"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-text-field
                                v-model="editedItem.manager"
                                label="Менеджер"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                              <v-select
                                v-model="editedItem.status_id"
                                :items="statuses"
                                item-text="status"
                                item-value="id"
                                menu-props="auto"
                                label="Статус"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" sm="6" md="2">
                              <v-select
                                v-model="editedItem.registered"
                                :items="[
                                  { id: null, name: ' ' },
                                  { id: 1, name: 'Да' },
                                  { id: 0, name: 'Нет' },
                                ]"
                                label="На учете"
                                item-text="name"
                                item-value="id"
                                menu-props="auto"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" sm="12" md="12">
                              <v-text-field
                                v-model="editedItem.comment"
                                label="Коментарии"
                                hide-details
                              />
                            </v-col>
                          </v-row>
                        </v-form>
                      </v-container>
                    </v-card-text>

                    <v-card-actions class="mt-n4">
                      <v-btn v-print="printObj" color="primary">
                        <v-icon class="mr-2"> mdi-printer </v-icon> Печать
                      </v-btn>
                      <v-spacer />
                      <v-chip v-if="editedItem.user !== null" color="error">
                        Удалил
                        {{ editedItem.user && editedItem.user.name }}</v-chip
                      >
                      <v-btn
                        color="error darken-1"
                        :disabled="editedItem.id === ''"
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
                  <template #activator="{ on, attrs }">
                    <v-btn
                      color="indigo"
                      dark
                      v-bind="attrs"
                      class="mt-2 ml-2"
                      v-on="on"
                    >
                      <v-icon class="mr-2"> mdi-menu </v-icon> Меню
                    </v-btn>
                  </template>

                  <v-card>
                    <v-list>
                      <v-list-item>
                        <v-list-item-action>
                          <v-btn
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
                        <span v-if="editedItem.showroom_id === 1">Урус </span>
                        <span v-else-if="editedItem.showroom_id === 2"
                          >Лайт</span
                        >
                        <span v-else-if="editedItem.showroom_id === 3"
                          >Марьино</span
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
<script>
import BreadCrumb from '~/components/BreadCrumb'
export default {
  components: { BreadCrumb },
  beforeRouteLeave(to, from, next) {
    this.$store.dispatch('user/toggle', true)
    next()
  },
  layout: 'user',
  middleware: 'permission',
  data: () => ({
    menu1: null,
    componentKey: 0,
    filter: {
      mark_id: null,
      model_id: null,
      transmission_id: null,
      showroom_id: [],
      wheeltype_id: null,
      year_from: null,
      year_to: null,
      milage_from: null,
      milage_to: null,
      incomePriceFrom: null,
      incomePriceTo: null,
    },
    deleted: false,
    menu: null,
    dialog: false,
    valid: false,
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
        text: 'Авто.ру',
        value: 'auto_ru',
        align: 'center',
        sortable: false,
        width: '30px',
      },
      {
        text: 'Авито цена',
        value: 'avito_price',
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
      { text: 'Модель', value: 'model.name', width: '110px', align: 'center' },
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
        width: '30px',
        align: 'center',
      },
      {
        text: 'Продажа',
        value: 'price',
        align: 'center',
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
      showroom_id: null,
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
      milage: '',
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
      repair_price: '',
      preparation_price: '',
      painting_price: '',
      frontal_price: '',
      milage_price: '',
      comment: null,
      deleted_at: null,
    },
    defaultItem: {
      id: '',
      showroom_id: null,
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
      milage: '',
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
      repair_price: '',
      preparation_price: '',
      painting_price: '',
      frontal_price: '',
      milage_price: '',
      comment: null,
      deleted_at: null,
    },
    options: {
      sortBy: ['mark.name', 'model.name', 'income_price', 'price'],
      multiSort: false,
    },
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
  async fetch({ store }) {
    await store.dispatch('property/fetchMarks')
    await store.dispatch('showroom/fetchShowrooms')
    await store.dispatch('property/fetchBodyTypes')
    await store.dispatch('property/fetchEngineTypes')
    await store.dispatch('property/fetchTransmissions')
    await store.dispatch('property/fetchWheelTypes')
    await store.dispatch('usedcar/fetchCars', {})
    await store.dispatch('property/fetchStatuses')
    await store.dispatch('user/toggle', false)
  },

  computed: {
    dateFormatted() {
      return this.editedItem.income_date
        ? this.$moment(this.editedItem.income_date).format('DD.MM.Y')
        : null
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

      if (this.filter.showroom_id.length > 0) {
        cars = cars.filter((item) =>
          this.filter.showroom_id.includes(item.showroom_id)
        )
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
    statuses() {
      return this.$store.state.property.statuses
    },
    marks() {
      return this.$store.state.property.marks
    },
    models() {
      return this.$store.state.property.models
    },
    enginetypes() {
      return this.$store.state.property.enginetypes
    },
    bodytypes() {
      return this.$store.state.property.bodytypes
    },
    transmissions() {
      return this.$store.state.property.transmissions
    },
    wheeltypes() {
      return this.$store.state.property.wheeltypes
    },
    role() {
      return this.$store.state.auth.role
    },
    showrooms() {
      return this.$store.state.showroom.showrooms
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
          text: 'Все салоны',
          disabled: true,
          href: '/used-cars/all',
        },
      ]
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
  mounted() {
    this.$echo.channel('used-cars').listen('UsedCarAdded', (e) => {
      console.log('usedcar added')
      this.$store.dispatch('usedcar/fetchCars').catch((error) => {
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
        'manager_acp',
        'manager_light',
      ]
      return users.includes(this.role && this.role.name)
    },

    total_price() {
      return (
        (parseFloat(this.editedItem.painting_price) || 0) +
        (parseFloat(this.editedItem.preparation_price) || 0) +
        (parseFloat(this.editedItem.repair_price) || 0) +
        (parseFloat(this.editedItem.milage_price) || 0) +
        (parseFloat(this.editedItem.frontal_price) || 0)
      )
    },
    total_item_price(item) {
      return (
        (parseFloat(item.painting_price) || 0) +
        (parseFloat(item.preparation_price) || 0) +
        (parseFloat(item.repair_price) || 0) +
        (parseFloat(item.milage_price) || 0) +
        (parseFloat(item.frontal_price) || 0)
      )
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
    save() {
      if (this.valid) {
        if (this.editedIndex > -1) {
          this.$store.dispatch('usedcar/save', { item: this.editedItem })
          this.$store.dispatch('usedcar/save', { item: this.editedItem })
          this.$toast.success('Данные изменены')
        } else {
          this.$store.dispatch('usedcar/save', { item: this.editedItem })
          this.$toast.success('Данные добавлены')
        }
        this.close()
      } else {
        this.$toast.error('Заполните обязательные поля')
      }
    },
    editItem(item) {
      this.editedIndex = this.usedcars.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
      if (item.mark_id !== 0) {
        this.$store.dispatch('property/fetchModels', { markId: item.mark_id })
      }
    },
    deleteItem(id) {
      confirm('Вы уверены, что хотите удалить этот запись?') &&
        this.$store
          .dispatch('usedcar/delete', { id })
          .then(() => {
            this.$toast.success('Данные удалены')
          })
          .catch((error) => {
            this.$toast.error('Ошибка: ' + error)
          })
      this.dialog = false
    },
    transitItem(item) {
      confirm('Вы уверены, что хотите переводить этот авто в другой салон?') &&
        this.$store
          .dispatch('usedcar/approveTransit', { item })
          .then(() => {
            this.$toast.success('Автомобиль переведён')
          })
          .catch((error) => {
            this.$toast.error('Ошибка: ' + error)
          })
    },
    close() {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
        this.$store.dispatch('car/emptyModels')
        this.clicks = 0
        this.componentKey += 1
      })
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
      this.filter.showroom_id = []
      this.$store.dispatch('car/emptyModels')
    },
    validate() {
      this.$refs.form.validate()
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
    row_classes(item) {
      if (item.status_id === 1) {
        return 'success lighten-1 white--text'
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
    getModels(markId = null) {
      this.editedItem.model_id = []
      if (markId !== 0) {
        this.$store.dispatch('property/fetchModels', { markId })
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
  },
}
</script>
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
  padding: 0 2px !important;
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
  padding: 0 2px;
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
  padding: 0 2px;
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

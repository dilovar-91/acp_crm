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
            <v-app-bar v-if="isFilter" class="" elevate-on-scroll dense>
              <v-row class="ml-1 mt-2">
                <v-col cols="12" sm="6" md="1" class="hidden-sm-and-down">
                  <date-picker
                    v-model="filter_from"
                    value-type="YYYY-MM-DD HH:mm"
                    format="DD.MM.Y HH:mm"
                    type="datetime"
                    placeholder="Дата с"
                    style="width: 100%; margin-top: 4px"
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
                  <v-text-field
                    v-model="search"
                    clearable
                    label="Поиск"
                    hide-details
                    outlined
                    dense
                    @keyup.enter="doSearch()"
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
                  <v-autocomplete
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
                    <template #selection="{ item, index }">
                      <template v-if="index === 0">
                        <span>{{ $truncate(item.title, 10) }}</span>
                      </template>
                      <span v-if="index === 1" class="grey--text text-caption">
                        &nbsp;(+{{ filter_site.length - 1 }})
                      </span>
                    </template>
                  </v-autocomplete>
                </v-col>
                <v-col
                  cols="12"
                  sm="6"
                  xl="1"
                  md="1"
                  class="hidden-sm-and-down mt-1"
                >
                  <v-select
                    v-model="filter_type"
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
                      <span v-if="index === 1" class="grey--text text-caption">
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
                  xl="1"
                  md="1"
                  class="hidden-sm-and-down mt-1"
                >
                  <v-checkbox
                    v-model="advanced_arrived"
                    label="Приезд потв."
                    outlined
                    dense
                    :size="'small'"
                  />
                </v-col>
                <v-col
                  cols="12"
                  sm="6"
                  xl="1"
                  md="1"
                  class="hidden-sm-and-down mt-1"
                >
                  <v-checkbox
                    v-model="not_confirmed"
                    label="Не потв."
                    outlined
                    dense
                    :size="'small'"
                  />
                </v-col>

                <v-col cols="12" sm="1" md="1" class="hidden-sm-and-down">
                  <v-btn
                    small
                    color="success"
                    dark
                    class="mb-2 mt-2"
                    @click="doSearch()"
                  >
                    Применить
                  </v-btn>
                </v-col>
                <v-col cols="12" sm="1" md="1" class="hidden-sm-and-down">
                  <v-btn
                    small
                    color="error"
                    dark
                    class="mb-2 mt-2"
                    @click="clearFilter()"
                  >
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
                    <v-icon class="mr-2"> mdi-menu </v-icon>
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
                    <v-list-item>
                      <v-list-item-action>
                        <v-btn
                          color="primary"
                          dark
                          class="mb-2"
                          @click="openDialogArrive()"
                        >
                          Скачать заявки приедет
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
                        xl="1"
                        md="1"
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
                        <v-text-field
                          v-model="advanced_fio"
                          clearable
                          label="Поиск по ФИО или Комментариям"
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
                        xl="1"
                        md="1"
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
                        :md="getColumnCount()"
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
                      <v-col
                        v-if="advanced_status === 7"
                        cols="12"
                        sm="6"
                        xl="1"
                        md="1"
                        class="hidden-sm-and-down mt-1"
                      >
                        <v-select
                          v-model="advanced_trash"
                          :items="trashes"
                          hide-details
                          item-text="name"
                          item-value="id"
                          label="Корзина"
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
                    </v-row>
                  </v-col>
                </v-row>

                <v-spacer />
                <v-btn v-if="false" icon color="green" @click="exportFile()">
                  <v-icon large>mdi-file-excel</v-icon>
                </v-btn>
                <v-menu
                  v-if="advanced_status !== 7"
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
                            v-model="advanced_from"
                            format="DD.MM.Y"
                            placeholder="Дата с"
                          />
                        </v-list-item-action>
                      </v-list-item>
                      <v-list-item class="hidden-md-and-up">
                        <v-list-item-action>
                          <date-picker
                            v-model="advanced_to"
                            format="DD.MM.Y"
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
              <v-app-bar class="indigo lighten-5" elevate-on-scroll dense>
                <v-row class="ml-3 mt-2 pb-2">
                  <v-col cols="12" sm="6" md="10">
                    <v-row>
                      <v-col
                        cols="12"
                        sm="5"
                        xl="5"
                        md="5"
                        class="hidden-sm-and-down mt-1"
                      >
                        <v-row>
                          <v-col
                            v-if="role_id !== 2"
                            cols="12"
                            sm="6"
                            xl="3"
                            md="3"
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
                            xl="3"
                            md="3"
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
                            xl="3"
                            md="3"
                            class="hidden-sm-and-down mt-1"
                          >
                            <v-select
                              v-model="advanced_payment"
                              :items="payment_methods"
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
                                $nextTick(() => (advanced_payment = null))
                              "
                            />
                          </v-col>

                          <v-col
                            cols="12"
                            sm="6"
                            xl="3"
                            md="3"
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
                              dense
                              @change="getAdvancedModels(advanced_mark)"
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
                        </v-row>
                      </v-col>

                      <v-col
                        cols="12"
                        sm="7"
                        xl="7"
                        md="7"
                        class="hidden-sm-and-down mt-1"
                      >
                        <v-row>
                          <v-col
                            cols="12"
                            sm="6"
                            xl="2"
                            md="2"
                            class="hidden-sm-and-down mt-1"
                          >
                            <v-select
                              v-model="advanced_model"
                              :items="advanced_models"
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
                            cols="12"
                            sm="6"
                            xl="2"
                            md="2"
                            class="hidden-sm-and-down mt-1"
                          >
                            <v-select
                              v-model="advanced_region"
                              :items="regions"
                              hide-details
                              item-text="name"
                              item-value="id"
                              label="Регион"
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
                              v-model="advanced_phone"
                              clearable
                              label="Телефон"
                              hide-details
                              outlined
                              dense
                              @keyup.enter="doSearch()"
                            >
                            </v-text-field>
                          </v-col>

                          <v-col
                            cols="12"
                            sm="6"
                            xl="1"
                            md="1"
                            class="hidden-sm-and-down mt-1"
                          >
                            <v-checkbox
                              v-model="advanced_arrived"
                              label="Приезд потв."
                              outlined
                              dense
                              :size="'small'"
                            />
                          </v-col>
                          <v-col
                            cols="12"
                            sm="6"
                            xl="2"
                            md="2"
                            class="hidden-sm-and-down mt-1 ml-4"
                          >
                            <v-checkbox
                              v-model="not_confirmed"
                              label="Приезд не потв."
                              outlined
                              dense
                              :size="'small'"
                            />
                          </v-col>

                          <v-col
                            cols="12"
                            sm="6"
                            xl="1"
                            md="1"
                            class="hidden-sm-and-down mt-1 ml-5 mr-4"
                          >
                            <v-btn
                              color="success"
                              dark
                              class="mb-2 mt-1 pr-2"
                              @click="doAdvancedSearch()"
                            >
                              Применить
                            </v-btn>
                          </v-col>
                          <v-col
                            cols="12"
                            sm="6"
                            xl="1"
                            md="1"
                            class="hidden-sm-and-down mt-1 mr-1"
                          >
                            <v-btn
                              class="mb-2 mt-1 ml-10"
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
                <template #header.retries="">
                  <div @click="sortRepeat()">Повторы</div>
                </template>
                <template #body="{ items }">
                  <tbody>
                    <tr
                      v-for="item in items"
                      :key="item.id"
                      :class="row_classes(item)"
                      @dblclick="editItem(item)"
                    >
                      <td>
                        <nuxt-link
                          :to="
                            '/crm/' +
                            item.showroom_id +
                            '/order/' +
                            item.id +
                            '/edit'
                          "
                          :class="row_classes(item)"
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
                              Передано с
                              {{
                                showrooms.find(
                                  (sh) => sh.id === item.copied_from
                                )?.name
                              }}
                            </v-chip>
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

                          <v-chip
                            v-if="item?.defer_purchase"
                            class="ma-2"
                            small
                            color="warning"
                          >
                            Отложенная покупка ({{
                              $moment(item?.defer_purchase?.return_date).format(
                                'DD.MM.YYYY'
                              )
                            }})
                          </v-chip>
                        </nuxt-link>

                        <template v-if="item.copied_to">
                          <v-chip
                            v-for="(salon, y) in item.copied_to"
                            :key="'showroom__' + y"
                            dark
                            class="mr-1 mb-1"
                            :color="colors[salon]"
                          >
                            Передано в
                            {{ showrooms.find((l) => l.id === salon)?.name }}
                          </v-chip>
                        </template>

                        <template
                          v-if="item.status_id === 15 && $auth.user?.id === 5"
                        >
                          <v-btn small color="error" @click="onDelete(item.id)">
                            Удалить
                          </v-btn>
                        </template>
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
                      <td>
                        {{ item.operator?.last_name }}
                        {{ item.operator?.first_name }}
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

                      <td>{{ item.price | currency }}</td>
                      <td>{{ item.client_name }}</td>
                      <td>{{ item.phone | mask('+7 ### ###-##-##') }}</td>
                      <td>
                        {{
                          item.will_arrive
                            ? $moment(item.will_arrive).format('DD.MM.YYYY')
                            : ''
                        }}
                      </td>
                      <td>
                        <a
                          class="font-weight-bold"
                          @click.prevent="openRepeat(item)"
                          >{{ item.retries }}</a
                        >
                      </td>
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
                        <span v-if="item.approved === 1">Да</span>
                        <span v-else>Нет</span>
                      </td>
                      <td>
                        {{
                          payment_methods.find(
                            (k) => k.id === item.payment_method
                          )?.name
                        }}
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

                <v-row dense justify="center">
                  <v-pagination
                    v-model="page"
                    color="blue"
                    :length="pageCount"
                    :total-visible="10"
                    @input="changedPage"
                  />
                </v-row>

                <v-row dense justify="center">
                  <v-col cols="12" md="3">
                    <v-select
                      v-model="limit"
                      :items="per_pages"
                      hide-details
                      label="Показать"
                      style="width: 100%"
                      menu-props="auto"
                      outlined
                      clearable
                      required
                      dense
                      @input="changeLimit"
                      @click:clear="$nextTick(() => (limit = null))"
                    >
                    </v-select>
                  </v-col>
                </v-row>
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-dialog v-model="dialog" max-width="1000">
          <v-card>
            <v-card-text>
              <v-container>
                <template v-if="false">
                  <p>{{ last_call }} --- {{ oldLastCall }}</p>
                  <p>{{ callback }} --- {{ oldCallback }}</p>
                  <p>{{ editedItem.will_arrive }} --- {{ oldWillArrive }}</p>
                  <p>{{ editedItem.arrived_date }} --- {{ oldArrivedDate }}</p>
                </template>
                <v-form ref="form" v-model="valid">
                  <v-row dense>
                    <v-col cols="12" sm="6" md="5">
                      <v-text-field
                        v-model="editedItem.client_name"
                        label="Клиент"
                        outlined
                        :rules="[(v) => !!v || 'Введитие имя клиента']"
                        dense
                        required
                        hide-details
                        append-icon="mdi-content-copy"
                        @click:append="copyName()"
                      />
                    </v-col>

                    <v-col cols="12" sm="6" md="4">
                      <v-text-field
                        v-model="editedItem.phone"
                        v-mask="'+7 ### ###-##-##'"
                        label="Сотовый"
                        outlined
                        dense
                        :rules="[(v) => !!v || 'Введите сотового']"
                        required
                        hide-details
                      >
                        <template
                          v-if="$auth.user?.work_place > 0"
                          slot="append"
                        >
                          <v-icon color="primary" @click="call()"
                            >mdi-phone
                          </v-icon>
                          <v-icon color="primary" @click="redirectToWhatsApp"
                            >mdi-whatsapp
                          </v-icon>
                          <v-icon
                            class="ml-2"
                            color="primary"
                            @click="copyPhone(editedItem?.phone)"
                            >mdi-content-copy
                          </v-icon>

                          <v-icon
                            class="ml-2"
                            color="primary"
                            @click="openDialogSms(editedItem)"
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
                        @change="changedType()"
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
                        :item-text="
                          (item) =>
                            item.last_name
                              ? item.first_name +
                                ' ' +
                                item.last_name +
                                ' (' +
                                item?.work_place +
                                ')'
                              : item.first_name + ' (' + item?.work_place + ')'
                        "
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Оператор"
                        hide-details
                        :disabled="role_id === 2"
                        outlined
                        dense
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="2">
                      <v-autocomplete
                        v-model="editedItem.mark_id"
                        :items="marks"
                        item-text="name"
                        item-value="id"
                        no-data-text="Нету данных"
                        menu-props="auto"
                        label="Марка"
                        hide-details
                        dense
                        outlined
                        clearable
                        @change="getModels(editedItem.mark_id)"
                        @click:clear="
                          $nextTick(() => (editedItem.mark_id = null))
                        "
                      />
                    </v-col>

                    <v-col cols="12" sm="6" md="2">
                      <v-autocomplete
                        :key="'model-' + editedItem.mark_id"
                        v-model="editedItem.model_id"
                        :items="models"
                        item-text="name"
                        item-value="id"
                        no-data-text="Нету данных"
                        menu-props="auto"
                        label="Модель"
                        hide-details
                        outlined
                        dense
                        :disabled="!editedItem.mark_id"
                        @click:clear="
                          $nextTick(() => (editedItem.model_id = null))
                        "
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
                        :items="payment_methods"
                        item-text="name"
                        no-data-text="Нету данных"
                        item-value="id"
                        :rules="[validatePayment]"
                        menu-props="auto"
                        label="Способ оплаты"
                        outlined
                        hide-details
                        dense
                        clearable
                        @change="resetValidation"
                        @click:clear="
                          $nextTick(() => (editedItem.payment_method = null))
                        "
                      />
                    </v-col>
                    <v-col
                      v-if="
                        editedItem.status_id !== 7 && editedItem.status_id !== 6
                      "
                      cols="12"
                      sm="6"
                      md="2"
                    >
                      <v-text-field
                        v-model="editedItem.entry_point"
                        label="Точка входа"
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
                    <v-col
                      cols="12"
                      sm="6"
                      :md="
                        editedItem.status_id === 6 || editedItem.status_id === 7
                          ? 2
                          : 3
                      "
                    >
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
                        :rules="[(v) => !!v || 'Выберите статус']"
                        required
                        dense
                        @change="changedStatus()"
                        @click:clear="
                          $nextTick(() => (editedItem.status_id = null))
                        "
                      />
                    </v-col>
                    <v-col
                      v-if="editedItem.status_id === 7"
                      cols="12"
                      sm="12"
                      xl="3"
                      md="3"
                      class="py-0 mb-2"
                    >
                      <v-select
                        v-model="editedItem.trash_id"
                        :items="trashes"
                        item-text="name"
                        item-value="id"
                        label="Тип корзины"
                        dense
                        outlined
                        hide-details="auto"
                      />
                    </v-col>
                    <v-col
                      v-if="
                        editedItem.status_id === 6 && $auth.user?.role_id !== 2
                      "
                      cols="12"
                      sm="12"
                      xl="3"
                      md="3"
                      class="py-0 mb-2"
                    >
                      <v-select
                        v-model="editedItem.arrival_id"
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
                    <v-col cols="12" sm="12" xl="3" md="3" class="py-0">
                      Последный прозвон
                      <CustomDataPicker
                        v-model="last_call"
                        value-type="DD.MM.YYYY HH:mm"
                        type="datetime"
                        :limit="$auth.user?.role_id == 2 ? 'after' : null"
                        format="DD.MM.YYYY HH:mm"
                        :time-picker-options="{
                          start: '08:00',
                          step: '00:15',
                          end: '20:00',
                          format: 'HH:mm',
                        }"
                        @setNow="setLastCall('last_call', true)"
                        @setAfter="setLastCallAfter('last_call', true)"
                      />
                    </v-col>

                    <v-col cols="12" sm="12" xl="3" md="3" class="py-0">
                      Перезвонить
                      <CustomDataPicker
                        v-model="callback"
                        value-type="DD.MM.YYYY HH:mm"
                        type="datetime"
                        :limit="$auth.user?.role_id == 2 ? 'before' : null"
                        format="DD.MM.YYYY HH:mm"
                        :time-picker-options="{
                          start: '08:00',
                          step: '00:15',
                          end: '20:00',
                          format: 'HH:mm',
                        }"
                        @setNow="setCallback('callback', true)"
                        @setAfter="setCallbackAfter('callback', true)"
                      />
                    </v-col>

                    <v-col cols="12" sm="12" xl="3" md="3" class="py-0">
                      Приедет

                      <CustomDataPicker
                        v-model="editedItem.will_arrive"
                        value-type="YYYY-MM-DD"
                        type="date"
                        :limit="$auth.user?.role_id == 2 ? 'before' : null"
                        format="DD.MM.YYYY"
                        @setNow="setWillArrive('will_arrive', false)"
                        @setAfter="setAfterWillArrive('will_arrive', false)"
                      />
                    </v-col>
                    <v-col cols="12" sm="12" xl="3" md="3" class="py-0">
                      Приехал
                      <CustomDataPicker
                        v-model="editedItem.arrived_date"
                        value-type="YYYY-MM-DD"
                        type="date"
                        :limit="$auth.user?.role_id == 2 ? 'before' : null"
                        format="DD.MM.YYYY"
                        @setNow="setArrived('arrived_date', false)"
                        @setAfter="setAfterArrived('arrived_date', false)"
                      />
                    </v-col>

                    <v-col cols="12" sm="6" md="6">
                      <v-textarea
                        v-model="editedItem.comment"
                        rows="3"
                        label="Комментарии"
                        outlined
                        hide-details
                        :readonly="
                          $auth?.user.role_id !== editedItem?.operator_id &&
                          role_id !== 1 &&
                          role_id !== 3 &&
                          role_id !== 6
                        "
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
              <v-btn color="green darken-1" text disabled @click="toArrive()">
                Передать в приезд
              </v-btn>

              <v-btn
                v-role-or-permission="'admin|can_pass_order'"
                color="lime darken-1"
                text
                @click="toConsultation()"
              >
                Передать в Call Center
              </v-btn>
              <v-switch
                v-model="editedItem.arrived"
                ripple
                dense
                size="small"
                label="Приезд подтвержден"
                class="mt-1 d-inline-block"
              />

              <v-spacer />
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
              <v-btn color="green darken-1" text @click="save()">
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
        <v-dialog v-model="exportDialog" max-width="400">
          <v-card>
            <v-card-title class="headline"> Экпорт НО </v-card-title>

            <v-card-text>
              <date-picker
                v-model="export_from"
                value-type="YYYY-MM-DD HH:mm"
                format="DD.MM.Y HH:mm"
                type="datetime"
                placeholder="Дата с"
                style="width: 100%; margin-top: 4px"
              />
              <date-picker
                v-model="export_to"
                value-type="YYYY-MM-DD HH:mm"
                format="DD.MM.Y HH:mm"
                type="datetime"
                placeholder="Дата до"
                style="width: 100%; margin-top: 4px"
              />
            </v-card-text>
            <v-card-actions>
              <v-spacer />
              <v-btn color="primary darken-1" text @click="exportNoAnswer()">
                Скачать
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-dialog v-model="smsDialog" max-width="1100">
          <v-card>
            <v-card-title class="headline">Отправка SMS</v-card-title>

            <v-card-text>
              <v-form ref="smsForm" v-model="validSms" lazy-validation>
                <v-row dense>
                  <v-col cols="12" md="4">
                    <v-text-field
                      v-model="smsData.phone"
                      label="Телефон"
                      :rules="[smsRules.required]"
                      outlined
                      hide-details
                      @change="updatePreview()"
                      dense
                    />
                  </v-col>

                  <v-col cols="12" md="4">
                    <v-select
                      v-model="smsData.operator_id"
                      :items="operators"
                      :item-text="
                        (item) =>
                          item.first_name +
                          ' ' +
                          item.last_name +
                          '(' +
                          item?.work_place +
                          ')'
                      "
                      @change="onOperatorChange()"
                      item-value="id"
                      label="Оператор"
                      :rules="[smsRules.required]"
                      outlined
                      hide-details
                      dense
                    />
                  </v-col>

                  <v-col cols="12" md="4">
                    <v-text-field
                      v-model="smsData.client_name"
                      label="Имя клиента"
                      :rules="[smsRules.required]"
                      outlined
                      @change="updatePreview()"
                      hide-details
                      dense
                    />
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-switch
                      v-model="useTemplate"
                      @change="updatePreview()"
                      label="Использовать шаблон"
                      inset
                      hide-details
                    />
                  </v-col>

                  <v-col cols="12" md="12" v-if="useTemplate">
                    <v-autocomplete
                      v-model="selectedTemplateId"
                      :items="sms_templates"
                      item-text="name"
                      item-value="id"
                      label="Шаблон"
                      hide-details
                      :menu-props="{
                        contentClass: 'sms-template-menu',
                        closeOnContentClick: false,
                      }"
                      @change="updatePreview()"
                      :loading="loadingTemplates"
                      :rules="[smsRules.required]"
                      outlined
                      dense
                      clearable
                    >
                      <template v-slot:item="{ item, on, attrs }">
                        <div
                          class="d-flex align-center px-3 py-1"
                          v-bind="attrs"
                          v-on="on"
                          style="width: 100%"
                        >
                          <div class="flex-grow-1">
                            <div class="text-body-2">{{ item.name }}</div>
                            <div
                              class="text-caption grey--text"
                              style="
                                white-space: normal;
                                word-break: break-word;
                              "
                            >
                              {{ item.body }}
                            </div>
                          </div>

                          <v-btn
                            icon
                            x-small
                            @click.stop="openTemplateDialog('edit', item)"
                          >
                            <v-icon small>mdi-pencil</v-icon>
                          </v-btn>

                          <v-btn
                            icon
                            x-small
                            @click.stop="deleteTemplate(item)"
                          >
                            <v-icon small>mdi-delete</v-icon>
                          </v-btn>
                        </div>
                      </template>

                      <template v-slot:append-outer>
                        <v-btn icon small @click="openTemplateDialog('create')">
                          <v-icon small>mdi-plus</v-icon>
                        </v-btn>
                      </template>
                    </v-autocomplete>
                  </v-col>

                  <v-col cols="12">
                    <div
                      style="
                        font-size: 12px;
                        color: rgba(0, 0, 0, 0.6);
                        margin-bottom: 6px;
                      "
                    >
                      Cообщение (можно отредактировать перед отправкой)
                    </div>
                    <v-textarea
                      v-model="smsData.finalText"
                      :rules="[smsRules.required]"
                      outlined
                      hide-details
                      auto-grow
                      rows="6"
                      counter
                    />
                  </v-col>
                </v-row>
              </v-form>
            </v-card-text>

            <v-card-actions>
              <v-spacer />
              <v-btn text @click="closeSmsDialog">Отмена</v-btn>
              <v-btn
                color="primary"
                :loading="sendingSms"
                :disabled="!validSms"
                @click="sendSms"
              >
                Отправить
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-dialog v-model="tplDialog" max-width="520">
          <v-card>
            <v-card-title class="subtitle-1">
              {{
                tplMode === 'create' ? 'Новый шаблон' : 'Редактировать шаблон'
              }}
            </v-card-title>

            <v-card-text>
              <v-form ref="tplForm" v-model="tplValid" lazy-validation>
                <v-text-field
                  v-model="tplForm.name"
                  label="Название"
                  :rules="[smsRules.required]"
                  outlined
                  dense
                />

                <v-textarea
                  v-model="tplForm.body"
                  label="Текст шаблона"
                  :rules="[smsRules.required]"
                  outlined
                  dense
                  auto-grow
                  rows="4"
                  persistent-hint
                />
              </v-form>
            </v-card-text>

            <v-card-actions>
              <v-spacer />
              <v-btn text @click="tplDialog = false">Отмена</v-btn>
              <v-btn
                color="primary"
                :loading="tplSaving"
                :disabled="!tplValid"
                @click="saveTemplate"
              >
                Сохранить
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-dialog v-model="exportArriveDialog" max-width="550">
          <v-card>
            <v-card-title class="headline">
              Экпорт заявки со статусом приедет
            </v-card-title>

            <v-card-text>
              <date-picker
                v-model="export_arrive_from"
                value-type="YYYY-MM-DD"
                format="DD.MM.Y"
                type="date"
                placeholder="Дата с"
                style="width: 100%; margin-top: 4px"
              />

              <date-picker
                v-model="export_arrive_to"
                value-type="YYYY-MM-DD"
                format="DD.MM.Y"
                type="date"
                style="width: 100%; margin-top: 4px"
                placeholder="Дата до"
              />
            </v-card-text>
            <v-card-actions>
              <v-spacer />
              <v-btn color="primary darken-1" text @click="exportArrive()">
                Скачать
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="repeatDialog" light max-width="850">
          <v-card flat style="background-color: #fdfdfd" class="px-3">
            <p class="pt-4 text-center font-weight-bold">
              Заявка №{{ repeatItem.id }}: повторы
            </p>
            <template>
              <v-simple-table style="background-color: #fdfdfd">
                <template #default>
                  <thead style="background-color: #eee">
                    <tr>
                      <th class="text-center">Номер</th>
                      <th class="text-center">Клиент</th>
                      <th class="text-center">Повторы</th>
                      <th class="text-center">Состояние заявки</th>
                      <th class="text-center">Дата создания</th>
                      <th class="text-center">Комментарий</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in repeats" :key="item.id">
                      <td>
                        <nuxt-link
                          :to="
                            '/crm/' +
                            item.showroom_id +
                            '/order/' +
                            item.id +
                            '/edit'
                          "
                        >
                          {{ item.id }}
                        </nuxt-link>
                      </td>
                      <td>{{ item.client_name }}</td>
                      <td>{{ item.retries }}</td>
                      <td>{{ item.status?.name }}</td>
                      <td>
                        {{
                          $moment(item.created_at).format('DD.MM.YYYY HH:mm:ss')
                        }}
                      </td>
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
import { saveAs } from 'file-saver'
import _ from 'lodash'
import BreadCrumb from '~/components/BreadCrumb'
import CustomDataPicker from '~/components/CustomDataPicker'

export default {
  name: 'CrmOrder',
  components: { BreadCrumb, CustomDataPicker },
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
      lazy: false,
    },
    per_pages: [10, 30, 50, 100, 150],
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
    validSms: false,
    lastUpdate: null,
    onProcess: false,
    // deleteDialog
    deleteId: '',
    deleteDialog: false,
    exportDialog: false,
    smsDialog: false,
    exportArriveDialog: false,
    export_from: null,
    export_to: null,
    export_arrive_from: null,
    export_arrive_to: null,
    nameRules: [(v) => !!v || 'Введитие ФИО клиента'],
    dateRules: [(v) => !!v || 'Выберите дату'],
    colors: [
      'green',
      'purple',
      'orange',
      'indigo',
      'red',
      'blue',
      'success',
      'red',
      'lime',
    ],
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
        text: 'Оператор',
        align: 'center',
        sortable: false,
        width: '80px',
        value: 'operator.first_name',
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
        text: 'Цена',
        align: 'start',
        width: '80px',
        sortable: false,
        value: 'price',
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
        sortable: true,
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
        text: 'Одобрен',
        align: 'center',
        width: '30px',
        value: 'commercial_offer',
      },
      {
        text: 'Способ оплаты',
        align: 'center',
        width: '30px',
        value: 'payment_method',
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
    smsData: {
      phone: '',
      client_name: '',
      showroom_id: '',
      operator_id: null,
      finalText: '',
      sender: '',
    },

    useTemplate: true,
    previewSmsText: '',
    sendingSms: false,
    loadingTemplates: false,
    selectedTemplateId: null,

    tplDialog: false,
    tplMode: 'create', // create | edit
    tplValid: false,
    tplSaving: false,
    tplForm: {
      id: null,
      name: '',
      body: '',
    },

    smsRules: {
      required: (v) => !!v || 'Обязательное поле',
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
      live_region: '',
      ads_source: '',
      will_arrive: '',
      arrived: '',
      arrived_date: '',
      date_of_sale: '',
      call_heard: '',
      type_id: '',
      trash_id: '',
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
      operator_id: null,
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
      trash_id: '',
      country: '',
      car_year: '',
      credit_period: '',
    },
    payment_methods: [
      { id: 7, name: 'Не определено' },
      { id: 1, name: 'Наличными' },
      { id: 2, name: 'В кредит' },
      { id: 4, name: 'Лизинг' },
      { id: 5, name: 'Не дозвон' },
      { id: 6, name: 'Повтор' },
      { id: 9, name: 'Не ликвид' },
      { id: 10, name: 'Повтор ДИ' },
    ],
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
    await store.dispatch('showroom/fetchOperators', {
      showroom_id: id || $auth.user?.showroom_id,
    })
    await store.dispatch('order/fetchAllOrders', { id })
    await store.dispatch('order/fetch_arrivals', { id })
    await store.dispatch('order/fetchTrashes')
    await store.dispatch('order/fetchDrops')

    await store.dispatch('order/fetchArrivalStatuses')
    await store.dispatch('order/fetch_missed_calls', { id })
  },

  computed: {
    showroom_id() {
      return Number(this.$route.params.id) || null
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
    sms_templates() {
      return this.$store.state.order.sms_templates
    },
    trashes() {
      return this.$store.state.order.trashes
    },
    drops() {
      return this.$store.state.order.drops
    },
    arrival_statuses() {
      return this.$store.state.order.arrival_statuses
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
        return this.$store.state.order.statuses.filter(
          (l) => !statuses.includes(l.id)
        )
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
    marks() {
      return this.$store.state.property.marks
    },
    models() {
      return this.$store.state.property.models
    },
    advanced_models() {
      return this.$store.state.property.tradein_models
    },
    operators() {
      return this.$store.state.showroom.operators
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

    useTemplate(val) {
      // при выключении шаблона не затираем finalText, но если оно пустое — подставим превью
      if (!val && !this.smsData.finalText) this.smsData.finalText = ''
      if (val) {
        if (!this.selectedTemplateId && this.sms_templates.length) {
          this.selectedTemplateId = this.sms_templates[0].id
        }
      }
      this.syncFinalTextIfAuto()
    },
    selectedTemplateId() {
      this.syncFinalTextIfAuto()
    },
    previewSmsText() {
      this.syncFinalTextIfAuto()
    },
  },
  mounted() {
    this.handleLoading()
    this.$echo
      .channel('orders_' + this.showroom_id)
      .listen('OrderCreated', async (e) => {
        if (
          this.filter_status === null &&
          this.filter_from === null &&
          this.search === null &&
          this.filter_to === null &&
          this.page === 1 &&
          this.dialog !== true
        ) {
          await this.refresh_page()
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
          this.dialog !== true &&
          this.search === null
        ) {
          this.refresh_page()
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
    if (this.$route.query.repeat) {
      this.repeat = this.$route.query.repeat
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
    if (this.$route.query.page) {
      this.page = parseInt(this.$route.query.page) || null
    }
    this.$store.dispatch('order/fetchOrders2', {
      id: this.$route.params.id,
      query: this.$route.query,
    })
  },

  methods: {
    copyName() {
      navigator.clipboard
        .writeText(this.editedItem?.client_name)
        .then(() => {
          this.$toast.success('ФИО успешно скопирован')
        })
        .catch((err) => {
          this.$toast.error('Произошла ошибка:' + err)
        })
    },

    openDialogArrive() {
      const tomorrow = this.$moment().add(1, 'day').format('YYYY-MM-DD')

      this.exportArriveDialog = true
      this.export_arrive_from = tomorrow
      this.export_arrive_to = tomorrow
    },

    async openDialogSms(item) {
      this.smsDialog = true
      await this.$store.dispatch('order/fetch_sms_templates', {
        id: this.showroom_id,
        operator_id: this.$auth.user?.id,
      })

      this.smsData.client_name = item.client_name
      this.smsData.phone = item.phone
      this.smsData.operator_name = item.operator?.first_name
      this.smsData.operator_id = this.$auth.user?.id

      this.updatePreview()
    },

    copyPhone(phone) {
      navigator.clipboard
        .writeText(phone)
        .then(() => {
          this.$toast.success('Номер успешно скопирован')
        })
        .catch((err) => {
          this.$toast.error('Произошла ошибка:' + err)
        })
    },

    refresh_page: _.debounce(async function () {
      if (this.onProcess === true || document.hidden) {
        console.log('refresh on process or tab hidden')
        return
      }

      if (
        this.lastUpdate &&
        this.$moment().diff(this.lastUpdate, 'seconds') <= 6
      ) {
        this.onProcess = true
        const ago = this.$moment().diff(this.lastUpdate, 'seconds')

        setTimeout(async () => {
          await this.$store.dispatch('order/fetchOrders2', {
            id: this.$route.params.id,
            query: this.$route.query,
          })
          this.lastUpdate = this.$moment()
          this.onProcess = false
          console.log('8.5 sec')
        }, 5500 - ago)
      } else {
        await this.$store.dispatch('order/fetchOrders2', {
          id: this.$route.params.id,
          query: this.$route.query,
        })
        this.lastUpdate = this.$moment()
        console.log('moment')
      }
    }, 300),
    async openRepeat(item) {
      if (item.callback) {
        this.repeatItem.callback = this.$moment(item.callback).format(
          'DD.MM.YYYY HH:mm'
        )
      }
      this.repeatItem.id = item.id
      this.repeatItem.phone = item.phone
      this.repeatItem.status = item.status
      this.repeatItem.showroom_id = this.$route.params.id
      this.repeatItem.created_at = item.created_at
      this.repeatDialog = true
      const { data } = await this.$axios.post('orders/repeats', {
        item: this.repeatItem,
      })
      this.repeats = data
    },
    editItem(item) {
      this.editedIndex = item.id
      this.date = this.$moment(item.date).format('YYYY-MM-DD')

      if (item.mark_id !== 0) {
        this.$store.dispatch('property/fetchModels', { markId: item.mark_id })
      }

      if (this.role_id !== 1) {
        this.$axios
          .post('/orders/visited-mini', { id: this.editedIndex })
          .then((response) => {
            // Handle successful response
          })
          .catch((error) => {
            // Handle error
            console.log('orders/visited-mini:' + error)
          })
      }

      this.editedItem = Object.assign({}, item)
      if (this.editedItem.callback) {
        this.callback = this.$moment(this.editedItem.callback).format(
          'DD.MM.YYYY HH:mm'
        )
        this.oldCallback = this.callback
      }
      if (this.editedItem.last_call) {
        this.last_call = this.$moment(this.editedItem.last_call).format(
          'DD.MM.YYYY HH:mm'
        )
        this.oldLastCall = this.last_call
      }
      this.oldWillArrive = this.editedItem.will_arrive
      this.oldArrivedDate = this.editedItem.arrived_date
      this.dialog = true
    },

    changedPage: _.debounce(async function () {
      const { query } = this.$route
      try {
        await this.$router.push({ query: { ...query, page: this.page } })
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') {
          /// /this.$sentry.captureException(error);
        }
      }
      await this.$store.dispatch('order/fetchOrders2', {
        id: this.$route.params.id,
        query: this.$route.query,
      })
    }, 300), // Adjust the debounce delay as needed
    async changeLimit() {
      const { query } = this.$route
      try {
        await this.$router.push({
          query: { ...query, page: 1, limit: this.limit },
        })
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') {
          /// /this.$sentry.captureException(error);
        }
      }
      await this.$store.dispatch('order/fetchOrders2', {
        id: this.$route.params.id,
        query: this.$route.query,
      })
    },
    async sortRepeat() {
      const { query } = this.$route
      console.log(this.repeat)
      this.repeat = !this.repeat
      console.log(this.repeat)
      try {
        await this.$router.push({
          query: {
            ...query,
            page: 1,
            repeat: this.repeat ? this.repeat : undefined,
          },
        })
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') {
          /// /this.$sentry.captureException(error);
        }
      }

      await this.$store.dispatch('order/fetchOrders2', {
        id: this.$route.params.id,
        query: this.$route.query,
      })
    },
    doSearch: _.debounce(async function () {
      await this.$router
        .push({
          query: {
            ...(this.search && { search: this.search }),
            ...(this.filter_operator && { operator_id: this.filter_operator }),
            ...(this.filter_type?.length && {
              type_id: this.filter_type.join(','),
            }),
            ...(this.filter_site?.length && {
              site_id: this.filter_site.join(','),
            }),
            ...(this.filter_status && { status: this.filter_status }),
            ...(this.filter_trash && { trash: this.filter_trash }),
            ...(this.campaign && { campaign: this.campaign }),
            ...(this.filter_status?.length && {
              status: this.filter_status.join(','),
            }),
            ...(this.filter_from && { from: this.filter_from + ':00' }),
            ...(this.not_confirmed && { not_confirmed: this.not_confirmed }),
            ...(this.paymentUndefined && {
              paymentUndefined: this.paymentUndefined,
            }),
            ...(this.filter_to && { to: this.filter_to + ':59' }),
            page: 1,
          },
        })
        .catch((err) => {
          // Ignore the vuex err regarding  navigating to the page they are already on.
          if (
            err.name !== 'NavigationDuplicated' &&
            !err.message.includes(
              'Avoided redundant navigation to current location'
            )
          ) {
            // But print any other errors to the console
            console.log(err)
          }
        })
      await this.$store.dispatch('order/fetchOrders2', {
        id: this.$route.params.id,
        query: this.$route.query,
      })
      this.page = 1
    }, 500),
    doAdvancedSearch: _.debounce(async function () {
      if (this.advanced_status) {
        this.filter_status = this.advanced_status
      }
      await this.$router
        .push({
          query: {
            ...(this.advanced_search && { search: this.advanced_search }),
            ...(this.advanced_fio && { fio: this.advanced_fio }),
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
            ...(this.advanced_trash && { trash: this.advanced_trash }),
            ...(this.advanced_status?.length && {
              status: this.advanced_status?.join(','),
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
            ...(this.advanced_payment && {
              payment_method: this.advanced_payment,
            }),
            ...(this.advanced_mark && { mark_id: this.advanced_mark }),
            ...(this.advanced_model && { model_id: this.advanced_model }),
            ...(this.advanced_region && { region_id: this.advanced_region }),
            ...(this.advanced_phone && { phone: this.advanced_phone }),
            ...(this.advanced_drop && { drop_id: this.advanced_drop }),
            ...(this.not_confirmed && { not_confirmed: this.not_confirmed }),
            page: 1,
          },
        })
        .catch((err) => {
          // Ignore the vuex err regarding  navigating to the page they are already on.
          if (
            err.name !== 'NavigationDuplicated' &&
            !err.message.includes(
              'Avoided redundant navigation to current location'
            )
          ) {
            // But print any other errors to the console
            console.log(err)
          }
        })

      this.page = 1
      await this.$store.dispatch('order/fetchOrders2', {
        id: this.$route.params.id,
        query: this.$route.query,
      })
    }, 500),

    onDelete(id) {
      if (confirm('Вы уверены, что хотите удалить?')) {
        this.$store
          .dispatch('order/delete', {
            id,
            showroom_id: this.$route.params.id,
          })
          .then(() => {
            this.$toast.success('Заявка удалёна')
          })
          .catch((error) => {
            this.$toast.error('Произошла ошибка при удалёние заявки: ' + error)
          })
      }
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
        .dispatch('order/delete', {
          id: this.deleteId,
          showroom_id: this.$route.params.id,
        })
        .then(() => {
          this.$toast.success('Заявка удалёна')
        })
        .catch((error) => {
          this.$toast.error('Произошла ошибка при удалёние заявки: ' + error)
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
    async save() {
      await this.$refs.form.validate()
      if (!this.valid) {
        this.$toast.error('Заполните обязательные поля!!!')
        return
      }
      const statuses = [1, 3, 7, 8, 15, 16, 1000]
      const lists = [1, 6, 7, 8, 15, 16, 1000]
      if (
        !statuses.includes(this.form?.status_id) &&
        this.form?.region_id === null
      ) {
        this.$toast.error('Выберите региона!!!')
        return
      } else if (
        !lists.includes(this.editedItem?.status_id) &&
        this.callback === null
      ) {
        this.$toast.error('Заполните поле перезвонить!!!')
        return
      }

      if (
        this.editedItem.status_id === 7 &&
        this.editedItem.trash_id === null
      ) {
        this.$toast.error('Вы не выбрали тип корзины!!!')
        return
      }
      this.editedItem.showroom_id = this.showroom_id

      if (this.last_call !== null) {
        var st = this.last_call + ':00'
        this.editedItem.last_call = this.$moment(
          st,
          'DD.MM.YYYY HH:mm:ss'
        ).format('YYYY-MM-DD HH:mm:ss')
        if (
          this.editedIndex !== -1 &&
          this.last_call !== this.oldLastCall &&
          this.$moment(st, 'DD.MM.YYYY HH:mm:ss').isAfter()
        ) {
          this.$toast.error('Поле последный прозвон не является корректной!!!')
          this.editedItem.last_call =
            this.$moment(st).format('DD.MM.YYYY HH:mm')
          return
        }
      }

      if (this.callback !== null) {
        const cb = this.callback + ':00'

        const call_back = this.$moment(cb, 'DD.MM.YYYY HH:mm:ss').format(
          'YYYY-MM-DD HH:mm:ss'
        )

        const after3Months = this.$moment()
          .add(3, 'months')
          .format('YYYY-MM-DD HH:mm:ss')
        console.log(
          this.$moment(cb, 'DD.MM.YYYY HH:mm:ss').isBefore(
            this.$moment(st, 'DD.MM.YYYY HH:mm:ss')
          )
        )

        if (
          this.editedIndex !== -1 &&
          this.callback !== this.oldCallback &&
          (this.$moment(cb, 'DD.MM.YYYY HH:mm:ss').isBefore(
            this.$moment(st, 'DD.MM.YYYY HH:mm:ss')
          ) ||
            this.$moment(cb, 'DD.MM.YYYY HH:mm:ss').isAfter(after3Months))
        ) {
          this.$toast.error('Поле перезвонить не является корректной!!!')
          this.editedItem.callback = this.$moment(cb).format('DD.MM.YYYY HH:mm')
          return
        }
        this.editedItem.callback = call_back
      } else {
        this.editedItem.callback = null
      }

      if (this.editedItem.will_arrive !== null) {
        const today = this.$moment()
        if (
          this.editedIndex !== -1 &&
          this.editedItem.will_arrive !== this.oldWillArrive &&
          !this.$moment(this.editedItem.will_arrive).isSameOrAfter(today, 'day')
        ) {
          this.$toast.error('Поле приедет не является корректной!!!')
          this.editedItem.last_call = this.$moment(
            this.editedItem.last_call
          ).format('DD.MM.YYYY HH:mm')
          this.editedItem.callback = this.$moment(
            this.editedItem.callback
          ).format('DD.MM.YYYY HH:mm')
          return
        }
      }

      if (this.editedItem.arrived_date !== null) {
        console.log(
          this.$moment(this.editedItem.arrived_date).isSame(new Date(), 'day')
        )
        if (
          this.editedIndex !== -1 &&
          this.editedItem.arrived_date !== this.oldArrivedDate &&
          !this.$moment(this.editedItem.arrived_date).isSame(
            new Date(),
            'day'
          ) &&
          this.role_id === 1
        ) {
          this.$toast.error('Поле приехал не является корректной!!!')
          this.editedItem.last_call = this.$moment(
            this.editedItem.last_call
          ).format('DD.MM.YYYY HH:mm')
          this.editedItem.callback = this.$moment(
            this.editedItem.callback
          ).format('DD.MM.YYYY HH:mm')
          return
        }
      }

      if (this.editedIndex == -1) {
        this.editedItem.operator_id =
          this.$auth.user?.role_id === 2
            ? this.$auth.user?.id
            : this.editedItem.operator_id
      }
      this.$store
        .dispatch('order/updateOrder', {
          item: this.editedItem,
        })
        .then((res) => {
          if (this.editedIndex > -1) {
            this.$toast.success('Заявка изменён')
          } else {
            this.$toast.success('Заявка добавлён')
          }
          this.close()
          this.refresh_page()
        })
        .catch((error) => {
          this.$toast.error('Заполните обязательные поля' + error)
        })
    },
    async toArrive() {
      console.log(this.editedItem.model?.name != null)
      this.editedItem.car_name = `${
        this.editedItem.mark?.name != null
          ? this.editedItem.mark?.name + ' '
          : ''
      }${
        this.editedItem.model?.name != null ? this.editedItem.model?.name : ''
      }`
      this.$store
        .dispatch('order/toArrive', {
          item: this.editedItem,
        })
        .then((res) => {
          if (this.editedIndex > -1) {
            this.$toast.success('Передано в приезд')
          }
          this.close()
        })
        .catch((error) => {
          this.$toast.error('Произошла ошибка ' + error)
        })
    },
    async toConsultation() {
      const postData = {
        showroom_id: this.editedItem.showroom_id,
        client: this.editedItem.client_name,
        phone: this.editedItem.phone,
        mark:
          this.editedItem.mark?.name != null
            ? this.editedItem.mark?.name
            : null,
        model:
          this.editedItem.model?.name != null
            ? this.editedItem.model?.name
            : null,
        region:
          this.editedItem.region?.name != null
            ? this.editedItem.region?.name
            : null,
      }
      const token = `$2a$10$wZOqtQTDzWtiWkhy5IX.S.5cTsYaxMju5fuzQUy9YJtSE6Nsz/1Gu`

      this.$axios.defaults.headers.common.Authorization = `Bearer ${token}`
      await this.$axios
        .post('https://acp77.ru/api/create/consultation', postData)
        .then((response) => {
          this.$toast.success('Передано в Call Center')
          this.dialog = false
        })
        .catch((error) => {
          this.$toast.error('Произошла ошибка ' + error)
        })
    },
    validate() {
      this.$refs.form.validate()
      console.log(this.$refs.form.validate())
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
      } else if (item.status_id === 1 && item.type_id === 21) {
        return 'teal white--text'
      }
    },
    getModels(markId = null) {
      console.log('getModels', markId)
      this.editedItem.model_id = null
      if (markId !== 0) {
        this.$store.dispatch('property/fetchModels', { markId })
      }
    },
    getAdvancedModels(markId = null) {
      this.advanced_model = null
      if (markId !== 0) {
        this.$store.dispatch('property/fetchTradeInModels', { markId })
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
      const { query } = this.$route
      try {
        await this.$router.push({
          query: {},
        })
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') {
          /// /this.$sentry.captureException(error);
        }
      } finally {
        await this.$store.dispatch('order/fetchOrders2', {
          id: this.$route.params.id,
          query: {},
        })
      }
    },
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
          query: {},
        })
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') {
          /// /this.$sentry.captureException(error);
        }
      }
    },
    async clearAdvanced() {
      this.advanced_from = null
      this.advanced_to = null
      this.advanced_site = null
      this.advanced_type = null
      this.advanced_search = null
      this.advanced_fio = null
      this.advanced_status = null
      this.advanced_trash = null
      this.advanced_operator = null
      this.advanced_filter_type = null
      this.advanced_region = null
      this.filter_status = null
      this.advanced_phone = null
      this.advanced_arrived = null
      this.advanced_drop = null
      this.not_confirmed = null

      this.page = 1
      try {
        await this.$router.push({
          query: { page: 1 },
        })
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') {
          /// /this.$sentry.captureException(error);
        }
      }
    },
    exportFile() {
      const XLSX = require('xlsx')
      const wb = XLSX.utils.book_new()
      const rows = this.orders?.data.map((row, index) => ({
        '№': index + 1,
        Дата: this.$moment(row.created_at).format('DD.MM.YYYY'),
        Клиент: row.client_name,
        Шоурум: row.showroom?.name,
        Телефон: row.phone,
        Цена: row.price,
        ПВ: row.initial_fee,
        Оператор: row.operator?.name,
        Регион: row.region?.name,
        Сайт: row.site?.title,
        'Комментарий КЦ': row.comment,
        'Общий комментарий': row.general_comment,
        'Источник рекламы': row.entry_point,
      }))
      const ws = XLSX.utils.json_to_sheet(rows)
      ws['!cols'] = [
        { wch: 3 },
        { wch: 14 },
        { wch: 30 },
        { wch: 14 },
        { wch: 14 },
        { wch: 12 },
        { wch: 18 },
        { wch: 18 },
        { wch: 20 },
        { wch: 20 },
        { wch: 20 },
        { wch: 20 },
        { wch: 65 },
        { wch: 65 },
        { wch: 35 },
      ]
      XLSX.utils.book_append_sheet(wb, ws, 'Завки - ' + this.showroom?.name)
      const wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' })
      saveAs(
        new Blob([wbout], { type: 'application/octet-stream' }),
        'Заявки_' +
          this.showroom?.name +
          '_' +
          this.$moment().format('DD-MM-YYYY') +
          '.xlsx'
      )
    },

    setNow(field, isDateTime = false) {
      this.editedItem[field] = isDateTime
        ? this.$moment().format('DD.MM.YYYY HH:mm')
        : this.$moment().format('YYYY-MM-DD')
    },

    async exportNoAnswer() {
      try {
        const response = await this.$axios.post(
          '/export/no-answer',
          {
            from: this.export_from,
            to: this.export_to,
            showroom_id: this.showroom_id,
          },
          {
            responseType: 'blob', // Treat the response as a binary blob
          }
        )

        // Create a Blob object containing the file data
        const blob = new Blob([response.data])

        // Create a link element to trigger the download
        const link = document.createElement('a')
        link.href = window.URL.createObjectURL(blob)
        link.download = 'Заявки со статусом НО.xlsx' // Change the filename as needed

        // Simulate a click on the link to trigger the download
        link.click()

        // Clean up by revoking the Object URL
        window.URL.revokeObjectURL(link.href)
        this.exportDialog = false
        this.$toast.success('Файл успешно скачан...')
      } catch (error) {
        this.$toast.success('Ошибка: ' + error)
      }
    },

    async exportArrive() {
      try {
        const response = await this.$axios.post(
          '/export/arrive',
          {
            from: this.export_arrive_from,
            to: this.export_arrive_to,
            showroom_id: this.showroom_id,
          },
          {
            responseType: 'blob', // Treat the response as a binary blob
          }
        )

        // Create a Blob object containing the file data
        const blob = new Blob([response.data])

        // Create a link element to trigger the download
        const link = document.createElement('a')
        link.href = window.URL.createObjectURL(blob)
        link.download = 'Заявки со статусом приедет.xlsx' // Change the filename as needed

        // Simulate a click on the link to trigger the download
        link.click()

        // Clean up by revoking the Object URL
        window.URL.revokeObjectURL(link.href)
        this.exportArriveDialog = false
        this.$toast.success('Файл успешно скачан...')
      } catch (error) {
        this.$toast.success('Ошибка: ' + error)
      }
    },

    setCallback(field, isDateTime = false) {
      this.callback = isDateTime
        ? this.$moment().format('DD.MM.YYYY HH:mm')
        : this.$moment().format('YYYY-MM-DD')
    },

    changedType() {
      console.log(this.editedItem.type_id)
      if (this.editedItem.type_id === 12) {
        this.editedItem.site_id = 6469
      }
    },

    setCallbackAfter(field, isDateTime = false) {
      this.callback = isDateTime
        ? this.$moment()
            .add('hours', 1)
            .add('minutes', 30)
            .format('DD.MM.YYYY HH:mm')
        : this.$moment().format('YYYY-MM-DD')
    },
    setLastCall(field, isDateTime = false) {
      this.last_call = isDateTime
        ? this.$moment().format('DD.MM.YYYY HH:mm')
        : this.$moment().format('YYYY-MM-DD')
    },
    setLastCallAfter(field, isDateTime = false) {
      this.last_call = isDateTime
        ? this.$moment().add('hours', 1.5).format('DD.MM.YYYY HH:mm')
        : this.$moment().format('YYYY-MM-DD')
    },
    setWillArrive(field, isDateTime = false) {
      this.editedItem.will_arrive = isDateTime
        ? this.$moment().format('DD.MM.YYYY HH:mm')
        : this.$moment().format('YYYY-MM-DD')
      this.open_dtp = false
      this.open_dtp_callback = false
    },
    setAfterWillArrive(field, isDateTime = false) {
      this.editedItem.will_arrive = isDateTime
        ? this.$moment().add('1', 'days').format('DD.MM.YYYY HH:mm')
        : this.$moment().add('1', 'days').format('YYYY-MM-DD')
      this.open_dtp = false
      this.open_dtp_callback = false
    },
    setArrived(field, isDateTime = false) {
      this.editedItem.arrived_date = isDateTime
        ? this.$moment().format('DD.MM.YYYY HH:mm')
        : this.$moment().format('YYYY-MM-DD')
      this.open_dtp = false
      this.open_dtp_callback = false
    },
    setAfterArrived(field, isDateTime = false) {
      this.editedItem.arrived_date = isDateTime
        ? this.$moment().add('1', 'days').format('DD.MM.YYYY HH:mm')
        : this.$moment().add('1', 'days').format('YYYY-MM-DD')
      this.open_dtp = false
      this.open_dtp_callback = false
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

    redirectToWhatsApp() {
      const phoneNumber = this.editedItem.phone?.match(/\d/g)?.join('')
      if (phoneNumber) {
        const url = `https://wa.me/${phoneNumber}`
        window.open(url, '_blank')
      } else {
        console.error('Invalid phone number')
      }
    },
    async changeStatus(id = null) {
      const { query } = this.$route
      this.filter_status = id
      this.page = 1
      try {
        await this.$router.push({ query: { ...query, page: 1, status: id } })
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') {
        }
      }
      await this.refresh_page()
    },
    validatePayment(value) {
      const allowedStatusIds = [1, 3, 8, 13, 15, 16, 1000]
      if (
        value !== null ||
        allowedStatusIds.includes(this.editedItem.status_id)
      ) {
        return true
      } else {
        return 'Выберите способ оплаты' // Validation failed
      }
    },
    resetValidation() {
      this.$refs.form.resetValidation() // Reset form validation
    },
    changedStatus() {
      if (this.editedItem.status_id === 6) {
        this.editedItem.arrived_date = this.$moment().format('YYYY-MM-DD')
      }
      if (this.editedItem?.status_id === 16) {
        this.editedItem.payment_method = 10
      }
      if (this.editedItem?.status_id === 8) {
        this.editedItem.payment_method = 6
      }
      if (this.editedItem?.status_id === 15) {
        this.editedItem.payment_method = 9
      }
      this.resetValidation()
    },
    getColumnCount() {
      const status = parseInt(this.filter_status || this.advanced_status)
      if (status === 7 || status === 13) {
        return 1
      } else return 2
    },
    getSelectedTemplate() {
      return (
        this.sms_templates.find((t) => t.id === this.selectedTemplateId) || null
      )
    },

    updatePreview() {
      console.log('reaact')
      if (!this.useTemplate) {
        this.previewSmsText = ''
        return
      }

      const tpl = this.getSelectedTemplate()
      if (!tpl) {
        this.previewSmsText = ''
        return
      }

      this.previewSmsText = tpl.body

      // если пользователь ещё не правил текст вручную — синхронизируем
      if (
        !this.smsData.finalText ||
        this.smsData.finalText === this._lastPreview
      ) {
        this.smsData.finalText = this.previewSmsText
      }

      console.log('previewSmsText:', this.previewSmsText)
      console.log('finalText:', this.smsData.finalText)

      this._lastPreview = this.previewSmsText
    },

    async onOperatorChange() {
      await this.$store.dispatch('order/fetch_sms_templates', {
        id: this.showroom_id,
        operator_id: this.smsData.operator_id,
      })
    },

    syncFinalTextIfAuto() {
      // логика: если итоговое сообщение пустое или совпадает с прошлым превью — перезапишем на актуальное превью
      // упрощённо: если используем шаблон — всегда подставляем превью, пока пользователь не начал редактировать
      if (!this.useTemplate) return

      if (
        !this.smsData.finalText ||
        this.smsData.finalText === this._lastpreviewText
      ) {
        this.smsData.finalText = this.previewSmsText
      }
      this._lastpreviewText = this.previewSmsText
    },
    closeSmsDialog() {
      this.openDialogSms = false
    },
    async sendSms() {
      const ok = this.$refs.smsForm && this.$refs.smsForm.validate()
      if (!ok) return

      this.sendingSms = true
      try {
        // Пример API: POST /api/sms/send
        const payload = {
          phone: this.smsData.phone,
          text: this.smsData.finalText,
          sender: this.smsData.sender || null,
          showroom_id: this.showroom_id,

          // полезно сохранить, что использовался шаблон
          template_id: this.useTemplate ? this.selectedTemplateId : null,

          // если хочешь — отправляй переменные для аудита/логов
          meta: {
            client_name: this.smsData.client_name,
            car_brand: this.smsData.car_brand,
            car_model: this.smsData.car_model,
            operator_name: this.smsData.operator_name,
          },
        }

        const { data } = await this.$axios.post(
          'https://a-c77.ru/api/prostor/sms/send',
          payload
        )

        this.$toast.success('СМС успешно отправлен!!!')
      } catch (e) {
        this.$toast.error(
          'Произошла ошибка проверьте правильность телефона!!!' +
            this.extractError(e)
        )
      } finally {
        this.sendingSms = false
      }
    },
    extractError(e) {
      // axios error helper
      return (
        (e &&
          e.response &&
          e.response.data &&
          (e.response.data.message || e.response.data.error)) ||
        (e && e.message) ||
        ''
      )
    },

    openTemplateDialog(mode, item = null) {
      this.tplMode = mode

      if (mode === 'create') {
        this.tplForm = { id: null, name: '', body: '' }
      } else {
        const t = item || this.getSelectedTemplate()
        if (!t) return
        this.tplForm = { id: t.id, name: t.name, body: t.body }
      }

      this.tplDialog = true
      this.$nextTick(() => this.$refs.tplForm?.resetValidation())
    },

    async deleteTemplate(item) {
      const ok = confirm(`Удалить шаблон "${item.name}"?`)
      if (!ok) return

      try {
        await this.$axios.delete(`/sms/templates/${item.id}`)

        await this.$store.dispatch('order/fetch_sms_templates', {
          id: this.showroom_id,
          operator_id: this.smsData.operator_id,
        })

        if (this.selectedTemplateId === item.id) {
          this.selectedTemplateId = null
          this.updatePreview()
        }
      } catch (e) {
        this.$toast.error('Ошибка при удаление!!!')
      }
    },

    async saveTemplate() {
      const ok = this.$refs.tplForm && this.$refs.tplForm.validate()
      if (!ok) return

      this.tplSaving = true
      try {
        if (this.tplMode === 'create') {
          const { data } = await this.$axios.post('/sms/templates', {
            showroom_id: this.showroom_id,
            operator_id: this.smsData.operator_id,
            name: this.tplForm.name,
            body: this.tplForm.body,
            is_active: true,
          })

          this.selectedTemplateId = data.id

          this.$toast.success('Шаблон успешно добавлен!!!')
        } else {
          const { data } = await this.$axios.post(`/sms/templates`, {
            name: this.tplForm.name,
            body: this.tplForm.body,
            id: this.tplForm.id,
            showroom_id: this.showroom_id,
            operator_id: this.smsData.operator_id,
          })

          this.$toast.success('Шаблон успешно изменен!!!')

          // обновили в списке

          this.selectedTemplateId = data.id
        }

        await this.$store.dispatch('order/fetch_sms_templates', {
          id: this.showroom_id,
          operator_id: this.smsData.operator_id,
        })

        this.tplDialog = false
        this.updatePreview()
      } catch (e) {
        // можешь заменить на свой обработчик ошибок
        console.error(e)
      } finally {
        this.tplSaving = false
      }
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

.sms-template-menu {
  z-index: 200 !important;
}
</style>

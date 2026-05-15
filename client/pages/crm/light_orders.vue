<template>
  <div>
    <BreadCrumb :items="links" />
    <v-container
      fluid
      class="pt-0"
      v-shortkey="['ctrl', 'space']"
      max-width="600px"
      @shortkey="openPassOrderDialog"
    >
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
                        placeholder="Дата с"
                        value-type="YYYY-MM-DD HH:mm"
                        format="DD.MM.Y HH:mm"
                        type="datetime"
                        style="width: 100%; margin-top: 4px"
                        @clear="clearFilter()"
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="2" class="hidden-sm-and-down">
                      <date-picker
                        v-model="filter_to"
                        value-type="YYYY-MM-DD HH:mm"
                        format="DD.MM.Y HH:mm"
                        type="datetime"
                        placeholder="Дата до"
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
                        v-on:keyup.enter="doSearch()"
                        clearable
                        label="Поиск"
                        hide-details
                        outlined
                        dense
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
                        v-model="filter_showroom"
                        :items="showrooms"
                        hide-details
                        :item-text="(item) => item.name"
                        item-value="id"
                        label="Салон"
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
                            &nbsp;(+{{ filter_showroom.length - 1 }})
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
                      <v-autocomplete
                        v-model="filter_site"
                        :items="sites"
                        hide-details
                        :item-text="
                          (item) =>
                            `${item.title} ${
                              item.description != null
                                ? '(' + item.description + ')'
                                : ''
                            }`
                        "
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
                            &nbsp;(+{{ filter_site.length - 1 }})
                          </span>
                        </template>
                      </v-autocomplete>
                    </v-col>

                    <v-col
                      cols="12"
                      sm="6"
                      xl="2"
                      md="2"
                      class="hidden-sm-and-down mt-1"
                      v-if="role_id !== 2"
                    >
                      <v-select
                        v-model="filter_operator"
                        :items="operators"
                        hide-details
                        :item-text="
                          (item) => item.first_name + ' ' + item.last_name
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
                  </v-row>
                </v-col>
                <v-col cols="12" sm="6" md="2">
                  <v-row>
                    <v-col cols="12" sm="6" md="6" class="hidden-sm-and-down">
                      <v-btn
                        color="success"
                        dark
                        class="mb-2 mt-1"
                        @click="doSearch()"
                      >
                        Применить
                      </v-btn>
                    </v-col>
                    <v-col cols="12" sm="6" md="6" class="hidden-sm-and-down">
                      <v-btn
                        color="error"
                        dark
                        class="mb-2 mt-1"
                        @click="clearFilter()"
                      >
                        Сбросить
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-col>
              </v-row>
              <v-spacer />
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

                    <v-list-item>
                      <v-list-item-action>
                        <v-btn
                          v-if="role_id === 1"
                          color="warning"
                          dark
                          class="mb-2"
                          @click="distributeDialog = true"
                        >
                          Распределить
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
              <template>
                <v-btn
                  :color="filter_status == 1 ? 'green darken-1' : 'darken-1'"
                  text
                  @click="changeStatus(1)"
                >
                  Новая
                </v-btn>

                <v-btn
                  :color="filter_status == 3 ? 'green darken-1' : 'darken-1'"
                  text
                  @click="changeStatus(3)"
                >
                  Не отвечает
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
                  Повтор
                </v-btn>
                <v-btn
                  :color="filter_status == 1000 ? 'green darken-1' : 'darken-1'"
                  text
                  @click="changeStatus(1000)"
                >
                  Передано
                </v-btn>
                <v-btn
                  :color="
                    filter_status === null ? 'green darken-1' : 'darken-1'
                  "
                  text
                  @click="changeStatus()"
                >
                  ВСЕ
                </v-btn>
              </template>
              <v-data-table
                :headers="computedHeaders"
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
                <template v-slot:header.select_all="">
                  <v-checkbox
                    v-model="selectAll"
                    :indeterminate="isIndeterminate"
                    @change="toggleSelectAll"
                  ></v-checkbox>
                </template>
                <template v-slot:header.retries="">
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
                      <td v-if="role_id === 1">
                        <v-checkbox
                          class="ml-3"
                          v-model="selectedOrders"
                          :key="item.id"
                          :value="item.id"
                        ></v-checkbox>
                      </td>
                      <td>
                        <nuxt-link
                          :to="
                            '/crm/' +
                            item.showroom_id +
                            '/order/' +
                            item.id +
                            '/edit-mini'
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
                          <template v-else> Не определено </template>
                        </nuxt-link>
                      </td>

                      <td>
                        <template v-if="item.site">
                          {{ item.site?.description }}
                        </template>
                      </td>

                      <td>
                        {{ item.status?.name }}
                      </td>
                      <td>
                        {{ item.operator?.first_name }}
                        {{ item.operator?.last_name }}
                      </td>
                      <td></td>

                      <td>
                        {{
                          $moment(item.created_at).format('DD.MM.YYYY HH:mm')
                        }}
                      </td>
                      <td>
                        {{ item.mark?.name }} {{ item.model?.name }}
                        {{ item.complectation }}
                      </td>

                      <td>{{ item.phone | mask('+7 ### ###-##-##') }}</td>

                      <td>
                        <a
                          @click.prevent="openRepeat(item)"
                          class="font-weight-bold"
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
                        <v-rating :value="item.call_count">
                          <template v-slot:item="props">
                            <v-icon
                              :color="props.isFilled ? 'red' : 'grey-lighten-1'"
                              size="28px"
                              style="margin-top: 1px"
                            >
                              {{
                                props.isFilled
                                  ? 'mdi-star-circle'
                                  : 'mdi-star-circle-outline'
                              }}
                            </v-icon>
                          </template>
                        </v-rating>
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
                        <template v-if="item.copied_to">
                          <v-chip
                            dark
                            class="mr-1 mb-1"
                            v-for="(salon, y) in item.copied_to"
                            :key="'showroom__' + y"
                            :color="colors[salon]"
                          >
                            {{ showrooms.find((l) => l.id === salon)?.name }}
                          </v-chip>
                        </template>
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

        <v-dialog v-model="dialog" max-width="1000" persistent>
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
                        v-model="editedItem.operator_id"
                        :items="operators"
                        :value="operators[editedItem.operator_id]"
                        :item-text="
                          (item) => item.first_name + ' ' + item.last_name
                        "
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Оператор"
                        hide-details
                        :disabled="role_id !== 1 && role_id !== 3"
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
                    <v-col cols="12" sm="6" md="5">
                      <v-select
                        v-model="editedItem.site_id"
                        :items="sites"
                        hide-details
                        :item-text="
                          (item) =>
                            `${item.title} ${
                              item.description != null
                                ? '(' + item.description + ')'
                                : ''
                            }`
                        "
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
                        class="mt-4"
                        menu-props="auto"
                        outlined
                        clearable
                        requiredF
                        dense
                        @click:clear="
                          $nextTick(() => (editedItem.status_id = null))
                        "
                      />
                    </v-col>
                    <v-col cols="12" sm="12" xl="3" md="3">
                      Последный прозвон
                      <DTPicker
                        v-model="last_call"
                        value-type="DD.MM.YYYY HH:mm"
                        type="datetime"
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
                      <DTPicker
                        v-model="callback"
                        value-type="DD.MM.YYYY HH:mm"
                        type="datetime"
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
                      <div class="text-center">
                        Кол-во звонков
                        <v-rating v-model="editedItem.call_count">
                          <template v-slot:item="props">
                            <v-icon
                              :color="props.isFilled ? 'red' : 'grey-lighten-1'"
                              size="28px"
                              style="margin-top: 1px"
                              @click="handleRatingChange(props)"
                            >
                              {{
                                props.isFilled
                                  ? 'mdi-star-circle'
                                  : 'mdi-star-circle-outline'
                              }}
                            </v-icon>
                          </template>
                        </v-rating>
                      </div>
                    </v-col>

                    <v-col cols="12" sm="12" md="12">
                      <v-textarea
                        v-model="editedItem.comment"
                        rows="3"
                        label="Комментарии"
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
              <v-btn color="purple" text class="mr-2" @click="distributeOpen()">
                Передать в другой салон
              </v-btn>

              <v-btn color="green darken-1" text disabled @click="toArrive()">
                Передать в приезд
              </v-btn>

              <v-checkbox
                v-model="editedItem.call_heard"
                color="purple darken-1"
                label="Прослушано"
                class="ml-4"
                v-if="role_id === 1"
              >
                Прослушано
              </v-checkbox>

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
        <v-dialog v-model="repeatDialog" light max-width="850">
          <v-card flat style="background-color: #fdfdfd" class="px-3">
            <p class="pt-4 text-center font-weight-bold">
              Заявка {{ repeatItem.id }}
              {{
                $moment(repeatItem.created_at).format('DD.MM.YYYY HH:mm:ss')
              }}: повторы
            </p>
            <template>
              <v-simple-table style="background-color: #fdfdfd">
                <template v-slot:default>
                  <thead style="background-color: #eee">
                    <tr>
                      <th class="text-center">№</th>
                      <th class="text-center">Номер</th>
                      <th class="text-center">Клиент</th>
                      <th class="text-center">Повтор</th>
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
                            '/edit-mini'
                          "
                        >
                          {{ item.id }}
                        </nuxt-link>
                      </td>
                      <td>{{ item.phone }}</td>
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

        <v-dialog v-model="isOpenDistribute" max-width="400px">
          <v-card>
            <v-card-title>
              <span class="headline"
                >Передача заявки {{ $route.params.showroom }}</span
              >
            </v-card-title>
            <v-card-text>
              <v-container>
                <v-form
                  id="copy-form"
                  ref="copy_form"
                  v-model="copyValid"
                  lazy-validation
                >
                  <v-row dense>
                    <v-col cols="12" class="py-0 text-right">
                      <v-select
                        v-model="toShowroom"
                        :items="
                          showrooms.filter((l) => l.id != $route.params.id)
                        "
                        item-value="id"
                        item-text="name"
                        placeholder="Салон"
                        :rules="[(v) => !!v || 'Выберите салона']"
                      />
                    </v-col>
                  </v-row>
                </v-form>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer />
              <v-btn color="primary" @click="distribute()">Передать</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-dialog v-model="distributeDialog" max-width="600px">
          <v-card>
            <v-card-title>
              <span class="headline">Передача заявки</span>
            </v-card-title>
            <v-card-text>
              <v-container>
                <v-form id="copy-form" ref="copy_form" lazy-validation>
                  <v-row dense>
                    <v-col cols="12" class="py-0 text-right">
                      <v-select
                        v-model="selectedOperator"
                        hide-details
                        :item-text="
                          (item) => item.first_name + ' ' + item.last_name
                        "
                        item-value="id"
                        :items="operators"
                        label="Оператор"
                        menu-props="auto"
                        style="width: 120%"
                        outlined
                        clearable
                        required
                        dense
                        @click:clear="
                          $nextTick(() => (selectedOperator = null))
                        "
                      >
                      </v-select>
                    </v-col>
                  </v-row>
                </v-form>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer />
              <v-btn
                :disabled="selectedOperator === null"
                color="primary"
                @click="passOrders()"
                >Передать</v-btn
              >
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-row>
    </v-container>
  </div>
</template>
<script>
import { saveAs } from 'file-saver'
import BreadCrumb from '~/components/BreadCrumb'
import DTPicker from '~/components/DTPicker'
import PhoneMask from '~/components/PhoneMask'

export default {
  name: 'CrmOrder',
  components: { BreadCrumb, DTPicker, PhoneMask },
  beforeRouteLeave(to, from, next) {
    this.$store.dispatch('user/toggle', true)
    clearInterval(this.intervalid)
    next()
  },
  layout: 'victory',
  middleware: 'permission',
  data: () => ({
    menu: false,
    componentKey: 0,
    page: 1,
    timeout: null,
    lastUpdate: null,
    onProcess: false,
    dialog: false,
    distributeDialog: false,
    isOpenDistribute: false,
    toShowroom: null,
    repeatDialog: false,
    repeats: [],
    isSearch: false,
    isCopied: false,
    search: null,
    mask: {
      mask: '{7} (000) 000-00-00',
      lazy: false,
    },
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
    isFilter: false,
    filter_from: null,
    filter_to: null,
    filter_site: null,
    filter_showroom: null,
    filter_type: null,
    filter_status: null,
    filter_operator: null,
    status_id: null,
    menu2: false,
    menu4: false,
    isLoading: false,
    valid: false,
    repeat: false,
    copyValid: false,
    last_call: false,
    callback: false,
    selectedOrders: [],
    selectedOperator: null,
    isIndeterminate: false,
    selectAll: false,
    // deleteDialog
    deleteId: '',
    deleteDialog: false,
    nameRules: [(v) => !!v || 'Введитие ФИО клиента'],
    dateRules: [(v) => !!v || 'Выберите дату'],
    headers: [
      {
        text: 'Select all',
        align: 'center',
        width: '120px',
        sortable: false,
        value: 'select_all',
      },
      {
        text: 'Сайт',
        align: 'center',
        width: '120px',
        value: 'id',
      },
      {
        text: 'Салон',
        align: 'center',
        width: '120px',
        value: 'salon',
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
        value: 'region',
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
        text: 'Сотовый',
        align: 'center',
        sortable: false,
        width: '140px',
        value: 'phone',
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
        text: 'Кол-во прозвонов',
        align: 'center',
        width: '30px',
        value: 'call_count',
      },
      {
        text: 'Комментарий',
        align: 'center',
        width: '250px',
        sortable: false,
        value: 'comment',
      },
      {
        text: 'Передан',
        align: 'center',
        width: '250px',
        sortable: false,
        value: 'copied',
      },
    ],
    editedIndex: -1,
    apiForm: {
      ext_number: null,
      phone: null,
      from_number: null,
      sip: null,
      showroom_id: null,
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
      will_arrive: '',
      arrived: '',
      arrived_date: '',
      type_id: '',
      call_count: 0,
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
      call_heard: '',
      type_id: '',
      country: '',
      car_year: '',
      credit_period: '',
      call_count: 0,
    },
  }),

  async fetch({ store, params: { id }, $auth }) {
    await store.dispatch('user/toggle', false)
    await store.dispatch('property/fetchMarks')
    await store.dispatch('showroom/fetchRegions')
    await store.dispatch('showroom/fetchShowroom', { id })
    await store.dispatch('showroom/fetchShowrooms')
    await store.dispatch('showroom/fetchSites', { id })
    await store.dispatch('showroom/fetchOperators', {
      showroom_id: id || $auth.user?.showroom_id,
      orderWorkers: false,
    })
    await store.dispatch('order/fetch_arrivals', { id })
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
    computedHeaders() {
      if (this.role_id !== 1) {
        return this.headers.filter((header) => header.value !== 'select_all')
      }
      return this.headers
    },
    showrooms() {
      return [
        {
          id: 4,
          name: 'АвтоПремиум',
        },
        {
          id: 2,
          name: 'Комфорт',
        },
        {
          id: 10,
          name: 'Автодом',
        },
        {
          id: 5,
          name: 'Авангард Юг',
        },
        {
          id: 7,
          name: 'Форсаж-Авто',
        },
        {
          id: 8,
          name: 'АвтоПремьер Авто',
        },
        {
          id: 13,
          name: 'АвтоПлюс',
        },
        {
          id: 14,
          name: 'Автопорт',
        },
        {
          id: 15,
          name: 'Авангард',
        },
        {
          id: 17,
          name: 'Автополе',
        },
        {
          id: 9,
          name: 'Victory',
          address: 'Victory',
          phone: null,
          logo: null,
          map_link: null,
          sort: 98,
        },
      ]
    },
    formTitle() {
      return this.editedIndex === -1 ? 'Добавлеение' : 'Изменение'
    },
    orders() {
      return this.$store.state.order.orders
    },
    statuses() {
      return [
        {
          id: 1,
          name: 'Новая',
        },
        {
          id: 3,
          name: 'Не отвечает',
        },
        {
          id: 7,
          name: 'Корзина',
        },
        {
          id: 8,
          name: 'Повтор',
        },
        {
          id: 1000,
          name: 'Передано',
        },
      ]
    },
    types() {
      return [
        {
          id: 13,
          name: 'SEO',
        },
        {
          id: 14,
          name: 'Реклама',
        },
        {
          id: 15,
          name: 'SEO/Реклама',
        },
        {
          id: 16,
          name: 'Victory',
        },
      ]
    },
    role() {
      return this.$store.state.auth.role
    },
    regions() {
      return this.$store.state.showroom.regions
    },
    sites() {
      if (
        Array.isArray(this.filter_showroom) &&
        this.filter_showroom.length > 0
      ) {
        return this.$store.state.showroom.sites.filter((site) =>
          this.filter_showroom.includes(site.to_showroom)
        )
      }
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
          href: '/crm/' + this.showroom_id + '/light-orders',
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
  created() {
    if (this.$route.query.from) {
      this.filter_from = this.$route.query.from
    }
    if (this.$route.query.to) {
      this.filter_to = this.$route.query.to
    }
    if (this.$route.query.search) {
      this.search = this.$route.query.search
    }
    if (this.$route.query.status) {
      const str = this.$route.query.status
      if (str.includes(',')) {
        const arr = str.split(',')
        this.filter_status = arr.map((x) => parseInt(x.trim()))
      } else {
        this.filter_status = this.$route.query.status
      }
      console.log(this.filter_status)
    }
    if (this.$route.query.site_id) {
      const str = this.$route.query.site_id
      const arr = str.split(',')
      this.filter_site = arr.map((x) => parseInt(x.trim()))
    }
    if (this.$route.query.to_showroom) {
      const str = this.$route.query.to_showroom
      const arr = str.split(',')
      this.to_showroom = arr.map((x) => parseInt(x.trim()))
    }
    if (this.$route.query.type_id) {
      const str = this.$route.query.type_id
      const arr = str.split(',')
      this.filter_type = arr.map((x) => parseInt(x.trim()))
    }
    if (this.$route.query.page) {
      this.page = parseInt(this.$route.query.page) || null
    }
    this.refresh_page()
  },
  mounted() {
    this.handleLoading()
    this.$echo
      .channel('orders_' + this.showroom_id)
      .listen('OrderCreated', (e) => {
        if (
          this.filter_status === null &&
          this.filter_from === null &&
          this.search === null &&
          this.filter_to === null &&
          this.page === 1 &&
          this.dialog !== true
        ) {
          this.refresh_page()
        } else {
          console.log('not reload')
        }
      })
    this.$echo
      .channel('orders_' + this.showroom_id)
      .listen('OrderProcessed', (e) => {
        //console.log('rr - ', this.isSearch === false && this.status_id < 1 && this.page === 1)
        if (
          this.filter_status === null &&
          this.filter_from === null &&
          this.search === null &&
          this.filter_to === null &&
          this.page === 1 &&
          this.dialog !== true
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
  methods: {
    handleRatingChange(props) {
      console.log(props)
      this.editedItem.call_count = parseInt(props.index + 1)
    },

    openPassOrderDialog() {
      if (this.role_id !== 1) return
      if (this.selectedOrders.length > 0) {
        this.distributeDialog = true
      } else {
        this.$toast.error('Сначала выберите заявку!!!')
      }
    },

    toggleSelectAll() {
      if (this.selectAll) {
        // Select all orders
        this.selectedOrders = this.orders.data.map((order) => order.id)
      } else {
        // Deselect all orders
        this.selectedOrders = []
      }

      // Update the indeterminate state based on the selection
      this.isIndeterminate =
        this.selectedOrders.length > 0 &&
        this.selectedOrders.length < this.orders.data.length
    },
    passOrders() {
      if (this.selectedOperator && this.selectedOrders.length > 0) {
        const payload = {
          operator_id: this.selectedOperator,
          order_ids: this.selectedOrders,
          showroom_id: this.showroom_id,
        }

        this.$store
          .dispatch('order/passOrders', { payload })
          .then((res) => {
            this.$toast.success('Заявка успешно передан оператору')
            this.distributeDialog = false
            this.selectedOperator = null
            this.refresh_page()
          })
          .catch((error) => {
            this.$toast.error('Произошла ошибка' + error?.message)
          })
      }
    },
    async refresh_page() {
      if (this.onProcess === true || document.hidden) {
        console.log('refresh on process or tab hidden')
        return
      }
      if (
        this.lastUpdate &&
        this.$moment().diff(this.lastUpdate, 'seconds') <= 10
      ) {
        this.onProcess = true
        const ago = this.$moment().diff(this.lastUpdate, 'seconds')
        setTimeout(async () => {
          await this.$store.dispatch('order/fetchLightOrders', {
            id: this.$route.params.id,
            query: this.$route.query,
          })
          this.lastUpdate = this.$moment()
          this.onProcess = false
          console.log('7.5 sec')
        }, 7500 - ago)
      } else {
        await this.$store.dispatch('order/fetchLightOrders', {
          id: this.$route.params.id,
          query: this.$route.query,
        })
        this.lastUpdate = this.$moment()
        console.log('moment')
      }
    },
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

      this.editedItem = Object.assign({}, item)
      if (this.editedItem?.call_count) {
        this.editedItem.call_count = parseInt(this.editedItem?.call_count)
      }

      if (this.editedItem.callback) {
        this.callback = this.$moment(this.editedItem.callback).format(
          'DD.MM.YYYY HH:mm'
        )
      }
      if (this.editedItem.last_call) {
        this.last_call = this.$moment(this.editedItem.last_call).format(
          'DD.MM.YYYY HH:mm'
        )
      }
      this.dialog = true
    },
    async changedPage() {
      let { query } = this.$route
      try {
        await this.$router.push({ query: { ...query, page: this.page } })
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') {
        }
      }
      await this.$store.dispatch('order/fetchLightOrders', {
        id: this.$route.params.id,
        query: this.$route.query,
      })
    },
    async doSearch() {
      this.handleLoading()
      const { query } = this.$route
      try {
        console.log(this.filter_from)
        var operator_id = null
        if (this.$auth.user?.role_id === 2 && !this.search) {
          operator_id = this.$auth.user?.id
        } else if (this.filter_operator) {
          operator_id = this.filter_operator
        }
        await this.$router.push({
          query: {
            ...(this.search && { search: this.search }),
            ...(operator_id && { operator_id: operator_id }),
            ...(this.filter_type?.length && {
              type_id: this.filter_type.join(','),
            }),
            ...(this.filter_site?.length && {
              site_id: this.filter_site.join(','),
            }),
            ...(this.filter_showroom?.length && {
              to_showroom: this.filter_showroom.join(','),
            }),
            ...(this.filter_from && { from: this.filter_from + ':00' }),
            ...(this.filter_to && { to: this.filter_to + ':59' }),
            page: 1,
          },
        })
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') {
        }
      } finally {
        await this.$store.dispatch('order/fetchLightOrders', {
          id: this.$route.params.id,
          query: this.$route.query,
        })
        this.page = 1
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
        this.componentKey += 1
        this.phone = null
        this.last_call = null
        this.callback = null
        this.$refs.form.resetValidation()
        this.valid = false
      })
    },
    async save() {
      await this.validate()
      if (this.valid) {
        this.editedItem.showroom_id = this.showroom_id
        if (this.last_call !== null) {
          const st = this.last_call + ':00'
          this.editedItem.last_call = this.$moment(
            st,
            'DD.MM.YYYY HH:mm:ss'
          ).isValid()
            ? this.$moment(st, 'DD.MM.YYYY HH:mm:ss').format(
                'YYYY-MM-DD HH:mm:ss'
              )
            : null
        } else {
          this.editedItem.last_call = null
        }
        if (this.callback !== null) {
          const cb = this.callback + ':00'
          this.editedItem.callback = this.$moment(
            cb,
            'DD.MM.YYYY HH:mm:ss'
          ).isValid()
            ? this.$moment(cb, 'DD.MM.YYYY HH:mm:ss').format(
                'YYYY-MM-DD HH:mm:ss'
              )
            : null
        } else {
          this.editedItem.callback = null
        }
        if (this.editedIndex === -1) {
          this.editedItem.operator_id =
            this.$auth.user?.role_id === 2 ? this.$auth.user?.id : null
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
      } else {
        this.$toast.error('Заполните обязательные поля')
      }
    },
    async sortRepeat() {
      let { query } = this.$route
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
          //
        }
      }

      await this.$store.dispatch('order/fetchLightOrders', {
        id: this.$route.params.id,
        query: this.$route.query,
      })
    },
    async distribute() {
      await this.validate()
      if (this.valid) {
        console.log(new Date().toLocaleString())
        this.editedItem.showroom_id = this.showroom_id
        if (this.last_call !== null) {
          const st = this.last_call + ':00'
          this.editedItem.last_call = this.$moment(
            st,
            'DD.MM.YYYY HH:mm:ss'
          ).isValid()
            ? this.$moment(st, 'DD.MM.YYYY HH:mm:ss').format(
                'YYYY-MM-DD HH:mm:ss'
              )
            : null
        }
        if (this.callback !== null) {
          const cb = this.callback + ':00'
          this.editedItem.callback = this.$moment(
            cb,
            'DD.MM.YYYY HH:mm:ss'
          ).isValid()
            ? this.$moment(cb, 'DD.MM.YYYY HH:mm:ss').format(
                'YYYY-MM-DD HH:mm:ss'
              )
            : null
        }
        if (this.editedIndex === -1) {
          this.editedItem.operator_id =
            this.$auth.user?.role_id === 2 ? this.$auth.user?.id : null
        }
        this.$store
          .dispatch('order/updateOrder', {
            item: this.editedItem,
          })
          .then((res) => {
            console.log(new Date().toLocaleString())
            const item = {
              showroom_id: this.toShowroom,
              order_id: this.editedItem?.id,
              isVictory: true,
            }
            if (this.$refs.copy_form.validate() && this.copyValid) {
              try {
                this.$store
                  .dispatch('order/distributeOrder', { item })
                  .then((res) => {
                    this.$toast.success('Заявка успешно передан в другой салон')
                    this.isOpenDistribute = false
                    this.dialog = false
                    this.close()
                    this.refresh_page()
                  })
                  .catch((error) => {
                    console.log('err ', error)
                    this.$toast.error('Произошла ошибка' + error?.message)
                  })
              } catch (e) {
                this.$toast.success('Произошла ошибка:' + e)
              }
            }
          })
          .catch((error) => {
            this.$toast.error('Заполните обязательные поля' + error)
          })
      } else {
        this.$toast.error('Заполните обязательные поля')
      }
    },
    distributeOpen() {
      this.isOpenDistribute = true
    },
    async toArrive() {
      this.editedItem.car_name = `${
        this.editedItem.mark?.name != null
          ? this.editedItem.mark?.name + ' '
          : ''
      }${
        this.editedItem.model?.name != null ? this.editedItem.model?.name : ''
      }`
      await this.$store
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
      if (
        this.role_id === 1 &&
        (item.call_heard === true || item.call_heard === 1)
      ) {
        return 'purple white--text'
      } else if (item.arrived === 1 && item.status_id === 5) {
        return 'yellow'
      }
    },
    getModels(markId = null) {
      this.editedItem.model_id = null
      if (markId !== 0) {
        this.$store.dispatch('property/fetchModels', { markId })
      }
    },

    async clearFilter() {
      this.filter_from = null
      this.filter_to = null
      this.filter_site = null
      this.filter_type = null
      this.filter_status = null
      this.filter_operator = null
      this.filter_showroom = []
      this.search = null
      this.page = 1
      try {
        await this.$router.push({
          query: {},
        })
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') {
        }
      } finally {
        await this.$store.dispatch('order/fetchLightOrders', {
          id: this.$route.params.id,
          query: {},
        })
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
        Оператор: row.operator?.first_name,
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
    setCallback(field, isDateTime = false) {
      this.callback = isDateTime
        ? this.$moment().format('DD.MM.YYYY HH:mm')
        : this.$moment().format('YYYY-MM-DD')
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
      let { query } = this.$route
      this.filter_status = id
      this.page = 1
      try {
        await this.$router.push({ query: { ...query, page: 1, status: id } })
      } catch (error) {
        if (error.name !== 'NavigationDuplicated') {
        }
      }
      console.log(this.$route.query)
      await this.$store.dispatch('order/fetchLightOrders', {
        id: this.$route.params.id,
        query: this.$route.query,
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

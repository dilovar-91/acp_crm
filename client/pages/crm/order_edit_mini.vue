<template>
  <v-container fluid>
    <v-btn
      elevation="2"
      fab
      color="success"
      outlined
      class="mt-14"
      raised
      fixed
      small
      @click="openRepeat()"
      v-if="repeats.length"
    >
      <v-badge color="error" :content="repeats.length">
        <v-icon>mdi-replay</v-icon>
      </v-badge>
    </v-btn>
    <v-row justify="center">
      <v-col cols="12" xl="10" md="12" lg="11" class="pt-0">
        <BreadCrumb :items="links" />
        <v-toolbar flat dense class="mt-0 mb-0">
          <v-icon color="blue" class="mr-2"> mdi-pen</v-icon>
          <v-toolbar-title
            >Изменение заявки (<span class="font-weight-bold">{{
              showroom?.name
            }}</span
            >)
          </v-toolbar-title>
          <v-spacer />
          <v-btn
            color="blue"
            class="mr-2 white--text"
            :to="'/crm/' + form?.showroom_id + '/light-orders'"
          >
            <v-icon>mdi-keyboard-return</v-icon>
            Вернутся в главную
          </v-btn>
        </v-toolbar>
      </v-col>
      <v-col cols="12" xl="10" md="12" lg="12" class="pt-0">
        <v-form ref="form" v-model="valid">
          <v-card
            class="pa-2 py-0"
            outlined
            style="border-bottom: 2px solid #007bff"
            tile
          >
            <v-row dense>
              <v-subheader class="mt-2 font-weight-bold">Тип</v-subheader>

              <v-col cols="12" md="2">
                <v-select
                  v-model="form.type_id"
                  :items="types"
                  item-value="id"
                  item-text="name"
                  menu-props="auto"
                  label="Тип заявка"
                  class="mt-2"
                  single-line
                  outlined
                  dense
                  required
                  hide-details
                />
              </v-col>

              <v-spacer />
              <v-btn target="_blank" :href="makeLink()" class="ma-2 mt-4">
                ФССП
                <v-icon dark right color="red">
                  mdi-shield-check-outline
                </v-icon>
              </v-btn>

              <v-btn
                target="_blank"
                :href="showroom?.map_link"
                class="ma-2 mt-4"
              >
                Яндекс карта
                <v-icon right color="success"> mdi-map-marker </v-icon>
              </v-btn>

              <v-btn
                target="_blank"
                href="https://cp.redsms.ru/dispatches/quick/new"
                class="ma-2 mt-4"
              >
                REDSMS
                <v-icon right color="warning">
                  mdi-message-text-outline
                </v-icon>
              </v-btn>
              <v-btn
                target="_blank"
                :href="'https://wa.me/' + form.phone?.match(/\d/g)?.join('')"
                class="ma-2 mt-4"
              >
                Whatsapp
                <v-icon color="success" right> mdi-whatsapp </v-icon>
              </v-btn>
              <v-btn
                target="_blank"
                :href="'https://t.me/+' + form.phone?.match(/\d/g)?.join('')"
                class="ma-2 mt-4"
              >
                Telegram
                <v-icon
                  color="primary"
                  right
                  style="transform: rotate(320deg); margin-top: -5px"
                >
                  mdi-send
                </v-icon>
              </v-btn>
              <v-btn
                v-if="form.site?.url"
                target="_blank"
                :href="setLink(form.site?.url)"
                class="ma-2 mt-4"
              >
                {{ form.site?.title }}
                <v-icon
                  color="primary"
                  right
                  style="transform: rotate(320deg); margin-top: -5px"
                >
                  mdi-web
                </v-icon>
              </v-btn>

              <v-spacer></v-spacer>
            </v-row>
          </v-card>
          <v-row no-gutters class="bottom-border">
            <v-col cols="12" md="9" sm="12">
              <v-card
                outlined
                style="
                  border-bottom: 2px solid #007bff;
                  border-right: 2px solid #007bff;
                "
                tile
                class="pa-2 py-3"
                align-self="scratch"
              >
                <v-row dense>
                  <v-col cols="12" sm="12" xl="4" md="4" class="py-1">
                    <v-text-field
                      v-model="form.client_name"
                      color="purple darken-2"
                      label="Клиент"
                      dense
                      outlined
                      hide-details
                    />
                  </v-col>
                  <v-col cols="12" sm="12" xl="2" md="2" class="pt-1">
                    <v-text-field
                      v-model="form.phone"
                      v-mask="'+7 ### ###-##-##'"
                      color="purple darken-2"
                      label="Сотовый"
                      :rules="[(v) => !!v || 'Введите сотового']"
                      required
                      dense
                      outlined
                      hide-details
                    >
                      <template slot="append">
                        <v-icon color="primary" @click="call(form.phone)"
                          >mdi-phone
                        </v-icon>
                      </template>
                    </v-text-field>
                  </v-col>
                  <v-col cols="12" sm="12" xl="2" md="2" class="pt-1">
                    <v-text-field
                      v-model="form.work_phone"
                      v-mask="'+7 ### ###-##-##'"
                      color="purple darken-2"
                      label="Рабочий телефон"
                      outlined
                      dense
                      hide-details
                      @maska="
                        form.work_phone = $event.target.dataset.maskRawValue
                      "
                    >
                      <template slot="append">
                        <v-icon color="primary" @click="call(form.work_phone)"
                          >mdi-phone
                        </v-icon>
                      </template>
                    </v-text-field>
                  </v-col>
                  <v-col cols="12" sm="12" xl="2" md="2" class="pt-1">
                    <v-text-field
                      v-model="form.phone_2"
                      v-mask="'+7 ### ###-##-##'"
                      color="purple darken-2"
                      label="Доп. номер"
                      required
                      dense
                      outlined
                      hide-details
                    >
                      <template slot="append">
                        <v-icon color="primary" @click="call(form.phone_2)"
                          >mdi-phone
                        </v-icon>
                      </template>
                    </v-text-field>
                  </v-col>
                  <v-col cols="12" sm="12" xl="2" md="2" class="pt-1">
                    <v-text-field
                      v-model="form.phone_3"
                      v-mask="'+7 ### ###-##-##'"
                      color="purple darken-2"
                      label="Доп. номер 2"
                      required
                      dense
                      outlined
                      hide-details
                    >
                      <template slot="append">
                        <v-icon color="primary" @click="call(form.phone_3)"
                          >mdi-phone
                        </v-icon>
                      </template>
                    </v-text-field>
                  </v-col>
                  <v-col cols="12" md="12" xl="12" sm="12">
                    <v-row dense>
                      <v-col cols="12" sm="12" xl="2" md="2" class="pa-0 mr-10">
                        Последный прозвон
                        <DTPicker
                          v-model="form.last_call"
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
                      <v-col cols="12" sm="12" xl="2" md="2" class="py-0 mr-10">
                        Перезвонить
                        <DTPicker
                          v-model="form.callback"
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
                      <v-col cols="12" sm="12" xl="2" md="2" class="py-0 mb-2">
                        <v-select
                          v-model="form.status_id"
                          :items="statuses"
                          item-text="name"
                          item-value="id"
                          label="Состояние заявки"
                          dense
                          required
                          :rules="[(v) => !!v || 'Выберите статус']"
                          clearable
                          outlined
                          hide-details="auto"
                          @change="changedStatus()"
                        />
                      </v-col>
                      <v-col
                        v-if="form.status_id === 7"
                        cols="12"
                        sm="12"
                        xl="2"
                        md="2"
                        class="py-0 mb-2"
                      >
                        <v-select
                          v-model="form.trash_id"
                          :items="trashes"
                          item-text="name"
                          item-value="id"
                          label="Тип корзины"
                          dense
                          outlined
                          required
                          hide-details="auto"
                        />
                      </v-col>
                      <v-col
                        v-if="form.status_id === 6 && role_id !== 2"
                        cols="12"
                        sm="12"
                        xl="2"
                        md="2"
                        class="py-0 mb-2"
                      >
                        <v-select
                          v-model="form.arrival_id"
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
                      <v-col cols="12" sm="12" xl="4" md="4" class="mt-4">
                        <v-select
                          v-model="form.operator_id"
                          :items="operators"
                          item-value="id"
                          :item-text="
                            (item) =>
                              item.last_name
                                ? item.first_name + ' ' + item.last_name
                                : item.first_name
                          "
                          dense
                          outlined
                          required
                          hide-details
                          label="Оператор"
                          :rules="[validateOperator]"
                          :disabled="
                            this.role_id === 2 && this.form.operator_id !== null
                          "
                        >
                        </v-select>
                      </v-col>
                      <v-col cols="12" sm="12" xl="12" md="12" class="py-0">
                        <v-textarea
                          v-model="form.comment"
                          rows="8"
                          class="mt-2"
                          outlined
                          dense
                          hide-details="auto"
                          label="Комментарий"
                        />
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>
              </v-card>
            </v-col>
            <v-col cols="12" xl="3" sm="12" md="3" class="bottom-border">
              <v-card class="pa-2 py-0" outlined tile lign-self="scratch">
                <v-row class="mt-2" dense>
                  <span class="ml-4 ads mb-1"
                    >Точка входа:
                    <a
                      v-if="form.entry_point"
                      :href="form.entry_point"
                      target="_blank"
                      rel="noreferrer"
                      >{{
                        form.entry_point
                          .toString()
                          .replace(/^(.*\/\/[^\/?#]*).*$/, '$1')
                      }}</a
                    ></span
                  ><br />

                  <span
                    v-if="form.utm_source || form.utm_medium"
                    class="ml-4 ads"
                    >Источник: {{ form.utm_source }} ({{
                      form.utm_medium
                    }})</span
                  ><br />

                  <span class="ml-4 ads">Кл. фраза: {{ form.utm_term }}</span>
                  <span class="ml-4 ads"
                    >Кампания : {{ form.utm_campaign }}</span
                  >

                  <v-col cols="12" sm="12" xl="12" md="12" class="py-0 pt-2">
                    <v-row dense>
                      <v-col cols="12" sm="12" xl="2" md="2">
                        <v-subheader class="font-weight-bold">
                          Сайт
                        </v-subheader>
                      </v-col>
                      <v-col cols="12" sm="12" xl="10" md="10">
                        <v-select
                          v-model="form.site_id"
                          :items="sites"
                          :item-text="
                            (item) =>
                              `${item.title} ${
                                item.description != null
                                  ? '(' + item.description + ')'
                                  : ''
                              }`
                          "
                          item-value="id"
                          dense
                          outlined
                          label="Сайт"
                          hide-details="auto"
                          clearable
                          no-data-text="Список пуст"
                          @click:clear="$nextTick(() => (form.site_id = null))"
                        />
                      </v-col>
                    </v-row>
                  </v-col>
                  <v-col cols="12" sm="12" xl="12" md="12" class="py-0">
                    <v-row dense>
                      <v-col cols="12" sm="12" xl="3" md="3">
                        <v-subheader class="font-weight-bold">
                          Марка 22
                        </v-subheader>
                      </v-col>
                      <v-col cols="12" sm="12" xl="9" md="9">
                        <v-select
                          v-model="form.mark_id"
                          :items="marks"
                          item-text="name"
                          item-value="id"
                          dense
                          outlined
                          label="Марка"
                          @change="
                            getModels(form.mark_id)
                            deleted = false
                          "
                          hide-details="auto"
                          clearable
                          no-data-text="Список пуст"
                          @click:clear="$nextTick(() => (form.mark_id = null))"
                        />
                      </v-col>
                    </v-row>
                  </v-col>
                  <v-col cols="12" sm="12" xl="12" md="12" class="py-0">
                    <v-row dense>
                      <v-col cols="12" sm="12" xl="3" md="3">
                        <v-subheader class="font-weight-bold">
                          Модель
                        </v-subheader>
                      </v-col>
                      <v-col cols="12" sm="12" xl="9" md="9">
                        <v-select
                          v-model="form.model_id"
                          :items="models"
                          item-text="name"
                          item-value="id"
                          label="Модель"
                          dense
                          outlined
                          no-data-text="Выберите марку"
                          clearable
                          hide-details="auto"
                          @click:clear="$nextTick(() => (form.model_id = null))"
                        />
                      </v-col>
                    </v-row>
                  </v-col>
                  <v-col cols="12" sm="12" xl="12" md="12" class="py-0">
                    <v-row dense>
                      <v-col cols="12" sm="12" xl="5" md="5">
                        <v-subheader class="font-weight-bold">
                          Кол-во звонков
                        </v-subheader>
                      </v-col>

                      <v-col cols="12" sm="12" xl="7" md="7">
                        <v-rating v-model="form.call_count">
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
                      </v-col>

                      <v-col cols="12" sm="12" xl="12" md="12">
                        <v-subheader class="font-weight-bold">
                          <v-checkbox
                            v-model="form.call_heard"
                            color="purple darken-1"
                            label="Прослушано"
                            class="ml-4"
                            v-if="role_id === 1"
                          >
                            Прослушано
                          </v-checkbox>
                        </v-subheader>
                      </v-col>
                    </v-row>
                  </v-col>

                  <v-col cols="12" xl="12" sm="12" md="12">
                    <v-btn
                      color="blue"
                      class="white--text my-2"
                      :loading="busy"
                      block
                      @click="save()"
                    >
                      Сохранить
                    </v-btn>
                    <v-row class="mb-2" dense>
                      <v-col cols="12" md="6">
                        <v-btn
                          width="170px"
                          color="warning"
                          :loading="busy"
                          dark
                          @click="distributeOpen(2)"
                        >
                          <v-icon>mdi-arrow-right</v-icon>
                          АМК
                        </v-btn>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-btn
                          width="170px"
                          color="red"
                          dark
                          @click="distributeOpen(4)"
                          :loading="busy"
                        >
                          <v-icon>mdi-arrow-right</v-icon>
                          АвтоПремиум
                        </v-btn>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-btn
                          width="170px"
                          color="green"
                          dark
                          @click="distributeOpen(5)"
                          :loading="busy"
                        >
                          <v-icon>mdi-arrow-right</v-icon>
                          Авангард Юг
                        </v-btn>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-btn
                          width="170px"
                          color="blue"
                          dark
                          @click="distributeOpen(7)"
                          :loading="busy"
                        >
                          <v-icon>mdi-arrow-right</v-icon>
                          Форсаж-Авто
                        </v-btn>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-btn
                          width="170px"
                          color="yellow"
                          @click="distributeOpen(8)"
                          :loading="busy"
                        >
                          <v-icon>mdi-arrow-right</v-icon>
                          АвтоПремьер
                        </v-btn>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-btn
                          width="170px"
                          color="purple"
                          dark
                          @click="distributeOpen(10)"
                          :loading="busy"
                        >
                          <v-icon>mdi-arrow-right</v-icon>
                          Автодом
                        </v-btn>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-btn
                          width="170px"
                          color="lime"
                          @click="distributeOpen(13)"
                          :loading="busy"
                        >
                          <v-icon>mdi-arrow-right</v-icon>
                          АвтоПлюс
                        </v-btn>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-btn
                          width="170px"
                          class="orange darken-3"
                          dark
                          @click="distributeOpen(14)"
                          :loading="busy"
                        >
                          <v-icon>mdi-arrow-right</v-icon>
                          Автопорт
                        </v-btn>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-btn
                          width="170px"
                          class="blue-grey darken-3"
                          dark
                          @click="distributeOpen(15)"
                          :loading="busy"
                        >
                          <v-icon>mdi-arrow-right</v-icon>
                          Авангард
                        </v-btn>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-btn
                          width="170px"
                          class="brown darken-2"
                          dark
                          @click="distributeOpen(17)"
                          :loading="busy"
                        >
                          <v-icon>mdi-arrow-right</v-icon>
                          Автополе
                        </v-btn>
                      </v-col>
                    </v-row>

                    <v-btn
                      color="warning"
                      class="white--text mb-4"
                      :loading="busy"
                      @click="save('return')"
                      block
                    >
                      <v-icon>mdi-content-save</v-icon>
                      вернуться к списку
                    </v-btn>
                  </v-col>
                </v-row>
              </v-card>
            </v-col>
          </v-row>
        </v-form>
      </v-col>

      <v-dialog v-model="dialog" max-width="400px">
        <v-card>
          <v-card-title>
            <span class="headline">Отправка смс</span>
            <p class="h5 text-primary">
              Отправка смс на {{ order && order.phone }}
            </p>
          </v-card-title>
          <v-card-text>
            <v-container>
              <v-form
                id="login-form"
                ref="sms_form"
                v-model="smsValid"
                lazy-validation
              >
                <v-row dense>
                  <v-col cols="12" class="py-0 text-right">
                    <v-autocomplete
                      v-model="form.template"
                      :items="[{ id: 1, name: 'Шаблон 1' }]"
                      item-value="id"
                      item-text="name"
                      placeholder="Шаблон"
                    />
                  </v-col>
                  <v-col cols="12" class="py-0 text-right">
                    <v-textarea
                      v-model="sms.text"
                      outlined
                      :rules="textRules"
                      dense
                      label="Текст *"
                      counter="70"
                      required
                    />
                  </v-col>
                </v-row>
              </v-form>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer />
            <v-btn color="primary" @click="sendSMS()"> Отправить</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="isCopyDialog" max-width="400px">
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
                      v-model="copyShowroom"
                      :items="
                        showrooms.filter((l) => l.id != $route.params.showroom)
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
            <v-btn color="primary" @click="copyOrder()">Передать</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-row>
    <v-row justify="center">
      <v-col cols="12" xl="10" md="12" lg="11" class="pt-0">
        <div
          style="color: #3b99e0; font-weight: bold; pointer: cursor"
          @click="loadHistory()"
        >
          Показать историю операций
          <v-icon v-if="history_active" color="primary" right
            >mdi-chevron-up</v-icon
          >
          <v-icon v-else color="primary" right>mdi-chevron-down</v-icon>
        </div>
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
                <td>
                  {{ $moment(item.created_at).format('DD.MM.YYYY HH:mm:ss') }}
                </td>
                <td>{{ item.user?.first_name }} {{ item.user?.last_name }}</td>
                <td>
                  <div v-if="item.description === '1'">Создана</div>
                  <div v-else-if="item.description === '2'">Просмотр</div>
                  <div v-else-if="item.description === '3'">Изменена</div>
                  <div v-else-if="item.description === '4'">Удалена</div>
                  <div v-else-if="item.description === '5'">
                    Входящий звонок
                  </div>
                  <div v-else-if="item.description === '6'">
                    Исходящий звонок
                  </div>
                  <div v-else-if="item.description === '7'">
                    Пропущенный звонок
                  </div>
                  <div v-else-if="item.description === '8'">Запись звонка</div>
                </td>
                <td v-if="item.description === '5' || item.description === '6'">
                  <table
                    class="activities"
                    width="100%"
                    v-if="item.properties?.create_time"
                  >
                    <thead>
                      <tr>
                        <th>Начало разговора</th>
                        <th>Конец разговора</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>
                          {{
                            $moment(
                              $moment.unix(item.properties?.create_time)
                            ).format('HH:mm:ss')
                          }}
                        </td>
                        <td>
                          {{
                            $moment(
                              $moment.unix(item.properties?.end_time)
                            ).format('HH:mm:ss')
                          }}
                        </td>
                      </tr>
                      <tr>
                        <td colspan="2">
                          <template
                            v-if="
                              item.recording_id &&
                              listen_record.includes(role_id)
                            "
                          >
                            <div
                              class="d-flex align-center justify-center mb-2"
                            >
                              <v-sheet class="ma-2 pa-2">
                                <audio
                                  :src="
                                    '/api/record2/' +
                                    showroom_id +
                                    '/' +
                                    item.recording_id
                                  "
                                  controls
                                  controlsList="nodownload"
                                />
                              </v-sheet>
                              <v-sheet class="ma-2 pa-2">
                                <v-icon
                                  @click="
                                    downloadRecord(
                                      '/record3/' +
                                        showroom_id +
                                        '/' +
                                        item.recording_id
                                    )
                                  "
                                >
                                  mdi-download
                                </v-icon>
                              </v-sheet>
                            </div>
                          </template>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
                <td v-else>
                  <table
                    class="activities"
                    width="100%"
                    v-if="
                      item.properties?.attributes &&
                      Array.isArray(item.properties?.attributes) == false
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
                          <td class="text-center">{{ activity[i] || i }}</td>
                          <td>
                            <template
                              v-if="item.properties.old?.hasOwnProperty(i)"
                            >
                              <template v-if="i === 'status_id'">
                                {{
                                  statuses.find(
                                    (l) => l.id == item.properties.old?.i
                                  )?.name
                                }}
                              </template>
                              <template v-else-if="i === 'trash_id'">
                                {{
                                  trashes.find(
                                    (l) => l.id == item.properties.old?.i
                                  )?.name
                                }}
                              </template>
                              <template v-else-if="i === 'drop_id'">
                                {{
                                  drops.find(
                                    (l) => l.id == item.properties.old?.i
                                  )?.name
                                }}
                              </template>
                              <template v-else-if="i === 'showroom_id'">
                                {{
                                  showrooms.find(
                                    (l) => l.id == item.properties.old?.i
                                  )?.name
                                }}
                              </template>
                              <template v-else-if="i === 'site_id'">
                                {{
                                  sites.find(
                                    (l) => l.id == item.properties.old?.i
                                  )?.title
                                }}
                              </template>
                              <template v-else-if="i === 'type_id'">
                                {{
                                  types.find(
                                    (l) => l.id == item.properties.old?.i
                                  )?.name
                                }}
                              </template>
                              <template v-else-if="i === 'operator_id'">
                                {{
                                  operators.find(
                                    (l) => l.id == item.properties.old?.i
                                  )?.first_name
                                }}
                              </template>
                              <template v-else-if="i === 'payment_method'">
                                {{
                                  payment_methods.find(
                                    (l) => l.id == item.properties.old?.i
                                  )?.name
                                }}
                              </template>
                              <template v-else-if="i === 'region_id'">
                                {{
                                  regions.find(
                                    (l) => l.id == item.properties.old?.i
                                  )?.name
                                }}
                              </template>
                              <template v-else-if="i === 'mark_id'">
                                {{
                                  marks.find(
                                    (l) => l.id == item.properties.old?.i
                                  )?.name
                                }}
                              </template>
                              <template v-else-if="i === 'model_id'">
                                {{
                                  models.find(
                                    (l) => l.id == item.properties.old?.i
                                  )?.name
                                }}
                              </template>
                              <template v-else-if="i === 'approved'">
                                <span v-if="item.properties?.old[i] == 1"
                                  >Да</span
                                >
                                <span v-if="item.properties?.old[i] == 0"
                                  >Нет</span
                                >
                              </template>

                              <template v-else-if="i == 'canceled'">
                                <span v-if="item.properties.old[i] == 1"
                                  >Да</span
                                >
                                <span v-if="item.properties.old[i] == 0"
                                  >Нет</span
                                >
                              </template>
                              <template v-else-if="i === 'commercial_offer'">
                                <span v-if="item.properties.old[i] == 1"
                                  >Да</span
                                >
                                <span v-if="item.properties.old[i] == 0"
                                  >Нет</span
                                >
                              </template>
                              <template v-else-if="i === 'arrived'">
                                <span v-if="item.properties.old[i] == 1"
                                  >Да</span
                                >
                                <span v-if="item.properties.old[i] == 0"
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
                                  $moment(item.properties.old[i]).format(
                                    'DD.MM.YYYY HH:mm:ss'
                                  )
                                }}
                              </template>
                              <template
                                v-else-if="
                                  i === 'last_call' &&
                                  item.properties.old[i] !== null
                                "
                              >
                                {{
                                  $moment(item.properties.old[i]).format(
                                    'DD.MM.YYYY HH:mm:ss'
                                  )
                                }}
                              </template>
                              <template
                                v-else-if="
                                  i === 'will_arrive' &&
                                  item.properties.old[i] !== null
                                "
                              >
                                {{
                                  $moment(item.properties.old[i]).format(
                                    'DD.MM.YYYY'
                                  )
                                }}
                              </template>
                              <template
                                v-else-if="
                                  i === 'arrived_date' &&
                                  item.properties.old[i] !== null
                                "
                              >
                                {{
                                  $moment(item.properties.old[i]).format(
                                    'DD.MM.YYYY'
                                  )
                                }}
                              </template>
                              <template v-else>
                                {{ item.properties.old[i] }}
                              </template>
                            </template>
                          </td>
                          <td>
                            <template v-if="i === 'status_id'">
                              {{ statuses.find((l) => l.id === row)?.name }}
                            </template>
                            <template v-else-if="i === 'trash_id'">
                              {{ trashes.find((l) => l.id === row)?.name }}
                            </template>
                            <template v-else-if="i === 'drop_id'">
                              {{ drops.find((l) => l.id === row)?.name }}
                              {{ row }}
                            </template>
                            <template v-else-if="i === 'showroom_id'">
                              {{ showrooms.find((l) => l.id === row)?.name }}
                            </template>
                            <template v-else-if="i === 'site_id'">
                              {{ sites.find((l) => l.id === row)?.title }}
                            </template>
                            <template v-else-if="i === 'type_id'">
                              {{ types.find((l) => l.id === row)?.name }}
                            </template>
                            <template v-else-if="i === 'operator_id'">
                              {{
                                operators.find((l) => l.id === row)?.first_name
                              }}
                            </template>
                            <template v-else-if="i === 'payment_method'">
                              {{
                                payment_methods.find((l) => l.id === row)?.name
                              }}
                            </template>
                            <template v-else-if="i === 'region_id'">
                              {{ regions.find((l) => l.id === row)?.name }}
                            </template>
                            <template v-else-if="i === 'mark_id'">
                              {{ marks.find((l) => l.id === row)?.name }}
                            </template>
                            <template v-else-if="i === 'model_id'">
                              {{
                                models.find((l) => l.id === row)?.name || row
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
                            <template v-else-if="i === 'commercial_offer'">
                              <span v-if="row === 1">Да</span>
                              <span v-if="row === 0">Нет</span>
                            </template>
                            <template v-else-if="i === 'arrived'">
                              <span v-if="row === 1">Да</span>
                              <span v-if="row === 0">Нет</span>
                            </template>
                            <template
                              v-else-if="i === 'callback' && row !== null"
                            >
                              {{ $moment(row).format('DD.MM.YYYY HH:mm:ss') }}
                            </template>
                            <template
                              v-else-if="i === 'last_call' && row !== null"
                            >
                              {{ $moment(row).format('DD.MM.YYYY HH:mm:ss') }}
                            </template>
                            <template
                              v-else-if="i === 'will_arrive' && row !== null"
                            >
                              {{ $moment(row).format('DD.MM.YYYY') }}
                            </template>
                            <template
                              v-else-if="i === 'arrived_date' && row !== null"
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
        <v-dialog v-model="isOpenDistribute" max-width="400px">
          <v-card>
            <v-card-title>
              <span class="headline"
                >Передача заявки
                {{
                  showrooms.find((l) => l.id === this.to_showroom)?.name
                }}</span
              >
            </v-card-title>
            <v-card-text>
              <p>
                Вы уверены что передать эту заявку в
                {{ showrooms.find((l) => l.id === this.to_showroom)?.name }}?
              </p>
            </v-card-text>

            <v-card-actions>
              <v-spacer />
              <v-btn color="success" @click="distribute()">Да</v-btn>
              <v-btn color="error" @click="closeDistribute()">Нет</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-col>
    </v-row>

    <v-dialog light v-model="repeatDialog" max-width="850">
      <v-card flat style="background-color: #fdfdfd" class="px-3">
        <p class="pt-4 text-center font-weight-bold">
          Заявка №{{ repeatItem.id }}: повторы
        </p>
        <template>
          <v-simple-table style="background-color: #fdfdfd">
            <template v-slot:default>
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
                    {{ $moment(item.created_at).format('DD.MM.YYYY HH:mm:ss') }}
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
  </v-container>
</template>

<script>
import DTPicker from '~/components/DTPicker'
import BreadCrumb from '~/components/BreadCrumb'
import DPicker from '~/components/DPicker'
import { activity } from '~/configs/activity.json'

export default {
  name: 'EditOrder',
  components: {
    DTPicker,
    DPicker,
    BreadCrumb,
  },
  layout: 'edit',
  middleware: 'auth',
  data() {
    return {
      activity,
      dialog: false,
      isCopyDialog: false,
      isOpenDistribute: false,
      repeatDialog: false,
      copyShowroom: null,
      history_active: false,
      searchText: '',
      smsValid: false,
      copyValid: false,
      distributeValid: false,
      to_showroom: null,
      valid: false,
      order: {},
      repeats: [],
      phone: '',
      test: '',
      busy: false,
      open_dtp: false,
      open_dtp_callback: false,
      showTimePanel: false,
      listen_record: [1, 2, 3, 6],
      types1: ['Звонок', 'Кредит', 'Экспресс-кредит', 'Seo'],
      sms: {
        text: '',
        phone: '',
        phone_extension: '',
        template: 0,
      },
      repeatItem: {
        id: '',
        phone: '',
      },
      periods: [
        {
          id: 1,
          value: 0.5,
          name: '6 месяцев',
        },
        {
          id: 2,
          value: 1,
          name: '1 год',
        },
        {
          id: 3,
          value: 2,
          name: '2 года',
        },
        {
          id: 4,
          value: 3,
          name: '3 года',
        },
        {
          id: 5,
          value: 4,
          name: '4 года',
        },
        {
          id: 6,
          value: 5,
          name: '5 лет',
        },
        {
          id: 7,
          value: 6,
          name: '6 лет',
        },
        {
          id: 8,
          value: 7,
          name: '7 лет',
        },
        {
          id: 9,
          value: 8,
          name: '8 лет',
        },
        {
          id: 10,
          value: 9,
          name: '9 лет',
        },
        {
          id: 11,
          value: 10,
          name: '10 лет',
        },
        {
          id: 12,
          value: 12,
          name: '12 лет',
        },
        {
          id: 14,
          name: '14 лет',
        },
        {
          id: 15,
          name: '15 лет',
        },
      ],
      form: {
        client_name: '',
        email: '',
        phone: '',
        work_phone: '',
        operator_id: '',
        birthday: '',
        age: '',
        supouse_name: '',
        supouse_birthday: '',
        region_id: '',
        region_ip_id: '',
        live_region: '',
        car_number: '',
        work_place: '',
        work_position: '',
        official_income: '',
        work_experience: '',
        ads_source: '',
        mark_id: '',
        model_id: '',
        car_year: '',
        price: '',
        complectation: '',
        initial_fee: '',
        credit_period: null,
        showroom_id: '',
        payment_cash: '',
        payment_credit: '',
        gifts: '',
        happy_hour: '',
        credit_deffer: '',
        job_loss_insurance: '',
        last_call: '',
        last_call_picker: '',
        callback: '',
        callback_picker: '',
        will_arrive: '',
        arrived: '',
        arrived_date: '',
        status_id: '',
        date_of_sale: '',
        call_heard: '',
        entry_point: '',
        utm_source: '',
        utm_medium: '',
        utm_term: '',
        utm_campaign: '',
        type_id: '',
        arrival_id: '',
        drop_id: '',
        comment: '',
        general_comment: '',
        form: '',
      },
      payment_methods: [
        { id: 7, name: 'Не определено' },
        { id: 1, name: 'Наличными' },
        { id: 2, name: 'В кредит' },
        { id: 3, name: 'Кредит(Скидка)' },
        { id: 4, name: 'Лизинг' },
        { id: 5, name: 'Не дозвон' },
        { id: 6, name: 'Повтор' },
        { id: 8, name: 'ЛНР/ДНР' },
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
      rules: {
        first_name: [(val) => (val || '').length > 0 || 'Введите имя клиента'],
        phone: [(val) => (val || '').length === 18 || 'Введите телефон'],
      },
      dateTimeMask: [
        /\d/,
        /\d/,
        '.',
        /\d/,
        /\d/,
        '.',
        /\d/,
        /\d/,
        /\d/,
        /\d/,
        ' ',
        /\d/,
        /\d/,
        ':',
        /\d/,
        /\d/,
      ],
      dateMask: [/\d/, /\d/, '.', /\d/, /\d/, '.', /\d/, /\d/, /\d/, /\d/],
      apiForm: {
        ext_number: null,
        phone: null,
        from_number: null,
        sip: null,
      },
      textRules: [
        (v) => !!v || 'Введите текст сообщений',
        (v) =>
          (v && v.length <= 70) ||
          'Текст сообщений не должно превышать 70 символов!!!',
      ],
    }
  },
  async fetch({ store, $axios, $moment, $auth, params: { showroom, id } }) {
    await store.dispatch('user/toggle', false)
    await store.dispatch('order/fetchTypes')
    await store.dispatch('order/fetchStatuses')
    await store.dispatch('order/fetchTrashes')
    await store.dispatch('order/fetchDrops')
    await store.dispatch('property/fetchMarks')
    await store.dispatch('property/fetchAllModels')
    await store.dispatch('showroom/fetchRegions')
    await store.dispatch('showroom/fetchShowrooms', {})
    await store.dispatch('showroom/fetchSites', {
      id: showroom || this.form?.showroom_id,
    })
    await store.dispatch('credit/fetchBanks')
    await store.dispatch('showroom/fetchOperators', {
      showroom_id: showroom || this.form?.showroom_id,
      orderWorkers: false,
    })
    await store.dispatch('permission/fetchPermissions')
  },
  created() {
    this.$axios
      .get(
        '/order/' +
          this.$route.params?.showroom +
          '/detail/' +
          this.$route.params?.id
      )
      .then(async (response) => {
        this.order = response.data

        this.form = Object.assign({}, this.order)
        if (this.form.birthday !== null) {
          this.form.birthday = this.$moment(this.form.birthday).format(
            'DD.MM.YYYY'
          )
        }
        if (this.form.last_call !== null) {
          this.form.last_call = this.$moment(this.form.last_call).format(
            'DD.MM.YYYY HH:mm'
          )
        }
        if (this.form.callback !== null) {
          this.form.callback = this.$moment(this.form.callback).format(
            'DD.MM.YYYY HH:mm'
          )
        }
        if (this.order?.mark_id !== null) {
          this.$store.dispatch('property/fetchModels', {
            markId: this.order?.mark_id,
          })
        }
        if (this.order?.tradein_mark_id !== null) {
          this.$store.dispatch('property/fetchTradeInModels', {
            markId: this.order?.tradein_mark_id,
          })
        }

        this.repeatItem.id = this.order.id
        this.repeatItem.phone = this.order.phone
        this.repeatItem.showroom_id = this.order.showroom_id
        await this.$axios
          .post('orders/repeats', { item: this.repeatItem })
          .then(async (response2) => {
            this.repeats = response2.data
          })
      })
  },

  computed: {
    payment_credit() {
      return this.form.payment_credit
    },
    payment_cash() {
      return this.form.payment_cash
    },
    role_id() {
      return this.$auth.user?.role_id
    },
    user_id() {
      return this.$auth.user?.id
    },
    types() {
      return this.$store.state.order.types
    },
    drops() {
      return this.$store.state.order.drops
    },
    banks() {
      return this.$store.state.credit.banks
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
    trashes() {
      return this.$store.state.order.trashes
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
    tradein_models() {
      return this.$store.state.property.tradein_models
    },
    showrooms() {
      return [
        {
          id: 4,
          name: 'АвтоПремиум',
        },
        {
          id: 2,
          name: 'АМК',
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
    showroom_id() {
      return this.$route.params.showroom
    },
    showroom() {
      return this.$store.state.showroom.showrooms.find(
        (l) => l.id === this.order?.showroom_id
      )
    },
    operators() {
      if (this.role_id === 2 && this.form.operator_id === null) {
        return this.$store.state.showroom.operators.filter(
          (l) => l.id === this.user_id
        )
      } else return this.$store.state.showroom.operators
    },
    histories() {
      return this.$store.state.order.histories
    },

    links() {
      return [
        {
          text: 'Главная',
          disabled: false,
          href: '/',
        },
        {
          text: this.showroom?.name,
          disabled: true,
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
      ]
    },
  },

  watch: {
    payment_credit(value) {
      if (value) {
        this.form.payment_cash = false
      }
    },
    payment_cash(value) {
      if (value) {
        this.form.payment_credit = false
        this.form.gifts = false
        this.form.happy_hour = false
        this.form.credit_deffer = false
        this.form.job_loss_insurance = false
      }
    },
  },

  methods: {
    async save(after) {
      try {
        if (!this.$refs.form.validate() || !this.valid) {
          this.$toast.error('Заполните обязательные поля!!!')
          return
        }
        this.busy = true
        if (this.form.last_call !== null) {
          const st = this.form.last_call + ':00'
          this.form.last_call = this.$moment(st, 'DD.MM.YYYY HH:mm:ss').format(
            'YYYY-MM-DD HH:mm:ss'
          )
        }
        if (this.form.callback !== null) {
          const cb = this.form.callback + ':00'
          this.form.callback = this.$moment(cb, 'DD.MM.YYYY HH:mm:ss').format(
            'YYYY-MM-DD HH:mm:ss'
          )
        }
        await this.$store.dispatch('order/update', { item: this.form })
        this.$toast.success('Заявка успешно обновлена')
        if (after === 'new') {
          this.$refs.form.reset()
          this.$refs.form.resetValidation()
          await this.$router.push({ name: 'create-order' })
        } else if (after === 'return') {
          this.$router.push('/crm/' + this.form?.showroom_id + '/light-orders')
        }
        if (this.form.last_call !== null) {
          this.form.last_call = this.$moment(this.form.last_call).format(
            'DD.MM.YYYY HH:mm'
          )
        }
        if (this.form.callback !== null) {
          this.form.callback = this.$moment(this.form.callback).format(
            'DD.MM.YYYY HH:mm'
          )
        }
      } catch (e) {
        //$sentry.captureException(e)
        this.$toast.error('Произошла ошибка:' + e?.message)
      } finally {
        this.busy = false
      }
    },
    setNow(field, isDateTime = false) {
      console.log('fired')
      this.form[field] = isDateTime
        ? this.$moment().format('DD.MM.YYYY HH:mm')
        : this.$moment().format('YYYY-MM-DD')
      this.open_dtp = false
      this.open_dtp_callback = false
    },

    handleRatingChange(props) {
      console.log(props)
      this.form.call_count = parseInt(props.index + 1)
    },

    setHour(field) {
      console.log('fired')
    },
    setLink(url) {
      if (!url) return
      if (!/^https:\/\//i.test(url) && !/^http:\/\//i.test(url)) {
        url = 'https://' + url
      }
      return url
    },
    makeLink() {
      if (this.form.client_name === null) return
      let fio = this.form.client_name?.split(' ')
      let birthday = null
      if (this.form?.birthday) {
        birthday = '&is%5Bdate%5D=' + this.form?.birthday
      }
      return `https://fssp.gov.ru/iss/ip?is[variant]=1&is%5Bregion_id%5D%5B0%5D=-1&${
        fio.length ? 'is%5Blast_name%5D=' + fio[0] : ''
      }${fio.length > 0 ? '&is%5Bfirst_name%5D=' + fio[1] : ''}${
        fio.length > 1 ? '&is%5Bpatronymic%5D=' + fio[2] : ''
      }${birthday ? birthday : ''}`
    },
    async call(phone) {
      console.log(this.$auth.user?.work_place)
      this.apiForm.phone = phone
      this.apiForm.showroom_id = this.order?.showroom_id
      this.apiForm.ext_number = this.$auth.user?.work_place
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
          this.$toast.error('Произошла ошибка...')
          /// reject(error)
        })
    },
    async sendSMS() {
      this.$refs.sms_form.validate()
      if (this.smsValid === true) {
        await axios
          .post(
            '/send/sms',
            {
              phone: this.order.phone,
              ext_number: this.order.operator?.phone_extension ?? null,
              text: this.sms.text,
              template: this.sms.template,
            },
            {
              headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
              },
            }
          )
          .then((response) => {
            if (response.status === 200) {
              this.$toast.success('Смс отправлен')
            }
            this.sms.text = ''
            this.sms.template = 0
            console.log(response.data)
          })
          .catch((error) => {
            this.$toast.error('Произошла ошибка!!!' + error)
          })
      } else {
        this.$toast.error('Запоните поля!')
      }
    },
    calc(type, bank) {
      let percent = this.form['percent_' + bank] / 12 / 100
      var period = 1
      if (typeof this.form.credit_period === 'object') {
        period = this.form.credit_period?.id * 12
      } else {
        period = this.form.credit_period * 12
      }

      let result =
        (this.form.price - this.form.initial_fee) *
        (percent + percent / ((1 + percent) ** period - 1))
      switch (type) {
        case 'payment':
          if (result <= 0) return
          return this.currency(Math.round(result)) || null
        case 'overpay':
          if (result <= 0) return
          return (
            this.currency(
              Math.round(
                result * period - (this.form.price - this.form.initial_fee)
              )
            ) || null
          )
        default:
          return
      }
    },
    currency(value) {
      if (isNaN(value)) {
        return value
      }
      let val = (value / 1).toFixed(0).replace('.', ',')
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ')
    },
    openBanki() {
      if (this.form.car_number) {
        let car_number = this.form.car_number.split(' ').join('')
        let link = `https://www.banki.ru/insurance/order/auto/type/osago/short-flow/steps/auto?licensePlate=${car_number}&source=main_widget`
        window.open(link, '_blank')
      }
    },
    async loadHistory() {
      if (!this.history_active) {
        await this.$store.dispatch('order/fetchHistory', {
          id: this.$route.params.id,
          showroom_id: this.$route.params.showroom,
        })
      }
      this.history_active = !this.history_active
    },

    getModels(markId = null) {
      this.form.model_id = null
      if (markId !== 0) {
        this.$store.dispatch('property/fetchModels', { markId })
      }
    },

    getTradeInModels(markId = null) {
      this.form.tradin_model_id = null
      if (markId !== 0) {
        this.$store.dispatch('property/fetchTradeInModels', { markId })
      }
    },

    async downloadRecord(url) {
      try {
        const response = await this.fetchMP3File(url)
        this.saveMP3Locally(response.data, 'downloaded.mp3')
      } catch (error) {
        console.error('Error downloading MP3:', error)
      }
    },
    fetchMP3File(url) {
      return this.$axios.get(url, {
        responseType: 'arraybuffer',
      })
    },
    saveMP3Locally(data, filename) {
      const blob = new Blob([data], { type: 'audio/mpeg' })
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = filename
      a.click()
      window.URL.revokeObjectURL(url)
    },
    async copyOrder() {
      const item = {
        showroom_id: this.copyShowroom,
        order_id: this.$route.params.id,
      }
      if (this.$refs.copy_form.validate() && this.copyValid) {
        try {
          this.$store
            .dispatch('order/copyOrder', { item })
            .then((res) => {
              this.$toast.success('Заявка успешно продублирован')
            })
            .catch((error) => {
              console.log('err ', error)
              this.$toast.error('Произошла ошибка' + error?.message)
            })
        } catch (e) {
          this.$toast.success('Произошла ошибка:' + e)
        }
      }
    },

    distributeOpen(to) {
      this.isOpenDistribute = true
      this.to_showroom = to
      this.save()
    },

    closeDistribute() {
      this.isOpenDistribute = false
      this.to_showroom = null
    },
    async distribute() {
      const item = {
        showroom_id: this.to_showroom,
        order_id: this.$route.params?.id,
      }
      if (this.to_showroom !== null) {
        try {
          this.$store
            .dispatch('order/distributeOrder', { item })
            .then((res) => {
              this.$toast.success('Заявка успешно передан в другой салон')
              this.closeDistribute()
            })
            .catch((error) => {
              console.log('err ', error)
              this.$toast.error('Произошла ошибка' + error?.message)
            })
        } catch (e) {
          await this.$toast.success('Произошла ошибка:' + e)
        }
      } else {
        await this.$toast.success('Салон не выбран')
      }
    },

    validateOperator(value) {
      if (value === null) {
        return 'Выберите оператора'
      }
      return true // Validation passes
    },

    openRepeat() {
      this.repeatDialog = true
    },
  },
}
</script>
<style lang="scss">
$namespace: 'mx';
$default-color: #555;
$primary-color: #1284e7;
@import '~vue2-datepicker/scss/index.scss';
.ads {
  font: 13px/1.2em Arial;
  font-weight: bold;
}

.bottom-border {
  border-bottom: 2px solid rgb(0, 123, 255);
  background-color: white;
}

.activities {
  border-collapse: collapse;
  width: 100%;
}

.activities td,
.activities th {
  border: 1px solid #ddd;
  padding: 1px;
  text-align: center;
}

.activities tr:nth-child(even) {
  background-color: #f2f2f2;
}

.activities tr:hover {
  background-color: #ddd;
}

.activities th {
  padding-top: 1px;
  padding-bottom: 1px;
  background-color: #04aa6d;
  color: white;
  text-align: center;
}
</style>

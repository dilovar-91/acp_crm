<template>
  <v-container fluid>

    <v-row justify="center">
      <v-col cols="12" xl="10" md="12" lg="11" class="pt-0">
        <BreadCrumb :items="links"/>
        <v-toolbar flat dense class="mt-0 mb-0">
          <v-icon color="blue" class="mr-2"> mdi-pen</v-icon>
          <v-toolbar-title>Изменение заявки (<span class="font-weight-bold">{{ showroom?.name }}</span>)
          </v-toolbar-title>
          <v-spacer/>
          <v-btn color="blue" class="mr-2 white--text" :to="'/crm/'+ form?.showroom_id+'/orders-mini/' + form.site?.agency_id">
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

              <v-spacer/>
              <v-btn
                target="_blank"
                :href="makeLink()"
                class="ma-2 mt-4"
              >
                ФССП
                <v-icon
                  dark
                  right
                  color="red"
                >
                  mdi-shield-check-outline
                </v-icon>
              </v-btn>

              <v-btn
                target="_blank"
                :href="showroom?.map_link"
                class="ma-2 mt-4"
              >
                Яндекс карта
                <v-icon
                  right
                  color="success"
                >
                  mdi-map-marker
                </v-icon>
              </v-btn>

              <v-btn
                target="_blank"
                href="https://cp.redsms.ru/dispatches/quick/new"
                class="ma-2 mt-4"
              >
                REDSMS
                <v-icon
                  right
                  color="warning"
                >
                  mdi-message-text-outline
                </v-icon>
              </v-btn>
              <v-btn
                target="_blank"
                :href="'https://wa.me/' + form.phone?.match(/\d/g)?.join('')"
                class="ma-2 mt-4"
              >
                Whatsapp
                <v-icon
                  color="success"
                  right
                >
                  mdi-whatsapp
                </v-icon>
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
                :href="form.site?.url"
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

                  <v-col cols="12" sm="12" xl="6" md="6" class="py-1">
                    <v-text-field
                      v-model="form.client_name"
                      color="purple darken-2"
                      label="Клиент"
                      dense
                      outlined
                      hide-details
                    />
                  </v-col>
                  <v-col cols="12" sm="12" xl="3" md="3" class="pt-1">
                    <v-text-field
                      v-model="form.phone"
                      v-mask="'+7 ### ###-##-##'"
                      color="purple darken-2"
                      label="Сотовый"
                      :rules="[v => !!v || 'Введите сотового']"
                      required
                      dense
                      outlined
                      hide-details
                    >
                      <template

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
                  <v-col cols="12" sm="12" xl="3" md="3" class="pt-1">
                    <v-text-field
                      v-model="form.work_phone"
                      v-mask="'+7 ### ###-##-##'"
                      color="purple darken-2"
                      label="Рабочий телефон"
                      outlined
                      dense
                      hide-details
                      @maska="form.work_phone = $event.target.dataset.maskRawValue"
                    >
                      <template
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
                </v-row>
              </v-card>

              <v-card
                outlined
                style="
                border-bottom: 2px solid #007bff;
                border-right: 2px solid #007bff;
              "
                tile
                class="pa-2 py-1"
              >

                <v-row dense>

                  <v-col cols="12" sm="12" xl="3" md="3" class="pa-3">
                    ДР
                    <DPicker
                      v-model="form.birthday"
                      value-type="DD.MM.YYYY"
                      type="date"
                      format="DD.MM.YYYY"
                      @setNow="setNow('birthday')"
                    />
                  </v-col>
                  <v-col cols="12" sm="12" xl="4" md="4" class="mt-2">
                    <v-autocomplete
                      v-model="form.region_id"
                      :items="regions"
                      menu-props="auto"
                      item-value="id"
                      item-text="name"
                      no-data-text="Не найдено"
                      hide-details
                      single-line
                      label="Место регистрации"
                      dense
                      outlined
                    />
                  </v-col>
                  <v-col cols="12" sm="12" xl="4" md="4" class="mt-2">
                    <v-autocomplete
                      v-model="form.live_region"
                      :items="regions"
                      menu-props="auto"
                      item-value="id"
                      item-text="name"
                      no-data-text="Список пуст!"
                      hide-details
                      single-line
                      label="Место жительства"
                      dense
                      outlined
                    />
                  </v-col>


                </v-row>
              </v-card>
              <v-card
                outlined
                style="
                border-bottom: 2px solid #007bff;
                border-right: 2px solid #007bff;
              "
                tile
                class="pa-2 py-1"
              >
                <v-row dense>
                  <v-col cols="12" sm="12" xl="12" md="12">
                    <v-row dense>
                      <v-col cols="12" sm="12" xl="3" md="3" class="py-0 mt-1">
                        <v-text-field
                          v-model="form.work_place"
                          dense
                          outlined
                          hide-details="auto"
                          label="Организация"
                        />
                      </v-col>
                      <v-col cols="12" sm="12" xl="3" md="3" class="py-0 mt-1">
                        <v-text-field
                          v-model="form.work_position"
                          dense
                          outlined
                          hide-details="auto"
                          label="Должность"
                        />
                      </v-col>
                      <v-col cols="12" sm="12" xl="3" md="3" class="py-0 mt-1">
                        <v-text-field
                          v-model="form.official_income"
                          dense
                          outlined
                          hide-details="auto"
                          label="Официальный доход"
                        />
                      </v-col>
                      <v-col cols="12" sm="12" xl="3" md="3" class="py-1">
                        <v-text-field
                          v-model="form.work_experience"
                          dense
                          outlined
                          hide-details="auto"
                          label="Стаж работы"
                        />
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>
              </v-card>
              <v-card
                outlined
                style="
                border-bottom: 2px solid #007bff;
                border-right: 2px solid #007bff;
              "
                tile
                class="pa-2 py-1"
              >
                <v-row dense>
                  <v-col cols="12" sm="12" xl="12" md="12">

                    <v-col cols="12" sm="12" xl="12" md="12" class="py-1">
                      <p class="text-center mb-0 font-weight-bold">Trade-In</p>
                    </v-col>
                    <v-row dense>
                      <v-col cols="12" sm="12" xl="1" md="1" class="py-1
              d-flex flex-row  px-auto">
                        <a href="https://www.avito.ru/evaluation/cars" class="mr-2"
                           target="_blank" rel="noreferrer">
                          <v-img style="border: #4caf50 1px solid; border-radius: 6px; cursor: pointer;"
                                 src="/images/logo-opoveshcheniiaavito.png" max-width="45px"/>
                        </a>

                        <a href="https://auto.ru/cars/evaluation/"
                           target="_blank" rel="noreferrer">
                          <v-img style="border: red 1px solid; border-radius: 6px; cursor: pointer;"
                                 src="/images/autru.png" max-width="45px"/>
                        </a>


                      </v-col>
                      <v-col cols="12" sm="12" xl="2" md="2" class="py-0 mt-2 pl-4">
                        <v-text-field color="primary" dense
                                      outlined
                                      label="Гос. номер"
                                      hide-details="auto" v-model="form.car_number"
                                      append-icon="mdi-magnify"
                                      @click:append="openBanki()"
                        />
                      </v-col>
                      <v-col cols="12" sm="12" xl="2" md="2" class="py-0 mt-2 pl-4">
                        <v-select
                          v-model="form.tradein_mark_id"
                          :items="marks"
                          item-text="name"
                          item-value="id"
                          dense
                          @change="
                            getTradeInModels(form.tradein_mark_id);
                          "
                          outlined
                          label="Марка"
                          hide-details="auto"
                          clearable
                          no-data-text="Список пуст"
                          @click:clear="$nextTick(() => (form.tradein_mark_id = null))"
                        />
                      </v-col>
                      <v-col cols="12" sm="12" xl="2" md="2" class="py-0 mt-2">
                        <v-select
                          v-model="form.tradein_model_id"
                          :items="tradein_models"
                          item-text="name"
                          item-value="id"
                          label="Модель"
                          dense
                          outlined
                          no-data-text="Выберите модель"
                          clearable
                          hide-details="auto"
                          @click:clear="$nextTick(() => (form.tradein_model_id = null))"
                        />
                      </v-col>
                      <v-col cols="12" sm="12" xl="2" md="2" class="py-0 mt-2">
                        <v-text-field
                          v-model="form.tradein_year"
                          dense
                          outlined
                          hide-details="auto"
                          label="Год"
                        />
                      </v-col>
                      <v-col cols="12" sm="12" xl="2" md="2" class="py-2">
                        <v-text-field
                          v-model="form.tradein_price"
                          dense
                          outlined
                          hide-details="auto"
                          label="Оценка"
                        />
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>
              </v-card>
              <v-card
                outlined
                style="
                border-bottom: 2px solid #007bff;
                border-right: 2px solid #007bff;
              "
                tile
                class="pa-2 py-1"
              >
                <v-row dense>


                  <v-col cols="12" sm="12" xl="12" md="12" class="py-1">
                    <p class="text-center mb-0 font-weight-bold">Кредитный калькулятор</p>
                  </v-col>
                  <v-col cols="12" sm="12" xl="3" md="3" class="py-1">
                    <v-select
                      v-model="form.payment_method"
                      :items="[
                        { id: 0, name: 'Не определено' },
                          { id: 1, name: 'Наличными' },
                          { id: 2, name: 'В кредит' },
                          { id: 3, name: 'Кредит(Скидка)' },
                        ]"
                      :rules="[validatePayment]"
                      item-text="name"
                      no-data-text="Нету данных"
                      item-value="id"
                      menu-props="auto"
                      label="Способ оплаты"
                      hide-details
                      outlined
                      dense
                      clearable
                      @click:clear="$nextTick(() => form.payment_method=null)"
                    />
                  </v-col>
                  <v-col cols="12" sm="12" xl="2" md="2" class="py-1">
                    <v-select
                      v-model="form.bank_1"
                      :items="banks"
                      item-value="id"
                      item-text="name"
                      menu-props="auto"
                      label="Банк 1"
                      single-line
                      outlined
                      no-data-text="Список пуст"
                      dense
                      required
                      hide-details
                    />
                  </v-col>

                  <v-col cols="12" sm="12" xl="2" md="2" class="py-1">
                    <v-text-field
                      v-model="form.percent_1"
                      dense
                      outlined
                      hide-details="auto"
                      label="Ставка %"
                    />
                  </v-col>

                  <v-col cols="12" sm="12" xl="2" md="2" class="py-1">
                    <v-text-field
                      :value="calc('payment', 1)"
                      dense
                      outlined
                      readonly
                      hide-details
                      label="Платёж"
                    />
                  </v-col>

                  <v-col cols="12" sm="12" xl="3" md="3" class="py-1">
                    <v-text-field
                      :value="calc('overpay', 1)"
                      dense
                      outlined
                      hide-details
                      label="Переплата"
                      readonly
                    />
                  </v-col>

                  <v-col cols="12" sm="12" xl="3" md="3" class="py-1">
                    <v-text-field
                      v-model="form.credit_period"
                      label="Срок кредита"
                      hide-details
                      outlined
                      dense
                      clearable
                    />
                  </v-col>
                  <v-col cols="12" sm="12" xl="2" md="2" class="py-1">
                    <v-select
                      v-model="form.bank_2"
                      :items="banks"
                      item-value="id"
                      item-text="name"
                      menu-props="auto"
                      label="Банк 2"
                      single-line
                      outlined
                      no-data-text="Список пуст"
                      dense
                      required
                      hide-details
                    />
                  </v-col>

                  <v-col cols="12" sm="12" xl="2" md="2" class="py-1">
                    <v-text-field
                      v-model="form.percent_2"
                      dense
                      outlined
                      hide-details="auto"
                      label="Ставка %"
                    />
                  </v-col>

                  <v-col cols="12" sm="12" xl="2" md="2" class="py-1">
                    <v-text-field
                      :value="calc('payment', 2)"
                      dense
                      outlined
                      hide-details
                      label="Платёж"
                      readonly
                    />
                  </v-col>

                  <v-col cols="12" sm="12" xl="3" md="3" class="py-1">
                    <v-text-field
                      :value="calc('overpay', 2)"
                      dense
                      outlined
                      hide-details="auto"
                      label="Переплата"
                      readonly
                    />
                  </v-col>
                  <v-col cols="12" sm="12" xl="3" md="3" class="py-0 mt-1">
                    <v-text-field
                      v-model="form.initial_fee"
                      dense
                      outlined
                      hide-details="auto"
                      label="Первоначальный взнос"
                    />
                  </v-col>
                  <v-col cols="12" sm="12" xl="2" md="2" class="py-1">
                    <v-select
                      v-model="form.bank_3"
                      :items="banks"
                      item-value="id"
                      class="mt-0"
                      item-text="name"
                      menu-props="auto"
                      label="Банк 3"
                      single-line
                      outlined
                      no-data-text="Список пуст"
                      dense
                      required
                      hide-details
                    />
                  </v-col>
                  <v-col cols="12" sm="12" xl="2" md="2" class="py-1">
                    <v-text-field
                      v-model="form.percent_3"
                      dense
                      outlined
                      hide-details="auto"
                      label="Ставка %"
                    />
                  </v-col>

                  <v-col cols="12" sm="12" xl="2" md="2" class="py-1">
                    <v-text-field
                      :value="calc('payment', 3)"
                      dense
                      outlined
                      hide-details="auto"
                      label="Платёж"
                      readonly
                    />
                  </v-col>

                  <v-col cols="12" sm="12" xl="3" md="3" class="py-1">
                    <v-text-field
                      :value="calc('overpay', 3)"
                      dense
                      outlined
                      hide-details="auto"
                      label="Переплата"
                      readonly
                    />
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
                        .replace(/^(.*\/\/[^\/?#]*).*$/, "$1")
                    }}</a
                  ></span
                ><br/>

                  <span v-if="form.utm_source || form.utm_medium" class="ml-4 ads"
                  >Источник: {{ form.utm_source }} ({{ form.utm_medium }})</span
                  ><br/>

                  <span class="ml-4 ads">Кл. фраза: {{ form.utm_term }}</span>
                  <span class="ml-4 ads">Кампания : {{ form.utm_campaign }}</span>

                  <v-col cols="12" sm="12" xl="12" md="12" class="py-0">
                    <v-row dense>
                      <v-col cols="12" sm="12" xl="5" md="5">
                        <v-subheader class="font-weight-bold">
                          Сайт
                        </v-subheader>
                      </v-col>
                      <v-col cols="12" sm="12" xl="7" md="7">
                        <v-select
                          v-model="form.site_id"
                          :items="sites"
                          item-text="title"
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
                      <v-col cols="12" sm="12" xl="5" md="5">
                        <v-subheader class="font-weight-bold">
                          Марка
                        </v-subheader>
                      </v-col>
                      <v-col cols="12" sm="12" xl="7" md="7">
                        <v-select
                          v-model="form.mark_id"
                          :items="marks"
                          item-text="name"
                          item-value="id"
                          dense
                          outlined
                          label="Марка"
                          @change="
                            getModels(form.mark_id);
                            deleted = false;
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
                      <v-col cols="12" sm="12" xl="5" md="5">
                        <v-subheader class="font-weight-bold">
                          Модель
                        </v-subheader>
                      </v-col>
                      <v-col cols="12" sm="12" xl="7" md="7">
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
                        <v-subheader class="font-weight-bold"> Год</v-subheader>
                      </v-col>
                      <v-col cols="12" sm="12" xl="7" md="7">
                        <v-text-field
                          v-model="form.car_year"
                          dense
                          outlined
                          hide-details="auto"
                          label="Год"
                        />
                      </v-col>
                    </v-row>
                  </v-col>
                  <v-col cols="12" sm="12" xl="12" md="12" class="py-0">
                    <v-row dense>
                      <v-col cols="12" sm="12" xl="5" md="5">
                        <v-subheader class="font-weight-bold">
                          Стоимость
                        </v-subheader>
                      </v-col>
                      <v-col cols="12" sm="12" xl="7" md="7">
                        <v-text-field
                          v-model="form.price"
                          dense
                          outlined
                          hide-details="auto"
                          label="Стоимость"
                        />
                      </v-col>
                    </v-row>
                  </v-col>
                  <v-col cols="12" sm="12" xl="12" md="12" class="py-0">
                    <v-row dense>
                      <v-col cols="12" sm="12" xl="5" md="5">
                        <v-subheader class="font-weight-bold">
                          Комплектация
                        </v-subheader>
                      </v-col>
                      <v-col cols="12" sm="12" xl="7" md="7">
                        <v-text-field
                          v-model="form.complectation"
                          dense
                          outlined
                          hide-details="auto"
                          label="Комплектация"
                        />
                      </v-col>
                    </v-row>
                  </v-col>
                  <v-col cols="12" sm="12" xl="12" md="12" class="py-0">
                    <v-row dense>
                      <v-col cols="12" sm="12" xl="5" md="5">
                        <v-subheader class="font-weight-bold">Ссылка</v-subheader>
                      </v-col>
                      <v-col cols="12" sm="12" xl="7" md="7">
                        <v-text-field
                          v-model="form.link_1"
                          dense
                          outlined
                          hide-details="auto"
                          label="Ссылка"
                        />
                      </v-col>
                    </v-row>
                    <v-row dense>
                      <v-col cols="12" sm="12" xl="5" md="5">
                        <v-subheader class="font-weight-bold">Ссылка 2</v-subheader>
                      </v-col>
                      <v-col cols="12" sm="12" xl="7" md="7">
                        <v-text-field
                          v-model="form.link_2"
                          dense
                          outlined
                          hide-details="auto"
                          label="Ссылка"
                        />
                      </v-col>
                    </v-row>
                  </v-col>
                  <v-col cols="12" sm="12" xl="12" md="12" class="py-0" v-if="false">
                    <v-row dense>
                      <v-col cols="12" sm="12" xl="5" md="5">
                        <v-subheader class="font-weight-bold">
                          Салон
                        </v-subheader>
                      </v-col>
                      <v-col cols="12" sm="12" xl="7" md="7">
                        <v-select
                          v-model="form.showroom_id"
                          :items="showrooms"
                          item-value="id"
                          item-text="name"
                          dense
                          outlined
                          hide-details="auto"
                          no-data-text="Список пуст"
                          label="Салон"
                        />
                      </v-col>
                    </v-row>
                  </v-col>

                </v-row>
              </v-card>
            </v-col>
          </v-row>
          <v-row no-gutters>
            <v-col cols="12" md="9" xl="9" sm="12">
              <v-card class="px-3 py-1" outlined tile>
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
                  <v-col cols="12" sm="12" xl="2" md="2" class="py-0 mr-10">

                    Приедет

                    <date-picker
                      v-model="form.will_arrive"
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

                  <v-col cols="12" sm="12" xl="2" md="2" class="py-0 mr-10">

                    Приехал
                    <date-picker
                      v-model="form.arrived_date"
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
                  <v-col cols="12" sm="12" xl="2" md="2" class="py-0">
                    Приезд подтвержден
                    <div>
                      <v-switch
                        v-model="form.arrived"
                        ripple
                        dense
                        class="mt-1 d-inline-block"
                      />
                    </div>
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
                      :rules="[v => !!v || 'Выберите статус']"
                      clearable
                      outlined
                      hide-details="auto"
                      @change="changedStatus()"
                    />
                  </v-col>
                  <v-col v-if="form.status_id === 7" cols="12" sm="12" xl="2" md="2" class="py-0 mb-2">

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
                  <v-col v-if="form.status_id === 6 && role_id !== 2" cols="12" sm="12" xl="2" md="2" class="py-0 mb-2">

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
                  <v-col cols="12" sm="12" xl="4" md="4" class="py-0 mb-2">
                    <v-select
                      v-model="form.operator_id"
                      :items="operators"
                      item-value="id"
                      :item-text="item => item.last_name ? item.first_name + ' ' + item.last_name : item.first_name"
                      dense
                      outlined
                      hide-details
                      label="Оператор"
                      :disabled="(role_id !== 1 && role_id !== 3)"
                    >
                    </v-select>
                  </v-col>
                  <v-col cols="12" sm="12" xl="8" md="8" class="py-0">
                    <v-textarea
                      v-model="form.comment"
                      rows="4"
                      class="mt-2"
                      outlined
                      dense
                      hide-details="auto"
                      label="Комментарий"
                      :readonly="
                          user_id !== order?.operator_id && role_id !== 1 &&  role_id !== 3
                        "
                    />
                  </v-col>

                  <v-col cols="12" sm="12" xl="4" md="4" class="py-0">
                    <v-textarea
                      v-model="form.general_comment"
                      rows="4"
                      class="mt-2"
                      outlined
                      dense
                      hide-details="auto"
                      label="Общий комментарий"
                    />
                  </v-col>
                </v-row>
              </v-card>
            </v-col>

            <v-col cols="12" xl="3" sm="12" md="3">

              <v-btn
                color="blue"
                class="white--text my-2"
                :loading="busy"
                block
                :to="'/crm/'+ form?.showroom_id+'/orders-mini/' + form.site?.agency_id"
              >
                Вернутьстя
              </v-btn>


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
            <v-spacer/>
            <v-btn color="primary" @click="sendSMS()"> Отправить</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="isCopyDialog" max-width="400px">
        <v-card>
          <v-card-title>
            <span class="headline">Передача заявки {{ $route.params.showroom }}</span>
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
                      :items="showrooms.filter(l=>l.id != $route.params.showroom)"
                      item-value="id"
                      item-text="name"
                      placeholder="Салон"
                      :rules="[v => !!v || 'Выберите салона']"
                    />
                  </v-col>
                </v-row>
              </v-form>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer/>
            <v-btn color="primary" @click="copyOrder()">Передать</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-row>
    <div
      style="color: #3b99e0; font-weight: bold; pointer: cursor;"
      @click="loadHistory()"
    >
      Показать историю операций
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
      <template
        #body="{ items }"
      >
        <tbody>
        <tr
          v-for="item in items"
          :key="item.id"
        >
          <td>
            {{ $moment(item.created_at).format('DD.MM.YYYY HH:mm:ss') }}
          </td>
          <td>
            {{ item.user?.first_name }} {{ item.user?.last_name }}
          </td>
          <td>



            <div v-if="item.description === '1'">Создана</div>
            <div v-else-if="item.description === '2'">Просмотр</div>
            <div v-else-if="item.description === '3'">Изменена</div>
            <div v-else-if="item.description === '4'">Удалена</div>
            <div v-else-if="item.description === '5'">Входящий  звонок</div>
            <div v-else-if="item.description === '6'">Исходящий звонок</div>
            <div v-else-if="item.description === '7'">Пропущенный звонок</div>
            <div v-else-if="item.description === '8'">Запись звонка</div>
          </td>
          <td>
            <table class="activities" width="100%"
                   v-if="item.properties?.attributes && Array.isArray(item.properties?.attributes) ==false">
              <thead>
              <tr>
                <th>Поле</th>
                <th>Было</th>
                <th>Стало</th>
              </tr>
              </thead>
              <tbody>


              <template v-for="(row, i) in (item.properties && item.properties.attributes)">
                <tr>
                  <td class="text-center" >{{ activity[i] || i }}</td>
                  <td>
                    <template v-if="item.properties.old?.hasOwnProperty(i)">
                      <template v-if="i == 'status_id'">
                        {{ statuses.find(l => l.id == item.properties.old?.i)?.name }}
                      </template>
                      <template v-else-if="i == 'showroom_id'">
                        {{ showrooms.find(l => l.id == item.properties.old?.i)?.name }}
                      </template>
                      <template v-else-if="i == 'site_id'">
                        {{ sites.find(l => l.id == item.properties.old?.i)?.title }}
                      </template>
                      <template v-else-if="i == 'type_id'">
                        {{ types.find(l => l.id == item.properties.old?.i)?.name }}
                      </template>
                      <template v-else-if="i == 'operator_id'">
                        {{ operators.find(l => l.id == item.properties.old?.i)?.first_name }}
                      </template>
                      <template v-else-if="i == 'payment_method'">
                        {{ payment_methods.find(l => l.id == item.properties.old?.i)?.name }}
                      </template>
                      <template v-else-if="i == 'region_id'">
                        {{ regions.find(l => l.id == item.properties.old?.i)?.name }}
                      </template>
                      <template v-else-if="i == 'mark_id'">
                        {{ marks.find(l => l.id == item.properties.old?.i)?.name }}
                      </template>
                      <template v-else-if="i == 'model_id'">
                        {{ models.find(l => l.id == item.properties.old?.i)?.name }}
                      </template>
                      <template v-else-if="i === 'approved'">
                        <span v-if="item.properties?.old[i] == 1">Да</span>
                        <span v-if="item.properties?.old[i]  == 0">Нет</span>
                      </template>

                      <template v-else-if="i == 'canceled'">
                        <span v-if="item.properties.old[i] == 1">Да</span>
                        <span v-if="item.properties.old[i] == 0">Нет</span>
                      </template>
                      <template v-else-if="i === 'commercial_offer'">
                        <span v-if="item.properties.old[i] == 1">Да</span>
                        <span v-if="item.properties.old[i] == 0">Нет</span>
                      </template>
                      <template v-else-if="i === 'arrived'">
                        <span v-if="item.properties.old[i] == 1">Да</span>
                        <span v-if="item.properties.old[i] == 0">Нет</span>
                      </template>
                      <template v-else-if="i === 'callback' && item.properties.old[i] !== null">
                        {{$moment(item.properties.old[i]).format('DD.MM.YYYY HH:mm:ss')}}
                      </template>
                      <template v-else-if="i === 'last_call' && item.properties.old[i] !== null">
                        {{$moment(item.properties.old[i]).format('DD.MM.YYYY HH:mm:ss')}}
                      </template>
                      <template v-else-if="i === 'will_arrive' && item.properties.old[i] !== null">
                        {{$moment(item.properties.old[i]).format('DD.MM.YYYY')}}
                      </template>
                      <template v-else-if="i === 'arrived_date' && item.properties.old[i] !== null">
                        {{$moment(item.properties.old[i]).format('DD.MM.YYYY')}}
                      </template>
                      <template v-else>
                        {{ item.properties.old[i] }}
                      </template>
                    </template>

                  </td>
                  <td>
                    <template v-if="i == 'status_id'">
                      {{ statuses.find(l => l.id === row)?.name }}
                    </template>
                    <template v-else-if="i == 'showroom_id'">
                      {{ showrooms.find(l => l.id === row)?.name }}
                    </template>
                    <template v-else-if="i == 'site_id'">
                      {{ sites.find(l => l.id === row)?.title }}
                    </template>
                    <template v-else-if="i == 'type_id'">
                      {{ types.find(l => l.id === row)?.name }}
                    </template>
                    <template v-else-if="i == 'operator_id'">
                      {{ operators.find(l => l.id === row)?.first_name }}
                    </template>
                    <template v-else-if="i == 'payment_method'">
                      {{ payment_methods.find(l => l.id === row)?.name }}
                    </template>
                    <template v-else-if="i == 'region_id'">
                      {{ regions.find(l => l.id === row)?.name }}
                    </template>
                    <template v-else-if="i == 'mark_id'">
                      {{ marks.find(l => l.id === row)?.name }}
                    </template>
                    <template v-else-if="i == 'model_id'">
                      {{ models.find(l => l.id === row)?.name ||  row}}
                    </template>
                    <template v-else-if="i === 'approved'">
                      <span v-if="row === 1">Да</span>
                      <span v-if="row === 0">Нет</span>
                    </template>
                    <template v-else-if="i ===  'canceled'">
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
                    <template v-else-if="i === 'callback' && row !== null">
                      {{$moment(row).format('DD.MM.YYYY HH:mm:ss')}}
                    </template>
                    <template v-else-if="i === 'last_call' && row !== null">
                      {{$moment(row).format('DD.MM.YYYY HH:mm:ss')}}
                    </template>
                    <template v-else-if="i === 'will_arrive' && row !== null">
                      {{$moment(row).format('DD.MM.YYYY')}}
                    </template>
                    <template v-else-if="i === 'arrived_date' && row !== null">
                      {{$moment(row).format('DD.MM.YYYY')}}
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

</template>

<script>
import DTPicker from '~/components/DTPicker'
import BreadCrumb from '~/components/BreadCrumb'
import DPicker from '~/components/DPicker'
import {activity} from "~/configs/activity.json";

export default {
  name: "EditOrder",
  components: {
    DTPicker,
    DPicker,
    BreadCrumb,
  },
  layout: 'edit',
  middleware: "auth",
  data() {
    return {
      activity,
      dialog: false,
      isCopyDialog: false,
      copyShowroom: null,
      history_active: false,
      searchText: '',
      smsValid: false,
      copyValid: false,
      valid: false,
      order: {},
      phone: '',
      test: '',
      busy: false,
      open_dtp: false,
      open_dtp_callback: false,
      showTimePanel: false,
      types1: ["Звонок", "Кредит", "Экспресс-кредит", "Seo"],
      sms: {
        text: "",
        phone: "",
        phone_extension: "",
        template: 0,
      },
      periods: [
        {
          id: 1,
          value: 0.5,
          name: '6 месяцев'
        },
        {
          id: 2,
          value: 1,
          name: '1 год'
        },
        {
          id: 3,
          value: 2,
          name: '2 года'
        },
        {
          id: 4,
          value: 3,
          name: '3 года'
        },
        {
          id: 5,
          value: 4,
          name: '4 года'
        },
        {
          id: 6,
          value: 5,
          name: '5 лет'
        },
        {
          id: 7,
          value: 6,
          name: '6 лет'
        },
        {
          id: 8,
          value: 7,
          name: '7 лет'
        },
        {
          id: 9,
          value: 8,
          name: '8 лет'
        },
        {
          id: 10,
          value: 9,
          name: '9 лет'
        },
        {
          id: 11,
          value: 10,
          name: '10 лет'
        },
        {
          id: 12,
          value: 12,
          name: '12 лет'
        },
        {
          id: 14,
          name: '14 лет'
        },
        {
          id: 15,
          name: '15 лет'
        },
      ],
      form: {
        client_name: "",
        email: "",
        phone: "",
        work_phone: "",
        operator_id: "",
        citizenship_id: "",
        birthday: "",
        age: "",
        supouse_name: "",
        supouse_birthday: "",
        region_id: "",
        region_ip_id: "",
        live_region: "",
        car_number: "",
        work_place: "",
        work_position: "",
        official_income: "",
        work_experience: "",
        ads_source: "",
        mark_id: "",
        model_id: "",
        car_year: "",
        price: "",
        complectation: "",
        initial_fee: "",
        credit_period: null,
        showroom_id: "",
        payment_cash: "",
        payment_credit: "",
        gifts: "",
        happy_hour: "",
        credit_deffer: "",
        job_loss_insurance: "",
        last_call: "",
        last_call_picker: "",
        callback: "",
        callback_picker: "",
        will_arrive: "",
        arrived: "",
        arrived_date: "",
        status_id: "",
        date_of_sale: "",
        call_heard: "",
        entry_point: "",
        utm_source: "",
        utm_medium: "",
        utm_term: "",
        utm_campaign: "",
        type_id: "",
        comment: "",
        general_comment: "",
      },
      history_headers: [
        {
          text: 'Дата и время',
          align: 'start',
          sortable: false,
          value: 'created_at',
        },
        {text: 'Пользователь', value: 'user.first_name'},
        {text: 'Операция', value: 'fat'},
        {text: 'Изменено', value: 'fat'},
      ],
      rules: {
        first_name: [
          (val) => (val || "").length > 0 || "Введите имя клиента",
        ],
        phone: [(val) => (val || "").length === 18 || "Введите телефон"],
      },
      dateTimeMask: [/\d/, /\d/, ".", /\d/, /\d/, ".", /\d/, /\d/, /\d/, /\d/, " ", /\d/, /\d/, ":", /\d/, /\d/],
      dateMask: [/\d/, /\d/, ".", /\d/, /\d/, ".", /\d/, /\d/, /\d/, /\d/],
      apiForm: {
        ext_number: null,
        phone: null,
        from_number: null,
        sip: null,
      },
      textRules: [
        (v) => !!v || "Введите текст сообщений",
        (v) =>
          (v && v.length <= 70) ||
          "Текст сообщений не должно превышать 70 символов!!!",
      ],
    };
  },
  async fetch({store, $axios, $moment, $auth, params: {showroom, id}}) {

    //this.form = response.data
    /*console.log('res', response)
    if (response === null) {
      if (process.server) {
        $nuxt.context.res.statusCode = 404;
      }
      throw new Error("Заявка не найдена!!!");
    }*/

    await store.dispatch('user/toggle', false)
    await store.dispatch('order/fetchTypes')
    await store.dispatch('order/fetchStatuses')
    await store.dispatch('order/fetchArrivalStatuses')
    await store.dispatch('order/fetchTrashes')
    await store.dispatch('property/fetchMarks')
    await store.dispatch('showroom/fetchRegions')
    await store.dispatch('showroom/fetchShowrooms', {})
    await store.dispatch('showroom/fetchSites', {id: (showroom || this.form?.showroom_id)})
    await store.dispatch('credit/fetchBanks')
    await store.dispatch('showroom/fetchManagers', {showroom_id: (showroom || this.form?.showroom_id)})
    await store.dispatch('showroom/fetchOperators', {showroom_id: (showroom || this.form?.showroom_id)})


  },
  created() {
    this.$axios.get('/order/' + this.$route.params?.showroom + '/detail/' + this.$route.params?.id)
      .then(
        response => {
          this.order = response.data

          this.form = Object.assign({}, this.order);
          if (this.form.birthday !== null) {
            this.form.birthday = this.$moment(this.form.birthday).format('DD.MM.YYYY')
          }
          if (this.form.last_call !== null) {
            this.form.last_call = this.$moment(this.form.last_call).format('DD.MM.YYYY HH:mm')
          }
          if (this.form.callback !== null) {
            this.form.callback = this.$moment(this.form.callback).format('DD.MM.YYYY HH:mm')
          }
          if (this.order?.mark_id !== null) {
            this.$store.dispatch('property/fetchModels', {markId: this.order?.mark_id})
          }
          if (this.order?.tradein_mark_id !== null) {
            this.$store.dispatch('property/fetchTradeInModels', {markId: this.order?.tradein_mark_id})
          }
        }
      );
  },

  computed: {
    payment_credit() {
      return this.form.payment_credit;
    },
    payment_cash() {
      return this.form.payment_cash;
    },
    role_id() {
      return this.$auth.user?.role_id;
    },
    user_id() {
      return this.$auth.user?.id;
    },
    types() {
      return this.$store.state.order.types
    },
    banks() {
      return this.$store.state.credit.banks
    },
    statuses() {
      return this.$store.state.order.statuses
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
      return this.$store.state.showroom.showrooms
    },
    showroom_id() {
      return this.$route.params.showroom
    },
    showroom() {
      return this.$store.state.showroom.showrooms.find(l => l.id === this.order?.showroom_id)
    },
    operators() {
      return this.$store.state.showroom.operators
    },
    histories() {
      return this.$store.state.order.histories
    },

    arrival_statuses() {
      return this.$store.state.order.arrival_statuses
    },

    links() {
      return [
        {
          text: 'Главная',
          disabled: false,
          href: '/'
        },
        {
          text: this.showroom?.name,
          disabled: true,
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
      ]
    },
  },


  watch: {
    payment_credit(value) {
      if (value) {
        this.form.payment_cash = false;
      }
    },
    payment_cash(value) {
      if (value) {
        this.form.payment_credit = false;
        this.form.gifts = false;
        this.form.happy_hour = false;
        this.form.credit_deffer = false;
        this.form.job_loss_insurance = false;
      }
    },
  },

  methods: {
    async save(after) {
      try {
        if (!this.$refs.form.validate() || !this.valid) {
          this.$toast.error("Заполните обязательные поля!!!");
          return;
        }
        this.busy = true;
        if (this.form.last_call !== null) {
          const st = this.form.last_call + ':00'
          this.form.last_call = this.$moment(st, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss');
        }
        if (this.form.callback !== null) {
          const cb = this.form.callback + ':00'
          this.form.callback = this.$moment(cb, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss');
        }
        await this.$store.dispatch('order/update', {item: this.form});
        this.$toast.success("Заявка успешно обновлена");
        if (after === "new") {
          this.$refs.form.reset();
          this.$refs.form.resetValidation();
          await this.$router.push({name: "create-order"});
        } else if (after === "return") {
          this.$refs.form.reset();
          this.$refs.form.resetValidation();
          await this.$router.push('/crm/' + this.form?.showroom_id + '/orders');
        }
        if (this.form.last_call !== null) {
          this.form.last_call = this.$moment(this.form.last_call).format('DD.MM.YYYY HH:mm');
        }
        if (this.form.callback !== null) {
          this.form.callback = this.$moment(this.form.callback).format('DD.MM.YYYY HH:mm');
        }
      } catch (e) {
        //this.$sentry.captureException(e)
        this.$toast.error("Произошла ошибка:" + e?.message);
      } finally {
        this.busy = false;
      }
    },
    setNow(field, isDateTime = false) {
      console.log('fired')
      this.form[field] = isDateTime
        ? this.$moment().format("DD.MM.YYYY HH:mm")
        : this.$moment().format("YYYY-MM-DD");
      this.open_dtp = false
      this.open_dtp_callback = false
    },
    setHour(field) {
      console.log('fired')
    },
    makeLink() {
      if (this.form.client_name === null) return
      let fio = this.form.client_name?.split(' ')
      let birthday = null
      if (this.form?.birthday) {
        birthday = '&is%5Bdate%5D=' + this.form?.birthday
      }
      return `https://fssp.gov.ru/iss/ip?is[variant]=1&is%5Bregion_id%5D%5B0%5D=-1&${fio.length ? ('is%5Blast_name%5D=' + fio[0]) : ''}${fio.length > 0 ? ('&is%5Bfirst_name%5D=' + fio[1]) : ''}${fio.length > 1 ? ('&is%5Bpatronymic%5D=' + fio[2]) : ''}${birthday ? birthday : ''}`
    },
    async call() {
      console.log(this.$auth.user?.work_place)
      this.apiForm.phone = this.form.phone || this.order.phone;
      this.apiForm.showroom_id = this.order?.showroom_id;
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
          this.$toast.error("Произошла ошибка...");
          /// reject(error)
        });
    },
    async sendSMS() {
      this.$refs.sms_form.validate();
      if (this.smsValid === true) {
        await this.$axios
          .post(
            "/send/sms",
            {
              phone: this.order.phone,
              ext_number: this.order.operator?.phone_extension ?? null,
              text: this.sms.text,
              template: this.sms.template,
            },
            {
              headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
              },
            }
          )
          .then((response) => {
            if (response.status === 200) {
              this.$toast.success("Смс отправлен");
            }
            this.sms.text = "";
            this.sms.template = 0;
            console.log(response.data);
          })
          .catch((error) => {
            this.$toast.error("Произошла ошибка!!!" + error);
          });
      } else {
        this.$toast.error("Запоните поля!");
      }
    },
    calc(type, bank) {
      let percent = this.form[('percent_' + bank)] / 12 / 100;
      var period = 1
      if (typeof (this.form.credit_period) === 'object') {
        period = this.form.credit_period?.id * 12;
      } else {
        period = this.form.credit_period * 12;
      }

      let result = (this.form.price - this.form.initial_fee) * (percent + (percent / (((1 + percent) ** period) - 1)));
      switch (type) {
        case 'payment':
          if (result <= 0) return
          return this.currency(Math.round(result)) || null
        case 'overpay':
          if (result <= 0) return
          return this.currency(Math.round((result * period) - (this.form.price - this.form.initial_fee))) || null
        default:
          return
      }


    },
    currency(value) {
      if (isNaN(value)) {
        return value
      }
      let val = (value / 1).toFixed(0).replace('.', ',')
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")
    },
    openBanki() {
      if (this.form.car_number) {
        let car_number = this.form.car_number.split(" ").join("")
        let link = `https://www.banki.ru/insurance/order/auto/type/osago/short-flow/steps/auto?licensePlate=${car_number}&source=main_widget`
        window.open(link, '_blank')
      }
    },
    async loadHistory() {
      if (!this.history_active) {
        await this.$store.dispatch('order/fetchHistory', {
          id: this.$route.params.id,
          showroom_id: this.$route.params.showroom
        })
      }
      this.history_active = !this.history_active
    },

    getModels(markId = null) {
      this.form.model_id = null
      if (markId !== 0) {
        this.$store.dispatch('property/fetchModels', {markId})
      }
    },

    getTradeInModels(markId = null) {
      this.form.tradin_model_id = null
      if (markId !== 0) {
        this.$store.dispatch('property/fetchTradeInModels', {markId})
      }
    },
    async copyOrder() {
      const item = {
        showroom_id: this.copyShowroom,
        order_id: this.$route.params.id,
      };
      if (this.$refs.copy_form.validate() && this.copyValid) {
        try {
          this.$store.dispatch('order/copyOrder', {item}).then((res) => {
              this.$toast.success("Заявка успешно продублирован");
            }
          ).catch(error => {
            console.log('err ', error)
            this.$toast.error("Произошла ошибка" + error?.message);
          })
        } catch (e) {
          this.$toast.success("Произошла ошибка:" + e,);
        }
      }
    },
    validatePayment(value) {
      if (value !== null || this.form.status_id === 3 || this.form.status_id === 1 || this.form.status_id === 8) {
        return true; // Validation passed
      } else {
        return 'Выберите способ оплаты'; // Validation failed
      }
    },
    resetValidation() {
      this.$refs.form.resetValidation(); // Reset form validation
    },


    changedStatus() {
      if (this.form.status_id === 6) {
        this.form.arrived_date = this.$moment().format("YYYY-MM-DD");
      }

      if (this.form?.status_id === 8) {
        this.form.payment_method = 6;
      }
      if (this.form?.status_id === 16) {
        this.form.payment_method = 10;
      }
      if (this.form?.status_id === 15) {
        this.form.payment_method = 9;
      }
      this.resetValidation();
    },
  },
};
</script>
<style lang="scss">
$namespace: "mx";
$default-color: #555;
$primary-color: #1284e7;
@import "~vue2-datepicker/scss/index.scss";
.ads {
  font: 13px/1.2em Arial;
  font-weight: bold;
}

.bottom-border {
  border-bottom: 2px solid rgb(0, 123, 255);
  background-color: white;
}
</style>

<template>
  <v-container fluid>
    <v-btn
      v-if="repeats.length"
      elevation="2"
      fab
      color="success"
      outlined
      class="mt-14"
      raised
      fixed
      small
      @click="openRepeat()"
    >
      <v-badge color="error" :content="repeats.length">
        <v-icon>mdi-replay</v-icon>
      </v-badge>
    </v-btn>

    <v-btn
      v-if="role_id === 5 || role_id === 1"
      elevation="2"
      fab
      color="warning"
      outlined
      style="margin-top: 110px"
      raised
      fixed
      small
      @click="openRepeats()"
    >
      <v-icon>mdi-sync-alert</v-icon>
    </v-btn>

    <v-row justify="center">
      <v-col cols="12" xl="11" md="12" lg="11" class="pt-0">
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
            :to="'/crm/' + form?.showroom_id + '/orders'"
          >
            <v-icon>mdi-keyboard-return</v-icon>
            Вернутся в главную
          </v-btn>
        </v-toolbar>
      </v-col>
      <v-col cols="12" xl="11" md="12" lg="12" class="pt-0">
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
                  @change="changedType()"
                />
              </v-col>
              <v-subheader v-if="form.copied_to" class="mt-2 font-weight-bold">
                Передано в:
                <template v-for="(chip, y) in form.copied_to">
                  <v-chip
                    :key="'showroom_' + y"
                    class="ma-1"
                    color="success"
                    small
                  >
                    {{ showrooms.find((sh) => sh.id === chip)?.name }}
                  </v-chip>
                </template>
              </v-subheader>

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
                :href="`https://api.whatsapp.com/send/?phone=${form.phone
                  ?.match(/\d/g)
                  ?.join('')}&text&type=phone_number&app_absent=0`"
                class="ma-2 mt-4"
                icon
              >
                <v-icon color="success" right large> mdi-whatsapp </v-icon>
              </v-btn>
              <v-btn
                target="_blank"
                :href="'https://t.me/+' + form.phone?.match(/\d/g)?.join('')"
                class="ma-2 mt-4"
                icon
              >
                <v-icon
                  color="primary"
                  right
                  large
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

              <v-btn
                href="https://autostock.group/admin/auth"
                class="ma-2 mt-4"
                target="_blank"
              >
                КП
                <v-icon
                  color="red"
                  right
                  style="transform: rotate(320deg); margin-top: -5px"
                >
                  mdi-shopping
                </v-icon>
              </v-btn>

              <v-spacer></v-spacer>
            </v-row>
          </v-card>
          <v-row no-gutters class="bottom-border">
            <v-col cols="12" md="10" sm="12">
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
                      append-icon="mdi-content-copy"
                      hide-details
                      @click:append="copyName()"
                    />
                  </v-col>
                  <v-col cols="12" sm="12" xl="3" md="3" class="pt-1">
                    <v-text-field
                      v-model="form.phone"
                      v-mask="'+7 ### ###-##-##'"
                      color="purple darken-2"
                      label="Сотовый"
                      :rules="[(v) => !!v || 'Введите сотового']"
                      required
                      dense
                      outlined
                      :readonly="role_id === 5"
                      hide-details
                      append-outer-icon="mdi-content-copy"
                      @click:append-outer="copyPhone(form.phone)"
                    >
                      <template slot="append">
                        <v-icon
                          class="mt-1"
                          color="primary"
                          small
                          @click="call(form.phone)"
                          >mdi-phone
                        </v-icon>

                        <v-icon
                          small
                          class="ml-2 mt-1"
                          color="primary"
                          @click="openDialogSms()"
                          >mdi-email-outline
                        </v-icon>
                      </template>
                    </v-text-field>
                  </v-col>
                  <v-col cols="12" md="5">
                    <v-row dense>
                      <v-col cols="12" sm="12" xl="4" md="4" class="pt-1">
                        <v-text-field
                          v-model="form.work_phone"
                          v-mask="'+7 ### ###-##-##'"
                          color="purple darken-2"
                          label="Рабочий телефон"
                          outlined
                          dense
                          :readonly="role_id === 5"
                          hide-details
                          append-outer-icon="mdi-content-copy"
                          @maska="
                            form.work_phone = $event.target.dataset.maskRawValue
                          "
                          @click:append-outer="copyPhone(form.work_phone)"
                        >
                          <template slot="append">
                            <v-icon
                              small
                              class="mt-1"
                              color="primary"
                              @click="call(form.work_phone)"
                              >mdi-phone
                            </v-icon>

                            <v-icon
                              small
                              class="ml-2 mt-1"
                              color="primary"
                              @click="openDialogSms('work_phone')"
                              >mdi-email-outline
                            </v-icon>
                          </template>
                        </v-text-field>
                      </v-col>
                      <v-col cols="12" sm="12" xl="4" md="4" class="pt-1">
                        <v-text-field
                          v-model="form.phone_2"
                          v-mask="'+7 ### ###-##-##'"
                          color="purple darken-2"
                          :readonly="role_id === 5"
                          label="Доп. номер"
                          required
                          dense
                          outlined
                          class="text-sm"
                          hide-details
                        >
                          <template slot="append">
                            <v-icon
                              class="mt-1"
                              color="primary"
                              small
                              @click="call(form.phone_2)"
                              >mdi-phone
                            </v-icon>
                            <v-icon
                              class="ml-2 mt-1"
                              small
                              color="primary"
                              @click="openDialogSms('phone_2')"
                              >mdi-email-outline
                            </v-icon>
                          </template>
                        </v-text-field>
                      </v-col>
                      <v-col cols="12" sm="12" xl="4" md="4" class="pt-1">
                        <v-text-field
                          v-model="form.phone_3"
                          v-mask="'+7 ### ###-##-##'"
                          color="purple darken-2"
                          label="Доп. номер 2"
                          required
                          dense
                          outlined
                          hide-details
                          :readonly="role_id === 5"
                        >
                          <template slot="append">
                            <v-icon
                              class="mt-1"
                              color="primary"
                              @click="call(form.phone_3)"
                              small
                              >mdi-phone
                            </v-icon>

                            <v-icon
                              class="ml-2 mt-1"
                              color="primary"
                              @click="openDialogSms('phone_3')"
                              small
                              >mdi-email-outline
                            </v-icon>
                          </template>
                        </v-text-field>
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
                      label="Место жительства (Регион)"
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
                      label="Место регистрации"
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
                      <v-col
                        cols="12"
                        sm="12"
                        xl="1"
                        md="1"
                        class="py-1 d-flex flex-row px-auto"
                      >
                        <a
                          href="https://www.avito.ru/evaluation/cars"
                          class="mr-2"
                          target="_blank"
                          rel="noreferrer"
                        >
                          <v-img
                            style="
                              border: #4caf50 1px solid;
                              border-radius: 6px;
                              cursor: pointer;
                            "
                            src="/images/logo-opoveshcheniiaavito.png"
                            max-width="45px"
                          />
                        </a>

                        <a
                          href="https://auto.ru/cars/evaluation/"
                          target="_blank"
                          rel="noreferrer"
                        >
                          <v-img
                            style="
                              border: red 1px solid;
                              border-radius: 6px;
                              cursor: pointer;
                            "
                            src="/images/autru.png"
                            max-width="45px"
                          />
                        </a>
                      </v-col>
                      <v-col
                        cols="12"
                        sm="12"
                        xl="2"
                        md="2"
                        class="py-0 mt-2 pl-4"
                      >
                        <v-text-field
                          v-model="form.car_number"
                          color="primary"
                          dense
                          outlined
                          label="Гос. номер"
                          hide-details="auto"
                          append-icon="mdi-magnify"
                          @click:append="openBanki()"
                        />
                      </v-col>
                      <v-col
                        cols="12"
                        sm="12"
                        xl="2"
                        md="2"
                        class="py-0 mt-2 pl-4"
                      >
                        <v-select
                          v-model="form.tradein_mark_id"
                          :items="marks"
                          item-text="name"
                          item-value="id"
                          dense
                          outlined
                          label="Марка"
                          hide-details="auto"
                          clearable
                          no-data-text="Список пуст"
                          @click:clear="
                            $nextTick(() => (form.tradein_mark_id = null))
                          "
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
                          @click:clear="
                            $nextTick(() => (form.tradein_model_id = null))
                          "
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
                    <p class="text-center mb-0 font-weight-bold">
                      Кредитный калькулятор
                    </p>
                  </v-col>
                  <v-col cols="12" sm="12" xl="3" md="3" class="py-1">
                    <v-select
                      v-model="form.payment_method"
                      :items="payment_methods"
                      :rules="[validatePayment]"
                      item-text="name"
                      no-data-text="Нету данных"
                      item-value="id"
                      menu-props="auto"
                      label="Способ оплаты"
                      hide-details="auto"
                      outlined
                      dense
                      clearable
                      @click:clear="
                        $nextTick(() => (form.payment_method = null))
                      "
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
            <v-col cols="12" xl="2" sm="12" md="2" class="bottom-border">
              <v-card class="pa-2 py-0" outlined tile lign-self="scratch">
                <v-row class="mt-2" dense>
                  <span class="ml-4 ads mb-1"
                    >Точка входа:
                    <a
                      :href="$formatUrl(form.entry_point)"
                      target="_blank"
                      rel="noreferrer"
                    >
                      {{ $formatUrl(form.entry_point) }}
                    </a></span
                  ><br />

                  <span
                    v-if="form.utm_source || form.utm_medium"
                    class="ml-4 ads"
                    >Источник: {{ form.utm_source }} ({{
                      form.utm_medium
                    }})</span
                  ><br />
                  <span class="ml-4 ads">Кл. фраза: {{ form.utm_term }}</span>

                  <v-col cols="12" sm="12" xl="12" md="12" class="mb-1">
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

                  <v-col cols="12" sm="12" xl="12" md="12">
                    <v-autocomplete
                      v-model="form.mark_id"
                      :items="marks"
                      item-text="name"
                      item-value="id"
                      dense
                      outlined
                      label="Марка"
                      hide-details="auto"
                      clearable
                      @change="getModels(form.mark_id)"
                      no-data-text="Список пуст"
                      @click:clear="$nextTick(() => (form.mark_id = null))"
                    />
                  </v-col>
                  <v-col cols="12" sm="12" xl="12" md="12">
                    <v-autocomplete
                      v-model="form.model_id"
                      :items="filtered_models"
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
                  <v-col cols="12" sm="12" xl="8" md="8">
                    <v-select
                      v-model="form.car_status_id"
                      :items="[
                        {
                          id: 1,
                          name: 'Новая',
                        },
                        {
                          id: 2,
                          name: 'Б.У.',
                        },
                        {
                          id: 3,
                          name: 'Подбор',
                        },
                      ]"
                      item-value="id"
                      item-text="name"
                      menu-props="auto"
                      single-line
                      outlined
                      dense
                      required
                      hide-details="auto"
                      label="Тип машины"
                    />
                  </v-col>
                  <v-col cols="12" sm="12" xl="4" md="4">
                    <v-text-field
                      v-model="form.car_year"
                      dense
                      outlined
                      hide-details="auto"
                      :rules="[carYearRequired]"
                      label="Год"
                    />
                  </v-col>
                  <v-col cols="12" sm="12" xl="12" md="12">
                    <v-text-field
                      v-model="form.price"
                      dense
                      outlined
                      hide-details="auto"
                      label="Стоимость"
                    />
                  </v-col>

                  <v-col cols="12" sm="12" xl="12" md="12">
                    <v-text-field
                      v-model="form.complectation"
                      dense
                      outlined
                      hide-details="auto"
                      label="Комплектация"
                      :rules="maxLength(280)"
                    />
                  </v-col>
                  <v-col cols="12" sm="12" xl="12" md="12">
                    <v-text-field
                      v-model="form.link_1"
                      dense
                      outlined
                      hide-details="auto"
                      label="Ссылка"
                      :rules="maxLength(322)"
                    />
                  </v-col>

                  <v-col cols="12" sm="12" xl="12" md="12" class="mb-2">
                    <v-text-field
                      v-model="form.link_2"
                      dense
                      outlined
                      hide-details="auto"
                      label="Ссылка"
                      :rules="maxLength(320)"
                    />
                  </v-col>
                </v-row>
              </v-card>
            </v-col>
          </v-row>
          <v-row no-gutters>
            <v-col cols="12" md="10" xl="10" sm="12">
              <v-card class="px-3 py-1" outlined tile>
                <v-row dense>
                  <v-col cols="12" sm="12" xl="2" md="2" class="pa-0 mr-10">
                    Последный прозвон
                    <CustomDataPicker
                      v-model="form.last_call"
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
                      @setNow="setNow('last_call', true)"
                      @setAfter="setAfter('last_call', true)"
                    />
                  </v-col>
                  <v-col cols="12" sm="12" xl="2" md="2" class="py-0 mr-10">
                    Перезвонить
                    <CustomDataPicker
                      v-model="form.callback"
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
                      @setNow="setNow('callback', true)"
                      @setAfter="setAfter('callback', true)"
                    />
                  </v-col>
                  <v-col cols="12" sm="12" xl="2" md="2" class="py-0 mr-10">
                    Приедет

                    <CustomDataPicker
                      v-model="form.will_arrive"
                      value-type="YYYY-MM-DD"
                      type="date"
                      :limit="$auth.user?.role_id == 2 ? 'before' : null"
                      format="DD.MM.YYYY"
                      @setNow="setNow('will_arrive', false)"
                      @setAfter="setAfter('will_arrive', false)"
                    />
                  </v-col>

                  <v-col cols="12" sm="12" xl="2" md="2" class="py-0 mr-10">
                    Приехал
                    <CustomDataPicker
                      v-model="form.arrived_date"
                      value-type="YYYY-MM-DD"
                      type="date"
                      :limit="$auth.user?.role_id == 2 ? 'before' : null"
                      format="DD.MM.YYYY"
                      @setNow="setNow('arrived_date', false)"
                      @setAfter="setAfter('arrived_date', false)"
                    />
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
                  <v-col cols="12" sm="12" xl="2" md="2" class="py-0 mb-2">
                    <v-select
                      v-model="form.operator_id"
                      :items="operators"
                      item-value="id"
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
                      dense
                      outlined
                      hide-details
                      required
                      :rules="[validateOperator]"
                      label="Оператор"
                      :disabled="role_id === 2 && form.operator_id !== null"
                    >
                    </v-select>
                  </v-col>
                  <v-switch
                    v-model="form.commercial_offer"
                    ripple
                    label="КП"
                    dense
                    class="ml-3 mt-2"
                  />

                  <v-switch
                    v-model="form.approved"
                    :disabled="!listen_record.includes(role_id)"
                    ripple
                    label="Одобрен"
                    dense
                    class="ml-10 mt-2"
                  />
                  <v-switch
                    v-model="form.canceled"
                    ripple
                    label="Отказался"
                    dense
                    class="ml-3 mt-2"
                  />

                  <v-col cols="12" sm="12" xl="8" md="8" class="py-0">
                    <v-textarea
                      v-model="form.comment"
                      rows="5"
                      class="mt-2"
                      outlined
                      dense
                      hide-details="auto"
                      label="Комментарий"
                      :readonly="
                        user_id !== order?.operator_id &&
                        role_id !== 1 &&
                        role_id !== 3 &&
                        role_id !== 6
                      "
                    />
                  </v-col>

                  <v-col cols="12" sm="12" xl="4" md="4" class="py-0">
                    <v-textarea
                      v-model="form.general_comment"
                      rows="5"
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

            <v-col cols="12" xl="2" sm="12" md="2">
              <v-card class="px-3 py-1" outlined tile>
                <v-btn
                  color="blue"
                  class="white--text my-2"
                  :loading="busy"
                  block
                  @click="save()"
                >
                  Сохранить
                </v-btn>

                <v-btn
                  color="warning"
                  class="white--text mb-2"
                  :loading="busy"
                  block
                  @click="save('return')"
                >
                  <v-icon>mdi-content-save</v-icon>
                  вернуться к списку
                </v-btn>

                <v-btn
                  color="error"
                  class="white--text mb-2"
                  :loading="busy"
                  block
                  @click="isCopyDialog = true"
                >
                  <v-icon class="mr-2">mdi-fast-forward</v-icon>
                  В другой салон
                </v-btn>

                <v-btn
                  color="purple"
                  light
                  class="white--text mb-2"
                  :loading="busy"
                  block
                  @click="openArrive()"
                >
                  <v-icon class="mr-2">mdi-share-outline</v-icon>
                  Передать в приезд
                </v-btn>

                <v-btn
                  v-role-or-permission="'admin|can_pass_order'"
                  color="lime"
                  class="text--secondary mb-2"
                  :loading="busy"
                  block
                  @click="toConsultation()"
                >
                  <v-icon class="mr-2">mdi-tablet</v-icon>
                  Передать в К.Ц.
                </v-btn>

                <v-btn
                  v-if="role_id === 3 || role_id === 1 || role_id === 6"
                  color="grey"
                  class="white--text mb-2"
                  :loading="busy"
                  block
                  @click="openApproveDialog()"
                >
                  <v-icon class="mr-2">mdi-trash-can-outline</v-icon>
                  Отправить в ЧС
                </v-btn>

                <v-btn
                  v-if="showroom_id !== 28"
                  color="deep-orange"
                  class="white--text mb-2 text-body-2"
                  :loading="busy"
                  block
                  @click="openDeferDialog()"
                >
                  <v-icon class="mr-2">mdi-timer-pause-outline</v-icon>
                  Отложенная покупка
                </v-btn>
                <span class="pb-12"></span>
                <template v-if="form?.defer_purchase">
                  <v-chip class="ma-2" small color="warning">
                    Отложенная покупка ({{
                      $moment(form?.defer_purchase?.return_date).format(
                        'DD.MM.YYYY'
                      )
                    }})
                  </v-chip>
                  <v-btn
                    v-if="role_id !== 2"
                    color="amber lighten-2"
                    class="mb-2 caption"
                    :loading="busy"
                    block
                    @click="deleteDefer()"
                  >
                    <v-icon class="mr-2">mdi-trash-can-outline</v-icon>
                    Удалить из отложенных
                  </v-btn>
                </template>
              </v-card>
            </v-col>
          </v-row>
        </v-form>
      </v-col>

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
                    dense
                    @change="updatePreview()"
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
                    item-value="id"
                    label="Оператор"
                    :rules="[smsRules.required]"
                    outlined
                    hide-details
                    dense
                    @change="onOperatorChange()"
                  />
                </v-col>

                <v-col cols="12" md="4">
                  <v-text-field
                    v-model="smsData.client_name"
                    label="Имя клиента"
                    :rules="[smsRules.required]"
                    outlined
                    hide-details
                    dense
                    @change="updatePreview()"
                  />
                </v-col>

                <v-col cols="12" md="6">
                  <v-switch
                    v-model="useTemplate"
                    label="Использовать шаблон"
                    inset
                    hide-details
                    @change="updatePreview()"
                  />
                </v-col>

                <v-col v-if="useTemplate" cols="12" md="12">
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
                    :loading="loadingTemplates"
                    :rules="[smsRules.required]"
                    outlined
                    dense
                    clearable
                    @change="updatePreview()"
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
                            style="white-space: normal; word-break: break-word"
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

                        <v-btn icon x-small @click.stop="deleteTemplate(item)">
                          <v-icon small>mdi-delete</v-icon>
                        </v-btn>
                      </div>
                    </template>

                    <template #append-outer>
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
            {{ tplMode === 'create' ? 'Новый шаблон' : 'Редактировать шаблон' }}
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

      <v-dialog
        v-model="arriveDialog"
        persistent
        max-width="700px"
        content-class="arrive_dialog"
      >
        <v-card>
          <v-card-title>
            <span class="headline">Передать завку в приезд</span>
          </v-card-title>
          <v-card-text class="mb-0 pb-o">
            <v-container>
              <v-form id="arrive-form" ref="copy_form" lazy-validation>
                <v-row dense>
                  <v-col cols="6" class="py-0 text-right">
                    <v-text-field
                      v-model="arrival.client_name"
                      outlined
                      dense
                      label="Клиент"
                      required
                    />
                  </v-col>
                  <v-col cols="6" class="py-0 text-right">
                    <v-text-field
                      v-model="arrival.car_name"
                      outlined
                      dense
                      label="Авто"
                      required
                    />
                  </v-col>
                  <v-col cols="4" class="py-0 text-right">
                    <v-text-field
                      v-model="arrival.phone_number"
                      outlined
                      dense
                      label="Телефон"
                    />
                  </v-col>
                  <v-col cols="4" class="py-0 text-right">
                    {{ arrival.arrival_date }}

                    <date-picker
                      v-model="arrival_date"
                      format="DD.MM.Y"
                      value-type="YYYY-MM-DD"
                      placeholder="Дата"
                    />
                  </v-col>

                  <v-col cols="4" class="py-0 text-right">
                    <v-text-field
                      v-model="arrival.site_name"
                      outlined
                      dense
                      label="Сайт"
                    />
                  </v-col>

                  <v-col cols="6" class="py-0 text-right">
                    <v-autocomplete
                      v-model="arrival.region_id"
                      :items="regions"
                      item-value="id"
                      item-text="name"
                      placeholder="Регион"
                      dense
                      outlined
                    />
                  </v-col>
                  <v-col cols="6" class="py-0 text-right">
                    <v-text-field
                      v-model="arrival.operator"
                      outlined
                      dense
                      label="Оператор"
                      required
                    />
                  </v-col>

                  <v-col cols="4" class="py-0 text-right">
                    <v-text-field
                      v-model="arrival.initial_fee"
                      outlined
                      dense
                      :rules="maxLength(120)"
                      label="ПВ"
                    />
                  </v-col>

                  <v-col cols="4" class="py-0 text-right">
                    <v-select
                      v-model="arrival.payment_method"
                      :items="
                        payment_methods.filter((l) => {
                          return l.id === 1 || l.id === 2
                        })
                      "
                      item-text="name"
                      no-data-text="Нету данных"
                      item-value="id"
                      menu-props="auto"
                      label="Способ оплаты"
                      hide-details="auto"
                      outlined
                      dense
                      clearable
                      @click:clear="
                        $nextTick(() => (arrival.payment_method = null))
                      "
                    />
                  </v-col>

                  <v-col cols="4" class="py-0 text-right">
                    <v-text-field
                      v-model="arrival.payment"
                      outlined
                      dense
                      label="Платёж"
                    />
                  </v-col>
                  <v-col cols="12" class="py-0 text-right">
                    <v-textarea
                      v-model="arrival.comment"
                      outlined
                      rows="3"
                      hide-details
                      dense
                      label="Комментарий"
                      required
                    />
                  </v-col>
                </v-row>
              </v-form>
            </v-container>
          </v-card-text>

          <v-card-actions class="mt-n4">
            <v-spacer />
            <v-checkbox
              v-model="arrival.visited"
              class="mr-4 mt-2"
              label="Приехал"
              outlined
              dense
            />
            <v-btn class="mr-3" color="error" @click="closeArrive()"
              >Отмена</v-btn
            >
            <v-btn class="mr-4" color="primary" @click="toArrive()"
              >Отправить</v-btn
            >
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="isCopyDialog" max-width="400px">
        <v-card>
          <v-card-title>
            <span class="headline">Передача заявки</span>
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
                        sorted_showrooms.filter(
                          (l) => l.id != $route.params.showroom
                        )
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

      <v-dialog v-model="approveDialog" max-width="290">
        <v-card>
          <v-card-title class="headline">
            Вы хотите отправить заявку в ЧС?
          </v-card-title>
          <v-card-actions>
            <v-spacer />
            <v-btn color="green darken-1" text @click="toBlacklist()">
              Да
            </v-btn>
            <v-btn color="error darken-1" text @click="approveDialog = false">
              Нет
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="deferDialog" max-width="290">
        <v-card>
          <v-card-title class="headline">
            Вы хотите отправить заявку в отложенные покупки?
          </v-card-title>
          <v-card-text>
            <date-picker
              v-model="deferred_purchase_date"
              format="DD.MM.Y"
              value-type="YYYY-MM-DD"
              placeholder="Дата"
            />
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn color="green darken-1" text @click="toDeferPurchase()">
              Да
            </v-btn>
            <v-btn color="error darken-1" text @click="deferDialog = false">
              Нет
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <repeat-dialog
        :show.sync="showRepeatDialog"
        :title="
          'Заявка № ' +
          repeatItem.id +
          ': другие заявки по номеру: ' +
          order.phone
        "
        :data="repeats"
      />
      <repeat-dialog
        :show.sync="showAllRepeatDialog"
        :title="
          'Заявка № ' + order.id + ': другие заявки по номеру: ' + order.phone
        "
        :data="all_repeats"
        :show-showroom="true"
      />
    </v-row>
    <v-row justify="center">
      <v-col cols="12" md="10">
        <div
          style="color: #3b99e0; font-weight: bold; pointer: cursor"
          @click="loadHistory()"
        >
          Показать историю операций
        </div>
        <v-data-table
          v-if="history_active"
          dense
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
                    v-if="item.properties?.create_time"
                    class="activities"
                    width="100%"
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
                    v-if="
                      item.properties?.attributes &&
                      Array.isArray(item.properties?.attributes) == false
                    "
                    class="activities"
                    width="100%"
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
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import DatePicker from 'vue2-datepicker'
import DTPicker from '~/components/DTPicker'
import BreadCrumb from '~/components/BreadCrumb'
import DPicker from '~/components/DPicker'
import CustomDataPicker from '~/components/CustomDataPicker'
import { activity } from '~/configs/activity.json'
import RepeatDialog from '~/components/RepeatDialog'

export default {
  name: 'EditOrder',
  components: {
    RepeatDialog,
    DTPicker,
    DPicker,
    BreadCrumb,
    CustomDataPicker,
    DatePicker,
  },
  layout: 'edit',
  middleware: 'auth',
  data() {
    return {
      activity,
      dialog: false,
      arriveDialog: false,
      approveDialog: false,
      deferDialog: false,
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
      oldCallback: null,
      oldLastCall: null,
      oldWillArrive: null,
      oldArrivedDate: null,
      showTimePanel: false,
      types1: ['Звонок', 'Кредит', 'Экспресс-кредит', 'Seo'],

      sms: {
        text: '',
        phone: '',
        phone_extension: '',
        template: 0,
      },
      arrival_date: null,
      deferred_purchase_date: null,
      arrival: {
        payment: null,
        phone_number: null,
        initial_fee: null,
        client_name: null,
        car_name: null,
        price: null,
        date: null,
        payment_method: null,
        operator: null,
        showroom_id: null,
        site_name: null,
        visited: null,
      },
      listen_record: [1, 3, 5, 6],
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
      showRepeatDialog: false,
      showAllRepeatDialog: false,
      repeats: [],
      all_repeats: [],
      repeatItem: {
        id: '',
        phone: '',
      },
      repeatDefault: {
        id: '',
        phone: '',
      },
      payment_methods: [
        { id: 7, name: 'Не определено' },
        { id: 1, name: 'Наличными' },
        { id: 2, name: 'В кредит' },
        { id: 4, name: 'Лизинг' },
        { id: 5, name: 'Не дозвон' },
        { id: 6, name: 'Повтор' },
        { id: 10, name: 'Повтор ДИ' },
        { id: 9, name: 'Не ликвид' },
      ],
      form: {
        client_name: '',
        email: '',
        phone: '',
        phone_2: '',
        phone_3: '',
        work_phone: '',
        operator_id: '',
        arrival_id: '',
        drop_id: '',
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
        car_status_id: '',
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
        status_id: null,
        date_of_sale: '',
        call_heard: '',
        entry_point: '',
        utm_source: '',
        utm_medium: '',
        utm_term: '',
        utm_campaign: '',
        type_id: '',
        comment: '',
        general_comment: '',
        commercial_offer: '',
        approved: '',
        canceled: '',
      },
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
      smsDialog: false,
      loadingTemplates: false,
      selectedTemplateId: null,

      tplDialog: false,
      tplMode: 'create', // create | edit
      tplValid: false,
      tplSaving: false,
      validSms: false,
      tplForm: {
        id: null,
        name: '',
        body: '',
      },

      smsRules: {
        required: (v) => !!v || 'Обязательное поле',
      },
    }
  },
  async fetch({ store, $axios, $gates, $auth, params: { showroom, id } }) {
    // this.form = response.data
    /* console.log('res', response)
    if (response === null) {
      if (process.server) {
        $nuxt.context.res.statusCode = 404;
      }
      throw new Error("Заявка не найдена!!!");
    } */

    await store.dispatch('user/toggle', false)
    await store.dispatch('order/fetchTypes')
    await store.dispatch('order/fetchStatuses')
    await store.dispatch('order/fetchTrashes')
    await store.dispatch('order/fetchArrivalStatuses')
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
    })
    await store.dispatch('order/fetch_sms_templates', {
      id,
      operator_id: $auth.user?.id,
    })
    await store.dispatch('permission/fetchPermissions')

    const roles = await $axios.$get('/roles')
    const permissions = await $axios.$get('/permissions')

    $gates.setPermissions(permissions)
    $gates.setRoles(roles)
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
    banks() {
      return this.$store.state.credit.banks
    },
    statuses() {
      return this.$store.state.order.statuses
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
      return this.$store.state.property.all_models
    },
    tradein_models() {
      return this.$store.state.property.all_models.filter(
        (l) => l.brand_id === this.form?.tradein_mark_id
      )
    },
    filtered_models() {
      return this.$store.state.property.all_models.filter(
        (l) => Number(l.brand_id) === Number(this.form?.mark_id)
      )
    },

    showrooms() {
      return this.$store.state.showroom.showrooms
    },
    sorted_showrooms() {
      const sortBy = [14, 2, 4, 10, 5, 7, 8, 13, 15, 17, 6]
      return this.showrooms
        .filter((showroom) => sortBy.includes(showroom.id))
        .sort((a, b) => sortBy.indexOf(a.id) - sortBy.indexOf(b.id))
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
          href: '/crm/' + this.showroom_id + '/orders',
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
    arriveDialog(value) {
      if (!value) {
        this.closeArrive()
      }
    },
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
          this.oldLastCall = this.form.last_call
        }
        if (this.form.callback !== null) {
          this.form.callback = this.$moment(this.form.callback).format(
            'DD.MM.YYYY HH:mm'
          )
          this.oldCallback = this.form.callback
          console.log('eeee_', this.form.callback)
        }

        this.oldWillArrive = this.form.will_arrive
        this.oldArrivedDate = this.form.arrived_date
        this.repeatItem.id = this.order.id
        this.repeatItem.phone = this.order.phone
        this.repeatItem.showroom_id = this.order.showroom_id
        await this.$axios
          .post('orders/repeats', { item: this.repeatItem })
          .then(async (response2) => {
            this.repeats = await response2.data
          })
      })
  },

  methods: {
    maxLength(length) {
      return [
        (v) => {
          if (!v || v.length <= length) {
            return true // Pass validation if the field is empty or its length is less than or equal to 350
          } else {
            return 'Текст должен содержать не более ' + length + ' символов.'
          }
        },
      ]
    },

    getModels(markId = null) {
      this.form.model_id = null
      if (markId !== 0) {
        this.$store.dispatch('property/fetchModels', { markId })
      }
    },

    carYearRequired(value) {
      if (this.form.car_status_id === 2) {
        return !!value || 'Год is required'
      }
      return true
    },

    setLink(url) {
      if (!/^https:\/\//i.test(url) && !/^http:\/\//i.test(url)) {
        // If it doesn't, prepend 'https://' to the URL
        url = 'https://' + url
      }
      return url
    },
    changedType() {
      console.log(this.form.type_id)
      if (this.form.type_id === 12) {
        this.form.site_id = 6469
      }
    },
    async toConsultation() {
      this.busy = true
      const postData = {
        showroom_id: this.form.showroom_id,
        client: this.form.client_name,
        phone: this.form.phone,
        mark: this.marks.find((l) => l.id === this.form?.mark_id)?.name || null,
        model:
          this.models.find((l) => l.id === this.form?.model_id)?.name || null,
        region:
          this.regions.find((l) => l.id === this.form?.region_id)?.name || null,
      }
      const token = `$2a$10$wZOqtQTDzWtiWkhy5IX.S.5cTsYaxMju5fuzQUy9YJtSE6Nsz/1Gu`

      this.$axios.defaults.headers.common.Authorization = `Bearer ${token}`
      this.$axios
        .post('https://acp77.ru/api/create/consultation', postData)
        .then((response) => {
          // Handle successful response
          this.$toast.success('Передано в Call Center')
          this.dialog = false
        })
        .catch((error) => {
          // Handle error
          this.$toast.error('Произошла ошибка ' + error)
        })
      this.busy = false
    },

    async toArrive() {
      this.busy = true
      const token = `$2a$10$wZOqtQTDzWtiWkhy5IX.S.5cTsYaxMju5fuzQUy9YJtSE6Nsz/1Gu`
      this.$axios.defaults.headers.common.Authorization = `Bearer ${token}`
      this.arrival.date = this.arrival_date
      this.$axios
        .post('https://acp77.ru/api/create/arrive', this.arrival)
        .then((response) => {
          // Handle successful response
          this.$toast.success('Передана в приезд')
          this.arriveDialog = false
        })
        .catch((error) => {
          // Handle error
          this.$toast.error('Произошла ошибка ' + error)
        })
      this.busy = false
    },
    async openArrive() {
      if (!this.form?.will_arrive) {
        this.$toast.error('Пожалуйста, укажите дату приезда.')
        return
      }
      const mark =
        this.marks.find((l) => l.id === this.form?.mark_id)?.name || ''
      const model =
        this.models.find((l) => l.id === this.form?.model_id)?.name || ''
      const complectation =
        this.form?.complectation !== null ? ' ' + this.form?.complectation : ''
      this.arrival.car_name = mark + ' ' + model + complectation
      this.arrival.client_name = this.form?.client_name
      this.arrival.operator =
        this.operators.find((l) => l.id === this.form?.operator_id)
          ?.first_name || null
      this.arrival.price = this.form?.price
      this.arrival.initial_fee = this.form?.initial_fee
      this.arrival.payment_method =
        this.form.payment_method === 1
          ? 1
          : this.form.payment_method === 2
          ? 2
          : null
      this.arrival.comment = this.form?.comment ?? null
      this.arrival.visited = null
      this.arrival.region_id = this.form?.region_id || null
      this.arrival.showroom_id = this.form?.showroom_id || null
      this.arrival.site_name =
        this.sites.find((l) => l.id === this.form?.site_id)?.title ||
        this.types.find((l) => l.id === this.form?.type_id)?.name
      this.arrival_date =
        this.form?.will_arrive || this.$moment().format('YYYY-MM-DD')
      const digitsOnly = this.form.phone?.replace(/\D/g, '')
      this.arrival.phone_number = digitsOnly.slice(-4)
      this.arriveDialog = true
    },
    closeArrive() {
      this.arrival.car_name = null
      this.arrival.client_name = null
      this.arrival.operator = null
      this.arrival.price = null
      this.arrival.initial_fee = null
      this.arrival.payment_method = null
      this.arrival.comment = null
      this.arrival.region_id = null
      this.arrival.date = null
      this.arrival.phone_number = null
      this.arrival.showroom_id = null
      this.arrival_date = null
      this.arriveDialog = false
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
    async save(after) {
      try {
        await this.$refs.form.validate()
        if (!this.$refs.form.validate() || !this.valid) {
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
          !lists.includes(this.form?.status_id) &&
          this.form?.callback === null
        ) {
          this.$toast.error('Заполните поле перезвонить!!!')
          return
        }

        if (this.form?.status_id === 7 && this.form?.trash_id === null) {
          this.$toast.error('Вы не выбрали тип корзины!!!')
          return
        }

        this.busy = true
        const after3Months = this.$moment()
          .add(3, 'months')
          .format('YYYY-MM-DD HH:mm:ss')

        if (this.form.last_call !== null) {
          const st = this.form.last_call + ':00'
          this.form.last_call = this.$moment(st, 'DD.MM.YYYY HH:mm:ss').format(
            'YYYY-MM-DD HH:mm:ss'
          )
          if (
            this.form.last_call !== this.oldLastCall &&
            this.$moment(st, 'DD.MM.YYYY HH:mm:ss').isAfter()
          ) {
            this.form.last_call = this.$moment(
              st,
              'DD.MM.YYYY HH:mm:ss'
            ).format('DD.MM.YYYY HH:mm')
            this.$toast.error(
              'Поле последный прозвон не является корректной!!!'
            )
            return
          }
        }

        if (this.form.callback !== null) {
          const cb = this.form.callback + ':00'
          const call_back = this.$moment(cb, 'DD.MM.YYYY HH:mm:ss').format(
            'YYYY-MM-DD HH:mm:ss'
          )

          const midnight = this.$moment(call_back)
            .startOf('day')
            .format('YYYY-MM-DD HH:mm:ss')

          if (call_back === midnight) {
            this.$toast.error('Поле перезвонить не является корректной!!!!!')
            this.form.callback = this.$moment(cb, 'DD.MM.YYYY HH:mm:ss').format(
              'DD.MM.YYYY HH:mm'
            )
            return
          }
          if (
            this.form.callback !== this.oldCallback &&
            (this.$moment(cb, 'DD.MM.YYYY HH:mm:ss').isBefore() ||
              this.$moment(cb, 'DD.MM.YYYY HH:mm:ss').isAfter(after3Months))
          ) {
            this.$toast.error('Поле перезвонить не является корректной!!!')
            this.form.callback = this.$moment(cb, 'DD.MM.YYYY HH:mm:ss').format(
              'DD.MM.YYYY HH:mm'
            )
            this.form.last_call = this.$moment(this.form.last_call).format(
              'DD.MM.YYYY HH:mm'
            )
            return
          }
          this.form.callback = call_back
        }

        if (this.form.will_arrive !== null) {
          const today = this.$moment()
          if (
            this.form.will_arrive !== this.oldWillArrive &&
            !this.$moment(this.form.will_arrive).isSameOrAfter(today, 'day')
          ) {
            this.$toast.error('Поле приедет не является корректной!!!')
            this.form.last_call = this.$moment(this.form.last_call).format(
              'DD.MM.YYYY HH:mm'
            )
            this.form.callback = this.$moment(this.form.callback).format(
              'DD.MM.YYYY HH:mm'
            )
            return
          }
        }

        if (this.form.arrived_date !== null) {
          console.log(
            this.$moment(this.form.arrived_date).isSame(new Date(), 'day')
          )
          if (
            this.form.arrived_date !== this.oldArrivedDate &&
            !this.$moment(this.form.arrived_date).isSame(new Date(), 'day') &&
            this.role_id === 2
          ) {
            this.$toast.error('Поле приехал не является корректной!!!')
            this.form.last_call = this.$moment(this.form.last_call).format(
              'DD.MM.YYYY HH:mm'
            )
            this.form.callback = this.$moment(this.form.callback).format(
              'DD.MM.YYYY HH:mm'
            )
            return
          }
        }

        await this.$store.dispatch('order/update', { item: this.form })
        this.$toast.success('Заявка успешно обновлена')
        if (after === 'new') {
          this.$refs.form.reset()
          this.$refs.form.resetValidation()
          await this.$router.push({ name: 'create-order' })
        } else if (after === 'return') {
          this.$refs.form.reset()
          this.$refs.form.resetValidation()
          await this.$router.push('/crm/' + this.form?.showroom_id + '/orders')
        }
        if (this.form.last_call !== null) {
          this.form.last_call = this.$moment(
            this.form.last_call + ':00'
          ).format('DD.MM.YYYY HH:mm')
        }
        if (this.form.callback !== null) {
          this.form.callback = this.$moment(this.form.callback).format(
            'DD.MM.YYYY HH:mm'
          )
        }
      } catch (e) {
        // this.$sentry.captureException(e)
        this.$toast.error('Произошла ошибка:' + e?.message)

        if (this.form.last_call !== null) {
          this.form.last_call = this.$moment(
            this.form.last_call + ':00'
          ).format('DD.MM.YYYY HH:mm')
        }
        if (this.form.callback !== null) {
          this.form.callback = this.$moment(this.form.callback + ':00').format(
            'DD.MM.YYYY HH:mm'
          )
        }
      } finally {
        this.busy = false
      }
    },
    setNow(field, isDateTime = false) {
      this.form[field] = isDateTime
        ? this.$moment().format('DD.MM.YYYY HH:mm')
        : this.$moment().format('YYYY-MM-DD')
      this.open_dtp = false
      this.open_dtp_callback = false
    },
    setArrivalDate(field) {
      this.arrival[field] = this.$moment().format('DD.MM.YYYY')
    },
    setAfter(field, isDateTime = false) {
      this.form[field] = isDateTime
        ? this.$moment().add('1', 'days').format('DD.MM.YYYY HH:mm')
        : this.$moment().add('1', 'days').format('YYYY-MM-DD')
      this.open_dtp = false
      this.open_dtp_callback = false
    },
    setHour(field) {
      console.log('fired')
    },
    makeLink() {
      if (this.form.client_name === null) return
      const fio = this.form.client_name?.split(' ')
      let birthday = null
      if (this.form?.birthday) {
        birthday = '&is%5Bdate%5D=' + this.form?.birthday
      }
      return `https://fssp.gov.ru/iss/ip?is[variant]=1&is%5Bregion_id%5D%5B0%5D=-1&${
        fio.length ? 'is%5Blast_name%5D=' + fio[0] : ''
      }${fio.length > 0 ? '&is%5Bfirst_name%5D=' + fio[1] : ''}${
        fio.length > 1 ? '&is%5Bpatronymic%5D=' + fio[2] : ''
      }${birthday || ''}`
    },
    async call(phone) {
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

    async openDialogSms(field = 'phone') {
      this.smsDialog = true
      this.smsData.client_name = this.form.client_name
      this.smsData.phone = this.form[field]
      this.smsData.operator_name = this.form.operator?.first_name

      await this.$store.dispatch('order/fetch_sms_templates', {
        id: this.showroom_id,
        operator_id: this.$auth.user?.id,
      })
      this.smsData.operator_id = this.$auth.user?.id
      this.updatePreview()
    },

    copyName() {
      navigator.clipboard
        .writeText(this.form?.client_name)
        .then(() => {
          this.$toast.success('ФИО успешно скопирован')
        })
        .catch((err) => {
          this.$toast.error('Произошла ошибка:' + err)
        })
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
    async sendSMS() {
      this.$refs.sms_form.validate()
      if (this.smsValid === true) {
        await this.$axios
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
      const percent = this.form['percent_' + bank] / 12 / 100
      let period = 1
      if (typeof this.form.credit_period === 'object') {
        period = this.form.credit_period?.id * 12
      } else {
        period = this.form.credit_period * 12
      }

      const result =
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
      }
    },
    currency(value) {
      if (isNaN(value)) {
        return value
      }
      const val = (value / 1).toFixed(0).replace('.', ',')
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ')
    },
    openBanki() {
      if (this.form.car_number) {
        const car_number = this.form.car_number.split(' ').join('')
        const link = `https://www.banki.ru/insurance/order/auto/type/osago/short-flow/steps/auto?licensePlate=${car_number}&source=main_widget`
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
              this.isCopyDialog = false
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
    async toDeferPurchase() {
      const item = {
        showroom_id: this.copyShowroom,
        order_id: this.$route.params.id,
        purchase_date: this.deferred_purchase_date,
      }
      if (this.deferred_purchase_date !== null) {
        try {
          this.$store
            .dispatch('order/deferPurchase', { item })
            .then((res) => {
              this.$toast.success('Заявка отправлена в отложенные покупки!!! ')
              this.deferDialog = false
            })
            .catch((error) => {
              console.log('err ', error)
              this.$toast.error('Произошла ошибка' + error?.message)
            })
        } catch (e) {
          this.$toast.success('Произошла ошибка:' + e)
        }
      } else {
        this.$toast.error('Введите дату')
      }
    },
    openApproveDialog() {
      this.approveDialog = true
    },
    openDeferDialog() {
      this.deferDialog = true
    },
    async deleteDefer() {
      const confirmed = confirm(
        'Вы действительно хотите удалить из отложенных?'
      )
      if (!confirmed) return false
      try {
        await this.$axios.post('orders/delete-deferred-item', {
          id: this.form?.id,
        })
        this.$toast.success('Заявка удалена успешно!')
      } catch (error) {
        this.$toast.error(
          'Произошла ошибка при удалении заявки. Попробуйте позже.'
        )
      }
    },
    async toBlacklist() {
      try {
        await this.$store
          .dispatch('order/blacklistRequest', {
            order_id: this.$route.params.id,
          })
          .then((res) => {
            this.$toast.success('Заявка успешно на отправку ЧС отправлена.')
            this.isCopyDialog = false
          })
          .catch((error) => {
            console.log('err ', error)
            this.$toast.error('Произошла ошибка' + error?.message)
          })
      } catch (e) {
        this.$toast.success('Произошла ошибка:' + e)
      } finally {
        this.approveDialog = false
      }
    },
    validatePayment(value) {
      const allowedStatusIds = [1, 3, 8, 13, 15, 16, 1000]
      if (value !== null || allowedStatusIds.includes(this.form.status_id)) {
        return true
      } else {
        return 'Выберите способ оплаты'
      }
    },
    resetValidation() {
      this.$refs.form.resetValidation()
    },

    validateOperator(value) {
      if (value === null) {
        return 'Выберите оператора'
      }
      return true
    },
    openRepeat() {
      this.showRepeatDialog = true
    },
    async openRepeats() {
      await this.$axios
        .post('orders/all-repeats', { item: this.order })
        .then((response) => {
          this.all_repeats = response.data
        })
      this.showAllRepeatDialog = true
    },
    changedStatus() {
      if (this.form.status_id === 6) {
        this.form.arrived_date = this.$moment().format('YYYY-MM-DD')
      }

      if (this.form?.status_id === 8) {
        this.form.payment_method = 6
      }
      if (this.form?.status_id === 16) {
        this.form.payment_method = 10
      }
      if (this.form?.status_id === 15) {
        this.form.payment_method = 9
      }
      this.resetValidation()
    },

    getSelectedTemplate() {
      return (
        this.sms_templates.find((t) => t.id === this.selectedTemplateId) || null
      )
    },

    async onOperatorChange() {
      await this.$store.dispatch('order/fetch_sms_templates', {
        id: this.showroom_id,
        operator_id: this.smsData.operator_id,
      })
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
    async sendSms(field) {
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
<style lang="scss">
$namespace: 'mx';
$default-color: #555;
$primary-color: #1284e7;
@import '~vue2-datepicker/scss/index.scss';
.ads {
  font: 12px/1.2em Arial;
  font-weight: bold;
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

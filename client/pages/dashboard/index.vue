<template>
  <v-hover>
    <v-container fluid>
      <v-row no-gutters align="start" class="d-flex">
        <v-col cols="12">
          <v-app-bar dense class="grey lighten-5" elevate-on-scroll>
            <v-toolbar-title class="text--primary mr-2">
              Дашборд
              <span v-if="!filter_from || !filter_to"> {{ $moment().format('DD.MM.YYYY') }}</span>
              <span v-else>{{ $moment(filter_from).format('DD.MM.YYYY') }} -  {{
                  $moment(filter_to).format('DD.MM.YYYY')
                }}</span>
            </v-toolbar-title>

            <date-picker
              v-model="filter_from"
              format="DD.MM.Y"
              placeholder="Дата с"
              class="my-2"
            />

            <date-picker
              v-model="filter_to"
              format="DD.MM.Y"
              placeholder="Дата до"
              class="my-2 ml-2"
            />

            <v-btn color="success" dark class="my-2 ml-2" @click="filter()">
              Применить
            </v-btn>

            <v-btn color="error" dark class="ml-2" @click="reset()">
              Сбросить
            </v-btn>
          </v-app-bar>
        </v-col>
        <v-col cols="12" md="12">
          <section class="pricing-table">
            <v-container fluid>
              <v-row
                justify="center"
              >
                <v-col class="" cols="12" lg="4" md="4"
                >
                  <v-hover v-slot="{ hover }">
                    <v-card
class="w-100 item" :elevation="hover ? 12 : 2"

                            :class="[(hover ? 'on-hover' : '')]"
                    >
                      <div class="ribbon">
                        Урус
                      </div>
                      <div class="heading">
                        <v-hover v-slot="{ hover }">
                          <v-row dense>
                            <v-col cols="12" md="5" xl="4" sm="12" class="my-0 mt-2">
                              <h3><img src="/images/urus.jpg" width="145px"></h3>
                            </v-col>
                            <v-col cols="12" md="6" xl="8" sm="12" class="mt-0">
                              <v-card
                                class="mx-auto"
                                max-width="200"
                                max-height="90"
                                @click="redirect(item.link)"
                              >


                                <div style="overflow:hidden;position:relative;">
                                  <iframe
                                    style="width: 100%;height: 95px;border: 1px solid rgb(230, 230, 230);border-radius: 8px;box-sizing: border-box;"
                                    :src="'https://yandex.ru/maps-reviews-widget/52509553531?size=m'"
                                  />
                                </div>
                              </v-card>
                            </v-col>

                          </v-row>


                        </v-hover>
                      </div>
                      <v-card-text class="pa-4 ">
                        <v-row>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/arrivals/1'" title="Приезд"
                                            :value="computeCount('arrivals', 1)" :subtitle="'('+computeCount('arrivals_credit', 1) + '/'+computeCount('arrivals_nal', 1) + ')'" icon="mdi-clipboard-text-outline"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/arrivals/1'" title="Приедет"
                                            :value="computeCount('will_arrive', 1)" icon="mdi-walk" :subtitle="'('+computeCount('will_arrive_credit', 1) + '/'+computeCount('will_arrive_nal', 1) + ')'"
                            />
                          </v-col>

                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/arrivals/1'" title="Приехал"
                                            :value="computeCount('arrived', 1)" :subtitle="'('+computeCount('arrived_credit', 1) + '/'+computeCount('arrived_nal', 1) + ')'"  icon="mdi-exit-run"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/arrivals/1'" title="Не пр."
                                            :value="computeCount('not_coming', 1)" :subtitle="'('+computeCount('not_coming_credit', 1) + '/'+computeCount('not_coming_nal', 1) + ')'"  icon="mdi-walk"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/arrivals/1'" title="Не отв."
                                            :value="computeCount('not_answering', 1)" icon="mdi-walk"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/credit-request/1'"
                                            title="Забилось" :value="computeCount('credits', 1)" icon="mdi-check"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/credit-request/1'"
                                            title="Решение" :value="computeCount('credit_approved', 1)"
                                            icon="mdi-check-all"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/credit-request/1'" title="Соскок"
                                            :value="computeCount('jump', 1)" icon="mdi-check-all"
                            />
                          </v-col>
                          <v-col cols="12" lg="12" md="12" class="py-0 pl-3 pr-3">
                            <hr class="divider w-100">
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/cars/1'" title="Новые"
                                            :value="computeCount('cars', 1)" icon="mdi-car"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/used-cars/1'" title="Б/У авто"
                                            :value="computeCount('usedCars', 1)" icon="mdi-car-info"
                            />
                          </v-col>

                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/used-cars/1'" title="Авито"
                                            :value="computeCount('avito', 1)" icon="mdi-caps-lock"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/used-cars/1'" title="БУ Авито"
                                            :value="computeCount('avito_old', 1)" icon=" mdi-alpha-a-circle-outline"
                            />
                          </v-col>
                        </v-row>
                      </v-card-text>
                    </v-card>
                  </v-hover>
                </v-col>
                <v-col class="" cols="12" lg="4" md="4"
                >

                  <v-hover v-slot="{ hover }">
                    <v-card
class="w-100 item" :elevation="hover ? 12 : 2"

                            :class="[(hover ? 'on-hover' : '')]"
                    >
                      <div class="ribbon">
                        Комфорт авто
                      </div>
                      <div class="heading">
                        <v-hover v-slot="{ hover }">
                          <v-row dense>
                            <v-col cols="12" md="4" xl="5" sm="12" class="my-0 mt-1">
                              <h3><img src="/images/light-logo.png" width="140px"></h3>
                            </v-col>
                            <v-col cols="12" md="6" xl="7" sm="12" class="mt-0">
                              <v-card
                                class="mx-auto"
                                max-width="200"
                                max-height="90"
                                @click="redirect(item.link)"
                              >


                                <div style="overflow:hidden;position:relative;">
                                  <iframe
                                    style="width: 100%;height: 95px;border: 1px solid rgb(230, 230, 230);border-radius: 8px;box-sizing: border-box;"
                                    :src="'https://yandex.ru/maps-reviews-widget/239962514707?size=m'"
                                  />
                                </div>
                              </v-card>
                            </v-col>
                          </v-row>
                        </v-hover>
                      </div>
                      <v-card-text class="pa-4 ">
                        <v-row>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/arrivals/2'" title="Приезд"
                                            :value="computeCount('arrivals', 2)" :subtitle="'('+computeCount('arrivals_credit', 2) + '/'+computeCount('arrivals_nal', 2) + ')'" icon="mdi-clipboard-text-outline"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/arrivals/2'" title="Приедет"
                                            :value="computeCount('will_arrive', 2)" icon="mdi-walk" :subtitle="'('+computeCount('will_arrive_credit', 2) + '/'+computeCount('will_arrive_nal', 2) + ')'"
                            />
                          </v-col>

                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/arrivals/2'" title="Приехал"
                                            :value="computeCount('arrived', 2)" :subtitle="'('+computeCount('arrived_credit', 2) + '/'+computeCount('arrived_nal', 2) + ')'"  icon="mdi-exit-run"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/arrivals/2'" title="Не пр."
                                            :value="computeCount('not_coming', 2)" :subtitle="'('+computeCount('not_coming_credit', 2) + '/'+computeCount('not_coming_nal', 2) + ')'"  icon="mdi-walk"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/arrivals/2'" title="Не отв."
                                            :value="computeCount('not_answering', 2)" icon="mdi-walk"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/credit-request/2'"
                                            title="Забилось" :value="computeCount('credits', 2)" icon="mdi-check"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/credit-request/2'"
                                            title="Решение" :value="computeCount('credit_approved', 2)"
                                            icon="mdi-check-all"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/credit-request/2'" title="Соскок"
                                            :value="computeCount('jump', 2)" icon="mdi-check-all"
                            />
                          </v-col>
                          <v-col cols="12" lg="12" md="12" class="py-0 pl-3 pr-3">
                            <hr class="divider w-100">
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/cars/2'" title="Новые"
                                            :value="computeCount('cars', 2)" icon="mdi-car"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/used-cars/2'" title="Б/У авто"
                                            :value="computeCount('usedCars', 2)" icon="mdi-car-info"
                            />
                          </v-col>

                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/used-cars/2'" title="Авито"
                                            :value="computeCount('avito', 2)" icon="mdi-caps-lock"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/used-cars/2'" title="БУ Авито"
                                            :value="computeCount('avito_old', 2)" icon=" mdi-alpha-a-circle-outline"
                            />
                          </v-col>
                        </v-row>
                      </v-card-text>
                    </v-card>
                  </v-hover>
                </v-col>

                <v-col cols="12" md="4" xl="4" >
                  <v-hover v-slot="{ hover }">
                    <v-card
class="w-100 item" :elevation="hover ? 12 : 2"

                            :class="[(hover ? 'on-hover' : '')]"
                    >
                      <div class="ribbon mt-3">
                        АвтоПремиум
                      </div>
                      <div class="heading">
                        <v-hover v-slot="{ hover }">
                          <v-row dense>
                            <v-col cols="12" md="5" xl="5" sm="12" class="my-0 mt-2">
                              <h3><img src="/images/avrora-logo.svg" width="240px"></h3>
                            </v-col>
                            <v-col cols="12" md="6" xl="6" sm="12" class="mt-0">
                              <v-card
                                class="mx-auto"
                                max-width="200"
                                max-height="90"
                                @click="redirect(item.link)"
                              >
                                <div style="overflow:hidden;position:relative;">
                                  <iframe
                                    style="width: 100%;height: 95px;border: 1px solid rgb(230, 230, 230);border-radius: 8px;box-sizing: border-box;"
                                    :src="'https://yandex.ru/maps-reviews-widget/203736250424?size=m'"
                                  />
                                </div>
                              </v-card>
                            </v-col>
                          </v-row>
                        </v-hover>
                      </div>

                      <v-card-text class="pa-4 ">
                        <v-row>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/arrivals/4'" title="Приезд"
                                            :value="computeCount('arrivals', 4)" :subtitle="'('+computeCount('arrivals_credit', 4) + '/'+computeCount('arrivals_nal', 4) + ')'" icon="mdi-clipboard-text-outline"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/arrivals/4'" title="Приедет"
                                            :value="computeCount('will_arrive', 4)" icon="mdi-walk" :subtitle="'('+computeCount('will_arrive_credit', 4) + '/'+computeCount('will_arrive_nal', 4) + ')'"
                            />
                          </v-col>

                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/arrivals/4'" title="Приехал"
                                            :value="computeCount('arrived', 4)" :subtitle="'('+computeCount('arrived_credit', 4) + '/'+computeCount('arrived_nal', 4) + ')'"  icon="mdi-exit-run"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/arrivals/4'" title="Не пр."
                                            :value="computeCount('not_coming', 4)" :subtitle="'('+computeCount('not_coming_credit', 4) + '/'+computeCount('not_coming_nal', 4) + ')'"  icon="mdi-walk"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/arrivals/4'" title="Не отв."
                                            :value="computeCount('not_answering', 4)" icon="mdi-walk"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/credit-request/4'"
                                            title="Забилось" :value="computeCount('credits', 4)" icon="mdi-check"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/credit-request/4'"
                                            title="Решение" :value="computeCount('credit_approved', 4)"
                                            icon="mdi-check-all"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/credit-request/4'" title="Соскок"
                                            :value="computeCount('jump', 4)" icon="mdi-check-all"
                            />
                          </v-col>
                          <v-col cols="12" lg="12" md="12" class="py-0 pl-3 pr-3">
                            <hr class="divider w-100">
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/cars/4'" title="Новые"
                                            :value="computeCount('cars', 4)" icon="mdi-car"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/used-cars/4'" title="Б/У авто"
                                            :value="computeCount('usedCars', 4)" icon="mdi-car-info"
                            />
                          </v-col>

                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/used-cars/4'" title="Авито"
                                            :value="computeCount('avito', 4)" icon="mdi-caps-lock"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/used-cars/4'" title="БУ Авито"
                                            :value="computeCount('avito_old', 4)" icon=" mdi-alpha-a-circle-outline"
                            />
                          </v-col>
                        </v-row>
                      </v-card-text>
                    </v-card>
                  </v-hover>
                </v-col>
                <v-col cols="12" md="4" xl="4">
                  <v-hover v-slot="{ hover }">
                    <v-card
class="w-100 item" :elevation="hover ? 12 : 2"

                            :class="[(hover ? 'on-hover' : '')]"
                    >
                      <div class="ribbon mt-3">
                        Авангард Юг
                      </div>
                      <div class="heading">
                        <v-hover v-slot="{ hover }">
                          <v-row dense>
                            <v-col cols="12" md="4" xl="3" sm="12" class="my-0 mt-4">
                              <h3><img src="/images/logo-avangard.png" width="140px"></h3>
                            </v-col>
                            <v-col cols="12" md="6" xl="8" sm="12" class="mt-0">
                              <v-card
                                class="mx-auto"
                                max-width="200"
                                max-height="90"
                                @click="redirect(item.link)"
                              >
                                <div style="overflow:hidden;position:relative;">
                                  <iframe
                                    style="width: 100%;height: 95px;border: 1px solid rgb(230, 230, 230);border-radius: 8px;box-sizing: border-box;"
                                    :src="'https://yandex.ru/maps-reviews-widget/70645239875?size=m'"
                                  />
                                </div>
                              </v-card>
                            </v-col>
                          </v-row>
                        </v-hover>
                      </div>

                      <v-card-text class="pa-4 ">
                        <v-row>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/arrivals/5'" title="Приезд"
                                            :value="computeCount('arrivals', 5)" :subtitle="'('+computeCount('arrivals_credit', 5) + '/'+computeCount('arrivals_nal', 5) + ')'" icon="mdi-clipboard-text-outline"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/arrivals/5'" title="Приедет"
                                            :value="computeCount('will_arrive', 5)" icon="mdi-walk" :subtitle="'('+computeCount('will_arrive_credit', 5) + '/'+computeCount('will_arrive_nal', 5) + ')'"
                            />
                          </v-col>

                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/arrivals/5'" title="Приехал"
                                            :value="computeCount('arrived', 5)" :subtitle="'('+computeCount('arrived_credit', 5) + '/'+computeCount('arrived_nal', 5) + ')'"  icon="mdi-exit-run"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/arrivals/5'" title="Не пр."
                                            :value="computeCount('not_coming', 5)" :subtitle="'('+computeCount('not_coming_credit', 5) + '/'+computeCount('not_coming_nal', 5) + ')'"  icon="mdi-walk"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/arrivals/5'" title="Не отв."
                                            :value="computeCount('not_answering', 5)" icon="mdi-walk"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/credit-request/5'"
                                            title="Забилось" :value="computeCount('credits', 5)" icon="mdi-check"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/credit-request/5'"
                                            title="Решение" :value="computeCount('credit_approved', 5)"
                                            icon="mdi-check-all"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/credit-request/5'" title="Соскок"
                                            :value="computeCount('jump', 5)" icon="mdi-check-all"
                            />
                          </v-col>
                          <v-col cols="12" lg="12" md="12" class="py-0 pl-3 pr-3">
                            <hr class="divider w-100">
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/cars/5'" title="Новые"
                                            :value="computeCount('cars', 5)" icon="mdi-car"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/used-cars/5'" title="Б/У авто"
                                            :value="computeCount('usedCars', 5)" icon="mdi-car-info"
                            />
                          </v-col>

                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/used-cars/5'" title="Авито"
                                            :value="computeCount('avito', 5)" icon="mdi-caps-lock"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/used-cars/5'" title="БУ Авито"
                                            :value="computeCount('avito_old', 5)" icon=" mdi-alpha-a-circle-outline"
                            />
                          </v-col>
                        </v-row>
                      </v-card-text>
                    </v-card>
                  </v-hover>
                </v-col>
                <v-col cols="12" md="4" xl="4">
                  <v-hover v-slot="{ hover }">
                    <v-card
class="w-100 item" :elevation="hover ? 12 : 2"

                            :class="[(hover ? 'on-hover' : '')]"
                    >
                      <div class="ribbon mt-3">
                        Форсаж
                      </div>
                      <div class="heading">
                        <v-hover v-slot="{ hover }">
                          <v-row dense>
                            <v-col cols="12" md="4" xl="4" sm="12" class="my-0 mt-4">
                              <h3><img src="/images/atlant-logo.png" width="140px"></h3>
                            </v-col>
                            <v-col cols="12" md="6" xl="8" sm="12" class="mt-0">
                              <v-card
                                class="mx-auto"
                                max-width="200"
                                max-height="90"
                                @click="redirect(item.link)"
                              >
                                <div style="overflow:hidden;position:relative;">
                                  <iframe
                                    style="width: 100%;height: 95px;border: 1px solid rgb(230, 230, 230);border-radius: 8px;box-sizing: border-box;"
                                    :src="'https://yandex.ru/maps-reviews-widget/50347892953?size=m'"
                                  />
                                </div>
                              </v-card>
                            </v-col>
                          </v-row>
                        </v-hover>
                      </div>

                      <v-card-text class="pa-4 ">
                        <v-row>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/arrivals/7'" title="Приезд"
                                            :value="computeCount('arrivals', 7)" :subtitle="'('+computeCount('arrivals_credit', 7) + '/'+computeCount('arrivals_nal', 7) + ')'" icon="mdi-clipboard-text-outline"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/arrivals/7'" title="Приедет"
                                            :value="computeCount('will_arrive', 7)" icon="mdi-walk" :subtitle="'('+computeCount('will_arrive_credit', 7) + '/'+computeCount('will_arrive_nal', 7) + ')'"
                            />
                          </v-col>

                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/arrivals/7'" title="Приехал"
                                            :value="computeCount('arrived', 7)" :subtitle="'('+computeCount('arrived_credit', 7) + '/'+computeCount('arrived_nal', 7) + ')'"  icon="mdi-exit-run"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/arrivals/7'" title="Не пр."
                                            :value="computeCount('not_coming', 7)" :subtitle="'('+computeCount('not_coming_credit', 7) + '/'+computeCount('not_coming_nal', 7) + ')'"  icon="mdi-walk"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/arrivals/7'" title="Не отв."
                                            :value="computeCount('not_answering', 7)" icon="mdi-walk"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/credit-request/7'"
                                            title="Забилось" :value="computeCount('credits', 7)" icon="mdi-check"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/credit-request/7'"
                                            title="Решение" :value="computeCount('credit_approved', 7)"
                                            icon="mdi-check-all"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/tablet/credit-request/7'" title="Соскок"
                                            :value="computeCount('jump', 7)" icon="mdi-check-all"
                            />
                          </v-col>
                          <v-col cols="12" lg="12" md="12" class="py-0 pl-3 pr-3">
                            <hr class="divider w-100">
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/cars/7'" title="Новые"
                                            :value="computeCount('cars', 7)" icon="mdi-car"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/used-cars/7'" title="Б/У авто"
                                            :value="computeCount('usedCars', 7)" icon="mdi-car-info"
                            />
                          </v-col>

                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/used-cars/7'" title="Авито"
                                            :value="computeCount('avito', 7)" icon="mdi-caps-lock"
                            />
                          </v-col>
                          <v-col cols="6" lg="3" md="3" style="padding: 4px !important;">
                            <DashboardCard2
:to="'/used-cars/7'" title="БУ Авито"
                                            :value="computeCount('avito_old', 7)" icon=" mdi-alpha-a-circle-outline"
                            />
                          </v-col>
                        </v-row>
                      </v-card-text>
                    </v-card>
                  </v-hover>
                </v-col>
              </v-row>
            </v-container>
          </section>
        </v-col>
      </v-row>
    </v-container>
  </v-hover>
</template>
<script>
import { mapState } from 'vuex'
import DashboardCard2 from '~/components/DashboardCard2'

export default {
  components: { DashboardCard2 },
  middleware:'permission',
  data: () => ({
    filter_from: null,
    filter_to: null
  }),

  async fetch ({ store }) {
    await store.dispatch('user/toggle', false)
    await store.dispatch('dashboard/fetchArrivals', { dateFrom: this.filter_from, dateTo: this.filter_to })
    await store.dispatch('dashboard/fetchCreditRequests', { dateFrom: this.filter_from, dateTo: this.filter_to })
    await store.dispatch('dashboard/fetchCars')
    await store.dispatch('dashboard/fetchUsedCars')
  },

  computed: {
    ...mapState({
      cars: state => state.dashboard.cars,
      usedCars: state => state.dashboard.used_cars,
      arrivals: state => state.dashboard.arrivals,
      credits: state => state.dashboard.credit_requests
    }),
    totalArrivals () {
      return this.arrivals.filter(l => l.showroom_id === 1)
    },
    role () {
      return this.$store.state.auth.role
    },
    links () {
      return [
        {
          text: this.role?.title,
          disabled: false,
          href: '/' + this.role?.name
        },
        {
          text: 'Дашборд',
          disabled: true,
          href: '/' + this.role?.name + '/dashboard'
        }
      ]
    }
  },

  watch: {
    date (val) {
      this.editedItem.date = val ? this.$date(new Date(val), 'Y-MM-dd') : ''
    }
  },

  mounted () {
    this.$echo.channel('cars')
      .listen('CarAdded', (e) => {
        console.log('car added')
        this.$store.dispatch('dashboard/fetchCars')
      })
    this.$echo.channel('used-cars')
      .listen('UsedCarAdded', (e) => {
        console.log('used car added')
        this.$store.dispatch('dashboard/fetchUsedCars')
      })
    this.$echo.channel('credits')
      .listen('CreditCreated', (e) => {
        if (this.filter_from && this.filter_to) {
          this.reset()
        } else {
          this.$store.dispatch('dashboard/fetchCreditRequests', { dateFrom: null, dateTo: null })
        }
      })
    this.$echo.channel('arrivals')
      .listen('ArrivalCreated', (e) => {
        if (this.filter_from && this.filter_to) {
          this.reset()
        } else {
          this.$store.dispatch('dashboard/fetchArrivals', { dateFrom: null, dateTo: null })
        }
      })
  },
  methods: {
    async filter () {
      if (this.filter_from && this.filter_to) {
        await this.$store.dispatch('dashboard/fetchArrivals', { dateFrom: this.filter_from, dateTo: this.filter_to })
        await this.$store.dispatch('dashboard/fetchCreditRequests', {
          dateFrom: this.filter_from,
          dateTo: this.filter_to
        })
      }
    },
    async reset () {
      this.filter_from = null
      this.filter_to = null
      await this.$store.dispatch('dashboard/fetchArrivals', { dateFrom: null, dateTo: null })
      await this.$store.dispatch('dashboard/fetchCreditRequests', { dateFrom: null, dateTo: null })
    },
    computeCount (type, showroomId) {
      let result = null
      switch (type) {
        case 'arrivals':
          result = this.arrivals.filter(l => l.showroom_id === showroomId)
          break
        case 'arrivals_nal':
          result = this.arrivals.filter(l => l.showroom_id === showroomId && l.payment_method===1)
          break
        case 'arrivals_credit':
          result = this.arrivals.filter(l => l.showroom_id === showroomId && l.payment_method===2)
          break
        case 'will_arrive':
          result = this.arrivals.filter(l => l.showroom_id === showroomId && l.not_coming !== 1 && l.visited !== 1 && l.not_responding !== 1)
          break
        case 'not_answering':
          result = this.arrivals.filter(l => l.showroom_id === showroomId && l.not_responding === 1)
          break
        case 'not_coming':
          result = this.arrivals.filter(l => l.showroom_id === showroomId && l.not_coming === 1)
          break
        case 'not_coming_nal':
          result = this.arrivals.filter(l => l.showroom_id === showroomId && l.not_coming === 1 && l.payment_method===1)
          break
        case 'not_coming_credit':
          result = this.arrivals.filter(l => l.showroom_id === showroomId && l.not_coming === 1 && l.payment_method===2)
          break
        case 'arrived':
          result = this.arrivals.filter(l => l.showroom_id === showroomId && l.visited === 1)
          break
        case 'arrived_credit':
          result = this.arrivals.filter(l => l.showroom_id === showroomId && l.visited === 1 && l.payment_method ===2)
          break
        case 'arrived_nal':
          result = this.arrivals.filter(l => l.showroom_id === showroomId && l.visited === 1 && l.payment_method ===1)
          break
        case 'will_arrive_nal':
          result = this.arrivals.filter(l => l.showroom_id === showroomId && l.not_coming !== 1 && l.not_responding !== 1 && l.visited !== 1 && l.payment_method ===1)
          break
        case 'will_arrive_credit':
          result = this.arrivals.filter(l => l.showroom_id === showroomId && l.not_coming !== 1 && l.not_responding !== 1 && l.visited !== 1 && l.payment_method ===2)
          break
        case 'credits':
          result = this.credits.filter(l => l.showroom_id === showroomId)
          break
        case 'jump':
          result = this.credits.filter(l => l.showroom_id === showroomId && l.client_canceled === 1)
          break
        case 'credit_approved':
          result = this.credits.filter(l => l.showroom_id === showroomId && l.canceled !== 1 && ((l.absolute === 4 || l.absolute === 5) || (l.expo === 4 || l.expo === 5) ||
            (l.sovkom === 4 || l.sovkom === 5) || (l.oranzh === 4 || l.oranzh === 5) || (l.tinkoff === 4 || l.tinkoff === 5) || (l.kvant === 4 ||
              l.kvant === 5) || (l.zenit === 4 || l.zenit === 5) || (l.keb === 4 || l.keb === 5) || (l.cetelem === 4 || l.cetelem === 5) || (l.vtb === 4 || l.vtb === 5) ||
            (l.primsots === 4 || l.primsots === 5) || (l.rnb === 4 || l.rnb === 5) || (l.otp_bank === 4 || l.otp_bank === 5) || (l.alfa_bank === 4 || l.alfa_bank === 5) || (l.bistro_bank === 4 || l.bistro_bank === 5) || (l.uralsib === 4 || l.uralsib === 5) || (l.loco_bank === 4 || l.loco_bank === 5) || (l.otkritie === 4 || l.otkritie === 5)))
          break
        case 'cars':
          result = this.cars.filter(l => l.showroom_id === showroomId)
          break
        case 'usedCars':
          result = this.usedCars.filter(l => l.showroom_id === showroomId)
          break
        case 'avito':
          result = this.cars.filter(l => l.showroom_id === showroomId && l.link !== null)
          break
        case 'avito_old':
          result = this.usedCars.filter(l => l.showroom_id === showroomId && l.auto_ru !== null)
          break
        case 'priceless':
          result = this.usedCars.filter(l => l.showroom_id === showroomId && l.price <= 0)
          break
        case 'not_sale':
          result = this.usedCars.filter(l => l.showroom_id === showroomId && l.status_id === 5)
          break
        case 'repairing':
          result = this.usedCars.filter(l => l.showroom_id === showroomId && l.status_id === 3)
          break
        default:
          result = null
      }
      return result ? result.length : 0
    },
    redirect (url) {
      // let route = this.$router.resolve(url);
      window.open(url, '_blank')
    }
  },

  beforeRouteLeave (to, from, next) {
    this.$store.dispatch('user/toggle', true)
    clearInterval(this.intervalid1)
    next()
  }
}
</script>
<style scoped>
.divider {
  border: 1px dashed darkgrey;
}

.pricing-table {
  padding-top: 5px;
  margin-bottom: 5px;
  text-align: center;
}

.pricing-table .block-heading h2 {
  color: #3b99e0;
}

.pricing-table .block-heading p {
  text-align: center;
  max-width: 250px;
  margin: auto;
  opacity: 0.7;
}

.pricing-table .heading {
  text-align: center;
  padding-bottom: 5px;
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.pricing-table .item {
  background-color: #ffffff;
  box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.075);
  border-top: 2px solid #5ea4f3;
  padding: 20px;
  overflow: hidden;
  position: relative;
}

.pricing-table .col-md-5:not(:last-child) .item {
  margin-bottom: 30px;
}

.pricing-table .item button {
  font-weight: 600;
}

.pricing-table .ribbon {
  width: 160px;
  height: 32px;
  font-size: 16px;
  text-align: center;
  color: #fff;
  font-weight: bold;
  box-shadow: 0px 2px 3px rgba(136, 136, 136, 0.25);
  background: #4dbe3b;
  transform: rotate(45deg);
  position: absolute;
  right: -42px;
  top: 20px;
  padding-top: 4px;
}

.pricing-table .item p {
  text-align: center;
  margin-top: 20px;
  opacity: 0.7;
}

.pricing-table .features .feature {
  font-weight: 600;
}

.pricing-table .features h4 {
  text-align: center;
  font-size: 18px;
  padding: 5px;
}

.pricing-table .price h4 {
  margin: 15px 0;
  font-size: 45px;
  text-align: center;
  color: #2288f9;
}

.pricing-table .buy-now button {
  text-align: center;
  margin: auto;
  font-weight: 600;
  padding: 9px 0;
}

.media-subtitle {
  font-size: .75rem;
  font-weight: bold;
}

.info-count {
  font-weight: bold;
  font-size: 16px;
}
</style>

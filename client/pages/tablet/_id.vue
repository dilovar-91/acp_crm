<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links" />
      <v-row class="mt-2">
        <template>
          <template v-for="(item, i) in items">
            <v-col :key="i" cols="12" md="3">
              <v-hover v-slot="{ hover }">
                <v-card
                  :elevation="hover ? 12 : 2"
                  dark
                  :class="{ 'on-hover': hover }"
                  :to="item.link + showroom_id"
                  color="#4caf50"
                >
                  <div height="225px" class="bg-blue">
                    <v-card-title class="headline">
                      {{ item.title }}
                    </v-card-title>
                    <v-card-subtitle>{{ item.text }}</v-card-subtitle>
                    <v-card-actions>
                      <v-btn :to="'/' + item.link + showroom_id" text>
                        Перейти
                      </v-btn>
                    </v-card-actions>
                  </div>
                </v-card>
              </v-hover>
            </v-col>
          </template>
        </template>
        <v-col cols="12" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              :to="'/tablet/clients/' + showroom_id"
              color="#4caf50"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline"> Call-Center </v-card-title>
                <v-card-subtitle>Раздел Колл-центр</v-card-subtitle>
                <v-card-actions>
                  <v-btn :to="'/tablet/clients/' + showroom_id" text>
                    Перейти
                  </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>
        <v-col cols="12" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              :to="'/tablet/credit-call/' + showroom_id"
              color="#4caf50"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline"> Обзвон </v-card-title>
                <v-card-subtitle>Обзвон клиентов</v-card-subtitle>
                <v-card-actions>
                  <v-btn :to="'/tablet/credit-call/' + showroom_id" text>
                    Перейти
                  </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>

        <v-col cols="12" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              :to="'/tradeins'"
              color="#4caf50"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline"> Оценка Trade-In </v-card-title>
                <v-card-subtitle>Раздел Trade-In</v-card-subtitle>
                <v-card-actions>
                  <v-btn :to="'/tradeins'" text> Перейти </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>
        <v-col cols="12" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              :to="'/tradein/create'"
              color="#4caf50"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline">
                  Заявка на Trade-In
                </v-card-title>
                <v-card-subtitle>Раздел Trade-In</v-card-subtitle>
                <v-card-actions>
                  <v-btn :to="'/tradein/create'" text> Перейти </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>
        <v-col cols="12" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              :to="'/tablet/usedcars/' + showroom_id"
              color="#4caf50"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline"> Автомобили БУ </v-card-title>
                <v-card-subtitle>Список Б/У автомобилей</v-card-subtitle>
                <v-card-actions>
                  <v-btn :to="'/tablet/usedcars/' + showroom_id" text>
                    Перейти
                  </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>
        <v-col cols="12" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              :to="'/tablet/jump/1'"
              color="#4caf50"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline"> Клиенты Комфорт </v-card-title>
                <v-card-subtitle>Клиенты Комфорт</v-card-subtitle>
                <v-card-actions>
                  <v-btn :to="'/tablet/jump/1'" text> Перейти </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>
        <v-col cols="12" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              :to="'/tablet/jump/2'"
              color="#4caf50"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline"> Клиенты Марьино </v-card-title>
                <v-card-subtitle>Клиенты Марьино</v-card-subtitle>
                <v-card-actions>
                  <v-btn :to="'/tablet/jump/2'" text> Перейти </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>
        <v-col cols="12" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              color="#4caf50"
              @click="redirect('https://ftp.accas.ru/shared/pilot')"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline"> Файлобменник </v-card-title>
                <v-card-subtitle>Хранилище файлов и фотографии</v-card-subtitle>
                <v-card-actions>
                  <v-btn
                    text
                    @click="redirect('https://ftp.accas.ru/shared/pilot')"
                  >
                    Перейти
                  </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>
        <v-col cols="12" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              color="#4caf50"
              @click="redirect('https://ftp.accas.ru/shared/light')"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline"> Файлобменник </v-card-title>
                <v-card-subtitle>Хранилище файлов и фотографии</v-card-subtitle>
                <v-card-actions>
                  <v-btn
                    text
                    @click="redirect('https://ftp.accas.ru/shared/light')"
                  >
                    Перейти
                  </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>
        <v-col cols="12" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              color="#4caf50"
              :to="'/tablet/landings'"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline"> Лендинги </v-card-title>
                <v-card-subtitle>Лендинг страницы Марьино</v-card-subtitle>
                <v-card-actions>
                  <v-btn text :to="'/tablet/landings'"> Перейти </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>
        <v-col cols="12" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              color="#4caf50"
              :to="'/used-cars/edit'"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline">
                  Склад БУ (загрузка фото)
                </v-card-title>
                <v-card-subtitle>Общий склад</v-card-subtitle>
                <v-card-actions>
                  <v-btn text :to="'/used-cars/edit'"> Перейти </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>
        <v-col cols="12" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              color="#4caf50"
              :to="'/used-cars/list/1'"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline"> БУ Склад </v-card-title>
                <v-card-subtitle
                  >БУ автомобили Марьино с фотографиями</v-card-subtitle
                >
                <v-card-actions>
                  <v-btn text :to="'/used-cars/list/1'"> Перейти </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>
        <v-col cols="12" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              color="#4caf50"
              :to="'/used-cars/list/2'"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline"> БУ Склад </v-card-title>
                <v-card-subtitle
                  >БУ автомобили Комфорт с фотографиями</v-card-subtitle
                >
                <v-card-actions>
                  <v-btn text :to="'/used-cars/list/2'"> Перейти </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>
<script>
import BreadCrumb from '~/components/BreadCrumb'
export default {
  components: { BreadCrumb },
  middleware: 'permission',
  data: () => ({
    users: [
      'admin',
      'operator_acp',
      'operator_light',
      'operator',
      'operator2',
      'custom',
    ],
    group: ['admin', 'operator_acp', 'operator_light', 'operator', 'operator2'],
    storage: [
      'admin',
      'operator_acp',
      'operator_light',
      'operator',
      'operator2',
      'manager_acp',
      'manager_light',
      'petr',
      'manager',
      'manager2',
    ],
    items: [
      {
        title: 'Приезд',
        text: 'Таблица приезд клиентов ',
        icon: 'mdi-cash-usd-outline',
        count: 800,
        link: '/tablet/arrivals/',
      },
      {
        title: 'Кредитный отдел',
        text: 'Раздел кредитного отдела',
        count: 450,
        icon: 'mdi-car',
        link: '/tablet/credit-requests/',
      },
    ],
  }),
  async fetch({ store, params: { id } }) {
    await store.dispatch('showroom/fetchShowroom', { id })
  },
  computed: {
    showroom() {
      return this.$store.state.showroom.showroom
    },
    showroom_id() {
      return Number(this.$route.params.id) || null
    },
    role() {
      return this.$store.state.auth.role
    },
    pages() {
      return this.$store.state.permission.nest_pages
    },
    user() {
      return this.$store.state.auth.user
    },
    links() {
      return [
        {
          text: 'Главная',
          disabled: false,
          href: '/',
        },
        {
          text: 'Планшетка',
          disabled: false,
          href: '/tablet/' + this.showroom_id,
        },
        {
          text: this.showroom.name || null,
          disabled: true,
          href: '/',
        },
      ]
    },
  },
  methods: {
    redirect(url) {
      window.open(url, '_blank')
    },
  },
}
</script>
<style scoped>
.v-card {
  transition: opacity 0.4s ease-in-out;
}

.v-card:not(.on-hover) {
  opacity: 5;
}

.show-btns {
  color: rgba(255, 255, 255, 1) !important;
}
</style>

<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links" />

      <v-row class="mt-2">
        <template v-for="(item, i) in items">
          <v-col :key="i" cols="12" xl="2" md="3">
            <v-hover v-slot="{ hover }">
              <v-card
                :elevation="hover ? 12 : 2"
                dark
                :class="{ 'on-hover': hover }"
                color="#4caf50"
              >
                <div height="225px" class="bg-blue">
                  <v-card-title class="headline">
                    {{ item.title }}
                  </v-card-title>
                  <v-card-actions>
                    <v-btn :to="item.link" text>
                      Перейти
                    </v-btn>
                  </v-card-actions>
                </div>
              </v-card>
            </v-hover>
          </v-col>
        </template>
        <v-col  cols="12" xl="2" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              :to="'/' + 'used-cars/transits'"
              color="#4caf50"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline">
                  История транзитов
                </v-card-title>
                <v-card-actions>
                  <v-btn :to="'/' + 'used-cars/transits'" text>
                    Перейти
                  </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>
        <v-col v-if="['admin'].includes(role && role.name)" cols="12" xl="2" md="3">
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
                  Склад БУ (фото)
                </v-card-title>
                <v-card-actions>
                  <v-btn
                    text
                    :to="'/used-cars/edit'"
                  >
                    Перейти
                  </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>

        <v-col  cols="12" xl="2" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              color="#4caf50"
              :to="'/used-cars/list/all'"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline">
                  Склад БУ
                </v-card-title>
                <v-card-actions>
                  <v-btn
                    text
                    :to="'/used-cars/list/all'"
                  >
                    Перейти
                  </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>
        <v-col cols="12" xl="2" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              color="#4caf50"
              :to="'/used-cars/list/1'"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline">
                  БУ Склад
                </v-card-title>
                <v-card-actions>
                  <v-btn
                    text
                    :to="'/used-cars/list/1'"
                  >
                    Перейти
                  </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>
        <v-col cols="12" xl="2" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              color="#4caf50"
              :to="'/used-cars/list/2'"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline">
                  БУ Склад
                </v-card-title>
                <v-card-actions>
                  <v-btn
                    text
                    :to="'/used-cars/list/2'"
                  >
                    Перейти
                  </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>
        <v-col cols="12" xl="2" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              color="#4caf50"
              :to="'/used-cars/list/3'"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline">
                  БУ Склад
                </v-card-title>
                <v-card-actions>
                  <v-btn
                    text
                    :to="'/used-cars/list/3'"
                  >
                    Перейти
                  </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>
        <v-col cols="12" xl="2" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              color="#4caf50"
              :to="'/used-cars/simple/1'"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline">
                  Урус T
                </v-card-title>
                <v-card-actions>
                  <v-btn
                    text
                    :to="'/used-cars/simple/1'"
                  >
                    Перейти
                  </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>
        <v-col v-if="['admin', 'director', 'stock'].includes(role && role.name)" cols="12" xl="2" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              color="#4caf50"
              :to="'/used-cars/simple/2'"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline">
                  Лайт Т
                </v-card-title>
                <v-card-actions>
                  <v-btn
                    text
                    :to="'/used-cars/simple/2'"
                  >
                    Перейти
                  </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>
        <v-col cols="12" xl="2" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              color="#4caf50"
              :to="'/used-cars/cars/1'"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline">
                  Урус - Список
                </v-card-title>
                <v-card-actions>
                  <v-btn
                    text
                    :to="'/used-cars/cars/1'"
                  >
                    Перейти
                  </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>
        <v-col cols="12" xl="2" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              color="#4caf50"
              :to="'/used-cars/cars/2'"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline">
                  Лайт - Список
                </v-card-title>
                <v-card-actions>
                  <v-btn
                    text
                    :to="'/used-cars/cars/2'"
                  >
                    Перейти
                  </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>
        <v-col cols="12" xl="2" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              color="#4caf50"
              :to="'/used-cars/cars/2'"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline">
                  Лайт - Список
                </v-card-title>
                <v-card-actions>
                  <v-btn
                    text
                    :to="'/used-cars/cars/2'"
                  >
                    Перейти
                  </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>
        <v-col cols="12" xl="2" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              color="#4caf50"
              :to="'/used-cars/cars/4'"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline">
                  АвтоПремиум - Список
                </v-card-title>
                <v-card-actions>
                  <v-btn
                    text
                    :to="'/used-cars/cars/4'"
                  >
                    Перейти
                  </v-btn>
                </v-card-actions>
              </div>
            </v-card>
          </v-hover>
        </v-col>
        <v-col cols="12" xl="2" md="3">
          <v-hover v-slot="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              color="#4caf50"
              :to="'/used-cars/cars/5'"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline">
                  Авангард Юг - Список
                </v-card-title>
                <v-card-actions>
                  <v-btn
                    text
                    :to="'/used-cars/cars/5'"
                  >
                    Перейти
                  </v-btn>
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
  layout: 'user',
  middleware: 'permission',
  data: () => ({
    items: [
      {
        title: 'В поставке',
        text: 'Автомобили в поставке ',
        icon: 'mdi-cash-usd-outline',
        count: 800,
        link: 'used-cars/6',
        access: []
      },
      {
        title: 'Все салоны',
        text: 'Список Б/У автомобилей всех салонов ',
        icon: 'mdi-cash-usd-outline',
        count: 800,
        link: 'used-cars/all',
        access: []
      },
      {
        title: 'Урус',
        text: 'Список Б/У автомобилей Урус',
        count: 450,
        icon: 'mdi-car',
        link: 'used-cars/1',
        access: []
      },
      {
        title: 'Лайт',
        text: 'Список Б/У автомобилей Лайт (Рязанка)',
        count: 300,
        icon: 'mdi-car-electric',
        link: 'used-cars/2',
        access: []
      },
      {
        title: 'Марьино',
        text: 'Список Б/У автомобилей Марьино',
        count: 300,
        icon: 'mdi-car-electric',
        link: 'used-cars/3',
        access: []
      },
      {
        title: 'АвтоПремиум',
        text: 'Список автомобилей АвтоПремиум',
        count: 300,
        icon: 'mdi-car-estate',
        link: 'used-cars/4'
      },
      {
        title: 'Авангард Юг',
        text: 'Список автомобилей Авангард Юг',
        count: 300,
        icon: 'mdi-car-estate',
        link: 'used-cars/5'
      },
      {
        title: 'Проданные авто',
        text: 'Список проданных автомобилей (Все салоны)',
        count: 300,
        icon: 'mdi-car-estate',
        link: 'used-cars/sold'
      },

      {
        title: 'Список Б/У Урус',
        text: 'Урус',
        count: 450,
        icon: 'mdi-car',
        link: 'used-cars/light/1',
        access: []
      },
      {
        title: 'Список Б/У Лайт',
        text: 'Лайт',
        count: 300,
        icon: 'mdi-car-electric',
        link: 'used-cars/light/2',
        access: []
      },
      {
        title: 'Сп. Б/У Марьино',
        text: 'Марьино',
        count: 300,
        icon: 'mdi-car-electric',
        link: 'used-cars/light/3',
        access: []
      },
      {
        title: 'Список  Б/У АвтоПремиум',
        text: 'АвтоПремиум',
        count: 300,
        icon: 'mdi-car-estate',
        link: 'used-cars/light/4'
      }
    ]
  }),
  async fetch ({ store }) {
    // await store.dispatch('showroom/fetchShowrooms')
   // await store.dispatch('permission/fetchNestPages')
  },
  computed: {
    showrooms () {
      return this.$store.state.showroom.showrooms
    },
    role () {
      return this.$store.state.auth.role
    },
    pages () {
      return this.$store.state.permission.nest_pages
    },
    user () {
      return this.$store.state.auth.user
    },
    links () {
      return [
        {
          text: 'Главная',
          disabled: false,
          href: '/'
        },
        {
          text: 'Автомобили БУ',
          disabled: false,
          href: '/used-cars'
        }
      ]
    }
  },
  methods: {
    checkPage (slug) {
      let s = 0
      this.pages.map((x) => { if ((x.userpermission.some(l => l.view === 1 && x.slug === slug)) || (x.childs.length > 0 && x.childs.some(t => t.slug === slug && (t.userpermission.some(m => m.user_id === (this.user && this.user.id) && m.view === 1))))) { s++ } }
      )
      if (s > 0) {
        return true
      } else { return false }
    }
  }
}
</script>
<style scoped>
.v-card {
  transition: opacity 0.4s ease-in-out;
}

.v-card:not(.on-hover) {
  opacity: 5;
}
</style>

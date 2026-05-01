<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links" />

      <v-row class="mt-1">
        <template v-for="(item, i) in items">
          <v-col v-if="checkPage(item.link)" :key="i" cols="12" xl="2" md="3">
            <v-hover v-slot:default="{ hover }">
              <v-card
                :elevation="hover ? 12 : 2"
                dark
                :class="{ 'on-hover': hover }"
                :to="'/' + role.name + '/' + item.link"
                color="#06bad4"
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
        <v-col v-if="role && (role.name === 'coordinator' || role.name === 'coordinator2' || role.name === 'admin')" cols="12" xl="2" md="3">
          <v-hover v-slot:default="{ hover }">
            <v-card
              :elevation="hover ? 12 : 2"
              dark
              :class="{ 'on-hover': hover }"
              :to="'/' + role.name + '/' + 'cars/transits'"
              color="#06bad4"
            >
              <div height="225px" class="bg-blue">
                <v-card-title class="headline">
                  История транзитов
                </v-card-title>
                <v-card-actions>
                  <v-btn :to="'/' + role.name + '/' + 'cars/transits'" text>
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
  layout: 'user',
  middleware: 'auth',
  components: { BreadCrumb },
  data: () => ({
    items: [
      {
        title: 'В поставке',
        text: 'Автомобили в поставке',
        count: 300,
        icon: 'mdi-car-estate',
        link: 'cars/6'
      },
      {
        title: 'Все салоны',
        text: 'Список автомобилей всех салонов ',
        icon: 'mdi-cash-usd-outline',
        count: 800,
        link: 'cars/all'
      },
      {
        title: 'КомфортАвто',
        text: 'Список автомобилей КомфортАвто (Рязанка)',
        count: 300,
        icon: 'mdi-car-electric',
        link: 'cars/2'
      },
      {
        title: 'Склад',
        text: 'Список автомобилей Склад',
        count: 300,
        icon: 'mdi-car-electric',
        link: 'cars/3'
      },
      {
        title: 'АвтоПремиум',
        text: 'Список автомобилей АвтоПремиум',
        count: 300,
        icon: 'mdi-car-estate',
        link: 'cars/4'
      },
      {
        title: 'Авангард Юг',
        text: 'Список автомобилей Авангард Юг',
        count: 300,
        icon: 'mdi-car-estate',
        link: 'cars/5'
      },
      {
        title: 'Форсаж',
        text: 'Список автомобилей Авангард Юг',
        count: 300,
        icon: 'mdi-car-estate',
        link: 'cars/7'
      },
      {
        title: 'АвтоПремьер',
        text: 'Список автомобилей АвтоПремьер',
        count: 300,
        icon: 'mdi-car-estate',
        link: 'cars/8'
      },
      {
        title: 'Автодом',
        text: 'Список автомобилей Автодом',
        count: 300,
        icon: 'mdi-car-estate',
        link: 'cars/10'
      },
      {
        title: 'Автоплюс',
        text: 'Список автомобилей Автоплюс',
        count: 300,
        icon: 'mdi-car-estate',
        link: 'cars/13'
      },
      {
        title: 'Проданные авто',
        text: 'Список проданных автомобилей (Все салоны)',
        count: 300,
        icon: 'mdi-car-estate',
        link: 'cars/sold'
      }
    ]
  }),
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
          text: this.role.title,
          disabled: false,
          href: '/' + this.role?.name
        },
        {
          text: 'Новые автомобили',
          disabled: false,
          href: '/' + this.role?.name + '/cars'
        }
      ]
    }
  },
  async fetch ({ store }) {
    await store.dispatch('showroom/fetchShowrooms')
    await store.dispatch('permission/fetchNestPages')
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

.show-btns {
  color: rgba(255, 255, 255, 1) !important;
}
</style>

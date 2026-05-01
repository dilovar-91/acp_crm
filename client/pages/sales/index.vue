<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links" />

      <v-row class="mt-1">
        <template v-for="(item, i) in items">
          <v-col :key="i" cols="12" md="4">
            <v-hover v-slot:default="{ hover }">
              <v-card
                :elevation="hover ? 12 : 2"
                dark
                :class="{ 'on-hover': hover }"
                :to="'/' + item.link"
                color="#06bad4"
              >
                <div height="225px" class="bg-blue">
                  <v-card-title class="headline">
                    {{ item.title }}
                  </v-card-title>
                  <v-card-subtitle>{{ item.text }}</v-card-subtitle>
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
        title: 'КомфортАвто',
        text: 'Продажи на Рязанке',
        count: 300,
        icon: 'mdi-car-electric',
        link: 'sales/2'
      },
      {
        title: 'АвтоПремиум',
        text: 'продажи в Автопремиуме',
        count: 300,
        icon: 'mdi-car-estate',
        link: 'sales/4'
      },
      {
        title: 'Авангард Юг',
        text: 'продажи в Авангард Юг',
        count: 300,
        icon: 'mdi-car-estate',
        link: 'sales/5'
      },
      {
        title: 'Форсаж',
        text: 'продажи в Форсаже',
        count: 300,
        icon: 'mdi-car-estate',
        link: 'sales/7'
      },
      {
        title: 'АвтоПремьер',
        text: 'продажи в АвтоПремьере',
        count: 300,
        icon: 'mdi-car-estate',
        link: 'sales/8'
      },
      {
        title: 'Автодом',
        text: 'продажи в Автодоме',
        count: 300,
        icon: 'mdi-car-estate',
        link: 'sales/10'
      },
      {
        title: 'Автоплюс',
        text: 'продажи в Автоплюсе',
        count: 300,
        icon: 'mdi-car-estate',
        link: 'sales/11'
      },
      {
        title: 'Авангард',
        text: 'продажи в Автоплюсе',
        count: 300,
        icon: 'mdi-google-drive',
        link: 'sales/15'
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
          text: 'Главная',
          disabled: false,
          href: '/'
        },
        {
          text: 'Продажи',
          disabled: false,
          href: '/sales'
        }
      ]
    }
  },
  async fetch ({ store }) {
    await store.dispatch('showroom/fetchShowrooms')
    //await store.dispatch('permission/fetchNestPages')
    // await store.dispatch('credit/fetchPreparers', { id: this.$route.params.id })
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

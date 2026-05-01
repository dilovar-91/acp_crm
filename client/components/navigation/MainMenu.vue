<template>
  <v-list nav dense >
    <div v-for="(item, index) in menu" :key="index">
      <div v-if="item.key || item.text" class="pa-1 mt-2 overline">
        <template v-if="item.link" v-can="'see_'.item.link" :to="item.link">
          <nuxt-link :to="item.link" style="text-decoration: none; ">
            {{ item.text }}
          </nuxt-link>
        </template>
        <template v-else>
          {{ item.text }}
        </template>

      </div>
      <nav-menu :menu="item.items"/>
    </div>
  </v-list>
</template>

<script>
import NavMenu from './NavMenu'

export default {
  components: {
    NavMenu
  },
  props: {
    menu: {
      type: Array,
      default: () => []
    }
  }
}
</script>

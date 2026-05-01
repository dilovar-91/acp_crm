<template>
  <v-row no-gutters align="start" class="d-flex">
    <v-col cols="12" class="">
      <v-breadcrumbs :items="items" divider=">" class="py-1">
        <v-breadcrumbs-item
          slot="item"
          slot-scope="{ item }"

          exact
          :to="item.href"
          :disabled="item.disabled"
        >
          {{ item.text }}
        </v-breadcrumbs-item>
      </v-breadcrumbs>
    </v-col>
  </v-row>
</template>
<script>
export default {
  props: {
    items: {
      type: Array,
      required: true
    }
  }
}
</script>

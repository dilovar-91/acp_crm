<template>
  <v-hover v-slot="{ hover }">
    <v-card :color="color"
class="w-100 primary" :to="to" :elevation="hover ? 12 : 2"
            dark
            :class="[(hover ? 'on-hover' : '')]"
    >
      <v-card-text class="pa-1">
        <v-icon color="" x-large class="mt-0 mb-n1">
          {{ icon }}
        </v-icon>
        <p class="white--text text-h6 mb-n1">
          <span class="badge">{{ value }}</span>
        </p><p class="media-subtitle white--text mb-0">
          {{ title }}
        </p>
      </v-card-text>
    </v-card>
  </v-hover>
</template>
<script>
export default {
  props: {
    title: {
      required: true,
      type: String
    },
    value: {
      required: false,
      type: Number,
      default: 0
    },
    to: {
      required: true,
      type: String
    },
    icon: {
      required: false,
      type: String,
      default: 'mdi-star'
    },
    iconColor: {
      required: false,
      type: String,
      default: 'white'
    },
    color: {
      required: false,
      type: String,
      default: 'success'
    },
    textColor: {
      required: false,
      type: String,
      default: 'white--text'
    }
  }
}
</script>
<style scoped>
.media-subtitle {
  font-size: .80rem !important;
  font-weight: bold;
}
</style>

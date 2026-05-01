<template>
  <v-card v-if="error.statusCode === 404" class="text-center w-full error-page pa-3" width="800px">
    <v-img src="/images/illustrations/404-illustration.svg" max-height="500" contain/>
    <div class="display-2 mt-10">Как вы сюда попали?</div>
    <div class="mt-3 mb-6">Извините, мы не можем найти страницу, которую вы ищете.</div>

    <v-btn :to="'/'" block large color="primary">На главную</v-btn>
  </v-card>
  <v-card v-else-if="error.statusCode === 403" class="text-center w-full error-page pa-3" width="800px">
    <v-img src="/lock.png" max-height="600" contain/>
    <div class="display-2 mt-10 mb-4">У Вас нету доступа</div>
    <v-btn :to="'/'" block large color="warning text-black">На главную</v-btn>
  </v-card>
  <v-card v-else class="text-center w-full error-page pa-3">
    <v-img src="/images/illustrations/500-illustration.svg" max-height="500" contain/>
    <div class="display-2 mt-10">OOPS! Что-то пошло не так</div>
    <div class="mt-3 mb-10">Наши специалисты работают над устранением проблемы.</div>
    <div class="mt-3 mb-10">
      <v-alert
        border="left"
        color="red"
        dense
        prominent
        shaped
        type="error"
      >{{ error && error.message }}
      </v-alert>
    </div>

    <v-btn :to="'/'" block large color="primary">На главную</v-btn>
  </v-card>
</template>

<script>
export default {
  layout: 'simple',
  props: {
    error: {
      type: Object,
      default: () => {
      }
    }
  }
}
</script>

<style>
.error-page {
  width: 45%;
  max-width: 500px;
}
</style>


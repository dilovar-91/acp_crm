<template>
  <v-app>
    <div class="d-flex text-center flex-column flex-md-row flex-grow-1">
      <v-sheet
        class="layout-side mx-auto mx-md-1 d-none d-md-flex flex-md-column justify-space-between px-2"
      >
        <div class="mt-3 mt-md-10 pa-2">
          <div class="display-2 font-weight-bold primary--text">a-c77.ru</div>
          <div class="title my-2">AUTO CRM</div>
          <v-btn :to="'/'" class="my-4">На главную</v-btn>
        </div>
        <img
          class="w-full"
          src="/images/illustrations/signin-illustration.svg"
        />
      </v-sheet>

      <div
        class="pa-2 pa-md-4 flex-grow-1 align-center justify-center d-flex flex-column bg-warning"
      >
        <div class="layout-content ma-auto w-full">
          <nuxt />
        </div>
        <div class="overline mt-4">a-c77.ru - 2.0</div>
      </div>
    </div>
  </v-app>
</template>

<script>
export default {
  name: 'AuthLayout',
}
</script>

<style scoped>
#app {
  background: #f2f5f8;
}

.layout-side {
  width: 420px;
}

.layout-content {
  max-width: 480px;
}
</style>

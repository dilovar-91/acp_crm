<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links" />

      <v-card class="mt-4">
        <v-card-title>Импорт заявок из Excel</v-card-title>
        <v-card-text>
          <p class="mb-4 text-body-2 grey--text text--darken-1">
            Колонки в файле:
            <strong>phone</strong>, <strong>name</strong>,
            <strong>utm_source</strong>. Первая строка может быть заголовком.
          </p>

          <v-row dense>
            <v-col cols="12" md="4">
              <v-select
                v-model="showroom_id"
                :items="showrooms"
                item-text="name"
                item-value="id"
                label="Шоурум *"
                outlined
                dense
                hide-details
                :disabled="loading"
              />
            </v-col>
            <v-col cols="12" md="5">
              <v-file-input
                v-model="file"
                label="Файл Excel (.xlsx, .xls, .csv)"
                accept=".xlsx,.xls,.csv"
                outlined
                dense
                hide-details
                prepend-icon="mdi-file-excel"
                show-size
                :disabled="loading"
              />
            </v-col>
            <v-col cols="12" md="3" class="d-flex align-center">
              <v-btn
                color="primary"
                dark
                block
                :loading="loading"
                :disabled="!canImport"
                @click="importFile"
              >
                Импортировать
              </v-btn>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>

      <v-card v-if="result" class="mt-4">
        <v-card-title class="pb-0">Результат импорта</v-card-title>
        <v-card-text>
          <v-alert type="success" dense text class="mb-3">
            Импортировано: <strong>{{ result.imported }}</strong
            >, пропущено пустых строк:
            <strong>{{ result.skipped }}</strong>
          </v-alert>

          <v-alert
            v-if="result.errors && result.errors.length"
            type="warning"
            dense
            text
          >
            Ошибки ({{ result.errors.length }}):
            <ul class="mb-0 mt-2">
              <li v-for="(err, i) in result.errors" :key="i">
                Строка {{ err.line }}: {{ err.message }}
              </li>
            </ul>
          </v-alert>
        </v-card-text>
      </v-card>
    </v-container>
  </div>
</template>

<script>
import BreadCrumb from '~/components/BreadCrumb'

export default {
  name: 'CrmImportOrders',
  components: { BreadCrumb },
  layout({ $auth }) {
    return $auth.user.role_id === 4 || $auth.user.role_id === 7
      ? 'agency'
      : 'default'
  },
  middleware: ['permission', 'admin'],
  data: () => ({
    showroom_id: null,
    file: null,
    loading: false,
    result: null,
    links: [
      { text: 'CRM', disabled: false, href: '/crm' },
      { text: 'Импорт заявок', disabled: true, href: '/crm/import-orders' },
    ],
  }),
  computed: {
    showrooms() {
      return this.$store.state.showroom.showrooms || []
    },
    canImport() {
      return this.showroom_id && this.file && !this.loading
    },
  },
  async mounted() {
    if (!this.showrooms.length) {
      await this.$store.dispatch('showroom/fetchShowrooms')
    }
    if (this.$auth.user?.showroom_id && !this.showroom_id) {
      this.showroom_id = this.$auth.user.showroom_id
    }
  },
  methods: {
    async importFile() {
      if (!this.canImport) {
        return
      }

      const formData = new FormData()
      formData.append('file', this.file)
      formData.append('showroom_id', this.showroom_id)

      this.loading = true
      this.result = null

      try {
        const { data } = await this.$axios.post('/orders/import-excel', formData, {
          headers: { 'Content-Type': 'multipart/form-data' },
        })
        this.result = data
        this.$toast.success(`Импортировано заявок: ${data.imported}`)
      } catch (e) {
        const message =
          e.response?.data?.message ||
          e.response?.data?.errors?.file?.[0] ||
          'Ошибка импорта'
        this.$toast.error(message)
      } finally {
        this.loading = false
      }
    },
  },
}
</script>

<template>
  <v-card class="mx-auto toast-box" max-width="344">
    <v-card-text class="flat">
      <p class="text-h6 text--primary">
        Входящий звонок от: <b>{{ formattedPhone }}</b>
      </p>
      <p v-if="data.call_state" class="text-h6 text--primary">
        Состояние звонка: <b>{{ callStateLabel }}</b>
      </p>
      <p v-if="data.client_name" class="text-h6 text--primary">
        Клиент: <b>{{ data.client_name }}</b>
      </p>
      <p v-if="statusName" class="text-h6 text--primary">
        Статус заявки: <b>{{ statusName }}</b>
      </p>
      <p v-if="data.site_name" class="text-h6 text--primary">
        Сайт: <b>{{ data.site_name }}</b>
      </p>
      <p v-if="data.line_number" class="text-h6 text--primary">
        Линия: <b>{{ data.line_number }}</b>
      </p>
      <p v-if="assignedOperatorName" class="text-h6 text--primary">
        Менеджер заявки: <b>{{ assignedOperatorName }}</b>
      </p>
      <p v-if="callOperatorName" class="text-h6 text--primary">
        Оператор звонка: <b>{{ callOperatorName }}</b>
      </p>
      <p v-if="clientRegionName" class="text-h6 text--primary">
        Регион клиента: <b>{{ clientRegionName }}</b>
      </p>
      <p v-if="phoneRegionName" class="text-h6 text--primary">
        Регион регистрации номера: <b>{{ phoneRegionName }}</b>
      </p>
      <p v-if="phoneOperatorName" class="text-h6 text--primary">
        Оператор связи: <b>{{ phoneOperatorName }}</b>
      </p>
      <p v-if="data.another_showroom" class="text-h6 text--primary">
        С другого салона: <b>Да</b>
      </p>
      <p v-if="callStartedAt" class="text-h6 text--primary">
        Время звонка: <b>{{ formatDate(callStartedAt) }}</b>
      </p>
      <p v-if="orderCreatedAt" class="text-h6 text--primary">
        Дата заявки: <b>{{ formatDate(orderCreatedAt) }}</b>
      </p>
    </v-card-text>
    <v-card-actions class="py-0">
      <v-btn
        v-if="!data.client_name && !hasOrder"
        text
        color="teal accent-4"
        @click="createOrder"
      >
        Создать заявку
      </v-btn>
      <v-btn
        v-if="hasOrder"
        text
        color="teal accent-4"
        target="_blank"
        @click="openOrder"
      >
        Открыть заявку
      </v-btn>
      <v-btn text color="teal accent-4" @click="removeOrder">
        Удалить
      </v-btn>
    </v-card-actions>
  </v-card>
</template>
<script>
export default {
  props: {
    data: {
      type: Object,
      required: true,
    },
    toastId: {
      type: [String, Number],
      default: null,
    },
    isMini: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    formattedPhone() {
      if (this.data.phone === null || this.data.phone === undefined) {
        return 'Не указан'
      }

      const phone = String(this.data.phone).trim()
      const digits = phone.replace(/\D/g, '')
      const normalized =
        digits.length === 11 && digits.startsWith('8')
          ? `7${digits.slice(1)}`
          : digits

      if (normalized.length === 11 && normalized.startsWith('7')) {
        return normalized.replace(
          /^7(\d{3})(\d{3})(\d{2})(\d{2})$/,
          '+7 $1 $2-$3-$4'
        )
      }

      return phone || 'Не указан'
    },
    callStateLabel() {
      return this.data.call_state === 'ringing'
        ? 'Звонит'
        : this.data.call_state
    },
    statusName() {
      return this.valueName(this.data.status)
    },
    assignedOperatorName() {
      return this.personName(this.data.assigned_operator || this.data.operator)
    },
    callOperatorName() {
      return this.personName(this.data.call_operator)
    },
    clientRegionName() {
      return this.valueName(this.data.client_region || this.data.region)
    },
    phoneRegionName() {
      return this.valueName(this.data.phone_region)
    },
    phoneOperatorName() {
      return this.valueName(this.data.phone_operator)
    },
    callStartedAt() {
      return this.data.call_started_at || null
    },
    orderCreatedAt() {
      return this.data.order_created_at || this.data.date || null
    },
    hasOrder() {
      return this.data.order_id !== null && this.data.order_id !== undefined
    },
  },
  methods: {
    valueName(value) {
      if (!value) return ''
      return typeof value === 'object' ? value.name || '' : String(value)
    },
    personName(person) {
      if (!person) return ''
      if (typeof person === 'string') return person

      return [person.last_name, person.first_name, person.middle_name]
        .filter(Boolean)
        .join(' ')
    },
    formatDate(value) {
      const date = this.$nuxt.$moment(value)
      return date.isValid() ? date.format('DD.MM.YYYY HH:mm') : value
    },
    dismissToast() {
      if (this.toastId !== null) {
        this.$nuxt.$toast.dismiss(this.toastId)
      }
    },
    openOrder() {
      const showroomId =
        this.data.showroom_id ||
        this.$nuxt.$auth.user?.showroom_id ||
        this.$nuxt.$route.params?.id
      const suffix = this.isMini ? '/edit-mini' : '/edit'
      window.open(
        `https://${window.location.hostname}/crm/${showroomId}/order/${this.data.order_id}${suffix}`,
        '_blank'
      )
    },
    async createOrder() {
      try {
        const item = {
          phone: this.data.phone,
          client_name: this.data.client_name,
          operator_id: this.$nuxt.$auth.user?.id,
          showroom_id:
            this.$nuxt.$auth.user?.showroom_id ||
            this.data.showroom_id ||
            this.$nuxt.$route.params?.id,
          status_id: 1,
          type_id: 1,
          site_id: this.data.site_id,
        }

        await this.$nuxt.$axios.post('orders/save', { item })
        this.dismissToast()
        this.$nuxt.$toast.success('Заявка успешно создана', {
          position: 'top-right',
          timeout: 5000,
        })
        await this.$nuxt.$axios.post('/clear-notify', {
          entry_id: this.data.entry_id,
          showroom_id:
            this.data.showroom_id || this.$nuxt.$auth.user?.showroom_id,
          phone: this.data.phone,
        })
      } catch (error) {
        this.$nuxt.$toast.error(
          `Произошла ошибка: ${error?.response?.data?.message || error.message}`,
          {
            position: 'top-right',
            timeout: 5000,
          }
        )
      }
    },
    removeOrder() {
      this.dismissToast()
    }
  },
}
</script>
<style>
.toast-box {
    background-color: rgb(255, 234, 168) !important;
}
.flat {
    padding-top: 1px !important;
    padding-bottom: 1px !important;
}
.Vue-Toastification__toast--default.call_info {
    padding: 0 !important;
    border-radius: 2px;
    border: 1px black solid;
    color: rgba(3, 3, 3, 0);
    background-color: white;
}
</style>

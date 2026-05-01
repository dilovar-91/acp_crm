<template>
  <div>
    <v-dialog light v-model="show" max-width="850" @click:outside="close"
              @keydown.esc="close">
      <v-card flat style="background-color: #fdfdfd;" class="px-3">
        <p class="pt-4 text-center font-weight-bold">{{ title }}</p>
        <template>
          <v-simple-table style="background-color: #fdfdfd;">
            <template v-slot:default>
              <thead style="background-color: #eee;">
              <tr>
                <th class="text-center">
                  Номер
                </th>
                <th class="text-center" v-if="showShowroom">
                  Салон
                </th>
                <th class="text-center">
                  Клиент
                </th>
                <th class="text-center">
                  Повторы
                </th>
                <th class="text-center">
                  Состояние заявки
                </th>
                <th class="text-center">
                  Дата создания
                </th>
                <th class="text-center">
                  Комментарий
                </th>
              </tr>
              </thead>
              <tbody>
              <tr
                v-for="item in data"
                :key="item.id"
              >
                <td>
                  <nuxt-link :to="'/crm/' + item.showroom_id + '/order/'+item.id + '/edit'">
                    {{ item.id }}
                  </nuxt-link>
                </td>
                <td v-if="showShowroom">{{ getShowroomName(item.showroom_id) }}</td>
                <td>{{ item.client_name }}</td>


                <td>{{ item.retries }}</td>
                <td>{{ item.status?.name }}</td>
                <td>{{ $moment(item.created_at).format('DD.MM.YYYY HH:mm:ss') }}</td>
                <td>
                  <v-tooltip bottom max-width="400px" color="primary">
                    <template #activator="{ on, attrs }">
                      <div color="primary" dark v-bind="attrs" v-on="on">
                        {{ item.comment | truncate(220) }}
                      </div>
                    </template>
                    <span>{{ item.comment }}</span>
                  </v-tooltip>
                </td>
              </tr>
              </tbody>
            </template>
          </v-simple-table>
        </template>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
export default {
  name: 'RepeatDialog',
  computed: {
    showrooms() {
      return this.$store.state.showroom.showrooms
    },
  },
  props: {
    show: {
      type: Boolean,
      required: true,
    },
    showShowroom: {
      type: Boolean,
      required: false,
    },

    data: {
      type: Array,
    },

    title: {
      type: String,
      default: 'Dialog Title',
    },
  },
  methods: {
    getShowroomName(id) {
      const showroom = this.showrooms.find(s => s.id === Number(id));
      return showroom?.name;
    },

    close() {
      this.$emit('update:show', false);
    },
  },
};
</script>

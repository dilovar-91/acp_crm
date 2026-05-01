<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links"/>
      <v-row justify="center" align="center" class="mt-0">
        <v-col cols="12">
          <v-card>
            <v-data-table
              width="100%"
              dense
              :headers="headers"
              :items="activities"
              :items-per-page="activities.length"
              item-key="id"
              class="elevation-1"
            >
              <template #item.transit="{ item }">
                <v-tooltip bottom>
                  <template #activator="{ on, attrs }">
                    <v-btn
                      v-if="
                      item.transit === 1 && item.showroom_id === showroom_id
                    "
                      v-bind="attrs"
                      x-small
                      color="error"
                      v-on="on"
                      @click="cancelTransit(item)"
                    >
                      <span key="cancel">Отменить</span>
                    </v-btn>
                    <v-btn
                      v-else-if="
                      item.transit === 1 || item.showroom_id !== showroom_id
                    "
                      v-bind="attrs"
                      x-small
                      color="warning"
                      v-on="on"
                      @click="approveTransit(item)"
                    >
                      <span key="approve">Принять</span>
                    </v-btn>
                    <v-btn
                      v-else
                      v-bind="attrs"
                      x-small
                      color="success"
                      dark
                      v-on="on"
                      @click="transitItem(item)"
                    >
                      <span key="transit">Транзит</span>
                    </v-btn>
                  </template>
                  <span v-text="transitStatus(item)"/>
                </v-tooltip>
              </template>
              <template #item.properties="{ item }">
                <table border="1" width="100%">
                  <thead>
                  <tr>
                    <th>Поле</th>
                    <th>Было</th>
                    <th>Стало</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr v-for="(row, i) in (item.properties && item.properties.attributes)">
                    <td>{{ car[i] || i }}</td>
                    <td>
                      <template v-for="(old_row, l) in item.properties.old">
                        <template v-if="l===i">
                          <template v-if="l === 'mark_id' || l === 'model_id'">
                            {{ getValue(old_row, l) }}
                          </template>
                          <template v-else>
                            {{ old_row }}
                          </template>
                        </template>
                      </template>
                    </td>
                    <td>
                      <template v-for="(row, k) in item.properties.attributes">
                        <template v-if="k===i">
                          <template v-if="k === 'mark_id' || k === 'model_id'">
                            {{ getValue(row, k) }}
                          </template>
                          <template v-else>
                            {{ row }}
                          </template>
                        </template>
                      </template>
                    </td>
                  </tr>
                  </tbody>
                </table>
              </template>
              <template #item.subject_type="{ item }">
                <v-chip class="ma-2" color="warning" text-color="white" :to="'/cars'">
                  Автомобили Б.У
                </v-chip>
              </template>
              <template #item.description="{ item }">
                <v-chip v-if="item.description" class="ma-2" color="error" text-color="white">
                  {{car[item.description] || ''}}
                </v-chip>
              </template>
              <template #item.log_date="{ item }">
                <v-chip v-if="item.created_at!==null" class="ma-2" color="green" text-color="white">
                  {{
                    moment(item.created_at).format('DD-MM-YYYY HH:mm')
                  }}
                </v-chip>
              </template>
              <template #item.properties.showroom_id="{ item }">
                <v-chip v-if="item.properties && item.properties.showroom_id" class="ma-2" color="green" text-color="white">
                  {{
                    getValue(item.properties.showroom_id, 'showroom_id')
                  }}
                </v-chip>
              </template>

              <template #top>
                <v-toolbar flat dense>
                  <v-col cols="12" sm="6" md="2" class="mt-2">
                    <date-picker
                      v-model="filter.date"
                      format="DD.MM.Y"
                      placeholder="Дата до"
                      style="width: 100%; margin-top: 2px;"
                      @clear="filter.date = null"
                    />
                  </v-col>
                  <v-col cols="3" md="2" xs="6" class="mt-2">
                    <v-select
                      v-model="filter.causer_id"
                      :items="users"
                      dense
                      item-text="name"
                      item-value="id"
                      class="mt-4"
                      label="Пользователь"
                      outlined
                      clearable

                    />
                  </v-col>
                  <v-col cols="3" md="2" xs="6" class="mt-2">
                    <v-select
                      v-model="filter.showroom_id"
                      :items="showrooms"
                      dense
                      item-text="name"
                      item-value="id"
                      class="mt-4"
                      label="Салон"
                      outlined
                      clearable
                    />
                  </v-col>
                  <v-col cols="3" md="2" xs="6" class="mt-2">
                    <v-select
                      v-model="filter.mark_id"
                      :items="marks"
                      dense
                      item-text="name"
                      item-value="id"
                      label="Марка"
                      class="mt-4"
                      outlined
                      clearable
                      @change="
                            getModels(filter.mark_id);
                            deleted = false;
                          "
                      @click="filter.model_id = null"
                      @click:clear="
                            $nextTick(() => (filter.mark_id = null))
                          "
                    />
                  </v-col>
                  <v-col cols="3" md="2" xs="6" class="mt-2">
                    <v-select
                      v-model="filter.model_id"
                      :items="models"
                      dense
                      item-text="name"
                      item-value="id"
                      label="Модель"
                      class="mt-4"
                      outlined
                      clearable
                      @click="deleted = false"
                      @click:clear="
                            $nextTick(() => (filter.model_id = null))
                          "
                    ><template #no-data>
                        <span class="small">Выберите марку</span>
                      </template>
                    </v-select>
                  </v-col>
                  <v-col cols="2" class="mt-2">
                    <v-btn color="success" dark @click="apply_filter()">
                      Применить
                    </v-btn>
                    <v-btn color="error" dark @click="clear()">
                      Сброс
                    </v-btn>
                  </v-col>
                </v-toolbar>
              </template>
            </v-data-table>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>
<script>
import BreadCrumb from '~/components/BreadCrumb'
import {car} from "~/configs/car.json";

export default {
  name: 'UserActivities',
  components: {BreadCrumb},
  layout: 'default',
  data: () => ({
    car,
    filter: {
      mark_id: null,
      model_id: null,
      causer_id: null,
      date: null,
      showroom_id: null,
    },
    headers: [
      {
        text: 'Пользователь',
        value: 'user.first_name',
        align: 'center',
        sortable: true,
        width: 100
      },
      {
        text: 'Дата',
        value: 'log_date',
        align: 'center',
        sortable: false,
        width: 100
      },
      {
        text: 'Тип',
        value: 'description',
        align: 'center',
        sortable: false,
        width: "10%"
      },
      {
        text: 'Операция',
        value: 'properties',
        align: 'center',
        sortable: false,
        width: "45%"
      },
      {
        text: 'Салон',
        value: 'properties.showroom_id',
        align: 'center',
        sortable: true,
        width: 100
      },
      {
        text: 'Объект',
        value: 'subject_type',
        align: 'center',
        sortable: true,
        width: 100
      },
      {
        text: 'IP',
        value: 'properties.ip',
        align: 'center',
        sortable: true,
        width: 100
      },
      {
        text: 'Устройства',
        value: 'properties.user_agent',
        align: 'center',
        sortable: true,
        width: '50%'
      }
    ],
  }),
  async fetch({store}) {
    await store.dispatch('activity/fetchUsedActivities', {})
    await store.dispatch('showroom/fetchShowrooms')
    await store.dispatch('user/fetchUsers')
    await store.dispatch('car/fetchMarks')
    await store.dispatch('car/fetchAllModels')
  },

  computed: {
    activities() {
      return this.$store.state.activity.used_activities
    },
    users() {
      return this.$store.state.user.users
    },
    showrooms() {
      return this.$store.state.showroom.showrooms
    },
    marks() {
      return this.$store.state.car.marks
    },
    models() {
      return this.$store.state.car.models
    },
    all_models() {
      return this.$store.state.car.all_models
    },
    links() {
      return [
        {
          text: 'Главная',
          disabled: false,
          href: '/'
        },
        {
          text: 'Пользователи',
          disabled: false,
          href: '/user'
        },
        {
          text: 'Сессии Б/У машин',
          disabled: true,
          href: '/user/used-logs'
        }
      ]
    }
  },
  methods: {
    async apply_filter() {
      await this.$store.dispatch('activity/fetchUsedActivities', {
        causer_id: this.filter.causer_id,
        mark_id: this.filter.mark_id,
        model_id: this.filter.model_id,
        date: this.filter.date ? this.$moment(this.filter.date).format('YYYY-MM-DD') : null,
        showroom_id: this.filter.showroom_id
        ,
      })
    },
    getModels(markId = null) {
      if (markId !== 0) {
        this.$store.dispatch('property/fetchModels', {markId})
      }
    },
    async clear() {
      this.filter.date = null
      this.filter.user_id = null
      this.filter.user_id = null
      await this.$store.dispatch('activity/fetchUsedActivities', {
        date: null,
        mark_id: null,
        model_id: null,
        causer_id: null,
        showroom_id: null,
      })
      this.filter.date = null
      this.filter.mark_id = null
      this.filter.model_id = null
      this.filter.causer_id = null
      this.filter.showroom_id = null
    },
    getValue(id, k) {
      let res;
      switch (k) {
        case 'mark_id':
          res = this.marks.find(x => x.id === id);
          break;
        case 'model_id':
          res = this.all_models.find(x => x.id === id);
          break;
        case 'showroom_id':
          res = this.showrooms.find(x => x.id === id);
          break;
        default:
        // code block
      }
      return res && res.name
    },
  }
}
</script>
<style scoped>
table {
  border-collapse: collapse;
}
</style>

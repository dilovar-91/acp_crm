<template>
  <div>
    <v-card
      class="mx-auto"
    >
      <v-data-table

        :headers="headers"
        :items="schedules"
        class="elevation-1"
        :items-per-page="15"
        :footer-props="{
          'items-per-page-options': [10, 15, 20, 30, -1],
          'items-per-page-all-text': 'Все',
          'items-per-page-text': 'Показать:'

        }
        "

        dense
      >
        <template #top>
          <v-toolbar
            flat
          >
            <v-toolbar-title class="mr-3">
              График работы операторов
            </v-toolbar-title>
            <v-btn color="primary" class="mb-1 mr-2" @click="dialog=true">
              <v-icon>mdi-plus</v-icon>
            </v-btn>
            <v-spacer/>
            <v-dialog
              v-model="dialog"
              max-width="650px"
            >
              <v-card>
                <v-card-title>
                  <span class="headline">{{ formTitle }}</span>
                </v-card-title>
                <v-card-text>
                  <v-container>
                    <v-form
                      id="login-form"
                      ref="form"
                      v-model="valid"
                    >
                      <v-row dense>
                        <v-col
                          cols="12"
                          sm="12"
                          md="12"
                          class="py-0 text-right"

                        >
                          <v-select
                            v-model="editedItem.user_id"
                            class="mt-1"
                            :items="operators"
                            dense
                            outlined
                            :item-text="item => item.last_name ? item.first_name + ' ' + item.last_name : item.first_name"
                            item-value="id"
                            label="Оператор *"
                            hide-details
                            @click:clear="$nextTick(() => editedItem.operator_id=null)"
                          />
                        </v-col>
                        <v-col
                          cols="12"
                          sm="12"
                          md="4"
                          class="py-0"
                        >
                          <v-switch
                            v-model="editedItem.monday"
                            label="Понедельник"
                            color="success"
                            hide-details
                          ></v-switch>
                        </v-col>
                        <v-col
                          cols="12"
                          sm="6"
                          md="4"
                          class="py-0"
                        >
                          <v-switch
                            v-model="editedItem.tuesday"
                            label="Вторник"
                            color="success"
                            :value="1"
                            hide-details
                          ></v-switch>
                        </v-col>
                        <v-col
                          cols="12"
                          sm="6"
                          md="4"
                          class="py-0"
                        >
                          <v-switch
                            v-model="editedItem.wednesday"
                            label="Среда"
                            color="success"
                            :value="1"
                            hide-details
                          ></v-switch>
                        </v-col>
                        <v-col
                          cols="12"
                          sm="6"
                          md="4"
                          class="py-0"
                        >
                          <v-switch
                            v-model="editedItem.thursday"
                            label="Четверг"
                            color="success"
                            :value="1"
                            hide-details
                          ></v-switch>
                        </v-col>
                        <v-col
                          cols="12"
                          sm="6"
                          md="4"
                          class="py-0"
                        >
                          <v-switch
                            v-model="editedItem.friday"
                            label="Пятница"
                            color="success"
                            hide-details
                          ></v-switch>
                        </v-col>

                        <v-col
                          cols="12"
                          sm="6"
                          md="4"
                          class="py-0"
                        >
                          <v-switch
                            v-model="editedItem.saturday"
                            label="Суббота"
                            color="success"
                            hide-details
                          ></v-switch>
                        </v-col>

                        <v-col
                          cols="12"
                          sm="6"
                          md="4"
                          class="py-0"
                        >
                          <v-switch
                            v-model="editedItem.sunday"
                            label="Воскресенье"
                            color="success"
                            hide-details
                          ></v-switch>
                        </v-col>
                      </v-row>
                    </v-form>
                  </v-container>
                </v-card-text>

                <v-card-actions>
                  <v-spacer/>
                  <v-btn
                    v-if="editedIndex !== -1"
                    color="red darken-1"
                    class="mr-3"
                    text
                    @click="deleteItem(editedItem.id)"
                  >
                    Удалить
                  </v-btn>
                  <v-btn
                    color="blue darken-1"
                    text
                    @click="close"
                  >
                    Отмена
                  </v-btn>
                  <v-btn
                    color="blue darken-1"
                    text
                    @click="save"
                  >
                    Сохранить
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
            <v-dialog v-model="dialogDelete" max-width="500px">
              <v-card>
                <v-card-title class="headline">
                  Вы уверены, что хотите удалить этот элемент?
                </v-card-title>
                <v-card-actions>
                  <v-spacer/>
                  <v-btn color="blue darken-1" text @click="closeDelete">
                    Отменить
                  </v-btn>
                  <v-btn color="blue darken-1" text @click="deleteItemConfirm">
                    Да
                  </v-btn>
                  <v-spacer/>
                </v-card-actions>
              </v-card>
            </v-dialog>
          </v-toolbar>
        </template>

        <template #no-data>
          <v-btn
            color="primary"
          >
            Список пуст!
          </v-btn>
        </template>
        <template #body="{ items }">
          <tbody>
          <tr
            v-for="(item, index) in items"
            :key="item.id"
            @dblclick="editItem(item)"
          >
            <td>
              {{ index + 1 }}
            </td>
            <td>
              {{ item.operator?.last_name }} {{ item.operator?.first_name }}
            </td>
            <td>
              <v-icon :color="item.monday===1 ? 'success' : 'error'">
                {{ (item.monday === 1 ? 'mdi-check' : 'mdi-close') }}
              </v-icon>
            </td>
            <td>
              <v-icon :color="item.tuesday===1 ? 'success' : 'error'">
                {{ (item.tuesday === 1 ? 'mdi-check' : 'mdi-close') }}
              </v-icon>
            </td>
            <td>
              <v-icon :color="item.wednesday===1 ? 'success' : 'error'">
                {{ (item.wednesday === 1 ? 'mdi-check' : 'mdi-close') }}
              </v-icon>
            </td>
            <td>
              <v-icon :color="item.thursday===1 ? 'success' : 'error'">
                {{ (item.thursday === 1 ? 'mdi-check' : 'mdi-close') }}
              </v-icon>
            </td>
            <td>
              <v-icon :color="item.friday===1 ? 'success' : 'error'">
                {{ (item.friday === 1 ? 'mdi-check' : 'mdi-close') }}
              </v-icon>
            </td>
            <td>
              <v-icon :color="item.saturday===1 ? 'success' : 'error'">
                {{ (item.saturday === 1 ? 'mdi-check' : 'mdi-close') }}
              </v-icon>
            </td>
            <td>
              <v-icon :color="item.sunday===1 ? 'success' : 'error'">
                {{ (item.sunday === 1 ? 'mdi-check' : 'mdi-close') }}
              </v-icon>
            </td>

            <td>
                            <span v-if="item.updated_at !== null">
                                {{ $moment(item.updated_at).format('DD.MM.YYYY HH:mm:ss') }}
                            </span>
            </td>
          </tr>
          </tbody>
        </template>
      </v-data-table>
    </v-card>
  </div>
</template>
<script>
export default {
  name: 'Schedules',
  middleware: 'permission',
  data: () => ({
    dialog: false,
    dialogDelete: false,
    headers: [
      {
        text: '№',
        align: 'center',
        sortable: true,
        value: 'id'
      },
      {text: 'Оператор', value: 'operator.first_name'},
      {text: 'Понедельник', value: 'monday'},
      {text: 'Вторник', value: 'tuesday'},
      {text: 'Среда', value: 'wednesday'},
      {text: 'Четверг', value: 'thursday'},
      {text: 'Пятница', value: 'friday'},
      {text: 'Суббота', value: 'saturday'},
      {text: 'Воскресенье', value: 'sunday'},
      {text: 'Изменён', value: 'updated_at', sortable: false}
    ],
    editedIndex: -1,
    editedItem: {
      id: 0,
      user_id: 0,
      monday: 0,
      tuesday: 0,
      wednesday: 0,
      thursday: 0,
      friday: 0,
      saturday: 0,
      sunday: 0,
      comment: ''
    },
    defaultItem: {
      id: 0,
      user_id: 0,
      monday: 0,
      tuesday: 0,
      wednesday: 0,
      thursday: 0,
      friday: 0,
      saturday: 0,
      sunday: 0,
      comment: ''
    },
    valid: true,
  }),


  async fetch({store, params: {id}}) {
    await store.dispatch('user/toggle', false)
    await store.dispatch('operator/fetchSchedules', {showroom_id: id})
    await store.dispatch('operator/fetchOperators', {showroom_id: id})
  },

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? 'Добавление графика' : 'Изменение графика'
    },
    operators() {
      return this.$store.state.operator.operators
    },
    schedules() {
      return this.$store.state.operator.schedules
    },

  },

  watch: {
    dialog(val) {
      val || this.close()
    },
    dialogDelete(val) {
      val || this.closeDelete()
    }
  },


  methods: {
    editItem(item) {
      this.editedIndex = this.schedules.indexOf(item)
      this.editedItem.id = item.id
      this.editedItem.monday = item.monday
      this.editedItem.tuesday = item.tuesday
      this.editedItem.wednesday = item.wednesday
      this.editedItem.thursday = item.thursday
      this.editedItem.friday = item.friday
      this.editedItem.saturday = item.saturday
      this.editedItem.sunday = item.sunday
      this.editedItem.user_id = item.user_id
      this.editedItem.comment = item.comment
      this.dialog = true
    },

    deleteItem(id) {
      this.editedIndex = id
      this.dialogDelete = true
    },
    async deleteItemConfirm() {
      try {
        if (this.editedIndex > -1) {
          await this.$store.dispatch('operator/deleteSchedule', {
            id: this.editedIndex,
            showroom_id: this.$route.params.id
          })
          this.$toast.success('График успешно удалён.', {
            position: 'top-right',
            timeout: 5000,
            closeOnClick: true,
            pauseOnFocusLoss: true,
            pauseOnHover: true,
            draggable: true,
            draggablePercent: 0.6,
            showCloseButtonOnHover: false,
            hideProgressBar: true,
            closeButton: 'button',
            icon: true,
            rtl: false
          })
        }
      } catch (e) {
        console.log('errors', e)
        this.$toast.error('Произошла ошибка при удаление!!!', {
          position: 'top-right',
          timeout: 5000,
          closeOnClick: true,
          pauseOnFocusLoss: true,
          pauseOnHover: true,
          draggable: true,
          draggablePercent: 0.6,
          showCloseButtonOnHover: false,
          hideProgressBar: false,
          closeButton: 'button',
          icon: true,
          rtl: false
        })
      }
      this.closeDelete()
      this.close()
    },
    closeDelete() {
      this.dialogDelete = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },
    close() {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },
    async save() {
      this.submitting = true
      const item = this.editedItem
      item.showroom_id = this.$route.params.id
      try {
        if (this.editedIndex > -1) {
          await this.$store.dispatch('operator/updateSchedule', {item})
          this.$toast.success('График успешно изменён.', {
            position: 'top-right',
            timeout: 5000,
            closeOnClick: true,
            pauseOnFocusLoss: true,
            pauseOnHover: true,
            draggable: true,
            draggablePercent: 0.6,
            showCloseButtonOnHover: false,
            hideProgressBar: true,
            closeButton: 'button',
            icon: true,
            rtl: false
          })
        } else {
          await this.$store.dispatch('operator/createSchedule', {item})
          this.$toast.success('График успешно создан.', {
            position: 'top-right',
            timeout: 5000,
            closeOnClick: true,
            pauseOnFocusLoss: true,
            pauseOnHover: true,
            draggable: true,
            draggablePercent: 0.6,
            showCloseButtonOnHover: false,
            hideProgressBar: true,
            closeButton: 'button',
            icon: true,
            rtl: false
          })
        }
      } catch (e) {
        console.log('errors', e)
        this.$toast.error('Произошла ошибка при добавление. Убедитесь что вы не дублируете оператора!!!', {
          position: 'top-right',
          timeout: 5000,
          closeOnClick: true,
          pauseOnFocusLoss: true,
          pauseOnHover: true,
          draggable: true,
          draggablePercent: 0.6,
          showCloseButtonOnHover: false,
          hideProgressBar: false,
          closeButton: 'button',
          icon: true,
          rtl: false
        })
      }
      this.submitting = false
      this.close()
    }

  }

}

</script>

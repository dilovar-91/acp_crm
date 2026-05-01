<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links" />

      <v-row no-gutters align="start" class="d-flex">
        <v-col cols="12" class="mt-1">
          <v-app-bar elevate-on-scroll dense>
            <v-toolbar-title>Черный список</v-toolbar-title>
            <v-spacer />
            <v-text-field
              v-model="search"
              label="Поиск"
              dense
              hide-details
              outlined
              clearable
              class="mx-3"
            >
            </v-text-field>
            <v-select
              v-model="showroom_id"
              :items="showrooms"
              hide-details
              item-text="name"
              item-value="id"
              label="Шоурум"
              menu-props="auto"
              outlined
              clearable
              required
              multiple
              class="mr-2"
              dense
              @click:clear="$nextTick(() => (showroom_id = []))"
            >
              <template #selection="{ item, index }">
                <template v-if="index === 0">
                  <span>{{ item.name }}</span>
                </template>
                <span v-if="index === 1" class="grey--text text-caption">
                  &nbsp;(+{{ showroom_id.length - 1 }})
                </span>
              </template>
            </v-select>
            <v-btn color="primary" class="mr-4" dark @click="dialog = true">
              <v-icon>mdi-plus</v-icon>
              Добавить
            </v-btn>
          </v-app-bar>
        </v-col>
      </v-row>
      <v-row dense>
        <v-col cols="12" md="12" sm="12">
          <v-data-table
            :key="componentKey"
            class="elevation-1 myTable grey lighten-3"
            :search="search"
            :headers="headers"
            fixed-header
            :items-per-page="lists.length"
            height="80vh"
            :hide-default-footer="true"
            dense
          >
            <template #body>
              <tbody>
                <template v-for="(item, i) in lists">
                  <tr :key="'row-' + item.id" @dblclick="editItem(item)">
                    <td>{{ i + 1 }}</td>

                    <td
                      :class="{
                        'bg-red': phoneCount[item.phone] > 1,
                      }"
                    >
                      {{ item.phone | mask('+7 ### ###-##-##') }}
                    </td>

                    <td>
                      {{ item.showroom?.name }}
                    </td>
                    <td>
                      {{ item.comment }}
                    </td>
                  </tr>
                </template>
              </tbody>
            </template>
          </v-data-table>
        </v-col>
        <v-dialog v-model="dialog" max-width="800">
          <v-card>
            <v-card-title class="headline">
              {{ formTitle }}
            </v-card-title>
            <v-card-text>
              <v-form ref="form" v-model="valid" class="pt-2">
                <v-row dense>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="editedItem.phone"
                      label="Телефон"
                      outlined
                      :rules="phoneNumberRules"
                      required
                      dense
                      @paste="handlePaste"
                    />
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-select
                      v-model="editedItem.showroom_id"
                      :items="showrooms"
                      item-text="name"
                      item-value="id"
                      menu-props="auto"
                      label="Шоурум"
                      hide-details
                      class="mb-3"
                      required
                      :rules="[(v) => !!v || 'Выберите салона']"
                      dense
                      outlined
                      clearable
                      @click:clear="
                        $nextTick(() => (editedItem.showroom_id = null))
                      "
                    />
                  </v-col>
                  <v-col cols="12" md="12">
                    <v-textarea
                      v-model="editedItem.comment"
                      label="Комментарий"
                      outlined
                      dense
                      append-icon="mdi-key"
                    />
                  </v-col>
                </v-row>
              </v-form>
            </v-card-text>
            <v-divider />
            <v-card-actions>
              <v-btn text @click="dialog = false"> Отмена </v-btn>
              <v-btn
                v-if="editedIndex !== -1"
                :disabled="role && role.name !== 'admin'"
                color="red darken-1"
                class="mr-3"
                text
                @click="confirmDelete(editedItem.id)"
              >
                Удалить
              </v-btn>
              <v-spacer />
              <v-btn color="primary" text @click="save()"> Сохранить </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="deleteDialog" max-width="290">
          <v-card>
            <v-card-title class="headline"> Вы хотите удалить? </v-card-title>

            <v-card-text>
              После удаления вы не можете восстановить эту строку.
            </v-card-text>

            <v-card-actions>
              <v-spacer />

              <v-btn color="green darken-1" text @click="deleteItem()">
                Да
              </v-btn>
              <v-btn color="green darken-1" text @click="deleteDialog = false">
                Нет
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-row>
    </v-container>
  </div>
</template>
<script>
import BreadCrumb from '~/components/BreadCrumb'

export default {
  components: { BreadCrumb },
  layout: 'crm',
  middleware: 'permission',
  data: () => ({
    dialog: false,
    editedIndex: -1,
    componentKey: 0,
    showroom_id: [],
    showCode: false,
    valid: false,
    deleteId: '',
    search: null,
    deleteDialog: false,
    headers: [
      {
        text: '№',
        value: 'number',
        sortable: false,
        fixed: true,
        width: '10px',
      },
      {
        text: 'Телефон',
        value: 'phone',
        sortable: false,
        fixed: true,
        width: '10px',
      },
      {
        text: 'Салон',
        value: 'showroom?.name',
        width: '200px',
        sortable: false,
      },
      {
        text: 'Коммент',
        sortable: false,
        width: '180px',
        value: 'comment',
        fixed: true,
      },
    ],

    phoneNumberRules: [
      (v) => !!v || 'Телефон объязательно',
      (v) => /^7\d{10}$/.test(v) || 'Введите номер в таком формате 79999999999',
    ],
    editedItem: {
      id: '',
      phone: '',
      showroom_id: '',
      comment: '',
    },
    defaultItem: {
      id: '',
      phone: '',
      showroom_id: '',
      comment: '',
    },
  }),
  async fetch({ store, params: { id } }) {
    await store.dispatch('showroom/fetchShowrooms', { id })
    await store.dispatch('showroom/fetchBlacklists', {})
  },
  computed: {
    lists() {
      const all = this.$store.state.showroom.blacklists || []

      // гарантия, что showroom_id — массив
      if (Array.isArray(this.showroom_id) && this.showroom_id.length) {
        return all.filter((item) => this.showroom_id.includes(item.showroom_id))
      }

      // безопасная проверка search (без optional chaining)
      if (this.search) {
        const normalizedSearch = String(this.search).replace(/\D/g, '')
        return all.filter(
          (item) => String(item.phone).replace(/\D/g, '') === normalizedSearch
        )
      }

      return all
    },

    phoneCount() {
      const counts = {}
      this.lists.forEach((item) => {
        counts[item.phone] = (counts[item.phone] || 0) + 1
      })
      return counts
    },

    showrooms() {
      return this.$store.state.showroom.showrooms
    },
    role() {
      return this.$store.state.auth.role
    },
    links() {
      return [
        {
          text: 'Главная',
          disabled: false,
          href: '/',
        },
        {
          text: 'Настройки',
          disabled: false,
          href: '/admin',
        },
        {
          text: 'Черный список',
          disabled: true,
          href: '/admin/blacklist',
        },
      ]
    },
    formTitle() {
      return this.editedIndex === -1 ? 'Добавление' : 'Изменение'
    },
  },
  watch: {
    dialog(val) {
      val || this.close()
    },
  },
  methods: {
    async save() {
      await this.validate()

      if (!this.valid) {
        this.$toast.error('Заполните обязательные поля!')
        return
      }

      const action =
        this.editedIndex > -1
          ? 'showroom/updateBlacklist'
          : 'showroom/saveBlacklist'

      this.$store
        .dispatch(action, { item: this.editedItem })
        .then((res) => {
          this.$toast.success(res.message)
          this.close()
        })
        .catch((err) => {
          this.$toast.error(err.message)
        })
    },
    editItem(item) {
      this.editedIndex = item.id
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },

    deleteItem(id) {
      this.$store
        .dispatch('showroom/deleteBlacklist', {
          id: this.deleteId,
        })
        .then(() => {
          this.$toast.error('Номер удалён')
        })
      this.deleteId = ''
      this.dialog = false
      this.deleteDialog = false
    },

    confirmDelete(id) {
      this.deleteId = id
      this.deleteDialog = true
    },

    validate() {
      this.$refs.form.validate()
    },

    close() {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1

        this.componentKey += 1
        this.$refs.form.resetValidation()
        this.valid = false
      })
    },

    handlePaste(event) {
      // Get the pasted value
      const pastedValue = event.clipboardData.getData('text/plain')

      // Remove the characters "+", "(", ")", space from the pasted value
      const cleanedValue = pastedValue?.replace(/\D/g, '')

      // Replace "8" with "7" if the number begins with "8"
      const modifiedValue = cleanedValue.startsWith('8')
        ? `7${cleanedValue.slice(1)}`
        : cleanedValue

      // Assign the modified value to the v-model
      setTimeout(() => {
        this.editedItem.phone = modifiedValue
      }, 20)
    },
  },
}
</script>

<style scoped>
table {
  border: none;
  border-collapse: collapse;
}

table td {
  border: 1px solid rgb(204, 200, 200);
}

table tfoot tr th {
  border: 1px solid rgb(204, 200, 200);
  padding: 0 12px !important;
}

.myTable > table > tfoot > tr > th {
  border: 1px solid rgb(204, 200, 200);
  padding: 0 12px !important;
}

pre {
  background: #333;
  white-space: pre;
  word-wrap: break-word;
  overflow: auto;
}

pre.code {
  margin: 5px 2px;
  border-radius: 4px;
  border: 1px solid #292929;
  position: relative;
}

pre.code label {
  font-family: sans-serif;
  font-weight: bold;
  font-size: 13px;
  color: #ddd;
  position: absolute;
  left: 1px;
  top: 15px;
  text-align: center;
  width: 60px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  pointer-events: none;
}

pre.code code {
  font-family: 'Inconsolata', 'Monaco', 'Consolas', 'Andale Mono',
    'Bitstream Vera Sans Mono', 'Courier New', Courier, monospace;
  display: block;
  margin: 0 0 0 60px;
  padding: 5px 6px 4px;
  border-left: 1px solid #555;
  overflow-x: auto;
  font-size: 13px;
  line-height: 15px;
  color: #ddd;
}

pre.css-code code {
  color: #ff4242;
}

pre.html-code code {
  color: #00ca02;
}

pre.javascript-code code {
  color: #ff8000;
}

pre.jquery-code code {
  color: #1da1f2;
}

@media (max-width: 750px) {
  pre::after {
    content: '';
  }
}

.bg-red {
  background-color: red !important;
  color: #ececec;
}
</style>

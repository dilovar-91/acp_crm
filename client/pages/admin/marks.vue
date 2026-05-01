<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links"/>

      <v-row no-gutters align="start" class="d-flex">
        <v-col cols="12" class="mt-1">
          <v-app-bar elevate-on-scroll dense>
            <v-toolbar-title>Список марок машин</v-toolbar-title>
            <v-spacer/>
            <v-text-field label="Поиск"
                          dense
                          hide-details
                          outlined
                          clearable
                          v-model="search"
                          class="mx-3"
            >

            </v-text-field>

            <v-btn
              color="primary"
              dark
              @click="dialog = true"
            >
              <v-icon>mdi-plus</v-icon>
              Добавить марку
            </v-btn>
          </v-app-bar>
        </v-col>
      </v-row>
      <v-row dense>
        <v-col cols="12" md="12" sm="12">
          <v-data-table
            :key="componentKey"
            class="elevation-1 myTable  grey lighten-3"
            :search="search"
            :headers="headers"
            fixed-header
            :items-per-page="marks.length"
            height="80vh"
            :hide-default-footer="true"
            dense
          >
            <template v-slot:body>
              <tbody>
              <template v-for="(item, i) in marks">
                <tr
                  :key="'row-' + item.id"
                  @dblclick="editItem(item)"
                >
                  <td>{{ i + 1 }}</td>
                  <td>{{ item.id }}</td>
                  <td>
                    {{ item.name }}
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
                      v-model="editedItem.id"
                      label="ID"
                      outlined
                      readonly
                      dense
                    />
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="editedItem.name"
                      label="Название"
                      outlined
                      :rules="[(v) => !!v || 'Введите марку']"
                      required
                      dense
                    />
                  </v-col>

                </v-row>
              </v-form>
            </v-card-text>
            <v-divider/>
            <v-card-actions>
              <v-btn text @click="dialog = false">
                Отмена
              </v-btn>
              <v-btn v-if="this.editedIndex !== -1"
                     :disabled="role && role.name !== 'admin'"
                     color="red darken-1"
                     class="mr-3"
                     text
                     @click="confirmDelete(editedItem.id)"
              >
                Удалить
              </v-btn>
              <v-spacer/>
              <v-btn
                color="primary"
                text
                @click="save()"
              >
                Сохранить
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="deleteDialog" max-width="290">
          <v-card>
            <v-card-title class="headline">
              Вы хотите удалить?
            </v-card-title>

            <v-card-text>
              После удаления вы не можете восстановить эту строку.
            </v-card-text>

            <v-card-actions>
              <v-spacer/>

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
import DateTimePicker from '@/components/DateTimePicker.vue'

export default {
  layout: 'crm',
  middleware: 'permission',
  components: {BreadCrumb, DateTimePicker},
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
        width: '10px'
      },
      {
        text: 'ID',
        value: 'id',
        sortable: false,
        fixed: true,
        width: '10px'
      },
      {
        text: 'Марка',
        align: 'start',
        sortable: false,
        value: 'date',
        fixed: true,
        width: '30px'
      }
    ],
    editedItem: {
      id: '',
      name: ''
    },
    defaultItem: {
      id: '',
      name: ''
    },
  }),
  async fetch({store}) {
    await store.dispatch('user/toggle', false)
    await store.dispatch('admin/fetchMarks', {})
  },
  computed: {
    marks() {
      if (this.search) {
        return this.$store.state.admin.marks.filter(mark => mark.name.toLowerCase().includes(this.search.toLowerCase()))
      }
      else return this.$store.state.admin.marks
    },

    role() {
      return this.$store.state.auth.role
    },
    links() {
      return [
        {
          text: 'Главная',
          disabled: false,
          href: '/'
        },
        {
          text: 'Настройки',
          disabled: false,
          href: '/admin'
        },
        {
          text: 'Список марок машин',
          disabled: true,
          href: '/admin/marks'
        }
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
      if (this.valid) {

        if (this.editedIndex > -1) {
          this.$store
            .dispatch('admin/updateMark', {
              item: this.editedItem
            }).then((res) => {
            this.$toast.success("Марка изменена!", {
              position: "top-right",
              timeout: 2000,
              closeOnClick: true,
              pauseOnFocusLoss: true,
              pauseOnHover: true,
              draggable: true,
              draggablePercent: 0.6,
              showCloseButtonOnHover: false,
              hideProgressBar: true,
              closeButton: "button",
              icon: true,
              rtl: false
            });
          });

        } else {
          this.$store
            .dispatch('admin/saveMark', {
              item: this.editedItem
            }).then((res) => {
            this.$toast.success("Марка добавлена!", {
              position: "top-right",
              timeout: 2000,
              closeOnClick: true,
              pauseOnFocusLoss: true,
              pauseOnHover: true,
              draggable: true,
              draggablePercent: 0.6,
              showCloseButtonOnHover: false,
              hideProgressBar: true,
              closeButton: "button",
              icon: true,
              rtl: false
            });
          });
        }
        this.close()
      } else {
        this.$toast.error("Заполните обязательные поля!", {
          position: "top-right",
          timeout: 2000,
          closeOnClick: true,
          pauseOnFocusLoss: true,
          pauseOnHover: true,
          draggable: true,
          draggablePercent: 0.6,
          showCloseButtonOnHover: false,
          hideProgressBar: true,
          closeButton: "button",
          icon: true,
          rtl: false
        });
      }
    },
    editItem(item) {
      this.editedIndex = item.id
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },

    deleteItem() {
      this.$store
        .dispatch('admin/deleteMark', {
          id: this.deleteId
        })
        .then(() => {
          this.$toast.success("Марка удалена", {
            position: "top-right",
            timeout: 2000,
            closeOnClick: true,
            pauseOnFocusLoss: true,
            pauseOnHover: true,
            draggable: true,
            draggablePercent: 0.6,
            showCloseButtonOnHover: false,
            hideProgressBar: true,
            closeButton: "button",
            icon: true,
            rtl: false
          });
        })
      this.deleteId = ''
      this.dialog = false
      this.deleteDialog = false
    },

    confirmDelete(id) {
      this.deleteId = id
      this.deleteDialog = true
      console.log(id)
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
  }
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
  font-family: "Inconsolata", "Monaco", "Consolas", "Andale Mono", "Bitstream Vera Sans Mono", "Courier New", Courier, monospace;
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
</style>

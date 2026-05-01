<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links"/>

      <v-row no-gutters align="start" class="d-flex">
        <v-col cols="12" class="mt-1">
          <v-app-bar elevate-on-scroll dense>
            <v-toolbar-title>Актуальность проектов</v-toolbar-title>
            <v-spacer/>

            <v-select
              v-model="filter_showroom"
              :items="showrooms"
              hide-details
              item-text="name"
              item-value="id"
              label="Шоурум"
              menu-props="auto"
              outlined
              clearable
              required
              class="mr-2"
              dense
              @click:clear="
                          $nextTick(() => (filter_showroom = null))
                        "
            >




            </v-select>
            <v-select
              v-model="filter_manager"
              :items="managers"
              hide-details
              item-text="name"
              item-value="id"
              label="Менеджер"
              menu-props="auto"
              outlined
              clearable
              required
              class="mr-2"
              dense
              @click:clear="
                          $nextTick(() => (filter_manager = null))
                        "
            >




            </v-select>
            <v-select
              v-model="filter_site"
              :items="sites"
              hide-details
              item-text="title"
              item-value="id"
              label="Сайт"
              menu-props="auto"
              outlined
              clearable
              required
              class="mr-2"
              dense
              @click:clear="
                          $nextTick(() => (filter_site = null))
                        "
            >
            </v-select>
            <v-select
              v-model="filter_type"
              :items="types"
              hide-details
              item-text="name"
              item-value="id"
              label="Тип"
              menu-props="auto"
              outlined
              clearable
              required
              class="mr-2"
              dense
              @click:clear="
                          $nextTick(() => (filter_type = null))
                        "
            >
            </v-select>
            <v-select
              v-model="filter_system"
              :items="systems"
              hide-details
              item-text="name"
              item-value="id"
              label="Система"
              menu-props="auto"
              outlined
              clearable
              required
              class="mr-2"
              dense
              @click:clear="
                          $nextTick(() => (filter_system = null))
                        "
            >
            </v-select>
            <v-select
              v-model="filter_status"
              :items="statuses"
              hide-details
              item-text="name"
              item-value="id"
              label="Статус"
              menu-props="auto"
              outlined
              clearable
              required
              class="mr-2"
              dense
              @click:clear="
                          $nextTick(() => (filter_status = null))
                        "
            >
            </v-select>

            <v-btn color="success" dark class="mb-2 mr-2 mt-1" @click="doFilter()">
              Применить

            </v-btn>


              <v-btn color="error" dark class="mb-2 mt-1" @click="clearFilter()">
                Сбросить
              </v-btn>
              <v-spacer />
            <v-btn
              color="primary"
              dark
              @click="dialog = true"
            >
              <v-icon>mdi-plus</v-icon>
              Добавить проект
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
            :items-per-page="projects.length"
            height="80vh"
            :hide-default-footer="true"
            dense
          >
            <template v-slot:body>
              <tbody>
              <template v-for="(item, i) in projects">
                <tr
                  :key="'row-' + item.id"
                  @dblclick="editItem(item)"
                >
                  <td>{{ i + 1 }}</td>
                  <td>
                    {{ item.showroom?.name }}
                  </td>
                  <td>
                    {{ item.site?.title }}
                  </td>
                  <td>
                    {{ item.manager?.name }}
                  </td>
                  <td>
                    {{ item.type?.name }}
                  </td>
                  <td>
                    {{ item.system?.name }}
                  </td>
                  <td>
                    {{ item.status?.name }}
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
                    <v-autocomplete
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
                      @click:clear="$nextTick(() => editedItem.showroom_id=null)"
                    />
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-autocomplete
                      v-model="editedItem.site_id"
                      :items="filtered_sites"
                      item-text="title"
                      item-value="id"
                      menu-props="auto"
                      label="Сайт"
                      hide-details
                      class="mb-3"
                      required
                      :rules="[(v) => !!v || 'Выберите сайта']"
                      dense
                      outlined
                      clearable
                      @click:clear="$nextTick(() => editedItem.showroom_id=null)"
                    />
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-select
                      v-model="editedItem.manager_id"
                      :items="managers"
                      item-text="name"
                      item-value="id"
                      menu-props="auto"
                      label="Менеджер"
                      hide-details
                      class="mb-3"
                      dense
                      outlined
                      clearable
                      @click:clear="$nextTick(() => editedItem.manager_id=null)"
                    />
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-select
                      v-model="editedItem.type_id"
                      :items="types"
                      item-text="name"
                      item-value="id"
                      menu-props="auto"
                      label="Тип сайта"
                      hide-details
                      class="mb-3"
                      dense
                      outlined
                      clearable
                      @click:clear="$nextTick(() => editedItem.type_id=null)"
                    />
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-select
                      v-model="editedItem.system_id"
                      :items="systems"
                      item-text="name"
                      item-value="id"
                      menu-props="auto"
                      label="Система"
                      hide-details
                      class="mb-3"
                      required
                      :rules="[(v) => !!v || 'Выберите систему']"
                      dense
                      outlined
                      clearable
                      @click:clear="$nextTick(() => editedItem.showroom_id=null)"
                    />
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-select
                      v-model="editedItem.status_id"
                      :items="statuses"
                      item-text="name"
                      item-value="id"
                      menu-props="auto"
                      label="Статус"
                      hide-details
                      class="mb-3"
                      dense
                      outlined
                      clearable
                      @click:clear="$nextTick(() => editedItem.status_id=null)"
                    />
                  </v-col>
                  <v-col cols="12" md="12">
                    <v-textarea v-model="editedItem.comment" rows="3" outlined
                                dense label="Комментарии"
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
    filter_showroom: null,
    filter_manager: null,
    filter_site: null,
    filter_type: null,
    filter_system: null,
    filter_status: null,
    showCode: false,
    valid: false,
    deleteId: '',
    search: null,
    deleteDialog: false,
    site_types: [
      {
        id: 1,
        value: 'Мультибренд',
      },
      {
        id: 2,
        value: 'С пробегом',
      },
      {
        id: 3,
        value: 'Квизы',
      },
      {
        id: 4,
        value: 'Моно',
      },
      {
        id: 5,
        value: 'БАН',
      },
      {
        id: 6,
        value: 'Остановлено',
      },
      {
        id: 7,
        value: 'Отключено',
      }
      ],
    headers: [
      {
        text: '№',
        value: 'number',
        sortable: false,
        fixed: true,
        width: '10px'
      },
      {
        text: 'Шоурум',
        value: 'id',
        sortable: false,
        fixed: true,
        width: '10px'
      },
      {
        text: 'Сайт',
        align: 'start',
        sortable: false,
        value: 'date',
        fixed: true,
        width: '100px'
      },
      {
        text: 'Менеджер',
        align: 'start',
        sortable: false,
        value: 'manager',
        fixed: true,
        width: '150px'
      },
      {text: 'Тип сайта ', value: 'type?.name', width: '200px', sortable: false},
      {text: 'Система', value: 'system?.name', width: '200px', sortable: false},
      {text: 'Статус', value: 'status?.name', width: '200px', sortable: false},
      {
        text: 'Комментарии',
        sortable: false,
        width: '180px',
        value: 'active',
        fixed: true
      },
    ],
    editedItem: {
      id: '',
      showroom_id: '',
      site_id: '',
      manager_id: '',
      type_id: '',
      system_id: '',
      style_id: '',
      comment: '',
    },
    defaultItem: {
      id: '',
      showroom_id: '',
      site_id: '',
      manager_id: '',
      type_id: '',
      system_id: '',
      style_id: '',
      comment: '',
    },
  }),
  computed: {
    sites() {
        return this.$store.state.showroom.admin_sites
    },
    filtered_sites() {
      if (this.editedItem.showroom_id !== null) {
        return this.$store.state.showroom.admin_sites.filter(site => site.showroom_id === this.editedItem.showroom_id)
      } else return []

    },
    projects() {
      return this.$store.state.site.actual_projects
    },
    statuses() {
      return this.$store.state.site.project_statuses
    },
    managers() {
      return this.$store.state.site.project_managers
    },
    systems() {
      return this.$store.state.site.project_systems
    },
    types() {
      return this.$store.state.site.project_types
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
          href: '/'
        },
        {
          text: 'Настройки',
          disabled: false,
          href: '/admin'
        },
        {
          text: 'Актуальность проектов',
          disabled: true,
          href: '/reports/projects'
        }
      ]
    },
    formTitle() {
      return this.editedIndex === -1 ? 'Добавление' : 'Изменение'
    },
  },
  async fetch({store, params: {id},},) {
    await store.dispatch('showroom/fetchShowrooms', {id})
    await store.dispatch('site/fetchActualProjects', {})
    await store.dispatch('site/fetchStatuses')
    await store.dispatch('showroom/fetchAdminSites', {})
    await store.dispatch('site/fetchTypes')
    await store.dispatch('site/fetchManagers')
    await store.dispatch('site/fetchSystems')
  },
  watch: {
    dialog(val) {
      val || this.close()
    },
  },
  methods: {
    redirect(url) {
      window.open(url, '_blank')
    },
    onChangeId(val) {
      console.log(val)
    },

    handlePaste(event) {
      // Get the pasted value
      const pastedValue = event.clipboardData.getData('text/plain');


      // Remove the characters "+", "(", ")", space from the pasted value
      const cleanedValue = pastedValue?.replace(/\D/g, '');

      // Replace "8" with "7" if the number begins with "8"
      const modifiedValue = cleanedValue.startsWith('8') ? `7${cleanedValue.slice(1)}` : cleanedValue;

      // Assign the modified value to the v-model
      setTimeout(() => {
        this.editedItem.phone = modifiedValue;
      }, 20);
    },

    async save() {
      await this.validate()
      if (this.valid) {

        if (this.editedIndex > -1) {
          this.$store
            .dispatch('site/saveProject', {
              item: this.editedItem
            }).then((res) => {
            this.$toast.success("Проект изменён!", {
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
            .dispatch('site/saveProject', {
              item: this.editedItem
            }).then((res) => {
            this.$toast.success("Проект изменён!", {
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

    deleteItem(id) {
      this.$store
        .dispatch('showroom/deleteSite', {
          id: this.deleteId
        })
        .then(() => {
          this.$toast.success("Сайт удалён", {
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
    },

    validate() {
      this.$refs.form.validate()
    },

    doFilter(){
      this.$store.dispatch('site/fetchActualProjects', {
        showroom: this.filter_showroom,
        site: this.filter_site,
        manager: this.filter_manager,
        type: this.filter_type,
        status: this.filter_status,
        system: this.filter_system
      })
    },
    clearFilter() {
      this.filter_showroom = null
      this.filter_site = null
      this.filter_status = null
      this.filter_type = null
      this.filter_system = null
      this.filter_manager = null
      this.$store.dispatch('site/fetchActualProjects', {})
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

<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links" />

      <v-row no-gutters align="start" class="d-flex">
        <v-col cols="12" class="mt-1">
          <v-app-bar elevate-on-scroll dense>
            <v-toolbar-title>Список сайтов</v-toolbar-title>
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
              v-model="agency_id"
              :items="agencies"
              hide-details
              item-text="name"
              item-value="id"
              label="Агентство"
              menu-props="auto"
              outlined
              clearable
              required
              multiple
              class="mr-2"
              dense
            >
            </v-select>
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
            <v-btn color="error" dark class="mr-4" icon @click="clearFilter()">
              <v-icon>mdi-close</v-icon>
            </v-btn>

            <v-btn color="primary" dark @click="dialog = true">
              <v-icon>mdi-plus</v-icon>
              Добавить сайт
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
            :items-per-page="sites.length"
            height="80vh"
            :hide-default-footer="true"
            dense
          >
            <template #body>
              <tbody>
                <template v-for="(item, i) in sites">
                  <tr :key="'row-' + item.id" @dblclick="editItem(item)">
                    <td class="flex">
                      {{ i + 1 }}

                      <v-tooltip bottom>
                        <template #activator="{ on, attrs }">
                          <v-btn
                            v-if="$auth?.user?.id == 1"
                            dark
                            class="ml-3"
                            v-bind="attrs"
                            icon
                            small
                            color="warning"
                            v-on="on"
                            @click="duplicateSite(item.id)"
                          >
                            <v-icon>mdi-content-copy</v-icon>
                          </v-btn>
                        </template>
                        <span>Дублировать сайт</span>
                      </v-tooltip>

                      <v-tooltip bottom>
                        <template #activator="{ on, attrs }">
                          <v-btn
                            dark
                            class="ml-3"
                            v-bind="attrs"
                            icon
                            small
                            color="primary"
                            v-on="on"
                            @click="copySiteInfo(item)"
                          >
                            <v-icon>mdi-content-copy</v-icon>
                          </v-btn>
                        </template>
                        <span>Скопировать сайт</span>
                      </v-tooltip>
                    </td>
                    <td>{{ item.id }}</td>
                    <td>
                      {{ item.title }}
                    </td>

                    <td :class="{ 'bg-red': phoneCount[item.phone] > 1 }">
                      {{ item.phone | mask('+7 ### ###-##-##') }}
                    </td>

                    <td>
                      {{ item.agency?.name }}
                    </td>
                    <td>
                      {{ item.showroom?.name }}
                    </td>
                    <td class="overflow-link">
                      <a
                        :href="
                          item.url && item.url.startsWith('http')
                            ? item.url
                            : 'https://' + (item.url || '')
                        "
                        target="_blank"
                        rel="noreferrer"
                      >
                        {{ item.url }}
                      </a>
                    </td>

                    <td>
                      {{ item.mango_site_id }}
                    </td>

                    <td>
                      {{ item.mango_token }}
                    </td>

                    <td>
                      {{ item.sip }}
                    </td>

                    <td>
                      <v-icon :color="item.actual === 1 ? 'success' : 'error'">
                        {{ item.actual === 1 ? 'mdi-check' : 'mdi-close' }}
                      </v-icon>
                    </td>

                    <td>
                      <v-icon :color="item.active === 1 ? 'success' : 'error'">
                        {{ item.active === 1 ? 'mdi-check' : 'mdi-close' }}
                      </v-icon>
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
              <v-switch
                v-model="editedItem.active"
                class="text-right ml-12"
                label="Активность"
              ></v-switch>
              <v-switch
                v-model="editedItem.actual"
                class="text-right ml-12"
                label="Актуальность"
              ></v-switch>
            </v-card-title>
            <v-card-text>
              <v-form ref="form" v-model="valid" class="pt-2">
                <v-row dense>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-if="editedIndex > -1"
                      v-model="editedItem.id"
                      label="ID"
                      outlined
                      readonly
                      dense
                    />
                  </v-col>
                  <v-col :cols="editedIndex <= -1 ? 12 : 6">
                    <v-text-field
                      v-model="editedItem.title"
                      label="Название"
                      outlined
                      :rules="[(v) => !!v || 'Введите название сайта']"
                      required
                      dense
                    />
                  </v-col>
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
                    <v-text-field
                      v-model="editedItem.url"
                      label="Ссылка"
                      outlined
                      dense
                      append-icon="mdi-link"
                      @click:append="redirect(editedItem.url)"
                    />
                  </v-col>
                  <v-col
                    cols="12"
                    :md="editedItem.showroom_id === 9 ? '4' : '6'"
                  >
                    <v-text-field
                      v-model="editedItem.post_url"
                      label="Post Url"
                      outlined
                      dense
                      append-icon="mdi-link"
                      @click:append="redirect(editedItem.post_url)"
                    />
                  </v-col>
                  <v-col
                    cols="12"
                    :md="editedItem.showroom_id === 9 ? '4' : '6'"
                  >
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

                  <v-col v-if="editedItem.showroom_id === 9" cols="12" md="4">
                    <v-select
                      v-model="editedItem.to_showroom"
                      :items="showrooms"
                      item-text="name"
                      item-value="id"
                      menu-props="auto"
                      label="Относиться к салону"
                      hide-details
                      class="mb-3"
                      required
                      :rules="[(v) => !!v || 'Выберите салона']"
                      dense
                      outlined
                      clearable
                      @click:clear="
                        $nextTick(() => (editedItem.to_showroom = null))
                      "
                    />
                  </v-col>
                  <v-col cols="12" md="12">
                    <v-text-field
                      v-model="editedItem.token"
                      label="Токен"
                      outlined
                      dense
                      append-icon="mdi-key"
                    />
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-select
                      v-model="editedItem.agency_id"
                      :items="agencies"
                      item-text="name"
                      item-value="id"
                      menu-props="auto"
                      label="Агенство"
                      hide-details
                      class="mb-3"
                      dense
                      outlined
                      clearable
                      @click:clear="
                        $nextTick(() => (editedItem.agency_id = null))
                      "
                    />
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-select
                      v-model="editedItem.type_id"
                      :items="site_types"
                      item-text="name"
                      item-value="id"
                      menu-props="auto"
                      label="Тип сайта"
                      hide-details
                      class="mb-3"
                      dense
                      outlined
                      clearable
                      @click:clear="
                        $nextTick(() => (editedItem.type_id = null))
                      "
                    />
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="editedItem.calltouch_site_id"
                      rows="1"
                      outlined
                      dense
                      label="Calltouch Site ID"
                      clearable
                    />
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="editedItem.calltouch_access_key"
                      rows="1"
                      outlined
                      dense
                      label="Calltouch API KEY"
                      clearable
                    />
                  </v-col>
                  <v-col cols="12" md="12">
                    <v-text-field
                      v-model="editedItem.sip"
                      rows="1"
                      outlined
                      dense
                      label="SIP номер сайта"
                      clearable
                    />
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="editedItem.mango_site_id"
                      rows="1"
                      outlined
                      dense
                      label="Mango Site ID"
                      clearable
                    />
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="editedItem.mango_token"
                      rows="1"
                      outlined
                      dense
                      label="Mango Token"
                      clearable
                    />
                  </v-col>
                  <v-col cols="12" md="12">
                    <v-textarea
                      v-model="editedItem.description"
                      rows="2"
                      outlined
                      dense
                      label="Комментарии"
                    />
                  </v-col>
                  <v-col cols="12" md="12">
                    <v-text-field
                      v-model="editedItem.autospot_id"
                      outlined
                      dense
                      label="autospot_id (Autospot ext_id) "
                    />
                  </v-col>
                  <v-col cols="12" md="12">
                    <pre class="code css-code"><code ref="codeBlock">
                  {{ editedItem.phone }}
                  {{ editedItem.url }}
                  id: {{ editedItem.id }}
                  showroom_id: {{ editedItem.showroom_id }}
                    </code><button type="button" style="font-size: 16px; position: absolute; top: 5px; right: 5px; color: white;" @click="copyCode()">
                        {{ copied ? '✅ Скопирован!' : '📋 Скопироать' }}
                      </button></pre>
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
    agency_id: [],
    showCode: false,
    copied: false,
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
        text: 'ID',
        value: 'id',
        sortable: false,
        fixed: true,
        width: '10px',
      },
      {
        text: 'Сайт',
        align: 'start',
        sortable: false,
        value: 'date',
        fixed: true,
        width: '30px',
      },
      {
        text: 'Телефон',
        align: 'start',
        sortable: false,
        value: 'phone',
        fixed: true,
        width: '20px',
      },

      {
        text: 'Агенство',
        value: 'agency?.name',
        width: '20px',
        sortable: false,
      },
      {
        text: 'Салон',
        value: 'showroom?.name',
        width: '20px',
        sortable: false,
      },
      {
        text: 'Ссылка',
        sortable: false,
        width: '35px',
        value: 'link',
        fixed: true,
      },
      {
        text: 'Mango site key',
        sortable: false,
        width: '20px',
        value: 'mango_site_key',
        fixed: true,
      },
      {
        text: 'Mango token',
        sortable: false,
        width: '20px',
        value: 'mango_token',
        fixed: true,
      },
      {
        text: 'SIP номер',
        sortable: false,
        width: '20px',
        value: 'sip',
        fixed: true,
      },
      {
        text: 'Актуально',
        sortable: false,
        width: '20px',
        value: 'actual',
        fixed: true,
      },
      {
        text: 'Активно',
        sortable: false,
        width: '20px',
        value: 'active',
        fixed: true,
      },
    ],
    editedItem: {
      id: '',
      title: '',
      url: '',
      description: '',
      showroom_id: '',
      to_showroom: '',
      phone: '',
      agency_id: '',
      post_url: '',
      token: '',
      type_id: '',
      autospot_id: '',
      calltouch_site_id: '',
      calltouch_access_key: '',
      mango_site_id: '',
      mango_token: '',
      sip: '',
      actual: '',
    },
    defaultItem: {
      id: '',
      title: '',
      url: '',
      description: '',
      showroom_id: '',
      to_showroom: '',
      phone: '',
      agency_id: '',
      post_url: '',
      token: '',
      type_id: '',
      autospot_id: '',
      calltouch_site_id: '',
      calltouch_access_key: '',
      mango_site_id: '',
      mango_token: '',
      sip: '',
      actual: '',
    },
    phoneNumberRules: [
      (v) => !!v || 'Телефон объязательно',
      (v) => /^7\d{10}$/.test(v) || 'Введите номер в таком формате 79999999999',
    ],
  }),
  async fetch({ store, params: { id } }) {
    await store.dispatch('user/toggle', false)
    await store.dispatch('showroom/fetchShowrooms', { id })
    await store.dispatch('showroom/fetchAdminSites', {})
    await store.dispatch('showroom/fetchSiteTypes')
    await store.dispatch('showroom/fetchAgencies')
  },
  computed: {
    sites() {
      let sites = this.$store.state.showroom.admin_sites

      if (this.showroom_id.length) {
        sites = sites.filter((site) =>
          this.showroom_id.includes(site.showroom_id)
        )
      }

      if (this.agency_id?.length) {
        sites = sites.filter((site) => this.agency_id.includes(site.agency_id))
      }

      if (this.search) {
        const cleanedSearch = this.search.replace(/\D/g, '')
        sites = sites.filter((site) => site.phone === cleanedSearch)
      }

      return sites
    },

    phoneCount() {
      const counts = {}
      this.sites.forEach((item) => {
        counts[item.phone] = (counts[item.phone] || 0) + 1
      })
      return counts
    },
    agencies() {
      return this.$store.state.showroom.agencies
    },
    site_types() {
      return this.$store.state.showroom.site_types
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
          text: 'Список сайтов',
          disabled: true,
          href: '/admin/sites',
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
    redirect(url) {
      window.open(url, '_blank')
    },
    clearFilter() {
      this.agency_id = []
      this.showroom_id = []
      this.search = null
    },

    handlePaste(event) {
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

    formatPhoneNumber(phone) {
      const number = phone.toString().replace(/\D/g, '')

      if (number.length === 11 && number.startsWith('7')) {
        const code = number.slice(1, 4)
        const part1 = number.slice(4, 7)
        const part2 = number.slice(7, 9)
        const part3 = number.slice(9, 11)

        return `7 (${code}) ${part1}-${part2}-${part3}`
      }

      return phone
    },

    copyCode() {
      const codeText = this.$refs.codeBlock.innerText.trim()
      navigator.clipboard
        .writeText(codeText)
        .then(() => {
          this.copied = true
          setTimeout(() => {
            this.copied = false
          }, 3000)
        })
        .catch((err) => {
          console.error('Ошибка при копировании:', err)
        })
    },
    copySiteInfo(item) {
      const codeText = `${this.formatPhoneNumber(item.phone)}
      ${item.url}
      site_id: ${item.id}
      showroom_id: ${item.showroom_id}`
      navigator.clipboard
        .writeText(codeText)
        .then(() => {
          this.$toast.success('Скопирован!', {
            position: 'top-right',
            timeout: 2000,
            closeOnClick: true,
            pauseOnFocusLoss: true,
            pauseOnHover: true,
            draggable: true,
            draggablePercent: 0.6,
            showCloseButtonOnHover: false,
            hideProgressBar: true,
            closeButton: 'button',
            icon: true,
            rtl: false,
          })
        })
        .catch((err) => {
          console.error('Ошибка при копировании:', err)
        })
    },

    async save() {
      await this.validate()
      if (this.valid) {
        if (this.editedIndex > -1) {
          this.$store
            .dispatch('showroom/updateSite', {
              item: this.editedItem,
            })
            .then((res) => {
              this.$toast.success('Сайт изменён!', {
                position: 'top-right',
                timeout: 2000,
                closeOnClick: true,
                pauseOnFocusLoss: true,
                pauseOnHover: true,
                draggable: true,
                draggablePercent: 0.6,
                showCloseButtonOnHover: false,
                hideProgressBar: true,
                closeButton: 'button',
                icon: true,
                rtl: false,
              })
            })
        } else {
          this.$store
            .dispatch('showroom/saveSite', {
              item: this.editedItem,
            })
            .then((res) => {
              this.$toast.success('Сайт добавлен!', {
                position: 'top-right',
                timeout: 2000,
                closeOnClick: true,
                pauseOnFocusLoss: true,
                pauseOnHover: true,
                draggable: true,
                draggablePercent: 0.6,
                showCloseButtonOnHover: false,
                hideProgressBar: true,
                closeButton: 'button',
                icon: true,
                rtl: false,
              })
            })
        }
        this.close()
      } else {
        this.$toast.error('Заполните обязательные поля!', {
          position: 'top-right',
          timeout: 2000,
          closeOnClick: true,
          pauseOnFocusLoss: true,
          pauseOnHover: true,
          draggable: true,
          draggablePercent: 0.6,
          showCloseButtonOnHover: false,
          hideProgressBar: true,
          closeButton: 'button',
          icon: true,
          rtl: false,
        })
      }
    },

    async duplicateSite(id) {
      await this.$store
        .dispatch('showroom/duplicateSite', {
          id,
        })
        .then((res) => {
          this.$toast.success('Сайт дублирован!', {
            position: 'top-right',
            timeout: 2000,
            closeOnClick: true,
            pauseOnFocusLoss: true,
            pauseOnHover: true,
            draggable: true,
            draggablePercent: 0.6,
            showCloseButtonOnHover: false,
            hideProgressBar: true,
            closeButton: 'button',
            icon: true,
            rtl: false,
          })
        })
    },
    editItem(item) {
      this.editedIndex = item.id
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },

    deleteItem(id) {
      this.$store
        .dispatch('showroom/deleteAdminSite', {
          id: this.deleteId,
        })
        .then(() => {
          this.$toast.success('Сайт удалён', {
            position: 'top-right',
            timeout: 2000,
            closeOnClick: true,
            pauseOnFocusLoss: true,
            pauseOnHover: true,
            draggable: true,
            draggablePercent: 0.6,
            showCloseButtonOnHover: false,
            hideProgressBar: true,
            closeButton: 'button',
            icon: true,
            rtl: false,
          })
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
  },
}
</script>

<style scoped>
table {
  border: none;
  border-collapse: collapse;
}

.bg-red {
  background-color: red !important;
  color: #ececec;
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
  padding: 0px;
}

pre.code {
  margin: 5px 2px;
  border-radius: 4px;
  border: 1px solid #292929;
  position: relative;
}

pre.code code {
  font-family: 'Inconsolata', 'Monaco', 'Consolas', 'Andale Mono',
    'Bitstream Vera Sans Mono', 'Courier New', Courier, monospace;
  display: block;
  margin: 0 0 0 10px;
  padding: 5px 6px 4px;
  border-left: 1px solid #555;
  overflow-x: auto;
  font-size: 13px;
  line-height: 12px;
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

.overflow-link {
  word-break: break-word;
  overflow-wrap: break-word;
}

copy-button {
  position: absolute;
  top: 5px;
  right: 125px;
  background: #f2f2f2;
  border: 1px solid #ccc;
  padding: 3px 8px;
  font-size: 22px;
  border-radius: 4px;
  cursor: pointer;
}
.copy-button:hover {
  background-color: #e6e6e6;
}
</style>

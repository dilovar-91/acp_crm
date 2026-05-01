<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links"/>

      <v-row no-gutters align="start" class="d-flex">
        <v-col cols="12" class="mt-1">
          <v-data-table
            dense
            :headers="headers"
            :items="operators"
            :items-per-page="15"
            item-key="id"
            class="elevation-1"
          >
            <template #body="{ items }">
              <tbody>
              <tr
                v-for="(item) in items"
                :key="item.id"

                @dblclick="editItem(item)"
              >
                <td class="text-center">
                  {{ item.first_name + ' ' + item.last_name }}
                </td>
                <td class="text-center">
                  {{ item.showroom && item.showroom.name }}
                </td>
                <td class="text-center">
                  <v-icon color="warning" class="mr-2" @click="editItem(item)">
                    mdi-pencil
                  </v-icon>
                  <v-icon color="error" @click="deleteItem(item)">
                    mdi-delete
                  </v-icon>
                </td>
              </tr>
              </tbody>
            </template>

            <template #top>
              <v-toolbar flat dense>
                <v-toolbar-title>
                  Список операторов (Всего
                  {{ operators.length || 0 }})
                </v-toolbar-title>
                <v-spacer/>
                <v-dialog v-model="dialogPasswordReset" max-width="900px">
                  <template #activator="{ on, attrs }">
                    <v-btn color="success" dark class="mb-2 mr-2" v-bind="attrs" v-on="on">
                      Сбросить пароли пользователей
                    </v-btn>
                  </template>

                  <v-card>
                    <v-card-title>
                      <span class="headline">Сброс паролей операторов</span>
                    </v-card-title>

                    <v-card-text>
                      <v-container>
                        <v-form ref="formResetPassword" v-model="validPassReset" lazy-validation>
                          <v-row>
                            <v-col cols="12" md="6">
                              <v-text-field
                                v-model.trim="operatorPassword"
                                label="Пароль старшего"
                                dense
                                hide-details
                                outlined
                                placeholder="Например call125002"
                                :rules="[rules.required, rules.login]"
                              />
                            </v-col>

                            <v-col cols="12" md="6">
                              <v-text-field
                                v-model="operatorPasswordRepeat"
                                dense
                                hide-details
                                outlined
                                label="Подтверждение пароля"
                                :append-icon="'mdi-refresh'"
                                @click:append="generateAndAssignPassword('operator')"
                                :rules="[rules.required]"
                              />
                              <div class="text-caption mt-1">
                                Пароль главного оператора и зама.
                              </div>
                            </v-col>

                            <v-col cols="12" md="6">
                              <v-text-field
                                v-model.trim="mainPassword"
                                label="Пароль оператора"
                                dense
                                hide-details
                                outlined
                                placeholder="Например call125002"
                                :rules="[rules.required, rules.login]"
                              />
                            </v-col>

                            <v-col cols="12" md="6">
                              <v-text-field
                                v-model="mainPasswordRepeat"
                                label="Подтверждение пароля"
                                dense
                                hide-details
                                outlined
                                :append-icon="'mdi-refresh'"
                                @click:append="generateAndAssignPassword('main')"
                                :rules="[rules.required]"
                              />
                              <div class="text-caption mt-1">
                                Пароль операторов.
                              </div>
                            </v-col>

                            <v-col cols="12" class="d-flex flex-wrap" style="gap: 8px;">
                              <v-btn color="success" @click="generateAll">
                                Сгенерировать пароли
                              </v-btn>

                              <v-spacer/>

                              <v-btn color="blue darken-1" text @click="closePassDialog">Отмена</v-btn>
                              <v-btn
                                color="blue darken-1"
                                text
                                :disabled="!validPassReset || saving"
                                @click="savePasswords()"
                              >
                                {{ saving ? "Сохранение..." : "Сбросить" }}
                              </v-btn>

                              <v-btn
                                color="blue darken-1"
                                text
                                :disabled="!validPassReset || saving"
                                @click="savePasswords(true)"
                              >
                                {{ saving ? "Сохранение..." : "Сбросить и скачать" }}
                              </v-btn>
                            </v-col>

                            <v-col cols="12" v-if="operatorsSorted.length">


                              <v-simple-table dense>
                                <thead>
                                <tr>
                                  <th>Роль</th>
                                  <th>Логин</th>
                                  <th>Новый пароль</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="row in operatorsSorted" :key="'userid_' + row.id">
                                  <td>
                                    <v-chip
                                      small
                                      :color="row.role_id === 3 ? 'red' : row.role_id === 6 ? 'orange' : 'blue'"
                                      dark
                                    >
                                      {{ getRoleName(row.role_id) }}
                                    </v-chip>
                                  </td>

                                  <td>{{ row.email }}</td>

                                  <td style="font-family: monospace;">
                                    {{ (row.role_id === 3 || row.role_id === 6) ? mainPassword : operatorPassword }}
                                  </td>
                                </tr>
                                </tbody>
                              </v-simple-table>


                            </v-col>

                          </v-row>
                        </v-form>
                      </v-container>
                    </v-card-text>
                  </v-card>
                </v-dialog>
                <v-dialog v-model="dialog" max-width="900px">
                  <template #activator="{ on, attrs }">
                    <v-btn
                      color="primary"
                      dark
                      class="mb-2"
                      v-bind="attrs"
                      v-on="on"
                    >
                      Добавить новую
                    </v-btn>
                  </template>
                  <v-card>
                    <v-card-title>
                      <span class="headline">{{ formTitle }}</span>
                    </v-card-title>
                    <v-card-text>
                      <v-container>
                        <v-form ref="form" v-model="valid">
                          <v-row>
                            <v-col cols="12" sm="12" md="12">
                              <v-text-field
                                v-model="editedItem.first_name"
                                label="Имя"
                              />
                            </v-col>

                            <v-col cols="12" sm="12" md="12">
                              <v-text-field
                                v-model="editedItem.last_name"
                                label="Фамилия"
                              />
                            </v-col>

                            <v-col cols="12" sm="12" md="12">
                              <v-text-field
                                v-model="editedItem.email"
                                label="Логин"
                              />
                            </v-col>

                            <v-col cols="12" sm="12" md="12">
                              <v-select
                                v-model="editedItem.role_id"
                                :items="filteredRoles"
                                item-text="title"
                                item-value="id"
                                menu-props="auto"
                                label="Тип пользователя"
                                hide-details
                                single-line
                                :rules="[(v) => !!v || 'Выберите тип пользователя']"
                                required
                              />
                            </v-col>

                            <v-col cols="12" md="6">
                              <v-text-field
                                v-model.trim="editedItem.password"
                                label="Пароль старшего"
                                dense
                                hide-details
                                outlined
                                placeholder="Например call125002"
                              />
                            </v-col>

                            <v-col cols="12" md="6">
                              <v-text-field
                                v-model="repeatPassword"
                                label="Подтверждение пароля"
                                dense
                                hide-details
                                outlined
                                :append-icon="'mdi-refresh'"
                                @click:append="generateAndAssignPassword('single')"
                              />
                            </v-col>
                          </v-row>
                        </v-form>
                      </v-container>
                    </v-card-text>

                    <v-card-actions>
                      <v-spacer/>
                      <v-btn color="blue darken-1" text @click="close">
                        Отмена
                      </v-btn>
                      <v-btn color="blue darken-1" text @click="save()">
                        Сохранить
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-dialog>
              </v-toolbar>
            </template>
          </v-data-table>
          <v-snackbar
            v-model="snackbar"
            :color="color"
            :right="'right'"
            :timeout="timeout"
            :top="'top'"
            outlined
          >
            {{ text }}
            <template #action="{ attrs }">
              <v-btn icon color="deep-orange" @click="snackbar = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </template>
          </v-snackbar>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>
<script>
import * as XLSX from "xlsx";
import BreadCrumb from '~/components/BreadCrumb'

export default {
  components: {BreadCrumb},
  layout: 'user',
  middleware: 'permission',
  data: () => ({
    dialog: false,
    dialogPasswordReset: false,
    valid: false,
    validPassReset: false,
    snackbar: false,
    text: '',
    color: '',
    timeout: 6000,
    expanded: [],
    singleExpand: false,
    headers: [
      {
        text: 'Пользователь',
        value: 'name',
        align: 'center',
        sortable: true,
        width: 100
      },
      {
        text: 'Салон',
        value: 'showroom.title',
        align: 'center',
        sortable: true,
        width: 100
      },
      {
        text: 'Действия',
        value: 'actions',
        align: 'center',
        sortable: false,
        width: 100
      }
    ],
    editedIndex: -1,
    editedItem: {
      id: '',
      name: '',
      showroom_id: ''
    },
    defaultItem: {
      id: '',
      name: '',
      showroom_id: ''
    },
    saving: false,
    operatorPassword: "",
    operatorPasswordRepeat: "",
    mainPassword: "",
    repeatPassword: "",
    mainPasswordRepeat: "",

    selectedOperatorIds: [],

    // хранение plain-паролей для операторов (НЕ хеши)
    opPassMap: new Map(),

    rules: {
      required: (v) => (!!v && String(v).trim().length > 0) || "Обязательное поле",
      login: (v) => /^[a-zA-Z0-9_]+$/.test(String(v || "")) || "Только латиница/цифры/_",
    },
  }),

  async fetch({store, error, params: {id}}) {
    await store.dispatch('operator/fetchOperatorRoles')
    await store.dispatch('showroom/fetchShowroom', { id })
    await store.dispatch('showroom/fetchOperators', {showroom_id: id, orderWorkers: false})
    await store.dispatch('user/fetchUsers')
  },

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? 'Добавить оператора' : 'Изменить'
    },

    filteredRoles() {
      return this.roles.filter(l => [2, 3, 6].includes(l.id))
    },
    roles() {
      return this.$store.state.operator.roles
    },
    operators() {
      return this.$store.state.showroom.operators
    },

    operatorsSorted() {
      const roleOrder = {3: 1, 6: 2, 2: 3};

      return [...this.operators].sort((a, b) => {
        return (roleOrder[a.role_id] || 99) - (roleOrder[b.role_id] || 99);
      });
    },

    showroom() {
      return this.$store.state.showroom.showroom
    },
    role() {
      return this.$store.state.auth.role
    },
    links() {
      return [
        {
          text: 'Главная',
          disabled: false,
          href: '/admin'
        },
        {
          text: 'Операторы',
          disabled: false,
          href: '/admin/operators'
        },
        {
          text: this.showroom?.name,
          disabled: true,
          href: '/admin/operators/' + this.showroom?.id
        }
      ]
    },

    operatorItems() {
      const ops = Array.isArray(this.operators) ? this.operators : [];
      console.log(ops)
      return ops.map((o) => ({
        id: o.id, // главное чтобы было уникально
        login: o.email ?? o.email ?? o.email,
        text: `${o.first_name + ' ' + o.last_name}`,
      }));
    },

    selectedOperators() {
      const byId = new Map(this.operatorItems.map((x) => [x.id, x]));
      return this.selectedOperatorIds
        .map((id) => byId.get(id))
        .filter(Boolean);
    },
  },
  watch: {
    dialog(val) {
      val || this.close()
    }
  },
  methods: {
    save() {
      if (this.valid) {
        if (this.editedIndex > -1) {
          this.editedItem.showroom_id = this.$route.params?.id
          this.$store.dispatch('showroom/saveOperator', {
            item: this.editedItem
          })
          this.$notify('success', 'Запись изменен')
        } else {
          this.$store.dispatch('showroom/saveOperator', {
            item: this.editedItem
          })
          this.$notify('success', 'Запись добавлен')
        }
        this.close()
      } else {
        this.$notify('error', 'Заполните обязательные поля')
      }
    },

    editItem(item) {
      this.editedIndex = this.operators.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },

    deleteItem(item) {
      confirm('Вы уверены, что хотите удалить этого пользователя?') &&
      this.$store
        .dispatch('showroom/deleteOperator', {
          id: item.id
        })
        .then(() => {
          this.$notify('success', 'Оператор удалён')
        })
      this.deleteId = ''
      this.deleteDialog = false
    },
    close() {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },


    getRoleName(roleId) {
      switch (roleId) {
        case 2:
          return "Оператор";
        case 3:
          return "Главный";
        case 6:
          return "Заместитель";
        default:
          return "Неизвестно";
      }
    },

    validate() {
      this.$refs.form.validate()
    },

    closePassDialog() {
      this.dialogPasswordReset = false;
      this.resetForm();
    },

    resetForm() {
      this.validPassReset = true;
      this.saving = false;
      this.operatorPassword = "";
      this.operatorPasswordRepeat = "";
      this.mainPassword = "";
      this.mainPasswordRepeat = "";
      this.opPassMap = new Map();
      if (this.$refs.formResetPassword) this.$refs.formResetPassword.resetValidation();
    },

    generateDigits(length = 6) {
      const arr = new Uint32Array(length);
      window.crypto.getRandomValues(arr);

      let out = "";
      for (let i = 0; i < length; i++) {
        out += (arr[i] % 10).toString();
      }
      return out;
    },


    async savePasswords(download = false) {
      const ok = this.$refs.formResetPassword?.validate?.();

      if (!ok) {

        this.$toast.error('Заполните обязательные поля!!!');
        return;
      }

      // на всякий случай, если не нажали "Сгенерировать"
      if (this.mainPassword !== this.mainPasswordRepeat) {
        this.$toast.error('Пароль главного оператора не совпадает с подтверждением!!!');

        return

      }

      if (this.operatorPassword !== this.operatorPasswordRepeat) {
        this.$toast.error('Пароли операторов не совпадают с подтверждением!!!');

        return

      }


      this.saving = true;

      try {
        // payload содержит PLAIN пароли (строки)
        const payload = {
          showroom_id: this.$route.params?.id,
          main_password: this.mainPassword,
          operator_password: this.operatorPassword,
        };

        // 1) Вызов твоего API (backend сам пусть хеширует при записи)
        // await this.$axios.post("/users/reset-passwords", payload);

        // 2) Excel скачиваем сразу после успешного API (или пока просто сразу)

        const response = await this.$axios.post("/users/reset-passwords", payload);
        const { admins_logins = [], operators_logins = [] } = response?.data || {};





        if (download) {
          this.downloadExcel({ admins_logins, operators_logins });
        }

        this.$toast.success('Пароли успешно сброшены');

        this.closePassDialog();
      } catch (e) {
        // eslint-disable-next-line no-console
        console.error(e);
        this.$toast.error('Ошибка при сохранении.!!!');
      } finally {
        this.saving = false;
      }
    },

    generateAll() {

      // один пароль для главных
      const mainPassword = `call${this.generateDigits(8)}`;

      // один пароль для обычных операторов
      const operatorPassword = `op${this.generateDigits(8)}`;


      this.mainPassword = mainPassword
      this.mainPasswordRepeat = mainPassword
      this.operatorPassword = operatorPassword
      this.operatorPasswordRepeat = operatorPassword
    },


    generatePassword(roleId) {
      if (roleId === 3 || roleId === 6) return `call${this.generateDigits(8)}`;
      return `op${this.generateDigits(8)}`;
    },


    generateAndAssignPassword(type = 'single') {


      if (type === 'single') {
        const newPass = this.generatePassword(this.editedItem.role_id);
        this.repeatPassword = newPass;
        this.editedItem.password = newPass;
      } else if (type === 'main') {
        const newPass = this.generatePassword(3);
        this.mainPassword = newPass;
        this.mainPasswordRepeat = newPass;
      } else if (type === 'operator') {
        const newPass = this.generatePassword(2);
        this.operatorPassword = newPass;
        this.operatorPasswordRepeat = newPass;
      }

    },

    downloadExcel({ admins_logins = [], operators_logins = [] } = {}) {

      const roleMap = { 2: "Оператор", 3: "Главный", 6: "Заместитель" };
      const rows = this.operatorsSorted.map((op) => {
        const isMain = [3, 6].includes(op.role_id);

        return {
          Роль: roleMap[op.role_id] || op.role_id,
          Логин: op.email || "",
          Фамилия: op.last_name || "",
          Имя: op.first_name || "",
          Пароль: isMain ? this.mainPassword : this.operatorPassword,
        };
      });

      // 👉 Добавляем блок "Доступы в планшетку"
      rows.push({});
      rows.push({ Роль: "Доступы в планшетку" });
      rows.push({});

      admins_logins.forEach((login) => {
        rows.push({
          Логин: login,
          Пароль: this.mainPassword,
        });
      });

      operators_logins.forEach((login) => {
        rows.push({
          Логин: login,
          Пароль: this.operatorPassword,
        });
      });

      const ws = XLSX.utils.json_to_sheet(rows);
      ws["!cols"] = [
        { wch: 18 },
        { wch: 28 },
        { wch: 18 },
        { wch: 18 },
        { wch: 18 },
      ];

      const wb = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(wb, ws, "Доступы");

      const filename = `Доступы_${this.showroom?.name}_${new Date()
        .toISOString()
        .slice(0, 10)}.xlsx`;

      XLSX.writeFile(wb, filename);
    }
  }
}
</script>

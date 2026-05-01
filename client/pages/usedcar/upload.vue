<template>
  <div>
    <BreadCrumb :items="links" />

    <v-container fluid>
      <v-row no-gutters align="start" class="d-flex">
        <v-col cols="12" class="mt-1">
          <v-card class="mx-auto">
            <v-app-bar class="grey lighten-5" elevate-on-scroll dense>
              <v-toolbar-title>Загрузка фото</v-toolbar-title>
              <v-spacer />
            </v-app-bar>
            <v-card-text>
              <v-container>
                <v-form ref="form" v-model="valid" lazy-validation>
                  <v-row>
                    <v-col cols="12" sm="6" md="3">
                      <v-select
                        v-model="form.mark_id"
                        :items="marks"
                        item-text="name"
                        item-value="id"
                        menu-props="auto"
                        label="Марка"
                        hide-details
                        :rules="[(v) => !!v || 'Выберите марку']"
                        required
                        @change="getModels(form.mark_id)"
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-select
                        v-model="form.model_id"
                        :items="models"
                        item-text="name"
                        item-value="id"
                        menu-props="auto"
                        label="Модель"
                        hide-details
                        :rules="[(v) => !!v || 'Выберите модель']"
                        required
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-select
                        v-model="form.showroom_id"
                        :items="[
                          { id: 1, name: 'Склад' },
                          { id: 2, name: 'КомфортАвто' },
                          { id: 3, name: 'Урус' },
                          { id: 4, name: 'АвтоПремиум' },
                          { id: 5, name: 'Авангард Юг' },
                          { id: 7, name: 'Форсаж' },
                        ]"
                        item-text="name"
                        item-value="id"
                        menu-props="auto"
                        label="Шоурум"
                        hide-details
                        disabled
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <v-select
                        v-model="form.year"
                        :items="$nuxt.$range(2023, 1960)"
                        menu-props="auto"
                        label="Год выпуска"
                        :rules="[(v) => !!v || 'Выберите год выпуска']"
                        hide-details
                        required
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="2">
                      <v-select
                        v-model="form.bodytype_id"
                        :items="bodytypes"
                        item-text="name"
                        item-value="id"
                        menu-props="auto"
                        label="Кузов"
                        :rules="[(v) => !!v || 'Выберите тип кузова']"
                        hide-details
                        required
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="2">
                      <v-select
                        v-model="form.wheeltype_id"
                        :items="wheeltypes"
                        item-text="name"
                        item-value="id"
                        menu-props="auto"
                        label="Привод"
                        hide-details
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="2">
                      <v-select
                        v-model="form.transmission_id"
                        :items="transmissions"
                        item-text="name"
                        item-value="id"
                        menu-props="auto"
                        label="КПП"
                        hide-details
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="2">
                      <v-select
                        v-model="form.volume"
                        :items="$nuxt.$range(0.8, 5, 0.1)"
                        menu-props="auto"
                        label="Объем"
                        hide-details
                      />
                    </v-col>
                    <v-col cols="12" sm="6" md="2">
                      <v-select
                        v-model="form.enginetype_id"
                        :items="enginetypes"
                        item-text="name"
                        item-value="id"
                        menu-props="auto"
                        label="Двигатель"
                        hide-details
                      />
                    </v-col>

                    <v-col cols="12" sm="6" md="2">
                      <v-text-field
                        v-model="form.color"
                        label="Цвет"
                        hide-details
                      />
                    </v-col>

                    <v-col cols="12" sm="6" md="3">
                      <v-text-field
                        v-model="form.price"
                        label="Продажа"
                        hide-details
                      />
                    </v-col>

                    <v-col cols="12" sm="6" md="3">
                      <v-select
                        v-model="form.status_id"
                        :items="statuses"
                        item-text="status"
                        item-value="id"
                        menu-props="auto"
                        label="Статус"
                        hide-details
                      />
                    </v-col>

                    <v-col cols="12" sm="6" md="6">
                      <v-text-field v-model="form.comment" label="Коментарии" />
                    </v-col>
                    <v-col cols="12" md="12">
                      <vue-dropzone
                        id="dropzone"
                        ref="myVueDropzone"
                        accepted-file-types=".jpg,.png,.jpeg"
                        :options="dropzoneOptions"
                        :destroy-dropzone="false"
                        @vdropzone-removed-file="vFileRemoved"
                        @vdropzone-success="vFileAdded"
                      />
                    </v-col>
                  </v-row>

                  <v-card-actions>
                    <v-spacer />
                    <v-switch
                      v-model="form.expose"
                      label="Выставить"
                      color="success"
                      hide-details
                      class="mb-5 mr-2"
                    />
                    <v-btn
                      color="error darken-1"
                      :disabled="form.id === ''"
                      text
                      @click="deleteItem(form.id)"
                    >
                      {{
                        form.deleted_at === null
                          ? 'Удалить'
                          : 'Удалить из корзины'
                      }}
                    </v-btn>
                    <v-btn
                      color="blue darken-1"
                      :disabled="!canChange()"
                      text
                      @click="save()"
                    >
                      Сохранить
                    </v-btn>
                  </v-card-actions>
                </v-form>
              </v-container>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
    <v-snackbar
      v-model="snackbar"
      :color="color"
      :right="'right'"
      :timeout="timeout"
      :top="'top'"
      outlined
    >
      {{ text }}
      <template v-slot:action="{ attrs }">
        <v-btn icon color="deep-orange" @click="snackbar = false">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </template>
    </v-snackbar>
  </div>
</template>
<script>
import axios from 'axios'
import vue2Dropzone from 'vue2-dropzone'
import BreadCrumb from '~/components/BreadCrumb'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'

export default {
  layout: 'user',
  name: 'Prism',
  middleware: ['auth', 'isAdmin'],
  components: { BreadCrumb, vueDropzone: vue2Dropzone },
  data: () => ({
    valid: true,
    dropzoneOptions: {
      url: '/api/usedcar/upload',
      thumbnailWidth: 300,
      maxFilesize: 3,
      // parallelUploads: 1,
      maxFiles: 8,
      headers: {},
      addRemoveLinks: true,
      dictDefaultMessage:
        "<i class='v-icon mdi mdi-camera theme--light text-h3'></i> Выберите фото",
      dictMaxFilesExceeded: 'Вы не можете загружать более 8 файлов',
      dictRemoveFile: 'Удалить',
      uploadMultiple: false,
      removeType: 'server',
      destroyDropzone: false,
    },
    menu1: false,
    menu2: false,
    search_page: '',
    search_number: '',
    // snackbar
    color: '',
    snackbar: false,
    text: '',
    timeout: 6000,
    x: null,
    y: 'bottom',
    pictures: [],
    form: {
      id: '',
      showroom_id: null,
      income_date: '',
      mark_id: '',
      model_id: '',
      year: '',
      auto_ru: '',
      bodytype_id: '',
      transmission_id: '',
      enginetype_id: '',
      salon: '',
      wheeltype_id: '',
      owner_count: 1,
      power: '',
      color: '',
      milage: '',
      volume: '',
      vin_number: '',
      number: '',
      sts: '',
      ptc: 1,
      income_price: '',
      price: 0,
      key_number: '',
      manager: '',
      status_id: '',
      registered: '',
      transit: '',
      pictures: [],
      expose: null,
      comment: null,
      deleted_at: null,
    },
    editorOption: {
      placeholder: 'Текст новости',
      theme: 'snow',
    },
  }),

  computed: {
    user() {
      return this.$store.state.auth.user
    },

    role() {
      return this.$store.state.auth.role
    },
    links() {
      return [
        {
          text: this.role.title,
          disabled: false,
          href: '/' + this.role?.name,
        },
        {
          text: 'Автомобилы БУ',
          disabled: false,
          href: '/' + this.role?.name + '/used-cars',
        },
        {
          text: 'Автомобилы БУ (C фотками)',
          disabled: false,
          href: '/' + this.role?.name + '/used-cars/edit',
        },
        {
          text: 'Загрузка фото',
          disabled: true,
          href: '/' + this.role?.name + '/upload',
        },
      ]
    },

    statuses() {
      return this.$store.state.usedcar.statuses
    },
    marks() {
      return this.$store.state.car.marks
    },
    models() {
      return this.$store.state.car.models
    },
    enginetypes() {
      return this.$store.state.car.enginetypes
    },
    bodytypes() {
      return this.$store.state.car.bodytypes
    },
    transmissions() {
      return this.$store.state.car.transmissions
    },
    wheeltypes() {
      return this.$store.state.car.wheeltypes
    },
  },
  async asyncData({ params, error }) {
    const { data } = await axios.get('/usedcars/' + params.id)
    if (Object.keys(data).length === 0) {
      error({
        statusCode: 404,
        message: 'Авто с этим идентификатором не найдены',
      })
    } else {
      return { item: data }
    }
  },
  async fetch({ store }) {
    await store.dispatch('car/fetchMarks')
    await store.dispatch('car/fetchBodyTypes')
    await store.dispatch('car/fetchEngineTypes')
    await store.dispatch('car/fetchTransmissions')
    await store.dispatch('car/fetchWheelTypes')
    await store.dispatch('usedcar/fetchCars', {})
    await store.dispatch('usedcar/fetchCoefficient')
    await store.dispatch('usedcar/fetchStatuses')
    await store.dispatch('user/toggle', false)
  },
  created() {
    this.form.id = this.item.id
    this.form.showroom_id = this.item.showroom_id
    this.form.income_date = this.item.income_date
    this.form.mark_id = this.item.mark_id
    this.form.model_id = this.item.model_id
    this.form.pictures = this.item.pictures
    this.form.year = this.item.year
    this.form.bodytype_id = this.item.bodytype_id
    this.form.transmission_id = this.item.transmission_id
    this.form.wheeltype_id = this.item.wheeltype_id
    this.form.enginetype_id = this.item.enginetype_id
    this.form.power = this.item.power
    this.form.color = this.item.color
    this.form.milage = this.item.milage
    this.form.volume = this.item.volume
    this.form.price = this.item.price
    this.form.number = this.item.number
    this.form.status_id = this.item.status_id
    this.form.expose = this.item.expose
    this.form.comment = this.item.comment
  },
  mounted() {
    for (let i = 0; i < this.item.pictures.length; i++) {
      const file = { size: 123, name: this.item.pictures[i], type: 'image/png' }
      const url = '/images/usedcar/' + this.item.pictures[i]
      this.$refs.myVueDropzone.manuallyAddFile(file, url)
    }
  },

  methods: {
    save() {
      if (this.valid) {
        this.form.pictures = [...this.pictures]
        this.form.pictures2 = [...this.pictures2]
        this.form.videos = [...this.pictures2]
        this.$store.dispatch('usedcar/save', { item: this.form })
        this.showSnack('success', 'Запись изменён')
      } else {
        this.showSnack('error', 'Заполните обязательные поля')
      }
    },

    canChange() {
      const users = [
        'admin',
        'director',
        'coordinator',
        'coordinator2',
        'manager_acp',
        'petr',
        'manager_light',
      ]
      return users.includes(this.role?.name)
    },
    parseDate(date) {
      if (!date) {
        return null
      }
      return this.moment(date).format('YY-MM-dd')
    },
    isNumber(evt) {
      evt = evt || window.event
      const charCode = evt.which ? evt.which : evt.keyCode
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        evt.preventDefault()
      } else {
        return true
      }
    },

    showSnack(type, text) {
      this.text = text
      this.color = type
      this.snackbar = true
    },
    validate() {
      this.$refs.form.validate()
    },
    reset() {
      this.$refs.form.reset()
      this.form.content = ''
      this.form.pictures = []
      this.$refs.myVueDropzone.removeAllFiles()
    },

    resetValidation() {
      this.$refs.form.resetValidation()
    },
    getModels(markId = null) {
      this.editedItem.model_id = []
      if (markId !== 0) {
        this.$store.dispatch('car/fetchModels', { markId })
      }
    },
    vFileAdded(file, response) {
      file.upload.filename = response.file
      this.pictures.push(response.file)
    },
    async vFileRemoved(file) {
      // console.log(file)
      const filename =
        file.manuallyAdded === true ? file.name : file.upload.filename
      const index = this.pictures.indexOf(filename)

      if (index > -1) {
        this.pictures.splice(index, 1)
      }
      await axios
        .post('/usedcar/deletePhoto/', filename)
        .then((response) => {
          this.showSnack('success', 'Фото удалён')
        })
        .catch((error) => {
          this.showSnack('error', error)
        })
    },
  },
}
</script>
<style scoped>
#dropzone {
  height: 350px !important;
}
</style>

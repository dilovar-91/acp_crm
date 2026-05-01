<template>
  <div>

    <v-container fluid>
      <v-row no-gutters align="start" class="d-flex">
        <v-col cols="12" class="mt-1">
          <v-card class="mx-auto">
            <v-app-bar class="grey lighten-5" elevate-on-scroll dense>
              <v-toolbar-title>Заявка проезда</v-toolbar-title>
              <v-spacer/>
            </v-app-bar>
            <v-card-text class="pa-0">
              <v-form ref="form" v-model="valid" lazy-validation>
                <v-container fluid>
                  <v-row>
                    <v-col cols="12" sm="12" md="12" class="px-6">
                      <v-text-field
                        v-model="form.client_name"
                        hide-details
                        outlined
                        placeholder="Введие ваш ФИО"
                        label="ФИО"
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="12" class="px-6">
                      <v-select
                        v-model="form.type"
                        :items="[
                          {
                           id: 1,
                           type: 'Авиа'
                          },
                          {
                           id: 2,
                           type: 'Ж/Д'
                          },
                          {
                           id: 3,
                           type: 'Авто'
                          },
                          {
                           id: 4,
                           type: 'Автобус'
                          },
                        ]"
                        item-text="type"
                        no-data-text="Нету данных"
                        item-value="id"
                        menu-props="auto"
                        label="Транспорт"
                        hide-details
                        outlined
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="12" class="px-6">
                      <v-text-field
                        v-model="form.price"
                        label="Стоимость проезда"
                        hide-details
                        outlined
                      />
                    </v-col>
                    <v-col cols="12" sm="12" md="12" class="px-6">
                      <v-text-field
                        v-model="form.region"
                        label="Регион"
                        hide-details
                        outlined
                      />
                    </v-col>
                  </v-row>
                  <v-col cols="12" md="12">
                    <input type="file" multiple ref="file" name="testrest" accept="image/*" class="input-file">
                  </v-col>
                </v-container>
              </v-form>
            </v-card-text>
            <v-card-actions class="px-6 pt-0 justify-end">
              <v-btn
                :disabled="!valid"
                :loading="loading"
                color="success"
                @click="save()"
              >
                Отправить
              </v-btn>

              <v-btn
                color="error"
                @click="reset()"
              >
                Сбросить
              </v-btn>
            </v-card-actions>
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
<style scoped>
#dropzone {
  z-index: 4;
}
</style>
<script>

import vue2Dropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'

export default {
  layout: 'simple',
  name: 'CreateFare',
  components: {vueDropzone: vue2Dropzone},
  data: () => ({
    loading: false,
    form: {
      client_name: null,
      phone: null,
      type: null,
      showroom_id: null,
      api_token: null,
      region: null,
      pictures: []
    },
    hasImage: false,
    image: null,
    images: [],
    valid: true,
    search_page: '',
    search_number: '',
    // snackbar
    color: '',
    snackbar: false,
    text: '',
    timeout: 6000,
    x: null,
    y: 'bottom',
    dropzoneStatus: false,
    dropzoneOptions: {
      url: 'http://localhost:8000/api/fares/upload',
      thumbnailWidth: 300,
      maxFilesize: 20,
      maxFiles: 12,
      headers: {},
      addRemoveLinks: true,
      dictDefaultMessage: "<i class='v-icon mdi mdi-camera theme--light text-h3'></i> Фотографии",
      dictRemoveFile: 'Удалить',
      dictMaxFilesExceeded: 'Вы не можете загружать более 12 файлов',
      uploadMultiple: false
    }
  }),

  computed: {
    links() {
      return [

        {
          text: 'Завка на проезд',
          disabled: false,
          href: '/fares'
        },
        {
          text: 'Создание',
          disabled: true,
          href: '/fares'
        }
      ]
    }
  },

  async fetch({store}) {
  },
  methods: {
    async save() {
      this.validate()


      if (this.valid && !this.empty(this.form.pictures)) {
        var formData = new FormData();
        for (var i = 0; i < this.$refs.file.files.length; i++ ){
          let file = this.$refs.file.files[i];
          formData.append('images[' + i + ']', file);
        }
        //formData.append("images", this.images);
        formData.append("showroom_id",  1);
        formData.append("type",  this.form.type);
        formData.append("client_name",  this.form.client_name);
        formData.append("region",  this.form.region);
        formData.append("api_token",  'voN1a4IGROdjYiaiorkPNvt45g1vBNezzJDJYz7nq8RzXwMOlUf5d3XESUsT');
        formData.append("phone",  this.form.phone);
        await this.$axios.post('fares/create', formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        }).then((res) => {
            this.$notify('success', 'Заяка добавлёна')
            //this.$toast.error('Заполните обязательные поля' + error);
            //this.$router.push('/fares/' + this.$route.params.id)
          }
        ).catch((error) => {
          this.$toast.error('Произошла ошибка: ' + error);
        })
        this.loading = false
      } else {
        this.$toast.error('Заполните обязательные поля' + error);
      }
    },

    validate() {
      this.$refs.form.validate()
    },
    empty(obj) {
      const exists = Object.keys(obj).some(function (k) {
        return obj[k] === null
      })
      return exists
    },
    reset() {
      this.$refs.form.reset()
      this.form.client_name = null
      this.form.price = null
      this.pictures = []
      this.$refs.myVueDropzone.removeAllFiles()
    },
    resetValidation() {
      this.$refs.form.resetValidation()
    },
    vFileAdded(file, response) {
      file.upload.filename = response.file
      this.form.pictures.push(response.file)
    },
    vFileRemoved(file) {
      const index = this.form.pictures.indexOf(file.upload.filename)
      if (index > -1) {
        this.form.pictures.splice(index, 1)
      }
    },
    maxReached() {
      this.dropzoneStatus = true
    }
  }
}
</script>

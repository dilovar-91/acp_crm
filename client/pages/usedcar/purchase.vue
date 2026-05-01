<template>
  <div>
    <v-container fluid>
      <BreadCrumb :items="links"/>
      <v-row align="start" class="d-flex" no-gutters>
        <v-col cols="12">
          <v-data-table
            :key="componentKey"
            :headers="headers"
            :items="filtered"
            :items-per-page="usedcars.length"
            :options="options"
            class="elevation-1  grey lighten-4"
            fixed-headerF
            height="80vh"
            hide-default-footer
            item-key="id"
          >
            <template v-slot:body="{ items }">
              <tbody>
              <tr v-for="item in items"
                  :key="item.name" :class="row_classes(item)"
                  @dblclick="editItem(item)"
              >
                <td :style="oldStyle(item.income_date)">
                  <template v-if="item.income_date !== null">
                    {{ moment(new Date(item.income_date)).format('DD.MM.Y') }}
                  </template>
                </td>
                <td>
                  {{ item.mark && item.mark.name }}
                </td>
                <td >
                  {{ item.model && item.model.name }}
                </td>
                <td>{{ item.year }}</td>
                <td :style="(item.is_ready ? 'background-color:#4CAF50 !important; color: white;': (item.milage>100000 ? 'background-color:red; color: white;': ''))">
                  {{ item.milage }}
                </td>
                <td>{{ item.vin_number }}</td>
                <td>
                  {{ total_item_price(item)  | formatCurrency }}
                </td>

                <td>
                  <template v-if="item.pictures && item.pictures.length > 0"><v-icon color="success" @click="showImage(item.pictures)" large>mdi-image</v-icon></template>
                </td>
                <td>
                  <template v-if="item.pictures2 && item.pictures2.length > 0"><v-icon color="success" @click="showImage(item.pictures2)" large>mdi-image</v-icon></template>
                </td>
                <td>
                  <template v-if="item.videos && item.videos.length > 0"><v-icon color="success" @click="showVideo(item.videos)" large>mdi-play</v-icon></template>
                </td>
                <td>
                  <v-chip
                    v-if="item.status && item.status.status !== null"
                    :color="item.status.color"
                    :text-color="item.status.text_color"
                    x-small
                  >
                    {{ item.status.status }}
                  </v-chip>
                </td>
              </tr>
              </tbody>
            </template>

            <template v-slot:top>
              <v-toolbar dense>
                <v-row class="mt-8 hidden-sm-and-down">
                  <v-col cols="3" md="2" xs="6">
                    <v-select
                      v-model="filter.mark_id"
                      :items="marks"
                      dense
                      item-text="name"
                      item-value="id"
                      label="Марка"
                      outlined
                      @change="getModels(filter.mark_id)"
                      @click="filter.model_id=null"
                      @click:clear="$nextTick(() => filter.mark_id=null)"
                    />
                  </v-col>
                  <v-col cols="3" md="2" xs="6">
                    <v-select
                      v-model="filter.model_id"
                      :items="models"
                      dense
                      item-text="name"
                      item-value="id"
                      label="Модель"
                      outlined
                      @click="deleted = false"
                      @click:clear="$nextTick(() => filter.model_id=null)"
                    >
                      <template v-slot:no-data>
                        <span class="small">Выберите марку</span>
                      </template>
                    </v-select>
                  </v-col>
                  <v-col cols="3" md="2" xs="6">
                    <v-select
                      v-model="filter.transmission_id"
                      :items="transmissions"
                      dense
                      item-text="name"
                      item-value="id"
                      label="Коробка"
                      outlined
                      @click="deleted = false"
                      @click:clear="$nextTick(() => filter.transmission_id=null)"
                    />
                  </v-col>
                  <v-col cols="3" md="2" xs="6">
                    <v-select
                      v-model="filter.wheeltype_id"
                      :items="wheeltypes"
                      dense
                      item-text="name"
                      item-value="id"
                      label="Привод"
                      outlined
                      @click="deleted = false"
                      @click:clear="$nextTick(() => filter.wheeltype_id=null)"
                    />
                  </v-col>
                  <v-col cols="3" md="2" xs="4">
                    <v-select
                      v-model="filter.year_from"
                      :items="range(2023, 1960)"
                      dense
                      item-text="name"
                      item-value="id"
                      label="Год от"
                      outlined
                      @click="deleted = false"
                      @click:clear="$nextTick(() => filter.year_from=null)"
                    />
                  </v-col>
                  <v-col cols="3" md="2" xs="6">
                    <v-select
                      v-model="filter.year_to"
                      :items="range(2023, 1960)"
                      dense
                      item-text="name"
                      item-value="id"
                      label="Год до"
                      outlined
                      @click="deleted = false"
                      @click:clear="$nextTick(() => filter.year_to=null)"
                    />
                  </v-col>
                </v-row>
                <v-btn color="error" dark @click="clearFilter()">
                  Сбросить
                </v-btn>
                <v-dialog v-model="dialog" max-width="900px">
                  <v-card>
                    <v-card-title>
                      <span class="headline">{{ formTitle }}</span>
                    </v-card-title>
                    <v-card-text>
                      <v-container>
                        <v-form ref="form" v-model="valid">
                          <v-row>
                            <v-col cols="12" md="3" sm="4">
                              <v-select
                                v-model="editedItem.mark_id"
                                :items="marks"
                                :rules="[(v) => !!v || 'Выберите марку']"
                                hide-details
                                item-text="name"
                                item-value="id"
                                label="Марка"
                                menu-props="auto"
                                required
                                :disabled="true"
                                @change="getModels(editedItem.mark_id)"
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="4">
                              <v-select
                                v-model="editedItem.model_id"
                                :items="models"
                                :rules="[(v) => !!v || 'Выберите модель']"
                                hide-details
                                item-text="name"
                                item-value="id"
                                label="Модель"
                                menu-props="auto"
                                required
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="4">
                              <v-select
                                v-model="editedItem.showroom_id"
                                :items="[
                                  { id: 1, name: 'Склад' },
                                  { id: 2, name: 'КомфортАвто' },
                                  { id: 3, name: 'Урус' },
                                  { id: 4, name: 'АвтоПремиум' },
                                  { id: 5, name: 'Авангард Юг' },
                                  { id: 7, name: 'Форсаж' },
                                ]"
                                hide-details
                                item-text="name"
                                item-value="id"
                                label="Шоурум"
                                menu-props="auto"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="6">
                              <v-select
                                v-model="editedItem.year"
                                :items="range(2023, 1960)"
                                :rules="[(v) => !!v || 'Выберите год выпуска']"
                                hide-details
                                label="Год выпуска"
                                menu-props="auto"
                                :disabled="true"
                                required
                              />
                            </v-col>
                            <v-col cols="12" md="3" sm="6">
                              <v-menu
                                v-model="menu1"
                                :close-on-content-click="false"
                                max-width="290"
                                :disabled="true"
                              >
                                <template v-slot:activator="{ on, attrs }">
                                  <v-text-field
                                    v-bind="attrs"
                                    :value="dateFormatted"
                                    clearable
                                    label="Приход"
                                    readonly
                                    :disabled="true"
                                    v-on="on"
                                    @blur="date = parseDate(dateFormatted)"
                                    @click:clear="editedItem.income_date = null"
                                  />
                                </template>
                                <v-date-picker
                                  v-model="editedItem.income_date"
                                  locale="ru-RU"
                                  :disabled="true"
                                  @change="menu1 = false"
                                />
                              </v-menu>
                            </v-col>
                            <v-col cols="12" md="2" sm="4">
                              <v-select
                                v-model="editedItem.bodytype_id"
                                :items="bodytypes"
                                :rules="[(v) => !!v || 'Выберите тип кузова']"
                                hide-details
                                item-text="name"
                                item-value="id"
                                label="Кузов"
                                menu-props="auto"
                                required
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="6">
                              <v-text-field
                                v-model="editedItem.milage"
                                label="Пробег"
                                type="number"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="6">
                              <v-select
                                v-model="editedItem.wheeltype_id"
                                :items="wheeltypes"
                                hide-details
                                item-text="name"
                                item-value="id"
                                label="Привод"
                                menu-props="auto"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="6">
                              <v-select
                                v-model="editedItem.transmission_id"
                                :items="transmissions"
                                hide-details
                                item-text="name"
                                item-value="id"
                                label="КПП"
                                menu-props="auto"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="4">
                              <v-select
                                v-model="editedItem.enginetype_id"
                                :items="enginetypes"
                                hide-details
                                item-text="name"
                                item-value="id"
                                label="Двигатель"
                                menu-props="auto"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="6">
                              <v-text-field
                                v-model="editedItem.salon"
                                label="Тип салона"
                                :disabled="true"
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="4">
                              <v-text-field
                                v-model="editedItem.color"
                                label="Цвет"
                                :disabled="true"
                              />
                            </v-col>

                            <v-col cols="12" md="2" sm="6">
                              <v-text-field
                                v-model="editedItem.key_number"
                                :disabled="true"
                                label="№ ключа"
                              />
                            </v-col>
                            <v-col cols="12" md="3" sm="6">
                              <v-select
                                v-model="editedItem.status_id"
                                :disabled="true"
                                :items="statuses"
                                hide-details
                                item-text="status"
                                item-value="id"
                                label="Статус"
                                menu-props="auto"
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="2" class="mx-0">
                              <v-text-field
                                append-icon="mdi-currency-usd"
                                v-model="editedItem.preparation_price"
                                label="Подготовка"
                                hide-details
                                class="mx-0"
                              />
                            </v-col>
                            <v-col cols="12" md="3" class="mx-0">
                              <v-text-field
                                append-icon="mdi-currency-usd"
                                v-model="editedItem.painting_price"
                                label="Покраска"
                                hide-details
                                class="mx-0"
                              />
                            </v-col>

                            <v-col cols="12" md="3" class="mx-0">
                              <v-text-field
                                v-model="editedItem.repair_price"
                                label="Ремонт"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" md="2" class="mx-0">
                              <v-text-field
                                v-model="editedItem.milage_price"
                                label="Расходы пробега"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" md="2" sm="2" class="mx-0">
                              <v-text-field
                                append-icon="mdi-currency-usd"
                                v-model="editedItem.transport_price"
                                label="Автовоз"
                                hide-details
                              />
                            </v-col>


                            <v-col cols="12" md="2" class="mx-0">
                              <v-text-field
                                append-icon="mdi-currency-usd"
                                v-model="editedItem.frontal_price"
                                label="Лобовое"
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" md="3" class="mx-0">
                              <v-text-field
                                append-icon="mdi-currency-usd"
                                :value="total_price()"
                                label="Всего расходов"
                                readonly
                                hide-details
                              />
                            </v-col>
                            <v-col cols="12" md="12">
                              <v-textarea
                                hide-details
                                rows="2"
                                v-model="editedItem.comment_purchase"
                                label="Расходы на ремонт"
                              />
                            </v-col>
                            <v-col cols="12" md="12" id="pic1">
                              <p>Загрузка фотографии</p>
                              <vue-dropzone key="1" id="dropzone" ref="myVueDropzone"
                                            accepted-file-types=".jpg,.png,.jpeg" :options="dropzoneOptions"
                                            :destroy-dropzone="false" @vdropzone-removed-file="vFileRemoved"
                                            @vdropzone-success="vFileAdded"/>
                            </v-col>
                            <v-col cols="12" md="12" id="pic2">
                              <p>Заказ-наряд</p>
                              <vue-dropzone key="2" id="testrest" ref="myVueDropzone2"
                                            accepted-file-types=".jpg,.png,.jpeg" :options="dropzoneOptions"
                                            :destroy-dropzone="false" @vdropzone-removed-file="vFileRemoved2"
                                            @vdropzone-success="vFileAdded2" @/>
                            </v-col>
                            <v-col cols="12" md="12" id="test_video">
                              <p>Видео</p>
                              <vue-dropzone key="3" id="restoftest" ref="myVueDropzone3"
                                            accepted-file-types=".mp4,.3gp,.mov, .avi, .wmv"
                                            :options="dropzoneVideoOptions" :destroy-dropzone="false"
                                            @vdropzone-removed-file="vFileRemovedVideo"
                                            @vdropzone-success="vFileAddedVideo"/>
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
                <v-dialog v-model="video_dialog" width="900px">
                  <v-card class="text-right">

                  <video controls width="900px">
                    <source :src="'/storage/uploads/purchase/' + video_preview"
                            type="video/mp4">

                    <a :href="'/storage/uploads/purchase/' + video_preview">Скачать видео</a>

                  </video>
                    <v-btn class="my-1 mr-3" color="primary" target="_blank" download :href="'/storage/uploads/purchase/' + video_preview"><v-icon>mdi-download </v-icon>Скачать видео</v-btn>

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
            <template v-slot:action="{ attrs }">
              <v-btn color="deep-orange" icon @click="snackbar = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </template>
          </v-snackbar>
          <FsLightbox
            :key="componentKey2"
            :toggler="toggler"
            :sources="addUrl(preview_pictures)"
            :slide="slide"
            :source-index="slide"
          />
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<style scoped>
table {
  border: none;
  border-collapse: collapse;
}

table td {
  border: 1px solid black;
  padding: 0 1px !important;
  text-align: center;
}

table th {
  border: 1px solid rgb(204, 200, 200);
  padding: 0 4px !important;
  font-weight: 700;
  color: black;
}

.v-data-table > .v-data-table__wrapper > table > thead > tr > th {
  padding: 0 4px !important;

  font-weight: 700;
  font-size: 0.7rem;
  color: black !important;
}

table.v-data-table__wrapper > table > thead > tr > th {
  padding: 0 6px;
}

.v-data-table > .v-data-table__wrapper > table > tfoot > tr > th {
  padding: 0 2px;
  font-size: 0.7rem;

}

.v-data-table > .v-data-table__wrapper > table > tbody > tr > td {
  font-size: 0.7rem;
  font-weight: 600;
  height: 0px;
}

table.v-table tbody td:first-child,
table.v-table tbody td:not(:first-child),
table.v-table tbody th:first-child,
table.v-table tbody th:not(:first-child),
table.v-table thead td:first-child,
table.v-table thead td:not(:first-child),
table.v-table thead th:first-child,
table.v-table thead th:not(:first-child) {
  padding: 0 2px !important;
  font-size: 0.7rem;
  font-weight: 500;
}

.vertical {
  writing-mode: tb-rl;
  font-size: 0.70rem;
  -webkit-transform: rotate(180deg);
  -moz-transform: rotate(180deg);
  -o-transform: rotate(180deg);
  -ms-transform: rotate(180deg);
  transform: rotate(180deg);
  white-space: nowrap;
  display: block;
  bottom: 0;
  height: 60px;
  margin-bottom: 5px;
  align-content: center;
  padding: 0;
}

.v-data-table
/deep/
tbody
/deep/
tr:hover:not(.v-data-table__expanded__content) {
  background: yellow !important;
}
</style>
<script>
import BreadCrumb from '~/components/BreadCrumb'
import vue2Dropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
import axios from "axios";
import FsLightbox from 'fslightbox-vue'

export default {
  layout: 'user',
  middleware: 'permission',
  components: {BreadCrumb, vueDropzone: vue2Dropzone, FsLightbox},

  data() {
    let path= ''
    return {
    menu1: null,
    componentKey: 0,
    componentKey2: 100,
    toggler: false,

    slide: 0,
    dropzoneOptions: {
      url: path + '/api/usedcar/upload',
      thumbnailWidth: 300,
      maxFilesize: 3,
      // parallelUploads: 1,
      maxFiles: 8,
      headers: {},
      addRemoveLinks: true,
      dictDefaultMessage: "<i class='v-icon mdi mdi-camera theme--light text-h3'></i> Выберите фото",
      dictMaxFilesExceeded: 'Вы не можете загружать более 8 файлов',
      dictRemoveFile: 'Удалить',
      uploadMultiple: false,
      removeType: 'server',
      destroyDropzone: false
    },
    dropzoneVideoOptions: {
      url: path + '/api/usedcar/upload-video',
      thumbnailWidth: 300,
      maxFilesize: 200,
      // parallelUploads: 1,
      maxFiles: 1,
      headers: {},
      acceptedFiles: ".mp4, .3gp, .mov, .avi, .wmv",
      addRemoveLinks: true,
      dictDefaultMessage: "<i class='v-icon mdi mdi-video theme--light text-h3'></i> Выберите видео",
      dictMaxFilesExceeded: 'Вы не можете загружать более 3 файлов',
      dictRemoveFile: 'Удалить',
      uploadMultiple: false,
      removeType: 'server',
      destroyDropzone: false
    },
    filter: {
      mark_id: null,
      model_id: null,
      transmission_id: null,
      wheeltype_id: null,
      year_from: null,
      year_to: null,
      milage_from: null,
      milage_to: null,
      incomePriceFrom: null,
      incomePriceTo: null
    },
    deleted: false,
    menu: null,
    dialog: false,
    video_dialog: false,
    valid: false,
    snackbar: false,
    text: '',
    color: '',
    timeout: 6000,
    expanded: [],
    pictures: [],
    pictures2: [],
    preview_pictures: [],
    videos: [],
    video_preview: null,
    singleExpand: false,
    headers: [
      {
        text: 'Дата прихода',
        value: 'income_date',
        align: 'center',
        sortable: false,
        width: '40px'
      },
      {
        text: 'Марка',
        align: 'start',
        value: 'mark.name',
        width: '100px'
      },
      {
        text: 'Модель',
        value: 'model.name',
        width: '110px',
        align: 'center'
      },
      {
        text: 'Год',
        value: 'year',
        sortable: false,
        width: '50px',
        align: 'center'
      },
      {
        text: 'Пробег',
        value: 'milage',
        sortable: false,
        width: '20px',
        align: 'center'
      },
      {
        text: 'Vin',
        value: 'vin_number',
        sortable: false,
        width: '25px',
        align: 'center'
      },
      {
        text: 'Расходы',
        value: 'repair_price',
        width: '30px',
        align: 'center',
        sortable: false
      },
      {
        text: 'Фото',
        value: 'pictures',
        width: '30px',
        align: 'center',
        sortable: false
      },
      {
        text: 'Заказ-наряд',
        value: 'pictures2',
        width: '30px',
        align: 'center',
        sortable: false
      },
      {
        text: 'Видео',
        value: 'videos',
        width: '30px',
        align: 'center',
        sortable: false
      },
      {
        text: 'Статус',
        value: 'status.status',
        align: 'center',
        sortable: false,
        width: '100px'
      }
    ],
    editedItem: {
      id: '',
      showroom_id: '',
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
      milage: 0,
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
      comment_purchase: null,
      repair_price: '',
      preparation_price: '',
      painting_price: '',
      frontal_price: '',
      milage_price: '',
      transport_price: '',
      pictures: [],
      pictures2: [],
      videos: [],
    },
    defaultItem: {
      id: '',
      showroom_id: '',
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
      milage: 0,
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
      comment_purchase: null,
      repair_price: '',
      preparation_price: '',
      painting_price: '',
      frontal_price: '',
      milage_price: '',
      transport_price: '',
      pictures: [],
      pictures2: [],
      videos: [],
    },
    options: {
      sortBy: ['mark.name', 'model.name'],
      multiSort: true
    }
    }
  },

  computed: {
    dateFormatted() {
      return this.editedItem.income_date ? this.moment(this.editedItem.income_date).format('DD.MM.Y') : null
    },
    showroom_id() {
      return Number(this.$route.params.id) || null
    },
    showroom() {
      return this.$store.state.showroom.showroom
    },
    formTitle() {
      return this.editedIndex === -1 ? 'Новая запись' : 'Изменить'
    },
    usedcars() {
      return this.$store.state.usedcar.cars
    },
    filtered() {
      let cars = this.usedcars
      if (this.deleted) {
        cars = cars.filter(item => item.deleted_at !== null)
      } else {
        cars = cars.filter(item => item.deleted_at === null)
      }
      if (this.$route.params.id) {
        cars = cars.filter(item => item.showroom_id === Number(this.$route.params.id) || item.transit === 1)
      }
      if (this.filter.mark_id) {
        cars = cars.filter(item => item.mark_id === this.filter.mark_id && item.deleted_at === null)
      }
      if (this.filter.model_id) {
        cars = cars.filter(item => item.model_id === this.filter.model_id && item.deleted_at === null)
      }
      if (this.filter.transmission_id) {
        cars = cars.filter(item => item.transmission_id === this.filter.transmission_id && item.deleted_at === null)
      }
      if (this.filter.wheeltype_id) {
        cars = cars.filter(item => item.wheeltype_id === this.filter.wheeltype_id && item.deleted_at === null)
      }
      if (this.filter.year_from && this.filter.year_to) {
        cars = cars.filter(item => item.year >= this.filter.year_from && item.year <= this.filter.year_to)
      } else if (this.filter.year_from) {
        cars = cars.filter(item => item.year >= this.filter.year_from)
      } else if (this.filter.year_to) {
        cars = cars.filter(item => item.year <= this.filter.year_to)
      }
      if (this.filter.milage_from && this.filter.milage_to) {
        cars = cars.filter(item => item.milage >= this.filter.milage_from && item.milage <= this.filter.milage_to)
      } else if (this.filter.milage_from) {
        cars = cars.filter(item => item.milage >= this.filter.milage_from)
      } else if (this.filter.milage_to) {
        cars = cars.filter(item => item.milage <= this.filter.milage_to)
      }
      if (this.filter.incomePriceFrom && this.filter.incomePriceTo) {
        cars = cars.filter(item => item.income_price >= this.filter.incomePriceFrom && item.income_price <= this.filter.incomePriceTo)
      } else if (this.filter.incomePriceFrom) {
        cars = cars.filter(item => item.income_price >= this.filter.incomePriceFrom)
      } else if (this.filter.incomePriceTo) {
        cars = cars.filter(item => item.income_price <= this.filter.incomePriceTo)
      }
      return cars
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
    statuses() {
      return this.$store.state.usedcar.statuses
    },
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
          href: '/' + this.role?.name
        },
        {
          text: 'Автомобили БУ',
          disabled: false,
          href: '/' + this.role?.name + '/used-cars'
        },
        {
          text: this.showroom.name || '',
          disabled: true,
          href: '/' + this.role?.name + '/used-cars/' + (this.showroom.id || '')
        }
      ]
    }
  },
  watch: {
    dialog(val) {
      val || this.close()
    },
    video_dialog(val) {
      val || (this.video_preview = null)
    },
  },

  async fetch({store, error, params: {id}}) {
    await store.dispatch('usedcar/fetchCars',{showroom_id:id})
    await store.dispatch('car/fetchMarks')
    await store.dispatch('car/fetchBodyTypes')
    await store.dispatch('car/fetchEngineTypes')
    await store.dispatch('car/fetchTransmissions')
    await store.dispatch('car/fetchWheelTypes')
    await store.dispatch('showroom/fetchShowroom', {id})
    await store.dispatch('user/toggle', false)
  },
  mounted() {
    this.$echo.channel('used-cars')
      .listen('UsedCarAdded', (e) => {
        console.log('usedcar added')
        this.$store.dispatch('usedcar/fetchCars', {showroom_id:this.showroom_id}).catch((error) => {
          console.log(error)
          this.reload()
        })
      })
  },
  methods: {

    save() {
      if (this.valid) {
        this.editedItem.pictures = this.pictures
        this.editedItem.pictures2 = this.pictures2
        this.editedItem.videos = this.videos
        this.$store.dispatch('usedcar/savePurchase', {item: this.editedItem})
        this.showSnack('success', 'Запись изменён')
        this.close()
      } else {
        this.showSnack('error', 'Заполните обязательные поля')
      }

    },

    vFileAdded(file, response) {
       file.upload.filename = response.file
      this.pictures.push(response.file)
    },
    async vFileRemoved(file) {
      const filename = file.manuallyAdded === true ? file.name : file.upload.filename
      const index = this.pictures.indexOf(filename)
      if (index > -1) {
        this.pictures.splice(index, 1)
      }
      console.log(123)
      await axios.post('/usedcar/delete-photo/' , {filename: filename})
        .then((response) => {
          this.showSnack('success', 'Фото удалён')
        })
        .catch((error) => {
          this.showSnack('error', error)
        })
    },
    vFileAdded2(file, response) {
      file.upload.filename = response.file
      this.pictures2.push(response.file)
    },
    async vFileRemoved2(file) {
      const filename = file.manuallyAdded === true ? file.name : file.upload.filename
      const index = this.pictures2.indexOf(filename)


      await axios.post('/usedcar/delete-photo', {filename: filename})
        .then((response) => {
          if (index > -1) {
            this.pictures2.splice(index, 1)
          }
          this.showSnack('success', 'Фото удалён')
        })
        .catch((error) => {
          this.showSnack('error', error)
        })
    },
    vFileAddedVideo(file, response) {
       file.upload.filename =  response.file
      this.videos.push(response.file)
    },
    async vFileRemovedVideo(file) {
      const filename = file.manuallyAdded === true ? file.name : file.upload.filename
      const index = this.videos.indexOf(filename)

      if (index > -1) {
        this.videos.splice(index, 1)
      }
      await axios.post('/usedcar/delete-video', {filename: filename})
        .then((response) => {
          this.showSnack('success', 'Видео удалён')
        })
        .catch((error) => {
          this.showSnack('error', error)
        })
    },
    editItem(item) {
      this.editedIndex = this.usedcars.indexOf(item)
      this.editedItem = Object.assign({}, item)
      if (item.pictures && item.pictures.length > 0) {
        this.pictures = item.pictures
      }
      if (item.pictures2 && item.pictures2.length > 0){
        this.pictures2 = item.pictures2
      }
      if (item.videos && item.videos.length > 0){
        this.videos = item.videos
      }

      if (item.pictures !== null) {
        for (let i = 0; i < item.pictures.length; i++) {
          const file = {size: 123, name: item.pictures[i], type: 'image/png'}
          const url = '/storage/uploads/purchase/' + item.pictures[i]
          this.$nextTick(function () {
            this.$refs.myVueDropzone.manuallyAddFile(file, url)
          })
        }
      }
      if (item.pictures2 !== null) {
        for (let i = 0; i < item.pictures2.length; i++) {
          const file = {size: 123, name: item.pictures2[i], type: 'image/png'}
          const url = '/storage/uploads/purchase/' + item.pictures2[i]
          this.$nextTick(function () {
            this.$refs.myVueDropzone2.manuallyAddFile(file, url)
          })
        }
      }
      if (item.videos !== null) {
        for (let i = 0; i < item.videos.length; i++) {
          const file = {name: item.videos[i], type: 'video/mp4'}
          const url = '/storage/uploads/purchase/' + item.videos[i]
          this.$nextTick(function () {
            this.$refs.myVueDropzone3.manuallyAddFile(file, url)
          })
        }
      }
      this.dialog = true
      if (item.mark_id !== 0) {
        this.$store.dispatch('car/fetchModels', {markId: item.mark_id})
      }
    },


    addUrl (pictures) {
      const result = []
      for (const x in pictures) {
        if (x) {
          result.push(
            '/storage/uploads/purchase/' + pictures[x]
          )
        }
      }
      return result
    },

    parseDate(date) {
      if (!date) {
        return null
      }
      // const [day, month, year] = date.split('.')
      return this.moment(date).format('YY-MM-dd')
    },

    clearFilter() {
      this.filter.mark_id = null
      this.filter.model_id = null
      this.filter.transmission_id = null
      this.filter.wheeltype_id = null
      this.filter.milage_from = null
      this.filter.milage_to = null
      this.filter.year_from = null
      this.filter.year_to = null
      this.$store.dispatch('car/emptyModels')
    },


    total_price() {
      return (parseFloat(this.editedItem.painting_price) || 0) + (parseFloat(this.editedItem.preparation_price) || 0) + (parseFloat(this.editedItem.repair_price) || 0) + (parseFloat(this.editedItem.transport_price) || 0) + (parseFloat(this.editedItem.milage_price) || 0) + (parseFloat(this.editedItem.frontal_price) || 0)
    },
    total_item_price(item) {
      return (parseFloat(item.painting_price) || 0) + (parseFloat(item.preparation_price) || 0) + (parseFloat(item.repair_price) || 0) + (parseFloat(item.transport_price) || 0) + (parseFloat(item.milage_price) || 0) + (parseFloat(item.frontal_price) || 0)
    },

    range(start, end, step = 1) {
      const years = []
      if (start > end) {
        for (let i = start; i >= end; i = i - step) {
          if (step === 1) {
            years.push(i)
          } else {
            years.push(i.toFixed(1))
          }
        }
      } else {
        for (let i = start; i <= end; i = i + step) {
          if (step === 1) {
            years.push(i)
          } else {
            years.push(i.toFixed(1))
          }
        }
      }
      return years
    },
    getModels(markId = null) {
      if (markId > 0) {
        this.$store.dispatch('car/fetchModels', {markId})
      }
    },
    showVideo(videos) {
      if (videos) {
        this.video_preview = videos[0]
        this.video_dialog = true
      }
    },
    showImage(pictures) {
      this.slide = 0
      this.componentKey = this.componentKey + 1
      this.preview_pictures = pictures
      this.$mount()
      this.toggler = !this.toggler
    },
    row_classes(item) {
      if (item.status_id === 5) {
        return 'primary white--text'
      } else if (item.transit === 1 || item.transit === 2) {
        return 'orange lighten-2' // can also return multiple classes e.g ["orange","disabled"]
      } else if (item.deleted_at !== null) {
        return 'black lighten-4 white--text'
      }
    },

    getColor(transit) {
      if (transit === 1) {
        return 'primary' // can also return multiple classes e.g ["orange","disabled"]
      } else if (transit === 2) {
        return 'error'
      } else {
        return 'success'
      }
    },
    transitStatus(item) {
      if (item.transit === 1 && item.showroom_id === this.showroom_id) {
        return 'На выезде (Отменить)' // can also return multiple classes e.g ["orange","disabled"]
      } else if (item.transit === 1) {
        return 'Принять транзит'
      } else {
        return 'Передать в другой салон'
      }
    },
    isNumber(evt) {
      evt = (evt) || window.event
      const charCode = (evt.which) ? evt.which : evt.keyCode
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        evt.preventDefault()
      } else {
        return true
      }
    },
    oldStyle(date) {
      return (date !== null && this.moment().diff(this.moment(date), 'days') > 90) ? 'background-color: red; color: white;' : ''
    },
    reload() {
      this.intervalid1 = setInterval(() => {
        console.log('reload')
        this.$store.dispatch('usedcar/fetchCars', {showroom_id:this.showroom_id})
      }, 15000)
    },
    close() {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem.is_ready = null
        this.componentKey += 1
        this.$refs.form.reset()
        this.pictures = []
        this.pictures2 = []
        this.videos = []
        //this.$refs.myVueDropzone.removeAllFiles(true)
        //this.$refs.myVueDropzone2.removeAllFiles(true)
        //this.$refs.myVueDropzone3.removeAllFiles(true)
      })


    },
    showSnack(type, text) {
      this.text = text
      this.color = type
      this.snackbar = true
    }

  },
  beforeRouteLeave(to, from, next) {
    this.$store.dispatch('user/toggle', true)
    next()
  }
}
</script>

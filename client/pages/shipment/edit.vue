<template>
  <v-container>
    <div class="d-flex align-center p-3">
      <div>
        <div class="display-1">Транспортировка машин</div>
        <v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>
      </div>
    </div>

    <v-row>
      <v-col cols="12" md="8">

        <!-- cart list -->
        <div v-if="cart.length === 0">
          <v-divider></v-divider>
          <div class="text-h5 pa-5 text-center">Empty Cart</div>
          <v-divider></v-divider>
        </div>
        <div class="d-flex my-6">

          <v-btn
class="mr-3" color="primary" dark
                 @click="dialog=true">
            <v-icon left>mdi-plus</v-icon>
            Добавить новую машину
          </v-btn>
          <v-btn
color="secondary" dark
                 @click="dialog=true">
            <v-icon left>mdi-plus</v-icon>
            Добавить БУ
          </v-btn>
          <v-dialog v-model="dialog" max-width="800px">

            <v-card>
              <v-card-title>
                <span class="headline">{{ formTitle }}</span>
              </v-card-title>
              <v-card-text>
                <v-container>
                  <v-form ref="form" v-model="valid">
                    <v-row>
                      <v-col cols="12" sm="6" md="6">
                        <v-text-field
                          v-model="editCar.name"
                          label="Имя"
                          hide-details
                          outlined
                          dense
                        />
                      </v-col>

                      <v-col cols="12" sm="6" md="6">
                        <v-select
                          v-model="editCar.is_used"
                          :items="[
                            {
                              id:0,
                              name: 'Новая'
                            },
                            {
                              id:1,
                              name: 'БУ'
                            }
                          ]"
                          item-text="name"
                          item-value="id"
                          hide-details
                          label="Тип машины"
                          outlined
                          dense
                        />
                      </v-col>

                      <v-col cols="12" sm="6" md="4">
                        <v-text-field outlined dense hide-details label="Кол-во"></v-text-field>
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                        <v-text-field outlined dense hide-details label="Кол-во"></v-text-field>
                      </v-col>
                      <v-col cols="12" sm="6" md="4">
                        <v-text-field outlined dense hide-details label="Кол-во"></v-text-field>
                      </v-col>
                      <v-col cols="12" sm="6" md="6">
                        <v-text-field outlined dense hide-details label="Кол-во"></v-text-field>
                      </v-col>
                      <v-col
                        md="6"
                        cols="12"
                      >
                        <v-text-field outlined dense hide-details label="Кол-во"></v-text-field>
                      </v-col>
                      <v-col
                        md="4"
                        cols="12"
                      >
                        <v-text-field outlined dense hide-details label="Кол-во"></v-text-field>
                      </v-col>
                      <v-col
                        md="4"
                        cols="12"
                      >
                        <v-text-field
v-model="editCar.amount" outlined dense hide-details type="number" label="Цена"
                                      hide-details dense/>
                      </v-col>

                      <v-col
                        md="12"
                        cols="12"
                      >
                        <v-textarea
v-model="editCar.comment" hide-details hide-details dense outlined
                                    label="Комментарий" rows="3"/>
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
        </div>
        <div v-for="(item, index) in cart" :key="index" class="my-2">

          <v-divider v-if="index !== 0" class="my-1"></v-divider>
          <div class="d-flex align-center justify-center font-weight-bold">
            <div class="d-none d-md-block">

            </div>
            <div class="flex-grow-1">
              {{ item.title }}
            </div>
            <div class="d-none d-sm-block mx-1 mx-sm-4">
              <div class="text-overline">Цена:</div>
              {{ item.price | formatCurrency }}
            </div>
            <div class="mx-1 mx-sm-4">
              <v-select
                v-model="item.quantity"
                :items="[1, 2, 3, 4, 5]"
                label="Кол-во"
                outlined
                hide-details
                dense
                style="max-width: 80px;"
              ></v-select>
            </div>
            <div class="mx-1 mx-sm-4">
              <div class="text-overline">Итого:</div>
              {{ (item.price * item.quantity) | formatCurrency }}
            </div>
            <v-btn icon @click="cart.splice(index, 1)">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </div>
        </div>

        <div class="d-flex my-6">
          <v-btn color="secondary" to="/shipments">
            <v-icon left>mdi-arrow-left</v-icon>
            К транспортировкам
          </v-btn>
        </div>
      </v-col>

      <v-col cols="12" md="4">
        <v-card class="pa-2">
          <div class="text-h5 mb-6">Оптравка к Ростову</div>
          <div class="d-flex">
            <div>Дата:</div>
            <v-spacer></v-spacer>
            <div>{{ subtotal | formatCurrency }}</div>
          </div>
          <v-divider class="my-2"></v-divider>
          <div v-if="discount" class="d-flex">
            <div>Цена транспортировки:</div>
            <v-spacer></v-spacer>
            <div>- {{ discount | formatCurrency }}</div>
          </div>
          <v-divider class="my-2"></v-divider>
          <div class="d-flex text-h6">
            <div class="text-uppercase">Итого:</div>
            <v-spacer></v-spacer>
            <div>{{ total | formatCurrency }}</div>
          </div>
          <v-btn
            large
            color="success"
            block
            class="mt-8"
            :disabled="cart.length === 0"
          >
            <v-icon left>mdi-cart-outline</v-icon>
            Оформление
          </v-btn>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import cart from './content/cart'

export default {
  data() {
    return {
      isLoading: false,
      dialog: false,
      valid: false,
      editedIndex: '',
      breadcrumbs: [
        {
          text: 'Главная',
          disabled: false,
          to: '/'
        },
        {
          text: 'Транспортировка машин',
          disabled: true,
          to: '/shipments'
        },
        {
          text: 'Детальная страница'
        }],
      discount: 10,
      cart,
      editCar: {
        id: '',
        car_id: '',
        is_used: '',
        quantity: '',
        amount: '',
        comment: '',
      },
      defaultCar: {
        id: '',
        car_id: '',
        is_used: '',
        quantity: '',
        amount: '',
        comment: '',
      },
      editUsedCar: {
        id: '',
        car_id: '',
        is_used: '',
        quantity: '',
        amount: '',
        comment: '',
      },
      defaultUsedCar: {
        id: '',
        car_id: '',
        is_used: '',
        quantity: '',
        amount: '',
        comment: '',
      },
    }
  },
  computed: {
    subtotal() {
      let total = 0

      this.cart.forEach((c) => {
        total += c.quantity * c.price
      })

      return total
    },
    formTitle() {
      return this.editedIndex === -1 ? 'Добавить пользователя' : 'Изменить'
    },
    total() {
      const total = this.subtotal - this.discount

      return total < 0 ? 0 : total
    },
    async fetchCars(val) {
      if (val === 0) {
        await store.dispatch('shipment/fetchCars', {})
      } else {
        await store.dispatch('shipment/fetchUsedCars', {})
      }

    }
  },

  watch: {
    dialog(val) {
      val || this.close()
    },
    is_used(val) {
      this.fetchCars(val)
    }
  },
  methods: {
    close() {
      this.dialog = false
      this.$nextTick(() => {
        this.editCar = Object.assign({}, this.defaultCar)
        this.editedIndex = -1
      })
    },
    editItem(item) {
      this.editedIndex = this.cars.indexOf(item)
      this.editCar = Object.assign({}, item)
      this.dialog = true
    },
    validate() {
      this.$refs.form.validate()
    },
  }

}
</script>

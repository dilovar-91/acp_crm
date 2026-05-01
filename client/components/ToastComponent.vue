<template>
    <v-card
        class="mx-auto toast-box"
        max-width="344"
    >
       <v-card-text class="flat">
           <p class="text-h6 text--primary">
               Входящий звонок от: <b v-if="data.phone.startsWith('7')">{{data.phone | mask('+7 ### ###-##-##')}}</b>
               <b v-else-if="data.phone.startsWith('8')">{{data.phone | mask('+7 ### ###-##-##')}}</b>
           </p>
           <p v-if="data.client_name !==null" class="text-h6 text--primary">
               Клиент: <b>{{data.client_name  }}</b>
           </p>
           <p v-if="data.status" class="text-h6 text--primary">
             Статус: <b>{{data.status?.name}}</b>
           </p>
           <p v-if="data.line_number || data.site_name !== null" class="text-h6 text--primary">
               Сайт: <b>{{data.site_name}}</b>
           </p>
           <p v-if="data.operator" class="text-h6 text--primary">
               Оператор: <b>{{data.operator?.last_name }} {{data.operator?.first_name }} {{data.operator?.middle_name }}</b>
           </p>
           <p v-if="data.region" class="text-h6 text--primary">
               Регион: <b>{{data.region?.name}}</b>
           </p>
           <p v-if="data.phone_region" class="text-h6 text--primary">
               Регион регистрации: <b>{{data.phone_region?.name}}</b>
           </p>
           <p v-if="data.phone_operator" class="text-h6 text--primary">
               Оператор связи: <b>{{data.phone_operator?.name}}</b>
           </p>
           <p v-if="data?.another_showroom" class="text-h6 text--primary">
               С другого салона: <b>Да</b>
           </p>
           <p v-if="data.date !== null" class="text-h6 text--primary">
               Дата заяки: <b>{{ $nuxt.$moment(data.date).format('DD.MM.YYYY HH:mm') }}</b>
           </p>
       </v-card-text>
        <v-card-actions class="py-0">
        <template v-if="data.client_name === null" >
          <v-btn
            v-if="data.order_id === null"
                text
                color="teal accent-4"
                @click = "createOrder()"
            >
                Создать заявку
            </v-btn>
        </template>
          <template v-else >
          <v-btn
            v-if="data.order_id !== null"
            text
            color="teal accent-4"
            target="_blank"
            @click = "openOrder()"
          >
            Открыть заявку
          </v-btn>
          </template>
            <v-btn
                text
                color="teal accent-4"
                @click = "removeOrder()"
            >
                Удалить
            </v-btn>
        </v-card-actions>
    </v-card>
</template>
<script>
import axios from "axios";
export default {
    props:{
        data :{
            type: Object,
            required:true
        },
        sites :{
            type: Array,
            required:true
        },
        isMini :{
            type: Boolean,
            required:false,
            default: false
        },
    },
    methods:{
       openOrder() {
         //window.location.replace(`/crm/${(this.data?.showroom_id ||this.$nuxt.$route.params?.id)}/order/${this.data?.order_id}/edit`);
         window.open(`https://${window.location.hostname}/crm/${(this.data?.showroom_id ||this.$nuxt.$route.params?.id)}/order/${this.data?.order_id}${this.isMini ? '/edit-mini':'/edit'}`, '_blank');
         //this.$nuxt.$router.push(`/crm/8/order/${this.$route.params?.id}/edit`)
       },
       async createOrder() {

           try {
               const item = {
                 phone: this.data?.phone,
                 client_name: this.data?.client_name,
                 operator_id:  this.$nuxt.$auth.user?.id,
                 showroom_id:  this.$nuxt.$auth.user?.showroom_id || this.$nuxt.$route.params?.id,
                 status_id:  1,
                 type_id:  1,
                 site_id:  this.data?.site_id,
               };
             console.log(item)

              await this.$nuxt.$axios.post('orders/save', { item })
               .then((response) => {
                 this.$nuxt.$toast.clear()

                 this.$nuxt.$toast.success('Заявка успешно создана', {
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

                 console.log(response)
                 /*
                 if (this.data?.showroom_id === 9) {
                   window.location.replace(`/crm/${(this.data?.showroom_id ||this.$nuxt.$route.params?.id)}/order/${this.data?.order_id}/edit-mini`);
                 } else {
                   window.location.replace(`/crm/${(this.data?.showroom_id ||this.$nuxt.$route.params?.id)}/order/${this.data?.order_id}/edit`);
                 }*/

                 this.$axios.post("/clear-notify", {phone: this.data.phone })

               })
               .catch((error) => {
                 console.log(error)
               })


           } catch (e) {
               this.$nuxt.$toast.error('Произошла ошибка:' + e, {
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


       },
       removeOrder() {
           this.$nuxt.$toast.clear()
       }
    }
}
</script>
<style>
.toast-box {
    background-color: rgb(255, 234, 168) !important;
}
.flat {
    padding-top: 1px !important;
    padding-bottom: 1px !important;
}
.Vue-Toastification__toast--default.call_info {
    padding: 0 !important;
    border-radius: 2px;
    border: 1px black solid;
    color: rgba(3, 3, 3, 0);
    background-color: white;
}
</style>

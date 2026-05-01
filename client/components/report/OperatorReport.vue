<template>
    <v-data-table
        v-if="items"
        :headers="headers"
        :items="items"
        sort-by="calories"
        class="elevation-1"
        hide-default-footer
    >
        <template #top>
            <v-toolbar
                flat
            >
                <v-toolbar-title class="mr-3">
                    Отчеты по операторам <span v-if="date_from && date_to">с {{ date_from }} по {{ date_to }}</span>
                </v-toolbar-title>

                <v-spacer/>
            </v-toolbar>
        </template>
        <template #body>
            <tbody>
            <tr
                v-for="item in items"
                :key="item.id"
            >
                <td>{{ item.name }}</td>
                <td>{{ item.NewCount }}</td>
            </tr>

            <tr>
                <td>Итого</td>
                <td>{{ getTotal('status_id', 12) }}</td>
                <td>{{ getTotal('status_id', 2) }}</td>
                <td>{{ getTotal('status_id', 3) }}</td>
                <td>{{ getTotal('status_id', 10) }}</td>
                <td>{{ getTotal('status_id', 11) }}</td>
                <td>{{ getTotal('status_id', 15) }}</td>
                <td>{{ getTotal('status_id', 6) }}</td>
                <td>{{ getTotal('status_id', 14) }}</td>
                <td>{{ getTotal('status_id', 4) }}</td>
                <td>{{ getTotal('status_id', 5) }}</td>
                <td>{{ getTotal('status_id', 8) }}</td>
                <td>{{ getTotal('status_id', 13) }}</td>
                <td>{{ getTotal('others', 1) }}</td>
                <td>{{ getTotal('efficiency') }}</td>
                <td>{{ items.length }}</td>
            </tr>
            </tbody>
        </template>
        <template #no-data>
            Пусто
        </template>
    </v-data-table>
</template>
<script>
// import AllTimeOperators from '~/apollo/queries/order/AllTimeOperators.gql'
// import reportOperators from '~/apollo/queries/order/reportOperators.gql'
// import operators from '~/apollo/queries/getOperators.gql'

export default {
    props: {
        date_from: {
            type: String,
            required: false
        },
        date_to: {
            type: String,
            required: false
        },
        order_type: {
            type: Number,
            required: false,
            default: null
        }

    },
    data: () => ({
        headers: [
            {
                text: 'Оператор',
                align: 'start',
                sortable: false,
                value: 'name'
            },
            {text: 'Новая', value: 'calories', sortable: false},
            {text: 'В работе', value: 'fat', sortable: false},
            {text: 'Не отвечает', value: 'fat', sortable: false},
            {text: 'Приедет', value: 'carbs', sortable: false},
            {text: 'Корзина', value: 'protein', sortable: false},
            {text: 'Повтор', value: 'actions', sortable: false},
            {text: 'Приехал', value: 'actions', sortable: false},
            {text: 'Долги ФССП', value: 'actions', sortable: false},
            {text: 'Не подавал', value: 'actions', sortable: false},
            {text: 'Консультация', value: 'actions', sortable: false},
            {text: 'Отказ клиента', value: 'actions', sortable: false},
            {text: 'Отказ по банкам', value: 'actions', sortable: false},
            {text: 'Прочие', value: 'actions', sortable: false},
            {text: 'Эффективность', value: 'actions', sortable: false},
            {text: 'Всего', value: 'actions', sortable: false}
        ]

    }),
    computed: {
        items() {
            return this.date_from && this.date_to ? this.reportOperators : this.AllTimeOperators
        }
    },
  /*
    apollo: {
        operators: {
            prefetch: true,
            query: operators,
            variables() {
                return {role_id: 2, active: true}
            }
        },
        reportOperators: {
            prefetch: false,
            query: reportOperators,
            variables() {
                return {
                    type_id: this.order_type,
                    from: this.$moment(this.date_from, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss'),
                    to: this.$moment(this.date_to, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss')
                }
            },
            skip() {
                return !(this.date_from && this.date_to)
            }
        },
        AllTimeOperators: {
            prefetch: true,
            query: AllTimeOperators,
            variables() {
            },
            skip() {
                return !!(this.date_from && this.date_to)
            }
        }

    }, */
    methods: {
        getCount(id, type, value = 0) {
            let orders = 0
            let res = 0
            let willArrive = 0
            let arrived = 0
            switch (type) {
                case 'type_id':
                    orders = this.items.filter(l => l.operator_id === id).length
                    res = this.items.filter(l => l.operator_id === id && l.type_id === value).length
                    return res > 0 ? (res + ' (' + ((res * 100) / orders).toFixed(2) + '%)') : 0
                case 'status_id':
                    orders = this.items.filter(l => l.operator_id === id).length
                    res = this.items.filter(l => l.operator_id === id && l.status_id === value).length
                    return res > 0 ? (res + ' (' + ((res * 100) / orders).toFixed(2) + '%)') : 0
                case 'retries':
                    orders = this.items.filter(l => l.operator_id === id).length
                    res = this.items.filter(l => l.operator_id === id && l.retries !== null).length
                    return res > 0 ? (res + ' (' + ((res * 100) / orders).toFixed(2) + '%)') : 0
                case 'others':
                    orders = this.items.filter(l => l.operator_id === id).length
                    res = this.items.filter(l => l.operator_id === id && ([1, 9, 7].includes(l.status_id) || l.status_id===null)).length
                    return res > 0 ? (res + ' (' + ((res * 100) / orders).toFixed(2) + '%)') : 0
                case 'all':
                    return this.items.filter(l => l.operator_id === id).length
                case 'efficiency':
                    orders = this.items.filter(l => l.operator_id === id).length ?? 0
                    res = this.items.filter(l => l.operator_id === id).length ?? 0
                    willArrive = this.items.filter(l => l.operator_id === id).length ?? 0
                    arrived = this.items.filter(l => l.operator_id === id && l.status_id === 6).length ?? 0
                    return ((((res * 100) / orders) || 0) + (((willArrive * 100) / orders) || 0) + (((arrived * 100) / orders) || 0)).toFixed(2) + '%'
                default:
                    console.log('empty')
            }
        },
        getTotal(type, value = 0) {
            let orders = 0
            let res = 0
            let willArrive = 0
            let arrived = 0
            let retries = 0
            let news = 0
            orders = this.items.filter(l => l.operator_id !== null).length
            switch (type) {
                case 'type_id':
                    res = this.items.filter(l => l.type_id === value && l.operator_id !== null).length
                    return res > 0 ? (res + ' (' + ((res * 100) / orders).toFixed(2) + '%)') : 0
                case 'status_id':
                    res = this.items.filter(l => l.status_id === value && l.operator_id !== null).length
                    return res > 0 ? (res + ' (' + ((res * 100) / orders).toFixed(2) + '%)') : 0
                case 'retries':
                    res = this.items.filter(l => l.retries > 0 && l.operator_id !== null).length
                    return res > 0 ? (res + ' (' + ((res * 100) / orders).toFixed(2) + '%)') : 0
                case 'others':
                    res = this.items.filter(l => ([1, 9, 7].includes(l.status_id) || l.status_id===null) || l.operator_id === null).length
                    return res > 0 ? (res + ' (' + ((res * 100) / orders).toFixed(2) + '%)') : 0
                case 'efficiency':
                    willArrive = this.items.filter(l => l.status_id === 5 && l.operator_id !== null).length ?? 0
                    arrived = this.items.filter(l => l.status_id === 6 && l.operator_id !== null).length ?? 0
                    retries = this.items.filter(l => l.status_id === 8 && l.operator_id !== null).length ?? 0
                    news = this.items.filter(l => l.status_id === 1 && l.operator_id !== null).length ?? 0
                    no_answers = this.items.filter(l => l.status_id === 3 && l.operator_id !== null).length ?? 0
                    return (((((willArrive * 100) / orders) || 0) + (((arrived * 100) / orders) || 0)) / (this.items.length  - retries - news - no_answers )).toFixed(2) + '%'
                default:
            }
        },
        async fetchReport() {
            const query  = this.getData()
            try {
                const { data } = await this.$axios.post("report/operator", query)
                console.log(data)
            } catch (e) {
                this.$nuxt.$toast.error('Произошла ошибка при формирование отчёта' + e, {
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
        getData(){
            if (this.date_from !==null && this.date_to !==null && this.order_type !==null ){
                return {
                    type_id: this.order_type,
                    from: this.$moment(this.date_from, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss'),
                    to: this.$moment(this.date_to, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss')
                }
            }
            else if (this.date_from !==null && this.date_to !==null){
                return {
                    from: this.$moment(this.date_from, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss'),
                    to: this.$moment(this.date_to, 'DD.MM.YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss')
                }
            }
            else if (this.order_type !==null){
                return {
                    type_id: this.order_type
                }
            }
            else return {}
        }
    }
}

</script>

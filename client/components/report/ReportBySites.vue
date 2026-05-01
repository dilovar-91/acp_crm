<template>
    <v-data-table
        v-if="data"
        :headers="headers"
        :items="data"
        sort-by="calories"
        class="elevation-1"
        hide-default-footer
        dense
    >
        <template #top>
            <v-toolbar
                flat
            >
                <v-toolbar-title class="mr-3">
                    Отчеты по сайтам <span v-if="date_from && date_to"> с {{ date_from }} по {{ date_to }}</span>
                </v-toolbar-title>

                <v-spacer/>
            </v-toolbar>
        </template>
        <template #body>
            <tbody>
            <tr
                v-for="item in data"
                :key="item.id"
            >
                <td>{{ item.title }}</td>
                <td>{{ item.total }} {{ getValue(item.id) }}</td>
            </tr>
            <tr>
                <td>Всего</td>
                <td>{{ totalAmount }}</td>
            </tr>
            </tbody>
        </template>
        <template #no-data>
            Пусто
        </template>
    </v-data-table>
</template>
<script>
import sumBy from 'lodash.sumby'
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
        data: {
            type: Array,
            required: true
        },
    },
    data: () => ({
        headers: [
            {
                text: 'Сайт',
                align: 'start',
                sortable: false,
                value: 'title'
            },
            {text: 'Количество', value: 'total', sortable: true}
        ]
    }),
    computed: {
        totalAmount() {
            return sumBy(this.data, function (item) {
                return parseInt(item.total);
            });

        }

    },
    created() {
        this.$nuxt.$on('export-report', () => {
            console.log(123)
            this.export()
        })
    },
    methods: {
        getValue(id) {
            let result = 0
            const items = this.data
            const res = items.find(l => l.id === id)
            result = (' (' + ((parseInt(res.total) * 100) / this.totalAmount).toFixed(2) + '%)')
            return result
        },
        export() {


        }
    }

}

</script>

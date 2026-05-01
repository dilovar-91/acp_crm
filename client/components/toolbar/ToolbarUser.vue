<template>
    <v-menu offset-y left transition="slide-y-transition">
        <template #activator="{ on }">
            <v-btn icon class="elevation-2" v-on="on">
                <v-badge
                    color="success"
                    dot
                    bordered
                    offset-x="10"
                    offset-y="10"
                >
                    <v-avatar size="40">
                        <v-img src="/images/avatars/avatar1.svg"></v-img>
                    </v-avatar>
                </v-badge>
            </v-btn>
        </template>

        <!-- user menu list -->
        <v-list dense nav>
            <v-list-item
                link
            >{{user?.last_name}} {{user?.first_name}}</v-list-item>
            <v-list-item
                v-for="(item, index) in menu"
                :key="index"
                :to="item.link"
                :exact="item.exact"
                :disabled="item.disabled"
                link
            >
                <v-list-item-icon>
                    <v-icon small :class="{ 'grey--text': item.disabled }">{{ item.icon }}</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>{{ item.text }}</v-list-item-title>
                </v-list-item-content>
            </v-list-item>

            <v-divider class="my-1"></v-divider>

            <v-list-item @click="logout()">
                <v-list-item-icon>
                    <v-icon small>mdi-logout-variant</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Выйти</v-list-item-title>
                </v-list-item-content>
            </v-list-item>

          <v-list-item v-role-or-permission="'admin|clear_cache'" @click="clearCache()">
            <v-list-item-icon>
              <v-icon small>mdi-cached</v-icon>
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title>Очистить кэш</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list>
    </v-menu>
</template>

<script>
import config from '../../configs'
/*
|---------------------------------------------------------------------
| Toolbar User Component
|---------------------------------------------------------------------
|
| Quickmenu for user menu shortcuts on the toolbar
|
*/
export default {
    data() {
        return {
            menu: config.toolbar.user,
            user: this.$auth.user && this.$auth.user,
        }
    },
    methods: {
        async logout() {
            await this.$auth.logout()
            this.$router.push('/auth/signin')
        },
        async clearCache() {
            await this.$axios.post('/admin/clear-cache').then((response) => {
              this.$notify('success', 'Кэш успешно очищен!!!')
            })
              .catch((error) => {
                this.$notify('error', 'Произошла ошибка при очищение кэша: ' + error)
              })
        },
    }
}
</script>

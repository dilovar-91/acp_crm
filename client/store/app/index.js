import configs from '../../configs'
import mutations from './mutations'

const { product, time, theme, currencies  } = configs

const { globalTheme, menuTheme, toolbarTheme, isToolbarDetached, isContentBoxed, isRTL } = theme
const { currency, availableCurrencies } = currencies

// state initial values
const state = () => ({
  product,

  time,

  // currency
  currency,
  availableCurrencies,

  // themes and layout configurations
  globalTheme,
  menuTheme,
  toolbarTheme,
  isToolbarDetached,
  isContentBoxed,
  isRTL
})

export const actions = {
  async nuxtClientInit ({ commit, context, dispatch, state }, { req }) {
    const { data: permissions } = await this.$axios.get('/permissions');
    //await this.$axios.get('/sites');
    dispatch('showroom/fetchSites');
  },
}

export default {
  namespaced: true,
  state,
  mutations
}


// state
export const state = () => ({
  transits: [],
  used_transits: []
})

// getters
export const getters = {
  transits: state => state.transits,
  used_transits: state => state.used_transits
}

// mutations
export const mutations = {
  SET_TRANSITS (state, transits) {
    state.transits = transits
  },
  SET_USED_TRANSITS (state, transits) {
    state.used_transits = transits
  }
}

// actions
export const actions = {
  async fetchTransits ({ commit }, {showroom_id=null, created_between=null, mark_id=null }) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`/car/transits?${showroom_id > 0 ? 'filter[showroom]=' + showroom_id : ''}${created_between !==null ? '&filter[created_between]=' + created_between : ''}${mark_id > 0 ? '&filter[mark]=' + mark_id : ''}`)
        .then((response) => {
          commit('SET_TRANSITS', response.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  async fetchUsedTransits ({ commit }, {showroom_id=null, created_between=null, mark_id=null }) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`/used-car/transits?${showroom_id > 0 ? 'filter[showroom]=' + showroom_id : ''}${created_between !==null ? '&filter[created_between]=' + created_between : ''}${mark_id > 0 ? '&filter[mark]=' + mark_id : ''}`)
        .then((response) => {
          commit('SET_USED_TRANSITS', response.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  }
}

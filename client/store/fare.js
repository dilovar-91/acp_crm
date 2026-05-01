
// state
export const state = () => ({
  fares: [],
})

// getters
export const getters = {
  fares: state => state.fares,
}

// mutations
export const mutations = {
  SET_FARES (state, payload) {
    state.fares = payload
  },
}

// actions
export const actions = {
  async fetchFares ({ commit }, {showroom_id=null, created_between=null, mark_id=null }) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`/fares?${showroom_id > 0 ? 'filter[showroom_id]=' + showroom_id : ''}${created_between !==null ? '&filter[created_between]=' + created_between : ''}${mark_id > 0 ? '&filter[mark]=' + mark_id : ''}`)
        .then((response) => {
          commit('SET_FARES', response.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  async save ({ dispatch }, { item }) {
    return new Promise((resolve, reject) => {
      this.$axios.post(`/fares/save`, {item})
        .then((response) => {
          if (response.data.id) {
            dispatch('fetchFares', {showroom_id: item.showroom_id})
            resolve(response)
          }
        })
        .catch((error) => { reject(error) })
    })
  },

  delete ({ commit, dispatch }, {item}) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/fares/delete' + {id: item.id})
        .then((response) => {
          dispatch('fetchFares', {showroom_id: item.showroom_id})
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
}

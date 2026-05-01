// state
export const state = () => ({
  arrivals: [],
  all_arrivals: [],
})

// getters
export const getters = {
  arrivals: state => state.arrivals,
  all_arrivals: state => state.all_arrivals,
}

// mutations
export const mutations = {
  SET_ARRIVALS (state, arrivals) {
    state.arrivals = arrivals
  },
  SET_ALL_ARRIVALS (state, payload) {
    state.all_arrivals = payload
  }
}

// actions
export const actions = {
  fetchArrivals ({ commit }, { id, from = this.$moment().format('YYYY-MM-DD'), to = this.$moment().format('YYYY-MM-DD'), site_id = null }) {
    return new Promise((resolve, reject) => {
      // const url = (from !== null && to !== null) ? 'arrivals?' + id + '/' + from + '/' + to : 'arrivals/' + id
      const url = `/arrivals?${id ? 'filter[showroom_id]='+id  : ''}${(from && to) ? '&filter[between]=' + from + ',' + to : ''}${site_id ? '&filter[site_id]='+site_id  : ''}`
      console.log(url)
      this.$axios.get(url)
        .then((response) => {
          commit('SET_ARRIVALS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },

  fetchAllArrivals ({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios.get('arrivals/all')
        .then((response) => {
          commit('SET_ALL_ARRIVALS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  saveArrival ({ dispatch }, data) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/arrivals/save', { item: data.item })
        .then((response) => {
          if (response.data.id) {
            // dispatch('fetchArrivals', { id: data.item.showroom_id })
            resolve(response)
          }
        })
        .catch((error) => {
          reject(error)
        })
    })
  }
}

// state
export const state = () => ({
  cars: [],
  sold_cars: [],
  filtered: [],
  transits: [],
  statuses: [],
  histories: [],
  price_coefficient: null,

  old_coefficient: null
})

// getters
export const getters = {
  cars: state => state.cars,
  sold_cars: state => state.sold_cars,
  filtered: state => state.filtered,
  transits: state => state.transits,
  statuses: state => state.statuses,
  histories: state => state.histories
}

// mutations
export const mutations = {
  SET_CARS (state, payload) {
    state.cars = payload
  },
  SET_SOLD_CARS (state, payload) {
    state.sold_cars = payload
  },
  SET_FILTERED (state, filtered) {
    state.filtered = filtered
  },
  SET_COEFFICIENT (state, payload) {
    state.price_coefficient = payload
  },
  SET_OLD_COEFFICIENT (state, payload) {
    state.old_coefficient = payload
  },
  SET_HISTORIES(state, payload) {
    state.histories = payload
  },
}

// actions
export const actions = {
  fetchCars({commit}, {
    showroom_id = null,
    mark_id = null,
    model_id = null,
    bodytype_id = null,
    transmission_id = null,
    enginetype_id = null,
    is_sold = null,
    withTrashed = false
  }) {
    return new Promise((resolve, reject) => {
      const query = `/used-cars?${withTrashed ? 'filter[withTrashed]=true' : ''}${showroom_id !== null ? '&filter[showroom_id]=' + showroom_id : ''}${mark_id !== null ? '&filter[mark_id]=' + mark_id : ''}${model_id !== null ? '&filter[model_id]=' + model_id : ''}${bodytype_id !== null ? '&filter[bodytype_id]=' + bodytype_id : ''}${transmission_id !== null ? '&filter[transmission_id]=' + transmission_id : ''}${transmission_id !== null ? '&filter[enginetype_id]=' + enginetype_id : ''}${is_sold !==null ? '&filter[is_sold]=' + is_sold : ''}`
      this.$axios.get(query)
        .then((response) => {
          commit('SET_CARS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchSoldCars ({ commit }, payload) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/used-cars/sold')
        .then((response) => {
          commit('SET_SOLD_CARS', response.data)
          commit('SET_FILTERED', response.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  fetchFilter ({ commit, state, dispatch }, { id }) {
    if (id === undefined) {
      commit('SET_FILTERED', state.cars)
      return
    }
    if (!state.cars.length) {
      dispatch('fetchCars')
    } else {
      const result = state.cars.filter(l => l.showroom_id === Number(id))
      if (result) {
        commit('SET_FILTERED', result)
      } else {
        commit('SET_FILTERED', state.cars)
      }
    }
  },
  save ({ commit, dispatch }, data) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/used-car/save', { item: data.item })
        .then((response) => {
          if (response.data.id) {
            dispatch('fetchCars', {})
            resolve(response)
          }
        })
        .catch((error) => { reject(error) })
    })
  },
  ready ({ commit, dispatch }, { id, isReady, diagnostic_before, diagnostic_after, dry_cleaning, polish, painting, comment }) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/used-car/ready', { id, isReady, diagnostic_before, diagnostic_after, dry_cleaning, polish, painting, comment })
        .then((response) => {
          if (response.data.id) {
            dispatch('fetchCars')
            resolve(response)
          }
        })
        .catch((error) => { reject(error) })
    })
  },
  saveStorage ({ commit, dispatch }, data) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/used-car/edit', { item: data.item })
        .then((response) => {
          if (response.data.id) {
            dispatch('fetchCars')
            resolve(response)
          }
        })
        .catch((error) => { reject(error) })
    })
  },
  fetchCoefficient ({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/setting')
        .then((response) => {
          commit('SET_COEFFICIENT', response.data && response.data['usedcar-coefficient'])
          commit('SET_OLD_COEFFICIENT', response.data && response.data['usedcar-coefficient'])
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  saveCoefficient ({ commit, dispatch }, data) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/setting/save', data)
        .then((response) => {
          if (response.data.id) {
            commit('SET_COEFFICIENT', response.data['usedcar-coefficient'])
            commit('SET_OLD_COEFFICIENT', response.data && response.data['usedcar-coefficient'])
            resolve(response)
          }
        })
        .catch((error) => { reject(error) })
    })
  },
  delete ({ commit, dispatch }, {data}) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/used-car/delete' + {id: data.id})
        .then((response) => {
          dispatch('fetchCars', {})
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  deleteFiltered ({ commit, dispatch }, { id, showroom_id }) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/used-car/delete/' + id)
        .then((response) => {
          dispatch('fetchCars')
          dispatch('fetchFilter', { showroom_id })
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  transit ({ commit, dispatch }, { item }) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/used-car/transit', { item })
        .then((response) => {
          dispatch('fetchCars', {})
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  approveTransit ({ commit, dispatch }, { item }) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/used-car/transit/approve', { item })
        .then((response) => {
          dispatch('fetchCars')
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  cancelTransit ({ commit, dispatch }, data) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/used-car/transit/cancel', { item: data.item })
        .then((response) => {
          dispatch('fetchCars')
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },

  async fetchTransits ({ commit, state, dispatch }) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/used-car/transits')
        .then((response) => {
          commit('SET_TRANSITS', response.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  fetchHistories({commit}, {
    id
  }) {
    return new Promise((resolve, reject) => {
      let query = `/used-car/histories`
      this.$axios.post(query, {id})
        .then((response) => {
          commit('SET_HISTORIES', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
}

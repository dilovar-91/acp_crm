// state
export const state = () => ({
  cars: [],
  sold_cars: [],
  transits: [],
})

// getters
export const getters = {
  cars: state => state.cars,
  sold_cars: state => state.sold_cars,
  transits: state => state.transits,
}

// mutations
export const mutations = {
  SET_CARS(state, cars) {
    state.cars = cars
  },
  SET_SOLD_CARS(state, payload) {
    state.sold_cars = payload
  },
  SET_TRANSITS(state, transits) {
    state.transits = transits
  }
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
    is_sold = 0,
    withTrashed = false
  }) {
    return new Promise((resolve, reject) => {
      const query = `/cars?${withTrashed ? 'filter[withTrashed]=true' : ''}${showroom_id !== null ? '&filter[showroom_id]=' + showroom_id : ''}${mark_id !== null ? '&filter[mark_id]=' + mark_id : ''}${model_id !== null ? '&filter[model_id]=' + model_id : ''}${bodytype_id !== null ? '&filter[bodytype_id]=' + bodytype_id : ''}${transmission_id !== null ? '&filter[transmission_id]=' + transmission_id : ''}${transmission_id !== null ? '&filter[enginetype_id]=' + enginetype_id : ''}${is_sold === 1 ? '&filter[is_sold]=' + is_sold : ''}`
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

  fetchSoldCars({commit}) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/car/sold')
        .then((response) => {
          commit('SET_SOLD_CARS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  save({commit, dispatch}, {item, showroom_id=null}) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/car/save', {item})
        .then((response) => {
          if (response.data.id) {
            dispatch('fetchCars', {showroom_id})
            resolve(response)
          }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  delete({commit, dispatch}, {id}) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/car/delete/' + id)
        .then((response) => {
          dispatch('fetchCars')
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  addCar({commit}, car) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/car/add', {car})
        .then((response) => {
          commit('ADD_CAR', Object.assign(car, {id: response.data.id}))
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },


  transit({commit, dispatch}, data) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/car/transit', {item: data.item})
        .then((response) => {
          dispatch('fetchCars')
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  approveTransit({commit, dispatch}, data) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/car/transit/approve', {item: data.item})
        .then((response) => {
          dispatch('fetchCars')
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  cancelTransit({commit, dispatch}, data) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/car/transit/cancel', {item: data.item})
        .then((response) => {
          dispatch('fetchCars')
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  async fetchTransits({commit}) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`/car/transits`)
        .then((response) => {
          commit('SET_TRANSITS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
}

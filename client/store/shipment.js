// state
export const state = () => ({
  shipment: {},
  shipments: [],
  drivers: [],
  companies: [],
  statuses: [],
})

// getters
export const getters = {
  shipment: state => state.shipment,
  shipments: state => state.shipments,
  drivers: state => state.drivers,
  companies: state => state.companies,
  statuses: state => state.statuses,
}

// mutations
export const mutations = {
  SET_SHIPMENTS(state, payload) {
    state.shipments = payload
  },
  SET_DRIVERS(state, payload) {
    state.drivers = payload
  },
  SET_COMPANIES(state, payload) {
    state.companies = payload
  },
  SET_STATUSES(state, payload) {
    state.statuses = payload
  },
  SET_SHIPMENT(state, payload) {
    state.shipment = payload
  }
}

// actions
export const actions = {
  async fetchShipments({commit}, {showroom_id = null, created_between = null, mark_id = null}) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`/car/shipments?${showroom_id > 0 ? 'filter[showroom]=' + showroom_id : ''}${created_between !== null ? '&filter[created_between]=' + created_between : ''}${mark_id > 0 ? '&filter[mark]=' + mark_id : ''}`)
        .then((response) => {
          commit('SET_SHIPMENTS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  async fetchCompanies({commit}) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`/shipment/companies`)
        .then((response) => {
          commit('SET_COMPANIES', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  async fetchDrivers({commit}) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`/shipment/drivers`)
        .then((response) => {
          commit('SET_DRIVERS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  async fetchShipment({commit}, {id}) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`/shipment/detail/${id}`)
        .then((response) => {
          commit('SET_SHIPMENT', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  async fetchStatuses({commit}) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`/shipment/statuses`)
        .then((response) => {
          commit('SET_STATUSES', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  save({commit, dispatch}, {item}) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/shipment/save', {item})
        .then((response) => {
          if (response.data.id) {
            dispatch('fetchShipments', {})
            resolve(response)
          }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  addCar({commit, dispatch}, {item}) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/shipment/car/add', {item})
        .then((response) => {
          if (response.data.id) {
            dispatch('fetchShipments', {})
            resolve(response)
          }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  addUsedCar({commit, dispatch}, {item}) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/shipment/used-car/add', {item})
        .then((response) => {
          if (response.data.id) {
            dispatch('fetchShipments', {})
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
      this.$axios.get('/shipment/delete/' + id)
        .then((response) => {
          dispatch('fetchShipments', {})
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
}

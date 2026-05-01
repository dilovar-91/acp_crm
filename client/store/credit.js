// state
export const state = () => ({
  credit_requests: [],
  sales: [],
  banks: [],
  preparers: []
})

// getters
export const getters = {
  credit_requests: state => state.credit_requests,
  sales: state => state.sales,
  credit_jumps: state => state.credit_jumps,
  banks: state => state.banks,
  preparers: state => state.preparers
}

// mutations
export const mutations = {
  SET_CREDIT_REQUESTS (state, payload) {
    state.credit_requests = [...payload.slice()]
  },
  SET_SALES (state, payload) {
    state.sales = [...payload.slice()]
  },
  SET_BANKS (state, payload) {
    state.banks = [...payload.slice()]
  },
  SET_PREPARERS (state, payload) {
    state.preparers = [...payload.slice()]
  }
}

// actions
export const actions = {
  fetchCreditRequests ({ commit }, { id, from = this.$moment().startOf('month').format('YYYY-MM-DD'), to =this.$moment().format('YYYY-MM-DD'), search = '', trashed = false }) {
    return new Promise((resolve, reject) => {
      const url = `credit-requests?${id !== '' ? 'filter[showroom_id]=' + id : ''}${search !== '' ? '&filter[search]=' + search : ''}${from !== null && to !== null  ? '&filter[between]=' + from + ',' + to: ''}${trashed !== false ? '&filter[withTrashed]=' + trashed : ''}`
      console.log(url)
      this.$axios.get(url)
        .then((response) => {
          commit('SET_CREDIT_REQUESTS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchSales ({ commit }, { id, from = null, to = null, search = '', trashed = true }) {

    return new Promise((resolve, reject) => {
      const url = `sales?${id !== null ? 'filter[showroom_id]=' + id : ''}${search !== '' ? 'filter[search]=' + search : ''}${from !== null && to !== null  ? '&filter[between]=' + from + ',' + to: ''}${trashed !== false ? '&filter[withTrashed]=' + trashed : ''}`

      this.$axios.get(url)
        .then((response) => {
          commit('SET_SALES', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchBanks ({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios.get('banks')
        .then((response) => {
          commit('SET_BANKS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchPreparers ({ commit }, { id }) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`preparers${id ? '?filter[showroom_id]=' + id: ''}`)
        .then((response) => {
          commit('SET_PREPARERS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  saveCreditRequest ({ dispatch }, data) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/credit-requests/save', { item: data.item })
        .then((response) => {
          if (response.data.id) {
            // dispatch('fetchCreditRequests', { id: data.item.showroom_id })
            resolve(response)
          }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  restoreCreditRequest ({ dispatch }, { id, showroom_id }) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/credit-requests/restore', { id })
        .then((response) => {
          dispatch('fetchCreditRequests', { id: showroom_id })
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  saleCreditRequest ({ dispatch }, data) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/credit-requests/sale', { item: data.item })
        .then((response) => {
          console.log(response)
          if (response.data.id) {
            console.log(data)
            dispatch('fetchSales', { id: data.item.showroom_id, trash: true })
            resolve(response)
          }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  deleteCreditRequest ({ dispatch }, { item }) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/credit-requests/delete', { item })
        .then((response) => {
          dispatch('fetchCreditRequests', { id: item.showroom_id })
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  deleteTrashCreditRequest ({ dispatch }, {id, showroom_id}) {
    return new Promise((resolve, reject) => {

      this.$axios.post('/credit-requests/delete/trash' , {id})
        .then((response) => {
          // dispatch('fetchCreditRequests', {id:showroom_id})
          dispatch('fetchSales', {id:showroom_id})
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  sendApprovedNotify ({ dispatch }, data) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/credit-requests/send-notify', { item: data.item })
        .then((response) => {
          if (response.data.id) {
            dispatch('fetchExpenses')
            resolve(response)
          }
        })
        .catch((error) => {
          reject(error)
        })
    })
  }
}

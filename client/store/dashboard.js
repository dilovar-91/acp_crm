// state
export const state = () => ({
  cars: [],
  used_cars: [],
  expenses: [],
  credit_requests: [],
  arrivals: []
})

// getters
export const getters = {
  cars: state => state.cars,
  used_cars: state => state.used_cars,
  expenses: state => state.expenses,
  credit_requests: state => state.credit_requests,
  arrivals: state => state.arrivals

}

// mutations
export const mutations = {
  SET_CARS (state, payload) {
    state.cars = payload
  },
  SET_USED_CARS (state, payload) {
    state.used_cars = payload
  },
  SET_EXPENSES (state, payload) {
    state.expenses = payload
  },
  SET_CREDIT_REQUESTS (state, payload) {
    state.credit_requests = payload
  },
  SET_ARRIVALS (state, arrivals) {
    state.arrivals = arrivals
  }
}

// actions
export const actions = {
  fetchCars ({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/dashboard/cars')
        .then((response) => {
          commit('SET_CARS', response.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  fetchUsedCars ({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/dashboard/used-cars')
        .then((response) => {
          commit('SET_USED_CARS', response.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  fetchExpenses ({ commit }, { dateFrom = null, dateTo = null }) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/dashboard/expenses', { dateFrom, dateTo })
        .then((response) => {
          commit('SET_EXPENSES', response.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  fetchArrivals ({ commit }, { dateFrom = this.$moment().format('YYYY-MM-DD') , dateTo =  this.$moment().format('YYYY-MM-DD') }) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/dashboard/arrivals?filter[between]=' +  dateFrom + ',' + dateTo)
        .then((response) => {
          commit('SET_ARRIVALS', response.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  fetchOrders ({ commit }, { from = this.$moment().format('YYYY-MM-DD') , to =  this.$moment().format('YYYY-MM-DD') }) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/dashboard/orders' ,   {from, to})
        .then((response) => {
          commit('SET_ARRIVALS', response.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  fetchCreditRequests ({ commit }, { dateFrom = this.$moment().format('YYYY-MM-DD') , dateTo =  this.$moment().format('YYYY-MM-DD') }) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/dashboard/credits?filter[between]=' +  dateFrom + ',' + dateTo)
        .then((response) => {
          commit('SET_CREDIT_REQUESTS', response.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  }

}

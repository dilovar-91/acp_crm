// state
export const state = () => ({
  marks: [],
  models: [],
  tradein_models: [],
  all_models: [],
  fact_models: [],
  enginetypes: [],
  transmissions: [],
  bodytypes: [],
  colors: [],
  wheeltypes: [],
  complectations: [],
  statuses: [],
})

// getters
export const getters = {
  marks: state => state.marks,
  models: state => state.models,
  tradein_models: state => state.tradein_models,
  all_models: state => state.all_models,
  fact_models: state => state.fact_models,
  enginetypes: state => state.enginetypes,
  transmissions: state => state.transmissions,
  bodytypes: state => state.bodytypes,
  colors: state => state.colors,
  wheeltypes: state => state.wheeltypes,
  complectations: state => state.complectations,
  statuses: state => state.statuses,
}

// mutations
export const mutations = {

  SET_MARKS(state, payload) {
    state.marks = payload
  },
  ADD_MARK(state, payload) {
    state.marks.unshift(payload)
  },
  SET_MODELS(state, payload) {
    state.models = payload
  },
  SET_TRADEIN_MODELS(state, payload) {
    state.tradein_models = payload
  },
  SET_ALL_MODELS(state, payload) {
    state.all_models = payload
  },
  EMPTY_MODELS(state) {
    state.models = []
  },
  SET_FACT_MODELS(state, payload) {
    state.fact_models = payload
  },
  EMPTY_FACT_MODELS(state) {
    state.fact_models = []
  },
  ADD_MODEL(state, payload) {
    state.models.unshift(payload)
  },
  SET_BODYTYPES(state, payload) {
    state.bodytypes = payload
  },
  SET_ENGINETYPES(state, payload) {
    state.enginetypes = payload
  },
  SET_TRANSMISSIONS(state, payload) {
    state.transmissions = payload
  },
  SET_COLORS(state, payload) {
    state.colors = payload
  },
  SET_WHEELTYPES(state, payload) {
    state.wheeltypes = payload
  },
  SET_COMPLECTATIONS(state, payload) {
    state.complectations = payload
  },
  SET_STATUSES(state, payload) {
    state.statuses = payload
  }
}

// actions
export const actions = {
  fetchMarks({commit}) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/marks')
        .then((response) => {
          commit('SET_MARKS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchModels({commit}, {markId}) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/models/' + markId)
        .then((response) => {
          commit('SET_MODELS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchTradeInModels({commit}, {markId}) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/models/' + markId)
        .then((response) => {
          commit('SET_TRADEIN_MODELS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchAllModels({commit}) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/models')
        .then((response) => {
          commit('SET_ALL_MODELS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchFactModels({commit}, {markId}) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/models/' + markId)
        .then((response) => {
          commit('SET_FACT_MODELS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchComplectations({commit}) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/complectations')
        .then((response) => {
          commit('SET_COMPLECTATIONS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  emptyModels({commit}) {
    commit('EMPTY_MODELS')
  },
  emptyFactModels({commit}) {
    commit('EMPTY_FACT_MODELS')
  },

  fetchBodyTypes({commit}, payload) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/bodytypes')
        .then((response) => {
          commit('SET_BODYTYPES', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchEngineTypes({commit}, payload) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/enginetypes')
        .then((response) => {
          commit('SET_ENGINETYPES', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchColors({commit}, payload) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/colors')
        .then((response) => {
          commit('SET_COLORS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchWheelTypes({commit}, payload) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/wheeltypes')
        .then((response) => {
          commit('SET_WHEELTYPES', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchTransmissions({commit}, payload) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/transmissions')
        .then((response) => {
          commit('SET_TRANSMISSIONS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  async fetchStatuses({commit}) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/statuses')
        .then((response) => {
          commit('SET_STATUSES', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  }
}

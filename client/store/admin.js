// state
export const state = () => ({
  showrooms: [],
  managers: [],
  managers2: [],
  operators: [],
  sites: [],
  marks: [],
  all_models: [],
  models: []
})

// getters
export const getters = {
  showrooms: state => state.showrooms,
  managers: state => state.managers,
  managers2: state => state.managers2,
  operators: state => state.operators,
  sites: state => state.sites,
  marks: state => state.marks,
  models: state => state.models,
  all_models: state => state.all_models,
  regions: state => state.regions,
}

// mutations
export const mutations = {
  SET_MANAGERS (state, managers) {
    state.managers = managers
  },
  SET_MANAGERS2 (state, payload) {
    state.managers2 = payload
  },
  SET_OPERATORS (state, operators) {
    state.operators = operators
  },
  SET_SHOWROOMS (state, showrooms) {
    state.showrooms = showrooms
  },
  SET_SITES (state, sites) {
    state.sites = sites
  },
  SET_REGIONS (state, regions) {
    state.regions = regions
  },
  SET_MARKS (state, payload) {
    state.marks = payload
  },
  SET_MODELS (state, payload) {
    state.models = payload
  },
}
// actions
export const actions = {
  fetchOperators ({ commit }, {showroom_id=null}) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`/admin/operators${showroom_id ? '?filter[showroom_id]=' + showroom_id : ''}`)
        .then((response) => {
          commit('SET_OPERATORS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchShowrooms ({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/admin/showrooms')
        .then((response) => {
          commit('SET_SHOWROOMS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchSites ({ commit }, {id}) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`/admin/sites${id ? '?filter[showroom_id]=' + id: ''}`)
        .then((response) => {
          commit('SET_SITES', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchMarks ({ commit }, {id}) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`/admin/marks${id ? '?filter[showroom_id]=' + id: ''}`)
        .then((response) => {
          commit('SET_MARKS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchModels ({ commit }, {id}) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`/admin/models${id ? '?filter[brand_id]=' + id: ''}`)
        .then((response) => {
          commit('SET_MODELS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },


  saveMark ({ dispatch }, data) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/admin/marks/save', { item: data.item })
        .then((response) => {
          if (response.data.id) {
            dispatch('fetchMarks',{})
            resolve(response)
          }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },


  updateMark ({ dispatch }, data) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/admin/marks/update', { item: data.item })
        .then((response) => {
          if (response.data.id) {
            dispatch('fetchMarks',{})
            resolve(response)
          }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },


  deleteMark ({ dispatch }, {id}) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/admin/marks/delete', { id })
        .then((response) => {
          if (response.data.id) {
            dispatch('fetchMarks',{})
            resolve(response)
          }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },


  saveModel ({ dispatch }, data) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/admin/models/save', { item: data.item })
        .then((response) => {
          if (response.data.id) {
            dispatch('fetchModels',{})
            resolve(response)
          }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },


  updateModel ({ dispatch }, data) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/admin/models/update', { item: data.item })
        .then((response) => {
          if (response.data.id) {
            dispatch('fetchModels',{})
            resolve(response)
          }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },


  deleteModel ({ dispatch }, {id}) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/admin/models/delete', { id })
        .then((response) => {
          if (response.data.id) {
            dispatch('fetchModels',{})
            resolve(response)
          }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },

  fetchRegions ({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/admin/regions')
        .then((response) => {
          commit('SET_REGIONS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
}

// state
export const state = () => ({
  showrooms: [],
  showroom: {},
  managers: [],
  managers2: [],
  operators: [],
  sites: [],
  competitors: [],
  arrivals: [],
  all_arrivals: [],
  regions: [],
  keys: [],
  reports: [],
  car_report: [],
  model_report: [],
  region_report: [],
  agencies: [],
  admin_sites: [],
  site_types: [],
  actual_projects: [],
  blacklists: [],
})

// getters
export const getters = {
  showroom: (state) => state.showroom,
  showrooms: (state) => state.showrooms,
  managers: (state) => state.managers,
  managers2: (state) => state.managers2,
  operators: (state) => state.operators,
  sites: (state) => state.sites,
  arrivals: (state) => state.arrivals,
  all_arrivals: (state) => state.all_arrivals,
  regions: (state) => state.regions,
  keys: (state) => state.keys,
  reports: (state) => state.reports,
  agencies: (state) => state.agencies,
  admin_sites: (state) => state.agencies,
  site_types: (state) => state.site_types,
  actual_projects: (state) => state.site_types,
  blacklists: (state) => state.blacklists,
}

// mutations
export const mutations = {
  SET_MANAGERS(state, managers) {
    state.managers = managers
  },
  SET_MANAGERS2(state, payload) {
    state.managers2 = payload
  },
  SET_OPERATORS(state, operators) {
    state.operators = operators
  },
  SET_SHOWROOM(state, showroom) {
    state.showroom = showroom
  },
  SET_SHOWROOMS(state, showrooms) {
    state.showrooms = showrooms
  },
  SET_SITES(state, sites) {
    state.sites = sites
  },
  SET_ADMIN_SITES(state, sites) {
    state.admin_sites = sites
  },
  SET_SITE_TYPES(state, payload) {
    state.site_types = payload
  },
  SET_COMPETITORS(state, competitors) {
    state.competitors = competitors
  },
  SET_ARRIVALS(state, arrivals) {
    state.arrivals = arrivals
  },
  SET_ALL_ARRIVALS(state, payload) {
    state.all_arrivals = payload
  },
  SET_REGIONS(state, regions) {
    state.regions = regions
  },
  SET_AGENCIES(state, payload) {
    state.agencies = payload
  },
  SET_ACTUAL_PROJECTS(state, payload) {
    state.actual_projects = payload
  },
  SET_BLACKLISTS(state, payload) {
    state.blacklists = payload
  },
}
// actions
export const actions = {
  fetchManagers({ commit }, { showroom_id = null }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .get(
          `/managers${showroom_id ? '?filter[showroom_id]=' + showroom_id : ''}`
        )
        .then((response) => {
          commit('SET_MANAGERS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchSecondManagers({ commit }, { showroom_id = null }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .get(
          `/managers2${
            showroom_id ? '?filter[showroom_id]=' + showroom_id : ''
          }`
        )
        .then((response) => {
          commit('SET_MANAGERS2', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  // eslint-disable-next-line camelcase
  fetchOperators({ commit }, { showroom_id = null, orderWorkers = true }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .get(
          `/operators?${orderWorkers ? 'filter[OrderWorkers]=true' : ''}${
            showroom_id ? '&filter[showroom_id]=' + showroom_id : ''
          }`
        )
        .then((response) => {
          commit('SET_OPERATORS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchShowroom({ commit }, { id }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .get('/showroom/' + id)
        .then((response) => {
          commit('SET_SHOWROOM', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchShowrooms({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .get('/showrooms')
        .then((response) => {
          commit('SET_SHOWROOMS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchSites({ commit }, { id }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .get(`/sites${id ? '?filter[showroom_id]=' + id : ''}`)
        .then((response) => {
          commit('SET_SITES', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchAdminSites({ commit }, { id }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .get(`/admin-sites${id ? '?filter[showroom_id]=' + id : ''}`)
        .then((response) => {
          commit('SET_ADMIN_SITES', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchActualProjects({ commit }, { id }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .get(`/reports/projects${id ? '?filter[showroom_id]=' + id : ''}`)
        .then((response) => {
          commit('SET_ACTUAL_PROJECTS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchSiteTypes({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .get(`/site-types`)
        .then((response) => {
          commit('SET_SITE_TYPES', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchAgencies({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .get(`/agencies`)
        .then((response) => {
          console.log(5555)
          commit('SET_AGENCIES', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchCompetitors({ commit }, { id }) {
    let endpoint = null
    if (id) {
      endpoint = '/competitors/' + id
    } else {
      endpoint = '/competitors'
    }
    return new Promise((resolve, reject) => {
      this.$axios
        .get(endpoint)
        .then((response) => {
          commit('SET_COMPETITORS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },

  fetchAllArrivals({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .get('arrivals/all')
        .then((response) => {
          commit('SET_ALL_ARRIVALS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  saveSite({ dispatch }, data) {
    return new Promise((resolve, reject) => {
      this.$axios
        .post('/site/save', { item: data.item })
        .then((response) => {
          if (response.data.id) {
            dispatch('fetchAdminSites', {})
            resolve(response)
          }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  saveProject({ dispatch }, data) {
    return new Promise((resolve, reject) => {
      this.$axios
        .post('/reports/projects/save', { item: data.item })
        .then((response) => {
          if (response.data.id) {
            dispatch('fetchActualProjects', {})
            resolve(response)
          }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  updateSite({ dispatch }, data) {
    return new Promise((resolve, reject) => {
      this.$axios
        .post('/site/update', { item: data.item })
        .then((response) => {
          if (response.data.id) {
            dispatch('fetchAdminSites', {})
            resolve(response)
          }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  duplicateSite({ dispatch }, { id }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .post('/site/duplicate', { id })
        .then((response) => {
          if (response.data.id) {
            dispatch('fetchAdminSites', {})
            resolve(response)
          }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  saveOperator({ dispatch }, { item }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .post('/operator/save', { item })
        .then((response) => {
          if (response.data.id) {
            dispatch('fetchOperators', { showroom_id: item.showroom_id })
            resolve(response)
          }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  deleteSite({ dispatch }, { id }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .post('/site/delete', { id })
        .then((response) => {
          dispatch('fetchSites', {})
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  deleteAdminSite({ dispatch }, { id }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .post('/site/delete', { id })
        .then((response) => {
          dispatch('fetchAdminSites', {})
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  deleteOperator({ dispatch }, { id }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .post('/operator/delete', { id })
        .then((response) => {
          dispatch('fetchOperators')
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchRegions({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .get('/regions')
        .then((response) => {
          commit('SET_REGIONS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchBlacklists({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .get('/blacklists')
        .then((response) => {
          commit('SET_BLACKLISTS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },

  saveBlacklist({ dispatch }, data) {
    return this.$axios
      .post('/blacklists/save', { item: data.item })
      .then((response) => {
        // success
        dispatch('fetchBlacklists', {})
        return {
          success: true,
          message: 'Номер добавлен!',
          data: response.data,
        }
      })
      .catch((error) => {
        // error from Laravel
        // eslint-disable-next-line prefer-promise-reject-errors
        return Promise.reject({
          success: false,
          message:
            error.response?.data?.error || 'Ошибка при добавлении номера',
          status: error.response?.status,
        })
      })
  },
  updateBlacklist({ dispatch }, data) {
    return this.$axios
      .post('/blacklists/update', { item: data.item })
      .then((response) => {
        dispatch('fetchBlacklists', {})
        return {
          success: true,
          message: 'Номер изменён!',
          data: response.data,
        }
      })
      .catch((error) => {
        // eslint-disable-next-line prefer-promise-reject-errors
        return Promise.reject({
          success: false,
          message: error.response?.data?.error || 'Ошибка при изменении номера',
          status: error.response?.status,
        })
      })
  },
  deleteBlacklist({ dispatch }, { id }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .post('/blacklists/delete', { id })
        .then((response) => {
          dispatch('fetchBlacklists', {})
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
}

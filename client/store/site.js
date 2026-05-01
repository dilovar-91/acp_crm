// state
export const state = () => ({
  project_managers: [],
  project_statuses: [],
  project_types: [],
  project_systems: [],
  actual_projects: [],
})

// getters
export const getters = {
  project_managers: state => state.project_managers,
  project_statuses: state => state.project_statuses,
  project_types: state => state.project_types,
  actual_projects: state => state.actual_projects,
  project_systems: state => state.project_systems,
}

// mutations
export const mutations = {
  SET_PROJECT_MANAGERS (state, managers) {
    state.project_managers = managers
  },
  SET_PROJECT_TYPES (state, payload) {
    state.project_types = payload
  },
  SET_PROJECT_SYSTEMS (state, payload) {
    state.project_systems = payload
  },
  SET_PROJECT_STATUSES (state, payload) {
    state.project_statuses = payload
  },
  SET_ACTUAL_PROJECTS (state, payload) {
    state.actual_projects = payload
  },
}
// actions
export const actions = {
  fetchManagers ({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`/project/managers`)
        .then((response) => {
          commit('SET_PROJECT_MANAGERS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchStatuses ({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`/project/statuses`)
        .then((response) => {
          commit('SET_PROJECT_STATUSES', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchTypes ({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`/project/types`)
        .then((response) => {
          commit('SET_PROJECT_TYPES', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchSystems ({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`/project/systems`)
        .then((response) => {
          commit('SET_PROJECT_SYSTEMS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchActualProjects ({ commit }, {showroom=null, site=null, manager=null, type=null, status=null, system=null}) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`/projects?${showroom ? 'filter[showroom_id]=' + showroom: ''}${site ? '&filter[site_id]=' + site: ''}${manager ? '&filter[manager_id]=' + manager: ''}${type ? '&filter[type_id]=' + type: ''}${status ? '&filter[status_id]=' + status: ''}${system ? '&filter[system_id]=' + system: ''}`)
        .then((response) => {
          commit('SET_ACTUAL_PROJECTS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  saveProject ({ dispatch }, data) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/projects/save', { item: data.item })
        .then((response) => {
          if (response.data.id) {
            dispatch('fetchActualProjects',{})
            resolve(response)
          }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  deleteProject ({ dispatch }, { id }) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/projects/delete/', {id})
        .then((response) => {
          dispatch('fetchActualProjects')
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  }
}

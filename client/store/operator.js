// state
export const state = () => ({
  operators: [],
  schedules: [],
  programmatic_schedules: [],
  work_places: [],
  roles: []
})

// getters
export const getters = {
  operators: state => state.operators,
  schedules: state => state.schedules,
  programmatic_schedules: state => state.programmatic_schedules,
  work_places: state => state.work_places,
  roles: state => state.roles,
}

// mutations
export const mutations = {

  SET_OPERATORS(state, operators) {
    state.operators = operators
  },

  SET_OPERATOR_ROLES(state, payload) {
    state.roles = payload
  },
  SET_SCHEDULES(state, payload) {
    state.schedules = payload
  },
  SET_PROGRAMMATIC_SCHEDULES(state, payload) {
    state.programmatic_schedules = payload
  },
  SET_WORK_PLACES(state, payload) {
    state.work_places = payload
  },
}
// actions
export const actions = {
  fetchOperators({commit}, {showroom_id = null}) {
    return new Promise((resolve, reject) => {
      console.log(showroom_id)
      this.$axios.get(`/operators${showroom_id ? '?filter[showroom_id]=' + showroom_id : ''}`)
        .then((response) => {
          commit('SET_OPERATORS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchOperatorRoles({commit}) {
    return new Promise((resolve, reject) => {

      this.$axios.get(`/operator/roles`)
        .then((response) => {
          commit('SET_OPERATOR_ROLES', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  updateSchedule({dispatch}, {item}) {
    return new Promise((resolve, reject) => {
      this.$axios.post(`/schedules/update`, {item})
        .then((response) => {
          dispatch('fetchSchedules', {showroom_id: item.showroom_id})
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  updateProgrammaticSchedule({dispatch}, {item}) {
    return new Promise((resolve, reject) => {
      this.$axios.post(`/programmatic-schedules/update`, {item})
        .then((response) => {
          dispatch('fetchProgrammaticSchedules', {showroom_id: item.showroom_id})
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  createSchedule({dispatch}, {item}) {
    return new Promise((resolve, reject) => {
      this.$axios.post(`/schedules/create`, {item})
        .then((response) => {
          dispatch('fetchSchedules', {showroom_id: item.showroom_id})
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  createProgrammaticSchedule({dispatch}, {item}) {
    return new Promise((resolve, reject) => {
      this.$axios.post(`/programmatic-schedules/create`, {item})
        .then((response) => {
          dispatch('fetchProgrammaticSchedules', {showroom_id: item.showroom_id})
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  deleteSchedule({dispatch}, {id, showroom_id}) {
    return new Promise((resolve, reject) => {
      this.$axios.post(`/schedules/delete`, {id, showroom_id})
        .then((response) => {
          dispatch('fetchSchedules', {showroom_id})
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  deleteProgrammaticSchedule({dispatch}, {id, showroom_id}) {
    return new Promise((resolve, reject) => {
      this.$axios.post(`/programmatic-schedules/delete`, {id, showroom_id})
        .then((response) => {
          dispatch('fetchProgrammaticSchedules', {showroom_id})
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },

  fetchSchedules({commit}, {showroom_id = null}) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`/schedules${showroom_id ? '?showroom_id=' + showroom_id : ''}`)
        .then((response) => {
          commit('SET_SCHEDULES', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchProgrammaticSchedules({commit}, {showroom_id = null}) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`/programmatic-schedules${showroom_id ? '?showroom_id=' + showroom_id : ''}`)
        .then((response) => {
          commit('SET_PROGRAMMATIC_SCHEDULES', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  saveOperator({dispatch}, {item}) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/operator/save', {item})
        .then((response) => {
          if (response.data.id) {
            dispatch('fetchOperators', {showroom_id: item.showroom_id})
            resolve(response)
          }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  deleteOperator({dispatch}, {id}) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/operator/delete', {id})
        .then((response) => {
          dispatch('fetchOperators')
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchWorkPlaces({commit}, {id = null}) {
    return new Promise((resolve, reject) => {

      this.$axios.get(`/work-places${id ? '?filter[showroom_id]=' + id : ''}`)
        .then((response) => {
          commit('SET_WORK_PLACES', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
}

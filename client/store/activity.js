// state
export const state = () => ({
  sessions: [],
  activities: [],
  used_activities: [],
})

// getters
export const getters = {
  sessions: state => state.sessions,
  activities: state => state.activities,
  used_activities: state => state.used_activities,
}

// mutations
export const mutations = {

  SET_SESSIONS (state, payload) {
    state.sessions = payload
  },
  SET_ACTIVITIES (state, payload) {
    state.activities = payload
  },
  SET_USED_ACTIVITIES (state, payload) {
    state.used_activities = payload
  }
}

// actions
export const actions = {
  fetchActivities ({ commit }, {type=null}) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/activities?type=type')
        .then((response) => {
          commit('SET_ACTIVITIES', response.data)
          console.log(123)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  fetchCarActivities ({ commit }, {causer_id=null, mark_id, model_id, date = null, showroom_id=null}) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`/car-activities?${causer_id > 0 ? 'filter[causer_id]=' + causer_id : ''}${mark_id > 0 ? '&filter[mark]=' + mark_id: ''}${model_id > 0 ? '&filter[model]=' + model_id: ''}${date !==null ? '&filter[created_at]='+date: ''}${showroom_id !==null ? '&filter[showroom]='+showroom_id: ''}`)
        .then((response) => {
          commit('SET_ACTIVITIES', response.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  fetchUsedActivities ({ commit }, {causer_id=null, mark_id, model_id, date = null, showroom_id=null}) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`/used-activities?${causer_id > 0 ? 'filter[causer_id]=' + causer_id : ''}${mark_id > 0 ? '&filter[mark]=' + mark_id: ''}${model_id > 0 ? '&filter[model]=' + model_id: ''}${date !==null ? '&filter[created_at]='+date: ''}${showroom_id !==null ? '&filter[showroom]='+showroom_id: ''}`)
        .then((response) => {
          commit('SET_USED_ACTIVITIES', response.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
}

// state
export const state = () => ({
  users: [],
  roles: [],
  showroom_users: [],
  sessions: [],
  activities: [],
  auth_activities: [],
  drawerState: null,
  sidebarState: false,
})

// getters
export const getters = {
  users: state => state.users,
  roles: state => state.roles,
  showroom_users: state => state.showroom_users,
  sessions: state => state.sessions,
  auth_activities: state => state.auth_activities,
  activities: state => state.activities,
  drawerState: state => state.drawerState,
  sidebarState: state => state.sidebarState
}

// mutations
export const mutations = {
  SET_USERS (state, payload) {
    state.users = payload
  },
  SET_ROLES (state, payload) {
    state.roles = payload
  },
  SET_SHOWROOM_USERS (state, payload) {
    state.showroom_users = payload
  },
  SET_SESSIONS (state, payload) {
    state.sessions = payload
  },
  SET_ACTIVITIES (state, payload) {
    state.activities = payload
  },
  SET_AUTH_ACTIVITIES (state, payload) {
    state.auth_activities = payload
  },
  toggleDrawerState (state, payload) {
    state.drawerState = payload
  },
  toggleSidebarState (state) {
    state.sidebarState = !state.sidebarState
  }
}

// actions
export const actions = {
  toggle ({ commit }, toggle) {
    commit('toggleDrawerState', toggle)
  },
  toggleSidebar ({ commit }) {
    commit('toggleSidebarState')
  },
  fetchUsers ({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/users')
        .then((response) => {
          commit('SET_USERS', response.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  fetchUsersByShowroom ({ commit }, {showroom_id=null}) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`/users/by-showroom${showroom_id ? '?filter[showroom_id]=' +showroom_id : '' }`)
        .then((response) => {
          commit('SET_SHOWROOM_USERS', response.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
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
  fetchAuthActivities ({ commit }, {query={}}) {
    const {
      page = null,
      showroom_id = null,
      filter_from = null,
      filter_to = null,
      user_id = null,
    } = query;
    const params = new URLSearchParams();
    if (page) params.append("page", page);
    if (user_id) params.append("filter[authenticatable_id]", user_id);
    if (showroom_id) params.append("filter[ByShowroomId]", showroom_id);
    if (filter_from && filter_to) {
      params.append('filter[between]', `${filter_from},${filter_to}`);
    }

    return new Promise((resolve, reject) => {

      this.$axios.get(`/user-activities/logs?${decodeURIComponent(params)}`)
        .then((response) => {
          commit('SET_AUTH_ACTIVITIES', response.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  fetchCarActivities ({ commit }, {causer_id=null, mark_id, model_id, date = null}) {
    return new Promise((resolve, reject) => {
      this.$axios.get(`/car-activities?${causer_id > 0 ? 'filter[causer_id]=' + causer_id : ''}${mark_id > 0 ? '&filter[mark]=' + mark_id: ''}${model_id > 0 ? '&filter[model]=' + model_id: ''}${date !==null ? '&filter[created_at]='+date: ''}`)
        .then((response) => {
          commit('SET_ACTIVITIES', response.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },

  fetchUserRoles ({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/user-roles')
        .then((response) => {
          commit('SET_ROLES', response.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  save ({ commit, dispatch }, data) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/user/save', data)
        .then((response) => {
          if (response.data) {
            dispatch('fetchUsers')
            resolve(response)
          }
        })
        .catch((error) => { reject(error) })
    })
  },
  update ({ commit, dispatch }, data) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/user/update', data)
        .then((response) => {
          if (response.data) {
            dispatch('fetchUsers')
            resolve(response)
          }
        })
        .catch((error) => { reject(error) })
    })
  },
  delete ({ commit, dispatch }, data) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/user/delete/' + data.id)
        .then((response) => {
          dispatch('fetchUsers')
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  }
}

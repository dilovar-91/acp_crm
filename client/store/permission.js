
// state
export const state = () => ({
  pages: [],
  nest_pages: [],
  roles: [],
  user_permissions: [],
  permissions: [],
  user: []
})

// getters
export const getters = {
  pages: state => state.pages,
  nest_pages: state => state.nest_pages,
  roles: state => state.roles,
  user_permissions: state => state.user_permissions,
  permissions: state => state.permissions,
  user: state => state.user

}

// mutations
export const mutations = {
  SET_PAGES (state, pages) {
    state.pages = pages
  },
  SET_NEST_PAGES (state, payload) {
    state.nest_pages = payload
  },
  SET_ROLES (state, roles) {
    state.roles = roles
  },
  SET_USER_PERMISSIONS (state, payload) {
    state.user_permissions = payload
  },
  SET_PERMISSIONS (state, permissions) {
    state.permissions = permissions
  },
  SET_USER (state, payload) {
    state.user = payload
  }

}

// actions
export const actions = {
  fetchNestPages ({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/nest-pages')
        .then((response) => {
          commit('SET_NEST_PAGES', response.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  fetchPages ({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/pages')
        .then((response) => {
          commit('SET_PAGES', response.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  fetchRoles ({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/roles')
        .then((response) => {
          commit('SET_ROLES', response.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },

  fetchPermissions ({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/permissions')
        .then((response) => {
          commit('SET_PERMISSIONS', response.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  fetchUserPermissions ({ commit }, { id }) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/user-permissions/' + id)
        .then((response) => {
          commit('SET_USER_PERMISSIONS', response.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  fetchUser ({ commit }, { id }) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/get-user/' + id)
        .then((response) => {
          commit('SET_USER', response.data)
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  },
  save ({ commit, dispatch }, data) {
    return new Promise((resolve, reject) => {
      this.$axios.post('/permission/save', { item: data.item })
        .then((response) => {
          if (response.data.id) {
            dispatch('fetchUserPermissions', { id: data.item.user_id })
            dispatch('fetchPages')
            resolve(response)
          }
        })
        .catch((error) => { reject(error) })
    })
  },
  saveUserPermission ({ commit, dispatch }, data) {
    return new Promise((resolve, reject) => {
      axios.post('/user-permission/save', { item: data.item })
        .then((response) => {
          if (response.data.id) {
            dispatch('fetchUserPermissions', { id: data.item.user_id })
            dispatch('fetchPages')
            resolve(response)
          }
        })
        .catch((error) => { reject(error) })
    })
  },
  delete ({ commit, dispatch }, data) {
    return new Promise((resolve, reject) => {
      this.$axios.get('/permission/delete/' + data.id)
        .then((response) => {
          dispatch('fetchPermissions')
          dispatch('fetchPages')
          resolve(response)
        })
        .catch((error) => { reject(error) })
    })
  }
}

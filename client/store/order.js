// state
export const state = () => ({
  orders: [],
  all_orders: [],
  all_arrivals: [],
  missed_calls: [],
  types: [],
  statuses: [],
  trashes: [],
  drops: [],
  histories: [],
  sms_templates: [],
})

// getters
export const getters = {
  orders: (state) => state.orders,
  all_orders: (state) => state.all_orders,
  missed_calls: (state) => state.missed_calls,
  types: (state) => state.types,
  statuses: (state) => state.statuses,
  trashes: (state) => state.trashes,
  histories: (state) => state.histories,
  drops: (state) => state.drops,
  sms_templates: (state) => state.sms_templates,
  urgent: (state) => {
    return state.all_orders.filter(
      (l) =>
        this.$moment().diff(this.$moment(l.callback), 'minutes') <= 0 &&
        this.$moment().diff(this.$moment(l.callback), 'minutes') >= -60
    )
  },
}

// mutations
export const mutations = {
  SET_ORDERS(state, orders) {
    state.orders = orders
  },
  SET_ALL_ORDERS(state, payload) {
    state.all_orders = payload
  },
  SET_ALL_ARRIVALS(state, payload) {
    state.all_arrivals = payload
  },
  SET_MISSED_CALLS(state, payload) {
    state.missed_calls = payload
  },
  SET_TYPES(state, payload) {
    state.types = payload
  },
  SET_STATUSES(state, payload) {
    state.statuses = payload
  },
  SET_TRASHES(state, payload) {
    state.trashes = payload
  },
  SET_DROPS(state, payload) {
    state.drops = payload
  },
  SET_ARRIVAL_STATUSES(state, payload) {
    state.arrival_statuses = payload
  },
  SET_HISTORIES(state, payload) {
    state.histories = payload
  },
  SET_SMS_TEMPLATES(state, payload) {
    state.sms_templates = payload
  },
}

// actions
export const actions = {
  fetchOrders(
    { commit },
    {
      id,
      from = null,
      to = null,
      site = null,
      type = null,
      status = null,
      page = 1,
      search = null,
      from_arrive = null,
      to_arrive = null,
      operator_id = null,
      query = null,
    }
  ) {
    console.log(query)
    return new Promise((resolve, reject) => {
      const url = `/orders?${page ? 'page=' + page : ''}${
        id ? '&filter[showroom_id]=' + id : ''
      }${from && to ? '&filter[between]=' + from + ',' + to : ''}${
        from_arrive && to_arrive
          ? '&filter[betweenArrive]=' + from_arrive + ',' + to_arrive
          : ''
      }${search ? '&filter[search]=' + search : ''}${
        site !== null ? '&filter[site_id]=' + site : ''
      }${type !== null ? '&filter[type_id]=' + type : ''}${
        status !== null && (status != 5 || from_arrive === null)
          ? '&filter[status_id]=' + status
          : ''
      }${operator_id > 0 ? '&filter[operator_id]=' + operator_id : ''}`
      this.$axios
        .get(url)
        .then((response) => {
          commit('SET_ORDERS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchOrders2(
    { commit },
    { id, query = {}, agency = null, blacklist = null }
  ) {
    const {
      to = null,
      site_id = null,
      type_id = null,
      status = null,
      trash = null,
      page = null,
      search = null,
      fio = null,
      from = null,
      operator_id = null,
      date_type = null,
      limit = null,
      payment_method = null,
      mark_id = null,
      model_id = null,
      region_id = null,
      phone = null,
      arrived = null,
      arrived_type  = null,
      repeat = null,
      drop_id = null,
      agency_id = null,
      not_confirmed = null,
      campaign = null,
      to_showroom = null,
      paymentUndefined = null,
      approvedCredit = null,
      creditCount = null,
      deferred_purchase = null,
      utm = null,
    } = query
    return new Promise((resolve, reject) => {
      const params = new URLSearchParams()
      if (page) params.append('page', page)
      if (id) params.append('filter[showroom_id]', id)

      if (search) params.append('filter[search]', search)
      if (fio) params.append('filter[searchFio]', fio)
      if (site_id !== null) params.append('filter[site_id]', site_id)
      if (to_showroom !== null) params.append('filter[ToShowroom]', to_showroom)
      if (type_id !== null) params.append('filter[type_id]', type_id)
      if (status !== null) params.append('filter[status_id]', status)
      if (agency !== null) params.append('filter[agency]', agency)
      if (agency_id !== null) params.append('filter[agency]', agency_id)
      if (mark_id !== null) params.append('filter[mark_id]', mark_id)
      if (trash !== null) params.append('filter[trash_id]', trash)
      if (region_id !== null) params.append('filter[region_id]', region_id)
      if (model_id !== null) params.append('filter[model_id]', model_id)
      if (phone !== null) params.append('filter[tell]', phone)
      if (arrived !== null) params.append('filter[arrived]', arrived)
      if (arrived_type !== null) params.append('filter[ArrivedType]', arrived_type)
      if (repeat !== null) params.append('filter[repeat]', repeat)
      if (drop_id !== null) params.append('filter[drop_id]', drop_id)
      if (campaign !== null) params.append('filter[utm_campaign]', campaign)
      if (not_confirmed !== null)
        params.append('filter[notConfirmed]', not_confirmed)
      if (paymentUndefined !== null)
        params.append('filter[paymentUndefined]', paymentUndefined)
      if (approvedCredit !== null)
        params.append('filter[approvedCredit]', approvedCredit)
      if (creditCount !== null)
        params.append('filter[creditCount]', creditCount)
      if (deferred_purchase !== null)
        params.append('filter[deferred_purchase]', deferred_purchase)
      if (utm !== null) params.append('filter[utm]', utm)
      if (blacklist !== null) {
        params.append('include', 'blacklist')
        params.append('filter[blacklist]', blacklist)
      }
      console.log(payment_method)
      if (payment_method !== null || payment_method === 0) {
        console.log(123)
        params.append('filter[payment_method]', payment_method)
      }
      if (limit !== null) params.append('limit', limit)
      if (from && to) {
        const paramMapping = {
          1: 'filter[between]',
          2: 'filter[betweenUpdated]',
          3: 'filter[betweenArrive]',
          4: 'filter[betweenArrived]',
          5: 'filter[betweenCallback]',
          6: 'filter[LastCall]',
        }
        const defaultParam = 'filter[between]' // Replace "default" with your desired default parameter name
        const paramKey = paramMapping[date_type] || defaultParam
        params.append(paramKey, `${from},${to}`)
      }

      if (operator_id > 0) params.append('filter[operator_id]', operator_id)
      console.log(decodeURIComponent(params))
      const url = `/orders?${decodeURIComponent(params)}`
      this.$axios
        .get(url)
        .then((response) => {
          commit('SET_ORDERS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },

  fetchDeferredOrders(
    { commit },
    { query = {}, agency = null, blacklist = null }
  ) {
    return new Promise((resolve, reject) => {
      const params = new URLSearchParams()

      // Base filters

      if (agency !== null) params.append('filter[agency]', agency)

      // Define simple key-to-param mappings
      const filters = {
        page: 'page',
        search: 'filter[search]',
        fio: 'filter[searchFio]',
        site_id: 'filter[site_id]',
        to_showroom: 'filter[ToShowroom]',
        showroom_id: 'filter[showroom_id]',
        type_id: 'filter[type_id]',
        status: 'filter[status_id]',
        mark_id: 'filter[mark_id]',
        trash: 'filter[trash_id]',
        region_id: 'filter[region_id]',
        model_id: 'filter[model_id]',
        phone: 'filter[tell]',
        arrived: 'filter[arrived]',
        repeat: 'filter[repeat]',
        drop_id: 'filter[drop_id]',
        campaign: 'filter[utm_campaign]',
        not_confirmed: 'filter[notConfirmed]',
        paymentUndefined: 'filter[paymentUndefined]',
        approvedCredit: 'filter[approvedCredit]',
        creditCount: 'filter[creditCount]',
        returned: 'filter[returned]',
        payment_method: 'filter[payment_method]',
        limit: 'limit',
      }

      // Append filters if present
      Object.entries(filters).forEach(([key, param]) => {
        const value = query[key]
        if (value !== null && value !== undefined) {
          // Special case: allow 0 values like payment_method = 0
          if (value || value === 0) {
            params.append(param, value)
          }
        }
      })

      // Special case: operator_id must be > 0
      if (query.operator_id > 0) {
        params.append('filter[operator_id]', query.operator_id)
      }

      // Special handling for blacklist
      if (blacklist !== null) {
        params.append('include', 'blacklist')
        params.append('filter[blacklist]', blacklist)
      }

      // Date filtering
      const { from, to, date_type } = query
      if (from && to) {
        const dateFilters = {
          1: 'filter[between]',
          2: 'filter[betweenUpdated]',
          3: 'filter[betweenArrive]',
          4: 'filter[betweenArrived]',
          5: 'filter[betweenCallback]',
          6: 'filter[LastCall]',
          7: 'filter[DeferredReturnDate]',
        }
        const paramKey = dateFilters[date_type] || 'filter[deferredReturnDate]'
        params.append(paramKey, `${from},${to}`)
      }

      const url = `/deffered-orders?filter[deferredPurchase]=true&${decodeURIComponent(
        params
      )}`
      console.log(url)

      this.$axios
        .get(url)
        .then((response) => {
          commit('SET_ORDERS', response.data)
          resolve(response)
        })
        .catch(reject)
    })
  },

  fetchLightOrders({ commit }, { id, query = {}, agency = null }) {
    const {
      to = null,
      site_id = null,
      type_id = null,
      status = null,
      trash = null,
      page = null,
      search = null,
      from = null,
      operator_id = null,
      date_type = null,
      limit = null,
      mark_id = null,
      model_id = null,
      to_showroom = null,
    } = query
    return new Promise((resolve, reject) => {
      const params = new URLSearchParams()
      if (page) params.append('page', page)
      if (id) params.append('filter[showroom_id]', id)
      if (search) params.append('filter[search]', search)
      if (site_id !== null) params.append('filter[site_id]', site_id)
      if (to_showroom !== null) params.append('filter[ToShowroom]', to_showroom)
      if (type_id !== null) params.append('filter[type_id]', type_id)
      if (status !== null) params.append('filter[status_id]', status)
      if (mark_id !== null) params.append('filter[mark_id]', mark_id)
      if (trash !== null) params.append('filter[trash_id]', trash)
      if (model_id !== null) params.append('filter[model_id]', model_id)
      if (limit !== null) params.append('limit', limit)
      if (operator_id > 0) params.append('filter[operator_id]', operator_id)

      if (from && to) {
        const paramMapping = {
          1: 'filter[between]',
        }
        const defaultParam = 'filter[between]' // Replace "default" with your desired default parameter name
        const paramKey = paramMapping[date_type] || defaultParam
        params.append(paramKey, `${from},${to}`)
      }

      const url = `/light-orders?${decodeURIComponent(params)}`
      this.$axios
        .get(url)
        .then((response) => {
          commit('SET_ORDERS', response.data)
          // console.log(response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },

  fetchAllOrders({ commit }, { id }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .get(`orders/all/${id}`)
        .then((response) => {
          commit('SET_ALL_ORDERS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },

  fetch_arrivals({ commit }, { id }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .get(`orders/arrivals/${id}`)
        .then((response) => {
          commit('SET_ALL_ARRIVALS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetch_missed_calls({ commit }, { id }) {
    if (!id) return
    return new Promise((resolve, reject) => {
      this.$axios
        .get(`orders/missed-calls/${id}`)
        .then((response) => {
          commit('SET_MISSED_CALLS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },

  fetchTypes({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .get('order-types')
        .then((response) => {
          commit('SET_TYPES', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchStatuses({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .get('order-statuses')
        .then((response) => {
          commit('SET_STATUSES', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchTrashes({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .get('order/trashes')
        .then((response) => {
          commit('SET_TRASHES', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchDrops({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .get('order/drops')
        .then((response) => {
          commit('SET_DROPS', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchArrivalStatuses({ commit }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .get('order/arrival-statuses')
        .then((response) => {
          commit('SET_ARRIVAL_STATUSES', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  saveOrder({ dispatch }, { item }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .post('/orders/save', { item })
        .then((response) => {
          // if (response.data.id) {
          // dispatch('fetchOrders', { id: data.item.showroom_id })
          resolve(response)
          // }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  updateOrder({ dispatch }, { item }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .post('/orders/update_mini', { item })
        .then((response) => {
          // if (response.data.id) {
          // dispatch('fetchOrders', { id: data.item.showroom_id })
          resolve(response)
          // }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  update({ dispatch }, { item }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .post('/orders/update', { item })
        .then((response) => {
          // if (response.data.id) {
          // dispatch('fetchOrders', { id: data.item.showroom_id })
          resolve(response)
          // }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  copyOrder({ dispatch }, { item }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .post('/orders/copy', { item })
        .then((response) => {
          // if (response.data.id) {
          // dispatch('fetchOrders', { id: data.item.showroom_id })
          resolve(response)
          // }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  deferPurchase({ dispatch }, { item }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .post('/orders/defer-purchase', { item })
        .then((response) => {
          // if (response.data.id) {
          // dispatch('fetchOrders', { id: data.item.showroom_id })
          resolve(response)
          // }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },

  returnDeferPurchase({ dispatch }, { order_id }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .post('/orders/return-defer-purchase', { order_id })
        .then((response) => {
          // if (response.data.id) {
          // dispatch('fetchOrders', { id: data.item.showroom_id })
          resolve(response)
          // }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  distributeOrder({ dispatch }, { item }) {
    console.log(item)
    return new Promise((resolve, reject) => {
      this.$axios
        .post('/orders/distribute', { item })
        .then((response) => {
          // if (response.data.id) {
          // dispatch('fetchOrders', { id: data.item.showroom_id })
          resolve(response)
          // }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  passOrders({ dispatch }, data) {
    console.log(data)
    return new Promise((resolve, reject) => {
      this.$axios
        .post('/order/pass-orders', data)
        .then((response) => {
          // if (response.data.id) {
          // dispatch('fetchOrders', { id: data.item.showroom_id })
          resolve(response)
          // }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  toArrive({ dispatch }, data) {
    return new Promise((resolve, reject) => {
      this.$axios
        .post('/arrivals/to-arrive', { item: data.item })
        .then((response) => {
          // if (response.data.id) {
          // dispatch('fetchOrders', { id: data.item.showroom_id })
          resolve(response)
          // }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  delete({ commit, dispatch }, { id }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .get('/order/delete/' + id)
        .then((response) => {
          // dispatch('fetchCars')
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetchHistory({ commit }, { id, showroom_id }) {
    return new Promise((resolve, reject) => {
      const url = `/orders/histories?${id ? 'id=' + id : ''}${
        id ? '&showroom_id=' + showroom_id : ''
      }`
      this.$axios
        .get(url)
        .then((response) => {
          commit('SET_HISTORIES', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  fetch_sms_templates({ commit }, { id, operator_id }) {
    return new Promise((resolve, reject) => {
      const url = `/sms/templates?${id ? 'showroom_id=' + id : ''}${operator_id ? '&operator_id=' + operator_id : ''}`
      this.$axios
        .get(url)
        .then((response) => {
          commit('SET_SMS_TEMPLATES', response.data)
          resolve(response)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },

  blacklistRequest({ dispatch }, { showroom_id, order_id }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .post('/orders/blacklist-request', { showroom_id, order_id })
        .then((response) => {
          // if (response.data.id) {
          // dispatch('fetchOrders', { id: data.item.showroom_id })
          resolve(response)
          // }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },

  blacklist({ dispatch }, { id, status, showroomId }) {
    return new Promise((resolve, reject) => {
      this.$axios
        .post('/orders/blacklist', { id, status })
        .then((response) => {
          // if (response.data.id) {
          dispatch('fetchOrders2', { id: showroomId, blacklist: true })
          resolve(response)
          // }
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
}

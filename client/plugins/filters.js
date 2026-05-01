import Vue from 'vue'
Vue.filter('capitalize', function (value) {
  if (!value) {
    return ''
  }
  value = value.toString()
  return value.charAt(0).toUpperCase() + value.slice(1)
})
Vue.filter('truncate', function (string, value = 100) {
  if (string === null) {
    return ''
  }
  return string.substring(0, value) + '…'
})
Vue.filter('upper', function (value) {
  if (!value) {
    return ''
  }
  value = value.toString()
  return value.toUpperCase()
})
Vue.filter('cutPhone', function (phone) {
  return phone !== null
    ? phone.substr(-4, 2) + '-' + phone.slice(phone.length - 2)
    : null
})

Vue.filter('currency', function (value) {
  const numbers = /^[-+]?[0-9]+$/
  if (!value) {
    return ''
  } else if (!value.match(numbers)) {
    return value
  }
  const val = (value / 1).toFixed().replace('.', ',')
  return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ')
})

import Vue from 'vue'
import Router from 'vue-router'
import { scrollBehavior } from '~/utils'

Vue.use(Router)
const page = (path) => () =>
  import('~/pages/' + path).then((m) => m.default || m)

const routes = [
  { path: '/', name: 'home', component: page('home.vue') },

  { path: '/ads/:id', name: 'ads-showroom', component: page('ads/_id.vue') },
  {
    path: '/showrooms',
    name: 'showrooms',
    component: page('showroom/index.vue'),
  },
  {
    path: '/showroom/:id',
    name: 'showroom-detail',
    component: page('showroom/_id.vue'),
  },
  { path: '/auth/signin', name: 'auth', component: page('auth/signin.vue') },

  {
    path: '/shipments',
    name: 'shipments',
    component: page('shipment/index.vue'),
  },
  { path: '/reports', name: 'reports', component: page('report/index.vue') },
  {
    path: '/reports/projects',
    name: 'projects',
    component: page('report/projects.vue'),
  },
  {
    path: '/reports/all',
    name: 'all-reports',
    component: page('report/reports.vue'),
  },
  {
    path: '/reports/justwe',
    name: 'justwe-reports',
    component: page('report/justwe.vue'),
  },
  {
    path: '/reports/inhouse',
    name: 'inhouse-reports',
    component: page('report/inhouse.vue'),
  },
  {
    path: '/reports/classified',
    name: 'classified-reports',
    component: page('report/classified.vue'),
  },

  {
    path: '/shipments/edit/:id',
    name: 'shipments-edit',
    component: page('shipment/edit.vue'),
  },

  { path: '/cars', name: 'cars-index', component: page('car/index.vue') },

  { path: '/cars/all', name: 'cars/all', component: page('car/all.vue') },
  { path: '/cars/sold', name: 'cars/sold', component: page('car/sold.vue') },
  {
    path: '/cars/transits',
    name: 'cars/transits',
    component: page('car/transits.vue'),
  },

  {
    path: '/cars/car-logs',
    name: 'car-logs',
    component: page('car/car-logs.vue'),
  },

  { path: '/cars/:id', name: 'cars', component: page('car/_id.vue') },

  {
    path: '/used-cars',
    name: 'used-cars-index',
    component: page('usedcar/index.vue'),
  },
  {
    path: '/used-cars/used-logs',
    name: 'used-logs',
    component: page('usedcar/used-logs.vue'),
  },
  {
    path: '/used-cars/transits',
    name: 'used-cars/transits',
    component: page('usedcar/transits.vue'),
  },
  {
    path: '/used-cars/all',
    name: 'used-cars-all',
    component: page('usedcar/all.vue'),
  },
  {
    path: '/used-cars/:id',
    name: 'used-cars-id',
    component: page('usedcar/_id.vue'),
  },

  {
    path: '/user/permission',
    name: 'permission',
    component: page('user/index.vue'),
  },
  { path: '/users', name: 'user-list', component: page('user/users.vue') },
  {
    path: '/user/edit/:id',
    name: 'user-id',
    component: page('user/user-edit.vue'),
  },

  { path: '/tablet', name: 'tablet', component: page('tablet/index.vue') },

  { path: '/profile', name: 'profile', component: page('auth/profile.vue') },

  { path: '/crm', name: 'crm', component: page('crm/index.vue') },

  { path: '/crm/:id', name: 'crm-id', component: page('crm/_id.vue') },
  {
    path: '/crm/28/orders',
    name: 'crm-orders',
    component: page('crm/orders_deferred_purchases.vue'),
  },
  {
    path: '/crm/12/orders',
    name: 'classified-orders',
    component: page('crm/classified_orders.vue'),
  },
  {
    path: '/crm/:id/orders',
    name: 'crm-orders',
    component: page('crm/orders.vue'),
  },
  {
    path: '/crm/:id/blacklist-orders',
    name: 'crm-blacklist-orders',
    component: page('crm/blacklist-orders.vue'),
  },
  {
    path: '/crm/:id/orders-mini/:agency',
    name: 'crm-orders-mini',
    component: page('crm/orders_mini.vue'),
  },
  {
    path: '/crm/:id/orders-mini',
    name: 'crm-orders-mini',
    component: page('crm/orders_mini.vue'),
  },
  {
    path: '/crm/:id/light-orders',
    name: 'light-orders',
    component: page('crm/light_orders.vue'),
  },
  {
    path: '/crm/:id/orders/filter',
    name: 'crm-orders',
    component: page('crm/orders/filter.vue'),
  },
  {
    path: '/crm/12/order/:id/edit',
    name: 'order-edit-classified',
    component: page('crm/order_edit_mini_classified.vue'),
  },
  {
    path: '/crm/:showroom/order/:id/edit',
    name: 'order-edit',
    component: page('crm/order_edit.vue'),
  },
  {
    path: '/crm/12/order/:id/edit-mini',
    name: 'order-edit-classified',
    component: page('crm/order_edit_mini_classified.vue'),
  },
  {
    path: '/crm/:showroom/order/:id/edit-mini',
    name: 'order-edit',
    component: page('crm/order_edit_mini.vue'),
  },
  {
    path: '/crm/:showroom/order/:id/detail',
    name: 'order-detail',
    component: page('crm/order_detail.vue'),
  },
  {
    path: '/analytics',
    name: 'analytics',
    component: page('crm/analytics.vue'),
  },

  {
    path: '/crm/9/reports',
    name: 'reports',
    component: page('crm/reports_victory.vue'),
  },
  {
    path: '/crm/:id/reports',
    name: 'reports',
    component: page('crm/reports.vue'),
  },
  {
    path: '/crm/:id/report',
    name: 'report',
    component: page('crm/report/index.vue'),
  },
  {
    path: '/crm/9/report/:agency',
    name: 'victory-agency',
    component: page('crm/report/_victory.vue'),
  },
  {
    path: '/crm/:id/report/:agency',
    name: 'report-agency',
    component: page('crm/report/_agency.vue'),
  },
  {
    path: '/crm/:id/schedules',
    name: 'schedules',
    component: page('crm/schedules.vue'),
  },

  // admin
  { path: '/admin', name: 'admin', component: page('admin/index.vue') },
  {
    path: '/admin/operators/:id',
    name: 'operator-id',
    component: page('admin/operators.vue'),
  },
  {
    path: '/admin/managers/:id',
    name: 'manager-id',
    component: page('admin/managers.vue'),
  },
  { path: '/admin/sites', name: 'site-id', component: page('admin/sites.vue') },
  {
    path: '/admin/blacklist',
    name: 'blacklist',
    component: page('admin/blacklist.vue'),
  },
  {
    path: '/admin/marks',
    name: 'marks-id',
    component: page('admin/marks.vue'),
  },
  {
    path: '/admin/models',
    name: 'models-id',
    component: page('admin/models.vue'),
  },
  {
    path: '/admin/auth-logs',
    name: 'auth-logs',
    component: page('admin/auth-logs.vue'),
  },
]

export function createRouter() {
  return new Router({
    routes,
    scrollBehavior,
    mode: 'history',
  })
}

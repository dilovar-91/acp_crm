export default async function ({ $gates, store, route, error, $axios, $auth }) {
  // If the user is not an admin
 // if (!$gates.hasRole('admin') ) {
  //  return redirect('/login')
  // }

  const roles = await $axios.$get('/roles')
  const permissions = await $axios.$get('/permissions')


  $gates.setPermissions(permissions)
  $gates.setRoles(roles)
  //console.log((!$gates.hasPermission(route.name) || !$gates.hasPermission(route.name + '/')) && !$gates.hasPermission(route.path))
  //console.log('see_'+route.name,  'see_'+route.path, $gates.hasRole('admin'))
  if (!$gates.hasRole('admin') && !$gates.hasPermission('see_'+route.name) && !$gates.hasPermission('see_'+route.path)) {
    error({ statusCode: 403, message: 'Упс! У вас нету прав доступа к этой странице!' })
  }
}

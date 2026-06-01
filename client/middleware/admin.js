export default function ({ $gates, error }) {
  if (!$gates.hasRole('admin')) {
    error({
      statusCode: 403,
      message: 'Упс! У вас нету прав доступа к этой странице!',
    })
  }
}

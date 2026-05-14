export default {
  menu: [
    {
      text: '',
      key: '',
      items: [
        //{icon: 'mdi-view-dashboard-outline', key: 'Дашборд', text: 'Дашборд', link: '/dashboard'},

        {
          icon: 'mdi-google-analytics',
          key: 'CRM',
          text: 'Crm',
          link: '/crm',
          items: [
            {
              icon: 'mdi-file-outline',
              key: 'menu.authLogin',
              text: 'Заявки',
              link: '/crm/orders',
              items: [
                {
                  icon: 'mdi-checkbox-marked-circle-outline',
                  key: 'menu.authLogin',
                  text: 'М1',
                  link: '/crm/1/orders',
                },
              ],
            },
          ],
        },
      ],

      // footer links
      footer: [],
    },
  ],
}

export default {
  menu: [{
    text: '',
    key: '',
    items: [
      {icon: 'mdi-view-dashboard-outline', key: 'Дашборд', text: 'Дашборд', link: '/dashboard'},
      {icon: 'mdi-store-check', key: 'Салоны', text: 'Салоны', link: '/showrooms'},
      {
        icon: 'mdi-tablet-cellphone', key: 'Планшетка', text: 'Планшетка', link: '/tablet', items: [
          {
            icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'Приезд', link: '/tablet/arrivals', items: [
              {icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'Комфорт', link: '/tablet/arrivals/2'},
              {icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'АвтоПремиум', link: '/tablet/arrivals/4'},
              {icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'Авангард Юг', link: '/tablet/arrivals/5'},
              {icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'Форсаж', link: '/tablet/arrivals/7'},
              {icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'АвтоПремьер', link: '/tablet/arrivals/8'},
              {icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'Автодом', link: '/tablet/arrivals/10'},
              {icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'Автоплюс', link: '/tablet/arrivals/13'},
            ]
          },
          {
            icon: 'mdi-file-outline',
            key: 'menu.authLogin',
            text: 'Кредитный отдел',
            link: '/tablet/credit-requests',
            items: [
              {icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'Комфорт', link: '/tablet/credit-requests/2'},
              {icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'АвтоПремиум', link: '/tablet/credit-requests/4'},
              {icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'Авангард Юг', link: '/tablet/credit-requests/5'},
              {icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'Форсаж', link: '/tablet/credit-requests/7'},
              {icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'АвтоПремьер', link: '/tablet/credit-requests/8'},
              {icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'Автодом', link: '/tablet/credit-requests/10'},
              {icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'Автоплюс', link: '/tablet/credit-requests/13'},
            ]
          },
        ]
      }, {
        icon: 'mdi-google-analytics', key: 'CRM', text: 'Crm', link: '/crm', items: [
          {
            icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'Заявки', link: '/crm/orders', items: [
              {
                icon: 'mdi-checkbox-marked-circle-outline',
                key: 'menu.authLogin',
                text: 'Комфорт',
                link: '/crm/2/orders'
              },
              {
                icon: 'mdi-checkbox-marked-circle-outline',
                key: 'menu.authLogin',
                text: 'АвтоПремиум',
                link: '/crm/4/orders'
              },
              {
                icon: 'mdi-checkbox-marked-circle-outline',
                key: 'menu.authLogin',
                text: 'Авангард Юг',
                link: '/crm/5/orders'
              },
              {
                icon: 'mdi-checkbox-marked-circle-outline',
                key: 'menu.authLogin',
                text: 'Форсаж',
                link: '/crm/7/orders'
              },
              {icon: 'mdi-checkbox-marked-circle-outline', key: 'menu.authLogin', text: 'АвтоПремьер', link: '/crm/8/orders'},
              {
                icon: 'mdi-checkbox-marked-circle-outline',
                key: 'menu.authLogin',
                text: 'Виктори',
                link: '/crm/9/light-orders'
              },
              {
                icon: 'mdi-checkbox-marked-circle-outline',
                key: 'menu.authLogin',
                text: 'Автодом',
                link: '/crm/10/orders'
              },
              {
                icon: 'mdi-checkbox-marked-circle-outline',
                key: 'menu.authLogin',
                text: 'Автоплюс',
                link: '/crm/13/orders'
              },
              {
                icon: 'mdi-checkbox-marked-circle-outline',
                key: 'menu.authLogin',
                text: 'Автопорт',
                link: '/crm/14/orders'
              },
              {
                icon: 'mdi-checkbox-marked-circle-outline',
                key: 'menu.authLogin',
                text: 'Авангард',
                link: '/crm/15/orders'
              },
              {
                icon: 'mdi-checkbox-marked-circle-outline',
                key: 'menu.authLogin',
                text: 'Автополе',
                link: '/crm/17/orders'
              },
            ]
          },]
      }, {
        text: 'База авто',
        key: 'База авто',
        link: '/cars',
        items: [
          {
            icon: 'mdi-file-lock-outline', key: 'Новые', link: "/cars", text: 'Новые', regex: /^\/cars/,
            items: [
              {icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'Все', link: '/cars/all'},
              {icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'Комфорт', link: '/cars/2'},
              {icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'Марьино', link: '/cars/3'},
              {icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'АвтоПремиум', link: '/cars/4'},
              {icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'Авангард Юг', link: '/cars/5'},
              {icon: 'mdi-history', key: 'menu.authLogin', text: 'Сессии', link: '/cars/car-logs'},
              {icon: 'mdi-arrow-collapse-horizontal', key: 'menu.authLogin', text: 'Транзиты', link: '/cars/transits'}
            ]
          },
          {
            icon: 'mdi-file-cancel-outline',
            key: 'Поддержание',
            link: "/used-cars",
            text: 'Поддержание',
            regex: /^\/used-cars/,
            items: [
              {icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'Все', link: '/used-cars/all'},
              {icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'Комфорт', link: '/used-cars/2'},
              {icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'Марьино', link: '/used-cars/3'},
              {icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'АвтоПремиум', link: '/used-cars/4'},
              {icon: 'mdi-file-outline', key: 'menu.authLogin', text: 'Авангард Юг', link: '/used-cars/5'},
              {icon: 'mdi-history', key: 'menu.authLogin', text: 'Сессии', link: '/used-cars/used-logs'},
              {
                icon: 'mdi-arrow-collapse-horizontal',
                key: 'menu.authLogin',
                text: 'Транзиты',
                link: '/used-cars/transits'
              }
            ]
          },
          {
            icon: 'mdi-file-cancel-outline',
            key: 'transits',
            link: "/shipments",
            text: 'Транспортировки',
            regex: /^\/shipments/
          }

        ]
      }],

    // footer links
    footer: []
  }]
}

export default defineAppConfig({
  pages: [
    'pages/index/index',
    'pages/user/index',
    'pages/user/profile/index',
    'pages/user/point/index',
    'pages/login/index',
    'pages/usage/index',
    'pages/usage/car/index',
    'pages/usage/car/add/index',
    'pages/usage/maintenance/add/index',
    'pages/usage/suggest/add/index',
    'pages/article/index',
    'pages/article/detail/index',
    'pages/sale/index',
    'pages/sale/detail/index',
    'pages/activity/index',
    'pages/activity/detail/index',
    'pages/registration/index',
    'pages/order/index',
    'pages/invite/index',
  ],
  window: {
    backgroundTextStyle: 'light',
    navigationBarBackgroundColor: '#fff',
    navigationBarTitleText: 'WeChat',
    navigationBarTextStyle: 'black'
  },
  tabBar: {
    list: [
      {
        iconPath: 'assets/icons/home.png',
        selectedIconPath: 'assets/icons/home_fill.png',
        pagePath: 'pages/index/index',
        text: '首页'
      },
      {
        iconPath: 'assets/icons/car.png',
        selectedIconPath: 'assets/icons/car_fill.png',
        pagePath: 'pages/usage/index',
        text: '用车'
      },
      {
        iconPath: 'assets/icons/sale.png',
        selectedIconPath: 'assets/icons/sale_fill.png',
        pagePath: 'pages/sale/index',
        text: '购车'
      },
      {
        iconPath: 'assets/icons/mountain.png',
        selectedIconPath: 'assets/icons/mountain_fill.png',
        pagePath: 'pages/activity/index',
        text: '玩车'
      },
      {
        iconPath: 'assets/icons/user.png',
        selectedIconPath: 'assets/icons/user_fill.png',
        pagePath: 'pages/user/index',
        text: '我的'
      },
    ],
    color: '#000',
    selectedColor: '#000',
    backgroundColor: '#fff',
    borderStyle: 'white',
  },
})

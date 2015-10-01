app.config(
['$routeProvider',
function($routeProvider) {
  $routeProvider
    .when('/dashboard', {
      templateUrl: '/partial/dashboard.html',
      controller: 'DashboardCtrl'
    })
    .when('/user/add', {
      templateUrl: '/partial/user/add.html',
      controller: 'UserAddCtrl'
    })
      .when('/activity', {
          templateUrl: '/partial/activity/index.html',
          controller: 'ActivityCtrl'
      })
      .when('/activity/type', {
        templateUrl: '/partial/activity/type.html',
        controller: 'ActivityTypeCtrl'
      })
    .otherwise({
      redirectTo: '/dashboard'
    });
}]);

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
    .otherwise({
      redirectTo: '/dashboard'
    });
}]);

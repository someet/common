app.config(
['$routeProvider',
function($routeProvider) {
  $routeProvider
    .when('/dashboard', {
      templateUrl: '/partial/dashboard.html',
      controller: 'DashboardCtrl'
    })
    .otherwise({
      redirectTo: '/dashboard'
    });
}]);

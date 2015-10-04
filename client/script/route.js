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
      .when('/activity/add', {
        templateUrl: '/partial/activity/view.html',
        controller: 'ActivityViewCtrl'
      })
      .when('/activity/:id', {
          templateUrl: '/partial/activity/view.html',
          controller: 'ActivityViewCtrl'
      })
      .when('/activity-type', {
        templateUrl: '/partial/activity-type/index.html',
        controller: 'ActivityTypeCtrl'
      })
      .when('/activity-type/add', {
        templateUrl: '/partial/activity-type/add.html',
        controller: 'ActivityTypeAddCtrl'
      })
      .when('/activity-type/:id', {
        templateUrl: '/partial/activity-type/view.html',
        controller: 'ActivityTypeViewCtrl'
      })
      .when('/question/add/:activityid', {
        templateUrl: '/partial/question/add.html',
        controller: 'QuestionAddCtrl'
      })
    .otherwise({
      redirectTo: '/dashboard'
    });
}]);

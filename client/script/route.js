app.config(
['$routeProvider',
function($routeProvider) {
  $routeProvider
    .when('/dashboard', {
      templateUrl: '/partial/dashboard.html',
      controller: 'DashboardCtrl'
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
      templateUrl: '/partial/activity-type/view.html',
      controller: 'ActivityTypeViewCtrl'
    })
    .when('/activity-type/:id', {
      templateUrl: '/partial/activity-type/view.html',
      controller: 'ActivityTypeViewCtrl'
    })
    .when('/answer/:id',{
      templateUrl: '/partial/answer/add.html',
      controller: 'AnswerAddCtrl'
    })
    .when('/answer/:activityid',{
      templateUrl: '/partial/answer/add.html',
      controller: 'AnswerAddCtrl'
    })
    .when('/user', {
      templateUrl: '/partial/user/list.html',
      controller: 'UserListCtrl'
    })
    .when('/user/add', {
      templateUrl: '/partial/user/add.html',
      controller: 'UserAddCtrl'
    })
    .when('/user/:id', {
      templateUrl: '/partial/user/update.html',
      controller: 'UserUpdateCtrl'
    })
    .when('/user/delete/:id', {
      templateUrl: '/partial/user/add.html',
      controller: 'UserDeleteCtrl'
    })
    .when('/question/add/:activity_id', {
      templateUrl: '/partial/question/add.html',
      controller: 'QuestionAddCtrl'
    })
    .when('/question/view/:activity_id', {
      templateUrl: '/partial/question/view.html',
      controller: 'QuestionViewCtrl'
    })
    .when('/special', {
      templateUrl: '/partial/special/index.html',
      controller: 'SpecialCtrl'
    })
    .when('/special/add', {
      templateUrl: '/partial/special/view.html',
      controller: 'SpecialViewCtrl'
    })
    .when('/special/:id', {
      templateUrl: '/partial/special/view.html',
      controller: 'SpecialViewCtrl'
    })
    .otherwise({
      redirectTo: '/dashboard'
    });
}]);

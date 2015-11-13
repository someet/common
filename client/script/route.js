app.config(
['$routeProvider',
function($routeProvider) {
  $routeProvider
      // 面板
    .when('/dashboard', {
      templateUrl: '/partial/dashboard.html',
      controller: 'DashboardCtrl'
    })

    //活动类型列表
    .when('/activity-type', {
      templateUrl: '/partial/activity-type/index.html',
      controller: 'ActivityTypeCtrl'
    })
    //添加活动类型
    .when('/activity-type/add', {
      templateUrl: '/partial/activity-type/view.html',
      controller: 'ActivityTypeViewCtrl'
    })
    //查看活动类型
    .when('/activity-type/:id', {
      templateUrl: '/partial/activity-type/view.html',
      controller: 'ActivityTypeViewCtrl'
    })
    //活动类型列表
    .when('/activity-tag', {
      templateUrl: '/partial/activity-tag/index.html',
      controller: 'ActivityTagCtrl'
    })

    //添加活动标签
    .when('/activity-tag/add', {
      templateUrl: '/partial/activity-tag/view.html',
      controller: 'ActivityTagViewCtrl'
    })
    //查看活动标签
    .when('/activity-tag/:id', {
      templateUrl: '/partial/activity-tag/view.html',
      controller: 'ActivityTagViewCtrl'
    })

    //根据分类id查询活动列表
    .when('/activity/list/:type_id', {
      templateUrl: '/partial/activity/index.html',
      controller: 'ActivityListCtrl'
    })
    //添加活动
    .when('/activity/add', {
      templateUrl: '/partial/activity/view.html',
      controller: 'ActivityViewCtrl'
    })
    //查看活动
    .when('/activity/:id', {
      templateUrl: '/partial/activity/view.html',
      controller: 'ActivityViewCtrl'
    })

    //专题列表
    .when('/special', {
      templateUrl: '/partial/special/index.html',
      controller: 'SpecialCtrl'
    })
    //添加专题
    .when('/special/add', {
      templateUrl: '/partial/special/view.html',
      controller: 'SpecialViewCtrl'
    })
    //查看专题
    .when('/special/:id', {
      templateUrl: '/partial/special/view.html',
      controller: 'SpecialViewCtrl'
    })
    //用户列表
    .when('/user', {
      templateUrl: '/partial/user/list.html',
      controller: 'UserListCtrl'
    })
    //添加用户
    .when('/user/add', {
      templateUrl: '/partial/user/add.html',
      controller: 'UserAddCtrl'
    })
    //查看用户
    .when('/user/:id', {
      templateUrl: '/partial/user/update.html',
      controller: 'UserUpdateCtrl'
    })
    //删除用户
    .when('/user/delete/:id', {
      templateUrl: '/partial/user/add.html',
      controller: 'UserDeleteCtrl'
    })
    //添加问题
    .when('/question/add/:activity_id', {
      templateUrl: '/partial/question/add.html',
      controller: 'QuestionAddCtrl'
    })
    //添加/查看活动
    .when('/question/view/:activity_id', {
      templateUrl: '/partial/question/view.html',
      controller: 'QuestionViewCtrl'
    })
    //预览问题
    .when('/answer/view/:activity_id',{
      templateUrl: '/partial/answer/add.html',
      controller: 'AnswerAddCtrl'
    })
    //查看答案
    .when('/answer/:activity_id',{
      templateUrl: '/partial/answer/index.html',
      controller: 'AnswerCtrl'
    })
    //查看活动反馈
      .when('/activity-feedback/:activity_id', {
        templateUrl: '/partial/activity-feedback/index.html',
        controller: 'ActivityFeedbackCtrl'
      })
    //消息模板列表
      .when('/sms-template', {
        templateUrl: '/partial/sms-template/index.html',
        controller: 'SmsTemplateCtrl'
      })
    //添加消息模板
      .when('/sms-template/add', {
        templateUrl: '/partial/sms-template/view.html',
        controller: 'SmsTemplateViewCtrl'
      })
    //查看消息模板
      .when('/sms-template/:id', {
        templateUrl: '/partial/sms-template/view.html',
        controller: 'SmsTemplateViewCtrl'
      })
    .otherwise({
      redirectTo: '/dashboard'
    });
}]);

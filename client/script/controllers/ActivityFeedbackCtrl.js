angular.module('controllers')
  .controller('ActivityFeedbackCtrl',
  ['$scope', '$routeParams', '$http', '$location', '$activityFeedbackManage', 'lodash', '$mdToast', '$mdDialog',
    function ($scope, $routeParams, $http, $location, $activityFeedbackManage, lodash, $mdToast, $mdDialog) {

      $scope.$parent.pageName = '活动反馈管理';

      // 获取GET参数的id
      var activity_id = $routeParams.activity_id;
      // 活动反馈列表
      $activityFeedbackManage.fetch(activity_id).then(function (data) {
        $scope.list = data;
        var countScore = '';
        // for(var i = 0; i<data.length; i++;){
          // var countScore += ((data[i].stars * 0.8) +(data[i].sponsor_stars*0.2))/(data.length -1)
          // console.log(data)

        // }
        // var objs =[{a:1},{a:2}];
        angular.forEach(data, function(list,index,array){
        //data等价于array[index]
          countScore = ((list.stars * 0.8) + (list.sponsor_stars*0.2));
          // list.length
           // countScore += list.stars * 0.8
          // console.log(countScore);
          // console.log(data.length)
        });
            var countScore = countScore/data.length;
            $scope.countScore = countScore;
            // console.log(countScore);
      }, function (err) {
        alert(err);
      });
        $scope.view = function(entity) {
          $scope.entity = entity;
        }
      // 跳转到更新反馈页面
      $scope.update = function (feedback) {
        $location.path('/activity-feedback/' + feedback.id);
      };

      // 删除活动反馈
      $scope.delete = function (feedback) {
        var confirm = $mdDialog.confirm()
          .title('确定要删除活动反馈“' + feedback.feedback + '”吗？')
          .ariaLabel('delete activity item')
          .ok('确定删除')
          .cancel('手滑点错了，不删');

        $mdDialog.show(confirm).then(function () {
          $activityFeedbackManage.delete(feedback).then(function (data) {
            lodash.remove($scope.list, function (tmpRow) {
              return tmpRow == feedback;
            });

            $mdToast.show($mdToast.simple()
              .content('删除活动反馈“' + feedback.feedback + '”成功')
              .hideDelay(5000)
              .position("top right"));

          }, function (err) {
            $mdToast.show($mdToast.simple()
              .content(err.toString())
              .hideDelay(5000)
              .position("top right"));
          });
        });



      };

      // 跳转到添加页面
      // $scope.createPage = function () {
      //   $location.path('/activity-feedback/add');
      // }
    }])
  .controller('ActivityFeedbackViewCtrl',
  ['$scope', '$http', '$routeParams', '$location', '$activityFeedbackManage', '$mdToast',
    function ($scope, $http, $routeParams, $location, $activityFeedbackManage, $mdToast) {

      // 获取GET参数的id
      var id = $routeParams.id;

      $scope.$parent.pageName = id>0 ? "更新活动反馈" : "添加活动反馈";
      // 查看单个活动反馈
      $activityFeedbackManage.fetch(id).then(function (data) {
        $scope.entity = data;
      }, function (err) {
        $location.path('/activity-feedback');
      });

      // 保存活动反馈
      $scope.save = function () {
        var entity = $scope.entity;
        var newEntity = {name: entity.name, star: entity.star, feedback: entity.feedback, status: entity.status};
        if (entity.id > 0) { // 更新
          $activityFeedbackManage.update(entity.id, newEntity).then(function (data) {
            $location.path('/activity-feedback');
            $mdToast.show($mdToast.simple()
                .content('修改成功')
                .hideDelay(5000)
                .position("top right"));
          }, function (err) {
            $mdToast.show($mdToast.simple()
                .content(err.toString())
                .hideDelay(5000)
                .position("top right"));
          })
        } else { // 添加
          // $activityFeedbackManage.create(newEntity).then(function (data) {
          //   $location.path('/activity-feedback');
          //   $mdToast.show($mdToast.simple()
          //       .content('添加活动反馈成功')
          //       .hideDelay(5000)
          //       .position("top right"));
          // }, function (err) {
          //   $mdToast.show($mdToast.simple()
          //       .content(err.toString())
          //       .hideDelay(5000)
          //       .position("top right"));
          // });
        }
      };

      // 在修改页面点击取消
      $scope.cancel = function () {
        $location.path('/activity-feedback');
      }
    }]);


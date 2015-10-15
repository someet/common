angular.module('controllers')
  .controller('ActivityTagCtrl',
  ['$scope', '$http', '$location', '$activityTagManage', 'lodash', '$mdToast', '$mdDialog',
    function ($scope, $http, $location, $activityTagManage, lodash, $mdToast, $mdDialog) {

      $scope.$parent.pageName = '活动标签管理';
      // 活动标签列表
      $activityTagManage.fetch().then(function (data) {
        $scope.list = data;
      }, function (err) {
        alert(err);
      });

      // 跳转到更新标签页面
      $scope.update = function (tag) {
        $location.path('/activity-tag/' + tag.id);
      };

      // 删除活动标签
      $scope.delete = function (tag) {
        var confirm = $mdDialog.confirm()
          .title('确定要删除活动标签“' + tag.label + '”吗？')
          .ariaLabel('delete activity item')
          .ok('确定删除')
          .cancel('手滑点错了，不删');

        $mdDialog.show(confirm).then(function () {
          $activityTagManage.delete(tag).then(function (data) {
            lodash.remove($scope.list, function (tmpRow) {
              return tmpRow == tag;
            });

            $mdToast.show($mdToast.simple()
              .content('删除活动标签“' + tag.label + '”成功')
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
      $scope.createPage = function () {
        $location.path('/activity-tag/add');
      }
    }])
  .controller('ActivityTagViewCtrl',
  ['$scope', '$http', '$routeParams', '$location', '$activityTagManage', '$mdToast',
    function ($scope, $http, $routeParams, $location, $activityTagManage, $mdToast) {

      // 获取GET参数的id
      var id = $routeParams.id;

      $scope.$parent.pageName = id>0 ? "更新活动标签" : "添加活动标签";
      // 查看单个活动标签
      $activityTagManage.fetch(id).then(function (data) {
        $scope.entity = data;
      }, function (err) {
        $location.path('/activity-tag');
      });

      // 保存活动标签
      $scope.save = function () {
        var entity = $scope.entity;
        var newEntity = {label: entity.label, status: entity.status};
        if (entity.id > 0) { // 更新
          $activityTagManage.update(entity.id, newEntity).then(function (data) {
            $location.path('/activity-tag');
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
          $activityTagManage.create(newEntity).then(function (data) {
            $location.path('/activity-tag');
            $mdToast.show($mdToast.simple()
                .content('添加活动标签成功')
                .hideDelay(5000)
                .position("top right"));
          }, function (err) {
            $mdToast.show($mdToast.simple()
                .content(err.toString())
                .hideDelay(5000)
                .position("top right"));
          });
        }
      };

      // 在修改页面点击取消
      $scope.cancel = function () {
        $location.path('/activity-tag');
      }
    }]);


angular.module('controllers')
  .controller('ActivityTypeCtrl',
  ['$scope', '$http', '$location', '$activityTypeManage', 'lodash', '$mdToast', '$mdDialog',
    function ($scope, $http, $location, $activityTypeManage, lodash, $mdToast, $mdDialog) {

      $scope.$parent.pageName = '活动类型管理';
      $activityTypeManage.fetch().then(function (data) {
        $scope.list = data;
      }, function (err) {
        alert(err);
      });

      $scope.update = function (type) {
        $location.path('/activity-type/' + type.id);
      };

      $scope.delete = function (type) {

        var confirm = $mdDialog.confirm()
          .title('确定要删除活动类型“' + type.name + '”吗？')
          .ariaLabel('delete activity item')
          .ok('确定删除')
          .cancel('手滑点错了，不删');

        $mdDialog.show(confirm).then(function () {
          $activityTypeManage.delete(type).then(function (data) {

            lodash.remove($scope.list, function (tmpRow) {
              return tmpRow == type;
            });

            $mdToast.show($mdToast.simple()
              .content('删除活动类型“' + type.name + '”成功')
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
      $scope.createActivityTypePage = function () {
        $location.path('/activity-type/add');
      }
    }])
  .controller('ActivityTypeViewCtrl',
  ['$scope', '$http', '$routeParams', '$location', '$activityTypeManage', '$mdToast',
    function ($scope, $http, $routeParams, $location, $activityTypeManage, $mdToast) {
      var id = $routeParams.id;

      $scope.$parent.pageName = '活动类型详情';
      $activityTypeManage.fetch(id).then(function (data) {
        $scope.typeContent = data;
      }, function (err) {
        $location.path('/activity-type');
      });

      $scope.save = function () {
        var type = $scope.typeContent;
        var newType = {name: type.name, displayorder: type.displayorder};
        $activityTypeManage.update(type.id, newType).then(function (data) {

          $location.path('/activity-type');
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
      };

      $scope.cancel = function () {
        $location.path('/activity-type');
      }
    }])
  .controller('ActivityTypeAddCtrl',
  ['$scope', '$http', '$routeParams', '$location', '$activityTypeManage', '$mdToast',
    function ($scope, $http, $routeParams, $location, $activityTypeManage, $mdToast) {
      $scope.$parent.pageName = '添加活动类型';

      $scope.create = function () {
        var newType = $scope.activityType;
        $activityTypeManage.create(newType).then(function (data) {

          $location.path('/activity-type');
          $mdToast.show($mdToast.simple()
            .content('添加活动类型成功')
            .hideDelay(5000)
            .position("top right"));

        }, function (err) {
          $mdToast.show($mdToast.simple()
            .content(err.toString())
            .hideDelay(5000)
            .position("top right"));
        });
      };
    }]);


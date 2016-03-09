angular.module('controllers')
  .controller('ActivityTypeCtrl',
  ['$scope', '$http', '$location', '$activityTypeManage', 'lodash', '$mdToast', '$mdDialog',
    function ($scope, $http, $location, $activityTypeManage, lodash, $mdToast, $mdDialog) {

      $scope.$parent.pageName = '活动类型管理';
      // 活动类型列表
      $activityTypeManage.fetch().then(function (data) {
        $scope.list = data;
      }, function (err) {
        alert(err);
      });

      // 跳转到更新类型页面
      $scope.update = function (type) {
        $location.path('/activity-type/' + type.id);
      };

      // 删除活动类型
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

      // 跳转到添加页面
      $scope.createPage = function () {
        $location.path('/activity-type/add');
      }
    }])
  .controller('ActivityTypeViewCtrl',
  ['$scope', '$http', '$routeParams', '$location', '$activityTypeManage', '$mdToast',
    function ($scope, $http, $routeParams, $location, $activityTypeManage, $mdToast) {

      // 获取GET参数的id
      var id = $routeParams.id;

      $scope.$parent.pageName = id>0 ? "更新活动类型" : "添加活动类型";
      // 查看单个活动类型
      $activityTypeManage.fetch(id).then(function (data) {
        $scope.entity = data;
        // $scope.selectedDirection = $scope.entity.status;
        // console.log(data);
      }, function (err) {
        $location.path('/activity-type');
      });
      // 修改状态
      $scope.availableDirections = ['0', '10', '20'];
      // $scope.selectedDirection = $scope.entity.status;
      // console.log($scope.entity);
      // $scope.selectedDirection = 10;
      // 保存活动类型
      $scope.save = function () {
        var entity = $scope.entity;
        var newEntity = {name: entity.name, display_order: entity.display_order, status:entity.status};
        if (entity.id > 0) { // 更新
          $activityTypeManage.update(entity.id, newEntity).then(function (data) {
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
        } else { // 添加
          $activityTypeManage.create(newEntity).then(function (data) {
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
        }
      };

      // 在修改页面点击取消
      $scope.cancel = function () {
        $location.path('/activity-type');
      }
    }]);


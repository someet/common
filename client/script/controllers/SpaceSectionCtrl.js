angular.module('controllers')
  .controller('SpaceSectionListCtrl',
    ['$scope', '$http', '$routeParams', '$location', '$spaceSectionManage', 'lodash', '$mdToast', '$mdDialog',
      function ($scope, $http, $routeParams, $location, $spaceSectionManage, lodash, $mdToast, $mdDialog) {

        $scope.$parent.pageName = '场地区间管理';

        // 区间列表
        var spot_id = $routeParams.spot_id;
        $spaceSectionManage.listBySpotId(spot_id).then(function(data) {
          $scope.list = data;
        }, function(err) {
          alert(err);
        });

        // 跳转到更新类型页面
        $scope.update = function (type) {
          $location.path('/space-section/' + type.id);
        };

        // 删除场地区间
        $scope.delete = function (type) {
          var confirm = $mdDialog.confirm()
            .title('确定要删除场地区间“' + type.name + '”吗？')
            .ariaLabel('delete section item')
            .ok('确定删除')
            .cancel('手滑点错了，不删');

          $mdDialog.show(confirm).then(function () {
            $spaceSectionManage.delete(type).then(function (data) {
              lodash.remove($scope.list, function (tmpRow) {
                return tmpRow == type;
              });

              $mdToast.show($mdToast.simple()
                .content('删除场地区间“' + type.name + '”成功')
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
          $location.path('/space-section/add/'+spot_id);
        }
      }])
  .controller('SpaceSectionViewCtrl',
    ['$scope', '$http', '$routeParams', '$location', '$spaceSectionManage', '$mdToast',
      function ($scope, $http, $routeParams, $location, $spaceSectionManage, $mdToast) {

        // 获取GET参数的id
        var id = $routeParams.id;

        var spot_id = $routeParams.spot_id;
        $scope.spot_id = spot_id;

        $scope.$parent.pageName = id>0 ? "更新场地区间" : "添加场地区间";
        if (id>0) {
          // 查看单个场地区间
          $spaceSectionManage.fetch(id).then(function (data) {
            $scope.entity = data;
          }, function (err) {
            $location.path('/space-section/list/'+spot_id);
          });
        }

        // 修改状态
        $scope.availableDirections = ['0', '10'];
        // 保存场地区间
        $scope.save = function () {
          var entity = $scope.entity;
          var spot_id = $scope.spot_id;
          var newEntity = {spot_id: spot_id, name: entity.name, people: entity.people, status:entity.status};
          if (entity.id > 0) { // 更新
            $spaceSectionManage.update(entity.id, newEntity).then(function (data) {
              $location.path('/space-section/list/'+spot_id);
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
            $spaceSectionManage.create(newEntity).then(function (data) {
              $location.path('/space-section/list/'+spot_id);
              $mdToast.show($mdToast.simple()
                .content('添加场地区间成功')
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
          $location.path('/space-section/list/'+spot_id);
        }
      }]);


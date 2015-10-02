angular.module('controllers')
  .controller('ActivityTypeCtrl',
  ['$scope', '$http', '$location', '$activityTypeManage', 'lodash',
    function ($scope, $http, $location, $activityTypeManage, lodash) {

      $activityTypeManage.fetch().then(function (data) {
        $scope.list = data;
      }, function (err) {
        alert(err);
      });

      $scope.update = function (type) {
        $location.path('/activity-type/' + type.id);
      };

      $scope.delete = function (type) {
        $activityTypeManage.delete(type).then(function (data) {
          alert('删除成功');
          lodash.remove($scope.list, function(tmpRow) {
            return tmpRow == type;
          });
        }, function (err) {
          alert(err);
        });
      };

      $scope.create = function () {
        var newType = {
          name: $scope.name
        };
        $activityTypeManage.create(newType).then(function (data) {
          alert('添加成功');
          $scope.list.push(data);
        }, function (err) {
          alert(err);
        });
      };
    }])
  .controller('ActivityTypeViewCtrl',
  ['$scope', '$http', '$routeParams', '$location', '$activityTypeManage',
    function ($scope, $http, $routeParams, $location, $activityTypeManage) {
      var id = $routeParams.id;

      $activityTypeManage.fetch(id).then(function (data) {
        $scope.typeContent = data;
      }, function (err) {
        $location.path('/activity-type');
      });

      $scope.save = function () {
        var type = $scope.typeContent;
        var newType = {name: type.name, displayorder: type.displayorder};
        $activityTypeManage.update(type.id, newType).then(function (data) {
          alert('修改成功');
          $location.path('/activity-type');
        }, function (err){
          alert(err);
        })
      };
    }]);

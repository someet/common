angular.module('controllers')
  .controller('ActivityLogCtrl',
    ['$scope', '$location', '$routeParams', '$activityManage', '$mdToast',
      function ($scope, $location, $routeParams, $activityManage, $mdToast) {

        var activity_id = $routeParams.id;
        $activityManage.fetchLogByActivityId(activity_id).then(function (data) {
          console.log(data);
          $scope.list = data;
        });
  }]);
angular.module('controllers')
.controller('DashboardCtrl', ['$scope', function($scope){
  $scope.$parent.pageName = 'Dashboard';

  $scope.clickMe = function() {
    alert('clicked!');
  }
}]);

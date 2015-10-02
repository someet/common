angular.module('controllers')
.controller('DashboardCtrl', ['$scope', function($scope){

  $scope.clickMe = function() {
    alert('clicked!');
  }
}]);

angular.module('controllers')
  .controller('MainCtrl', ['$scope', '$mdSidenav', '$location', function($scope, $mdSidenav, $location) {
    $scope.toggleSidenav = function(menuId) {
      $mdSidenav(menuId).toggle();
    };

    $scope.isActive = function(route) {
      return $location.path().indexOf(route) != -1;
    }
  }]);
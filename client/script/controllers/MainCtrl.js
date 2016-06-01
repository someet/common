angular.module('controllers')
  .controller('MainCtrl', ['$scope', '$rootScope','$mdSidenav', '$location','$userManage', function($scope, $rootScope, $mdSidenav, $location, $userManage) {
    $scope.toggleSidenav = function(menuId) {
      $mdSidenav(menuId).toggle();
    };

        $userManage.fetchUserRole().then(function(data){
		      if (data) {
		      	$location.path('dashboard');
		      }else{
		      	$location.path('founder');		      	
		      }
		});
    $scope.isActive = function(route) {
      return $location.path().indexOf(route) != -1;
    }
  }]);
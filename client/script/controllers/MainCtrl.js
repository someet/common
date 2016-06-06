angular.module('controllers')
  .controller('MainCtrl', ['$scope', '$location','$routeParams','$rootScope','$mdSidenav', '$location','$userManage', function($scope, $location, $routeParams, $rootScope, $mdSidenav, $location, $userManage) {
    $scope.toggleSidenav = function(menuId) {
      $mdSidenav(menuId).toggle();
    };

    // 判断angular是否带参数
	if ($location.url() == '') {
		$userManage.fetchUserRole().then(function(data){
	      if (data) {
	      	$location.path('dashboard');
	      }else{
	      	$location.path('founder');		      	
	      }
		});
	}

    $scope.isActive = function(route) {
      return $location.path().indexOf(route) != -1;
    }
  }]);
angular.module('backendServices')
	.factory('$shareManage' ,['$http' ,'$rootScope' ,function($http,$rootScope){
		return {
			fetch:function(){
				return $http.get('/share/index').then(function (data){
					return data;
				})
			},
			update:function (entity) {
				return $http.post('/share/update',entity).then(function (data){
					return data;
				})
			},
		}
	}])
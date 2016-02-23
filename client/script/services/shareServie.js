angular.module('backendServices')
	.factory('$shareManage' ,['$http' ,'$rootScope' ,function($http,$rootScope){
		return {
			fetchList:function(){
				return $http.get('/share/list').then(function (data){
					return data;
				})
			},			
			fetch:function(id){
				return $http.get('/share/index?id='+id).then(function (data){
					return data;
				})
			},
			update:function (entity) {
				console.log(entity);
				return $http.post('/share/update?id='+entity.id,entity).then(function (data){
					return data;
				})
			},
			create:function (entity) {
				return $http.post('/share/create',entity).then(function (data){
					return data;
				})
			}
		}
	}])
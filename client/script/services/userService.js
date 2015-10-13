angular.module('backendServices')
  .factory('$userManage', ['$http', '$q', function($http, $q) {
    return {
      fetch: function(params) {
        return $http.get('/user', {
          params: params
        }).then(function(userList) {
          return userList;
        });
      },
      add: function(newEntity) {
        return $http.post('/user/create', newEntity).then(function(data) {
          return data;
        });
      },
      update: function(id, entity) {

        return $http.post('/user/update?id='+id, entity).then(function(data){
          return data;
        });
      },
      delete: function(id) {
        var entity = {
          status: 0
        }
        return $http.post('/user/update?id='+id, entity).then(function(data){
          return data;
        });
      }
    };
  }]);
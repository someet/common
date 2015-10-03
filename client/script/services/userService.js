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
      add: function(newUser) {
        return $http.post('/user/create', newUser).then(function(data) {
          return data;
        });
      },
      update: function(userId, userInfo) {

        return $http.post('/user/update?id='+userId, userInfo).then(function(data){
          return data;
        });
      },
      delete: function(userId) {
        var userInfo = {
          status: 0
        }
        return $http.post('/user/update?id='+userId, userInfo).then(function(data){
          return data;
        });
      }
    };
  }]);
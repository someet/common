angular.module('backendServices')
  .factory('$userManage', ['$http', '$q', '$rootScope', function($http, $q, $rootScope) {
    return {
      fetch: function() {
        return $http.post('/user/fetch', null).then(function(data) {
          return data;
        });
      },
      add: function(newUser) {
        return $http.post('/user/add', newUser).then(function(data) {
          alert("添加成功");
        });
      }
    };
  }]);
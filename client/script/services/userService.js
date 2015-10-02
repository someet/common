angular.module('backendServices')
  .factory('$userManage', ['$http', '$q', '$rootScope', function($http, $q, $rootScope) {
    return {
      add: function(newUser) {
        return $http.post('/user/add', newUser).then(function(data) {
          alert("添加成功");
        });
      }
    };
  }]);
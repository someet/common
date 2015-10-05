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
      },
      userPageMeta: function() {
        return $http.get('/user?scenario=total').then(function(data) {
          return data.total;
        });
      },
      fetchPage: function(page) {
        page = page || 1;

        var params = {
          'page': page,
          'per-page': 2
        };

        return $http.get('/user?scenario=page', {
          params: params
        }).then(function(data) {

          return data;
        });

      }
    };
  }]);
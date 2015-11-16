angular.module('backendServices')
  .factory('$userManage', ['$http', '$q', function($http, $q) {
    return {
      fetch: function(params) {
        return $http.get('/member', {
          params: params
        }).then(function(userList) {
          return userList;
        });
      },
      add: function(newEntity) {
        return $http.post('/member/create', newEntity).then(function(data) {
          return data;
        });
      },
      update: function(id, entity) {

        return $http.post('/member/update?id='+id, entity).then(function(data){
          return data;
        });
      },
      delete: function(id) {
        var entity = {
          status: 0
        }
        return $http.post('/member/update?id='+id, entity).then(function(data){
          return data;
        });
      },
      userPageMeta: function(pageNum) {

        return $http.get('/member?scenario=total&perPage='+pageNum).then(function(data) {
          return data;
        });
      },
      fetchPage: function(page) {
        page = page || 1;

        var params = {
          'page': page,
          'per-page': 20
        };

        return $http.get('/member?scenario=page', {
          params: params
        }).then(function(data) {

          return data;
        });

      }
    };
  }]);
angular.module('backendServices')
  .factory('$spaceTypeManage', ['$http', '$q', '$rootScope', function ($http, $q, $rootScope) {
    return {
      create: function (newEntity) {
        return $http.post('/space-type/create', newEntity).then(function (data) {
          return data;
        });
      },
      delete: function (entity) {
        return $http.post('/space-type/delete?id=' + entity.id, {}).success(function (data) {
          return data;
        });
      },
      update: function (id, newEntity) {
        return $http.post('/space-type/update?id=' + id, newEntity).then(function (data) {
          return data;
        });
      },
      fetch: function (id) {
        var url = typeof id == 'undefined' ? '/space-type/index' : '/space-type/view?id=' + id;

        return $http.get(url).then(function (data) {
          return data;
        });
      }
    };
  }]);
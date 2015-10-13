angular.module('backendServices')
    .factory('$specialManage', ['$http', '$q', '$rootScope', function ($http, $q, $rootScope) {
      return {
        create: function (newEntity) {
          return $http.post('/special/create', newEntity).then(function (data) {
            return data;
          });
        },
        delete: function (entity) {
          return $http.post('/special/delete?id=' + entity.id, {}).success(function (data) {
            return data;
          });
        },
        update: function (id, newEntity) {
          return $http.post('/special/update?id=' + id, newEntity).then(function (data) {
            return data;
          });
        },
        fetch: function (id) {
          var url = typeof id == 'undefined' ? '/special/index' : '/special/view?id=' + id;

          return $http.get(url).then(function (data) {
            return data;
          });
        }
      };
    }]);
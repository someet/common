angular.module('backendServices')
    .factory('$specialManage', ['$http', '$q', '$rootScope', function ($http, $q, $rootScope) {
      var moduleName = 'special';
      return {
        create: function (newEntity) {
          return $http.post('/' + moduleName + '/create', newEntity).then(function (data) {
            return data;
          });
        },
        delete: function (entity) {
          return $http.post('/' + moduleName + '/delete?id=' + entity.id, {}).success(function (data) {
            return data;
          });
        },
        update: function (id, newEntity) {
          return $http.post('/' + moduleName + '/update?id=' + id, newEntity).then(function (data) {
            return data;
          });
        },
        fetch: function (id) {
          var url = typeof id == 'undefined' ? '/' + moduleName + '/index' : '/' + moduleName + '/view?id=' + id;

          return $http.get(url).then(function (data) {
            return data;
          });
        }
      };
    }]);
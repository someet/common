angular.module('backendServices')
    .factory('$questionManage', ['$http', '$q', '$rootScope', function ($http, $q, $rootScope) {
      return {
        create: function (newEntity) {
          return $http.post('/question/create', newEntity).then(function (data) {
            return data;
          });
        },
        delete: function (entity) {
          return $http.post('/question/delete?id=' + entity.id, {}).success(function (data) {
            return data;
          });
        },
        update: function (id, newEntity) {
          return $http.post('/question/update?id=' + id, newEntity).then(function (data) {
            return data;
          });
        },
        fetch: function (id) {
          var url = typeof id == 'undefined' ? '/question/index' : '/question/view?id=' + id;

          return $http.get(url).then(function (data) {
            return data;
          });
        }
      };
    }]);
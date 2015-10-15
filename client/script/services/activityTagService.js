angular.module('backendServices')
  .factory('$activityTagManage', ['$http', '$q', '$rootScope', function ($http, $q, $rootScope) {
    return {
      create: function (newEntity) {
        return $http.post('/activity-tag/create', newEntity).then(function (data) {
          return data;
        });
      },
      delete: function (entity) {
        return $http.post('/activity-tag/delete?id=' + entity.id, {}).success(function (data) {
          return data;
        });
      },
      update: function (id, newEntity) {
        return $http.post('/activity-tag/update?id=' + id, newEntity).then(function (data) {
          return data;
        });
      },
      fetch: function (id) {
        var url = typeof id == 'undefined' ? '/activity-tag/index' : '/activity-tag/view?id=' + id;

        return $http.get(url).then(function (data) {
          return data;
        });
      }
    };
  }]);
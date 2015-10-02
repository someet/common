angular.module('backendServices')
  .factory('$activityTypeManage', ['$http', '$q', '$rootScope', function ($http, $q, $rootScope) {
    return {
      create: function (newType) {
        return $http.post('/activity-type/create', newType).then(function (data) {
          return data;
        });
      },
      delete: function (type) {
        return $http.post('/activity-type/delete?id=' + type.id, {}).success(function (data) {
          return data;
        });
      },
      update: function (typeId, newType) {
        return $http.post('/activity-type/update?id=' + typeId, newType).then(function (data) {
          return data;
        });
      },
      fetch: function (id) {
        var url = typeof id == 'undefined' ? '/activity-type/index' : '/activity-type/view?id=' + id;

        return $http.get(url).then(function (data) {
          return data;
        });
      }
    };
  }]);
angular.module('backendServices')
  .factory('$spaceSectionManage', ['$http', '$q', '$rootScope', function ($http, $q, $rootScope) {
    return {
      create: function (newEntity) {
        return $http.post('/space-section/create', newEntity).then(function (data) {
          return data;
        });
      },
      delete: function (entity) {
        return $http.post('/space-section/delete?id=' + entity.id, {}).success(function (data) {
          return data;
        });
      },
      update: function (id, newEntity) {
        return $http.post('/space-section/update?id=' + id, newEntity).then(function (data) {
          return data;
        });
      },
      listBySpotId: function(spot_id) {
        return $http.post('/space-section/list-by-spot-id?spot_id=' + spot_id).then(function (data) {
          return data;
        });
      },
      fetch: function (id) {
        var url = typeof id == 'undefined' ? '/space-section/index' : '/space-section/view?id=' + id;

        return $http.get(url).then(function (data) {
          return data;
        });
      }
    };
  }]);
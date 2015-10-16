angular.module('backendServices')
  .factory('$activityFeedbackManage', ['$http', '$q', '$rootScope', function ($http, $q, $rootScope) {
    return {
      // create: function (newEntity) {
      //   return $http.post('/activity-feedback/create', newEntity).then(function (data) {
      //     return data;
      //   });
      // },
      delete: function (entity) {
        return $http.post('/activity-feedback/delete?id=' + entity.id, {}).success(function (data) {
          return data;
        });
      },
      update: function (id, newEntity) {
        return $http.post('/activity-feedback/update?id=' + id, newEntity).then(function (data) {
          return data;
        });
      },
      fetch: function (id) {
        var url = typeof id == 'undefined' ? '/activity-feedback/index' : '/activity-feedback/view?id=' + id;

        return $http.get(url).then(function (data) {
          return data;
        });
      }
    };
  }]);
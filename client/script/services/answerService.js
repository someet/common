angular.module('backendServices')
    .factory('$answerManage', ['$http', '$q', '$rootScope', function ($http, $q, $rootScope) {
      return {
        create: function (newEntity) {
          return $http.post('/answer/create', newEntity).then(function (data) {
            return data;
          });
        },
        delete: function (entity) {
          return $http.post('/answer/delete?id=' + entity.id, {}).success(function (data) {
            return data;
          });
        },
        update: function (id, newEntity) {
          return $http.post('/answer/update?id=' + id, newEntity).then(function (data) {
            return data;
          });
        },
        fetch: function (id) {
          var url = typeof id == 'undefined' ? '/answer/index' : '/answer/view?id=' + id;

          return $http.get(url).then(function (data) {
            return data;
          });
        },
        fetchByActivityId: function (activity_id) {
          return $http.get('/answer/view-by-activity-id?activity_id=' + activity_id).then(function (data) {
            return data;
          });
        }
      };
    }]);
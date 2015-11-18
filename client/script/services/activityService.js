angular.module('backendServices')
    .factory('$activityManage', ['$http', '$q', '$rootScope', function ($http, $q, $rootScope) {
      return {
        create: function (newEntity) {
          return $http.post('/activity/create', newEntity).then(function (data) {
            return data;
          });
        },
        delete: function (entity) {
          return $http.post('/activity/delete?id=' + entity.id, {}).success(function (data) {
            return data;
          });
        },
        update: function (id, newEntity) {
          return $http.post('/activity/update?id=' + id, newEntity).then(function (data) {
            return data;
          });
        },
        fetch: function (id) {
          var url = typeof id == 'undefined' ? '/activity/index' : '/activity/view?id=' + id;

          return $http.get(url).then(function (data) {
            return data;
          });
        },
        listByType: function(type_id) {
          return $http.get('/activity/list-by-type-id?type_id=' + type_id).then(function(data) {
            return data;
          });
        },
        tags: function(query) {
          return $http.get('/activity-tag/list?query=' + query);
        },
        searchUser: function(query) {
          return $http.get('/member/search?username=' + query);
        },
      };
    }]);
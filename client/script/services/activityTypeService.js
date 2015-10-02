angular.module('backendServices')
  .factory('$activityTypeManage', ['$http', '$q', '$rootScope', function ($http, $q, $rootScope) {
    return {
      create: function (newType) {
        return $http.post('/activity-type/create', newType).then(function (response) {
          if (typeof response === 'object' && response.data.success == "1") {
            return response.data.data;
          }
          return $q.reject(response.data.errmsg);
        }, function (err) {
          return $q.reject(err.statusText);
        });
      },
      delete: function (type) {
        return $http.post('/activity-type/delete?id=' + type.id, {}).success(function (response) {
          if (typeof response === 'object' && response.data.success == "1") {
            return response.data.data;
          }
          return $q.reject(response.data.errmsg);
        }, function (err) {
          return $q.reject(err.statusText);
        });
      },
      update: function (typeId, newType) {
        return $http.post('/activity-type/update?id=' + typeId, newType).then(function (response) {
          if (typeof response === 'object' && response.data.success == "1") {
            return response.data.data;
          }
          return $q.reject(response.data.errmsg);
        }, function (err) {
          return $q.reject(err.statusText);
        });
      },
      fetch: function (id) {
        var url = typeof id == 'undefined' ? '/activity-type/index' : '/activity-type/view?id=' + id;

        return $http.get(url).then(function (response) {
          if (typeof response === 'object' && response.data.success == "1") {
            return response.data.data;
          }
          return $q.reject(response.data.errmsg);
        }, function (err) {
          return $q.reject(err.statusText);
        });
      }
    };
  }]);
angular.module('backendServices')
  .factory('$spaceSpotDeviceManage', ['$http', '$q', '$rootScope', function ($http, $q, $rootScope) {
    return {
      modelPageMeta: function(pageNum) {
        return $http.get('/space-spot-device?scenario=total&perPage='+pageNum).then(function(data) {
          return data;
        });
      },
      fetchPage: function(page) {
        page = page || 1;

        var params = {
          'page': page,
          'perPage': 20  //每页20条
        };

        return $http.get('/space-spot-device?scenario=page', {
          params: params
        }).then(function (data) {

          return data;
        });
      },

        create: function (newEntity) {
        return $http.post('/space-spot-device/create', newEntity).then(function (data) {
          return data;
        });
      },
      delete: function (entity) {
        return $http.post('/space-spot-device/delete?id=' + entity.id, {}).success(function (data) {
          return data;
        });
      },
      update: function (id, newEntity) {
        return $http.post('/space-spot-device/update?id=' + id, newEntity).then(function (data) {
          return data;
        });
      },
      fetch: function (id) {
        var url = typeof id == 'undefined' ? '/space-spot-device/index' : '/space-spot-device/view?id=' + id;

        return $http.get(url).then(function (data) {
          return data;
        });
      }
    };
  }]);
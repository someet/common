angular.module('backendServices')
  .factory('$smsTemplateManage', ['$http', '$q', '$rootScope', function ($http, $q, $rootScope) {
    return {
      create: function (newEntity) {
        return $http.post('/sms-template/create', newEntity).then(function (data) {
          return data;
        });
      },
      delete: function (entity) {
        return $http.post('/sms-template/delete?id=' + entity.id, {}).success(function (data) {
          return data;
        });
      },
      update: function (id, newEntity) {
        return $http.post('/sms-template/update?id=' + id, newEntity).then(function (data) {
          return data;
        });
      },
      fetch: function (id) {
        var url = typeof id == 'undefined' ? '/sms-template/index' : '/sms-template/view?id=' + id;

        return $http.get(url).then(function (data) {
          return data;
        });
      }
    };
  }]);
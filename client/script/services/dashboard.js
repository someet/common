angular.module('backendServices')
  .factory('$dashboardManage', ['$http', '$q', '$rootScope', function ($http, $q, $rootScope) {
    return {
      fetch: function () {
        var url = '/site/fetch';

        return $http.get(url).then(function (data) {
          return data;
        });
      }
    };
  }]);
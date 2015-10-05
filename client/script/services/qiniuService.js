angular.module('backendServices').factory('$qiniuManage', [
  '$http', '$q', '$rootScope', 'localStorageService',
  function ($http, $q, $rootScope, localStorageService) {
    return {
      completelyUrl: function (key) {
        return $http.post('/qiniu/create-completely-url', {key: key}).then(function (data) {
          return data.url;
        });
      },
      fetchUploadToken: function (force) {
        return $http.get('/qiniu/get-upload-token').then(function (data) {
          return data.token;
        });
      }
    };
  }]);
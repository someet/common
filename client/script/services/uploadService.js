angular.module('backendServices')
  .factory('$uploadService',
  ['$http', '$q', 'Upload', '$mdToast',
    function($http, $q, Upload, $mdToast) {
      var token,
        threshold = 1000 * 60 * 60,
        timestamp = Date.now(),
        toast;

      return {
        getUploadToken: function() {
          var now = Date.now();

          if (angular.isDefined(token) && (now - timestamp < threshold)) {
            return $q.when(token);
          } else {
            return $http.get('/qiniu/upload-token', {
              params: {
                t: new Date().getTime()
              }
            }).then(function(data) {
              token = data.token;
              timestamp = Date.now();

              return token;
            });
          }
        },
        upload: function(file) {
          toast = $mdToast.show($mdToast.simple({
            position: 'bottom right',
            hideDelay: false
          }).content('图片上传中'));

          return this.getUploadToken().then(function(uploadToken) {
            return Upload.upload({
              url: 'http://up.qiniu.com',
              fields: {
                token: uploadToken
              },
              file: file
            });
          }).then(function(response) {
            $mdToast.hide(toast);

            return response.key;
          }, function(err) {
            $mdToast.hide(toast);
          });
        }
      };
    }]);

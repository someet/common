// custom text angular image insert image link and video insert
angular.module('someetTextAngular', ['textAngular'])
  .config(['$provide', function($provide) {

    $provide.decorator('taOptions', ['$delegate', 'taRegisterTool', '$uploadService', '$http', '$window', '$mdToast',
      function(taOptions, taRegisterTool, $uploadService, $http, $window, $mdToast) {
        var imgSize = 300000,
          imgSizeKb = (imgSize / 1000) + 'KB';

        //taOptions.toolbar = [
        //  ['redo', 'undo', 'justifyLeft','justifyCenter','justifyRight','html', 'tudouVideo']
        //];

        taOptions.defaultFileDropHandler = function(file, insertAction) {
          if (file.size >= imgSize) {
            $mdToast.show($mdToast.simple({
              position: 'bottom right'
            }).content('图片的大小超过' + imgSizeKb + '限制'));

            return;
          }

          $uploadService.upload(file).then(function(filename) {
            var imgsrc = 'http://7te94f.com2.z0.glb.qiniucdn.com/' + filename;

            insertAction('insertImage', imgsrc, true);
          });

          return true;
        };


        return taOptions;
      }]);


  }]);


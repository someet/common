angular.module('controllers')
    .controller('SpecialCtrl',
    ['$scope', '$http', '$location', '$specialManage', 'lodash', '$mdToast', '$mdDialog',
      function ($scope, $http, $location, $specialManage, lodash, $mdToast, $mdDialog) {

        $scope.$parent.pageName = '专题管理';
        $specialManage.fetch().then(function (data) {
          $scope.list = data;
        }, function (err) {
          alert(err);
        });

        // 置顶/取消置顶
        $scope.top = function(entity, is_top) {
          var newEntity = entity;
          newEntity.is_top = is_top > 0 ? 1 : 0; // 是否置顶
          $specialManage.update(entity.id, newEntity).then(function(data){
            $location.path('/special');
            $mdToast.show($mdToast.simple()
                .content('置顶成功')
                .hideDelay(5000)
                .position("top right"));
          }, function(err) {
            $mdToast.show($mdToast.simple()
                .content(err.toString())
                .hideDelay(5000)
                .position("top right"));
          })
        }

        $scope.update = function (entity) {
          $location.path('/special/' + entity.id);
        };

        $scope.delete = function (entity) {

          var confirm = $mdDialog.confirm()
              .title('确定要删除专题“' + entity.title + '”吗？')
              .ariaLabel('delete special')
              .ok('确定删除')
              .cancel('手滑点错了，不删');

          $mdDialog.show(confirm).then(function () {
            $specialManage.delete(entity).then(function (data) {

              lodash.remove($scope.list, function (tmpRow) {
                return tmpRow == entity;
              });

              $mdToast.show($mdToast.simple()
                  .content('删除专题“' + entity.title + '”成功')
                  .hideDelay(5000)
                  .position("top right"));

            }, function (err) {
              $mdToast.show($mdToast.simple()
                  .content(err.toString())
                  .hideDelay(5000)
                  .position("top right"));
            });
          });
        };
        $scope.createPage = function () {
          $location.path('/special/add');
        }
      }])
    .controller('SpecialViewCtrl',
    ['$scope', '$http', '$routeParams', '$location', '$specialManage', '$qupload', '$qiniuManage', '$mdToast',
      function ($scope, $http, $routeParams, $location, $specialManage, $qupload, $qiniuManage, $mdToast) {
        var id = $routeParams.id;

        $scope.$parent.pageName = id>0 ? "更新专题" : "添加专题";
        $scope.selectFile = null ;

        var start = function () {
          $qiniuManage.fetchUploadToken().then(function (token) {

            $qupload.upload({
              key: '',
              file: $scope.selectFile.file,
              token: token
            }).then(function (response) {
              console.log(response);
              $qiniuManage.completelyUrl(response.key).then(function(url) {
                $scope.entity.poster = url;
              });
            }, function (response) {
              console.log(response);
            }, function (evt) {
              if($scope.selectFile !== null){
                $scope.selectFile.progress.p = Math.floor(100 * evt.loaded / evt.totalSize);
              }
            });

          });
        };

        $scope.abort = function () {
          $scope.selectFile.upload.abort();
          $scope.selectFile = null;
        };

        $scope.onFileSelect = function ($files) {
          $scope.selectFile = {
            file: $files[0],
            progress: {p: 0}
          };
          start();
        };

        $specialManage.fetch(id).then(function (data) {
          $scope.entity = data;
        }, function (err) {
          $location.path('/special');
        });



        // 新建或修改
        $scope.save = function () {
          var entity = $scope.entity;
          var newEntity = {title: entity.title, desc: entity.desc, poster: entity.poster, display_order: entity.display_order};
          if (entity.id > 0) {
            $specialManage.update(entity.id, newEntity).then(function (data) {
              $location.path('/special');
              $mdToast.show($mdToast.simple()
                  .content('修改成功')
                  .hideDelay(5000)
                  .position("top right"));
            }, function (err) {
              $mdToast.show($mdToast.simple()
                  .content(err.toString())
                  .hideDelay(5000)
                  .position("top right"));
            })
          } else {
            $specialManage.create(newEntity).then(function (data) {
              $location.path('/special');
              $mdToast.show($mdToast.simple()
                  .content('添加成功')
                  .hideDelay(5000)
                  .position("top right"));
            }, function (err) {
              $mdToast.show($mdToast.simple()
                  .content(err.toString())
                  .hideDelay(5000)
                  .position("top right"));
            })
          }
        };

        $scope.cancel = function () {
          $location.path('/special');
        }
      }]);

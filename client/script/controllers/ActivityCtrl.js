angular.module('controllers')
    .controller('ActivityCtrl',
    ['$scope', '$location', '$activityManage', '$mdDialog', 'lodash', '$mdToast',
        function ($scope, $location, $activityManage, $mdDialog, lodash, $mdToast) {

            $scope.$parent.pageName = '活动管理';
            $activityManage.fetch().then(function (data) {
                $scope.list = data;
            }, function (err) {
                alert(err);
            });

          // 置顶/取消置顶
          $scope.top = function(entity, is_top) {
            var newEntity = entity;
            newEntity.is_top = is_top > 0 ? 1 : 0; // 是否置顶
            $activityManage.update(entity.id, newEntity).then(function(data){
              $mdToast.show($mdToast.simple()
                  .content('置顶成功')
                  .hideDelay(5000)
                  .position("top right"));
              $location.path('/activity');
            }, function(err) {
              $mdToast.show($mdToast.simple()
                  .content(err.toString())
                  .hideDelay(5000)
                  .position("top right"));
            })
          }

            $scope.update = function (entity) {
                $location.path('/activity/' + entity.id);
            };

            $scope.delete = function (entity) {

                var confirm = $mdDialog.confirm()
                    .title('确定要删除活动“' + entity.title + '”吗？')
                    .ariaLabel('delete activity item')
                    .ok('确定删除')
                    .cancel('手滑点错了，不删');

                $mdDialog.show(confirm).then(function () {
                    $activityManage.delete(entity).then(function (data) {

                        lodash.remove($scope.list, function (tmpRow) {
                            return tmpRow == entity;
                        });

                        $mdToast.show($mdToast.simple()
                            .content('删除活动类型“' + entity.title + '”成功')
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
            $scope.createPage = function() {
                $location.path('/activity/add');
            }
          // 报名表单
          $scope.viewQuestion = function(activity) {
            $location.path('/question/view/' + activity.id);
          }

          $scope.viewAnswer = function(activity) {
            $location.path('/answer//' + activity.id);
          }

        }])
    .controller('ActivityViewCtrl',
    ['$scope', '$routeParams', '$location', '$activityManage', '$activityTypeManage', '$qupload', '$qiniuManage', '$mdToast',
        function ($scope, $routeParams, $location, $activityManage, $activityTypeManage, $qupload, $qiniuManage, $mdToast) {
            $scope.$parent.pageName = '活动详情';

          // qiniu upload start //
          $scope.selectFile = null ;

          var start = function () {
            $qiniuManage.fetchUploadToken().then(function (token) {

              $qupload.upload({
                key: '',
                file: $scope.selectFile.file,
                token: token
              }).then(function (response) {
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

          // qiniu upload end //
            var id = $routeParams.id;
            if(id>0) {
              $activityManage.fetch(id).then(function (data) {
                $scope.entity = data;
              }, function (err) {
                  $location.path('/activity');
              });
            }

          // 列表
            $activityTypeManage.fetch().then(function(data){
                $scope.typelist = data;
            });

          // 取消
            $scope.cancel = function() {
                $location.path('/activity/');
            }

            $scope.save = function () {
                var newEntity = $scope.entity;
                if (newEntity.id > 0 ) {
                    $activityManage.update(newEntity.id, newEntity).then(function(data) {
                        $mdToast.show($mdToast.simple()
                            .content('活动保存成功')
                            .hideDelay(5000)
                            .position("top right"));
                        $location.path('/activity');
                    }, function(err){
                        $mdToast.show($mdToast.simple()
                            .content(err.toString())
                            .hideDelay(5000)
                            .position("top right"));
                    });
                } else {
                    $activityManage.create(newEntity).then(function (data) {
                        $mdToast.show($mdToast.simple()
                            .content('活动添加成功')
                            .hideDelay(5000)
                            .position("top right"));
                        $location.path('/question/add/'+data.id);
                    }, function (err) {
                        $mdToast.show($mdToast.simple()
                            .content(err.toString())
                            .hideDelay(5000)
                            .position("top right"));
                    });
                }
            };
        }])
;

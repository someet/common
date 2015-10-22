angular.module('controllers', ['ngTagsInput'])
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

          // 预览问题
          $scope.previewQuestion = function(entity) {
            $location.path('/answer/view/'+entity.id);
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

          // 设置问题
          $scope.viewQuestion = function(activity) {
            $location.path('/question/view/' + activity.id);
          }

          $scope.viewAnswer = function(activity) {
            $location.path('/answer/' + activity.id);
          }

        }])
    .controller('ActivityViewCtrl',
    ['$scope', '$routeParams', '$location', '$activityManage', '$activityTypeManage', '$qupload', '$qiniuManage', '$mdToast',
        function ($scope, $routeParams, $location, $activityManage, $activityTypeManage, $qupload, $qiniuManage, $mdToast) {
            $scope.$parent.pageName = '活动详情';

          // 标签
          $scope.tags = [];
          // 标签搜索功能
          $scope.loadTags = function(query) {
            return $activityManage.tags(query);
          };

          // qiniu upload poster start //
          $scope.selectPoster = null ;

          var startPoster = function () {
            $qiniuManage.fetchUploadToken().then(function (token) {

              $qupload.upload({
                key: '',
                file: $scope.selectPoster.file,
                token: token
              }).then(function (response) {
                $qiniuManage.completelyUrl(response.key).then(function(url) {
                  $scope.poster = url;
                });
              }, function (response) {
                console.log(response);
              }, function (evt) {
                if($scope.selectPoster !== null){
                  $scope.selectPoster.progress.p = Math.floor(100 * evt.loaded / evt.totalSize);
                }
              });

            });
          };

          $scope.posterAbort = function () {
            $scope.selectPoster.upload.abort();
            $scope.selectPoster = null;
          };

          $scope.onPosterSelect = function ($files) {
            $scope.selectPoster = {
              file: $files[0],
              progress: {p: 0}
            };
            startPoster();
          };
          // qiniu upload poster end //

          // qiniu upload code start //
          $scope.selectCode = null ;

          var startCode = function () {
            $qiniuManage.fetchUploadToken().then(function (token) {

              $qupload.upload({
                key: '',
                file: $scope.selectCode.file,
                token: token
              }).then(function (response) {
                $qiniuManage.completelyUrl(response.key).then(function(url) {
                  $scope.group_code = url;
                });
              }, function (response) {
                console.log(response);
              }, function (evt) {
                if($scope.selectCode !== null){
                  $scope.selectCode.progress.p = Math.floor(100 * evt.loaded / evt.totalSize);
                }
              });

            });
          };

          $scope.codeAbort = function () {
            $scope.selectCode.upload.abort();
            $scope.selectCode = null;
          };

          $scope.onCodeSelect = function ($files) {
            $scope.selectCode = {
              file: $files[0],
              progress: {p: 0}
            };
            startCode();
          };
          // qiniu upload group code end //

            var id = $routeParams.id;
            if(id>0) {
              $activityManage.fetch(id).then(function (data) {
                $scope.entity = data;
                $scope.entity.start_time = getTimeByTimestamp(data.start_time);
                $scope.entity.end_time = getTimeByTimestamp(data.end_time);
                $scope.poster = data.poster;
                $scope.group_code = data.group_code;

                var tags = [];
                for(var k in data.tags) {
                  var tag = data.tags[k].name;
                  tags.push(tag);
                }
                $scope.tags = tags;

              }, function (err) {
                  $location.path('/activity');
              });
            }

          // 列表
            $activityTypeManage.fetch().then(function(data){
                $scope.type_list = data;
            });

          // 取消
            $scope.cancel = function() {
                $location.path('/activity/');
            }


            $scope.save = function () {
                var newEntity = $scope.entity;
                newEntity.start_time = getTimestamp($scope.entity.start_time);
                newEntity.end_time = getTimestamp($scope.entity.end_time);
                newEntity.poster = $scope.poster;
                newEntity.group_code = $scope.group_code;

                var tags = [];
                for(var k in $scope.tags) {
                  var tag = $scope.tags[k].text;
                  tags.push(tag);
                }
                newEntity.tagNames = tags.join();

                if (newEntity.id > 0 ) { // 更新活动
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
                } else { // 添加活动
                    $activityManage.create(newEntity).then(function (data) {
                      $location.path('/activity');
                        $mdToast.show($mdToast.simple()
                            .content('活动添加成功')
                            .hideDelay(5000)
                            .position("top right"));
                    }, function (err) {
                        $mdToast.show($mdToast.simple()
                            .content(err.toString())
                            .hideDelay(5000)
                            .position("top right"));
                    });
                }
            };

        }]);

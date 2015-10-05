angular.module('controllers')
    .controller('ActivityCtrl',
    ['$scope', '$location', '$activityManage', '$mdDialog', 'lodash', '$mdToast',
        function ($scope, $location, $activityManage, $mdDialog, lodash, $mdToast) {

            $scope.$parent.pageName = '活动管理';
            $activityManage.fetch().then(function (data) {
                console.log(data);
                $scope.list = data;
            }, function (err) {
                alert(err);
            });

            $scope.update = function (activity) {
                $location.path('/activity/' + activity.id);
            };

            $scope.delete = function (activity) {

                var confirm = $mdDialog.confirm()
                    .title('确定要删除活动“' + activity.title + '”吗？')
                    .ariaLabel('delete activity item')
                    .ok('确定删除')
                    .cancel('手滑点错了，不删');

                $mdDialog.show(confirm).then(function () {
                    $activityManage.delete(activity).then(function (data) {

                        lodash.remove($scope.list, function (tmpRow) {
                            return tmpRow == activity;
                        });

                        $mdToast.show($mdToast.simple()
                            .content('删除活动类型“' + activity.title + '”成功')
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
            $scope.createActivityPage = function() {
                $location.path('/activity/add');
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
                console.log(response);
                $qiniuManage.completelyUrl(response.key).then(function(url) {
                  $scope.poster = url;
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
                  console.log(data);
                  $scope.id = data.id;
                  $scope.title = data.title;
                  $scope.groupcode = data.groupcode;
                  $scope.details = data.details;
                  $scope.type_id = data.type_id;
                  $scope.desc = data.desc;
                  $scope.poster = data.poster;
                  $scope.area = data.area;
                  $scope.address = data.address;
              }, function (err) {
                  $location.path('/activity');
              });
            }

            $activityTypeManage.fetch().then(function(data){
                $scope.typelist = data;
                console.log(data);
            });

            $scope.cancel = function() {
                $location.path('/activity/');
            }

            $scope.create = function () {
                var newActivity = {
                    title: $scope.title,
                    desc: $scope.desc,
                    type_id: $scope.type_id,
                    groupcode: $scope.groupcode,
                    details: $scope.details,
                    address: $scope.address,
                    area: $scope.area,
                    poster: $scope.poster,
                };
                console.log(newActivity);
                if ($scope.id > 0 ) {
                    newActivity.id = $scope.id;
                    console.log(newActivity);
                    $activityManage.update($scope.id, newActivity).then(function(data) {
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
                    $activityManage.create(newActivity).then(function (data) {
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

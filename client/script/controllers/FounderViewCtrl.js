angular.module('controllers')
.controller('FounderViewCtrl', ['$scope', '$routeParams', '$location', '$founderManage', '$activityTypeManage', '$qupload', '$qiniuManage', '$mdToast',
        function($scope, $routeParams, $location, $founderManage, $activityTypeManage, $qupload, $qiniuManage, $mdToast) {
            $scope.$parent.pageName = '活动详情';
            // $scope.tips1 = "1.活动不允许空降,如果有同行的小伙伴请把活动分享给TA让TA自行报名哟;";
            // $scope.tips2 = "2.活动若无法照常参加,请进入Someet服务平台中的个人界面自行请假;";
            // $scope.tips3 = $scope.tips1 +$scope.tips2;

            // 默认用户
            $founderManage.defaultData().then(function(data){
            	$scope.user = data.user;
            });

            // 活动类型
            $activityTypeManage.fetch().then(function(data) {
                $scope.type_list = data;
                console.log(data);
            });

            // 开始时间
            $scope.onStartTimeSet = function(newDate, oldDate) {
                $scope.start_time_str = getTimeByTimestamp(getTimestamp(newDate));
                $scope.entity.start_time = getTimestamp(newDate);
            }

            // 结束时间
            $scope.onStopTimeSet = function(newDate, oldDate) {
                $scope.end_time_str = getTimeByTimestamp(getTimestamp(newDate));
                $scope.entity.end_time = getTimestamp(newDate);
            }

            // qiniu upload 海报 start //
            $scope.selectPoster = null;
            var startPoster = function() {
                $qiniuManage.fetchUploadToken().then(function(token) {

                    $qupload.upload({
                        key: '',
                        file: $scope.selectPoster.file,
                        token: token
                    }).then(function(response) {
                        $qiniuManage.completelyUrl(response.key).then(function(url) {
                            $scope.poster = url;
                        });
                    }, function(response) {}, function(evt) {
                        if ($scope.selectPoster !== null) {
                            $scope.selectPoster.progress.p = Math.floor(100 * evt.loaded / evt.totalSize);
                        }
                    });

                });
            };

            $scope.posterAbort = function() {
                $scope.selectPoster.upload.abort();
                $scope.selectPoster = null;
            };

            $scope.onPosterSelect = function($files) {
                $scope.selectPoster = {
                    file: $files[0],
                    progress: {
                        p: 0
                    }
                };
                startPoster();
            };
            // qiniu upload 海报 end //

            // 更新活动时设置默认值
            var id = $routeParams.id;
            if (id > 0) {
                $founderManage.fetch(id).then(function(data) {
                    $scope.entity = data;
                    // $scope.tips3 = $scope.entity.field2 + $scope.tips1 +$scope.tips2;
                    console.log($scope.tips3);
                    $scope.start_time_str = getTimeByTimestamp(data.start_time);
                    $scope.end_time_str = getTimeByTimestamp(data.end_time);
                    $scope.poster = data.poster;
                    // 转化为number类型
                    $scope.entity.cost = parseInt(data.cost);
                }, function(err) {
                    alert(err);
                });
            }

            //保存活动
            $scope.save = function() {
                var newEntity = $scope.entity;

                newEntity.start_time = $scope.entity.start_time;
                newEntity.end_time = $scope.entity.end_time;
                newEntity.poster = $scope.poster;
                newEntity.pma_type = $scope.entity.pma_type;
                newEntity.created_by = $scope.user.id;
                // newEntity.field2 = $scope.tips1 + $scope.tips2 + $scope.entity.field2;
                
                if (newEntity.id > 0) { // 更新活动
                    newEntity.field2 = $scope.entity.field2;
                    console.log(newEntity.field2);
                    $founderManage.update(newEntity.id, newEntity).then(function(data) {
                        $mdToast.show($mdToast.simple()
                            .content('活动保存成功')
                            .hideDelay(5000)
                            .position("top right"));
                    }, function(err) {
                        $mdToast.show($mdToast.simple()
                            .content(err.toString())
                            .hideDelay(5000)
                            .position("top right"));
                    });
                } else { // 添加活动
                    $founderManage.create(newEntity).then(function(data) {
                        $location.path('/founder');
                        $mdToast.show($mdToast.simple()
                            .content('活动添加成功')
                            .hideDelay(5000)
                            .position("top right"));
                    }, function(err) {
                        $mdToast.show($mdToast.simple()
                            .content(err.toString())
                            .hideDelay(5000)
                            .position("top right"));
                    });
                }
            };

        }
    ]);

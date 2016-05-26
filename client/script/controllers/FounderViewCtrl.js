angular.module('controllers')
.controller('FounderViewCtrl', ['$scope', '$routeParams', '$location', '$founderManage', '$activityTypeManage', '$qupload', '$qiniuManage', '$mdToast',
        function($scope, $routeParams, $location, $founderManage, $activityTypeManage, $qupload, $qiniuManage, $mdToast) {
            $scope.$parent.pageName = '活动详情';

            // 默认用户
            // $scope.user = 
            $founderManage.defaultData().then(function(data){
            	$scope.user = data.user;
            	console.log($scope.user);
            });
            // console.log($scope.defaultData);
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

            // qiniu upload 群二维码 start //
            $scope.selectCode = null;

            var startCode = function() {
                $qiniuManage.fetchUploadToken().then(function(token) {

                    $qupload.upload({
                        key: '',
                        file: $scope.selectCode.file,
                        token: token
                    }).then(function(response) {
                        $qiniuManage.completelyUrl(response.key).then(function(url) {
                            $scope.group_code = url;
                        });
                    }, function(response) {}, function(evt) {
                        if ($scope.selectCode !== null) {
                            $scope.selectCode.progress.p = Math.floor(100 * evt.loaded / evt.totalSize);
                        }
                    });

                });
            };

            $scope.codeAbort = function() {
                $scope.selectCode.upload.abort();
                $scope.selectCode = null;
            };

            $scope.onCodeSelect = function($files) {
                $scope.selectCode = {
                    file: $files[0],
                    progress: {
                        p: 0
                    }
                };
                startCode();
            };

            // qiniu upload 群二维码 end //

            // 更新活动时设置默认值
            var id = $routeParams.id;
            if (id > 0) {
                $founderManage.fetch(id).then(function(data) {
                    $scope.user = {};
                    $scope.entity = data;
                    $scope.start_time_str = getTimeByTimestamp(data.start_time);
                    $scope.end_time_str = getTimeByTimestamp(data.end_time);
                    $scope.poster = data.poster;
                    $scope.user = data.user;
                }, function(err) {
                    alert(err);
                });
            }

            // 列表
            $activityTypeManage.fetch().then(function(data) {
                $scope.type_list = data;
                console.log(data);
            });

            //保存活动
            $scope.save = function() {
                var newEntity = $scope.entity;
                newEntity.start_time = $scope.entity.start_time;
                newEntity.end_time = $scope.entity.end_time;
                newEntity.poster = $scope.poster;
                newEntity.pma_type = $scope.entity.pma_type;

                if ($scope.user) {
                    newEntity.created_by = $scope.user.id;
                }

                if (newEntity.id > 0) { // 更新活动
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
                        $location.path('/activity/list/0');
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

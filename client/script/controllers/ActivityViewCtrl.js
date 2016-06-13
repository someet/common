angular.module('controllers')
	.controller('ActivityViewCtrl', ['$scope', '$routeParams', '$location', '$activityManage', '$activityTypeManage', '$qupload', '$qiniuManage', '$mdToast','$mdDialog',
        function($scope, $routeParams, $location, $activityManage, $activityTypeManage, $qupload, $qiniuManage, $mdToast, $mdDialog) {
            $scope.$parent.pageName = '活动详情';

            // 添加发起人
            $scope.founder = [];
            $scope.addFounder = function(obj) {
                if ($scope.user == null) {
                    $mdToast.show($mdToast.simple()
                        .content('请先添加发起人')
                        .hideDelay(5000)
                        .position("top right"));
                    return false;
                }else if (obj == null) {
                    $mdToast.show($mdToast.simple()
                        .content('联合发起人不能为空')
                        .hideDelay(5000)
                        .position("top right"));
                    return false;
                } else if (obj.id == $scope.user.id) {
                    $mdToast.show($mdToast.simple()
                        .content('联合发起人不能与发起人相同')
                        .hideDelay(5000)
                        .position("top right"));
                    return false;
                } else {
                    var founderBull = true;
                    angular.forEach($scope.founder, function(index, value) {
                        if (index.id == obj.id) {
                            $mdToast.show($mdToast.simple()
                                .content('联合发起人不能重复添加')
                                .hideDelay(5000)
                                .position("top right"));
                            founderBull = false;
                        }
                    })

                    if (founderBull) {
                        $scope.founder.push(obj);
                    }
                }

            }
            //审核活动是否通过
            $scope.updateStatus = function(id, status) {
                if(status == 10) {
                    var confirm = $mdDialog.confirm()
                    .title('确定要通过吗？')
                    .ariaLabel('delete activity item')
                    .ok('确定通过')
                    .cancel('点错了，再看看');
                } else {
                    var confirm = $mdDialog.confirm()
                    .title('确定不通过吗？')
                    .ariaLabel('delete activity item')
                    .ok('确定不通过')
                    .cancel('点错了，再看看');
                }
                $mdDialog.show(confirm).then(function() {
                    $activityManage.updateStatus(id, status).then(function(data) {
                        $location.path("/activity-check/check");
                        console.log('test');
                    });
                });
            }

            // 删除发起人
            $scope.deteFounder = function(founder) {
                $scope.founder.splice(founder, 1);
            }

            // 搜索场地功能
            $scope.getSpace = function(spacename) {
                console.log('test11111');
                    $scope.space_spot = $activityManage.searchSpace(spacename);
                    return $scope.space_spot;
            }
                // 获取场地
            $activityManage.searchSpace('').then(function(data) {
                console.log('test');
                $scope.spaceSpots = data;
            });

            //搜索空间
            $scope.getSection = function(obj) {
                if (obj != null) {
                    $scope.selectedSection = [];
                    // 把字符转化为对象
                    if (typeof obj == 'string') {
                        var obj = JSON.parse(obj)
                        $scope.sections = obj.sections;
                    } else if (typeof obj == 'object') {
                        $scope.sections = obj.sections;
                    }
                }
            }

            // 搜索用户功能
            $scope.getUsers = function(query) {
                return $activityManage.searchFounder(query);
            }

            // 搜索PMA功能
            $scope.getPrincipals = function(query) {
                return $activityManage.searchPrincipal(query);
            }

            // 搜索管理员功能
            $scope.getDts = function(query) {
                return $activityManage.searchDts(query);
            }

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

            // 标签
            $scope.tags = [];
            // 标签搜索功能
            $scope.loadTags = function(query) {
                return $activityManage.tags(query);
            };

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

            $scope.pma_type_count = [
                { pma_type: 0, name: '线上' },
                { pma_type: 1, name: '线下' },
            ];
            // $scope.pma_type = { pma_type: 0, name: '线上' };

            // qiniu upload 群二维码 end //
            $scope.isweek = true;
            var id = $routeParams.id;
            if (id > 0) {
                $activityManage.fetch(id).then(function(data) {
                    $scope.user = {};
                    $scope.dts = {};
                    $scope.entity = data;
                    $scope.start_time_str = getTimeByTimestamp(data.start_time);
                    $scope.end_time_str = getTimeByTimestamp(data.end_time);
                    $scope.poster = data.poster;
                    $scope.group_code = data.group_code;
                    $scope.user = data.user;
                    $scope.dts = data.dts;
                    $scope.pma = data.pma;
                    $scope.address_assign = data.address_assign;
                    $scope.selectedSpaceSpot = data.space;
                    $scope.selectedSpace = data.space;
                    $scope.founder = data.founder;
                     // 转化为number类型
                    $scope.entity.cost = parseInt(data.cost);
                    $scope.entity.peoples = parseInt(data.peoples);
                    $scope.entity.ideal_number = parseInt(data.ideal_number);
                    $scope.entity.ideal_number_limit = parseInt(data.ideal_number_limit);
                    // 场地的id
                    $scope.selectedSection = [];
                    angular.forEach(data.sections, function(value, key) {
                        $scope.selectedSection.push(value.space_section_id);
                    });
                    var tags = [];
                    for (var k in data.tags) {
                        var tag = data.tags[k].name;
                        tags.push(tag);
                    }
                    $scope.tags = tags;

                }, function(err) {
                    alert(err);
                });
            }

            // 列表
            $activityTypeManage.fetch().then(function(data) {
                $scope.type_list = data;
            });

            // 取消
            $scope.cancel = function() {
                $location.path('/activity/list/0');
            }

            //保存活动
            $scope.save = function() {
                var newEntity = $scope.entity;
                newEntity.start_time = $scope.entity.start_time;
                newEntity.end_time = $scope.entity.end_time;
                newEntity.poster = $scope.poster;
                newEntity.group_code = $scope.group_code;
                newEntity.pma_type = $scope.entity.pma_type;
                // 创建发起人
                if ($scope.founder) {
                    newEntity.founder = $scope.founder;
                }
                // 创建场地
                if ($scope.selectedSpaceSpot) {
                    newEntity.area = $scope.selectedSpaceSpot.area;
                    newEntity.address = $scope.selectedSpaceSpot.address;
                    newEntity.space_spot_id = $scope.selectedSpaceSpot.id;
                }
                // 新创建活动时 未定义 更新时数组为0
                if (typeof($scope.selectedSection) == 'undefined') {
                    newEntity.space_section_id = 0;
                } else {

                    if ($scope.selectedSection.length == 0) {
                        newEntity.space_section_id = 0;
                    } else {
                        newEntity.space_section_id = $scope.selectedSection;
                    }
                }
                if ($scope.user) {
                    newEntity.created_by = $scope.user.id;
                }
                if ($scope.dts) {
                    newEntity.updated_by = $scope.dts.id;
                }
                if ($scope.pma) {
                    newEntity.principal = $scope.pma.id;
                }
                if ($scope.co_founder1) {
                    newEntity.co_founder1 = $scope.co_founder1.id;
                } else {
                    newEntity.co_founder1 = 0;
                }
                if ($scope.co_founder2) {
                    newEntity.co_founder2 = $scope.co_founder2.id;
                } else {
                    newEntity.co_founder2 = 0;
                }

                var tags = [];
                for (var k in $scope.tags) {
                    var tag = $scope.tags[k].text;
                    tags.push(tag);
                }
                newEntity.tagNames = tags.join();

                if (newEntity.id > 0) { // 更新活动
                    $activityManage.update(newEntity.id, newEntity).then(function(data) {
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
                    $activityManage.create(newEntity).then(function(data) {
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

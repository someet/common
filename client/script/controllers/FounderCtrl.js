angular.module('controllers')
    .controller('FounderListCtrl', [
            '$scope',
            '$routeParams',
            '$location',
            '$questionManage',
            '$founderManage',
            '$activityTypeManage',
            '$mdDialog',
            'lodash',
            '$mdToast',
            '$uibModal',
            '$log',
        function(
            $scope,
            $routeParams, 
            $location, 
            $questionManage, 
            $founderManage, 
            $activityTypeManage, 
            $mdDialog, 
            lodash, 
            $mdToast, 
            $uibModal, 
            $log
            ) {
            //活动列表开始
            var listtype = $routeParams.type_id;
            if (listtype > 0) {
                normalPagination(listtype);
            } else {
                normalPagination(0);
            }

            // 初始化分页数据开始
            function normalPagination(type) {
                $scope.modelPagination = {
                    totalItems: 0,
                    currentPage: 1,
                    maxSize: 5,
                    itemsPerPage: 20, //每页多少条
                    pageChange: function() {
                        fetchPage(type, this.currentPage);
                    }
                };

                $founderManage.modelPageMeta(type, $scope.modelPagination.itemsPerPage).then(function(total) {
                    $scope.modelPagination.totalItems = total;
                });

                $scope.userList = fetchPage(type, $scope.modelPagination.currentPage);
            }

            // 页数改变
            $scope.changePage = function(type, page) {
                fetchPage(type, page);
            }

            function fetchPage(type, page) {
                $founderManage.fetchPage(type, page).then(function(data) {
                    $scope.list = data.model;
                    $scope.user = data.user;
                    $scope.modelPagination.currentPage = page;
                });
            }
            // 初始化分页数据结束


            // 弹出问题表单
            $scope.open = function(entity) {
                // console.log(entity);
                var modalInstance = $uibModal.open({
                    animation: $scope.animationsEnabled,
                    templateUrl: 'question.html',
                    controller: 'ModalInstanceCtrl',
                    entity: entity,
                    resolve: {
                        entity: function() {
                            return entity;
                        }
                    }
                });

                modalInstance.result.then(function(data) {
                    normalPagination(listtype);
                    console.log(data);
                }, function() {
                    $log.info('Modal dismissed at: ' + new Date());
                });
            };

            //提交审核
            $scope.updateStatus = function(id, status, entity) {
                if (entity.question) {
                    var confirm = $mdDialog.confirm()
                    .title('确定要提交审核吗？')
                    .ariaLabel('delete activity item')
                    .ok('确定提交')
                    .cancel('点错了，再看看');
                    $mdDialog.show(confirm).then(function() {
                        $founderManage.updateStatus(id, status).then(function(data) {
                            normalPagination(listtype);
                    });
                });
                } else {
                    var noquestion = $mdDialog.alert()
                        .title('请先设置问题表单然后再发布活动！')
                        .clickOutsideToClose(true)
                        .ariaLabel('delete activity item')
                        .ok('知道了');
                    $mdDialog.show(noquestion);
                }   
            }

            // 活动类型列表
            $activityTypeManage.fetch().then(function(data) {
                $scope.activityTypeList = data;
            }, function(err) {
                alert(err);
            });


            // 更新活动类型
            $scope.onTypeChangeClick = function(activity, type_id) {
                var old_type_id = activity.type_id;
                activity.type_id = type_id;
                $founderManage.update(activity.id, activity).then(function(data) {
                    $location.path('/activity/list/' + type_id);
                }, function(err) {
                    alert(err);
                });
            };


            // 复制一个活动
            $scope.copy = function(activityData) {

                // 原活动的id  下面下面直接取不到值 会被覆盖
                var originActivityID = activityData.id;

                var confirm = $mdDialog.confirm()
                    .title('确定要复制“' + activityData.title + '”吗？')
                    .ariaLabel('delete activity item')
                    .ok('确定复制')
                    .cancel('点错了，再看看');

                // 确认提醒
                $mdDialog.show(confirm).then(function() {
                    var originActivity = activityData;
                    originActivity.id = null;
                    originActivity.title = activityData.title + " 副本";
                    originActivity.status = 10; //活动状态10为草稿
                    $founderManage.create(originActivity).then(function(newActivity) {
                        // 复制表单
                        $questionManage.fetchByActivityId(originActivityID).then(function(originQuestion) {

                            // 如果表单问题存在则创建表单
                            if (originQuestion) {

                                // 组建新的问题表单
                                var newQuestion = {
                                    // 新的活动的id
                                    activity_id: newActivity.id,
                                    questionItemList: [],
                                };

                                // 遍历表单数据结构
                                for (var k in originQuestion.questionItemList) {
                                    var questionItem = {
                                        label: originQuestion.questionItemList[k].label,
                                    };
                                    newQuestion.questionItemList.push(questionItem);
                                }

                                // 创建活动的表单
                                $questionManage.create(newQuestion).then(function(lastQuestion) {
                                    $mdToast.show($mdToast.simple()
                                        .content('活动和问题复制成功')
                                        .hideDelay(5000)
                                        .position("top right"));
                                    $location.path('/founder/');
                                }, function(err) {
                                    alert(err);
                                });
                            } else {
                                $mdToast.show($mdToast.simple()
                                    .content('活动复制成功')
                                    .hideDelay(5000)
                                    .position("top right"));
                                $location.path('/founder/');
                            }


                        }, function(err) {
                            alert(err);
                        });
                    }, function(err) {
                        alert(err);
                    });
                });
            };


            //提交审核  状态8为提交审核
            $scope.release = function(entity) {
                if (entity.question) {
                    var confirm = $mdDialog.confirm()
                        .title('确定要提交审核“' + entity.title + '”吗？')
                        .ariaLabel('delete activity item')
                        .ok('确定提交审核')
                        .cancel('点错了，再看看');

                    $mdDialog.show(confirm).then(function() {
                        $founderManage.updateStatus(entity.id, 8).then(function(data) {
                            $mdToast.show($mdToast.simple()
                                .content('活动 “' + entity.title + '” 已提交审核')
                                .hideDelay(5000)
                                .position("top right"));

                        }, function(err) {
                            $mdToast.show($mdToast.simple()
                                .content(err.toString())
                                .hideDelay(5000)
                                .position("top right"));
                        });
                    });
                } else {

                    var noquestion = $mdDialog.alert()
                        .title('请先设置问题表单然后再提交审核！')
                        .clickOutsideToClose(true)
                        .ariaLabel('delete activity item')
                        .ok('知道了');

                    $mdDialog.show(noquestion);
                }

            }

            //搜索活动
            $scope.getActivity = function(query) {
                var title = $scope.title;
                $founderManage.search(title).then(function(activityList) {
                    if (activityList.status == 1) {
                        $scope.list = activityList.models;
                    } else {
                        $scope.list = '';
                    }
                });
            }


            // 增加新活动
            $scope.createPage = function() {
                $location.path('/founder/add');
            }


        }
    ])

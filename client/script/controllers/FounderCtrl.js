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
        function($scope, $routeParams, $location, $questionManage, $founderManage, $activityTypeManage, $mdDialog, lodash, $mdToast, $uibModal, $log) {
            //活动列表开始
            var listtype = $routeParams.type_id;

            // console.log($routeParams);
            if (listtype > 0) {
                normalPagination(listtype, 0);
            } else {
                normalPagination(0, 0);
            }

            // 打开问题表单
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
                    console.log(data);
                }, function() {
                    $log.info('Modal dismissed at: ' + new Date());
                });
            };


            function normalPagination(type, isWeek) {
                $scope.modelPagination = {
                    totalItems: 0,
                    currentPage: 1,
                    maxSize: 5,
                    isWeek: isWeek,
                    itemsPerPage: 20, //每页多少条
                    pageChange: function() {
                        fetchPage(type, this.currentPage, isWeek);
                    }
                };

                $founderManage.modelPageMeta(type, $scope.modelPagination.itemsPerPage, isWeek).then(function(total) {
                    $scope.modelPagination.totalItems = total;
                });

                $scope.userList = fetchPage(type, $scope.modelPagination.currentPage, $scope.modelPagination.isWeek);
            }

            $scope.changePage = function(type, page) {
                fetchPage(type, page, $scope.modelPagination.isWeek);
            }

            $scope.prev = function(type) {
                var page = $scope.modelPagination.currentPage - 1;
                if (page < 1) {
                    page = 1;
                }
                fetchPage(type, page, $scope.modelPagination.isWeek);
            }
            $scope.next = function(type) {
                var page = $scope.modelPagination.currentPage + 1;
                if (page > $scope.modelPagination.totalItems) {
                    page = $scope.modelPagination.totalItems;
                }
                fetchPage(type, page, $scope.modelPagination.isWeek);
            }

            function fetchPage(type, page, isWeek) {
                $founderManage.fetchPage(type, page, isWeek).then(function(data) {
                    $scope.list = data.model;
                    $scope.user = data.user;
                    $scope.modelPagination.currentPage = page;
                    //纯js分页
                    if ($scope.modelPagination.currentPage > 1 && $scope.modelPagination.currentPage < $scope.modelPagination.totalItems) {
                        $scope.pages = [
                            $scope.modelPagination.currentPage - 1,
                            $scope.modelPagination.currentPage,
                            $scope.modelPagination.currentPage + 1
                        ];
                    } else if ($scope.modelPagination.currentPage <= 1 && $scope.modelPagination.totalItems > 1) {
                        $scope.modelPagination.currentPage = 1;
                        $scope.pages = [
                            $scope.modelPagination.currentPage,
                            $scope.modelPagination.currentPage + 1
                        ];
                    } else if ($scope.modelPagination.currentPage >= $scope.modelPagination.totalItems && $scope.modelPagination.totalItems > 1) {
                        $scope.modelPagination.currentPage = $scope.modelPagination.totalItems;
                        $scope.pages = [
                            $scope.modelPagination.currentPage - 1,
                            $scope.modelPagination.currentPage
                        ];
                    }
                });
            }

            // 更新活动状态
            $scope.updateStatus = function(id, status) {
                $founderManage.updateStatus(id, status).then(function(data) {
                    angular.forEach($scope.list, function(index, value) {
                        if (index.id == data.id) {
                            index.status = data.status;
                        }
                    })
                })
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

            // 设置报名表单状态 20关闭 10打开
            $scope.applyStatus = function(entity, status) {
                var new_question = entity.question;
                new_question.status = status;
                $questionManage.update(entity.question.id, new_question).then(function(data) {}, function(err) {
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
                            }else{
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


            // 发布活动
            $scope.release = function(entity) {
                if (entity.question) {
                    var confirm = $mdDialog.confirm()
                        .title('确定要提交审核“' + entity.title + '”吗？')
                        .ariaLabel('delete activity item')
                        .ok('确定提交审核')
                        .cancel('点错了，再看看');

                    $mdDialog.show(confirm).then(function() {
                        var newEntity = entity;
                        newEntity.status = 10; //活动状态10为提交审核
                        $founderManage.update(newEntity.id, newEntity).then(function(data) {
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



            function copyTextToClipboard(url) {
                window.prompt("复制链接：Command + C, Enter\n关闭窗口：Esc", url);
            }


            // 更新活动排名序号
            // $scope.$watch($scope.list,function(newvalue,oldvalue){
            // })



            // 复制预览链接
            $scope.copyPreviewUrl = function(activity) {
                copyTextToClipboard(activity.preview_url);
            }

            // 复制筛选链接
            $scope.copyFilterUrl = function(activity) {
                copyTextToClipboard(activity.filter_url);
            }

            // 复制活动链接
            $scope.copyActivityUrl = function(activity) {
                copyTextToClipboard('https://m.someet.cc/activity/' + activity.id);
            }

            // 查看反馈
            $scope.viewFeedback = function(activity) {
                $location.path('/activity-feedback/' + activity.id);
            }

            // 预览问题
            $scope.previewQuestion = function(entity) {
                $location.path('/answer/view/' + entity.id);
            }

            // tab
            $scope.isActive = function(type_id) {
                var route = "/activity/list/" + type_id
                return route === $location.path() || $location.path() === '/question' || $location.path() === '/answer';
            }

            //点击增加类型按钮
            $scope.onTypeAddClicked = function() {
                $scope.showAddForm = true;
            };

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

            //ng-if会增加新的child，需要设置初始值
            $scope.addForm = {
                newType: ""
            };

            // 取消增加新类型
            $scope.cancelAddType = function() {
                $scope.showAddForm = false;
            };

            var addTypeName = function(data) {
                var newEntity = {
                    name: data,
                    display_order: 3
                };
                $activityTypeManage.create(newEntity).then(function(data) {
                    $activityTypeManage.fetch().then(function(data) {
                        $scope.activityTypeList = data;
                    }, function(err) {
                        alert(err);
                    });

                    $location.path('/activity/list/0');
                    $mdToast.show($mdToast.simple()
                        .content('添加活动类型成功')
                        .hideDelay(5000)
                        .position("top right"));
                }, function(err) {
                    $mdToast.show($mdToast.simple()
                        .content(err.toString())
                        .hideDelay(5000)
                        .position("top right"));
                });
                $scope.showAddForm = false;
                $scope.addForm = {
                    newType: ""
                };
            };

            // 增加新的类型
            $scope.commitTypeName = function(typeName) {

                if (typeName.length < 2) {
                    $mdToast.show(
                        $mdToast.simple()
                        .content("分组名称不能少于2个字符")
                        .hideDelay(5000)
                        .position("top right"));
                } else if (typeName.length > 20) {
                    $mdToast.show(
                        $mdToast.simple()
                        .content("分组名称不能超过20个字符")
                        .hideDelay(5000)
                        .position("top right"));
                } else {
                    addTypeName(typeName);
                }

            };

            // 增加新活动
            $scope.createPage = function() {
                $location.path('/founder/add');
            }


            $scope.totalItems = 64;
            $scope.currentPage = 4;

            $scope.setPage = function (pageNo) {
                console.log(11111);
                $scope.currentPage = pageNo;
            };

            $scope.pageChanged = function() {
                $log.log('Page changed to: ' + $scope.currentPage);
            };

            $scope.maxSize = 5;
            $scope.bigTotalItems = 175;
            $scope.bigCurrentPage = 1;

        }
    ])

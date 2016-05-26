angular.module('controllers')
    .controller('FounderListCtrl', ['$scope', '$routeParams', '$location', '$questionManage', '$founderManage', '$activityTypeManage', '$mdDialog', 'lodash', '$mdToast',
        function($scope, $routeParams, $location, $questionManage, $founderManage, $activityTypeManage, $mdDialog, lodash, $mdToast) {
            //活动列表开始
            var listtype = $routeParams.type_id;
            if (listtype > 0) {
                normalPagination(listtype, 0);
            } else {
                normalPagination(0, 0);
            }

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
                console.log($scope.userList);
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
                $founderManage.fetchPage(type, page, isWeek).then(function(modelList) {
                    $scope.list = modelList;
                    console.log(modelList);
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

            // 内部编辑状态
            $scope.onEditStatusChangeClick = function(activity, edit_status) {
                var newActivity = activity;
                newActivity.edit_status = edit_status;
                $founderManage.update(newActivity.id, newActivity).then(function(data) {
                    $location.path('/activity/list/' + activity.type_id);
                }, function(err) {
                    alert(err);
                })
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
            $scope.copy = function(entity) {

                var newEntity = entity;
                newEntity.id = null;
                newEntity.title = entity.title + " 副本";
                newEntity.status = 10; //活动状态10为草稿
                $founderManage.create(newEntity).then(function(data) {
                    $location.path('/activity/list/' + entity.type_id);

                }, function(err) {
                    alert(err);
                });
            };

            // 草稿
            $scope.draft = function(entity) {

                // var confirm = $mdDialog.confirm()
                //   .title('确定要更改为草稿吗')
                //   .ariaLabel('delete activity item')
                //   .ok('确定发布')
                //   .cancel('点错了，再看看');

                // $mdDialog.show(confirm).then(function() {


                var newEntity = entity;
                newEntity.status = 10; //活动状态20为发布
                $founderManage.update(newEntity.id, newEntity).then(function(data) {
                    // $location.path('/activity/list/' + entity.type_id);
                    $mdToast.show($mdToast.simple()
                        .content('切换为草稿成功')
                        .hideDelay(5000)
                        .position("top right"));

                }, function(err) {
                    $mdToast.show($mdToast.simple()
                        .content(err.toString())
                        .hideDelay(5000)
                        .position("top right"));
                });
                // });

            }


            // 发布活动
            $scope.release = function(entity) {
                if (entity.question) {
                    var confirm = $mdDialog.confirm()
                        .title('确定要发布活动“' + entity.title + '”吗？')
                        .ariaLabel('delete activity item')
                        .ok('确定发布')
                        .cancel('点错了，再看看');

                    $mdDialog.show(confirm).then(function() {
                        var newEntity = entity;
                        newEntity.status = 20; //活动状态20为发布
                        $founderManage.update(newEntity.id, newEntity).then(function(data) {
                            $location.path('/activity/list/' + entity.type_id);
                            $mdToast.show($mdToast.simple()
                                .content('活动 “' + entity.title + '” 已发布')
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
                        .title('请先设置问题表单然后再发布活动！')
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

            // 设置问题
            $scope.viewQuestion = function(activity) {
                $location.path('/question/view/' + activity.id);
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

        }
    ])
    
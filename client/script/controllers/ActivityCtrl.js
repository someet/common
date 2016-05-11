angular.module('controllers', ['ngTagsInput'])
    .controller('ActivityListCtrl', ['$scope', '$routeParams', '$location', '$questionManage', '$activityManage', '$activityTypeManage', '$mdDialog', 'lodash', '$mdToast',
        function($scope, $routeParams, $location, $questionManage, $activityManage, $activityTypeManage, $mdDialog, lodash, $mdToast) {

            /*
            var type_id = $routeParams.type_id;
            $activityManage.listByType(type_id).then(function(data) {
              $scope.list = data;
            }, function(err) {
              alert(err);
            });
            */

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

                $activityManage.modelPageMeta(type, $scope.modelPagination.itemsPerPage, isWeek).then(function(total) {
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
                $activityManage.fetchPage(type, page, isWeek).then(function(modelList) {
                    $scope.list = modelList;
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
                $activityManage.updateStatus(id, status).then(function(data) {})
            }

            // 活动类型列表
            $activityTypeManage.fetch().then(function(data) {
                $scope.activityTypeList = data;
            }, function(err) {
                alert(err);
            });

            // 一键发布预发布活动
            $scope.prevenIssuetActivity = function() {
                    $activityManage.updateAllPrevent().then(function(data) {
                        $mdToast.show($mdToast.simple()
                            .content('一键发布预发布活动成功')
                            .hideDelay(5000)
                            .position("top right"));
                        $scope.list = data;
                    }, function(err) {
                        $mdToast.show($mdToast.simple()
                            .content(err.toString())
                            .hideDelay(5000)
                            .position("top right"));
                    });
                }
                // 预发布活动
            $scope.preventActivity = function() {
                $activityManage.filterPrevent().then(function(data) {
                    $scope.list = data;
                })
            }

            // 本周活动
            $scope.weekActivity = function() {
                if (listtype > 0) {
                    normalPagination(listtype, 0);
                } else {
                    normalPagination(0, 0);
                }
            }

            // 历史活动
            $scope.historyActivity = function() {
                if (listtype > 0) {
                    normalPagination(listtype, 1);
                } else {
                    normalPagination(0, 1);
                }
            }

            // 调整顺序
            $scope.adjust_order = function(entity) {
                    // entity.display_order
                    $activityManage.update(entity.id, entity).then(function(data) {
                        // $location.path('/activity/list/' + type_id);
                    }, function(err) {
                        alert(err);
                    });
                }
                // 更新活动类型
            $scope.onTypeChangeClick = function(activity, type_id) {
                var old_type_id = activity.type_id;
                activity.type_id = type_id;
                $activityManage.update(activity.id, activity).then(function(data) {
                    $location.path('/activity/list/' + type_id);
                }, function(err) {
                    alert(err);
                });
            };

            // 内部编辑状态
            $scope.onEditStatusChangeClick = function(activity, edit_status) {
                var newActivity = activity;
                newActivity.edit_status = edit_status;
                $activityManage.update(newActivity.id, newActivity).then(function(data) {
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
                $activityManage.create(newEntity).then(function(data) {
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
                $activityManage.update(newEntity.id, newEntity).then(function(data) {
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

            // 预发布
            $scope.prevent = function(entity) {

                // var confirm = $mdDialog.confirm()
                //   .title('确定要预发布活动“' + entity.title + '”吗？')
                //   .ariaLabel('delete activity item')
                //   .ok('确定发布')
                //   .cancel('点错了，再看看');

                // $mdDialog.show(confirm).then(function() {


                var newEntity = entity;
                newEntity.status = 15; //活动状态20为发布
                $activityManage.update(newEntity.id, newEntity).then(function(data) {
                    // $location.path('/activity/list/' + entity.type_id);
                    $mdToast.show($mdToast.simple()
                        .content('切换为预发布成功')
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
                        $activityManage.update(newEntity.id, newEntity).then(function(data) {
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

            // 删除
            $scope.delete = function(entity) {
                var confirm = $mdDialog.confirm()
                    .title('确定要删除活动“' + entity.title + '”吗？')
                    .ariaLabel('delete activity item')
                    .ok('确定删除')
                    .cancel('手滑点错了，不删');

                $mdDialog.show(confirm).then(function() {
                    $activityManage.delete(entity).then(function(data) {

                        lodash.remove($scope.list, function(tmpRow) {
                            return tmpRow == entity;
                        });

                        $mdToast.show($mdToast.simple()
                            .content('删除活动“' + entity.title + '”成功')
                            .hideDelay(5000)
                            .position("top right"));

                    }, function(err) {
                        $mdToast.show($mdToast.simple()
                            .content(err.toString())
                            .hideDelay(5000)
                            .position("top right"));
                    });
                });
            };

            // 置顶/取消置顶
            $scope.top = function(entity, is_top) {
                var newEntity = entity;
                newEntity.is_top = is_top > 0 ? 1 : 0; // 是否置顶
                var toastText = is_top > 0 ? "活动置顶成功" : "取消置顶成功"; // 是否置顶
                $activityManage.update(entity.id, newEntity).then(function(data) {
                    $mdToast.show($mdToast.simple()
                        .content(toastText)
                        .hideDelay(5000)
                        .position("top right"));
                    $location.path('/activity/list/' + $routeParams.type_id);
                }, function(err) {
                    $mdToast.show($mdToast.simple()
                        .content(err.toString())
                        .hideDelay(5000)
                        .position("top right"));
                })
            }

            function copyTextToClipboard(url) {
                window.prompt("复制链接：Command + C, Enter\n关闭窗口：Esc", url);
            }


            // 更新活动排名序号
            // $scope.$watch($scope.list,function(newvalue,oldvalue){
            //   console.log(newvalue+'----'+oldvalue);
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

            // 查看报名
            $scope.viewAnswer = function(activity) {
                $location.path('/answer/' + activity.id);
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
                $activityManage.search(title).then(function(activityList) {
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
                $location.path('/activity/add');
            }

        }
    ])
    .controller('ActivityViewCtrl', ['$scope', '$routeParams', '$location', '$activityManage', '$activityTypeManage', '$qupload', '$qiniuManage', '$mdToast',
        function($scope, $routeParams, $location, $activityManage, $activityTypeManage, $qupload, $qiniuManage, $mdToast) {
            $scope.$parent.pageName = '活动详情';

            // 搜索场地功能
            $scope.getSpace = function(query) {
                // $scope.space_spot = $activityManage.searchSpace(query);
                // $scope.sections = $scope.space_spot.sections;
                // return $scope.space_spot;
                $activityManage.searchSpace(query).then(function(data) {
                    // $scope.section = data.sections;
                    // $scope.space_spot = data;
                    // console.log(data.models.sections);
                    // console.log(data.sections);
                    // console.log(data);
                    return data;
                });

            }


            // 获取场地
            $activityManage.searchSpace('').then(function(data) {
                $scope.section = data.sections;
                $scope.space_spots = data;
                // console.log(data.models.sections);
                // console.log(data.sections);
                console.log(data);
                return data;
            });

            //搜索空间
            $scope.getSection = function(space_spot) {
                if (space_spot != null) {
                    $scope.sections = space_spot.sections;
                    console.log(space_spot);
                } else {
                    $scope.sections = {};
                    console.log(22222);
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

            var id = $routeParams.id;
            if (id > 0) {
                $activityManage.fetch(id).then(function(data) {
                    console.log(data);
                    $scope.user = {};
                    $scope.dts = {};
                    $scope.entity = data;
                    $scope.start_time_str = getTimeByTimestamp(data.start_time);
                    // $scope.start_time_str = getTimeByTimestamp(data.start_time);
                    $scope.end_time_str = getTimeByTimestamp(data.end_time);
                    $scope.poster = data.poster;
                    $scope.group_code = data.group_code;
                    $scope.user = data.user;
                    $scope.dts = data.dts;
                    $scope.pma = data.pma;
                    $scope.co_founder1 = data.cofounder1;
                    $scope.co_founder2 = data.cofounder2;
                    $scope.space_spot = data.space;
                    $scope.section = data.space.sections;

                    $scope.space_spots = $scope.searchSpace('');
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
                newEntity.space_spot_id = $scope.space_spot.id;
                newEntity.space_section_id = $scope.sections.id;

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

                        console.log(data.pma_type);
                        // $location.path('/activity/list/0');
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

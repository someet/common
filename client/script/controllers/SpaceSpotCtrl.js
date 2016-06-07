angular.module('controllers')
    .controller('SpaceSpotCtrl', ['$scope', '$routeParams', '$location', '$spaceTypeManage', '$spaceSpotManage', '$mdDialog', 'lodash', '$mdToast',
        function($scope, $routeParams, $location, $spaceTypeManage, $spaceSpotManage, $mdDialog, lodash, $mdToast) {
            // 场地类型列表
            $spaceTypeManage.fetch().then(function(data) {
                $scope.typeList = data;
            }, function(err) {
                alert(err);
            });

            //点击增加类型按钮
            $scope.onTypeAddClicked = function() {
                $scope.showAddForm = true;
            };

            // 增加新的类型
            $scope.commitTypeName = function(typeName) {
                if (typeName.length < 2) {
                    $mdToast.show(
                        $mdToast.simple()
                        .content("场地分类名称不能少于2个字符")
                        .hideDelay(5000)
                        .position("top right"));
                } else if (typeName.length > 20) {
                    $mdToast.show(
                        $mdToast.simple()
                        .content("场地分类名称不能超过20个字符")
                        .hideDelay(5000)
                        .position("top right"));
                } else {
                    addTypeName(typeName);
                }
            };

            // 取消增加新类型
            $scope.cancelAddType = function() {
                $scope.showAddForm = false;
            };

            var addTypeName = function(data) {
                var newEntity = {
                    name: data,
                    display_order: 0
                };
                $spaceTypeManage.create(newEntity).then(function(data) {
                    $spaceTypeManage.fetch().then(function(data) {
                        $scope.typeList = data;
                    }, function(err) {
                        alert(err);
                    });

                    $location.path('/space-spot/list/0');
                    $mdToast.show($mdToast.simple()
                        .content('添加场地类型成功')
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

            //场地列表开始
            var listtype = $routeParams.type_id;
            if (listtype > 0) {
                normalPagination(listtype);
            } else {
                normalPagination(0);
            }

            function normalPagination(type) {
                $scope.modelPagination = {
                    totalItems: 0,
                    currentPage: 1,
                    maxSize: 5,
                    itemsPerPage: 2, //每页多少条
                };
                fetchPage(type);
            }

            $scope.pageChange = function() {
                if (!$scope.name) {
                    fetchPage(listtype);
                } else {
                    searchSpot($scope.name, $scope.modelPagination.currentPage);
                }
            }

            function fetchPage(type) {
                $spaceSpotManage.fetchPage(type, $scope.modelPagination.currentPage).then(function(data) {
                    $scope.list = data.model;
                    $scope.modelPagination.totalItems = data.totalCount;
                });
            }

            //搜索场地
            $scope.getSpace = function() {
                $scope.modelPagination.currentPage = 1;
                searchSpot($scope.name, 1);
            }

            //搜索活动函数 分页调用 活动按钮调用
            function searchSpot(query, page) {
                $spaceSpotManage.search(query, page).then(function(data) {
                    $scope.list = data.models;
                    $scope.modelPagination.totalItems = data.totalCount;
                });
            }
            // 增加新场地
            $scope.createPage = function() {
                $location.path('/space-spot/add');
            }
        }
    ])
    .controller('SpaceSpotViewCtrl', ['$scope', '$routeParams', '$location', '$spaceTypeManage', '$spaceSpotManage', '$qupload', '$qiniuManage', '$mdToast',
        function($scope, $routeParams, $location, $spaceTypeManage, $spaceSpotManage, $qupload, $qiniuManage, $mdToast) {
            $scope.$parent.pageName = '场地详情';

            // 场地类型列表
            $spaceTypeManage.fetch().then(function(data) {
                $scope.typeList = data;
            }, function(err) {
                alert(err);
            });

            // qiniu upload MapPic start //
            $scope.selectPic = null;

            var startPic = function() {
                $qiniuManage.fetchUploadToken().then(function(token) {

                    $qupload.upload({
                        key: '',
                        file: $scope.selectPic.file,
                        token: token
                    }).then(function(response) {
                        $qiniuManage.completelyUrl(response.key).then(function(url) {
                            $scope.map_pic = url;
                        });
                    }, function(response) {}, function(evt) {
                        if ($scope.selectPic !== null) {
                            $scope.selectPic.progress.p = Math.floor(100 * evt.loaded / evt.totalSize);
                        }
                    });

                });
            };

            $scope.PicAbort = function() {
                $scope.selectPic.upload.abort();
                $scope.selectPic = null;
            };

            $scope.onPicSelect = function($files) {
                $scope.selectPic = {
                    file: $files[0],
                    progress: {
                        p: 0
                    }
                };
                startPic();
            };
            // qiniu upload MapPic end //

            // qiniu upload Image start //
            $scope.selectImage = null;

            var startImage = function() {
                $qiniuManage.fetchUploadToken().then(function(token) {

                    $qupload.upload({
                        key: '',
                        file: $scope.selectImage.file,
                        token: token
                    }).then(function(response) {
                        $qiniuManage.completelyUrl(response.key).then(function(url) {
                            $scope.image = url;
                        });
                    }, function(response) {}, function(evt) {
                        if ($scope.selectImage !== null) {
                            $scope.selectImage.progress.p = Math.floor(100 * evt.loaded / evt.totalSize);
                        }
                    });

                });
            };

            $scope.imageAbort = function() {
                $scope.selectImage.upload.abort();
                $scope.selectImage = null;
            };

            $scope.onImageSelect = function($files) {
                $scope.selectImage = {
                    file: $files[0],
                    progress: {
                        p: 0
                    }
                };
                startImage();
            };
            // qiniu upload Image end //

            //查看活动
            var id = $routeParams.id;
            if (id > 0) {
                $spaceSpotManage.fetch(id).then(function(data) {
                    $scope.user = {};
                    $scope.entity = data;

                    $scope.map_pic = data.map_pic;
                    $scope.image = data.image;
                    $scope.user = data.user;

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

            //保存活动
            $scope.save = function() {
                var newEntity = $scope.entity;
                newEntity.map_pic = $scope.map_pic;
                newEntity.image = $scope.image;
                if ($scope.pma) {
                    newEntity.principal = $scope.pma.id;
                }

                if (newEntity.id > 0) { // 更新场地
                    $spaceSpotManage.update(newEntity.id, newEntity).then(function(data) {
                        $mdToast.show($mdToast.simple()
                            .content('场地保存成功')
                            .hideDelay(5000)
                            .position("top right"));
                    }, function(err) {
                        $mdToast.show($mdToast.simple()
                            .content(err.toString())
                            .hideDelay(5000)
                            .position("top right"));
                    });
                } else { // 添加场地
                    $spaceSpotManage.create(newEntity).then(function(data) {
                        $location.path('/space-spot/list/0');
                        $mdToast.show($mdToast.simple()
                            .content('场地添加成功')
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

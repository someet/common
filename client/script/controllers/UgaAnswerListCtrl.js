angular.module('controllers').controller('UgaAnswerListCtrl', [
    '$scope', '$routeParams', '$ugaAnswerManage', '$ugaManage', 'lodash', '$mdToast', '$mdDialog',
    function($scope, $routeParams, $ugaAnswerManage, $ugaManage, lodash, $mdToast, $mdDialog) {
        $scope.question_id = $routeParams.id;
        // $ugaAnswerManage.fetch(id).then(function(data) {
        //     $scope.list = data;
        // }, function(err) {
        //     alert(err);
        // });
        $scope.orderBy = 'id';
        normalPagination($scope.question_id, $scope.orderBy);
        // 排序
        $scope.order = function(order) {
                $scope.orderBy = order;
                fetchPage($scope.question_id, 1, $scope.orderBy);
            }
            // 删除问题
        $scope.questionDelete = function(entity, status) {
                console.log(111);

                if (0 == status) {
                    var confirm = $mdDialog.confirm()
                        .title('确定要删除“' + entity.content + '”吗？')
                        .ariaLabel('delete activity item')
                        .ok('确定删除')
                        .cancel('手滑点错了，不删');
                } else if (1 == status) {
                    var confirm = $mdDialog.confirm()
                        .title('确定要还原“' + entity.content + '”吗？')
                        .ariaLabel('delete activity item')
                        .ok('确定还原')
                        .cancel('手滑点错了，不还原');
                }


                $mdDialog.show(confirm).then(function() {
                    $ugaManage.delete(entity.id, status).then(function(data) {
                        console.log(status);
                        $scope.list.question.status = status;
                        if (0 == status) {
                            $mdToast.show($mdToast.simple()
                                .content('删除成功')
                                .hideDelay(5000)
                                .position("top right"));
                        } else if (1 == status) {
                            $mdToast.show($mdToast.simple()
                                .content('还原成功')
                                .hideDelay(5000)
                                .position("top right"));
                        }

                    }, function(err) {
                        $mdToast.show($mdToast.simple()
                            .content(err.toString())
                            .hideDelay(5000)
                            .position("top right"));
                    });
                });
            }
            // 放入公开库 移除公开库
        $scope.putOpen = function(entity, open) {
            // console.log(entity.open);
            if (1 == open) {
                var confirm = $mdDialog.confirm()
                    .title('确定移入公开库吗？')
                    .ok('确定移入')
                    .cancel('手滑点错了，不移');
            } else if (2 == open) {
                var confirm = $mdDialog.confirm()
                    .title('确定移出' + entity.content + '”公开库吗？')
                    .ok('确定移除')
                    .cancel('手滑点错了，不还原');
            }

            $mdDialog.show(confirm).then(function() {
                $ugaManage.putOpen(entity.id, open).then(function(data) {
                    $mdToast.show($mdToast.simple()
                        .content('操作成功')
                        .hideDelay(5000)
                        .position("top right"));
                }, function(err) {
                    $mdToast.show($mdToast.simple()
                        .content(err.toString())
                        .hideDelay(5000)
                        .position("top right"));
                });
            });
        }


        // 删除问题的回答
        $scope.delete = function(entity, status) {
            console.log(entity.status);
            if (0 == status) {
                var confirm = $mdDialog.confirm()
                    .title('确定要删除“' + entity.content + '”吗？')
                    .ariaLabel('delete activity item')
                    .ok('确定删除')
                    .cancel('手滑点错了，不删');
            } else if (1 == status) {
                var confirm = $mdDialog.confirm()
                    .title('确定要还原“' + entity.content + '”吗？')
                    .ariaLabel('delete activity item')
                    .ok('确定还原')
                    .cancel('手滑点错了，不还原');
            }

            $mdDialog.show(confirm).then(function() {
                $ugaAnswerManage.delete(entity.id, status).then(function(data) {
                    console.log(status);
                    entity.status = status;
                    if (0 == status) {
                        $mdToast.show($mdToast.simple()
                            .content('删除回答“' + entity.content + '”成功')
                            .hideDelay(5000)
                            .position("top right"));
                    }

                }, function(err) {
                    $mdToast.show($mdToast.simple()
                        .content(err.toString())
                        .hideDelay(5000)
                        .position("top right"));
                });
            });
        };


        function normalPagination(question_id) {
            console.log(question_id);
            $scope.modelPagination = {
                totalItems: 0,
                currentPage: 1,
                maxSize: 5,
                itemsPerPage: 4, //每页多少条
                pageChange: function() {
                    fetchPage(type, this.currentPage);
                }
            };

            $ugaAnswerManage.ugaPageMeta(question_id, $scope.modelPagination.itemsPerPage).then(function(total) {
                $scope.modelPagination.totalItems = total;
            });

            $scope.list = fetchPage(question_id, $scope.modelPagination.currentPage);
        }

        $scope.changePage = function(type, page) {
            fetchPage($scope.question_id, page, $scope.orderBy);
        }
        $scope.prev = function(type) {
            var page = $scope.modelPagination.currentPage - 1;
            if (page < 1) {
                page = 1;
            }
            fetchPage($scope.question_id, page, $scope.orderBy = order);
        }
        $scope.next = function(type) {
            var page = $scope.modelPagination.currentPage + 1;
            if (page > $scope.modelPagination.totalItems) {
                page = $scope.modelPagination.totalItems;
            }
            fetchPage($scope.question_id, page, $scope.orderBy);
        }

        // 分页
        function fetchPage(question_id, page, order) {
            $ugaAnswerManage.fetchPage(question_id, page, order).then(function(data) {
                console.log(1111);
                $scope.list = data;
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
    }
])

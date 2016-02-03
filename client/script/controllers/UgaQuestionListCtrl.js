angular.module('controllers').controller('UgaQuestionListCtrl', [
    '$scope', '$ugaManage', '$routeParams', '$location', '$mdToast',
    function($scope, $ugaManage, $routeParams, $location, $mdToast) {

        $scope.is_official = $routeParams.is_official;

        // 删除一个Uga问题
        $scope.delete = function(entity) {
            var newEntity = entity;
            newEntity.status = 0;
            $ugaManage.update(newEntity.id, newEntity).then(function (data) {
                $mdToast.show($mdToast.simple()
                .content('Uga问题更新成功')
                .hideDelay(5000)
                .position("top right"));
                $location.path('/uga-question-list');
            }, function (err) {
                $mdToast.show($mdToast.simple()
                .content(err.toString())
                .hideDelay(5000)
                .position("top right"));
            });
        }
        //恢复一个Uga问题
        $scope.resume = function(entity) {
            var newEntity = entity;
            newEntity.status = 1;
            $ugaManage.update(newEntity.id,newEntity).then(function(data){
                $mdToast.show($mdToast.simple()
                  .content('Uga问题恢复成功')
                  .hideDelay(5000)
                  .position("top right"));
                $location.path('/uga-question-list');
            }, function(err) {
                $mdToast.show($mdToast.simple()
                  .content(err.toString())
                  .hideDelay(5000)
                  .position("top right"));
            });
        }
        // 保存一个Uga问题
        $scope.save = function() {
                var newEntity = $scope.entity;
                if (newEntity.id > 0) { // 更新一个Uga问题
                    $ugaManage.update(newEntity.id, newEntity).then(function(data) {
                        $mdToast.show($mdToast.simple()
                          .content('Uga问题更新成功')
                          .hideDelay(5000)
                          .position("top right"));
                         $location.path('/uga-question-list?is_official='+$scope.is_official);
                    }, function(err) {
                        $mdToast.show($mdToast.simple()
                          .content(err.toString())
                          .hideDelay(5000)
                          .position("top right"));
                    });
                } else { // 添加一个Uga问题
                    newEntity.status = 1;
                    newEntity.is_official = $scope.is_official;
                    $ugaManage.create(newEntity).then(function(data) {
                        $location.path('/uga-question-list?is_official=' + $scope.is_official);
                        $mdToast.show($mdToast.simple()
                          .content('Uga问题添加成功')
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

        // 跳转到创建一个问题的页面
        $scope.createQuestion = function() {
            $location.path('/uga-question/add');
        }

    	// 初始化活动列表
    	normalPagination($scope.is_official)

        // $ugaManage.fetch($scope.is_official).then(function(data) {
        //     $scope.list = data;
        // }, function(err) {
        //     alert(err);
        // });

        // 排序
        $scope.order = function(order) {
			$scope.orderBy = order;
            fetchPage($scope.is_official, 1,$scope.orderBy);
        }

        // 只看公开库
        $scope.viewPublic = function(is_official) {
            $scope.is_official = is_official;
			fetchPage($scope.is_official, 1,$scope.orderBy);
        }

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

            $ugaManage.ugaPageMeta(type, $scope.modelPagination.itemsPerPage).then(function(total) {
                $scope.modelPagination.totalItems = total;
            });

            $scope.list = fetchPage(type, $scope.modelPagination.currentPage);
        }

        $scope.changePage = function(type, page) {
            fetchPage($scope.is_official, page);
        }
        $scope.prev = function(type) {
            var page = $scope.modelPagination.currentPage - 1;
            if (page < 1) {
                page = 1;
            }
            fetchPage($scope.is_official, page ,$scope.orderBy = order);
        }
        $scope.next = function(type) {
            var page = $scope.modelPagination.currentPage + 1;
            if (page > $scope.modelPagination.totalItems) {
                page = $scope.modelPagination.totalItems;
            }
            fetchPage($scope.is_official, page ,$scope.orderBy = order);
        }

        // 分页
        function fetchPage(type, page, order) {
            $ugaManage.fetchPage(type, page , order).then(function(data) {
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

angular.module('controllers').controller('UgaQuestionListCtrl', [
    '$scope', '$ugaManage', '$routeParams',
    function($scope, $ugaManage, $routeParams) {

        $scope.is_official = $routeParams.is_official;

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
                itemsPerPage: 4, //每页多少条
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

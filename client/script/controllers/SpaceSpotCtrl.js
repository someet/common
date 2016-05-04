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
            itemsPerPage: 20, //每页多少条
            pageChange: function() {
              fetchPage(type, this.currentPage);
            }
          };

          $spaceSpotManage.modelPageMeta(type, $scope.modelPagination.itemsPerPage).then(function(total) {
            $scope.modelPagination.totalItems = total;
          });

          $scope.userList = fetchPage(type, $scope.modelPagination.currentPage);
        }

        function fetchPage(type, page) {
          $spaceSpotManage.fetchPage(type, page).then(function(modelList) {
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

      }
  ]);
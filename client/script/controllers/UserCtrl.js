angular.module('controllers')
    .controller('UserAddCtrl', ['$scope', '$location', '$userManage', '$mdDialog', function($scope, $location, $userManage, $mdDialog) {
        $scope.$parent.pageName = '添加用户';
        $scope.user = {
            username: "",
            email: "",
            password: "",
            password1: ""
        };
        $scope.checkUserResult = '';

        $scope.createUser = function() {

            var newUser = {
                username: $scope.user.username,
                email: $scope.user.email,
                password: $scope.user.password
            };

            if ($scope.checkUserResult == '') {
                $userManage.add(newUser).then(function() {
                    $location.path('/user');
                }, function(err) {
                    $scope.syncing = false;
                });
            } else {
                alert($scope.checkUserResult);
            }
        }

        function checkUser() {
            if ($scope.user.password == "") {
                $scope.checkUserResult = "密码不能为空！";
            }
            if ($scope.user.password != $scope.user.password1) {
                $scope.checkUserResult = "两次密码不一致！";
            }
        }
    }])



    .controller('UserListCtrl', ['$scope', '$routeParams', '$location', '$userManage', function($scope, $routeParams, $location, $userManage) {
        $scope.$parent.pageName = '用户管理';
        $scope.today = new Date();
        $scope.userPageList = [];

        function modelPagination(listtype){
            $scope.modelPagination = {
                totalItems: 0,
                currentPage: 1,
                maxSize: 5,
                itemsPerPage: 20, //每页多少条
            };
            fetchPage(listtype);
        }


        $userManage.fetchUserAppealList().then(function(data) {
            $scope.countAppealnum = data;
        });

        $scope.isActive = function(type_id) {
            var route = "/member/list/" + type_id;
            if (type_id === "all") {
                route = "/member"
            }
            return route === $location.path();
        }
        // 改变页数
        $scope.pageChange = function(){
            if (!$scope.search) {
                fetchPage(listtype);
            }else{
                $scope.searchUser();
            }
        }

        function fetchPage(type) {
            $userManage.fetchPage(type, $scope.modelPagination.currentPage).then(function(data) {
                $scope.userList = data.users;
                $scope.modelPagination.totalItems = data.totalCount;
            });
        }
        //搜索用户
        $scope.searchUser = function() {
            $userManage.search($scope.search,$scope.modelPagination.currentPage).then(function(data) {
                $scope.userList = data.users;
                $scope.modelPagination.totalItems = data.totalCount;
            });
        }


        // 查看用户详情
        $scope.viewUser = function(user) {
            $location.path('/member/' + user.id);
        }

        $scope.createUserPage = function() {
            $location.path('/user/add');
        }

        $scope.remove = remove;

        function remove($index, $event) {
            var user = $scope.userList[$index],
                uid = parseInt(user.id, 10);

            $userManage.delete(uid).then(function() {
                $scope.userList.splice($index, 1);
            });
        }

        var listtype = $routeParams.type;

        switch (listtype) {
            case 'white': //白名单
            case 'black': //黑名单
            case 'pma': //PMA
            case 'founder': //发起人
            case 'admin': //管理员
            case 'delete': //已删除
            case 'appeal': //黄牌申诉用户
                modelPagination(listtype);
                break;
            default:
                modelPagination();
                break;
        }

    }])






   
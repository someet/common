angular.module('controllers')
.controller('UserAddCtrl', ['$scope', '$location', '$userManage', function($scope, $location, $userManage){
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

    if($scope.checkUserResult == ''){
      $userManage.add(newUser).then(function() {
        $location.path('/user');
      }, function(err) {
        console.log(err);
        $scope.syncing = false;
      });
    } else {
      alert($scope.checkUserResult);
    }
  }


  function checkUser(){
    if($scope.user.password == ""){
      $scope.checkUserResult = "密码不能为空！";
    }
    if($scope.user.password != $scope.user.password1){
      $scope.checkUserResult = "两次密码不一致！";
    }
  }
}])
  .controller('UserListCtrl', ['$scope', '$location', '$userManage', function($scope, $location, $userManage){
    $scope.$parent.pageName = '用户管理';

    $scope.userList = [];
    $scope.userPageList = [];

    //$userManage.fetchPage().then(function(data) {
    //  $scope.userList = data;
    //});

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

    normalPagination();

    function normalPagination() {
      $scope.userPagination = {
        totalItems: 0,
        currentPage: 1,
        maxSize: 5,
        itemsPerPage: 2,
        pageChange: function() {
          fetchPage(this.currentPage);
        }
      };

      $userManage.userPageMeta($scope.userPagination.itemsPerPage).then(function(total) {
        $scope.userPagination.totalItems = total;
      });

      $scope.userList = fetchPage($scope.userPagination.currentPage);
    }

    $scope.changePage = function(page) {
      fetchPage(page);
    }
    $scope.prev = function () {
      var page = $scope.userPagination.currentPage - 1;
      if(page < 1){
        page = 1;
      }
      fetchPage(page);
    }
    $scope.next = function(){
      var page = $scope.userPagination.currentPage + 1;
      if(page > $scope.userPagination.totalItems){
        page = $scope.userPagination.totalItems;
      }
      fetchPage(page);
    }

    function fetchPage(page) {
      $userManage.fetchPage(page).then(function (userList) {
        $scope.userList = userList;
        $scope.userPagination.currentPage = page;
        //纯js分页
        if ($scope.userPagination.currentPage > 1 && $scope.userPagination.currentPage < $scope.userPagination.totalItems) {
          $scope.pages = [
            $scope.userPagination.currentPage - 1,
            $scope.userPagination.currentPage,
            $scope.userPagination.currentPage + 1
          ];
        } else if ($scope.userPagination.currentPage <= 1 && $scope.userPagination.totalItems > 1) {
          $scope.userPagination.currentPage = 1;
          $scope.pages = [
            $scope.userPagination.currentPage,
            $scope.userPagination.currentPage + 1
          ];
        } else if ($scope.userPagination.currentPage >= $scope.userPagination.totalItems && $scope.userPagination.totalItems > 1) {
          $scope.userPagination.currentPage = $scope.userPagination.totalItems;
          $scope.pages = [
            $scope.userPagination.currentPage - 1,
            $scope.userPagination.currentPage
          ];
        }
      });
    }


  }])
  .controller('UserUpdateCtrl', ['$scope', '$routeParams', '$userManage', function($scope, $routeParams, $userManage){
    $scope.$parent.pageName = '用户详情';

    var userId = $routeParams.id;
    if(userId != null){
      var params = {
       id: userId
      }
    }
    $userManage.fetch(params).then(function(data) {
      $scope.user = data;
    }, function (err) {
      alert(err);
    });

    $scope.updateUser = function() {
      var userData = {
        email: $scope.user.email
      };

      $userManage.update(userId, userData).then(function(data){
        alert("修改成功");
      }, function (err) {
        alert(err);
      });
    }
  }])
;

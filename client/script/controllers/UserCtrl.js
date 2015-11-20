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
  .controller('UserListCtrl', ['$scope', '$routeParams', '$location', '$userManage', function($scope, $routeParams, $location, $userManage){
    $scope.$parent.pageName = '用户管理';

    $scope.userList = [];
    $scope.userPageList = [];

    //$userManage.fetchPage().then(function(data) {
    //  $scope.userList = data;
    //});


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
      console.log(listtype);
      //白名单
      if (listtype == 'white') {
        $userManage.fetchWhiteList().then(function(data) {
          $scope.userList = data;
        });
        //黑名单
      } else if (listtype == 'black') {
        $userManage.fetchBlackList().then(function(data) {
          $scope.userList = data;
        });
        // pma
      } else if (listtype=='pma') {
        $userManage.fetchPmaList().then(function(data) {
          $scope.userList = data;
        });
        //发起人
      } else if (listtype=='founder') {
        $userManage.fetchFounderList().then(function(data) {
          $scope.userList = data;
        });

        // 正常的所有用户
      } else {
        normalPagination();
      }

    function normalPagination() {
      $scope.userPagination = {
        totalItems: 0,
        currentPage: 1,
        maxSize: 5,
        itemsPerPage: 20,
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
      $scope.data = {};

    $userManage.fetch(params).then(function(data) {
      var isFounder = false;
      var isPma = false;
      for(var i= 0, k=data.assignment.length; i<k; i++) {
        if (data.assignment[i].item_name == 'founder') {
          isFounder = true;
        }
        if (data.assignment[i].item_name == 'pma') {
          isPma = true;
        }
      }
      $scope.user = data;
      $scope.data.cb3 = data.in_white_list == 1;
      $scope.data.cb2 = isFounder;
      $scope.data.cb = isPma;
    }, function (err) {
      alert(err);
    });

    $scope.updateUser = function() {
      var userData = {
        email: $scope.user.email,
        bio: $scope.user.profile.bio
      };

      var user_id = $scope.user.id;

      var setup = $scope.data.cb3 == true;
      $userManage.setUserInWhiteList(user_id, setup).then(function (data) {
        console.log('设置用户为白名单成功');
      });

      var assignPMA = $scope.data.cb == true;
      $userManage.setUserAsPma(user_id, assignPMA).then(function (data) {
        console.log('设置用户为PMA成功');
      });

      var assignFounder = $scope.data.cb2 == true;
      $userManage.setUserAsFounder(user_id, assignFounder).then(function (data) {
        console.log('设置用户为Founder成功');
      });

      $userManage.update(user_id, userData).then(function(data){
        alert("修改成功");
      }, function (err) {
        alert(err);
      });
    }
  }])
;

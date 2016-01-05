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

    // tab
    $scope.isActive = function(type_id) {        
      var route = "/member/list/"+type_id;
      if (type_id === "all"){
        route = "/member"
      }
      console.log("member/list = " + route + "--" + $location.path());
      return route === $location.path();
      // return .indexOf(route) != -1;
    }

    //搜索用户
    $scope.searchUser = function() {
      var username = $scope.username;
      $userManage.search(username).then(function (userList) {
        $scope.userList = userList;
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
      console.log(listtype);
      switch (listtype){
        case 'white'://白名单
        case 'black'://黑名单
        case 'pma'://PMA
        case 'founder'://发起人
        case 'admin'://管理员
        case 'delete'://已删除
          normalPagination(listtype);
          break;
        default:
          normalPagination();
          break;
      }

    function normalPagination(type) {
      $scope.modelPagination = {
        totalItems: 0,
        currentPage: 1,
        maxSize: 5,
        itemsPerPage: 20,//每页多少条
        pageChange: function() {
          fetchPage(type, this.currentPage);
        }
      };

      $userManage.userPageMeta(type, $scope.modelPagination.itemsPerPage).then(function(total) {
        $scope.modelPagination.totalItems = total;
      });

      $scope.userList = fetchPage(type, $scope.modelPagination.currentPage);
    }

    $scope.changePage = function(type, page) {
      fetchPage(type, page);
    }
    $scope.prev = function (type) {
      var page = $scope.modelPagination.currentPage - 1;
      if(page < 1){
        page = 1;
      }
      fetchPage(type, page);
    }
    $scope.next = function(type){
      var page = $scope.modelPagination.currentPage + 1;
      if(page > $scope.modelPagination.totalItems){
        page = $scope.modelPagination.totalItems;
      }
      fetchPage(type, page);
    }

    function fetchPage(type, page) {
      $userManage.fetchPage(type, page).then(function (userList) {
        $scope.userList = userList;
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


  }])
  .controller('UserUpdateCtrl', ['$scope', '$location', '$routeParams', '$userManage','$mdToast', function($scope, $location, $routeParams, $userManage, $mdToast){
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

          // 取消
      $scope.cancel = function() {
        $location.path('/member');
      }

    $scope.updateUser = function() {
      var userData = {
        email: $scope.user.email,
        password: $scope.user.password,
        bio: $scope.user.profile.bio
      };

      var user_id = $scope.user.id;

      var setup = $scope.data.cb3 == true;
      $userManage.setUserInWhiteList(user_id, setup).then(function (data) {
        console.log('设置用户为白名单成功');
      });

      var assignPMA = $scope.data.cb == true ? 1 : 0;
      $userManage.setUserAsPma(user_id, assignPMA).then(function (data) {
        console.log('设置用户为PMA成功');
      });

      var assignFounder = $scope.data.cb2 == true ? 1 : 0;
      $userManage.setUserAsFounder(user_id, assignFounder).then(function (data) {
        console.log('设置用户为Founder成功');
      });

      $userManage.update(user_id, userData).then(function(data){
        $mdToast.show($mdToast.simple()
              .content('设置用户属性成功')
              .hideDelay(5000)
              .position("top right"));
        $location.path('/member');
      }, function (err) {
        $mdToast.show($mdToast.simple()
              .content('设置用户属性发生错误：' + err + '')
              .hideDelay(5000)
              .position("top right"));
      });
    }
  }])
;

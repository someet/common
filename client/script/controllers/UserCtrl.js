angular.module('controllers')
.controller('UserAddCtrl', ['$scope', '$location', '$userManage', function($scope, $location, $userManage){
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
    $userManage.fetch().then(function(data) {
      $scope.userList = data;
    });

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

  }])
  .controller('UserUpdateCtrl', ['$scope', '$routeParams', '$userManage', function($scope, $routeParams, $userManage){
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

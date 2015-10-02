angular.module('controllers')
.controller('UserAddCtrl', ['$scope', '$userManage', function($scope, $userManage){
  $scope.user = {
    username: "langshuang",
    password: "",
    password1: "",
    rule: ""
  };
  $scope.checkUserResult = '';

  $scope.createUser = function() {

    var newUser = {
      username: $scope.user.username,
      password: $scope.user.password,
      rule: $scope.user.rule
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
  .controller('UserListCtrl', ['$scope', '$userManage', function($scope, $userManage){
    $userManage.fetch().then(function(data) {
      $scope.userList = data;
    });

    $scope.remove = remove;

    function remove($index, $event) {
      var user = $scope.userList[$index],
        uid = parseInt(user.id, 10);

      $userManage.delete(uid).then(function() {
        $scope.userList.splice($index, 1);
      });
    }

  }])
  .controller('UserUpdateCtrl', ['$scope', '$userManage', function($scope, $userManage){
    $userManage.fetch().then(function(data) {
      $scope.userList = data;
    });
  }])
;

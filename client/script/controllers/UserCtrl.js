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
}]);

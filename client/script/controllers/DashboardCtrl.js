angular.module('controllers').controller('DashboardCtrl', [
  '$scope', '$mdDialog',
  function ($scope, $mdDialog) {
    $scope.$parent.pageName = 'Dashboard';

    $scope.clickMe = function () {
      alert('clicked!');
    };

    $scope.showAlert = function (event) {
      $mdDialog.show($mdDialog.alert()
          .parent(angular.element(document.querySelector('#content')))
          .title('这里是标题')
          .content('这里是内容')
          .ariaLabel('知道了')
          .ok('我知道了！')
          .targetEvent(event)
      );
    };
    $scope.showConfirm = function (event) {
      var confirm = $mdDialog.confirm()
        .title('你确定要删除这个活动吗？')
        // .content('All of the banks have agreed to <span class="debt-be-gone">forgive</span> you your debts.')
        .ariaLabel('Lucky day')
        .targetEvent(event)
        .ok('确定')
        .cancel('手滑点错了');
      $mdDialog.show(confirm).then(function() {
        alert('确定删除')
      }, function() {
        alert('不删除')
      });
    };
  }]);

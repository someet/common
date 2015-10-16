angular.module('controllers')
  .controller('SmsTemplateCtrl',
  ['$scope', '$http', '$location', '$smsTemplateManage', 'lodash', '$mdToast', '$mdDialog',
    function ($scope, $http, $location, $smsTemplateManage, lodash, $mdToast, $mdDialog) {

      $scope.$parent.pageName = '消息模板管理';
      // 消息模板列表
      $smsTemplateManage.fetch().then(function (data) {
        $scope.list = data;
      }, function (err) {
        alert(err);
      });

      // 跳转到更新消息模板
      $scope.update = function (type) {
        $location.path('/sms-template/' + type.id);
      };

      // 删除消息模板
      $scope.delete = function (type) {
        var confirm = $mdDialog.confirm()
          .title('确定要删除消息模板“' + type.name + '”吗？')
          .ariaLabel('delete sms template')
          .ok('确定删除')
          .cancel('手滑点错了，不删');

        $mdDialog.show(confirm).then(function () {
          $smsTemplateManage.delete(type).then(function (data) {
            lodash.remove($scope.list, function (tmpRow) {
              return tmpRow == type;
            });

            $mdToast.show($mdToast.simple()
              .content('删除消息模板“' + type.name + '”成功')
              .hideDelay(5000)
              .position("top right"));

          }, function (err) {
            $mdToast.show($mdToast.simple()
              .content(err.toString())
              .hideDelay(5000)
              .position("top right"));
          });
        });
      };

      // 跳转到添加页面
      $scope.createPage = function () {
        $location.path('/sms-template/add');
      }
    }])
  .controller('SmsTemplateViewCtrl',
  ['$scope', '$http', '$routeParams', '$location', '$smsTemplateManage', '$mdToast',
    function ($scope, $http, $routeParams, $location, $smsTemplateManage, $mdToast) {

      // 获取GET参数的id
      var id = $routeParams.id;

      $scope.$parent.pageName = id>0 ? "更新消息模板" : "添加消息模板";
      // 查看单个消息模板
      $smsTemplateManage.fetch(id).then(function (data) {
        $scope.entity = data;
      }, function (err) {
        $location.path('/sms-template');
      });

      // 保存消息模板
      $scope.save = function () {
        var entity = $scope.entity;
        var newEntity = {name: entity.name, template:entity.template, status: entity.status};
        if (entity.id > 0) { // 更新
          $smsTemplateManage.update(entity.id, newEntity).then(function (data) {
            $location.path('/sms-template');
            $mdToast.show($mdToast.simple()
                .content('修改成功')
                .hideDelay(5000)
                .position("top right"));
          }, function (err) {
            $mdToast.show($mdToast.simple()
                .content(err.toString())
                .hideDelay(5000)
                .position("top right"));
          })
        } else { // 添加
          $smsTemplateManage.create(newEntity).then(function (data) {
            $location.path('/sms-template');
            $mdToast.show($mdToast.simple()
                .content('添加消息模板成功')
                .hideDelay(5000)
                .position("top right"));
          }, function (err) {
            $mdToast.show($mdToast.simple()
                .content(err.toString())
                .hideDelay(5000)
                .position("top right"));
          });
        }
      };

      // 在修改页面点击取消
      $scope.cancel = function () {
        $location.path('/sms-template');
      }
    }]);


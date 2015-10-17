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


      $scope.data = {
        orightml: '<h2>Try me!</h2>'
      };
      $scope.data.htmlcontent = $scope.data.orightml;
      //$scope.$watch('data.htmlcontent', function(val){console.log('htmlcontent changed to:', val);});
      $scope.disabled = false;
      $scope.canEdit = true;
      $scope.changetesth1 = function() {
        textAngularManager.updateToolbarToolDisplay('test', 'h1', {
          buttontext: 'Heading 1'
        });
      };
      $scope.changeallh2 = function() {
        textAngularManager.updateToolDisplay('h2', {
          buttontext: 'Heading 2'
        });
      };
      $scope.changeallh = function() {
        var data = {};
        for (var i = 1; i < 7; i++) {
          data['h' + i] = {
            buttontext: 'Heading ' + i
          };
        }
        textAngularManager.updateToolsDisplay(data);
      };
      $scope.resettoolbar = function() {
        textAngularManager.resetToolsDisplay();
      };
      $scope.iconsallh = function() {
        var data = {};
        for (var i = 1; i < 7; i++) {
          data['h' + i] = {
            iconclass: 'fa fa-flag',
            buttontext: i
          };
        }
        textAngularManager.updateToolsDisplay(data);
      };
      $scope.submit = function() {
        console.log('Submit triggered');
      };
      $scope.clear = function() {
        $scope.data = {
          orightml: '<h2>Try me!</h2><p>textAngular is a super cool WYSIWYG Text Editor directive for AngularJS</p><p><b>Features:</b></p><ol><li>Automatic Seamless Two-Way-Binding</li><li>Super Easy <b>Theming</b> Options</li><li style="color: green;">Simple Editor Instance Creation</li><li>Safely Parses Html for Custom Toolbar Icons</li><li class="text-danger">Doesn&apos;t Use an iFrame</li><li>Works with Firefox, Chrome, and IE9+</li></ol><p><b>Code at GitHub:</b> <a href="https://github.com/fraywing/textAngular">Here</a> </p>'
        };
      };
      $scope.reset = function() {
        $scope.data.htmlcontent = $scope.data.orightml;
      };
      $scope.testPaste = function($html) {
        console.log('Hit Paste', arguments);
        return '<p>Jackpot</p>';
      };




    }]);


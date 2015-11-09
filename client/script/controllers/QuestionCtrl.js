angular.module('controllers')
    .controller('QuestionViewCtrl',
    ['$scope', '$location', '$routeParams', '$questionManage', '$mdToast',
      function ($scope, $location, $routeParams, $questionManage, $mdToast) {
        $scope.$parent.pageName = '查看问题';
        var activity_id = $routeParams.activity_id;
        $scope.activity_id = activity_id;


        $scope.status_list = [
          {
            id: 10,
            title: "打开",
          },
          {
            id: 20,
            title: "关闭",
          },
        ];

        $questionManage.fetchByActivityId(activity_id).then(function(data) {
          $scope.entity = data;
        }, function(err) {

        });

        $scope.cancel = function() {
          $location.path('/activity/list/0');
        };

        // 添加/修改问题
        $scope.save = function(){
          var newEntity = {
            activity_id: $scope.activity_id,
            title: $scope.entity.title,
            desc: $scope.entity.desc,
            status: $scope.entity.status,
            questionItemList: [],
          };

          if ($scope.entity.id > 0) { // 更新

            newEntity.id = $scope.entity.id;
            for(var k in $scope.entity.questionItemList) {
              var questionItem = {
                question_id: newEntity.id,
                id: $scope.entity.questionItemList[k].id,
                label: $scope.entity.questionItemList[k].label,
              };
              newEntity.questionItemList.push(questionItem);
            }

            $questionManage.update($scope.entity.id, newEntity).then(function(data) {
              $location.path('/activity/list/0');
              $mdToast.show($mdToast.simple()
                  .content('问题保存成功')
                  .hideDelay(5000)
                  .position("top right"));
            }, function(err) {
              alert(err);
            });
          } else { // 新建
            var questionItem1 = {
              label: $scope.entity.q1,
            };
            var questionItem2 = {
              label: $scope.entity.q2,
            };
            var questionItem3 = {
              label: $scope.entity.q3,
            };
            newEntity.questionItemList.push(questionItem1);
            newEntity.questionItemList.push(questionItem2);
            newEntity.questionItemList.push(questionItem3);
            $questionManage.create(newEntity).then(function (data) {
              $location.path('/activity/list/0');
              $mdToast.show($mdToast.simple()
                  .content('问题添加成功')
                  .hideDelay(5000)
                  .position("top right"));
            }, function(err) {
              alert(err);
            });
          }
        };

      }]);

angular.module('controllers')
    .controller('QuestionAddCtrl',
    ['$scope', '$location', '$routeParams', '$questionManage', '$questionItemManage', '$mdToast',
      function ($scope, $location, $routeParams, $questionManage, $questionItemManage, $mdToast) {
          //$location.path('/question/add');

        $scope.$parent.pageName = '添加活动报名表单';
        var activityid = $routeParams.activityid;
        $scope.activityid = activityid;

        $scope.cancel = function() {
          $location.path('/activity/');
        };

        $scope.save = function(){
          /*
          if ($scope.id > 0) {
            newE.id = $scope.id;
            $questionManage.update($scope.id, newE).then(function(data) {
              alert('保存成功');
              $location.path('/activity');
            })
          } else {
          */
            // 添加一个表单主表
            var newE = {
              activity_id: $scope.activityid,
              title: $scope.title,
              desc: $scope.desc,
              questionList: [$scope.q1, $scope.q2, $scope.q3]
            };
            $questionManage.create(newE).then(function(data){
              // 添加三个问题
              var questionList = [$scope.q1, $scope.q2, $scope.q3];
              for (var k in questionList ){
                var newQI = {
                  label: questionList[k],
                  question_id: data.id
                };
                console.log(newQI);
                $questionItemManage.create(newQI).then(function(data){
                }), function(err){
                  alert(err);
                }
              }

              $mdToast.show($mdToast.simple()
                  .content('表单添加成功')
                  .hideDelay(5000)
                  .position("top right"));

              $location.path('/activity/');
            }, function(err){
              alert(err);
            })
          //}
        };
    }]);
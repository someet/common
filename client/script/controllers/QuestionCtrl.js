angular.module('controllers')
    .controller('QuestionAddCtrl',
    ['$scope', '$location', '$routeParams', '$questionManage', '$questionItemManage',
      function ($scope, $location, $routeParams, $questionManage, $questionItemManage) {
          //$location.path('/question/add');

        var activityid = $routeParams.activityid;
        $scope.activityid = activityid;

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
                alert(questionList[k]);
                var newQI = {
                  label: questionList[k],
                  question_id: data.id
                };
                console.log(newQI);
                $questionItemManage.create(newQI).then(function(data){
                  alert(data);
                }), function(err){
                  alert(err);
                }
              }
            }, function(err){
              alert(err);
            })
          //}
        };
    }]);
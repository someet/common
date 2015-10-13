angular.module('controllers')
    .controller('QuestionViewCtrl',
    ['$scope', '$location', '$routeParams', '$questionManage', '$questionItemManage', '$mdToast',
      function ($scope, $location, $routeParams, $questionManage, $questionItemManage, $mdToast) {
        $scope.$parent.pageName = '修改问题';
        var activityid = $routeParams.activityid;
        $scope.activityid = activityid;
        console.log(activityid);

        $questionManage.fetchByActivityid(activityid).then(function(data){
          $scope.question = data;

          var questionData = {
            question_id: $scope.question.id,
            activity_id: $scope.question.activity_id,
            answerList: []
          };

          for(var k in $scope.question.questionList) {
            var question = {
              question_item_id: $scope.question.questionList[k].id,
              question_id: answerData.question_id,
              question_label: $scope.question.questionList[k].label,
              question_value: $scope.question.questionList[k].answer
            };
            questionData.answerList.push(question);
          }
        });


        $scope.save = function(){
          var newE = {
            activity_id: $scope.question.activityid,
            title: $scope.question.title,
            desc: $scope.question.desc,
          };
          if ($scope.id > 0) {
            newE.id = $scope.id;
            $questionManage.update($scope.id, newE).then(function(data) {
              alert('保存成功');
              $location.path('/activity');
            })
          }
        };

      }])
    .controller('QuestionAddCtrl',
    ['$scope', '$location', '$routeParams', '$questionManage', '$questionItemManage', '$mdToast',
      function ($scope, $location, $routeParams, $questionManage, $questionItemManage, $mdToast) {
          //$location.path('/question/add');

        $scope.$parent.pageName = '添加活动报名表单';
        var activity_id = $routeParams.activity_id;
        $scope.activity_id = activity_id;

        $scope.cancel = function() {
          $location.path('/activity/');
        };

        $scope.save = function(){
          var newE = {
            activity_id: $scope.activityid,
            title: $scope.title,
            desc: $scope.desc,
          };
          if ($scope.id > 0) {
            newE.id = $scope.id;
            $questionManage.update($scope.id, newE).then(function(data) {
              alert('保存成功');
              $location.path('/activity');
            })
          } else {
            // 添加一个表单主表
            var newE = {
              activity_id: $scope.activity_id,
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
          }
        };
    }]);
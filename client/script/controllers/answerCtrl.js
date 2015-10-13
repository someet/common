angular.module('controllers')
    .controller('AnswerAddCtrl',
    ['$scope', '$location', '$routeParams', '$answerManage',
      function ($scope, $location, $routeParams, $answerManage) {

        var questionId = $routeParams.id;
        $scope.questionId = questionId;

        $answerManage.fetch(questionId).then(function(data){
          $scope.questions = data;
        });

        $scope.createAnswer = function(){
          var answerData = {
            question_id: $scope.questionId,
            activity_id: $scope.questions.activity_id,
            answerList: []
          };

          for(var k in $scope.questions.questionList) {
            var answer = {
              question_item_id: $scope.questions.questionList[k].id,
              question_id: answerData.question_id,
              question_label: $scope.questions.questionList[k].label,
              question_value: $scope.questions.questionList[k].answer
            };
            answerData.answerList.push(answer);
          }

          $answerManage.create(answerData).then(function(data) {
            $location.path('/#/answer');
          }, function(err){
            alert(err);
          });
        };

    }]);
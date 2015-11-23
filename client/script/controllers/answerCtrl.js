angular.module('controllers')
    .controller('AnswerCtrl',
    ['$scope', '$location', '$routeParams', '$answerManage', '$mdToast',
      function ($scope, $location, $routeParams, $answerManage, $mdToast) {
        $scope.today = new Date();

        var activity_id = $routeParams.activity_id;
        $answerManage.fetchByActivityId(activity_id).then(function (data) {
          $scope.list = data;
          $scope.answerItemList = data[0].answerItemList;
        }, function (err) {

        });

        //查看一个报名信息
        $scope.view = function(entity) {
          $scope.answerItemList = entity.answerItemList;
          $scope.entity = entity;
        }

        //通过审核
        $scope.pass = function(entity) {
          $answerManage.filter(entity.id, 1).then(function(data) {
            $mdToast.show($mdToast.simple()
                .content('已通过')
                .hideDelay(5000)
                .position("top right"));
          }, function(err){
            $mdToast.show($mdToast.simple()
                .content(err.toString())
                .hideDelay(5000)
                .position("top right"));
          });
        }

        //拒绝通过
        $scope.reject = function(entity) {
          $answerManage.filter(entity.id, 0).then(function(data) {
            $mdToast.show($mdToast.simple()
                .content('已拒绝')
                .hideDelay(5000)
                .position("top right"));
          }, function(err){
            $mdToast.show($mdToast.simple()
                .content(err.toString())
                .hideDelay(5000)
                .position("top right"));
          });
        }

      }])
    .controller('AnswerAddCtrl',
    ['$scope', '$location', '$routeParams', '$questionManage', '$answerManage',
      function ($scope, $location, $routeParams, $questionManage, $answerManage) {

        var activity_id = $routeParams.activity_id;
        $scope.activity_id = activity_id;

        $questionManage.fetchByActivityId(activity_id).then(function(data){
          $scope.entity = data;
        });

        $scope.createAnswer = function(){
          var answerData = {
            question_id: $scope.entity.id,
            activity_id: $scope.entity.activity_id,
            answerItemList: []
          };

          for(var k in $scope.entity.questionItemList) {
            var answer = {
              question_item_id: $scope.entity.questionItemList[k].id,
              question_id: answerData.question_id,
              question_label: $scope.entity.questionItemList[k].label,
              question_value: $scope.entity.questionItemList[k].answer
            };
            answerData.answerItemList.push(answer);
          }

          $answerManage.create(answerData).then(function(data) {
            $location.path('/activity/list/0');
            $mdToast.show($mdToast.simple()
                .content('报名活动成功')
                .hideDelay(5000)
                .position("top right"));

          }, function(err){
            alert(err);
          });
        };
    }]);
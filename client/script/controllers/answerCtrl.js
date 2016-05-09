angular.module('controllers')
    .controller('AnswerCtrl',
    ['$scope', '$location', '$routeParams', '$answerManage', '$mdToast',
      function ($scope, $location, $routeParams, $answerManage, $mdToast) {
        $scope.today = new Date();

        var activity_id = $routeParams.activity_id;
        $answerManage.fetchByActivityId(activity_id).then(function (data) {
          $scope.list = data.model;
          $scope.goodScore = data.good_score;
          $scope.middleScore = data.middle_score;
          $scope.badScore = data.bad_score;
          $scope.sponsorScore = data.sponsor_score.toFixed(2);

          $scope.countData = {
            apply:$scope.status
          }

          var outInfo = '';
          angular.forEach(data.model, function(list,index,array){

          
            outInfo += list.user.username +'('+list.user.profile.name +') 手机：'+ list.user.mobile +' 微信：'+list.user.wechat_id +' 职业：'+list.user.profile.occupation+' 状态：'+list.status+'\n';
            outInfo += list.user.profile.headimgurl+'\n';
              angular.forEach(list.answerItemList, function(item,index,array){
                outInfo += item.question_value +'\n';
              })

              outInfo += '\n';
          });

          console.log(outInfo);
          $scope.answerItemList = data.model[0].answerItemList;
          // console.log(data);
          // 将所有的反馈给放到一个数组
          // var feedbacks = [];
          // angular.forEach(data, function(list,index,array){
          //   if (typeof list.feedbacks != "undefined" ) {
          //     feedbacks.push(list.feedback);
          //   }
          //     console.log(array);
          // });

          if (data.feedbacks.length > 0) {
            var countScore = 0;
            angular.forEach(data.feedbacks, function(list,index,array){
              countScore += ((list.stars * 0.8) + (list.sponsor_stars*0.2));
            });
            var countScore = countScore/data.feedbacks.length;
            $scope.countScore = countScore.toFixed(2);
          } else {
            $scope.countScore = 0;
          }
        }, function (err) {

        });

        //查看一个报名信息
        $scope.view = function(entity) {
          $scope.dbtn = false;
          $scope.answerItemList = entity.answerItemList;
          $scope.entity = entity;
        }

        //取消报名，未取消报名
        $scope.apply = function(entity, status) {
          $answerManage.apply(entity.id, status).then(function(data) {
            $mdToast.show($mdToast.simple()
              .content('已操作成功')
              .hideDelay(5000)
              .position("top right"));
          }, function(err){
            $mdToast.show($mdToast.simple()
              .content(err.toString())
              .hideDelay(5000)
              .position("top right"));
          });
        }        

        //未到,迟到，准时功能, 0未到 1 迟到 2准时
        $scope.arrive = function(entity, status) {
          $answerManage.arrive(entity.id, status).then(function(data) {
            $mdToast.show($mdToast.simple()
              .content('已操作成功')
              .hideDelay(5000)
              .position("top right"));
          }, function(err){
            $mdToast.show($mdToast.simple()
              .content(err.toString())
              .hideDelay(5000)
              .position("top right"));
          });
        }

        //请假状态 查从                                                                                          
        $scope.leave = function(entity, status) {
          $answerManage.leave(entity.id, status).then(function(data) {
            $mdToast.show($mdToast.simple()
              .content('已操作成功')
              .hideDelay(5000)
              .position("top right"));
          }, function(err){
            $mdToast.show($mdToast.simple()
              .content(err.toString())
              .hideDelay(5000)
              .position("top right"));
          });
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
        //发送消息
        $scope.feedbackResult = '点击按钮发送通知';

        $scope.sendMessage = function(entity,dbtn){
          $scope.dbtn = true;
          var user_id = entity.user.id;
          var activity_id = entity.activity_id;
          // console.log(user_id + '----'+activity_id);
          $answerManage.sendMessage(user_id,activity_id).then(function(data) {
            // console.log(data);
            // var feedbackResult = '未手动发送过通知';
            if (data.status == 0) {
                  $mdToast.show($mdToast.simple()
                  .content('发送成功！')
                  .hideDelay(5000)
                  .position("top right"));
              $scope.feedbackResult = data.sms +'--'+data.wechatResult;
            }else if (data.status == 2) {
              $mdToast.show($mdToast.simple()
                  .content('还没有筛选不需要发通知;-)')
                  .hideDelay(5000)
                  .position("top right"));
              
            }else{
                  $mdToast.show($mdToast.simple()
                  .content('消息发送失败')
                  .hideDelay(5000)
                  .position("top right"));
              $scope.feedbackResult = '消息发送失败';
            }
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
angular.module('controllers').controller('ModalInstanceCtrl', ['$scope', '$uibModalInstance', 'entity', '$questionManage', '$mdDialog', 'lodash', '$location' ,
    '$mdToast',
    function($scope, $uibModalInstance, entity, $questionManage, $mdDialog, lodash,$location,$mdToast) {
        $scope.questionItem = {};
        $scope.activity = entity;
        $questionManage.fetchByActivityId(entity.id).then(function(data) {
            $scope.entity = data;
        }, function(err) {
            alert(err);
        });

        // 设置问题
        $scope.questionSave = function() {
            var newEntity = {
                activity_id: entity.id,
                questionItemList: [],
            };

            if ($scope.entity != null && $scope.entity.id > 0) { // 更新
                newEntity.id = $scope.entity.id;
                for (var k in $scope.entity.questionItemList) {
                    var questionItem = {
                        question_id: newEntity.id,
                        id: $scope.entity.questionItemList[k].id,
                        label: $scope.entity.questionItemList[k].label,
                    };
                    newEntity.questionItemList.push(questionItem);
                }
                console.log(questionItem);
                $questionManage.update($scope.entity.id, newEntity).then(function(data) {
                    $mdToast.show($mdToast.simple()
                        .content('问题保存成功')
                        .hideDelay(5000)
                        .position("top right"));
                    $uibModalInstance.close(data);
                }, function(err) {
                    alert(err);
                });
            } else { // 新建
                var questionItem1 = {
                    label: $scope.questionItem.q1,
                };
                var questionItem2 = {
                    label: $scope.questionItem.q2,
                };
                var questionItem3 = {
                    label: $scope.questionItem.q3,
                };
                newEntity.questionItemList.push(questionItem1);
                newEntity.questionItemList.push(questionItem2);
                newEntity.questionItemList.push(questionItem3);
                $questionManage.create(newEntity).then(function(data) {
                    $mdToast.show($mdToast.simple()
                        .content('问题添加成功')
                        .hideDelay(5000)
                        .position("top right"));
                    $uibModalInstance.close(data);
                }, function(err) {
                    alert(err);
                });
            }
            // 关闭摸态框
        };

        $scope.cancel = function() {
            $uibModalInstance.dismiss('cancel');
        };
    }
]);

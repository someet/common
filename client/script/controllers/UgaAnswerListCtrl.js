angular.module('controllers').controller('UgaAnswerListCtrl', [
    '$scope', '$routeParams', '$ugaAnswerManage', 'lodash', '$mdToast', '$mdDialog',
    function($scope, $routeParams, $ugaAnswerManage , lodash, $mdToast, $mdDialog) {
        var id = $routeParams.id;
        $ugaAnswerManage.fetch(id).then(function(data) {
            $scope.list = data;
        }, function(err) {
            alert(err);
        });
        // 删除问题的回答
        $scope.delete = function(entity, status) {
            console.log(entity.status);
            if (0 == status) {
                var confirm = $mdDialog.confirm()
                    .title('确定要删除“' + entity.content + '”吗？')
                    .ariaLabel('delete activity item')
                    .ok('确定删除')
                    .cancel('手滑点错了，不删');
            }else if (1 == status) {
                var confirm = $mdDialog.confirm()
                    .title('确定要还原“' + entity.content + '”吗？')
                    .ariaLabel('delete activity item')
                    .ok('确定还原')
                    .cancel('手滑点错了，不还原');
            }

            $mdDialog.show(confirm).then(function() {
                $ugaAnswerManage.delete(entity.id, status).then(function(data) {
                    console.log(status);
                    entity.status = status;
                    if (0 == status) {
                        $mdToast.show($mdToast.simple()
                            .content('删除回答“' + entity.content + '”成功')
                            .hideDelay(5000)
                            .position("top right"));
                    }

                }, function(err) {
                    $mdToast.show($mdToast.simple()
                        .content(err.toString())
                        .hideDelay(5000)
                        .position("top right"));
                });
            });
        };
    }
])

 angular.module('controllers')
 .controller('UserUpdateCtrl', ['$scope', '$location', '$routeParams', '$qiniuManage', '$qupload', '$userManage', '$mdToast', '$mdDialog', function($scope, $location, $routeParams, $qiniuManage, $qupload, $userManage, $mdToast, $mdDialog) {
        $scope.$parent.pageName = '用户详情';
        // 选择类别（黄牌原因理由）1 迟到 2请假 3爽约 4带人
        $scope.card_category_status = ['0', '1', '2', '3', '4', '5', '6'];

        var userId = $routeParams.id;
        if (userId != null) {
            var params = {
                id: userId
            }
        }
        $scope.selectHeader = null;

        var startHeader = function() {
            $qiniuManage.fetchUploadToken().then(function(token) {

                $qupload.upload({
                    key: '',
                    file: $scope.selectHeader.file,
                    token: token
                }).then(function(response) {
                    $qiniuManage.completelyUrl(response.key).then(function(url) {
                        $scope.profile.headimgurl = url;
                    });
                }, function(response) {}, function(evt) {
                    if ($scope.selectHeader !== null) {
                        $scope.selectHeader.progress.p = Math.floor(100 * evt.loaded / evt.totalSize);
                    }
                });

            });
        };

        $scope.headerAbort = function() {
            $scope.selectHeader.upload.abort();
            $scope.selectHeader = null;
        };

        $scope.onHeaderSelect = function($files) {
            $scope.selectHeader = {
                file: $files[0],
                progress: {
                    p: 0
                }
            };
            startHeader();
        };
        // qiniu upload 头像 end //



        // 用户报名的活动
        $userManage.fetchUserJoinActivity(userId).then(function(data) {
            $scope.joinActivity = data;
        })


        // 用户获得的黄牌
        $userManage.fetchUserYellowCard(userId).then(function(data) {
            $scope.yellowCardList = data;
            // console.log(data);
        })

        // 更新黄牌
        $scope.updateCategory = function(id, status) {
            var confirm = $mdDialog.confirm()
                .title('确定更新吗')
                .ariaLabel('update yellow card item')
                .ok('确定更新')
                .cancel('手滑点错了，不更新');

            $mdDialog.show(confirm).then(function() {
                $userManage.fetchUseraUpdateCategory(id, status).then(function(data) {
                    // $scope.yellowCardList = data;
                    // if (data == 1) {
                    // $scope.yellowCardList.status = data.status;
                    // }
                    angular.forEach($scope.yellowCardList, function(list, index, array) {
                        if (list.id == id) {
                            list.card_num = data.card_num;
                        }
                    })


                    $mdToast.show($mdToast.simple()
                        .content('更新成功')
                        .hideDelay(5000)
                        .position("top right"));

                }, function(err) {
                    $mdToast.show($mdToast.simple()
                        .content(err.toString())
                        .hideDelay(5000)
                        .position("top right"));
                });
            });
        }

        // 取消黄牌
        $scope.abandonYellowCard = function(id, status) {
            var confirm = $mdDialog.confirm()
                .title('确定取消吗')
                .ariaLabel('delete yellow card item')
                .ok('确定取消')
                .cancel('手滑点错了，不取消');

            $mdDialog.show(confirm).then(function() {
                $userManage.fetchUserAbandonYellowCard(id, status).then(function(data) {
                    // if (data == 1) {
                    // $scope.yellowCardList.status = data.status;
                    // }

                    $mdToast.show($mdToast.simple()
                        .content('取消成功')
                        .hideDelay(5000)
                        .position("top right"));

                }, function(err) {
                    $mdToast.show($mdToast.simple()
                        .content(err.toString())
                        .hideDelay(5000)
                        .position("top right"));
                });
            });
        }

        // 驳回申请
        $scope.rejectYellowCard = function(id, handle_reply) {
            var confirm = $mdDialog.confirm()
                .title('确定驳回吗')
                .ariaLabel('delete yellow card item')
                .ok('确定驳回')
                .cancel('手滑点错了，不驳回');

            $mdDialog.show(confirm).then(function() {
                $userManage.fetchUserRejectYellowCard(id, handle_reply).then(function(data) {
                    // $scope.yellowCardList.appeal_status = data.appeal_status;
                    $mdToast.show($mdToast.simple()
                        .content('驳回成功')
                        .hideDelay(5000)
                        .position("top right"));

                }, function(err) {
                    $mdToast.show($mdToast.simple()
                        .content(err.toString())
                        .hideDelay(5000)
                        .position("top right"));
                });
            });
        }



        // 发起人发起的活动
        $scope.founderActivity = function() {
            $userManage.fetchActivityByRole($routeParams.id, 'founder').then(function(data) {
                $scope.founderActivity = data;
            })
        }

        // PMA参与的活动
        $scope.pmaActivity = function() {
            $userManage.fetchActivityByRole($routeParams.id, 'pma').then(function(data) {
                $scope.pmaActivity = data;
            })
        }

        $scope.data = {};
        $scope.profile = {};

        $userManage.fetch(params).then(function(data) {
            var isFounder = false;
            var isPma = false;
            for (var i = 0, k = data.assignment.length; i < k; i++) {
                if (data.assignment[i].item_name == 'founder') {
                    isFounder = true;
                }
                if (data.assignment[i].item_name == 'pma') {
                    isPma = true;
                }
            }
            $scope.user = data;
            $scope.profile.headimgurl = data.profile.headimgurl;
            $scope.data.cb3 = data.in_white_list == 1;
            $scope.data.cb2 = isFounder;
            $scope.data.cb = isPma;
        }, function(err) {
            alert(err);
        });

        // 取消
        $scope.cancel = function() {
            $location.path('/member');
        }
        $scope.updateHeader = function() {
            var user_id = $scope.user.id;

            var userData = {
                headimgurl: $scope.user.profile.headimgurl,
            };

            $userManage.update(user_id, userData).then(function(data) {
                $mdToast.show($mdToast.simple()
                    .content('设置用户属性成功')
                    .hideDelay(5000)
                    .position("top right"));
                $location.path('/member');
            }, function(err) {
                $mdToast.show($mdToast.simple()
                    .content('设置用户属性发生错误：' + err + '')
                    .hideDelay(5000)
                    .position("top right"));
            });


        }

        $scope.updateUser = function() {
            var userData = {
                headimgurl: $scope.profile.headimgurl,
                email: $scope.user.email,
                password: $scope.user.password,
                bio: $scope.user.profile.bio,
                username: $scope.user.username,
                mobile: $scope.user.mobile,
                sex: $scope.user.profile.sex,
                black_label: $scope.user.black_label,
            };

            var user_id = $scope.user.id;

            var setup = $scope.data.cb3 == true;
            $userManage.setUserInWhiteList(user_id, setup).then(function(data) {
                console.log('设置用户为白名单成功');
            });

            var assignPMA = $scope.data.cb == true ? 1 : 0;
            $userManage.setUserAsPma(user_id, assignPMA).then(function(data) {
                console.log('设置用户为PMA成功');
            });

            var assignFounder = $scope.data.cb2 == true ? 1 : 0;
            $userManage.setUserAsFounder(user_id, assignFounder).then(function(data) {
                console.log('设置用户为Founder成功');
            });

            $userManage.update(user_id, userData).then(function(data) {
                $mdToast.show($mdToast.simple()
                    .content('设置用户属性成功')
                    .hideDelay(5000)
                    .position("top right"));
                $location.path('/member');
            }, function(err) {
                $mdToast.show($mdToast.simple()
                    .content('设置用户属性发生错误：' + err + '')
                    .hideDelay(5000)
                    .position("top right"));
            });
        }
    }]);

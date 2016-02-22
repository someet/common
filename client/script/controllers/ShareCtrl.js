angular.module('controllers').controller('ShareCtrl', [
    '$scope', '$routeParams', '$shareManage', '$mdToast', '$qiniuManage', '$qupload',
    function($scope, $routeParams, $shareManage, $mdToast, $qiniuManage, $qupload) {


        $shareManage.fetchList().then(function(data) {
            console.log(data);
            $scope.list = data;
            // $scope.share = {
            //     title: data.title,
            //     desc: data.desc,
            //     link: data.link,
            //     imgurl: data.imgurl,
            // };
        })




        // qiniu upload image start //
        $scope.selectHeader = null;

        var startHeader = function() {
            $qiniuManage.fetchUploadToken().then(function(token) {

                $qupload.upload({
                    key: '',
                    file: $scope.selectHeader.file,
                    token: token
                }).then(function(response) {
                    $qiniuManage.completelyUrl(response.key).then(function(url) {
                        $scope.share.imgurl = url;
                    });
                }, function(response) {
                    console.log(response);
                }, function(evt) {
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
            console.log($files);
            $scope.selectHeader = {
                file: $files[0],
                progress: {
                    p: 0
                }
            };
            startHeader();
        };
        // qiniu upload image end //



        //保存信息
        $scope.save = function() {
            var newEntity = $scope.share;
            newEntity.title = $scope.share.title;
            newEntity.desc = $scope.share.desc;
            newEntity.link = $scope.share.link;
            newEntity.imgurl = $scope.share.imgurl;
            newEntity.status = $scope.share.status;
            newEntity.id = $scope.id;

            $shareManage.create(newEntity).then(function(data) {
                $mdToast.show($mdToast.simple()
                    .content('信息保存成功')
                    .hideDelay(5000)
                    .position("top right")
                );
                console.log(newEntity);

            }, function(err) {
                $mdToast.show($mdToast.simple()
                    .content(err.toString())
                    .hideDelay(5000)
                    .position("top right"));

            })
        }





    }
])

.controller('ShareUpdateCtrl', [
    '$scope', '$location', '$routeParams', '$qiniuManage', '$qupload', '$shareManage', '$mdToast',
    function($scope, $location, $routeParams, $qiniuManage, $qupload, $shareManage, $mdToast) {
        // 分享内容的id
        $scope.id = $routeParams.id;
        $scope.share = {};

        $shareManage.fetch($scope.id).then(function(data) {
            $scope.share.title = data.title;
            $scope.share.desc = data.desc;
            $scope.share.link = data.link;
            $scope.share.imgurl = data.imgurl;
            $scope.share.status = data.status;
        })


        // qiniu upload image start //
        $scope.selectHeader = null;

        var startHeader = function() {
            $qiniuManage.fetchUploadToken().then(function(token) {

                $qupload.upload({
                    key: '',
                    file: $scope.selectHeader.file,
                    token: token
                }).then(function(response) {
                    $qiniuManage.completelyUrl(response.key).then(function(url) {
                        $scope.share.imgurl = url;
                    });
                }, function(response) {
                    console.log(response);
                }, function(evt) {
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
            console.log($files);
            $scope.selectHeader = {
                file: $files[0],
                progress: {
                    p: 0
                }
            };
            startHeader();
        };
        // qiniu upload image end //

        //更新信息
        $scope.update = function() {
            var newEntity = $scope.share;
            newEntity.id = $scope.id;
            newEntity.title = $scope.share.title;
            newEntity.desc = $scope.share.desc;
            newEntity.link = $scope.share.link;
            newEntity.imgurl = $scope.share.imgurl;
            newEntity.status = $scope.share.status;
            console.log(newEntity);
            $shareManage.update(newEntity).then(function(data) {
                $mdToast.show($mdToast.simple()
                    .content('信息保存成功')
                    .hideDelay(5000)
                    .position("top right")
                );
                console.log(newEntity);

            }, function(err) {
                $mdToast.show($mdToast.simple()
                    .content(err.toString())
                    .hideDelay(5000)
                    .position("top right"));

            })
        }



    }
])

angular.module('controllers')
    .controller('ActivityCtrl',
    ['$scope', '$location', '$activityManage',
        function ($scope, $location, $activityManage) {

            $scope.$parent.pageName = '活动管理';
            $activityManage.fetch().then(function (data) {
                $scope.list = data;
            }, function (err) {
                alert(err);
            });

            $scope.update = function (activity) {
                $location.path('/activity/' + activity.id);
            };

            $scope.delete = function (activity) {
                $activityManage.delete(activity).then(function (data) {
                    alert('删除成功');
                    var index = $scope.list.indexOf(activity);
                    $scope.list.splice(index, 1)
                }, function (err) {
                    alert(err);
                });
            };

        }])
    .controller('ActivityViewCtrl',
    ['$scope', '$routeParams', '$location', '$activityManage', '$activityTypeManage',
        function ($scope, $routeParams, $location, $activityManage, $activityTypeManage) {
            $scope.$parent.pageName = '活动详情';
            var id = $routeParams.id;
            console.log(id);
            console.log(id>0);
            if(id>0) {
              $activityManage.fetch(id).then(function (data) {
                  console.log(data);
                  $scope.id = data.id;
                  $scope.title = data.title;
                  $scope.longitude = data.longitude;
                  $scope.latitude = data.latitude;
                  $scope.groupcode = data.groupcode;
                  $scope.details = data.details;
                  $scope.type_id = data.type_id;
                  $scope.desc = data.desc;
                  $scope.poster = data.poster;
                  $scope.area = data.area;
                  $scope.address = data.address;
              }, function (err) {
                  $location.path('/activity');
              });
            }

            $activityTypeManage.fetch().then(function(data){
                $scope.typelist = data;
                console.log(data);
            });

            $scope.save = function () {
                var newActivity = {
                    title: $scope.title,
                    desc: $scope.desc,
                    type_id: $scope.type_id,
                    longitude: $scope.longitude,
                    latitude: $scope.latitude,
                    groupcode: $scope.groupcode,
                    details: $scope.details,
                    address: $scope.address,
                    area: $scope.area,
                    poster: $scope.poster,
                };
                if ($scope.id > 0 ) {
                    newActivity.id = $scope.id;
                    console.log(newActivity);
                    $activityManage.update($scope.id, newActivity).then(function(data) {
                       alert('修改成功');
                        $location.path('/activity');
                    });
                } else {
                    console.log(newActivity);
                    $activityManage.create(newActivity).then(function (data) {
                        alert('保存成功');
                        $location.path('/activity');
                    }, function (err) {
                        alert(err);
                    });
                }
            };
        }])
;

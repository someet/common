angular.module('controllers')
    .controller('ActivityTypeCtrl',
    ['$scope', '$http', '$location',
        function($scope, $http, $location){
        var p = $http({
            method: 'GET',
            url: '/activity-type/index'
        });
        p.success(function(response, status, headers, config){
            console.log(response.data);
           $scope.list = response.data;
        });

        $scope.update= function (type) {
            $location.path('/activity-type/' + type.id);
        };

        $scope.delete = function(type) {
            $http.post('/activity-type/delete?id=' + type.id ).success(function(response, status){
                if(response.success=="1"){
                   alert('删除成功');
                    var index = $scope.list.indexOf(type);
                    $scope.list.splice(index, 1)
                } else {
                    alert('删除失败');
                }
            })
        }

        $scope.create= function() {
            $scope.url = '/activity-type/create';
            var name = $scope.name;
            $http.post($scope.url, {name: name},  {'Content-Type': 'application/json;charset=utf-8'}).success(function(data){
                if(data.success=="1"){
                    alert('添加成功');
                    $scope.list.push(data.data);
                } else {
                    alert('添加失败');
                }
            });
        }
    }])
    .controller('ActivityTypeViewCtrl',
    ['$scope', '$http', '$routeParams', '$location',
    function($scope, $http, $routeParams, $location){
        var id = $routeParams.id;

        $http({
            method: 'GET',
            url: '/activity-type/view?id=' + id
        }).success(function(response){
            if(response.success=="1"){
                $scope.typeContent = response.data;
            }else {
               $location.path('/activity-type');
            }
        });
        $scope.save = function () {
            var type = $scope.typeContent;
            $http.post(
                '/activity-type/update?id=' + type.id,
                {name: type.name, displayorder: type.displayorder}
            ).success(function(response) {
                if(response.success=="1") {
                    alert('修改成功');
                    $location.path('/activity-type');
                } else {
                    alert('修改失败: ' + response.errmsg);
                }
            });
        };
    }]);


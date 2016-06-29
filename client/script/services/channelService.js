angular.module('backendServices')
    .factory('$channelManage', ['$http', '$q', function($http, $q) {
        return {
            //获取当前的数据
            fetch: function() {
                return $http.get('/channel/index').then(function(data){
                    return data;
                });
            },
            create: function(params) {
                return $http.get('/channel/create?channel='+params,{channel:params}).then(function(data){
                    return data;
                })
            }
        };
    }]);
angular.module('backendServices')
    .factory('$ugaAnswerManage', ['$http', '$q', '$rootScope', function($http, $q, $rootScope) {
        return {
            fetch: function(id) {
                var url = '/uga-answer/list?question_id=' + id;
                return $http.get(url).then(function(data) {
                    return data;
                })

            },
            delete: function(id,status) {
                return $http.post('/uga-answer/delete?id=' + id + '&status=' + status, {}).success(function(data) {
                    return data;
                });
            },
        };
    }])

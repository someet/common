angular.module('backendServices')
    .factory('$ugaAnswerManage', ['$http', '$q', '$rootScope', function($http, $q, $rootScope) {
        return {
            fetch: function(id) {
            	// var id = 1;
                var url = '/uga-answer/list?question_id=' + id;

                return $http.get(url).then(function(data) {
                	console.log(data);
                    return data;
                })

            }
        };
    }])
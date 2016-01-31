angular.module('backendServices')
    .factory('$ugaManage', ['$http', '$q', '$rootScope', function($http, $q, $rootScope) {
        return {
            fetch: function() {
            	var id = 1;
                var url = '/uga-question/list?order=' + id;

                return $http.get(url).then(function(data) {
                	console.log(data);
                    return data;
                })

            }
        };
    }])
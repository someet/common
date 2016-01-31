angular.module('backendServices')
    .factory('$ugaManage', ['$http', '$q', '$rootScope', function($http, $q, $rootScope) {
        return {
            fetch: function(is_official, order) {
                if (order == null) {
                    var orderBy = '&order=id';
                } else {
                    var orderBy = '&order=' + order;
                }
                console.log(orderBy);
                var url = '/uga-question/list?is_official=' + is_official + orderBy;

                return $http.get(url).then(function(data) {
                    console.log(data);
                    return data;
                })

            },
            ugaPageMeta: function(type, pageNum) {

                return $http.get('/uga-question/list?scenario=total&perPage=' + pageNum + '&is_official=' + type).then(function(data) {
                    return data;
                });
            },
            fetchPage: function(type, page) {
                page = page || 1;

                var params = {
                    'is_official': type,
                    'page': page,
                    'perPage': 4 //每页20条
                };

                return $http.get('/uga-question/list?scenario=page&is_official='+type, {
                    params: params
                }).then(function(data) {

                    return data;
                });

            },

        };
    }])

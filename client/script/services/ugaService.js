angular.module('backendServices')
    .factory('$ugaManage', ['$http', '$q', '$rootScope', function($http, $q, $rootScope) {
        return {
            create: function (newEntity) {
                return $http.post('/uga-question/create', newEntity).then(function (data) {
                    return data;
                });
            },
            update: function (id, newEntity) {
                return $http.post('/uga-question/update?id=' + id, newEntity).then(function (data) {
                    return data;
                });
            },
            data: function() {
                var url = '/uga-question/data';

                return $http.get(url).then(function(data) {
                    return data;
                })
            },
            fetch: function(is_official, order) {
                if (order == null) {
                    var orderBy = '&order=id';
                } else {
                    var orderBy = '&order=' + order;
                }
                var url = '/uga-question/list?scenario=page&order=id&page=1&perPage=10&is_official=' + is_official + orderBy;

                return $http.get(url).then(function(data) {
                    console.log(data);
                    return data;
                })

            },
            order: function(order) {
                if (order == null) {
                    var orderBy = '&order=id';
                } else {
                    var orderBy = '&order=' + order;
                }
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
            fetchPage: function(type, page, order) {
                page = page || 1;
                order = order || 'id';
                var params = {
                    'order': order,
                    'is_official': type,
                    'page': page,
                    'perPage': 20 //每页20条
                };

                return $http.get('/uga-question/list?scenario=page', {
                    params: params
                }).then(function(data) {

                    return data;
                });

            },
            delete: function(id, status) {
                return $http.post('/uga-question/review?id=' + id + '&status=' + status, {}).success(function(data) {
                    return data;
                });
            },
            putOpen: function(id, open) {
                return $http.post('/uga-question/public?id=' + id + '&open=' + open, {}).success(function(data) {
                    return data;
                });
            }

        };
    }])

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
            order: function(order) {
                if (order == null) {
                    var orderBy = '&order=id';
                } else {
                    var orderBy = '&order=' + order;
                }
                var url = '/uga-answer/list?question_id=' + question_id + orderBy;
                return $http.get(url).then(function(data) {
                    console.log(data);
                    return data;
                })
            },
            ugaPageMeta: function(question_id,pageNum) {
                return $http.get('/uga-answer/list?question_id='+ question_id +'&scenario=total&perPage=' + pageNum ).then(function(data) {
                    return data;
                });
            },
            fetchPage: function(question_id, page, order) {
                // console.log(222);
                page = page || 1;
                order = order || 'id';
                var params = {
                    'question_id':question_id,
                    'order':order,
                    'page': page,
                    'perPage': 20 //每页20条
                };

                return $http.get('/uga-answer/list?scenario=page', {
                    params: params
                }).then(function(data) {
                    return data;
                });

            },
        };
    }])

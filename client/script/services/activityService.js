angular.module('backendServices')
    .factory('$activityManage', ['$http', '$q', '$rootScope', function($http, $q, $rootScope) {
        return {
            // 创建发起人
            createFounder: function(activity_id, founder_id) {
                return $http.get('/activity/add-founder', { activity_id: 'activity_id', founder_id: 'founder_id' }).then(function(data) {
                    return data;
                })
            },

            //删除发起人
            deteFounder: function(id){
                return $http.get('/activity/dete-founder?id=' + id).then(function(data){
                    return data;
                })
            },

            // 更新状态
            updateStatus: function(id, status) {
                return $http.get('/activity/update-status?id=' + id + '&status=' + status).then(function(data) {
                    return data;
                })
            },
            updateAllPrevent: function(newEntity) {
                return $http.get('/activity/update-all-prevent').then(function(data) {
                    return data;
                })
            },
            filterPrevent: function(newEntity) {
                return $http.get('/activity/filter-prevent').then(function(data) {
                    return data;
                })
            },
            create: function(newEntity) {
                return $http.post('/activity/create', newEntity).then(function(data) {
                    return data;
                });
            },
            delete: function(entity) {
                return $http.post('/activity/delete?id=' + entity.id, {}).success(function(data) {
                    return data;
                });
            },
            update: function(id, newEntity) {
                return $http.post('/activity/update?id=' + id, newEntity).then(function(data) {
                    return data;
                });
            },
            fetch: function(id) {
                var url = typeof id == 'undefined' ? '/activity/index' : '/activity/view?id=' + id;

                return $http.get(url).then(function(data) {
                    return data;
                });
            },
            listByType: function(type_id) {
                return $http.get('/activity/list-by-type-id?type_id=' + type_id).then(function(data) {
                    return data;
                });
            },
            tags: function(query) {
                return $http.get('/activity-tag/list?query=' + query);
            },
            //搜索活动名字
            search: function(query,page) {
                page = page || 1;
                var params = {
                    'page': page,
                    'perPage': 10 //每页20条
                };
                return $http.get('/activity/search?title=' + query,{params: params});
            },
            //搜索场地
            searchSpace: function(query) {
                return $http.get('/space-spot/search?name=' + query).then(function(data) {

                    return data.models;
                });
            },

            //搜索空间
            searchSection: function(query) {
                return $http.get('/space-spot/search?name=' + query).then(function(data) {
                    console.log(data);
                    return data.models;
                });
            },
            //搜索用户
            searchUser: function(query) {
                return $http.get('/member/search?username=' + query);
            },
            //搜索发起人
            searchFounder: function(query) {
                return $http.get('/member/search-by-auth?username=' + query + '&auth=founder');
            },
            //搜索pma
            searchPrincipal: function(query) {

                return $http.get('/member/search-by-auth?username=' + query + '&auth=pma');
            },
            //搜索管理员
            searchDts: function(query) {
                return $http.get('/member/search-by-auth?username=' + query + '&auth=admin');
            },
            modelPageMeta: function(type, pageNum, isWeek, search) {
                console.log(search);
                if (typeof search == 'undefined') {
                    search = '';
                }

                return $http.get('/activity?scenario=total&perPage=' + pageNum + '&type=' + type + '&isWeek=' + isWeek + '&search=' + search
                    ).then(function(data) {
                    return data;
                });
            },
            fetchPage: function(type, page, isWeek, search) {
                page = page || 1;
                var params = {
                    'type': type,
                    'page': page,
                    'isWeek': isWeek,
                    'perPage': 10 //每页20条
                };
                return $http.get('/activity', {
                    params: params
                }).then(function(data) {

                    return data;
                });

            }
        };
    }]);

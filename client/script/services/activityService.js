angular.module('backendServices')
    .factory('$activityManage', ['$http', '$q', '$rootScope', function ($http, $q, $rootScope) {
      return {
        // 更新状态
        updateStatus: function (id,status){
          return $http.get('/activity/update-status?id='+id+'&status='+status).then(function (data) {
            return data;
          })
        },  
        updateAllPrevent: function (newEntity){
          return $http.get('/activity/update-all-prevent').then(function (data) {
            return data;
          })
        },        
        filterPrevent: function (newEntity){
          return $http.get('/activity/filter-prevent').then(function (data) {
            return data;
          })
        },
        create: function (newEntity) {
          return $http.post('/activity/create', newEntity).then(function (data) {
            return data;
          });
        },
        delete: function (entity) {
          return $http.post('/activity/delete?id=' + entity.id, {}).success(function (data) {
            return data;
          });
        },
        update: function (id, newEntity) {
          return $http.post('/activity/update?id=' + id, newEntity).then(function (data) {
            return data;
          });
        },
        fetch: function (id) {
          var url = typeof id == 'undefined' ? '/activity/index' : '/activity/view?id=' + id;

          return $http.get(url).then(function (data) {
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
        search: function(query){
          return $http.get('/activity/search?title=' + query);
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
        modelPageMeta: function(type, pageNum, isWeek) {
          return $http.get('/activity?scenario=total&perPage='+pageNum+'&type='+type+'isWeek='+isWeek).then(function(data) {
            return data;
          });
        },
        fetchPage: function(type, page, isWeek) {
          page = page || 1;

          var params = {
            'type': type,
            'page': page,
            'isWeek': isWeek,
            'perPage': 20  //每页20条
          };

          return $http.get('/activity?scenario=page', {
            params: params
          }).then(function(data) {

            return data;
          });

        }
      };
    }]);

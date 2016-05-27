angular.module('backendServices')
    .factory('$founderManage', ['$http', '$q', '$rootScope', function ($http, $q, $rootScope) {
      return {
        // 更新状态
        updateStatus: function (id,status){
          return $http.get('/activity/update-status?id='+id+'&status='+status).then(function (data) {
            return data;
          })
        },  
        create: function (newEntity) {
          return $http.post('/founder/create', newEntity).then(function (data) {
            return data;
          });
        },
        update: function (id, newEntity) {
          return $http.post('/founder/update?id=' + id, newEntity).then(function (data) {
            return data;
          });
        },
        fetch: function (id) {
          var url = typeof id == 'undefined' ? '/founder/index' : '/founder/view?id=' + id;
          return $http.get(url).then(function (data) {
            return data;
          });
        },
        listByType: function(type_id) {
          return $http.get('/activity/list-by-type-id?type_id=' + type_id).then(function(data) {
            return data;
          });
        },
        //搜索活动名字
        search: function(query){
          return $http.get('/founder/search?title=' + query);
        },      
        //搜索发起人
        defaultData: function() {
          return $http.get('/founder/default-data');
        },
        modelPageMeta: function(type, pageNum, isWeek) {
          return $http.get('/founder/index?scenario=total&perPage='+pageNum+'&type='+type+'isWeek='+isWeek).then(function(data) {
          	console.log(data);
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

          return $http.get('/founder/index?scenario=page', {
            params: params
          }).then(function(data) {

            return data;
          });

        }
      };
    }]);

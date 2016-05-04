angular.module('backendServices')
  .factory('$spaceSpotManage', ['$http', '$q', '$rootScope', function ($http, $q, $rootScope) {
    return {
      create: function (newEntity) {
        return $http.post('/space-spot/create', newEntity).then(function (data) {
          return data;
        });
      },
      delete: function (entity) {
        return $http.post('/space-spot/delete?id=' + entity.id, {}).success(function (data) {
          return data;
        });
      },
      update: function (id, newEntity) {
        return $http.post('/space-spot/update?id=' + id, newEntity).then(function (data) {
          return data;
        });
      },
      fetch: function (id) {
        var url = typeof id == 'undefined' ? '/space-spot/index' : '/space-spot/view?id=' + id;

        return $http.get(url).then(function (data) {
          return data;
        });
      },
      listByType: function(type_id) {
        return $http.get('/space-spot/list-by-type-id?type_id=' + type_id).then(function(data) {
          return data;
        });
      },
      //搜索活动名字
      search: function(query){
        return $http.get('/space-spot/search?name=' + query);
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
      modelPageMeta: function(type, pageNum) {
        return $http.get('/space-spot?scenario=total&perPage='+pageNum+'&type='+type).then(function(data) {
          return data;
        });
      },
      fetchPage: function(type, page) {
        page = page || 1;

        var params = {
          'type': type,
          'page': page,
          'perPage': 20  //每页20条
        };

        return $http.get('/space-spot?scenario=page', {
          params: params
        }).then(function(data) {

          return data;
        });

      }
    };
  }]);

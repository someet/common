angular.module('backendServices')
  .factory('$userManage', ['$http', '$q', function($http, $q) {
    return {
      //联系人列表
      fetch: function(params) {
        return $http.get('/member', {
          params: params
        }).then(function(userList) {
          return userList;
        });
      },
      //设置用户为白名单
      setUserInWhiteList: function(user_id, in_white_list){
         return $http.post('/member/set-user-in-white-list?user_id='+user_id+'&in_white_list='+in_white_list).then(function(data) {
            return data;
         })
      },
      //设置用户为PMA
      setUserAsPma: function(user_id, assign){
        return $http.post('/member/update-assignment?user_id='+user_id+'&role_name=pma&assign_or_not='+assign).then(function(data) {
          return data;
        })
      },
      //设置用户为发起人
      setUserAsFounder: function(user_id, assign){
        return $http.post('/member/update-assignment?user_id='+user_id+'&role_name=founder&assign_or_not='+assign).then(function(data) {
          return data;
        })
      },
      //添加联系人
      add: function(newEntity) {
        return $http.post('/member/create', newEntity).then(function(data) {
          return data;
        });
      },
      //更新用户
      update: function(id, entity) {
        return $http.post('/member/update?id='+id, entity).then(function(data){
          return data;
        });
      },
      //删除用户
      delete: function(id) {
        var entity = {
          status: 0
        }
        return $http.post('/member/update?id='+id, entity).then(function(data){
          return data;
        });
      },
      //搜索用户
      search: function(username) {
        return $http.get('/member/search?username='+username);
      },
      userPageMeta: function(type, pageNum) {
        return $http.get('/member?scenario=total&perPage='+pageNum+'&type='+type).then(function(data) {
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
        return $http.get('/member?scenario=page', {
          params: params
        }).then(function(data) {
          return data;
        });
      }//end fetchPage
    };
  }]);
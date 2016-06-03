angular.module('backendServices')
  .factory('$userManage', ['$http', '$q', function($http, $q) {
    return {
      // 获取登陆用户的权限
      fetchUserRole: function(params) {
        return $http.get('/member/user-role').then(function(data){
          return data;
        });
      },
      //联系人列表
      fetch: function(params) {
        return $http.get('/member', {
          params: params
        }).then(function(userList) {
          return userList;
        });
      },

      // 黄牌申诉列表
      fetchUserAppealList: function() {
        return $http.get('/member/appeal-list').then(function(AppealList) {
          return AppealList;
        })
      },
      // 获取用户报名的活动
      fetchUserJoinActivity: function(userId) {
        return $http.get('/member/user-join-activities?user_id='+userId).then(function(userJoinActivity) {
          return userJoinActivity;
        })
      },

      // 获取用户获得的黄牌
      fetchUserYellowCard: function(userId) {
        return $http.get('/member/yellow-card?user_id='+userId).then(function(data) {
          return data;
        })
      },

      // 更改用户黄牌的种类
      fetchUseraUpdateCategory: function(id,status) {
        return $http.get('/member/update-category?id='+id +'&status='+status).then(function(data) {
          return data;
        })
      },
      // 取消用户的黄牌
      fetchUserAbandonYellowCard: function(id,status) {
        return $http.get('/member/abandon-yellow-card?id='+id +'&status='+status).then(function(data) {
          return data;
        })
      },

      // 驳回用户的申请
      fetchUserRejectYellowCard: function(id, handle_reply) {
        return $http.get('/member/reject-yellow-card?id='+id + '&handle_reply='+handle_reply).then(function(data) {
          return data;
        })
      },

      //发起人发起的活动 && PMA参与的活动
      fetchActivityByRole: function(userId,role) {
        return $http.get('/member/activity-by-role?user_id='+userId+'&role='+role).then(function(data){
          return data;
        })
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
      search: function(search,page) {
        if (typeof search == 'undefined') {
          search = '';
        }
        page = page || 1;

        var params = {
          'page': page,
          'perPage': 2  //每页20条
        };
        return $http.get('/member/search?search='+search,{params:params});
      },
      userPageMeta: function(type, pageNum) {
        return $http.get('/member?scenario=total&perPage='+pageNum+'&type='+type).then(function(data) {
          return data;
        });
      },
      fetchPage: function(type, page) {
        console.log(page);
        page = page || 1;

        var params = {
          'type': type,
          'page': page,
          'perPage': 2  //每页20条
        };
        return $http.get('/member', {
          params: params
        }).then(function(data) {
          return data;
        });
      }//end fetchPage
    };
  }]);

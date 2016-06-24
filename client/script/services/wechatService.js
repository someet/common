angular.module('backendServices')
  .factory('$wechatManage', ['$http', '$q', function($http, $q) {
    return {
      //获取当前的数据
      fetch: function() {
        return $http.get('/wechat/index').then(function(data){
          return data;
        });
      },
      create: function(params) {
      	return $http.get('/wechat/create',{wechatReply:params}).then(function(data){
      		return data;
      	})
      }
   };
  }]);
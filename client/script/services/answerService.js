angular.module('backendServices')
    .factory('$answerManage', ['$http', '$q', '$rootScope', function ($http, $q, $rootScope) {
      return {
        create: function (newEntity) {
          return $http.post('/answer/create', newEntity).then(function (data) {
            return data;
          });
        },
        delete: function (entity) {
          return $http.post('/answer/delete?id=' + entity.id, {}).success(function (data) {
            return data;
          });
        },
        update: function (id, newEntity) {
          return $http.post('/answer/update?id=' + id, newEntity).then(function (data) {
            return data;
          });
        },
        fetch: function (id) {
          var url = typeof id == 'undefined' ? '/answer/index' : '/answer/view?id=' + id;

          return $http.get(url).then(function (data) {
            return data;
          });
        },
        //请假情况
        leave: function (id, leave_status) {
          return $http.post('/answer/leave?id=' + id + '&leave_status=' + leave_status).then(function (data) {
            return data;
          });
        },
        //到场情况
        arrive: function (id, arrive_status) {
          return $http.post('/answer/arrive?id=' + id + '&arrive_status=' + arrive_status).then(function (data) {
            return data;
          });
        },
        //筛选
        filter: function (id, pass_or_not) {
          return $http.post('/answer/filter?id=' + id + '&pass_or_not=' + pass_or_not).then(function (data) {
            return data;
          });
        },
        fetchByActivityId: function (activity_id) {
          return $http.get('/answer/view-by-activity-id?activity_id=' + activity_id).then(function (data) {
            return data;
          });
        },
        // 发送消息
        sendMessage: function (user_id) {
          return $http.get('/answer/send-message?user_id=' + user_id).then(function (data) {
            return data;
          });
        }
      };
    }]);
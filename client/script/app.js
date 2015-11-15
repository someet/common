var app = angular.module('SomeetBackendApp', [
  'ui.bootstrap.datetimepicker',
  'ngMaterial',
  'ngMessages',
  'ngRoute',
  'ngLodash',
  'controllers',
  'ui.bootstrap',
  'someetPagination',
  'someetTextAngular',
  'angularQFileUpload',
  'backendServices'  
]);

// init submodule
angular.module('controllers', []);
angular.module('backendServices', []);

app.config(["$httpProvider", function($httpProvider) {
  $httpProvider.defaults.headers.post['X-CSRF-Token'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}]);

// 根据时间的字符串获得时间戳
function getTimestamp(str) {
  return (new Date(str)).getTime() / 1000;
}
// 根据时间戳获取时间的字符串
function getTimeByTimestamp(timestamp) {
  return new Date(parseInt(timestamp) * 1000).toLocaleString().replace(/:\d{1,2}$/,' ');
}

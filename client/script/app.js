var app = angular.module('SomeetBackendApp', [
  'ui.bootstrap.datetimepicker',
  'ngMaterial',
  'ngMessages',
  'ngRoute',
  'ngLodash',
  'controllers',
  'ui.bootstrap',
  'someetPagination',
  'angularQFileUpload',
  'backendServices'
]);

// init submodule
angular.module('controllers', []);
angular.module('backendServices', []);

app.config(["$httpProvider", function($httpProvider) {
  $httpProvider.defaults.headers.post['X-CSRF-Token'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}]);

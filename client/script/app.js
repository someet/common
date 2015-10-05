var app = angular.module('SomeetBackendApp', [
  'ngMaterial',
  'ngRoute',
  'ngLodash',
  'controllers',
  'angularQFileUpload',
  'backendServices'
]);

// init submodule
angular.module('controllers', []);
angular.module('backendServices', []);

app.config(["$httpProvider", function($httpProvider) {
  $httpProvider.defaults.headers.post['X-CSRF-Token'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}]);

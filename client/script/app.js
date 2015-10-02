var app = angular.module('SomeetBackendApp', [
  'ngMaterial',
  'ngRoute',
  'controllers',
  'backendServices'
]);

// init submodule
angular.module('controllers', []);
angular.module('backendServices', []);

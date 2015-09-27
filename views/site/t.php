<html lang="en" ng-app="StarterApp">
  <head>
    <link rel="stylesheet" href="/static/style/angular-material.min.css">
    <meta name="viewport" content="initial-scale=1" />
    <link rel="stylesheet" href="/static/style/pages/site-t.css">
  </head>
  <body layout="column" ng-controller="AppCtrl">
    <md-toolbar layout="row">
      <div class="md-toolbar-tools">
        <md-button ng-click="toggleSidenav('left')" hide-gt-sm class="md-icon-button">
          <md-icon aria-label="Menu" md-svg-icon="https://s3-us-west-2.amazonaws.com/s.cdpn.io/68133/menu.svg"></md-icon>
        </md-button>
        <h1>Hello World</h1>
      </div>
    </md-toolbar>
    <div layout="row" flex>
        <md-sidenav layout="column" class="md-sidenav-left md-whiteframe-z2" md-component-id="left" md-is-locked-open="$mdMedia('gt-sm')">
        </md-sidenav>
        <div layout="column" flex id="content">
            <md-content layout="column" flex class="md-padding">
            </md-content>
        </div>
    </div>
    <!-- Angular Material Dependencies -->
    <script src="/static/js/angular.min.js"></script>
    <script src="/static/js/angular-animate.min.js"></script>
    <script src="/static/js/angular-aria.min.js"></script>
    <script src="/static/js/angular-material.min.js"></script>
    <script>
    var app = angular.module('StarterApp', ['ngMaterial']);

    app.controller('AppCtrl', ['$scope', '$mdSidenav', function($scope, $mdSidenav){
        $scope.toggleSidenav = function(menuId) {
            $mdSidenav(menuId).toggle();
        };
    }]);
    </script>
  </body>
</html>

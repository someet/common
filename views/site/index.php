<?php
use yii\helpers\Html;
?>
<!DOCTYPE html>
<html lang="en" ng-app="SomeetBackendApp">
  <head>
    <link rel="stylesheet" href="/static/style/bundle.css">
    <meta name="viewport" content="initial-scale=1" />
    <link rel="stylesheet" href="/static/style/pages/site-t.css">
    <?= Html::csrfMetaTags() ?>
  </head>
  <body layout="column" ng-controller="MainCtrl">
    <md-toolbar layout="row">
      <div class="md-toolbar-tools">
        <md-button hide-lg ng-click="toggleSidenav('left')">
          <md-icon aria-label="Menu" md-svg-icon="https://s3-us-west-2.amazonaws.com/s.cdpn.io/68133/menu.svg"></md-icon>
          <h1>Someet Backend</h1>
        </md-button>
        <h3>{{pageName}}</h3>
      </div>
    </md-toolbar>
    <div layout="row" flex>
        <md-sidenav layout="column"
          class="md-sidenav-left md-whiteframe-z2"
          md-component-id="left"
          md-is-locked-open="$mdMedia('lg')">
          <md-list>
            <md-list-item>
              <md-button ng-href="/#/activity-type">活动类型管理</md-button>
            </md-list-item>
            <md-list-item>
              <md-button ng-href="/#/activity">活动管理</md-button>
            </md-list-item>
          </md-list>
        </md-sidenav>
        <!--div layout="column" flex id="content"-->
        <div ng-view></div>
    </div>
    <script src="/static/js/bundle.js"></script>
    <script src="/static/js/all.js"></script>
  </body>
</html>

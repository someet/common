<?php
use yii\helpers\Html;
?>
<!DOCTYPE html>
<html lang="en" ng-app="SomeetBackendApp">
  <head>
    <link rel="stylesheet" href="/static/style/bundle.css">
    <meta name="viewport" content="initial-scale=1" />
    <link rel="stylesheet" href="/static/style/pages/site-t.css">
    <link rel="stylesheet" href="/static/style/main.css">
    <?= Html::csrfMetaTags() ?>
  </head>
  <body layout="row" ng-controller="MainCtrl">
    <md-sidenav layout="column"
        class="md-sidenav-left md-whiteframe-z2"
        md-component-id="left"
        md-is-locked-open="$mdMedia('gt-md')">
      <md-toolbar layout="column">
        <div class="md-toolbar-tools">
          <h1>Someet Backend</h1>
        </div>
      </md-toolbar>
      <md-content role="navigation" class="flex md-default-theme" layout="column">
        <md-button ng-href="/#/activity-type">活动类型管理</md-button>
        <md-button ng-href="/#/activity">活动管理</md-button>
        <md-button ng-href="/#/special">专题管理</md-button>
        <md-button ng-href="/#/user">用户管理</md-button>
        <md-button ng-href="/#/activity-tag">标签管理</md-button>
      </md-content>
    </md-sidenav>
    <div class="flex md-layout-column">
      <md-toolbar tabindex="-1" layout="row" role="main" >
          
        <md-button hide-gt-md ng-click="toggleSidenav('left')">
          <md-icon aria-label="Menu" md-svg-icon="https://s3-us-west-2.amazonaws.com/s.cdpn.io/68133/menu.svg"></md-icon>
        </md-button>
        <h3>{{pageName}}</h3>
      </md-toolbar>
    <div layout="row" flex>
        <!--div layout="column" flex id="content"-->
        <div style="margin-top: 10px; margin-left: 10px;" ng-view></div>
    </div>
    <script src="/static/js/bundle.js"></script>
    <script src="/static/js/all.js"></script>
  </body>
</html>

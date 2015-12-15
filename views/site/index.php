<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html lang="en" ng-app="SomeetBackendApp">
<head>
  <link rel="stylesheet" href="/static/style/bundle.css">
  <meta name="viewport" content="initial-scale=1" />
  <link rel="stylesheet" href="/css/reset.css">
  <link rel="stylesheet" href="/static/style/pages/site-t.css">
  <!-- 新 Bootstrap 核心 CSS 文件 -->
  <!--link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css"-->

  <!-- 可选的Bootstrap主题文件（一般不用引入） -->
  <!--link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap-theme.min.css"-->

  <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
  <!--script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script-->

  <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
  <!--script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script-->
  <link rel="stylesheet" href="/static/style/main.css">
  <link rel="stylesheet" href="/static/style/mystyle.css">

  <?= Html::csrfMetaTags() ?></head>
<body class="main_css layout-column flex"  flex="" layout="column" ng-controller="MainCtrl">


  <md-toolbar class="site-content-toolbar" aria-hidden="false" style="background-color:#FFF;">
    <nav class="navbar navbar-default container" role="navigation">
      <!-- Brand and toggle get grouped for better mobile display -->

      <div class="navbar-header">        
        <a class="navbar-brand" href="#/dashboard">Someet</a>
      </div>

      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse " >
        <ul class="nav navbar-nav h4">
          <?php echo Yii::$app->request->getQueryString(); ?>
          <li ng-class="{active:isActive('/special')}">
            <a href="#/special">专题</a>
          </li>
          <li ng-class="{active:isActive('/activity')||isActive('/answer')||isActive('/question')}">
            <a href="#/activity/list/0">活动</a>
          </li>
          <li ng-class="{active:isActive('/member')}">
            <a href="#/member">联系人</a>
          </li>
        </ul>

        <ul class="nav navbar-nav navbar-right">
          <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown">
              <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
              <?php $user = Yii::$app->user->identity; ?>
              <?= $user->username ?> <b class="caret"></b>

            </a>
            <ul class="dropdown-menu">
              <li>
                <a href="/admin-log/index">工作日志</a>
              </li>
              <li>
                <a href="/rbac">权限管理</a>
              </li>
              <li class="divider"></li>
              <li>
                <a href="/site/logout">注销</a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
      <!-- /.navbar-collapse --> </nav>
    </md-toolbar>
  <!-- /container -->
<md-content ng-view >
  </md-content>
  <!-- /container -->

  <script src="/static/js/bundle.js"></script>
  <script src="/static/js/all.js"></script>
</body>
</html>
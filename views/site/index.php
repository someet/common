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
        <link rel="stylesheet" href="/static/style/main.css">
        <link rel="stylesheet" href="/static/style/mystyle.css">
        <?= Html::csrfMetaTags() ?>
    </head>

    <body class="main_css layout-column flex" flex="" layout="column" ng-controller="MainCtrl">
        <md-toolbar class="site-content-toolbar" aria-hidden="false" style="background-color:#FFF;">
            <nav class="navbar navbar-default">
                    <div class="container">
                        <!-- Brand and toggle get grouped for better mobile display -->
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                            <a class="navbar-brand" href="#/dashboard">Someet</a>
                        </div>
                        <!-- Collect the nav links, forms, and other content for toggling -->
                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                            <ul class="nav navbar-nav h4">
                                <?php echo Yii::$app->request->getQueryString(); ?>
                                <li ng-class="{active:isActive('/special')}">
                                    <a target="_parent" href="#/special">专题</a>
                                </li>
                                <li ng-class="{active:isActive('/activity')||isActive('/answer')||isActive('/question')}">
                                    <a target="_parent" href="#/activity/list/0">活动</a>
                                </li>
                                <li ng-class="{active:isActive('/member')}">
                                    <a target="_parent" href="#/member">联系人</a>
                                </li>
                                <li ng-class="{active:isActive('/uga')}">
                                    <a target="_parent" href="#/uga">UGA问题系统</a>
                                </li>
                                <li ng-class="{active:isActive('/share')}">
                                    <a target="_parent" href="#/share">分享</a>
                                </li>
                                <li ng-class="{active:isActive('/space-spot')}">
                                    <a target="_parent" href="#/space-spot/list/0">场地</a>
                                </li>
                                <li ng-class="{active:isActive('/space-spot-device')}">
                                    <a target="_parent" href="#/space-spot-device">设备</a>
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
                        <!-- /.navbar-collapse -->
                    </div>
                    <!-- /.container-fluid -->
                </nav>
        </md-toolbar>
        <!-- /container -->
        <div>
            
        </div>
        <md-content ng-view>
        </md-content>
        <!-- /container -->
        <script src="/static/js/bundle.js"></script>
        <script src="/static/js/all.js"></script>
    </body>

    </html>

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
                <nav class="navbar navbar-default">
                    <div class="container">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </div>
                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                            <ul class="nav navbar-nav h4">
                                <?php echo Yii::$app->request->getQueryString(); ?>
                                <li ng-class="{active:isActive('/space-spot-device')}">
                                    <a target="_parent" href="#/founder">活动列表</a>
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
                <!-- /.navbar-collapse -->
        <!-- /container -->
        <md-content ng-view>
        </md-content>
        <!-- /container -->
        <script src="/static/js/bundle.js"></script>
        <script src="/static/js/all.js"></script>
    </body>

    </html>

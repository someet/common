<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html lang="en" ng-app="SomeetBackendApp">
<head>
  <link rel="stylesheet" href="/static/style/bundle.css">
  <meta name="viewport" content="initial-scale=1" />
  <link rel="stylesheet" href="http://asset.mikecrm.com/css/reset.css">
  <link rel="stylesheet" href="/static/style/pages/site-t.css">
  <!-- 新 Bootstrap 核心 CSS 文件 -->
  <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css">

  <!-- 可选的Bootstrap主题文件（一般不用引入） -->
  <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

  <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
  <script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>

  <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
  <script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="/static/style/main.css">

  <?= Html::csrfMetaTags() ?></head>
<body flex="grow" class="main_css" layout="column" ng-controller="MainCtrl">

  <div class="container">
    <nav class="navbar navbar-default" role="navigation">
      <!-- Brand and toggle get grouped for better mobile display -->

      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">Someet</a>
      </div>

      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse " id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav h4">
          <li class="active">
            <a href="#">专题</a>
          </li>
          <li>
            <a href="#">活动</a>
          </li>
          <li>
            <a href="#">联系人</a>
          </li>

        </ul>

        <ul class="nav navbar-nav navbar-right">
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
              LukeYU <b class="caret"></b>
            </a>
            <ul class="dropdown-menu">
              <li>
                <a href="#">工作日志</a>
              </li>
              <li>
                <a href="#">权限管理</a>
              </li>
              <li class="divider"></li>
              <li>
                <a href="#">注销</a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
      <!-- /.navbar-collapse --> </nav>
  </div>

  <div class="container">
    <button type="button" class="btn btn-success btn-lg">
      <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
      新建活动
    </button>
    <form class="navbar-form navbar-right" role="search">
      <div class="form-group">
        <input type="text" class="form-control" placeholder="标题"></div>
      <button type="submit" class="btn btn-default">
        <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
        搜索
      </button>
    </form>
  </div>

  <br/>

  <div class="container">
    <div>
      <ul class="nav nav-tabs">
        <li class="active">
          <a href="#">全部</a>
        </li>
        <li>
          <a href="#">一日体验</a>
        </li>
        <li>
          <a href="#">本周活动</a>
        </li>
        <li>
          <a href="#">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
          </a>
        </li>
      </ul>
    </div>

    <div  class="activityList">
      <ul class="activityListProperty">
        <li>
          <div class="panel panel-primary">
            <div class="panel-heading">
              <h3 class="panel-title">
                <button type="button" class="btn btn-default ">
                  <span class="glyphicon glyphicon-circle-arrow-up" aria-hidden="true"></span>
                  置顶
                </button>
                10444－施博文：乱画帮——来时空规划局画旧物
                <button class="btn btn-primary btn-float-right" type="button">
                  反馈
                  <span class="badge">4</span>
                </button>
                <button class="btn btn-warning btn-float-right" type="button">
                  报名（16/20）
                  <span class="badge">10</span>
                </button>
              </h3>
            </div>
            <div class="panel-body">
              <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                  本周活动
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                  <li>
                    <a href="#">一日体验</a>
                  </li>
                  <li>
                    <a href="#">临时</a>
                  </li>
                  <li>
                    <a href="#">历史活动</a>
                  </li>
                </ul>
              </div>
              <button class="btn btn-info" type="button">查看</button>
              <button class="btn btn-success" type="button">编辑</button>
              <button class="btn btn-warning" type="button">发布</button>
              <div class="btn-group btn-float-right">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                  更多
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                  <li>
                    <a href="#">删除活动</a>
                  </li>
                  <li>
                    <a href="#">复制活动</a>
                  </li>
                  <li class="divider"></li>
                  <li>
                    <a href="#">关闭报名</a>
                  </li>
                  <li>
                    <a href="#">打开报名</a>
                  </li>
                  <li class="divider"></li>
                  <li>
                    <a href="#">结束活动</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </li>


        <li>
          <div class="panel panel-primary">
            <div class="panel-heading">
              <h3 class="panel-title">
                <button type="button" class="btn btn-default ">
                  <span class="glyphicon glyphicon-circle-arrow-up" aria-hidden="true"></span>
                  置顶
                </button>
                10445－施博文：乱画帮——来时空规划局画旧物
                <button class="btn btn-primary btn-float-right" type="button">
                  反馈
                  <span class="badge">4</span>
                </button>
                <button class="btn btn-warning btn-float-right" type="button">
                  报名（16/20）
                  <span class="badge">10</span>
                </button>
              </h3>
            </div>
            <div class="panel-body">
              <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                  本周活动
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                  <li>
                    <a href="#">一日体验</a>
                  </li>
                  <li>
                    <a href="#">临时</a>
                  </li>
                  <li>
                    <a href="#">历史活动</a>
                  </li>
                </ul>
              </div>
              <button class="btn btn-info" type="button">查看</button>
              <button class="btn btn-success" type="button">编辑</button>
              <button class="btn btn-warning" type="button">发布</button>
              <div class="btn-group btn-float-right">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                  更多
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                  <li>
                    <a href="#">删除活动</a>
                  </li>
                  <li>
                    <a href="#">复制活动</a>
                  </li>
                  <li class="divider"></li>
                  <li>
                    <a href="#">关闭报名</a>
                  </li>
                  <li>
                    <a href="#">打开报名</a>
                  </li>
                  <li class="divider"></li>
                  <li>
                    <a href="#">结束活动</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </li>

      </ul>
    </div>

  </div>
  <!-- /container -->

  <script src="/static/js/bundle.js"></script>
  <script src="/static/js/all.js"></script>
</body>
</html>
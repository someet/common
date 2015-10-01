Someet Backend
================

基于 codemix/yii2-dockerized 

1. 环境搭建
-------------

需要安装 [docker](http://www.docker.com) (>=1.5.0) 和
[docker-compose](https://docs.docker.com/compose/install/)。

```sh
\# 通过 git 把项目克隆到本地，并进入项目目录
$ cp docker-compose-example.yml docker-compose.yml
$ cp .env-example .env
$ docker-compose up
\# From another terminal window:
$ docker-compose run --rm app ./yii migrate
$ cnpm install 
$ bower install
$ gulp    // 这个命令在开发的时候要让它一直运行着
```

第一次会去下载相应的 docker 镜像需要花一些时间，当启动完成后，你可以访问 http://your-docker-ip:8080

### 前端目录说明

1. client/partial AngularJS 模板文件
2. client/script  JavaScript
3. client/style   Sass
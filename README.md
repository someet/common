Someet Backend
================

基于 codemix/yii2-dockerized 

1. 环境搭建
-------------

需要安装 [docker](http://www.docker.com) (>=1.5.0) 和
[docker-compose](https://docs.docker.com/compose/install/)。


### 开发环境搭建


通过 git 把项目克隆到本地，并进入项目目录

```sh
$ cp docker-compose-example.yml docker-compose.yml
```

修改 docker-compose.yml 


>enviroment: 

>	ENABLE_ENV_FILE: 1

在自己的 Github 上生成并 修改 YOUR GITHUB API TOKEN 

数据库根据实际情况填写，多个项目共用一个数据库其他应用可以用 external_links 来连接。

```sh
$ cp .env-example .env
```

修改 .env

启动应用

```sh
$ docker-compose up
```

可以在参数后面加 -d 代表在后台执行

再打开另一个窗口，执行 migrate，以及前端依赖包安装

```sh
$ docker-compose run --rm app ./yii migrate
$ npm install -g bower gulp-cli
$ npm install gulp
$ npm install
$ bower install
$ gulp    // 这个命令在开发的时候要让它一直运行着
```

第一次会去下载相应的 docker 镜像需要花一些时间，当启动完成后，你可以访问 http://your-docker-ip:8080

#### 前端目录说明

1. client/partial AngularJS 模板文件
2. client/script  JavaScript
3. client/style   Sass

### 线上环境搭建

Requirements

- 阿里云服务器安装 docker、docker-compose
- 用 DaoCloud 管理阿里云的 Docker
- DaoCloud 还用于镜像构建， 当对代码库进行相应的 tag 的时候，会触发镜像的构建
- 使用应用编排来部署应用（docker-compose.yml 格式）


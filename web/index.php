<?php
require('/var/www/html/helpers/DockerEnv.php');
\DockerEnv::init();
$config = \DockerEnv::webConfig();
\Yii::setAlias('common', dirname(__DIR__) . '/packages/someet-common/');
\Yii::setAlias('dektrium-user', dirname(__DIR__) . '/packages/yii2-user/');
(new yii\web\Application($config))->run();

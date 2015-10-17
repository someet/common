<?php
require('/var/www/html/helpers/DockerEnv.php');
\DockerEnv::init();
$config = \DockerEnv::webConfig();
\Yii::setAlias('common', dirname(__DIR__) . '/packages/someet-common/');
(new yii\web\Application($config))->run();

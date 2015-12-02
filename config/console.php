<?php
/* @var array() $web the web configuration */
$web = self::webConfig();
return [
    'id' => $web['id'],
    'basePath' => $web['basePath'],
    'vendorPath' => $web['vendorPath'],
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'components' => [
        'db' => $web['components']['db'],
        'log' => [
            'targets' => [
                [
                    'class' => 'codemix\streamlog\Target',
                    'url' => 'php://stdout',
                    'levels' => ['info','trace'],
                    'logVars' => [],
                ],
                [
                    'class' => 'codemix\streamlog\Target',
                    'url' => 'php://stderr',
                    'levels' => ['error', 'warning'],
                    'logVars' => [],
                ],
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'yunpian' => [
            'class' => 'dcb9\Yunpian\sdk\Yunpian',
            'apiKey' => \DockerEnv::get('YUNPIAN_API_KEY'),
            'useFileTransport' => false, // 如果该值为 true 则不会真正的发送短信，而是把内容写到文件里面，测试环境经常需要用到！
        ],
        'wechat' => [
            'class' => 'callmez\wechat\sdk\MpWechat',
            'appId' => \DockerEnv::get('WEIXIN_APP_ID'),
            'appSecret' => \DockerEnv::get('WEIXIN_APP_SECRET'),
            'token' => \DockerEnv::get('WEIXIN_TOKEN'),
            'encodingAesKey' => \DockerEnv::get('WEIXIN_ENCODING_AES_KEY')
        ],
        'cache' => [
            'class' => 'yii\redis\Cache',
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => \DockerEnv::get('REDIS_PORT_6379_TCP_ADDR'),
            'port' => 6379,
            'password' => \DockerEnv::get('REDIS_1_ENV_REDIS_PASSWORD'),
            'database' => 0,
        ],
    ],
    'params' => $web['params'],
];

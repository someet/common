<?php
$config = [
    'id' => 'backend',
    'basePath' => '/var/www/html',
    'vendorPath' => '/var/www/vendor',
    'timeZone' => 'Asia/Chongqing',
    'language' => 'zh-CN',
    'bootstrap' => ['log', 'raven', 'newrelic'],
    'components' => [
        'cache' => [
            'class' => 'yii\redis\Cache',
        ],
        'session' => [
            'class' => 'yii\redis\Session',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => \DockerEnv::dbDsn(),
            'username' => \DockerEnv::dbUser(),
            'password' => \DockerEnv::dbPassword(),
            'charset' => 'utf8',
            'tablePrefix' => '',
            'enableSchemaCache' => \DockerEnv::get('ENABLE_OPTIMIZE'),
            'schemaCacheDuration' => 86400, // time in seconds
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => \DockerEnv::get('REDIS_PORT_6379_TCP_ADDR'),
            'port' => 6379,
            'password' => \DockerEnv::get('REDIS_1_ENV_REDIS_PASSWORD'),
            'database' => 0,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mail' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => \DockerEnv::get('SMTP_HOST'),
                'username' => \DockerEnv::get('SMTP_USER'),
                'password' => \DockerEnv::get('SMTP_PASSWORD'),
            ],
        ],
        'newrelic' => [
            'class' => 'bazilio\yii\newrelic\Newrelic',
            'name' => \DockerEnv::get('NEW_RELIC_APP_NAME'),
        ],
        'raven' => [
            'class' => 'e96\sentry\ErrorHandler',
            'dsn' => \DockerEnv::get('SENTRY_DSN')
        ],
        'log' => [
            'traceLevel' => \DockerEnv::get('YII_TRACELEVEL', 0),
            'targets' => [
                [
                    'class' => 'codemix\streamlog\Target',
                    'url' => 'php://stdout',
                    'levels' => ['info', 'trace'],
                    'logVars' => [],
                ],
                [
                    'class' => 'codemix\streamlog\Target',
                    'url' => 'php://stderr',
                    'levels' => ['error', 'warning'],
                    'logVars' => [],
                ],
                [
                    'class' => 'e96\sentry\Target',
                    'levels' => ['error', 'warning'],
                    'dsn' => \DockerEnv::get('SENTRY_DSN'),
                ]
            ],
        ],
        'request' => [
            'cookieValidationKey' => \DockerEnv::get('COOKIE_VALIDATION_KEY', null, !YII_ENV_TEST),
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => require(__DIR__ . '/json-response-unity.php'),
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'user' => [
            'identityClass' => 'dektrium\user\models\User',
            'loginUrl' => ['user/login'],
            'enableAutoLogin' => true,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'qiniu' => [
            'class' => 'app\components\QiniuComponent',
            'accessKey' => \DockerEnv::get('QINIU_ACCESS_KEY'),
            'secretKey' => \DockerEnv::get('QINIU_SECRET_KEY'),
        ],
        'wechat' => [
            'class' => 'callmez\wechat\sdk\MpWechat',
            'appId' => \DockerEnv::get('WEIXIN_APP_ID'),
            'appSecret' => \DockerEnv::get('WEIXIN_APP_SECRET'),
            'token' => \DockerEnv::get('WEIXIN_TOKEN'),
            'encodingAesKey' => \DockerEnv::get('WEIXIN_ENCODING_AES_KEY')
        ],
        'yunpian' => [
            'class' => 'dcb9\Yunpian\sdk\Yunpian',
            'apiKey' => \DockerEnv::get('YUNPIAN_API_KEY'),
            'useFileTransport' => false, // 如果该值为 true 则不会真正的发送短信，而是把内容写到文件里面，测试环境经常需要用到！
        ],
    ],
    'modules' => [
        'rbac' => [
            'class' => 'dektrium\rbac\Module',
        ],
        'user' => [
            'class' => 'dektrium\user\Module',
            'enableRegistration' => true,
            'enableConfirmation' => false,
            'enableUnconfirmedLogin' => true,
            'enablePasswordRecovery' => true,
            'confirmWithin' => 21600,
            'rememberFor' => 1209600,
            'admins' => ['admin'],
        ],
    ],
    'params' => require('/var/www/html/config/params.php'),
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'],
    ];
}

return $config;

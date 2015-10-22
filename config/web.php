<?php
$config = [
    'id' => 'backend',
    'name' => 'Someet活动平台',
    'basePath' => '/var/www/html',
    'vendorPath' => '/var/www/vendor',
    'timeZone' => 'Asia/Chongqing',
    'language' => 'zh-CN',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\ApcCache',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => \DockerEnv::dbDsn(),
            'username' => \DockerEnv::dbUser(),
            'password' => \DockerEnv::dbPassword(),
            'charset' => 'utf8',
            'tablePrefix' => '',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => \DockerEnv::get('SMTP_HOST'),
                'username' => \DockerEnv::get('SMTP_USER'),
                'password' => \DockerEnv::get('SMTP_PASSWORD'),
            ],
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
        'yunpian' => [
            'class' => 'dcb9\Yunpian\sdk\Yunpian',
            'apiKey' => \DockerEnv::get('YUNPIAN_API_KEY'),
            'useFileTransport' => true, // 如果该值为 true 则不会真正的发送短信，而是把内容写到文件里面，测试环境经常需要用到！
        ],
    ],
    'modules' => [
        'rbac' => [
            'class' => 'dektrium\rbac\Module',
        ],
        'user' => [
            'class' => 'dektrium\user\Module',
            'enableFlashMessages' => true,
            'enableRegistration' => true,
            'enableGeneratingPassword' => false,
            'enableConfirmation' => true,
            'enableUnconfirmedLogin' => true,
            'enablePasswordRecovery' => true,
            'confirmWithin' => 21600,
            'rememberFor' => 1209600,
            'recoverWithin' => 21600,
            'admins' => ['admin'], //只有用户名为admin这个用户可以管理用户和添加用户
            'mailer' => [
                'sender'                => 'dcj3sjt@126.com', // or ['no-reply@myhost.com' => 'Sender name']
                'welcomeSubject'        => 'Welcome subject',
                'confirmationSubject'   => 'Confirmation subject',
                'reconfirmationSubject' => 'Email change subject',
                'recoverySubject'       => 'Recovery subject',
            ],
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

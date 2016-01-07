<?php
/* @var array() $web the web configuration */
$web = self::webConfig();
return [
    'id' => $web['id'],
    'basePath' => $web['basePath'],
    'vendorPath' => $web['vendorPath'],
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'timeZone' => 'Asia/Chongqing',
    'language' => 'zh-CN',
    'components' => [
        'db' => $web['components']['db'],
        'beanstalk' => $web['components']['beanstalk'],
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
        'yunpian' => $web['components']['yunpian'],
        'wechat' => $web['components']['wechat'],
        'redis' => $web['components']['redis'],
        'cache' => $web['components']['cache'],
        'weather' => $web['components']['weather'],
    ],
    'modules' => [
        'user' => $web['modules']['user'],
    ],
    'params' => $web['params'],
    // add you controller with name and class name next to params.
    'controllerMap' => [
        'worker'=>[
            'class' => 'app\commands\WorkerController',
        ]

    ],
];

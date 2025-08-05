<?php
$params = array_merge(
    require __DIR__ . '/params.php',
);

return [
    'id' => 'app-web',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-web',
            'cookieValidationKey' => 'your-secret-key-here',
        ],

        'session' => [
            // this is the name of the session cookie used for login on the web
            'name' => 'advanced-web',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info', 'warning', 'error'],
                    'categories' => ['api.bot'],
                    'logFile' => '@app/runtime/logs/api-bot.log',
                    'maxFileSize' => 1024, // 1MB
                    'maxLogFiles' => 5,
                    'logVars' => [],
                    'prefix' => function ($message) {
                        return '';
                    },
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'top' => 'site/top',
                'api/url/create' => 'url/create',
                '<shortCode:[a-zA-Z0-9]{5}>' => 'url/redirect',
            ],
        ],
        'db' => require __DIR__ . '/db.php',
    ],
    'params' => $params,
]; 
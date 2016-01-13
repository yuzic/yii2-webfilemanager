<?php
return [
    'id' => 'app-console',
    'class' => 'yii\console\Application',
    'basePath' =>   dirname(__DIR__) . '/../',
    'runtimePath' => \Yii::getAlias('@tests/_output'),
    'bootstrap' => [],
    'components' => [
        'db' => [
            'class' => '\yii\db\Connection',
            'dsn' => 'sqlite:'.\Yii::getAlias('@tests/_output/temp.db'),
            'username' => '',
            'password' => '',
        ]
    ]
];
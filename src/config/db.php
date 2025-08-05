<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=' . (getenv('DB_HOST') ?: 'db') . ';dbname=' . (getenv('DB_DATABASE') ?: 'qnits'),
    'username' => getenv('DB_USER') ?: 'qnits',
    'password' => getenv('DB_PASSWORD') ?: 'password',
    'charset' => 'utf8mb4',
]; 
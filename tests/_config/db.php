<?php
$params = require(__DIR__ . '/params.php');
return [
    'class' => 'yii\db\Connection',
    'dsn' => $params['dsn'],
    'username' => $params['db_username'],
    'password' => $params['db_password'],
    'charset' => 'utf8',
    'enableSchemaCache' =>false,
    // Duration of schema cache.
    'schemaCacheDuration' => 3600,
    'schemaCache' => 'cache',
    'attributes'=>[
        PDO::ATTR_PERSISTENT => true
    ]
];
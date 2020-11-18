<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=basics_admin',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'enableSchemaCache' => false,
            'tablePrefix' => 'ba_'   //加入前缀名称ba_
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
        ],
    ],
];

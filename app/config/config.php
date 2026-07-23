<?php

return [
    // required
    'application_mode' => getenv('APP_MODE'),

    // required
    // 0 - срок действия cookie истечёт с окончанием сессии, time() + 60*60*24*30 установит, что срок действия cookie истекает через 30 дней
    'authCookieTime' => 0,

    'logPath' => 'log',

    'db' => array (
        'dbname' => getenv('DB_NAME'),
        'host' => getenv('DB_HOST'),
        'username' => getenv('DB_USER'),
        'password' => getenv('DB_PASSWORD'),
        'charset' => getenv('DB_CHARSET'),
        'sqlType' => getenv('DB_DRIVER'),
    ),
];
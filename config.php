<?php

return [
    'telegram' => [
        'username' => 'uficrobot',
        'token' => '6656623277:AAHBKsg4XPvxgIVbNUMBRsxr3h8iF6V6o9Q',
        'webhookUrl' => 'https://vkrka.ru/uficbot',
    ],


    'driver' => new \Yiisoft\Db\Mysql\Driver(
        (new \Yiisoft\Db\Mysql\Dsn('mysql', '127.0.0.1', 'u1998825_tg', '3306', ['charset' => 'utf8mb4']))->asString(),
        'u1998825_tg',
        '@Telega123@',
    ),
    
];
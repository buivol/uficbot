<?php

return [
    'telegram' => [
        'username' => 'uficrobot',
        'token' => '6656623277:AAHBKsg4XPvxgIVbNUMBRsxr3h8iF6V6o9Q',
        'webhookUrl' => 'https://vkrka.ru/uficbot',
    ],


    'driver' => new \Yiisoft\Db\Mysql\Driver(
        (new \Yiisoft\Db\Mysql\Dsn('mysql', '127.0.0.1', 'bot', '3306', ['charset' => 'utf8mb4']))->asString(),
        'bot',
        '@Bot777@',
    ),

    'db' =>
        [
            'prod' => 'mysql://bot:@Bot777@@194.28.226.217/bot',
        ]


];
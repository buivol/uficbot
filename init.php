<?php

//Файл инициализации

require 'vendor/autoload.php';

$config = require './config.php';

use SergiX44\Nutgram\Nutgram;


ActiveRecord\Config::initialize(function ($cfg) {
    global $config;
    $cfg->set_model_directory(__DIR__ . '/models');
    $cfg->set_connections(
        array(
            'development' => "mysql://{$config['db']['username']}:{$config['db']['password']}@{$config['db']['host']}/{$config['db']['database']}",
            'test' => "mysql://{$config['db']['username']}:{$config['db']['password']}@{$config['db']['host']}/{$config['db']['database']}",
            'production' => "mysql://{$config['db']['username']}:{$config['db']['password']}@{$config['db']['host']}/{$config['db']['database']}"
        )
    );
});


$tg = new Nutgram($config['telegram']['token']);
//TODO: Сделать чтобы ставился один раз, а не при каждом вызове
$tg->setWebhook($config['telegram']['webhookUrl']);

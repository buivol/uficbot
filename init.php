<?php

//Файл инициализации
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

$config = require './config.php';

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Webhook;
use SergiX44\Nutgram\RunningMode\Polling;
use \SergiX44\Nutgram\Configuration;

// Connection.
$cfg = ActiveRecord\Config::instance();
$cfg->set_connections($config['db']);
$cfg->set_default_connection('prod');
$cfg->set_date_format( "Y-m-d H:i:s" );


$tg = new Nutgram($config['telegram']['token'], new Configuration(logger: \SergiX44\Nutgram\Logger\ConsoleLogger::class));
$tg->setRunningMode(Polling::class);
//TODO: Сделать чтобы ставился один раз, а не при каждом вызове
//$tg->setWebhook($config['telegram']['webhookUrl']);

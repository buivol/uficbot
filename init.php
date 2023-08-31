<?php

//Файл инициализации
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

$config = require './config.php';

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Webhook;
use Yiisoft\Cache\ArrayCache;
use \Yiisoft\Db\Cache\SchemaCache;
use \Yiisoft\Db\Mysql\Connection;


// Connection.
$arrayCache = new ArrayCache();
$schemaCache = new SchemaCache($arrayCache);
$db = new Connection($config['driver'], $schemaCache);


$tg = new Nutgram($config['telegram']['token']);
$tg->setRunningMode(Webhook::class);
//TODO: Сделать чтобы ставился один раз, а не при каждом вызове
//$tg->setWebhook($config['telegram']['webhookUrl']);

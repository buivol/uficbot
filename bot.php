<?php

namespace App;

use SergiX44\Nutgram\Nutgram;
use models\User;

require_once './init.php';
require_once 'MessageHandler.php';
const STEP_REGISTRATION_START = 'registration_start';
const STEP_REGISTRATION_NAME = 'registration_name';
const STEP_REGISTRATION_FIRST_NAME = 'registration_first_name';
const STEP_REGISTRATION_LAST_NAME = 'registration_last_name';
const STEP_REGISTRATION_BIRTH_YEAR = 'registration_birth';
const STEP_REGISTRATION_ROOM = 'registration_room';
const STEP_REGISTRATION_PHONE = 'registration_phone';
const STEP_TALON_COUNT = 'talon_count';
const STEP_MAINPAGE = 'mainpage';


const CALLBACK_ADD_BALANCE = 'add_balance';
const CALLBACK_BUY_TALON_MAIN = 'buy_talon_main';
const CALLBACK_BUY_TALON = 'buy_talon';
const CALLBACK_MAIN = 'main';

const ADMIN_CHAT_ID = -960649112;


file_put_contents('last_request.log', file_get_contents('php://input'));
$handler = new MessageHandler();

/** @var $tg Nutgram */
$tg->onMessage(function (Nutgram $bot) use ($handler) {


    if($bot->chatId() == ADMIN_CHAT_ID){
       $handler->onAdminMessage($bot);
    } else {
        try {
            $user = User::find($bot->userId());
        } catch (\Exception $e) {
            // Пользоваетль не найден
            $user = null;
        }

        /** @var User $user */
        if (!$user) {
            // новый пользователь, добавляем в базу
            $user = new User();
            $user->id = $bot->userId();
            $user->nickname = $bot->user()->username;
            $user->step = STEP_REGISTRATION_START;
        }

        $handler = new MessageHandler();
        $func = "step_" . $user->step;
        $handler->$func($bot, $user);
        $user->save();
    }

});



$tg->onCallbackQuery(function (Nutgram $bot) use ($handler){
    $user = User::find($bot->userId());
    $arr = explode('/', $bot->callbackQuery()->data);
    $func = "callback_" . $arr[0];
    $handler->$func($bot, $user, $arr[1] ?? null);
    $user->save();
});


$tg->fallback(function (Nutgram $bot) {
    $bot->sendMessage('Я тебя не понимаю');
});


$handler->sendAdmin($tg, 'Сервер запущен');



$tg->run();
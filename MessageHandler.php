<?php

namespace App;

use helpers\TextUtils;
use models\User;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardRemove;

class MessageHandler
{
    public function step_registration_start(Nutgram &$bot, User &$user)
    {
        $bot->sendMessage("Привет {$user->nickname}, для начала тебе надо пройти процедуру регистрации. Введи своё имя");
        $user->step = STEP_REGISTRATION_NAME;
    }

    public function step_registration_name(Nutgram &$bot, User &$user)
    {
        $name = TextUtils::firstUp($bot->message()->text);

        if (mb_strlen($name) > 3) {
            $user->name = $name;
            $user->step = STEP_REGISTRATION_FIRST_NAME;
            $bot->sendMessage("Отлично, {$user->name}, теперь введи свою фамилию:");
        } else {
            $bot->sendMessage("{$user->nickname}, твое имя необходимо для дальнейшей работы, пожалуйста введи правильное имя:");
        }
    }

    public function step_registration_first_name(Nutgram &$bot, User &$user)
    {
        $firstName = TextUtils::firstUp($bot->message()->text);

        if (mb_strlen($firstName) > 3) {
            $user->first_name = $firstName;
            $user->step = STEP_REGISTRATION_LAST_NAME;
            $bot->sendMessage("Отлично, {$user->name} {$user->first_name}, теперь введи своё отчество:");
        } else {
            $bot->sendMessage("{$user->name}, твоя фамилия необходима для дальнейшей работы, пожалуйста введи её правильно:");
        }
    }


    public function step_registration_last_name(Nutgram &$bot, User &$user)
    {
        $lastName = TextUtils::firstUp($bot->message()->text);

        if (mb_strlen($lastName) > 3) {
            $user->last_name = $lastName;
            $user->step = STEP_REGISTRATION_BIRTH_YEAR;
            $bot->sendMessage("Записал. В каком году ты родился?");
        } else {
            $bot->sendMessage("{$user->name}, твоя отчество необходимо для дальнейшей работы, пожалуйста введи его правильно:");
        }
    }

    public function step_registration_birth(Nutgram &$bot, User &$user)
    {
        $birth = intval($bot->message()->text);

        if ($birth > 1940 && $birth < 2010) {
            $user->birth = $birth;
            $user->step = STEP_REGISTRATION_ROOM;
            $bot->sendMessage("Супер. Жизнь только начинается. Из какой ты комнаты? Введи номер:");
        } else {
            $bot->sendMessage("{$user->name}, год твоего рождения нужен для дальнейшей работы, пожалуйста введи его правильно:");
        }
    }

    public function step_registration_room(Nutgram &$bot, User &$user)
    {
        $room = intval($bot->message()->text);

        if ($room > 1 && $room < 40) {
            $user->room = $room;
            $user->step = STEP_REGISTRATION_PHONE;
            $bot->sendMessage(
                text: "Хорошо, {$user->name} из {$room}-ой комнаты, когда я соберу себе роботизированное тело обязательно загляну к тебе. Остался последний шаг, напиши свой номер телефона:",
                reply_markup: ReplyKeyboardMarkup::make()->addRow(
                    KeyboardButton::make('Отправить свой номер', request_contact: true)
                ));
        } else {
            $bot->sendMessage("{$user->name}, не волнуйся, я всеголишь робот и не приду к тебе этой ночью, введи номе комнаты правильно");
        }
    }


    public function step_registration_phone(Nutgram &$bot, User &$user)
    {
        $phone = $bot->message()->contact
            ? $bot->message()->contact->phone_number
            : $bot->message()->text;

        $phone = preg_replace("/[^0-9]/", '', $phone);


        if ($phone > 79000000001 && $phone < 89999999999) {
            $user->phone = $phone;
            $user->step = STEP_MAINPAGE;
            $bot->sendMessage("{$user->name}, ты лучший! Регистрация завершена! Теперь ты можешь пользоваться всем функионалом бота.",
                reply_markup: ReplyKeyboardRemove::make(true));
            $this->step_mainpage($bot, $user);
        } else {
            $bot->sendMessage("{$user->name}, твой телефон нужен для дальнейшей работы, пожалуйста введи его правильно:",
                reply_markup: ReplyKeyboardMarkup::make()->addRow(
                    KeyboardButton::make('Отправить свой номер', request_contact: true)
                ));
        }
    }

    public function step_mainpage(Nutgram &$bot, User &$user)
    {
        $message = "Привет, {$user->name}\n" .
                    "На твоём счету {$user->balance} руб.\n" .
                    "Чем я могу тебе помочь?\n";

        $bot->sendMessage(text: $message, reply_markup: InlineKeyboardMarkup::make()->addRow(
            InlineKeyboardButton::make('Пополнить счёт', callback_data: 'add_balance'))->addRow(
            InlineKeyboardButton::make('Купить талоны', callback_data: 'talon_start'))->addRow(
            InlineKeyboardButton::make('Купить бланки заявлений', callback_data: 'zayava'))->addRow(
            InlineKeyboardButton::make('Напечатать заявление', callback_data: 'print'))->addRow(
            InlineKeyboardButton::make('Сообщить свой график', callback_data: 'grafik'))
        );
    }

}
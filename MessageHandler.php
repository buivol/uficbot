<?php

namespace App;

use helpers\TextUtils;
use models\Product;
use models\User;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Input\InputMediaPhoto;
use SergiX44\Nutgram\Telegram\Types\Internal\InputFile;
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
            InlineKeyboardButton::make('Пополнить счёт', callback_data: CALLBACK_ADD_BALANCE))->addRow(
            InlineKeyboardButton::make('Купить талоны', callback_data: CALLBACK_BUY_TALON_MAIN))->addRow(
            InlineKeyboardButton::make('Купить бланки заявлений', callback_data: 'zayava'))->addRow(
            InlineKeyboardButton::make('Напечатать заявление', callback_data: 'print'))->addRow(
            InlineKeyboardButton::make('Сообщить свой график', callback_data: 'grafik'))
        );
    }


    public function callback_add_balance(Nutgram &$bot, User &$user)
    {
        $message = "Чтобы пополнить свой баланс отпавь необходимую сумму по номеру телефона +7 (999) 999-99-99 в комментарии к платежу укажи:"
            . "\n\nПополнение бота №{$user->id}";
        $keyboard = InlineKeyboardMarkup::make()->addRow(
            InlineKeyboardButton::make('Я пополнил', callback_data: CALLBACK_ADD_BALANCE))->addRow(
            InlineKeyboardButton::make('Могу только наличными', callback_data: 'talon_start'))->addRow(
            InlineKeyboardButton::make('< Назад', callback_data: CALLBACK_MAIN));

        $bot->editMessageText(text: $message, chat_id: $bot->userId(), message_id: $bot->callbackQuery()->message->message_id, reply_markup: $keyboard);

    }


    public function callback_buy_talon_main(Nutgram &$bot, User &$user)
    {
        $this->talon_page($bot, $user);
    }

    public function step_talon_count(Nutgram &$bot, User &$user)
    {
        $bot->deleteMessage($bot->userId(), $bot->message()->message_id);
        $user->talon_count = intval($bot->message()->text);
        $this->talon_page($bot, $user);
    }

    public function callback_buy_talon_minus_1(Nutgram &$bot, User &$user)
    {
        $user->talon_count--;
        $this->talon_page($bot, $user);
    }

    public function callback_buy_talon_minus_10(Nutgram &$bot, User &$user)
    {
        $user->talon_count -= 10;
        $this->talon_page($bot, $user);
    }

    public function callback_buy_talon_plus_1(Nutgram &$bot, User &$user)
    {
        $user->talon_count++;
        $this->talon_page($bot, $user);
    }

    public function callback_buy_talon_plus_10(Nutgram &$bot, User &$user)
    {
        $user->talon_count += 10;
        $this->talon_page($bot, $user);
    }

    public function talon_page(Nutgram &$bot, User &$user)
    {
        $product = Product::find(1);

        if ($user->talon_count < 1) {
            $user->talon_count = 1;
        }

        $user->talon_count = ($user->talon_count > $product->count) ? $product->count : $user->talon_count;
        $summ = $product->price * $user->talon_count;

        $message = "<b>{$product->name}</b>\n\n" .
            "<i>{$product->description}</i>\n\n" .
            "Выберите количество товара или введите его вручную\n" .
            "Выбрано <b>{$user->talon_count}</b> шт.";
        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make("Оплатить {$summ} руб", callback_data: CALLBACK_ADD_BALANCE)
            )->addRow(
                InlineKeyboardButton::make('-1', callback_data: 'buy_talon_minus_1'),
                InlineKeyboardButton::make('-10', callback_data: 'buy_talon_minus_10'),
                InlineKeyboardButton::make('+10', callback_data: 'buy_talon_plus_10'),
                InlineKeyboardButton::make('+1', callback_data: 'buy_talon_plus_1'),
            )->addRow(
                InlineKeyboardButton::make('< Назад', callback_data: CALLBACK_MAIN)
            );
        $user->last_message_id = $this->sendMessage($bot,
            $message, 'http://blog.sergeykopylov.ru/pictures/Blank.png',
            message_id: $bot->callbackQuery()->message->message_id,
            keyboard: $keyboard);
        $user->step = STEP_TALON_COUNT;
    }

    public function callback_main(Nutgram &$bot, User &$user)
    {
        $message = "Привет, <b>{$user->name}</b>\n" .
            "На твоём счету {$user->balance} руб.\n" .
            "Чем я могу тебе помочь?\n";

        $keyboard = InlineKeyboardMarkup::make()->addRow(InlineKeyboardButton::make('Пополнить счёт', callback_data: CALLBACK_ADD_BALANCE))->addRow(
            InlineKeyboardButton::make('Купить талоны', callback_data: CALLBACK_BUY_TALON_MAIN))->addRow(
            InlineKeyboardButton::make('Купить бланки заявлений', callback_data: 'zayava'))->addRow(
            InlineKeyboardButton::make('Напечатать заявление', callback_data: 'print'))->addRow(
            InlineKeyboardButton::make('Сообщить свой график', callback_data: 'grafik'));

        $this->sendMessage($bot, $message, message_id: $bot->callbackQuery()->message->message_id, keyboard: $keyboard);
    }

    public function sendMessage(Nutgram &$bot, $message = '', $photo = null, $message_id = null, $keyboard = null): int
    {
        try {
            $bot->deleteMessage(chat_id: $bot->userId(), message_id: $message_id);
        } catch (\Exception $e) {

        } finally {
            if ($photo) {
                $m = $bot->sendPhoto(photo: $photo, chat_id: $bot->userId(), caption: $message, parse_mode: ParseMode::HTML, reply_markup: $keyboard);
                return $m->message_id;
            } else {
                $m = $bot->sendMessage(text: $message, chat_id: $bot->userId(), parse_mode: ParseMode::HTML, reply_markup: $keyboard);
                return $m->message_id;
            }
        }
    }

    public function sendAdmin(Nutgram &$bot, $text, $keyboard = null)
    {
        $m = $bot->sendMessage(text: $text, chat_id: ADMIN_CHAT_ID, parse_mode: ParseMode::HTML, reply_markup: $keyboard);
        return $m->message_id;
    }

    public function onAdminMessage(Nutgram &$bot)
    {

    }

}
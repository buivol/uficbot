<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

global $db;
require_once './init.php';

use SergiX44\Nutgram\Nutgram;
use \Yiisoft\ActiveRecord\ActiveQuery;
use models\User;

const STEP_REGISTRATION_START = 'registration_start';
const STEP_REGISTRATION_NAME = 'registration_name';

const STEP_MAINPAGE = 'mainpage';


file_put_contents('last_request.log', file_get_contents('php://input'));

function messageHandler(Nutgram $bot, User $user)
{
    if ($user->step == STEP_REGISTRATION_START) {
        $bot->sendMessage("Привет {$user->nickname}, для начала тебе надо пройти процедуру регистрации. Введи своё имя");
    } else if ($user->step == STEP_REGISTRATION_NAME) {
        $user->first_name = $bot->message()->text;
        $bot->sendMessage("Отлично, {$user->first_name}, теперь введи свою фамилию");
    } else {
        $bot->sendMessage("Нет действий для шага {$user->step}");
    }
    $user->save();
}


/** @var $tg Nutgram */
$tg->onCommand('start', function (Nutgram $bot) use ($db) {
    messageHandler($bot, new User($db));
});



echo "\r\n ----- 0 -------";

$tg->onMessage(function (Nutgram $bot) use ($db) {

    $bot->sendMessage(text: 'sss', chat_id: 5583104886);
    echo "\r\n ----- 0.1 -------";
    $userQuery = new ActiveQuery(User::class, $db);

    echo "\r\n ----- 1 -------";
    $user = $userQuery->findOne($bot->userId());
    echo "\r\n ----- 2 -------";



    /** @var User $user */
    if (!$user) {
        echo 'Создаем пользователя';
        // новый пользователь, добавляем в базу
        $user = new User($db);
        $user->id = $bot->userId();
        $user->nickname = $bot->user()->username;
        $user->step = STEP_REGISTRATION_START;
        echo 'Пользователь создан';
    } else {
        echo 'Пользователь уже есть';
        $user->step = STEP_MAINPAGE;
    }

    messageHandler($bot, $user);
});


$tg->fallback(function (Nutgram $bot) {
    $bot->sendMessage('Sorry, I don\'t understand.');
});

//$tg->sendMessage(text: 'hi', chat_id: 5583104886);

$tg->run();



//// Обработка команды /start
//$telegram->addCommand('start', function ($update) use ($telegram, $mysqli) {
//    $user_id = $update->getMessage()->getFrom()->getId();
//    $chat_id = $update->getMessage()->getChat()->getId();
//
//    $userExistsQuery = "SELECT * FROM users WHERE user_id = $user_id";
//    $userExistsResult = $mysqli->query($userExistsQuery);
//
//    if ($userExistsResult->num_rows === 0) {
//
//        $keyboard = Keyboard::forceReply();
//        $telegram->sendMessage(compact('chat_id', 'message', 'keyboard'));
//    } else {
//        showProducts($update);
//    }
//});
//
//// Функция для вывода списка товаров
//function showProducts($update)
//{
//    global $telegram, $mysqli;
//
//    $user_id = $update->getMessage()->getFrom()->getId();
//    $chat_id = $update->getMessage()->getChat()->getId();
//
//    $getProductsQuery = "SELECT * FROM products";
//    $productsResult = $mysqli->query($getProductsQuery);
//
//    $keyboard = [];
//
//    while ($product = $productsResult->fetch_assoc()) {
//        $keyboard[] = [$product['name']];
//    }
//
//    $reply_markup = Keyboard::make([
//        'keyboard' => $keyboard,
//        'resize_keyboard' => true,
//        'one_time_keyboard' => true
//    ]);
//
//    $message = "Выберите товар:";
//    $telegram->sendMessage(compact('chat_id', 'message', 'reply_markup'));
//}
//
//// Обработка выбора товара
//$telegram->on(function ($update) use ($telegram, $mysqli) {
//    $user_id = $update->getMessage()->getFrom()->getId();
//    $chat_id = $update->getMessage()->getChat()->getId();
//    $message = $update->getMessage()->getText();
//
//    $getProductQuery = "SELECT * FROM products WHERE name = '$message'";
//    $productResult = $mysqli->query($getProductQuery);
//
//    if ($productResult->num_rows > 0) {
//        $product = $productResult->fetch_assoc();
//        $keyboard = Keyboard::forceReply();
//        $telegram->sendMessage(compact('chat_id', 'message', 'keyboard'));
//
//        $telegram->sendMessage([
//            'chat_id' => $chat_id,
//            'text' => "Цена за 1 штуку: {$product['price']}\nВведите количество:"
//        ]);
//    }
//}, function ($update) {
//    return true; // Возвращаем true, чтобы обработать следующий шаг
//});
//
//// Запуск бота
//$telegram->run();
//

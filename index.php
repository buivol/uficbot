<?php

require_once './init.php';

use SergiX44\Nutgram\Nutgram;

/** @var $tg Nutgram */

$tg->onCommand('start {parameter}', function (Nutgram $bot, $parameter) {
    $bot->sendMessage("Привет {$parameter}");
});

$tg->onCommand('help', function (Nutgram $bot) {
    $bot->sendMessage('Help me!');
});

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

<?php
require 'vendor/autoload.php';

require 'config.php';
require 'function.php';

use Telegram\Bot\Api;

$telegram = new Api(BOT_API_KEY); //set api telegram bot

$result = $telegram -> getWebhookUpdates(); //get full information about message

$text = $result['message']['text']; //Text message
$chat_id = $result['message']['chat']['id']; //id user
$name = $result['message']['from']['username']; //Username

$keyboard_main = [
    ["\xF0\x9F\x93\x83 Расчитать полис"],
    ["Информация"]
];

$keyboard_home = [
    ["Домой"]
];

$keyboard_rp = [
    ["Весь мир", "Вся Европа"],
    ["Назад"]
];



if($text){

    switch ($text){

        case '/start':{
            $reply = "Добрый день!.<br/> Это - бот, который умеет рассчитывать туристические страховые полисы, оставлять заявки на их приобретение, присылать их в нужный момент. Бот работает с информацией компании «Европейское туристическое страхование (ERV)";
            UserEvent($chat_id, 'Null');
            $keyboard = $keyboard_main;

            break;
        }

        case 'Информация':{
            $reply = "Вывод текста";
            UserEvent($chat_id, 'Null');
            $keyboard = $keyboard_home;

            break;
        }

        case "\xF0\x9F\x93\x83 Расчитать полис":{
            $reply = "Пожалуйста, введите название страны, или выберите из предложенных вариантов";
            UserEvent($chat_id, 'RP');
            $keyboard = $keyboard_rp;

            break;
        }
    }

    //отправка смс

    $reply_markup = $telegram->replyKeyboardMarkup([
        'keyboard' => $keyboard,
        'resize_keyboard' => true,
        'one_time_keyboard' => false
    ]);

    $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => $reply,
        'parse_mode'=> 'HTML',
        'reply_markup' => $reply_markup
    ]);


}else{
    $telegram->sendMessage([
        'chat_id' => $chat_id,
        'text' => "Отправьте текстовое сообщение."
    ]);
}

?>
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

$keyboard_inf_back = [
    ["Информация", "Назад"]
];

$keyboard_home = [
    ["Домой"]
];

$keyboard_rp = [
    ["Весь мир", "Вся Европа"],
    ["Назад"]
];



if($text){
    $keyboard = $keyboard_main;

    switch ($text){

        case '/start':{
            $reply = $lang['start_text'];
            UserEvent($chat_id, 'Null');
            $keyboard = $keyboard_main;

            break;
        }

        case 'Информация':{
            $reply = $lang['information_text'];
            UserEvent($chat_id, 'Null');
            $keyboard = $keyboard_home;

            break;
        }

        case "\xF0\x9F\x93\x83 Расчитать полис":{
            $reply = $lang['rp_text'];
            UserEvent($chat_id, 'RP');
            $keyboard = $keyboard_rp;

            break;
        }

        case "Назад":{
            switch (UserSelect($chat_id)){
                case "RP": {
                    $reply = $lang['start_text'];
                    UserEvent($chat_id, 'Null');
                    $keyboard = $keyboard_main;
                    break;
                }

                case "All_Europe":
                case "All_World":{
                    $reply = $lang['rp_text'];
                    UserEvent($chat_id, 'RP');
                    $keyboard = $keyboard_rp;
                    break;
                }
                case "Date_to_europe":{
                    $reply = $lang['all_europe_text'] . $lang['dateto_text'];
                    UserEvent($chat_id, 'All_Europe');
                    OrderEdit($chat_id, 'world', 'Вся Европа');
                    $keyboard = $keyboard_inf_back;
                    break;
                }
                case "Date_to_world":{
                    $reply = $lang['all_world_text'] . $lang['dateto_text'];
                    UserEvent($chat_id, 'All_World');
                    OrderEdit($chat_id, 'world', 'Весь мир');
                    $keyboard = $keyboard_inf_back;
                    break;
                }

            }
            break;
        }

        case "Домой":{
            $reply = $lang['start_text'];
            UserEvent($chat_id, 'Null');
            $keyboard = $keyboard_main;
            break;
        }

        case "Вся Европа":{
            $reply = $lang['all_europe_text'] . $lang['dateto_text'];
            UserEvent($chat_id, 'All_Europe');
            OrderEdit($chat_id, 'world', 'Вся Европа');
            $keyboard = $keyboard_inf_back;
            break;
        }

        case "Весь мир":{
            $reply = $lang['all_world_text'] . $lang['dateto_text'];
            UserEvent($chat_id, 'All_World');
            OrderEdit($chat_id, 'world', 'Весь мир');
            $keyboard = $keyboard_inf_back;
            break;
        }

        case (preg_match_all('/^[1-3]{1}[0-9]{1}[.]{1}[0-1]{1}[0-9]{1}[.]{1}[2]{1}[0]{1}[1-2]{1}[0-9]{1}$/', $text) ? true : false):{

            switch (UserSelect($chat_id)){
                case "All_World":{
                    $reply = $lang['date_back'];
                    UserEvent($chat_id, 'Date_to_world');
                    OrderEdit($chat_id, 'date_to', $text);
                    $keyboard = $keyboard_inf_back;
                    break;
                }
                case "All_Europe":{
                    $reply = $lang['date_back'];
                    UserEvent($chat_id, 'Date_to_europe');
                    OrderEdit($chat_id, 'date_to', $text);
                    $keyboard = $keyboard_inf_back;
                    break;
                }

                case "Date_to_europe":
                case "Date_to_world":{
                    $reply = $lang['date_back'];
                    UserEvent($chat_id, 'Date_back');
                    OrderEdit($chat_id, 'date_back', $text);
                    $keyboard = $keyboard_inf_back;
                    break;
                }
            }
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
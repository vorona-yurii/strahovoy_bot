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
    ["Информация", "Назад"],
    ["\xF0\x9F\x8F\xA0 На главную"]
];

$keyboard_home = [
    ["Домой"]
];

$keyboard_back = [
    ["Назад"],
    ["\xF0\x9F\x8F\xA0 На главную"]
];

$keyboard_rp = [
    ["Весь мир", "Вся Европа"],
    ["Назад"]
];

$keyboard_work_recreation = [
    ["Отдых", "Работа"],
    ["Назад", "\xF0\x9F\x8F\xA0 На главную"]
];

$keyboard_tarif = [
    ["Стандарт", "Расширенный"],
    ["Путешествие на авто"],
    ["Назад", "\xF0\x9F\x8F\xA0 На главную"]
];

$keyboard_civil_bag_email = [
    ["\xF0\x9F\x93\x83 Да", "\xE2\x9D\x8C Нет"],
    ["Назад", "\xF0\x9F\x8F\xA0 На главную"]
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
                    $keyboard = $keyboard_inf_back;
                    break;
                }
                case "Date_to_world":{
                    $reply = $lang['all_world_text'] . $lang['dateto_text'];
                    UserEvent($chat_id, 'All_World');
                    $keyboard = $keyboard_inf_back;
                    break;
                }

                case "Date_back":{
                    $reply = $lang['date_back'];

                    switch (OrderSelect($chat_id, 'world')){

                        case "Весь мир":{
                            UserEvent($chat_id, 'Date_to_world');
                            break;
                        }
                        case "Вся Европа":{
                            UserEvent($chat_id, 'Date_to_europe');
                            break;
                        }
                    }

                    $keyboard = $keyboard_inf_back;
                    break;
                }

                case "Work":
                case "Recreation":{
                    $reply = $lang['work_recreation_text'];
                    UserEvent($chat_id, 'Date_back');
                    $keyboard = $keyboard_work_recreation;
                    break;
                }

                case "Extended_tarif":
                case "Car_tarif":
                case "Standart_tarif":{
                    $reply = $lang['tarif_text'];
                    UserEvent($chat_id, 'Recreation');
                    $keyboard = $keyboard_tarif;
                    break;
                }

                case "Not_civil":
                case "Yes_civil": {
                    $reply = $lang['civil_text'];
                    UserEvent($chat_id, 'Standart_tarif');
                    $keyboard = $keyboard_civil_bag_email;
                    break;
                }

                case "Not_baggage":
                case "Yes_baggage": {
                    $reply = $lang['baggage_text'];
                    UserEvent($chat_id, 'Yes_civil');
                    $keyboard = $keyboard_civil_bag_email;
                    break;
                }

                case "Success":{
                    $reply = $lang['date_bith_text'];
                    UserEvent($chat_id, 'Yes_civil');
                    $keyboard = $keyboard_back;
                    break;
                }

                case "Yes_order":{
                    $reply = $lang['success_text'];
                    UserEvent($chat_id, 'Success');
                    $keyboard = $keyboard_civil_bag_email;
                    break;
                }

                case "Email":{
                    $reply = $lang['email_text'];
                    UserEvent($chat_id, 'Yes_order');
                    $keyboard = $keyboard_back;
                    break;
                }
                case "Phone":{
                    $reply = $lang['phone_text'];
                    UserEvent($chat_id, 'Email');
                    $keyboard = $keyboard_back;
                    break;
                }

            }
            break;
        }

        case "\xF0\x9F\x8F\xA0 На главную":
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
        //получаем дату а в формате xx.xx.xxxx
        case (preg_match_all('/^[1-3]{1}[0-9]{1}[.]{1}[0-1]{1}[0-9]{1}[.]{1}[1-2]{1}[0-9]{1}[0-9]{1}[0-9]{1}$/', $text) ? true : false):{

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
                    $reply = $lang['work_recreation_text'];
                    UserEvent($chat_id, 'Date_back');
                    OrderEdit($chat_id, 'date_back', $text);
                    $keyboard = $keyboard_work_recreation;
                    break;
                }

                case "Work":
                case "Extended_tarif":
                case "Car_tarif":
                case "Not_baggage":
                case "Yes_baggage":{
                    $reply = str_replace("%world%", OrderSelect($chat_id, 'world'), $lang['success_text']);
                    $reply = str_replace("%date_to%", OrderSelect($chat_id, 'date_to'), $lang['success_text']);
                    $reply = str_replace("%date_back%", OrderSelect($chat_id, 'date_back'), $lang['success_text']);
                    $reply = str_replace("%days_total%", DaysCount($chat_id), $lang['success_text']);

                    switch (OrderSelect($chat_id, 'world')){

                        case "Весь мир":{
                            $world_total1 = '50000';
                            $world_total2 = '5000';
                            break;
                        }
                        case "Вся Европа":{
                            $world_total1 = '30000';
                            $world_total2 = '3000';
                            break;
                        }
                    }

                    $reply = str_replace("%world_total1%", $world_total1, $lang['success_text']);
                    $reply = str_replace("%world_total2%", $world_total2, $lang['success_text']);
                    $reply = str_replace("%price%", OrderTotal($chat_id), $lang['success_text']);

                    UserEvent($chat_id, 'Success');
                    OrderEdit($chat_id, 'bithday', $text);
                    $keyboard = $keyboard_civil_bag_email;

                    break;
                }
            }
            break;
        }
        //получаем емейл
        case (preg_match_all('/^(?!.*@.*@.*$)(?!.*@.*\-\-.*\..*$)(?!.*@.*\-\..*$)(?!.*@.*\-$)(.*@.+(\..{1,11})?)$/', $text) ? true : false):{
            $reply = $lang['phone_text'];
            UserEvent($chat_id, 'Email');
            OrderEdit($chat_id, 'email', $text);
            $keyboard = $keyboard_back;
            break;
        }

        //получаем номер телефона
        case (preg_match_all('/^\+380\d{3}\d{2}\d{2}\d{2}$/', $text) ? true : false):{
            $reply = $lang['thank_text'];
            UserEvent($chat_id, 'Phone');
            OrderEdit($chat_id, 'phone', $text);
            $keyboard = $keyboard_back;
            break;
        }

        case 'Отдых':{
            $reply = $lang['tarif_text'];
            UserEvent($chat_id, 'Recreation');
            OrderEdit($chat_id, 'work_recreation', 'Отдых');
            $keyboard = $keyboard_tarif;

            break;
        }

        case 'Работа':{
            $reply = $lang['date_bith_text'];
            UserEvent($chat_id, 'Work');
            OrderEdit($chat_id, 'work_recreation', 'Работа');
            OrderEdit($chat_id, 'tarif', '-');
            OrderEdit($chat_id, 'civil', '-');
            OrderEdit($chat_id, 'baggage', '-');
            $keyboard = $keyboard_back;

            break;
        }

        case 'Стандарт':{
            $reply = $lang['civil_text'];
            UserEvent($chat_id, 'Standart_tarif');
            OrderEdit($chat_id, 'tarif', 'Стандарт');
            $keyboard = $keyboard_civil_bag_email;

            break;
        }

        case 'Расширенный':{
            $reply = $lang['date_bith_text'];
            UserEvent($chat_id, 'Extended_tarif');
            OrderEdit($chat_id, 'tarif', 'Расширенный');
            OrderEdit($chat_id, 'civil', '-');
            OrderEdit($chat_id, 'baggage', '-');
            $keyboard = $keyboard_back;

            break;
        }

        case 'Путешествие на авто':{
            $reply = $lang['date_bith_text'];
            UserEvent($chat_id, 'Car_tarif');
            OrderEdit($chat_id, 'tarif', 'Путешествие на авто');
            OrderEdit($chat_id, 'civil', '-');
            OrderEdit($chat_id, 'baggage', '-');
            $keyboard = $keyboard_back;

            break;
        }

        case "\xF0\x9F\x93\x83 Да":{
            switch (UserSelect($chat_id)){
                case 'Standart_tarif': {
                    $reply = $lang['baggage_text'];
                    UserEvent($chat_id, 'Yes_civil');
                    OrderEdit($chat_id, 'civil', 'Да');
                    $keyboard = $keyboard_civil_bag_email;
                    break;
                }

                case 'Not_civil':
                case 'Yes_civil': {
                    $reply = $lang['date_bith_text'];
                    UserEvent($chat_id, 'Yes_baggage');
                    OrderEdit($chat_id, 'baggage', 'Да');
                    $keyboard = $keyboard_back;
                    break;
                }

                case "Success": {
                    $reply = $lang['email_text'];
                    UserEvent($chat_id, 'Yes_order');
                    $keyboard = $keyboard_back;
                    break;
                }
            }
            break;
        }

        case "\xE2\x9D\x8C Нет":{
            switch (UserSelect($chat_id)){
                case 'Standart_tarif': {
                    $reply = $lang['baggage_text'];
                    UserEvent($chat_id, 'Not_civil');
                    OrderEdit($chat_id, 'civil', 'Нет');
                    $keyboard = $keyboard_civil_bag_email;
                    break;
                }

                case 'Yes_civil':
                case 'Not_civil': {
                    $reply = $lang['date_bith_text'];
                    UserEvent($chat_id, 'Not_baggage');
                    OrderEdit($chat_id, 'baggage', 'Нет');
                    $keyboard = $keyboard_back;
                    break;
                }

                case "Success": {
                    $reply = $lang['start_text'];
                    UserEvent($chat_id, 'Null');
                    $keyboard = $keyboard_main;
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
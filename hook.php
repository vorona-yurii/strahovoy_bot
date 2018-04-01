<?php
require 'vendor/autoload.php';

require 'config.php';
require 'function.php';

use Telegram\Bot\Api;

$telegram = new Api(BOT_API_KEY); //set api telegram bot

$result = $telegram -> getWebhookUpdates(); //get full information about message

$text = $result['message']['text']; //Text message
$phone_number = $result['message']['contact']['phone_number'];
$chat_id = $result['message']['chat']['id']; //id user
$name = $result['message']['from']['username']; //Username

$keyboard_main = [
    ["\xF0\x9F\x93\x83 Расчитать полис"],
    ["Информация"]
];

$keyboard_back = [
    ['Информация'],
    ["Назад"],
    ["\xF0\x9F\x8F\xA0 На главную"]
];
$keyboard_back_phone = [
    [['text'=>"Отправить телефон",'request_contact'=>true]],
    ['Информация'],
    ["Назад"],
    ["\xF0\x9F\x8F\xA0 На главную"]
];

$keyboard_rp = [
    ["Весь мир", "Вся Европа"],
    ['Информация'],
    ["Назад"],
    ["\xF0\x9F\x8F\xA0 На главную"]
];

$keyboard_work_recreation = [
    ["Активный отдых"],
    ["Отдых", "Работа"],
    ['Информация'],
    ["Назад"],
    ["\xF0\x9F\x8F\xA0 На главную"]
];

$keyboard_tarif = [
    ["Стандарт", "Расширенный"],
    ["Путешествие на авто"],
    ['Информация'],
    ["Назад"],
    ["\xF0\x9F\x8F\xA0 На главную"]
];

$keyboard_civil_bag_email = [
    ["\xF0\x9F\x93\x83 Да", "\xE2\x9D\x8C Нет"],
    ['Информация'],
    ["Назад"],
    ["\xF0\x9F\x8F\xA0 На главную"]
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
            $keyboard = false;

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
                    $keyboard = $keyboard_back;
                    break;
                }
                case "Date_to_world":{
                    $reply = $lang['all_world_text'] . $lang['dateto_text'];
                    UserEvent($chat_id, 'All_World');
                    $keyboard = $keyboard_back;
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

                    $keyboard = $keyboard_back;
                    break;
                }

                case "Work":
                case "Recreation":
                case "Active_Recreation":{
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
                    UserEvent($chat_id, 'Work');
                    $keyboard = $keyboard_back;
                    break;
                }

                case "Yes_order":{
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

                    if(OrderSelect($chat_id, 'civil') == "Да" && OrderSelect($chat_id, 'baggage') == "Да"){
                        $options = 'Гражданская ответственность, Страхование багажа.';
                    }elseif(OrderSelect($chat_id, 'civil') == "Да" && (OrderSelect($chat_id, 'baggage') == "Нет" || OrderSelect($chat_id, 'baggage') == "-")){
                        $options = 'Гражданская ответственность.';
                    }elseif(OrderSelect($chat_id, 'baggage') == "Да" && (OrderSelect($chat_id, 'civil') == "Нет" || OrderSelect($chat_id, 'baggage') == "-")){
                        $options = 'Страхование багажа.';
                    }else{
                        $options = 'Нет';
                    }

                    $array_str = [
                        '%world%' =>        OrderSelect($chat_id, 'world'),
                        '%date_to%' =>      OrderSelect($chat_id, 'date_to'),
                        '%date_back%' =>    OrderSelect($chat_id, 'date_back'),
                        '%days_total%' =>   DaysCount($chat_id),
                        '%world_total1%' => $world_total1,
                        '%world_total2%' => $world_total2,
                        '%options%' =>      $options,
                        '%price%' =>        round(OrderTotal($chat_id), 2)
                    ];

                    $reply =  strtr($lang['success_text'], $array_str);
                    UserEvent($chat_id, 'Success');
                    $keyboard = $keyboard_civil_bag_email;
                    break;
                }

                case "Email":{
                    $reply = $lang['email_text'];
                    UserEvent($chat_id, 'Phone');
                    $keyboard = $keyboard_back;
                    break;
                }
                case "Phone":{
                    $reply = $lang['phone_text'];
                    UserEvent($chat_id, 'Success');
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
            $keyboard = $keyboard_back;
            break;
        }

        case "Весь мир":{
            $reply = $lang['all_world_text'] . $lang['dateto_text'];
            UserEvent($chat_id, 'All_World');
            OrderEdit($chat_id, 'world', 'Весь мир');
            $keyboard = $keyboard_back;
            break;
        }
        //получаем дату а в формате xx.xx.xxxx
        case (preg_match_all('/^[0-3]{1}[0-9]{1}[.]{1}[0-1]{1}[0-9]{1}[.]{1}[1-2]{1}[0-9]{1}[0-9]{1}[0-9]{1}$/', $text) ? true : false):{

            switch (UserSelect($chat_id)){
                case "All_World":{
                    if(getDiffDate("Now", $text, 0)){
                        $reply = $lang['date_back'];
                        UserEvent($chat_id, 'Date_to_world');
                        OrderEdit($chat_id, 'date_to', $text);
                    }else{
                        $reply = $lang['error_date_to_text'];
                    }

                    $keyboard = $keyboard_back;
                    break;
                }
                case "All_Europe":{
                    if(getDiffDate("Now", $text, 0)){
                        $reply = $lang['date_back'];
                        UserEvent($chat_id, 'Date_to_europe');
                        OrderEdit($chat_id, 'date_to', $text);
                    }else{
                        $reply = $lang['error_date_to_text'];
                    }

                    $keyboard = $keyboard_back;
                    break;
                }

                case "Date_to_europe":
                case "Date_to_world":{

                    $arr = getDiffDateR(OrderSelect($chat_id, 'date_to'), $text, 3, $lang);

                    if($arr['return']){
                        $reply = $lang['work_recreation_text'];
                        UserEvent($chat_id, 'Date_back');
                        OrderEdit($chat_id, 'date_back', $text);
                        $keyboard = $keyboard_work_recreation;
                    }else{
                        $reply = $arr['answer'];
                        $keyboard = $keyboard_back;
                    }
                    break;
                }

                case "Work":
                case "Extended_tarif":
                case "Car_tarif":
                case "Not_baggage":
                case "Yes_baggage":{

                    $arr = getDiffYear($text, 'Now', 80, $lang);

                    if($arr['return']){
                        $reply = $lang['phone_text'];
                        UserEvent($chat_id, 'Success');
                        OrderEdit($chat_id, 'birthday', $text);
                        $keyboard = $keyboard_back_phone;
                    }else{
                        $reply = $arr['answer'];
                        $keyboard = $keyboard_back;
                    }

                    break;
                }
            }
            break;
        }
        //получаем емейл
        case (preg_match_all('/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/', $text) ? true : false):{
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

            if(OrderSelect($chat_id, 'civil') == "Да" && OrderSelect($chat_id, 'baggage') == "Да"){
                $options = 'Гражданская ответственность, Страхование багажа.';
            }elseif(OrderSelect($chat_id, 'civil') == "Да" && (OrderSelect($chat_id, 'baggage') == "Нет" || OrderSelect($chat_id, 'baggage') == "-")){
                $options = 'Гражданская ответственность.';
            }elseif(OrderSelect($chat_id, 'baggage') == "Да" && (OrderSelect($chat_id, 'civil') == "Нет" || OrderSelect($chat_id, 'baggage') == "-")){
                $options = 'Страхование багажа.';
            }else{
                $options = 'Нет';
            }

            $array_str = [
                '%world%' =>        OrderSelect($chat_id, 'world'),
                '%date_to%' =>      OrderSelect($chat_id, 'date_to'),
                '%date_back%' =>    OrderSelect($chat_id, 'date_back'),
                '%days_total%' =>   DaysCount($chat_id),
                '%world_total1%' => $world_total1,
                '%world_total2%' => $world_total2,
                '%options%' =>      $options,
                '%price%' =>        round(OrderTotal($chat_id), 2)
            ];
            $reply =  strtr($lang['success_text'], $array_str);
            UserEvent($chat_id, 'Email');
            OrderEdit($chat_id, 'email', $text);
            $keyboard = $keyboard_civil_bag_email;
            break;
        }

        //получаем номер телефона
        case (preg_match_all('/^\+380\d{3}\d{2}\d{2}\d{2}$/', $text) ? true : false):{

            $reply = $lang['email_text'];
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

        case "Активный отдых":{
            $reply = $lang['tarif_text'];
            UserEvent($chat_id, 'Active_Recreation');
            OrderEdit($chat_id, 'work_recreation', 'Активный отдых');
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

                case "Success":
                case "Email":{
                    $array_str = [
                        '%link%' => LinkGenFondy($chat_id)
                    ];
                    $reply =  strtr($lang['thank_text'], $array_str);
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

                case "Success":
                case "Email":{
                    $reply = $lang['start_text'];
                    UserEvent($chat_id, 'Null');
                    $keyboard = $keyboard_main;
                    break;
                }
            }
            break;
        }
        default:{
            switch (UserSelect($chat_id)){
                case "All_World":
                case "All_Europe":{
                    $reply = $lang['error_date_to_text'];
                    $keyboard = false;
                    break;
                }
                case "Date_to_europe":
                case "Date_to_world":{
                    $reply = $lang['error_date_back_text'];
                    $keyboard = false;
                    break;
                }
                case "Work":
                case "Extended_tarif":
                case "Car_tarif":
                case "Not_baggage":
                case "Yes_baggage":{
                    $reply = $lang['error_birthday_little_text'];
                    $keyboard = false;
                    break;
                }

                case "Phone":{
                    $reply = $lang['email_error_text'];
                    $keyboard = false;
                    break;
                }
                default:{
                    $reply = "По запросу <b>".$text."</b> ничего не найдено.";
                    $keyboard = false;
                    break;
                }

            }
            break;
        }
    }

    //отправка смс
    if($keyboard){
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
            'text' => $reply,
            'parse_mode'=> 'HTML'
        ]);
    }



}elseif($phone_number){

    $reply = $lang['email_text'];
    UserEvent($chat_id, 'Phone');
    OrderEdit($chat_id, 'phone', $phone_number);
    $keyboard = $keyboard_back;

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
<?php
/**
 * Created by PhpStorm.
 * User: yuv
 * Date: 22.03.2018
 * Time: 23:15
 */

require 'config.php';
require "excel.php";

/**
 * @param $data
 * @param int $lastInsertId
 * @return PDOStatement|string
 */
function dbQuery($data, $lastInsertId = 0){

    $dsn = "mysql:host=".DB_HOST.";dbname=".DB_TABLE;
    $dbo = new PDO( $dsn, DB_USER, DB_PASS);
    $dbo -> exec("SET NAMES utf8mb4");
    $dbo -> exec("SET CHARACTER SET utf8mb4_general_ci");
    $dbo -> exec("SET SESSION collation_connection = utf8mb4_general_ci");
    $result = $dbo->prepare( $data );
    $result->execute();

    if($lastInsertId){
        return $dbo->lastInsertId();
    }

    return $result;
}

/**
 * @param $user_id
 * @return mixed
 */
function UserSelect($user_id)
{
   $result =  dbQuery("SELECT * FROM `users` WHERE user_id = '".$user_id."'")->fetch( PDO::FETCH_ASSOC );

   return $result['last_event'];
}

/**
 * @param $user_id
 * @param $last_event
 */
function UserEvent($user_id, $last_event){

    if(dbQuery("SELECT * FROM `users` WHERE user_id = '".$user_id."'")->fetch( PDO::FETCH_COLUMN ) == NULL) {
        dbQuery("INSERT INTO `users` (`user_id`, `last_event`, `date`) VALUES ('" . $user_id . "', '" . $last_event . "','". time() ."')");
    }else {
        $sql = "UPDATE `users` SET";

        if($last_event){
            $sql .= "`last_event` = '".$last_event."', ";
        }
        $sql .= "`date` = '". time() ."'";
        $sql .= " WHERE `user_id` = '". $user_id ."'";

        dbQuery($sql);
    }
}

/**
 * @param $user_id
 * @param $key
 * @return mixed
 */
function OrderSelect($user_id, $key)
{
    $result =  dbQuery("SELECT * FROM `orders` WHERE user_chat_id = '".$user_id."'")->fetch( PDO::FETCH_ASSOC );

    return $result[$key];
}

/**
 * @param $user_id
 * @return mixed
 */
function OrderFull($user_id)
{
    $result =  dbQuery("SELECT * FROM `orders` WHERE user_chat_id = '".$user_id."'")->fetch( PDO::FETCH_ASSOC );

    return $result;
}

/**
 * @param $user_id
 * @param $key
 * @param $val
 */
function OrderEdit($user_id, $key, $val){

    if(dbQuery("SELECT * FROM `orders` WHERE user_chat_id = '".$user_id."'")->fetch( PDO::FETCH_COLUMN ) == NULL) {
        dbQuery("INSERT INTO `orders` (`user_chat_id`) VALUES ('" . $user_id . "')");
    }else {
        $sql = "UPDATE `orders` SET";

        if($key && $val){
            $sql .= "`" . $key . "` = '". $val. "', ";
        }
        $sql .= "`date` = '". time() ."'";
        $sql .= " WHERE `user_chat_id` = '". $user_id ."'";

        dbQuery($sql);
    }
}

/**
 * @param $user_id
 */
function DaysCount($user_id){

    $order = OrderFull($user_id);

    $datetime1 = new DateTime($order['date_to']);
    $datetime2 = new DateTime($order['date_back']);
    $interval = $datetime1->diff($datetime2);

    $days_count = $interval->format('%a');

    return $days_count;
}

/**
 * @param $user_id
 */
function OrderTotal($user_id){

    $order = OrderFull($user_id);

    $days_count = DaysCount($user_id);

    $datetime1 = new DateTime("Now");
    $datetime2 = new DateTime($order['bithday']);
    $interval = $datetime1->diff($datetime2);

    $user_years = $interval->format('%Y');

    if($order['world'] == "Вся Европа"){
        $sheetname = '30000';
    }elseif($order['world'] == "Весь мир"){
        $sheetname = '50000';
    }

    if($order['civil'] == "Да"){
        $civil = true;
    }elseif($order['civil'] == "Нет" || $order['civil'] == "-"){
        $civil = false;
    }

    $coefficient = getCofFromTableExcel($sheetname, $days_count, $civil);

    $order_total = $days_count * $coefficient;

    if($order['work_recreation'] == "Отдых"){
        $order_total = $order_total * 1.5;
    }

    if($order['tarif'] == "Расширенный"){
        $order_total = $order_total * 1.2;
    }elseif($order['tarif'] == "Путешествие на авто"){
        $order_total = $order_total * 1.6;
    }

    return $order_total;

}

$lang = array(
    "start_text" => "Добрый день! Это - бот, который умеет рассчитывать туристические страховые полисы, оставлять заявки на их приобретение, присылать их в нужный момент. Бот работает с информацией компании «Европейское туристическое страхование (ERV).",
    "information_text" => "Вывод текста",
    "rp_text" => "Пожалуйста, выберите из предложенных вариантов.",
    "all_europe_text" => "Вся Европа? ",
    "all_world_text" => "Весь мир? ",
    "dateto_text" => "Отлично! Пожалуйста, напишите дату начала путешествия в формате ДД.ММ.ГГГГ (например, 15.07.2018).",
    "date_back" => "Принято! Пожалуйста, напишите дату окончания путешествия в формате ДД.ММ.ГГГГ (например, 25.07.2018).",
    "work_recreation_text" => "Отдых или работа?",
    "tarif_text" => "Какой тарифный план Вам посчитать?",
    "civil_text" => "Добавить гражданскую ответственность?",
    "date_bith_text" => "Пожалуйста, укажите дату рождения застрахованного в формате ДД.ММ.ГГГГ (например, 25.07.1990).",
    "baggage_text" => "Добавить страхование багажа на 500€/$?",
    "success_text" => "Расчет выполнен! Территория страхования: %world%<br/>Период страхования: %date_to% - %date_back% (%days_total% д.)*<br/>*К выбранному Вами периоду страхования, будет автоматически добавлено 15 календарных дней, в соответствие с Решением Совета ЕС 2004/17/ EG об условиях медицинского страхования путешествующих лицСтраховая сумма %world_total1%€.<br/>Несчастный случай %world_total2%€.<br/>Доп. опции: ...<br/>Цена полиса: %price%грн",
    "email_text" => "Отлично! Пожалуйста, укажите свой E-mail и мы с Вами свяжемся для консультации и оформления полиса.",
    "phone_text" => "Отлично! Пожалуйста, укажите свой № телефона в формате +380ххххххххх.",
    "thank_text" => "Спасибо за Вашу заявку!",
);
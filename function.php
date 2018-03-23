<?php
require 'config.php';

function calc_zp($zp){
    $result = $zp - 0.18 * $zp - 0.015 * $zp;

    return $result;
}

function calc_oc1($a, $b){
    $result = round(($a * $b)/100, 2);

    return $result;
}

function calc_oc2($a, $b){
    $result = round(($a * $b)/100, 2);

    return $result;
}

function calc_oc3($a, $b){
    $result = round(($a / $b)*100, 2);

    return $result;
}

function calc_oc4($a, $b){
    if($b < $a){
        $result = round(($b * 100 / $a) - 100, 2);
    }else{
        $result = round(($a * 100 / $b) - 100, 2);
    }


    return $result;
}

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

function UserSelect($user_id)
{
   $result =  dbQuery("SELECT * FROM `users` WHERE user_id = '".$user_id."'")->fetch( PDO::FETCH_ASSOC );

   return $result['last_event'];
}

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
function OrderSelect($user_id, $key)
{
    $result =  dbQuery("SELECT * FROM `orders` WHERE user_chat_id = '".$user_id."'")->fetch( PDO::FETCH_ASSOC );

    return $result[$key];
}

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
    "success_text" => "Расчет выполнен!Желаете Оставить заявку на приобретение»?"
);
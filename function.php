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
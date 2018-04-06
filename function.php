<?php
/**
 * Created by PhpStorm.
 * User: yuv
 * Date: 22.03.2018
 * Time: 23:15
 */

require 'config.php';
require "excel.php";

require_once('Googl.class.php');

$lang = [
    "start_text" => "Добрый день! Это - бот, который умеет рассчитывать туристические страховые полисы, оставлять заявки на их приобретение, присылать их в нужный момент. Бот работает с информацией компании «Европейское туристическое страхование (ERV).",
    "information_text" => "Я согласен с тем, чтобы мои личные данные, которые будут введены в базу данных Amadeus Insurance Hosting, а также для хранения, используются и обрабатываются с целью предоставления запрошенных услуг. Я подтверждаю точность всей информации, введенной выше Я согласен с условиями страхования.",
    "rp_text" => "Пожалуйста, выберите, куда Вы хотите поехать.",
    "all_europe_text" => "Вся Европа? ",
    "all_world_text" => "Весь мир? ",
    "dateto_text" => "Отлично! Пожалуйста, напишите дату начала путешествия в формате <b>ДД.ММ.ГГГГ</b> (например, 15.07.2018).",
    "date_back" => "Принято! Пожалуйста, напишите дату окончания путешествия в формате <b>ДД.ММ.ГГГГ</b> (например, 25.07.2018).",
    "work_recreation_text" => "Цель Вашего путешествия: отдых или работа?",
    "tarif_text" => "Какой тарифный план Вам посчитать?",
    "civil_text" => "Добавить гражданскую ответственность?",
    "date_bith_text" => "Пожалуйста, укажите дату рождения застрахованного в формате <b>ДД.ММ.ГГГГ</b> (например, 25.07.1990).",
    "baggage_text" => "Добавить страхование багажа на 500€/$?",
    "success_text" => "Расчет выполнен!
        \nТерритория страхования: %world% 
        \nПериод страхования: %date_to% - %date_back% (%days_total% д.)%text_europe%
        \nСтраховая сумма %world_total1%€.
        \nНесчастный случай %world_total2%€.
        \nДоп. опции: %options%
        \nЦена полиса: %price%грн
        \nВам удобно связаться с нашим менеджером или сделать оформление полиса онлайн?",
    "email_text" => "Супер! Осталось всего ничего, 1 шаг и Вы на странице оплаты страховки. Введите, пожалуйста, свой email  для того,  что бы мы могли максимально быстро Вас индефицировать в случае потери коммуникации.",
    "phone_text" => "Отлично! Осталось всего 2 шага для оплаты страховки. Отправте, пожалуйста, свой мобильный номер телефона  для того,  что бы наш менеджер с Вами связался.",
    "thank_text" => "Спасибо за заказ!\n<a href='%link%'>Ссылка на оплату</a>",
    "error_date_to_text" => "Введите пожалуйста коректную дату, которая больше сегодняшнего дня в формате <b>ДД.ММ.ГГГГ</b>",
    "error_date_back_text" => "Введите коректную дату, которая больше даты начала путешествия хотя бы на 3 дня!!!",
    "error_birthday_little_text" => 'Введите, пожалуйста, коректную своего дату рождения в формате  <b>ДД.ММ.ГГГГ</b>.',
    "error_birthday_big_text" => 'Приносим свои извинения, но наша компания не страхует лиц которым больше чем 80 лет. Если Вы ошиблись - введите пожалуйста дату еще раз в формате <b>ДД.ММ.ГГГГ</b>',
    "email_error_text" => 'Введите пожалуйста коректный электронный адресс в формате  <b>xxxx@xxx.xx</b>',
    "error_date_365_text" => 'Приносим свои извинения, но наша компания не страхует на период больше года.',
    "type_text" => 'Типы поездки:',
    "europe_order_text" => "*\n*К выбранному Вами периоду страхования, будет автоматически добавлено 15 календарных дней, в соответствие с Решением Совета ЕС 2004/17/ EG об условиях медицинского страхования путешествующих лиц",
    "manager_text" => 'Вам удобно связаться с нашим менеджером или сделать оформление полиса онлайн?',
    "enter_name_text" => "Для оформления полиса онлайн укажите свои данные.\nПожалуйста, укажите имя и фамилию застрахованного в полном соответствии с документом загран. паспортом (латинецей). Пример: <b>IVAN IVANOV</b>",
    "enter_pass_text" => "Укажите серию и номер паспорта(латинские буквы). Пример: ER 256987",
    "enter_inn_text" => "Ваш ИНН",
    "enter_adress_text" => "Укажите Ваш адрес(латиненецей)",
    "error_name_text" => "Введите имя и фамилию(латиница)\nПример: <b>IVAN IVANOV</b>",
    "error_pass_text" => "Укажите серию и номер паспорта(латинские буквы). Пример: <b>ER 256987</b>",
    "error_inn_text" => "Введите правельный ИНН <b>(10 цифр)</b>",
    "error_adress_text" => "Укажите Ваш адрес(латиненецей). Пример <b>KIEV</b>"

];

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
 * @return mixed
 */
function OrderFullUser()
{
    $result =  dbQuery("SELECT * FROM `orders`")->fetchAll( PDO::FETCH_ASSOC );

    return $result;
}

/**
 * @param $user_id
 * @param $key
 * @param $val
 */
function OrderEdit($user_id, $key, $val){

    if(dbQuery("SELECT * FROM `orders` WHERE user_chat_id = '".$user_id."'")->fetch( PDO::FETCH_COLUMN ) == NULL) {
        dbQuery("INSERT INTO `orders` (`user_chat_id`,`".$key."`) VALUES ('" . $user_id . "','".$val."')");
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
 * @return PDOStatement|string
 */
function OrderDeleteUser($id)
{
    $result =  dbQuery("DELETE FROM `orders` WHERE  `id` = '".$id."'");

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
 * @param $key
 * @return mixed
 */
function getApiNBU($key)
{
    if($key == 'USD'){
        $val = json_decode(file_get_contents('https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?valcode=USD&json'), TRUE);
    }elseif($key == 'EUR'){
        $val = json_decode(file_get_contents('https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?valcode=EUR&json'), TRUE);
    }
    return $val['0']['rate'];
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

    $sheetname = '';
    $civil = false;
    $valut = 'USD';

    if($order['world'] == "Вся Европа"){
        $sheetname = '30000';
        $valut = 'EUR';
    }elseif($order['world'] == "Весь мир"){
        $sheetname = '50000';
        $valut = 'USD';
    }

    if($order['civil'] == "Да"){
        $civil = true;
    }elseif($order['civil'] == "Нет" || $order['civil'] == "-"){
        $civil = false;
    }

    $coefficient = getCofFromTableExcel($sheetname, $days_count, $civil);

    $order_total = $coefficient;

    if($order['work_recreation'] == "Активный отдых"){
        $order_total = $order_total * 1.5;
    }

    if($order['tarif'] == "Расширенный"){
        $order_total = $order_total * 1.2;
    }elseif($order['tarif'] == "Путешествие на авто"){
        $order_total = $order_total * 1.6;
    }

    if($user_years >= 71 && $user_years < 75){
        $order_total = $order_total * 2;
    }elseif($user_years >= 75 && $user_years <= 80){
        $order_total = $order_total * 3;
    }

    if($order['baggage'] == "Да"){
        $order_total = $order_total + getCofBagFromTableExcel($days_count, $order_total);
    }

    if($coff = getSettings('coff')){
        $order_total = $order_total * $coff['value'];
    }

    $order_total = $order_total * getApiNBU($valut);

    OrderEdit($user_id, 'total_price', $order_total);

    return $order_total;

}

/**
 * @param $date_to
 * @param $date_back
 * @param $diff
 * @return bool
 */
function getDiffDate($date_to, $date_back, $diff, $lang)
{
    if($date_to == "Now") {
        $date_to = date('d.m.Y');
    }

    if($date_back == "Now") {
        $date_back = date('d.m.Y');
    }

    $date1 = strtotime($date_to);
    $date2 = strtotime($date_back);

    if(!$date1 || !$date2){
        return false;
    }

    if($date2 >= $date1){
        $datetime1 = new DateTime($date_to);
        $datetime2 = new DateTime($date_back);
        $interval = $datetime1->diff($datetime2);

        $diff_years = $interval->format('%a');

        if($diff_years >= 365){
            $arr['return'] = false;
            $arr['answer'] = $lang['error_birthday_little_text'];

            return $arr;
        }

        if($diff_years >= $diff){
            return true;

        }

        return false;
    }
    return false;
}

/**
 * @param $date_to
 * @param $date_back
 * @param $diff
 * @param $lang
 * @return bool
 */
function getDiffDateR($date_to, $date_back, $diff, $lang)
{
    if($date_to == "Now") {
        $date_to = date('d.m.Y');
    }

    if($date_back == "Now") {
        $date_back = date('d.m.Y');
    }

    $date1 = strtotime($date_to);
    $date2 = strtotime($date_back);

    if(!$date1 || !$date2){
        $arr['return'] = false;
        $arr['answer'] = $lang['error_date_to_text'];

        return $arr;
    }

    if($date2 >= $date1){
        $datetime1 = new DateTime($date_to);
        $datetime2 = new DateTime($date_back);
        $interval = $datetime1->diff($datetime2);

        $diff_years = $interval->format('%a');

        if($diff_years > 365){
            $arr['return'] = false;
            $arr['answer'] = $lang['error_date_365_text'];

            return $arr;
        }

        if($diff_years >= $diff){
            $arr['return'] = true;
            return $arr;

        }

        $arr['return'] = false;
        $arr['answer'] = $lang['error_date_to_text'];

        return $arr;
    }
    $arr['return'] = false;
    $arr['answer'] = $lang['error_date_to_text'];

    return $arr;
}

/**
 * @param $date_to
 * @param $date_back
 * @param $diff
 * @return string
 */
function getDiffYear($date_to, $date_back, $diff, $lang = array())
{
    if($date_to == "Now") {
        $date_to = date('d.m.Y');
    }

    if($date_back == "Now") {
        $date_back = date('d.m.Y');
    }

    $date1 = strtotime($date_to);
    $date2 = strtotime($date_back);

    if(!$date1 || !$date2){

        $arr['return'] = false;
        $arr['answer'] = $lang['error_birthday_little_text'];

        return $arr;
    }

    if($date2 >= $date1){
        $datetime1 = new DateTime($date_to);
        $datetime2 = new DateTime($date_back);
        $interval = $datetime1->diff($datetime2);

        $diff_years = $interval->format('%Y');

        if($diff_years <= $diff){
            $arr['return'] = true;

            return $arr;
        }

        $arr['return'] = false;
        $arr['answer'] = $lang['error_birthday_big_text'];

        return $arr;
    }
    $arr['return'] = false;
    $arr['answer'] = $lang['error_birthday_little_text'];

    return $arr;
}

/**
 * @param $key
 * @return mixed
 */
function getSettings($key){

    $result =  dbQuery("SELECT * FROM `settings` WHERE `key` = '".$key."'")->fetch( PDO::FETCH_ASSOC );

    return $result;
}

/**
 * @param $key
 * @param $value
 */
function setSettings($key, $value){

    if(dbQuery("SELECT * FROM `settings` WHERE `key` = '".$key."'")->fetch( PDO::FETCH_COLUMN ) == NULL) {
        dbQuery("INSERT INTO `settings` (`key`, `value`) VALUES ('" . $key . "', '" . $value . "')");
    }else {
        $sql = "UPDATE `settings` SET";

        if($value){
            $sql .= "`value` = '".$value."'";
        }
        $sql .= " WHERE `key` = '". $key ."'";

        dbQuery($sql);
    }
}

/**
 * @param $merchant_id
 * @param $password
 * @param array $params
 * @return string
 */
function getSignature( $merchant_id , $password , $params = array() ){
    $params['merchant_id'] = $merchant_id;
    $params = array_filter($params,'strlen');
    ksort($params);
    $params = array_values($params);
    array_unshift( $params , $password );
    $params = join('|',$params);
    return(sha1($params));
}

/**
 * @param $user_id
 * @return bool|string
 */
function LinkGenFondy($user_id)
{
    $googl = new Googl(GOOGL_API);

    $merchant_id = getSettings('merchant_id');
    $merchant_id = $merchant_id['value'];

    $password = getSettings('key_payment');
    $password = $password['value'];

    $total_price = round(OrderSelect($user_id, 'total_price'), 2);
    $order_id = "ID".OrderSelect($user_id, 'id');

    $sender_email = OrderSelect($user_id, 'email');

    $params = [
        'order_id' => $order_id,
        'merchant_id' => $merchant_id,
        'currency' => 'UAH',
        'order_desc' => 'Оплата страхового полиса',
        'amount' => $total_price
    ];

    $signature = getSignature($merchant_id, $password, $params);

    $link = 'https://api.fondy.eu/api/checkout?button={"merchant_id":"'.$merchant_id.'","signature":"'.$signature.'","sender_email":"'.$sender_email.'","currency":"UAH","fields":[{"name":"descr","value":"Оплата страхового полиса","label":"Назначение платежа","valid":"","readonly":true}],"params":{"order_id":"'.$order_id.'"},"amount":"'.$total_price.'","amount_readonly":true}';

    //return $googl->shorten($link);
    return $link;
}

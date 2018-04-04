<?php
require_once "vendor/autoload.php";
require 'config.php';

use Telegram\Bot\Api;

$telegram = new Api(BOT_API_KEY);
$response = $telegram->setWebhook(['url' => HANDLERURL]);

?>
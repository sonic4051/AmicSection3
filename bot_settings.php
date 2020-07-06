<?php
$accessToken = "i01ExIyKX9/iOZ/z+sZVY/yxfx3QIGuxSAKzNM29JmBlk2ZK1aO9gLQt9uf3kJl5MpHwv0BqWkV4/55N4BSjxs9NaRLM+6yWLplwWZTTwylAJxy9djgppCsbYQSJeRvs7hWU5hCov1JxZkx1ZXIVVwdB04t89/1O/w1cDnyilFU=";//copy Channel access token ตอนที่ตั้งค่ามาใส่
$channelSecret = "1e3afa9e6459fba7b178fa4e940762c4";
//file_put_contents('log.txt', file_get_contents('php://input') . PHP_EOL, FILE_APPEND);
$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($accessToken);
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $channelSecret]);
$content = file_get_contents('php://input');
$arrayJson = json_decode($content, true);
$SaveTopic="";
$arrayHeader = array();
$arrayHeader[] = "Content-Type: application/json";
$arrayHeader[] = "Authorization: Bearer {$accessToken}";
date_default_timezone_set("Asia/Bangkok");
$Ndate=date('Y/m/d');
$Ntime=date('H:i:s');
?>

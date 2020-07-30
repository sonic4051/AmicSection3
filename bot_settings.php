<?php
$accessToken = "hiCo5SaKOlgH0Lxjjl3VvpcxFijj2B00ouHm24f62sQ+SrdjTJOgS5AHX8v88fuZJuXHExYi99mAAddQ3qalql3Sw49OdaVxxveCw3voJtTA+3oxUEp22jIUS2qpR6jQTS3N2HZVVUfne5F+ZtwJwAdB04t89/1O/w1cDnyilFU=";//copy Channel access token ตอนที่ตั้งค่ามาใส่
$channelSecret = "5a0255679b5da885f15fd883f892f160";
//file_put_contents('log.txt', file_get_contents('php://input') . PHP_EOL, FILE_APPEND);
$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($accessToken);
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $channelSecret]);
$content = file_get_contents('php://input');
$arrayJson = json_decode($content, true);
$SaveTopic="";
$arrayHeader = array();
$webURL = "https://amic3.herokuapp.com/";
$web-Storage-URL = "https://amic-bot-storage.s3-ap-southeast-1.amazonaws.com/";
$arrayHeader[] = "Content-Type: application/json";
$arrayHeader[] = "Authorization: Bearer {$accessToken}";
date_default_timezone_set("Asia/Bangkok");
$Ndate=date('Y/m/d');
$Ntime=date('H:i:s');
?>

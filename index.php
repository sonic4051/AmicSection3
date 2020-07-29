<?php 
################################################################################
########################## AMIC CHATBOT BY SONIC4051############################
################################################################################
namespace LINE\LINEBot;
header('Content-Type: text/html; charset=utf-8');
require_once 'vendor/autoload.php';
require_once('connect.php');
require_once('bot_settings.php');
use \Rollbar\Rollbar;
use \Rollbar\Payload\Level;
################################################################################
########################### namespace marcuscode ###############################
################################################################################
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
//use LINE\LINEBot\Event;
//use LINE\LINEBot\Event\BaseEvent;
//use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use LINE\LINEBot\MessageBuilder\AudioMessageBuilder;
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use LINE\LINEBot\ImagemapActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder ;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\DatetimePickerTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder;
################################################################################
############################ รับข้อความจากผู้ใช้ ###################################
################################################################################
$PostbackData = $arrayJson['events'][0]['postback']['data'];
$Message = $arrayJson['events'][0]['message']['text'];
$MessageID = $arrayJson['events'][0]['message']['id'];
$UserID  = $arrayJson['events'][0]['source']['userId'];
$MessageSendType = $arrayJson['events'][0]['source']['type'];
if ($MessageSendType=="group") {
  $GroupID = $arrayJson['events'][0]['source']['groupId'];
}
$replyToken = $arrayJson['events'][0]['replyToken'];
if((isset($PostbackData))) {
  $typeMessage = $arrayJson['events'][0]['type'];
} else {
  $typeMessage = $arrayJson['events'][0]['message']['type'];
}
    switch ($typeMessage){
        case 'text':
            switch ($Message) {
                case "t":
					$url="https://ift.tt/393BoPZ";
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_HEADER, true);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$a = curl_exec($ch); // $a will contain all headers
					$url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); // This is what you need, it will return you the last effective URL
                    $textReplyMessage = $url;
                    $replyData = new TextMessageBuilder($textReplyMessage);
                    break;
                case "i":
                    $picFullSize = 'https://amic-bot-storage.s3-ap-southeast-1.amazonaws.com/test.PNG';
                    $picThumbnail = 'https://amic-bot-storage.s3-ap-southeast-1.amazonaws.com/test.PNG';
                    $replyData = new ImageMessageBuilder($picFullSize,$picThumbnail);
                    break;
        default:
            $textReplyMessage = json_encode($events);
            $replyData = new TextMessageBuilder($textReplyMessage);         
            break;  
    }
}
//l ส่วนของคำสั่งตอบกลับข้อความ
$response = $bot->replyMessage($replyToken,$replyData);
?>
<?php
################################################################################
########################## AMIC CHATBOT BY SONIC4051############################
################################################################################
namespace LINE\LINEBot;
header('Content-Type: text/html; charset=utf-8');
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
################################################################################
##################### แยกข้อความเพื่อเตรียมบันทึกลงฐานข้อมูล ##########################
################################################################################
$Tags = explode("\n", $Message); //แยกกรณีบันทึกข่าวสาร
$Command = explode(" ", $Message); //แยกกรณีมีคำสั่งให้สืบค้นขอมูล
foreach($Tags as $key => $value) {
   if ($key>=2) {
     $AllContent=$AllContent.$value."\n";
   }
}
################################################################################
####################### ตรวจสอบสิทธิ์การใช้งานของ user #############################
################################################################################
$sql = "SELECT * FROM userid WHERE userlinekey='$UserID'";
$result = $conn->query($sql);
if($result->num_rows > 0) {
   $Passport = "true";
   while($row = $result->fetch_assoc()) {
     if ($row["userlinekey"]==$UserID) {
        $Reporter = $row["first_name"];
        $ReporterKey = $row["userlinekey"];
     }
   }
 } else {
  $Passport = "false";
 }
################################################################################
################################### เก็บ Log file################################
################################################################################
$strFileName = "log.txt";
$objFopen = fopen($strFileName, 'a');
if (isset($Reporter)) {
  if (isset($GroupID)) {
      $strText1 = $Ndate." , ".$Ntime." || ".$Reporter." [คุยในกลุ่ม] >>> ".$Message."\n";
  }else {
      $strText1 = $Ndate." , ".$Ntime." || ".$Reporter." [คุยส่วนตัว] >>> ".$Message."\n";
  }
}else {
  if (isset($GroupID)) {
      $strText1 = $Ndate." , ".$Ntime." || ไม่ได้ลงทะเบียน(".$UserID.") [คุยในกลุ่ม] >>> ".$Message."\n";
  }else {
      $strText1 = $Ndate." , ".$Ntime." || ไม่ได้ลงทะเบียน(".$UserID.") [คุยส่วนตัว] >>> ".$Message."\n";
  }
}
fwrite($objFopen, $strText1);
################################################################################
################################### เริ่มโปรแกรม ##################################
################################################################################
if($Passport=="true") {
    switch ($typeMessage) {
      case 'text':
                  if ($Tags[0]=="เบ็ดเตล็ด" or  $Tags[0]=="แรงงานต่างด้าว" or $Tags[0]=="อาชญากรรม"
                  or $Tags[0]=="การเมือง" or $Tags[0]=="ยาเสพติด" or $Tags[0]=="อาวุธสงคราม"
                  or $Tags[0]=="ศาสนา" or $Tags[0]=="ชนกลุ่มน้อยในประเทศ" or $Tags[0]=="กำลังฝ่ายตรงข้าม") {
                       if (isset($Tags[1]) && isset($Tags[2])) {
                          $sql="INSERT INTO news (NewsHadline, NewsContent, NewsDate, NewsTime, NewsType, NewsReporter)
                          VALUES ('$Tags[1]','$AllContent','$Ndate','$Ntime','$Tags[0]','$UserID')";
                          if ($conn->query($sql) === TRUE) {
                              $ReplyData = "ข่าว [".$Tags[1]."] \nผู้ส่ง ".$Reporter."ฯ \nบันทึกลงฐานข้อมูลเรียบร้อย \n*** สามารถเพิ่มรูปของข่าวได้ 4 รูป (กรุณาส่งทีละรูป)";
                              $textMessageBuilder = new TextMessageBuilder($ReplyData);
                              break;
                          } else {
                              $ReplyData= "Error: " . $conn->error;
                              $textMessageBuilder = new TextMessageBuilder($ReplyData);
                              break;
                          }
                       } else {
                         $ReplyData= "ไม่บันทึก :: รูปแบบข้อมูลไม่ถูกต้อง";
                         $textMessageBuilder = new TextMessageBuilder($ReplyData);
                         break;
                       }
                       $conn->close();
                  }
                  switch ($Command[0]) { // คำสั่งต่างๆ เขียนที่นี่
                     case "ลงทะเบียน" :
                                      $ReplyData= $Reporter." ไอดีนี้ลงทะเบียนแล้ว";
                                      $textMessageBuilder = new TextMessageBuilder($ReplyData);
                                      break;
                     case "อ่านข่าว" :
                                      if (isset($Command[1]) && is_numeric($Command[1])) {
                                        $sql = "SELECT * FROM news WHERE News_id = '$Command[1]'";
                                        $result = $conn->query($sql);
                                        if ($result->num_rows > 0) {
                                          while($row = $result->fetch_assoc()) {
                                            $NewContent = $row["NewsContent"];// เนื้อหาข่าว
                                            $NewHeader = $row["NewsHadline"];
                                            $Newdate =$row["NewsDate"]." , ".$row["NewsTime"];
                                            $IDReporter = $row["NewsReporter"];
                                            $LinkNews = $webURL."readnew.php?newsid=".$row["News_id"];
                                            $piccount = $row["NewsPictureCount"];
                                            $pic[] = $row["NewsPicture1"];
                                            $pic[] = $row["NewsPicture2"];
                                            $pic[] = $row["NewsPicture3"];
                                            $pic[] = $row["NewsPicture4"];
                                            $sql = "SELECT * FROM userid WHERE userlinekey='$IDReporter'";
                                            $result = $conn->query($sql);
                                            if($result->num_rows > 0) {
                                               while($row = $result->fetch_assoc()) {
                                                 if ($row["userlinekey"]==$IDReporter) {
                                                    $NewReporter = $row["first_name"];
                                                 }
                                               }
                                            }
                                         }
                                        $arr_replyData = array();
                                        $ReplyData1 ="                 [ข่าวระหัสที่ ".$Command[1]."]\n----------------------------------------------------\n"."[เรื่อง] :: ".$NewHeader."\n[โดย] :: ".$NewReporter."ฯ".
                                        "\n[รายงาน] :: ".$Newdate."\n----------------------------------------------------\n - ".$NewContent;
                                        $countStr = utf8_strlen($ReplyData1);
                                        if ($countStr>1800) {
                                          #################################################################
                                          $strFileName = "floderfile/".$Command[1].".txt";
                                          $objFopen = fopen($strFileName, 'w');
                                          $strText1 = $Command[1];
                                          fwrite($objFopen, $strText1);
                                          ##################################################################
                                          $cutstr = mb_strimwidth($ReplyData1, 0,1500, "...", "UTF-8"); //ตัดคำให้เหลือ 1800
                                          $EditContent = $cutstr."\n (".$LinkNews.")";
                                          $ReplyData = $EditContent;
                                        } else {
                                            $ReplyData = $ReplyData1;
                                        }
                                        //$cutstr11 = mb_strimwidth($NewContent, 0,1500, "...", "UTF-8");
                                        $arr_replyData[] = new TextMessageBuilder($ReplyData);
                                        //แสดงรูป
                                        for ($i = 0; $i < $piccount; $i++) {
                                            $url = $web_Storage_URL.$pic[$i];
                                            $arr_replyData[] = new ImageMessageBuilder($url,$url);
                                        }
                                        $multiMessage    = new MultiMessageBuilder;
                                        foreach ($arr_replyData as $arr_Reply){
                                               $multiMessage->add($arr_Reply);
                                        }
                                       $textMessageBuilder = $multiMessage;
                                       break;
                                       } else {
                                       $ReplyData=" ตรวจสอบไม่ได้";
                                       $textMessageBuilder = new TextMessageBuilder($ReplyData);
                                       break;
                                     }
                                    $conn->close();
                                   }
                     case "เมนูคำสั่ง" :
                                    $sql="UPDATE LastChat SET LastKey='$UserID', LastGroup='', DateStrat='', DateEnd=''
                                    WHERE LastID='1'";
                                    $result2 = $conn->query($sql); //โฟกัสที่คนสั่งงาน
                                    $actionBuilder = array(
                                    new PostbackTemplateActionBuilder(
                                    'สถิติการรายงานข่าวสาร', http_build_query(array(
                                    'action'=>'staticNaws',
                                    ))),
                                    new PostbackTemplateActionBuilder(
                                    'ค้นหา', http_build_query(array(
                                    'action'=>'findNews',
                                    ))),
                                    new PostbackTemplateActionBuilder(
                                    'อ่านข่าว', http_build_query(array(
                                    'action'=>'readNews',
                                    ))),
                                    new PostbackTemplateActionBuilder(
                                    'ข้อมูลเครือข่ายเป้าหมาย', http_build_query(array(
                                    'action'=>'posernal',
                                    ))),
                                    );
                                    //$imageUrl = $webURL.'picture/amic.jpg';
									$imageUrl = 'https://amic-bot-storage.s3-ap-southeast-1.amazonaws.com/picture/amic.jpg';
                                    $textMessageBuilder = new TemplateMessageBuilder('รับคำสั่ง',
                                    new ButtonTemplateBuilder(
                                      $Reporter.'ฯ ต้องการให้ช่วยอะไรครับ', // กำหนดหัวเรื่อง
                                      'โปรดเลือกคำสั่ง โดยการกดที่เมนู', // กำหนดรายละเอียด
                                      $imageUrl, // กำหนด url รุปภาพ
                                      $actionBuilder ));
                                  $conn->close();
                                  break;
                     case "ค้นหา" :
                                  $sqlsr = "SELECT * FROM lastchat WHERE LastID='1'";
                                  $resultsr = $conn->query($sqlsr);
                                  if ($resultsr->num_rows > 0) {
                                     while($rowsr = $resultsr->fetch_assoc()) {
                                       $commandPoint = $rowsr["LastGroup"];
                                       $strDate = $rowsr["DateStrat"];
                                       $stpDate = $rowsr["DateEnd"];
                                     }
                                   }
                                  switch ($commandPoint) {
                                    case 'ค้นหาข่าวสาร':
                                                      if (isset($Command[3]) && $Command[3]!='') {
                                                        $srWord = 3;
                                                      } elseif (isset($Command[2]) && $Command[2]!='') {
                                                        $srWord = 2;
                                                      } elseif (isset($Command[1]) && $Command[1]!='') {
                                                        $srWord = 1;
                                                      }
                                                      if ((isset($Command[3]) && $Command[3]=='')|| (isset($Command[2]) && $Command[2]=='') || (isset($Command[1]) && $Command[1]=='')) {
                                                        $ReplyData = "ไม่สามารถค้นหาได้\nเนื่องจากรูปแบบการกรอกข้อมูลผิด\nกรุณาตรวจสอบเครื่องหมายวรรคตอน\n# หากต้องการกำหนดห้วงเวลาการค้นหาใหม่ให้พิมพ์ เมนูคำสั่ง";
                                                        $srWord = 0;
                                                      }
                                                      if ($srWord==1) {
                                                        $ReplyData="-----------------------------------------------------------\n";
                                                        $ReplyData=$ReplyData."                      ผลการค้นหา\n";
                                                        $ReplyData=$ReplyData."                    คำว่า ".$Command[1]."\n";
                                                        $ReplyData=$ReplyData."    ห้วง ".$strDate."  ถึง   ".$stpDate."\n";
                                                      }
                                                      if ($srWord==2) {
                                                        $ReplyData="-----------------------------------------------------------\n";
                                                        $ReplyData=$ReplyData."                      ผลการค้นหา\n";
                                                        $ReplyData=$ReplyData."         คำว่า ".$Command[1]." และ ".$Command[2]."\n";
                                                        $ReplyData=$ReplyData."    ห้วง ".$strDate."  ถึง   ".$stpDate."\n";
                                                      }
                                                      if ($srWord==3) {
                                                        $ReplyData="-----------------------------------------------------------\n";
                                                        $ReplyData=$ReplyData."                      ผลการค้นหา\n";
                                                        $ReplyData=$ReplyData."  คำว่า ".$Command[1]." และ ".$Command[2]." และ ".$Command[3]."\n";
                                                        $ReplyData=$ReplyData."    ห้วง ".$strDate."  ถึง   ".$stpDate."\n";
                                                      }
                                                      $sqlSR = "SELECT * FROM news WHERE NewsDate BETWEEN '$strDate' AND '$stpDate' ORDER BY News_id DESC";
                                                      $resultSR = $conn->query($sqlSR);
                                                      if ($resultSR->num_rows > 0) {
                                                         while($rowSR = $resultSR->fetch_assoc()) {
                                                          if ($srWord==1) {
                                                           if (strpos($rowSR["NewsContent"],$Command[1])!==false) {
                                                             $countidSR = $countidSR+1;
                                                             $ContentData=$ContentData.$rowSR["NewsDate"]." [".$rowSR["News_id"]."] => ".$rowSR["NewsHadline"]."\n";
                                                           }
                                                         } elseif ($srWord==2) {
                                                          if (strpos($rowSR["NewsContent"],$Command[1])!==false AND strpos($rowSR["NewsContent"],$Command[2])!==false) {
                                                            $countidSR = $countidSR+1;
                                                            $ContentData=$ContentData.$rowSR["News_id"]." => ".$rowSR["NewsHadline"]."[".$rowSR["NewsDate"]."]"."\n";
                                                          }
                                                        } elseif ($srWord==3) {
                                                         if (strpos($rowSR["NewsContent"],$Command[1])!==false AND strpos($rowSR["NewsContent"],$Command[2])!==false AND strpos($rowSR["NewsContent"],$Command[3])!==false) {
                                                           $countidSR = $countidSR+1;
                                                           $ContentData=$ContentData.$rowSR["News_id"]." => ".$rowSR["NewsHadline"]."[".$rowSR["NewsDate"]."]"."\n";
                                                         }
                                                       }
                                                          }
                                                        }
                                                      if ($countidSR>0) {
                                                      $ReplyData=$ReplyData."                  พบทั้งหมด ".$countidSR." ข่าว\n";
                                                      $ReplyData=$ReplyData."-----------------------------------------------------------\n";
                                                      $ReplyData=$ReplyData."   วันที่          IDข่าว          ชื่อเรื่อง\n";
                                                      $ReplyData=$ReplyData."-----------------------------------------------------------\n";
                                                      $ReplyData=$ReplyData.$ContentData;
                                                      $countSR = utf8_strlen($ReplyData);
                                                      }
                                                      if ($srWord==1) {
                                                        $LinkNews = $webURL."readnewsheader.php?word1=".$Command[1]."&id=".$countidSR."&strDate=".$strDate."&stpDate=".$stpDate;
                                                      } elseif ($srWord==2) {
                                                        $LinkNews = $webURL."readnewsheader.php?word1=".$Command[1]."&word2=".$Command[2]."&id=".$countidSR."&strDate=".$strDate."&stpDate=".$stpDate;
                                                      } elseif ($srWord==3) {
                                                        $LinkNews = $webURL."readnewsheader.php?word1=".$Command[1]."&word2=".$Command[2]."&word3=".$Command[3]."&id=".$countidSR."&strDate=".$strDate."&stpDate=".$stpDate;
                                                      }
                                                      if ($countSR>1800) {
                                                        #################################################################
                                                        $strFileName = "floderfile/".$countidSR.".txt";
                                                        $objFopen = fopen($strFileName, 'w');
                                                        $strText1 = $Command[1];
                                                        fwrite($objFopen, $strText1);
                                                        #################################################################
                                                        $cutstr = mb_strimwidth($ReplyData, 0,1800, "...", "UTF-8"); //ตัดคำให้เหลือ 1800
                                                        $EditContent = $cutstr."\n( ข้อมูลเพิ่มเติม ==> ".$LinkNews." )";
                                                        $ReplyData = $EditContent;
                                                      }
                                                      $conn->close();
                                                      break;
                                    default:
                                                      // code...
                                                      break;
                                  }//สิ้นสุดสวิช
                                  $textMessageBuilder = new TextMessageBuilder($ReplyData);
                                  $conn->close();
                                  break;
                     case "ค้นหาผลซักถาม" :
                                      if (isset($Command[1]) and $Command[1]!='') {
                                          $ReplyData="-----------------------------------------------------------\n";
                                          $ReplyData=$ReplyData."                 ค้นหาผลการซักถาม\n";
                                          $ReplyData=$ReplyData."                    คำว่า ".$Command[1]."\n";
                                          $countidSR = 0;
                                          $sqlSR = "SELECT * FROM filedata";
                                          $resultSR = $conn->query($sqlSR);
                                          if ($resultSR->num_rows > 0) {
                                          while($rowSR = $resultSR->fetch_assoc()) {
                                                if ((strpos($rowSR["fileName"],$Command[1])!==false) && $rowSR["filegroup"]=="file1") {
                                                    $countidSR = $countidSR+1;
                                                    $ContentData=$ContentData.$rowSR["fileId"]." => ".$rowSR["fileName"]."\n";
                                                    }
                                                }
                                              }
                                          if ($countidSR>0) {
                                              $ReplyData=$ReplyData."                  พบทั้งหมด ".$countidSR." ฉบับ\n";
                                              $ReplyData=$ReplyData."-----------------------------------------------------------\n";
                                              $ReplyData=$ReplyData."   ID                       ผลซักถาม\n";
                                              $ReplyData=$ReplyData.$ContentData;
                                              $countSR = utf8_strlen($ReplyData);
                                              }else {
                                                $ReplyData=$ReplyData."                  พบทั้งหมด ".$countidSR." ฉบับ\n";
                                                $ReplyData=$ReplyData."-----------------------------------------------------------\n";
                                              }
                                          $LinkNews = $webURL."readfileheader.php?word=".$Command[1]."&type=1&count=".$countidSR;
                                          if ($countSR>1800) {
                                             #################################################################
                                             $strFileName = "floderfile/file".$countidSR.".txt";
                                             $objFopen = fopen($strFileName, 'w');
                                             $strText1 = $Command[1];
                                             fwrite($objFopen, $strText1);
                                             #################################################################
                                             $cutstr = mb_strimwidth($ReplyData, 0,1800, "...", "UTF-8"); //ตัดคำให้เหลือ 1800
                                             $EditContent = $cutstr."\n( ข้อมูลเพิ่มเติม ==> ".$LinkNews." )";
                                             $ReplyData = $EditContent;
                                           }} else {
                                             $ReplyData = "รูปแบบการกรอกข้อมูลไม่ถูกต้อง";
                                           }
                                             $textMessageBuilder = new TextMessageBuilder($ReplyData);
                                             $conn->close();
                                             break;
                     case "ค้นหาประวัติบุคคล" :
                                     if (isset($Command[1]) and $Command[1]!='') {
                                         $ReplyData="-----------------------------------------------------------\n";
                                         $ReplyData=$ReplyData."                  ค้นหาประวัติบุคคล\n";
                                         $ReplyData=$ReplyData."                    คำว่า ".$Command[1]."\n";
                                         $countidSR = 0;
                                         $sqlSR = "SELECT * FROM filedata";
                                         $resultSR = $conn->query($sqlSR);
                                         if ($resultSR->num_rows > 0) {
                                         while($rowSR = $resultSR->fetch_assoc()) {
                                               if ((strpos($rowSR["fileName"],$Command[1])!==false) && $rowSR["filegroup"]=="file2") {
                                                   $countidSR = $countidSR+1;
                                                   $ContentData=$ContentData.$rowSR["fileId"]." => ".$rowSR["fileName"]."\n";
                                                   }
                                               }
                                             }
                                         if ($countidSR>0) {
                                             $ReplyData=$ReplyData."                  พบทั้งหมด ".$countidSR." ฉบับ\n";
                                             $ReplyData=$ReplyData."-----------------------------------------------------------\n";
                                             $ReplyData=$ReplyData."   ID                       ประวัติบุคคล\n";
                                             $ReplyData=$ReplyData.$ContentData;
                                             $countSR = utf8_strlen($ReplyData);
                                           }else {
                                             $ReplyData=$ReplyData."                  พบทั้งหมด ".$countidSR." ฉบับ\n";
                                             $ReplyData=$ReplyData."-----------------------------------------------------------\n";
                                           }
                                         $LinkNews = $webURL."readfileheader.php?word=".$Command[1]."&type=2&count=".$countidSR;
                                         if ($countSR>1800) {
                                            #################################################################
                                            $strFileName = "floderfile/pos".$countidSR.".txt";
                                            $objFopen = fopen($strFileName, 'w');
                                            $strText1 = $Command[1];
                                            fwrite($objFopen, $strText1);
                                            #################################################################
                                            $cutstr = mb_strimwidth($ReplyData, 0,1800, "...", "UTF-8"); //ตัดคำให้เหลือ 1800
                                            $EditContent = $cutstr."\n( ข้อมูลเพิ่มเติม ==> ".$LinkNews." )";
                                            $ReplyData = $EditContent;
                                          }} else {
                                            $ReplyData = "รูปแบบการกรอกข้อมูลไม่ถูกต้อง";
                                          }
                                            $textMessageBuilder = new TextMessageBuilder($ReplyData);
                                            $conn->close();
                                            break;
                     case "ค้นหาเฉพาะกรณี" :
                                    if (isset($Command[1]) and $Command[1]!='') {
                                        $ReplyData="-----------------------------------------------------------\n";
                                        $ReplyData=$ReplyData."                  ค้นหาเฉพาะกรณี\n";
                                        $ReplyData=$ReplyData."                    คำว่า ".$Command[1]."\n";
                                        $countidSR = 0;
                                        $sqlSR = "SELECT * FROM filedata";
                                        $resultSR = $conn->query($sqlSR);
                                        if ($resultSR->num_rows > 0) {
                                        while($rowSR = $resultSR->fetch_assoc()) {
                                              if ((strpos($rowSR["fileName"],$Command[1])!==false) && $rowSR["filegroup"]=="file3") {
                                                  $countidSR = $countidSR+1;
                                                  $ContentData=$ContentData.$rowSR["fileId"]." => ".$rowSR["fileName"]."\n";
                                                  }
                                              }
                                            }
                                        if ($countidSR>0) {
                                            $ReplyData=$ReplyData."                  พบทั้งหมด ".$countidSR." ฉบับ\n";
                                            $ReplyData=$ReplyData."-----------------------------------------------------------\n";
                                            $ReplyData=$ReplyData."   ID                       เรื่อง\n";
                                            $ReplyData=$ReplyData.$ContentData;
                                            $countSR = utf8_strlen($ReplyData);
                                          }else {
                                            $ReplyData=$ReplyData."                  พบทั้งหมด ".$countidSR." ฉบับ\n";
                                            $ReplyData=$ReplyData."-----------------------------------------------------------\n";
                                          }
                                        $LinkNews = $webURL."readfileheader.php?word=".$Command[1]."&type=3&count=".$countidSR;
                                        if ($countSR>1800) {
                                           #################################################################
                                           $strFileName = "floderfile/pos".$countidSR.".txt";
                                           $objFopen = fopen($strFileName, 'w');
                                           $strText1 = $Command[1];
                                           fwrite($objFopen, $strText1);
                                           #################################################################
                                           $cutstr = mb_strimwidth($ReplyData, 0,1800, "...", "UTF-8"); //ตัดคำให้เหลือ 1800
                                           $EditContent = $cutstr."\n( ข้อมูลเพิ่มเติม ==> ".$LinkNews." )";
                                           $ReplyData = $EditContent;
                                         }} else {
                                           $ReplyData = "รูปแบบการกรอกข้อมูลไม่ถูกต้อง";
                                         }
                                           $textMessageBuilder = new TextMessageBuilder($ReplyData);
                                           $conn->close();
                                           break;

                     case "ขอไฟล์" :
                                     if (isset($Command[1]) && is_numeric($Command[1])) {
                                       $sql = "SELECT * FROM filedata WHERE fileId = '$Command[1]'";
                                       $result = $conn->query($sql);
                                       if ($result->num_rows > 0) {
                                         while($row = $result->fetch_assoc()) {
                                           $fileheadline = $row["fileName"];
                                           $fileLink = $webURL."downloadfile.php?newsid=".$Command[1];
                                           $filedate = $row["fileDate"];
                                        }
                                       $arr_replyData = array();
                                       $ReplyData1 ="                 [ไฟล์ระหัสที่ ".$Command[1]."]\n----------------------------------------------------\n"."[เรื่อง] :: ".$fileheadline.
                                       "\n[รายงาน] :: ".$filedate."\n----------------------------------------------------\n - ".$fileLink."\n";

                                         #################################################################
                                         $strFileName = "floderfile/fd".$Command[1].".txt";
                                         $objFopen = fopen($strFileName, 'w');
                                         $strText1 = $Command[1];
                                         fwrite($objFopen, $strText1);
                                         ##################################################################
                                         $ReplyData1 = $ReplyData1."ก๊อปลิ้งค์เพื่อโหลดจากเบราเซอร์ปกติ (เบราเซอร์ของไลน์โหลดไม่ได้)";
                                         $ReplyData = $ReplyData1;
                                      } else {
                                      $ReplyData=" ตรวจสอบไม่ได้";
                                      }
                                   $conn->close();
                                   $textMessageBuilder = new TextMessageBuilder($ReplyData);
                                   break;
                                  }

                     case "เก็บไฟล์" :
                                    $keepid= $ReporterKey;
                                    $cutstr = mb_strimwidth($keepid, 0,10, "", "UTF-8");
                                    #################################################################
                                    $strFileName = "floderfile/keep".$cutstr.".txt";
                                    $objFopen = fopen($strFileName, 'w');
                                    $strText1 = $keepid;
                                    fwrite($objFopen, $strText1);
                                    ##################################################################
                                    $ReplyData= "เก็บได้ 1 ครั้ง ต่อการของลิ้งค์ 1 ครั้ง\n".$webURL."keepfile.php?newsid=".$cutstr;
                                    $textMessageBuilder = new TextMessageBuilder($ReplyData);
                                    break;

                     case "ค้นหาเครือข่าย" :
                                    if (isset($Command[1]) and $Command[1]!='') {
                                    $ReplyData="-----------------------------------------------------------\n";
                                    $ReplyData=$ReplyData."                  ค้นหาเครือข่าย\n";
                                    $ReplyData=$ReplyData."                  คำว่า ".$Command[1]."\n";
                                    $countidSR = 0;
                                    $sqlSR = "SELECT * FROM filedata";
                                    $resultSR = $conn->query($sqlSR);
                                    if ($resultSR->num_rows > 0) {
                                      while($rowSR = $resultSR->fetch_assoc()) {
                                        if ((strpos($rowSR["fileName"],$Command[1])!==false) && $rowSR["filegroup"]=="file4") {
                                          $countidSR = $countidSR+1;
                                          $ContentData=$ContentData.$rowSR["fileId"]."     =>     ".$rowSR["fileName"]."\n";
                                        }
                                      }
                                    }
                                    if ($countidSR>0) {
                                        $ReplyData=$ReplyData."                  พบทั้งหมด ".$countidSR." เครือข่าย\n";
                                                               $ReplyData=$ReplyData."-----------------------------------------------------------\n";
                                                               $ReplyData=$ReplyData."   ID                       เครือข่าย\n";
                                                               $ReplyData=$ReplyData.$ContentData;
                                                               $countSR = utf8_strlen($ReplyData);
                                                             }else {
                                                                    $ReplyData=$ReplyData."                  พบทั้งหมด ".$countidSR." เครือข่าย\n";
                                                                    $ReplyData=$ReplyData."-----------------------------------------------------------\n";
                                                                   }
                                                               $LinkNews = $webURL."readfileheader.php?word=".$Command[1]."&type=3&count=".$countidSR;
                                                               if ($countSR>1800) {
                                                                                #################################################################
                                                                                $strFileName = "floderfile/pos".$countidSR.".txt";
                                                                                $objFopen = fopen($strFileName, 'w');
                                                                                $strText1 = $Command[1];
                                                                                fwrite($objFopen, $strText1);
                                                                                #################################################################
                                                                                $cutstr = mb_strimwidth($ReplyData, 0,1800, "...", "UTF-8"); //ตัดคำให้เหลือ 1800
                                                                                $EditContent = $cutstr."\n( ข้อมูลเพิ่มเติม ==> ".$LinkNews." )";
                                                                                $ReplyData = $EditContent;
                                                                              }
                                                               } else {
                                                                       $ReplyData = "รูปแบบการกรอกข้อมูลไม่ถูกต้อง";
                                                                      }
                                                               $textMessageBuilder = new TextMessageBuilder($ReplyData);
                                                               $conn->close();
                                                               break;

                    case "บันทึกเครือข่าย" :
                         if (isset($Command[1]) && is_dir('./spiderweb/'.$Command[1])) {
                           $sql="INSERT INTO fileData (fileName, filePath, fileDate, filegroup, fileReportor)
                           VALUES ('$Command[1]','spiderweb/$Command[1]','$Ndate','file4','$Reporter')";
                           if ($conn->query($sql) === TRUE) {
                           ##############################################################################
                            $ReplyData= "่บันทึกเครือข่าย ".$Command[1]." เรียบร้อย\nผู้บันทึก ".$Reporter;
                          }else $ReplyData= "ไม่สามารถบันทึกเครือข่าย ".$Command[1]." ได้";
                         } else {
                           $ReplyData= "ไม่บันทึก :: ข้อมูลไม่ถูกต้อง";
                         }
                         $conn->close();
                         $textMessageBuilder = new TextMessageBuilder($ReplyData);
                         break;

                    case "ดูเครือข่าย" :
                          if (isset($Command[1]) && is_numeric($Command[1])) {
                          $sql = "SELECT * FROM filedata WHERE fileId = '$Command[1]' && filegroup = 'file4' ";
                          $result = $conn->query($sql);
                          if ($result->num_rows > 0) {
                          while($row = $result->fetch_assoc()) {
                                                $fileheadline = $row["fileName"];
                                                $fileLink = $webURL.$row["filePath"];
                                                $filedate = $row["fileDate"];
                                             }
                                            $arr_replyData = array();
                                            $ReplyData1 ="                 [ูเครือข่ายระหัสที่ ".$Command[1]."]\n----------------------------------------------------\n"."[เครือข่าย] :: ".$fileheadline.
                                            "\n[บันทึก] :: ".$filedate."\n----------------------------------------------------\n - ".$fileLink."\n";

                          #################################################################
                          $strFileName = "floderfile/fd".$Command[1].".txt";
                          $objFopen = fopen($strFileName, 'w');
                          $strText1 = $Command[1];
                          fwrite($objFopen, $strText1);
                          ##################################################################
                          $ReplyData = $ReplyData1;
                          } else {
                            $ReplyData=" ตรวจสอบไม่ได้";
                          }
                          $conn->close();
                          $textMessageBuilder = new TextMessageBuilder($ReplyData);
                          break;
                          }

                     default:
                                break;

                  }
      case 'image':
                  $sql2 = "SELECT * FROM news";
                  $result2 = $conn->query($sql2);
                  if ($result2->num_rows > 0) {
                     while($row2 = $result2->fetch_assoc()) {
                          $FindLastReportor = $row2["NewsReporter"];
                          $FindLastNewsID  =  $row2["News_id"];
                          $FindLastNewsPictureCount = $row2["NewsPictureCount"];
                          $FindLastNewsHadline = $row2["NewsHadline"];
                      }
                  }
                  if ($FindLastReportor==$UserID && $FindLastNewsPictureCount<4) {
                     $response = $bot->getMessageContent($MessageID);
                     $ReplyData= "m id :: ".$MessageID;
                     if ($response->isSucceeded()) {
                        $dataBinary = $response->getRawBody();
                        $countpicture = $FindLastNewsPictureCount+1;
                        $PictureName = $FindLastNewsID.$countpicture.'.jpg';
                        $fileFullSavePath = 'NewPicture/'.$PictureName;
                        file_put_contents($fileFullSavePath,$dataBinary);
                        $TableColume = "NewsPicture".$countpicture;
                        $sql="UPDATE news SET NewsPictureCount=$countpicture, $TableColume='$fileFullSavePath'
                        WHERE News_id=$FindLastNewsID";
                        if ($conn->query($sql) === TRUE) {
                           $ReplyData= "บันทึกรูปที่ ".$countpicture." \nหัวข้อข่าว".$FindLastNewsHadline." \nลงฐานข้อมูลข่าวเรียบร้อย";
                           $textMessageBuilder = new TextMessageBuilder($ReplyData);
                        } else {
                           $ReplyData= "Error: " . $sql . "\n" . $conn->error;
                           $textMessageBuilder = new TextMessageBuilder($ReplyData);
                        }
                     }
                  }
                  $conn->close();
                  break;
      case "postback" : // เอาตัวนี้ไปแยกใส่ Array
                    $sql = "SELECT * FROM lastchat WHERE LastID='1'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                       while ($row = $result->fetch_assoc()) {
                          if ($row["LastKey"]==$UserID) {
                             $getCommand = explode("=", $PostbackData);
                             switch ($getCommand[1]) {
                                    case 'staticNaws'://ดูสกิติ จะเรียกหน้า ดูชุดไหน วันไหนถึงวันไหน
                                                      $actionBuilder = array(
                                                      new PostbackTemplateActionBuilder(
                                                     'ชุด ตข.1', http_build_query(array(
                                                     'action'=>'CI1',
                                                      ))),
                                                      new PostbackTemplateActionBuilder(
                                                      'ชุด ตข.2', http_build_query(array(
                                                      'action'=>'CI2',
                                                      ))),
                                                      new PostbackTemplateActionBuilder(
                                                      'ชุด ตข.3', http_build_query(array(
                                                      'action'=>'CI3',
                                                      ))),
                                                      new PostbackTemplateActionBuilder(
                                                      'ชุด ทกร.', http_build_query(array(
                                                      'action'=>'CI4',
                                                      ))),
                                                      );
                                                      $imageUrl = $webURL.'picture/amic.jpg';
                                                      $textMessageBuilder = new TemplateMessageBuilder('เมนูคำสั่ง',
                                                      new ButtonTemplateBuilder(
                                                      $Reporter.'ฯ เลือกชุดที่ต้องการ', // กำหนดหัวเรื่อง
                                                      'เลือกชุดที่ต้องการตรวจสอบสถิติการรายงาน', // กำหนดรายละเอียด
                                                      $imageUrl, // กำหนด url รุปภาพ
                                                      $actionBuilder ));
                                                      $conn->close();
                                                      break;
                                    case 'findNews'://ค้นหาข่าว
                                                      $actionBuilder = array(
                                                      new PostbackTemplateActionBuilder(
                                                      'ข่าวสาร', http_build_query(array(
                                                      'action'=>'findNews1',
                                                      ))),
                                                      new PostbackTemplateActionBuilder(
                                                      'ผลการซักถาม', http_build_query(array(
                                                      'action'=>'findNews2',
                                                      ))),
                                                      new PostbackTemplateActionBuilder(
                                                      'ประวัติบุคคล', http_build_query(array(
                                                      'action'=>'findNews3',
                                                      ))),
                                                      new PostbackTemplateActionBuilder(
                                                      'เฉพาะกรณี', http_build_query(array(
                                                      'action'=>'findNews4',
                                                      ))),
                                                      );
                                                      $imageUrl = $webURL.'picture/amic.jpg';
                                                      $textMessageBuilder = new TemplateMessageBuilder('ค้นหาข้อมูล',
                                                      new ButtonTemplateBuilder(
                                                      $Reporter.'ฯ ค้นหาข้อมูล', // กำหนดหัวเรื่อง
                                                      'เลือกข้อมูลต้องการค้นหา', // กำหนดรายละเอียด
                                                      $imageUrl, // กำหนด url รุปภาพ
                                                      $actionBuilder ));
                                                      $conn->close();
                                                      break;

                                    case 'findNews1'://ค้นหาข่าว
                                                      //$sql4="UPDATE LastChat SET LastGroup='ค้นหา' WHERE LastID='1'";
                                                      $sql4="UPDATE LastChat SET LastGroup='ค้นหาข่าวสาร', DateStrat='', DateEnd='' WHERE LastID='1'";
                                                      $result4 = $conn->query($sql4);
                                                      $actionBuilder = array(
                                                      new DatetimePickerTemplateActionBuilder(
                                                      'เลือกวันเริ่มต้น', // ข้อความแสดงในปุ่ม
                                                      http_build_query(array(
                                                      'action'=>'startDatefindNews1',
                                                      )), // ข้อมูลที่จะส่งไปใน webhook ผ่าน postback event
                                                      'date', // date | time | datetime รูปแบบข้อมูลที่จะส่ง ในที่นี้ใช้ datatime
                                                      date("Y-m-d"), // วันที่ เวลา ค่าเริ่มต้นที่ถูกเลือก
                                                      date("Y-m-d"), //วันที่ เวลา มากสุดที่เลือกได้
                                                      date("Y-m-d",strtotime("-3650 day")) //วันที่ เวลา น้อยสุดที่เลือกได้
                                                      ),
                                                      );
                                                      $imageUrl = $webURL.'picture/amic.jpg';
                                                      $textMessageBuilder = new TemplateMessageBuilder('ค้นหาข่าว',
                                                      new ButtonTemplateBuilder(
                                                      $Reporter.'ฯ เลือกวันเริ่มต้น', // กำหนดหัวเรื่อง
                                                      'เลือกวันที่ต้องการเริ่มค้นหาข่าวสาร', // กำหนดรายละเอียด
                                                      $imageUrl, // กำหนด url รุปภาพ
                                                      $actionBuilder ));
                                                      $conn->close();
                                                      break;

                                    case 'startDatefindNews1'://ค้นหาข่าว
                                                      $paramPostback = $arrayJson['events'][0]['postback']['params']['date'];
                                                      $sql5="UPDATE LastChat SET DateStrat='$paramPostback' WHERE LastID='1'";
                                                      $result5 = $conn->query($sql5);
                                                      $actionBuilder = array(
                                                      new DatetimePickerTemplateActionBuilder(
                                                      $paramPostback.' ถึงวันที่ ', // ข้อความแสดงในปุ่ม
                                                      http_build_query(array(
                                                      'action'=>'stoptDatefindNews1',
                                                      )), // ข้อมูลที่จะส่งไปใน webhook ผ่าน postback event
                                                      'date', // date | time | datetime รูปแบบข้อมูลที่จะส่ง ในที่นี้ใช้ datatime
                                                      date("Y-m-d"), // วันที่ เวลา ค่าเริ่มต้นที่ถูกเลือก
                                                      date("Y-m-d"), //วันที่ เวลา มากสุดที่เลือกได้
                                                      date("Y-m-d",strtotime("-3650 day")) //วันที่ เวลา น้อยสุดที่เลือกได้
                                                      ),
                                                      );
                                                      $imageUrl = $webURL.'picture/amic.jpg';
                                                      $textMessageBuilder = new TemplateMessageBuilder('ค้นหาข่าว',
                                                      new ButtonTemplateBuilder(
                                                      $Reporter.'ฯ เลือกวันสุดท้าย', // กำหนดหัวเรื่อง
                                                      'เลือกวันสุดท้ายที่ต้องการค้นหาข่าวสาร', // กำหนดรายละเอียด
                                                      $imageUrl, // กำหนด url รุปภาพ
                                                      $actionBuilder ));
                                                      $conn->close();
                                                      break;

                                    case 'stoptDatefindNews1'://ค้นหาข่าว
                                                      $groupCount = 0;
                                                      $paramPostback = $arrayJson['events'][0]['postback']['params']['date'];
                                                      $sql5="UPDATE LastChat SET DateEnd='$paramPostback' WHERE LastID='1'";
                                                      $result5 = $conn->query($sql5);
                                                      if ($result5) {
                                                         $talkToUser = "กรุณาพิมพ์คำว่า ค้นหา จากนั้นวรรคแล้วตามด้วยที่ต้องการค้นหา(มากสุด 3 คำ)\nเช่น \nค้นหา เชียงใหม่\nค้นหา เชียงใหม่ อ.เมือง\nค้นหา เชียงใหม่ อ.เมือง ต.ห้วยแก้ว";
                                                      }
                                                      $ReplyData= $talkToUser;
                                                      $textMessageBuilder = new TextMessageBuilder($ReplyData);
                                                      $conn->close();
                                                      break;

                                    case 'findNews2'://ค้นหาผลซักถาม
                                                      $ReplyData= "พิมพ์ ค้นหาผลซักถาม (คำค้น)\nเช่น\nค้นหาผลซักถาม ชวน";
                                                      $textMessageBuilder = new TextMessageBuilder($ReplyData);
                                                      $conn->close();
                                                      break;

                                    case 'findNews3'://ค้นหาประวัติบุคคล
                                                      $ReplyData= "พิมพ์ ค้นหาประวัติบุคคล (คำค้น)\nเช่น\nค้นหาประวัติบุคคล ชวน";
                                                      $textMessageBuilder = new TextMessageBuilder($ReplyData);
                                                      $conn->close();
                                                      break;
                                    case 'findNews4'://ค้นหาเฉพาะกรณี
                                                      $ReplyData= "พิมพ์ ค้นหาเฉพาะกรณี (คำค้น)\nเช่น\nค้นหาเฉพาะกรณี ชวน";
                                                      $textMessageBuilder = new TextMessageBuilder($ReplyData);
                                                      $conn->close();
                                                      break;

                                    case 'readNews'://อ่านข่าว
                                                      $ReplyData= "พิมพ์ อ่านข่าว ตามด้วยไอดีข่าวที่ต้องการ\nเช่น ==> อ่านข่าว 150";
                                                      $textMessageBuilder = new TextMessageBuilder($ReplyData);
                                                      $conn->close();
                                                      break;
                                    case 'posernal'://ข้อมูลเครือข่ายเป้า
                                                      $ReplyData= "ต้องการค้นหา\nพิมพ์ ค้นหาเครือข่าย ตามด้วยชื่อเครือข่าย\nเช่น ==> ค้นหาเครือข่าย วีระ\nต้องการดูเครือข่าย\nพิมพ์ ดูเครือข่าย ตามด้วยไอดีเครือข่าย\nเช่น ==> ดูเครือข่าย 123\nต้องการบันทึกเครือข่าย\nพิมพ์ บันทึกเครือข่าย ตามด้วยชื่อเรือข่ายที่ต้องการบันทึก\nเช่น ==> บันทึกเครือข่าย วีระ หมื่นจะดา นักค้ายาเสพติดข้ามชาติ\n";
                                                      $textMessageBuilder = new TextMessageBuilder($ReplyData);
                                                      $conn->close();
                                                      break;

                                  case 'CI1'://สถิติชุด 1
                                                      $sql4="UPDATE LastChat SET LastGroup='ตข.1' WHERE LastID='1'";
                                                      $result4 = $conn->query($sql4);
                                                      $actionBuilder = array(
                                                      new DatetimePickerTemplateActionBuilder(
                                                      'เลือกวันเริ่มต้น', // ข้อความแสดงในปุ่ม
                                                      http_build_query(array(
                                                      'action'=>'startDate',
                                                      )), // ข้อมูลที่จะส่งไปใน webhook ผ่าน postback event
                                                      'date', // date | time | datetime รูปแบบข้อมูลที่จะส่ง ในที่นี้ใช้ datatime
                                                      date("Y-m-d"), // วันที่ เวลา ค่าเริ่มต้นที่ถูกเลือก
                                                      date("Y-m-d"), //วันที่ เวลา มากสุดที่เลือกได้
                                                      date("Y-m-d",strtotime("-3650 day")) //วันที่ เวลา น้อยสุดที่เลือกได้
                                                      ),
                                                      );
                                                      $imageUrl = $webURL.'picture/amic.jpg';
                                                      $textMessageBuilder = new TemplateMessageBuilder('ตข.1',
                                                      new ButtonTemplateBuilder(
                                                      $Reporter.'ฯ เลือกวันเริ่มต้น', // กำหนดหัวเรื่อง
                                                      'เลือกวันที่ต้องการตรวจสอบสถิติการรายงาน', // กำหนดรายละเอียด
                                                      $imageUrl, // กำหนด url รุปภาพ
                                                      $actionBuilder ));
                                                      $conn->close();
                                                      break;

                                 case 'CI2'://สถิติชุด 2
                                                   $sql4="UPDATE LastChat SET LastGroup='ตข.2' WHERE LastID='1'";
                                                   $result4 = $conn->query($sql4);
                                                   $actionBuilder = array(
                                                   new DatetimePickerTemplateActionBuilder(
                                                   'เลือกวันเริ่มต้น', // ข้อความแสดงในปุ่ม
                                                   http_build_query(array(
                                                   'action'=>'startDate',
                                                   )), // ข้อมูลที่จะส่งไปใน webhook ผ่าน postback event
                                                   'date', // date | time | datetime รูปแบบข้อมูลที่จะส่ง ในที่นี้ใช้ datatime
                                                   date("Y-m-d"), // วันที่ เวลา ค่าเริ่มต้นที่ถูกเลือก
                                                   date("Y-m-d"), //วันที่ เวลา มากสุดที่เลือกได้
                                                   date("Y-m-d",strtotime("-3650 day")) //วันที่ เวลา น้อยสุดที่เลือกได้
                                                   ),
                                                   );
                                                   $imageUrl = $webURL.'picture/amic.jpg';
                                                   $textMessageBuilder = new TemplateMessageBuilder('ตข.2',
                                                   new ButtonTemplateBuilder(
                                                   $Reporter.'ฯ เลือกวันเริ่มต้น', // กำหนดหัวเรื่อง
                                                   'เลือกวันที่ต้องการตรวจสอบสถิติการรายงาน', // กำหนดรายละเอียด
                                                   $imageUrl, // กำหนด url รุปภาพ
                                                   $actionBuilder ));
                                                   $conn->close();
                                                   break;

                                  case 'CI3'://สถิติชุด 3
                                                    $sql4="UPDATE LastChat SET LastGroup='ตข.3' WHERE LastID='1'";
                                                    $result4 = $conn->query($sql4);
                                                    $actionBuilder = array(
                                                    new DatetimePickerTemplateActionBuilder(
                                                    'เลือกวันเริ่มต้น', // ข้อความแสดงในปุ่ม
                                                    http_build_query(array(
                                                    'action'=>'startDate',
                                                    )), // ข้อมูลที่จะส่งไปใน webhook ผ่าน postback event
                                                    'date', // date | time | datetime รูปแบบข้อมูลที่จะส่ง ในที่นี้ใช้ datatime
                                                    date("Y-m-d"), // วันที่ เวลา ค่าเริ่มต้นที่ถูกเลือก
                                                    date("Y-m-d"), //วันที่ เวลา มากสุดที่เลือกได้
                                                    date("Y-m-d",strtotime("-3650 day")) //วันที่ เวลา น้อยสุดที่เลือกได้
                                                    ),
                                                    );
                                                    $imageUrl = $webURL.'picture/amic.jpg';
                                                    $textMessageBuilder = new TemplateMessageBuilder('ตข.3',
                                                    new ButtonTemplateBuilder(
                                                    $Reporter.'ฯ เลือกวันเริ่มต้น', // กำหนดหัวเรื่อง
                                                    'เลือกวันที่ต้องการตรวจสอบสถิติการรายงาน', // กำหนดรายละเอียด
                                                    $imageUrl, // กำหนด url รุปภาพ
                                                    $actionBuilder ));
                                                    $conn->close();
                                                    break;
                               case 'CI4'://สถิติ ทกร.
                                                 $sql4="UPDATE LastChat SET LastGroup='ทกร.' WHERE LastID='1'";
                                                 $result4 = $conn->query($sql4);
                                                 $actionBuilder = array(
                                                 new DatetimePickerTemplateActionBuilder(
                                                 'เลือกวันเริ่มต้น', // ข้อความแสดงในปุ่ม
                                                 http_build_query(array(
                                                 'action'=>'startDate',
                                                 )), // ข้อมูลที่จะส่งไปใน webhook ผ่าน postback event
                                                 'date', // date | time | datetime รูปแบบข้อมูลที่จะส่ง ในที่นี้ใช้ datatime
                                                 date("Y-m-d"), // วันที่ เวลา ค่าเริ่มต้นที่ถูกเลือก
                                                 date("Y-m-d"), //วันที่ เวลา มากสุดที่เลือกได้
                                                 date("Y-m-d",strtotime("-3650 day")) //วันที่ เวลา น้อยสุดที่เลือกได้
                                                 ),
                                                 );
                                                 $imageUrl = $webURL.'picture/amic.jpg';
                                                 $textMessageBuilder = new TemplateMessageBuilder('ทกร.',
                                                 new ButtonTemplateBuilder(
                                                 $Reporter.'ฯ เลือกวันเริ่มต้น', // กำหนดหัวเรื่อง
                                                 'เลือกวันที่ต้องการตรวจสอบสถิติการรายงาน', // กำหนดรายละเอียด
                                                 $imageUrl, // กำหนด url รุปภาพ
                                                 $actionBuilder ));
                                                 $conn->close();
                                                 break;

                                    case 'startDate':
                                                      $paramPostback = $arrayJson['events'][0]['postback']['params']['date'];
                                                      $sql5="UPDATE LastChat SET DateStrat='$paramPostback' WHERE LastID='1'";
                                                      $result5 = $conn->query($sql5);
                                                      $actionBuilder = array(
                                                      new DatetimePickerTemplateActionBuilder(
                                                      $paramPostback.' ถึงวันที่ ', // ข้อความแสดงในปุ่ม
                                                      http_build_query(array(
                                                      'action'=>'stoptDate',
                                                      )), // ข้อมูลที่จะส่งไปใน webhook ผ่าน postback event
                                                      'date', // date | time | datetime รูปแบบข้อมูลที่จะส่ง ในที่นี้ใช้ datatime
                                                      date("Y-m-d"), // วันที่ เวลา ค่าเริ่มต้นที่ถูกเลือก
                                                      date("Y-m-d"), //วันที่ เวลา มากสุดที่เลือกได้
                                                      date("Y-m-d",strtotime("-3650 day")) //วันที่ เวลา น้อยสุดที่เลือกได้
                                                      ),
                                                      );
                                                      $imageUrl = $webURL.'picture/amic.jpg';
                                                      $textMessageBuilder = new TemplateMessageBuilder('เลือกวัน',
                                                      new ButtonTemplateBuilder(
                                                      $Reporter.'ฯ เลือกวันสิ้นสุด', // กำหนดหัวเรื่อง
                                                      'เลือกวันที่ต้องการตรวจสอบสถิติการรายงาน', // กำหนดรายละเอียด
                                                      $imageUrl, // กำหนด url รุปภาพ
                                                      $actionBuilder ));
                                                      $conn->close();
                                                      break;

                                 case 'stoptDate'://สถิติชุด
                                                      $groupCount = 0;
                                                      $paramPostback = $arrayJson['events'][0]['postback']['params']['date'];
                                                      $sql5="UPDATE LastChat SET DateEnd='$paramPostback' WHERE LastID='1'";
                                                      $result5 = $conn->query($sql5);
                                                      if ($result5) {
                                                        $actionBuilder = array(
                                                          new PostbackTemplateActionBuilder(
                                                          'ตกลง', http_build_query(array(
                                                          'action'=>'CFforCheckstatic',
                                                          )))
                                                        );
                                                        $imageUrl = $webURL.'picture/amic.jpg';
                                                        $textMessageBuilder = new TemplateMessageBuilder('เลือกวัน',
                                                        new ButtonTemplateBuilder(
                                                        $Reporter.'ฯ ต้องการดูสถิติ', // กำหนดหัวเรื่อง
                                                        'ตั้งแต่ '.$row["DateStrat"].' ถึง '.$paramPostback, // กำหนดรายละเอียด
                                                        $imageUrl, // กำหนด url รุปภาพ
                                                        $actionBuilder ));
                                                      }
                                                      $conn->close();
                                                      break;
                                    case 'CFforCheckstatic'://แสดงผล
                                                      $findinfo = $row["LastGroup"];
                                                      $Team =0;
                                                      $politics= 0;
                                                      $Drug = 0;
                                                      $Weapon = 0;
                                                      $Religion = 0;
                                                      $Minority = 0;
                                                      $Oppforce = 0;
                                                      $crime = 0;
                                                      $worker = 0;
                                                      $etc = 0;
                                                      $sqlCF = "SELECT * FROM userid WHERE workposition ='$findinfo'";
                                                      $resultCF = $conn->query($sqlCF);
                                                      if ($resultCF->num_rows > 0) {
                                                         while ($rowCF = $resultCF->fetch_assoc()) { #ตรวจหาชุดจากไอดีไลน์
                                                               $BDate = $row["DateStrat"];
                                                               $LDate = $row["DateEnd"];
                                                               $sqlCF1 = "SELECT * FROM news WHERE NewsDate BETWEEN '$BDate' AND '$LDate'";
                                                               $resultCF1 = $conn->query($sqlCF1);
                                                               if ($resultCF1->num_rows > 0) {
                                                                  while ($rowCF1 = $resultCF1->fetch_assoc()) {
                                                                         if ($rowCF1["NewsReporter"]==$rowCF["userlinekey"]) {
                                                                             $Team=$Team+1;
                                                                         if (strpos($rowCF1["NewsType"],"การเมือง")!==false) {
                                                                               $politics=$politics+1; }
                                                                         if (strpos($rowCF1["NewsType"],"ยาเสพติด")!==false) {
                                                                               $Drug=$Drug+1; }
                                                                         if (strpos($rowCF1["NewsType"],"อาวุธสงคราม")!==false) {
                                                                               $Weapon=$Weapon+1; }
                                                                         if (strpos($rowCF1["NewsType"],"ศาสนา")!==false) {
                                                                               $Religion=$Religion+1; }
                                                                         if (strpos($rowCF1["NewsType"],"ชนกลุ่มน้อยในประเทศ")!==false) {
                                                                               $Minority=$Minority+1; }
                                                                         if (strpos($rowCF1["NewsType"],"กำลังฝ่ายตรงข้าม")!==false) {
                                                                               $Oppforce=$Oppforce+1; }
                                                                         if (strpos($rowCF1["NewsType"],"อาชญากรรม")!==false) {
                                                                               $crime=$crime+1; }
                                                                         if (strpos($rowCF1["NewsType"],"แรงงานต่างด้าว")!==false) {
                                                                               $worker=$worker+1; }
                                                                         if (strpos($rowCF1["NewsType"],"เบ็ดเตล็ด")!==false) {
                                                                               $etc=$etc+1; }
                                                                           }
                                                                         }
                                                                       }
                                                                     }
                                                                   }
                                                              $ReplyData="สถิติรายงานข่าวสารของ ชุด " .$findinfo;
                                                              $ReplyData=$ReplyData."\n ตั้งแต่ ".$BDate." ถึง ".$LDate;
                                                              $ReplyData=$ReplyData."\n มีจำนวน ".$Team." ฉบับ ดังนี้";
                                                              $ReplyData=$ReplyData."\n ข่าวการเมือง        จำนวน ".$politics." ฉบับ";
                                                              $ReplyData=$ReplyData."\n ข่าวยาเสพติด       จำนวน ".$Drug." ฉบับ";
                                                              $ReplyData=$ReplyData."\n ข่าวอาวุธสงคราม     จำนวน ".$Weapon." ฉบับ";
                                                              $ReplyData=$ReplyData."\n ข่าวศาสนา          จำนวน ".$Religion." ฉบับ";
                                                              $ReplyData=$ReplyData."\n ข่าว ชกน.ในประเทศ  จำนวน ".$Minority." ฉบับ";
                                                              $ReplyData=$ReplyData."\n ข่าวกำลังฝ่ายตรงข้าม  จำนวน ".$Oppforce." ฉบับ";
                                                              $ReplyData=$ReplyData."\n ข่าวอาชญากรรม      จำนวน ".$crime." ฉบับ";
                                                              $ReplyData=$ReplyData."\n ข่าวแรงงานต่างด้าว    จำนวน ".$worker." ฉบับ";
                                                              $ReplyData=$ReplyData."\n ข่าวอื่นๆ/เบ็ดเตล็ด    จำนวน ".$etc." ฉบับ";
                                                              $textMessageBuilder = new TextMessageBuilder($ReplyData);
                                                              $conn->close();
                                                              break;
                                    default:
                                 // code...$paramPostback = $events['events'][0]['postback']['params']['date'];
                                                break;
                             }
                          }
                       }
                    }

                break;
      default:
                //No reply ถ้าเกิดต้องการออฟชั่นอื่น เช่น วีดีโอ ไฟล์
                break;
    }
} else {
   if ($Command[0]=="ลงทะเบียน" && $Command[1]!="" && $Command[2]!=""
   && $Command[3]!="" && $Command[4]!="") {
      $sql = "SELECT * FROM userid WHERE userlinekey='$UserID'";
      $result = $conn->query($sql);
      if ($result->num_rows > 0) {
         $ReplyData="บัญชีไลน์นี้ได้ลงทะเบียนแล้ว";
         $textMessageBuilder = new TextMessageBuilder($ReplyData);
      } else {
         $sql = "INSERT INTO userid (first_name, last_name, workposition, userphone, userlinekey)
         VALUES ( '$Command[1]', '$Command[2]', '$Command[3]', '$Command[4]', '$UserID')";
         if ($conn->query($sql) === TRUE) {
            $ReplyData= $Command[1]." ลงทะเบียนเรียบร้อย";
            $textMessageBuilder = new TextMessageBuilder($ReplyData);
         } else {
            $ReplyData= "Error: " . $sql . "\n" . $conn->error;
            $textMessageBuilder = new TextMessageBuilder($ReplyData);
         }
     }
   } elseif($Command[0]=="เมนูคำสั่ง" or $Command[0]=="ค้นหา" or $Command[0]=="อ่านข่าว" or $Command[0]=="เครือข่าย") {
     $ReplyData = "ยังไม่ลงทะเบียน :: ลงทะเบียนก่อนใช้ระบบ";
     $textMessageBuilder = new TextMessageBuilder($ReplyData);
   }
}
if ($replyToken!='' AND $textMessageBuilder!= '') {
  $response = $bot->replyMessage($replyToken, $textMessageBuilder);
}

 function utf8_strlen($str) {
     $c = strlen($str);
     $l = 0;
     for ($i = 0; $i < $c; ++$i) {
        if ((ord($str[$i]) & 0xC0) != 0x80) {
           ++$l;
        }
     }
     return $l;
   }
 $conn->close();
?>

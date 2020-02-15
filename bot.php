<?php

require('line-message-service.php');

define("LINE_MESSAGING_API_CHANNEL_SECRET", '/dS99PmL9r96rJ3BbmRAYktUDbUSYdBDWGa+/IMYQLvXfvx56/c3ss6jKAv36H8D1Tgo03mP7LzN87umgVZbWYi4xbNkME6Zaxy9BPLnq/DjA9VT/tDDFS748H/7PBhTcdJef79+P5pPyGP7/YL1HAdB04t89/1O/w1cDnyilFU=');
define("LINE_MESSAGING_API_CHANNEL_TOKEN", '3642a5308ae8d0816c64d96d924b4ac6');

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\MessageEvent;


require('vendor/autoload.php');

$bot = new LINEBot(new CurlHTTPClient(LINE_MESSAGING_API_CHANNEL_TOKEN), [
            'channelSecret' => LINE_MESSAGING_API_CHANNEL_SECRET,
        ]);

$signature = $_SERVER["HTTP_".\LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
$body = file_get_contents("php://input");

foreach ($events as $event) {
    // メッセージを返信
    $response = $bot->replyMessage(
        $event->getReplyToken(), new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($event->getText())
    );
}

// try {
//     // Bodyと$signatureから内容をVerifyして成功すればEventを得られる
//     $events = $bot->parseEventRequest($body, $signature);
//
//     foreach ($events as $event) {
//        if ($event instanceof FollowEvent) {
//           continue;
//        } else if ($event instanceof UnfollowEvent) {
//           continue;
//        } else if ($event instanceof PostbackEvent) {
//           continue;
//        } else if ($event instanceof TextMessage) {
//           processTextMessageEvent($bot, $event);
//           continue;
//        } else if ($event instanceof LocationMessage) {
//          replyTaberguList($bot, $event, $event->getLatitude(), $event->getLongitude()); //＊追加＊
//          continue;
//        } else {
//
//        }
//
//     }
// } catch (Exception $e) {
//   // none
// }
//
// function processTextMessageEvent($bot, $event) {
//   $text = $event->getText();
//   if (isCategoryText($text)) {
//     putCategory($event->getUserId(), $text);
//     replayLocationActionMessage($bot, $event->getReplyToken());
//   } else {
//     searchFromLocationWord($bot, $event);
//     $res = $bot->replyText($event->getReplyToken(),'ジャンル(1〜4)を入力してください。(和=1,洋=2,中=3,その他=4)');
//   }
// }
//
// function isCategoryText($text) {
//   return ($text === '1' || $text === '2' || $text === '3' || $text === '4'); // FIXME magic number
// }
//
// function putCategory($user_id, $category) {
//   $data = ['type'=>'set','user_id' => $user_id,'cat'=>intval($category)];
//   $conn = curl_init();
//
//   curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, false);
//   curl_setopt($conn, CURLOPT_SSL_VERIFYHOST, false);
//   curl_setopt($conn, CURLOPT_RETURNTRANSFER, true);
//   curl_setopt($conn, CURLOPT_POST, true);
//   curl_setopt($conn, CURLOPT_URL,  'https://iimise.herokuapp.com/bot.php');
//   curl_setopt($conn, CURLOPT_POSTFIELDS, http_build_query($data));
//
//   $result = curl_exec($conn);
//
//   curl_close($conn);
//
//   return $result;
// }
//
// function replayLocationActionMessage($bot, $token) {
//   $action = new UriTemplateActionBuilder("位置情報を送る", 'line://nv/location');
//   $buttonObj = new ButtonTemplateBuilder(NULL, '続いて位置情報を送るか、住所／地域名を入力してください。', NULL, [$action]);
//   $bot->replyMessage($token,new TemplateMessageBuilder('続いて位置情報を送ってください。',$buttonObj));
// }
//
// function searchFromLocationWord($bot, $event) {
//   $location = searchGoogleGeocodingAPI($event->getText());
//   if ($location) {
//     $lat = $location['lat'];
//     $lng = $location['lng'];
//     replyTaberguList($bot, $event, $lat, $lng);
//   }
// }
//
// function searchGoogleGeocodingAPI($address) {
//   $address = urlencode($address);
//
//   $url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$address."+CA&key=".GOOGLE_MAP_API_KEY;
//
//   $contents= file_get_contents($url);
//   $jsonData = json_decode($contents,true);
//
//   return $jsonData["results"][0]["geometry"]["location"];
//
// }
//
//   function replyTaberguList($bot, $eventData, $lat, $lng) {
//      $category = getCategory($eventData->getUserId());
//      $taberoguList = getTaberoguData($category,$lat,$lng);
//      if (count($taberoguList) === 0) {
//        $bot->replyText($eventData->getReplyToken(),'お店が見つかりませんでした。');
//      } else {
//        $lineService = new LineMessageService(LINE_MESSAGING_API_CHANNEL_TOKEN);
//        $res = $lineService->postFlexMessage($eventData->getReplyToken(), $taberoguList);
//        $bot->replyText($event->getReplyToken(),$res);
//      }
//   }
//
//   function getTaberoguData($cat,$lat,$lng) {
//     $params = ['lat'=>$lat,'lng'=>$lng,'cat'=>$cat];
//     $conn = curl_init();
//
//     curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, false);
//     curl_setopt($conn, CURLOPT_SSL_VERIFYHOST, false);
//     curl_setopt($conn, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($conn, CURLOPT_POST, true);
//     curl_setopt($conn, CURLOPT_URL,  'https://iimise.herokuapp.com/bot.php');
//     curl_setopt($conn, CURLOPT_POSTFIELDS, http_build_query($params));
//
//     $result = curl_exec($conn);
//
//     curl_close($conn);
//
//     return json_decode($result);
//   }
//
//   function getCategory($user_id) {
//     $conn = curl_init();
//     $data = ['type'=>'get','user_id' => $user_id];
//     curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, false);
//     curl_setopt($conn, CURLOPT_SSL_VERIFYHOST, false);
//     curl_setopt($conn, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($conn, CURLOPT_POST, true);
//     curl_setopt($conn, CURLOPT_URL,  '{秘密のAPI URL}');
//     curl_setopt($conn, CURLOPT_POSTFIELDS, http_build_query($data));
//
//     $result = curl_exec($conn);
//
//     curl_close($conn);
//
//     $status = json_decode($result)->{'status'};
//     if ($status === 'success') {
//       return json_decode($result)->{'user'}->{'cat'};
//     } else {
//       return 1;
//     }
//   }

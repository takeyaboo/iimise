<?php

require('line-message-service.php');

use LINE\LINEBot\Event\MessageEvent\TextMessage;
// use LineMessageService;
use LINE\LINEBot\Event\MessageEvent\LocationMessage;


// Composerでインストールしたライブラリを一括読み込み
require_once __DIR__ . '/vendor/autoload.php';



// アクセストークンを使いCurlHTTPClientをインスタンス化
$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient('/dS99PmL9r96rJ3BbmRAYktUDbUSYdBDWGa+/IMYQLvXfvx56/c3ss6jKAv36H8D1Tgo03mP7LzN87umgVZbWYi4xbNkME6Zaxy9BPLnq/DjA9VT/tDDFS748H/7PBhTcdJef79+P5pPyGP7/YL1HAdB04t89/1O/w1cDnyilFU=');

//CurlHTTPClientとシークレットを使いLINEBotをインスタンス化
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => '3642a5308ae8d0816c64d96d924b4ac6']);


// LINE Messaging APIがリクエストに付与した署名を取得
$signature = $_SERVER["HTTP_" . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
$body = file_get_contents("php://input");

try {
    // Bodyと$signatureから内容をVerifyして成功すればEventを得られる
    $events = $bot->parseEventRequest($body, $signature);

    foreach ($events as $event) {
       if ($event instanceof FollowEvent) {
          continue;
       } else if ($event instanceof UnfollowEvent) {
          continue;
       } else if ($event instanceof PostbackEvent) {
          continue;
       } else if ($event instanceof TextMessage) {
         // $text = $event->getText();
         // if($text == '確認'){
         //   $messageData = [
         //      'type' => 'template',
         //      'altText' => '確認ダイアログ',
         //      'template' => [ 'type' => 'confirm', 'text' => '元気ですかー？',
         //        'actions' => [
         //            [ 'type' => 'message', 'label' => '元気です', 'text' => '元気です' ],
         //            [ 'type' => 'message', 'label' => 'まあまあです', 'text' => 'まあまあです' ],
         //        ]
         //      ]
         //   ];
         //   $bot->replyMessage($event->getReplyToken(), $messageData);
         // }else{
           processTextMessageEvent($bot, $event);
         // }
          continue;
       } else if ($event instanceof LocationMessage) {
         replyGurunaviList($bot, $event, $event->getLatitude(), $event->getLongitude()); //＊追加＊
         continue;
       } else {

       }

    }
} catch (Exception $e) {
  // none
}

function processTextMessageEvent($bot, $event) {
  $text = $event->getText();
  // if (isCategoryText($text)) {
    putCategory($event->getUserId(), $text);
    replayLocationActionMessage($bot, $event->getReplyToken());
  // } else {
  //   searchFromLocationWord($bot, $event);
  //   $res = $bot->replyText($event->getReplyToken(),'ジャンル(1〜4)を入力してください。(和=1,洋=2,中=3,その他=4)');
  // }
}

function isCategoryText($text) {
  return ($text === '1' || $text === '2' || $text === '3' || $text === '4');
}

function putCategory($user_id, $word) {
  // $data = ['type'=>'set','user_id' => $user_id,'cat'=>intval($category)];
  $data = ['type'=>'set','user_id' => $user_id,'word'=> $word];

  $conn = curl_init();

  curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($conn, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($conn, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($conn, CURLOPT_POST, true);
  curl_setopt($conn, CURLOPT_URL,  'https://iimise.herokuapp.com/bot.php');
  curl_setopt($conn, CURLOPT_POSTFIELDS, http_build_query($data));

  $result = curl_exec($conn);

  curl_close($conn);

  return $result;
}

function replayLocationActionMessage($bot, $token) {
  $action = new LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder("位置情報を送る", 'line://nv/location');
  $buttonObj = new LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder(NULL, '続いて位置情報を送るか、住所／地域名を入力してください。', NULL, [$action]);
  $bot->replyMessage($token,new LINE\LINEBot\MessageBuilder\TemplateMessageBuilder('続いて位置情報を送ってください。',$buttonObj));
}

function searchFromLocationWord($bot, $event) {
  $location = searchGoogleGeocodingAPI($event->getText());
  if ($location) {
    $lat = $location['lat'];
    $lng = $location['lng'];
    replyGurunaviList($bot, $event, $lat, $lng);
  }
}

function searchGoogleGeocodingAPI($address) {
  $address = urlencode($address);

  $url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$address."+CA&key=".GOOGLE_MAP_API_KEY;

  $contents= file_get_contents($url);
  $jsonData = json_decode($contents,true);

  return $jsonData["results"][0]["geometry"]["location"];

}

  function replyGurunaviList($bot, $eventData, $lat, $lng) {

     $freeword = getFreeword($eventData->getUserId());
     $gurunaviList = getGurunaviData($freeword,$lat,$lng);
     // $taberoguList = ['lat'=>$lat,'lng'=>$lng,'cat'=>1];
     if (count($gurunaviList) === 0) {
       $bot->replyText($eventData->getReplyToken(),'お店が見つかりませんでした。');
     } else {
       $lineService = new LineMessageService('/dS99PmL9r96rJ3BbmRAYktUDbUSYdBDWGa+/IMYQLvXfvx56/c3ss6jKAv36H8D1Tgo03mP7LzN87umgVZbWYi4xbNkME6Zaxy9BPLnq/DjA9VT/tDDFS748H/7PBhTcdJef79+P5pPyGP7/YL1HAdB04t89/1O/w1cDnyilFU=');

       $shop_detail = array(array('name' => '', 'url' => '', 'address' => ''));
       $i = 0;
       $shop_detailes = "";
       foreach ($gurunaviList['rest'] as $shop) {


                   //APIから取得した情報を変数に格納
                   $shop_id = empty($shop['id']) ? '' : $shop['id'];
                   $name           = empty($shop['name']) ? '' : $shop['name'];
                   $url                 = empty($shop['url']) ? '' : $shop['url'];
                   // $coupon_pc_url       = empty($shop['coupon_url']['pc']) ? '' : $shop['coupon_url']['pc'];
                   // $coupon_mobile_url   = empty($shop['coupon_url']['mobile']) ? '' : $shop['coupon_url']['mobile'];
                   $address             = empty($shop['address']) ? '' : $shop['address'];
                   // $tel                 = empty($shop['tel']) ? '' : $shop['tel'];
                   // $opentime            = empty($shop['opentime']) ? '' : $shop['opentime'];
                   // $holiday             = empty($shop['holiday']) ? '' : $shop['holiday'];
                   // $pr                  = empty($shop['pr']['pr_short']) ? '' : $shop['pr']['pr_short'];
                   // $pr_long             = empty($shop['pr']['pr_long']) ? '' : $shop['pr']['pr_long'];
                   // $image_url           = empty($shop['image_url']['shop_image1']) ? $arrayPhotoApiData['image_url'] : $shop['image_url']['shop_image1'];
                   // $lunch               = empty($shop['lunch']) ? '' : $shop['lunch'];
                   // $update_date         = empty($shop['update_date']) ? '' : $shop['update_date'];
                   // $shop_categories     = empty($shop['code']['category_name_s']) ? '' : $shop['code']['category_name_s'];
                   // $category_name = array();
                   // foreach ($shop['code']['category_name_s'] as $v) {
                   //     if (isset($v) && !is_array($v)) {
                   //         $category_name[] = $v;
                   //     }
                   // }
                   // $category_names = implode(',', $category_name);
                   //
                   // array_push($shop_detail[$i]['name'], $name);
                   // array_push($shop_detail[$i]['url'], $url);
                   // array_push($shop_detail[$i]['address'], $address);

                   $shop_detail[$i]['name'] = $name;
                   $shop_detail[$i]['url'] = $url;
                   $shop_detail[$i]['address'] = $address;

                   $result_num = $i + 1;
                   $shop_detailes .= '['.$result_num.'件目]'."\n".'店名:'.$name."\n".'URL:'.$url."\n".'住所:'.$address."\n\n";



                   $i++;

               }

       // $res = $lineService->postFlexMessage($eventData->getReplyToken(), $taberoguList['rest']);
       // $res = $lineService->postFlexMessage($eventData->getReplyToken(), $shop_detail);
       $res = $shop_detailes;
       // $res = serialize($taberoguList);
       // $res = implode(",", array_keys($taberoguList));
       // $res = implode(",", array_keys($taberoguList));
       // $res = implode(",", $shop_detail[0]['name']);

       $bot->replyText($eventData->getReplyToken(), $res);
     }
  }

  function getGurunaviData($freeword,$lat,$lng) {
    // $params = ['lat'=>$lat,'lng'=>$lng,'cat'=>$cat];
    $params = array(
            // 'format' => 'json',
            'keyid' => '47af4b099ec1b7e4406c7a2dc247f0da',
            //10件まで
            'hit_per_page' => '10',
            'latitude' => $lat,
            'longitude' => $lng,
            //朝までやってる店
            'until_morning' => 1,
            //フリーワード検索
            'freeword' => $freeword,
            // 'range' => 2,
            // 'inputCoordinatesMode' => 1,
            // 'coordinatesMode' => 1,
        );



   $url = 'https://api.gnavi.co.jp/RestSearchAPI/v3/?' . http_build_query($params);
   // $url = 'https://api.gnavi.co.jp/RestSearchAPI/v3/?keyid=47af4b099ec1b7e4406c7a2dc247f0da&hit_per_page=10&latitude='.$lat.'&longitude='.$lng.'&freeword='.$freeword;


   $option = [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 3];
   $ch = curl_init($url);
   curl_setopt_array($ch, $option);
   $json = curl_exec($ch);
   $info = curl_getinfo($ch);
   $errorNo = curl_errno($ch);
   if ($errorNo !== CURLE_OK) {
       return [];
   }
   if ($info['http_code'] !== 200) {
       return [];
   }
   return json_decode($json, true);
  }

  function getFreeword($user_id) {
    $conn = curl_init();
    $data = ['type'=>'get','user_id' => $user_id];
    curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($conn, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($conn, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($conn, CURLOPT_POST, true);
    curl_setopt($conn, CURLOPT_URL,  'https://iimise.herokuapp.com/bot.php');
    curl_setopt($conn, CURLOPT_POSTFIELDS, http_build_query($data));

    $result = curl_exec($conn);

    curl_close($conn);

    $status = json_decode($result)->{'status'};
    if ($status === 'success') {
      return json_decode($result)->{'user'}->{'word'};
    } else {
      return 1;
    }
  }

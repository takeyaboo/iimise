<?php

require('line-message-service.php');

define("LINE_MESSAGING_API_CHANNEL_SECRET", '/dS99PmL9r96rJ3BbmRAYktUDbUSYdBDWGa+/IMYQLvXfvx56/c3ss6jKAv36H8D1Tgo03mP7LzN87umgVZbWYi4xbNkME6Zaxy9BPLnq/DjA9VT/tDDFS748H/7PBhTcdJef79+P5pPyGP7/YL1HAdB04t89/1O/w1cDnyilFU=');
define("LINE_MESSAGING_API_CHANNEL_TOKEN", '3642a5308ae8d0816c64d96d924b4ac6');

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\MessageEvent;
// use LineMessageService;


require('/vendor/autoload.php');

$bot = new LINEBot(new CurlHTTPClient(LINE_MESSAGING_API_CHANNEL_TOKEN), [
            'channelSecret' => LINE_MESSAGING_API_CHANNEL_SECRET,
        ]);

$signature = $_SERVER["HTTP_".\LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
$body = file_get_contents("php://input");

try {
    // Bodyと$signatureから内容をVerifyして成功すればEventを得られる
    $events = $bot->parseEventRequest($body, $signature);

    foreach ($events as $event) {
       if ($event instanceof TextMessage) {
          $bot->replyText($event->getReplyToken(), 'メッセージが来たよ！');
          continue;
       }
    }
} catch (Exception $e) {
  // none
}

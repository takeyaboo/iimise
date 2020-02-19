<?php

class LineMessageService {

  private $accessToken;

  public function __construct($accessToken) {
     $this->accessToken = $accessToken;
  }

  public function postFlexMessage($token, $param) {
        $postJson = $this->createJsonParameter($token, $param);
        return $this->postMessage($postJson);
  }

  private function createJsonParameter($token, $list) {
    $messages = $this->generateFlexMessageContents($list);
    $result = ['replyToken'=>$token, 'messages'=>$messages];
    return json_encode($result);
  }

  private function postMessage($jsonParam) {
    $conn = curl_init();

    curl_setopt($conn, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($conn, CURLOPT_POST, true);
    curl_setopt($conn, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$this->accessToken,'Content-type: application/json'));
    curl_setopt($conn, CURLOPT_URL,  'https://api.line.me/v2/bot/message/reply');
    curl_setopt($conn, CURLOPT_POSTFIELDS, $jsonParam);

    $result = curl_exec($conn);

    curl_close($conn);

    // $a = var_export($result, true);
    // $result = $jsonParam['token'];

    return $result;
    // return 'test3';
  }

  private function generateFlexMessageContents($list) {
      $carouselItem = [];
      foreach ($list as $taberogu) {
           $carouselItem[] = $this->getFlexTemplate($taberogu);
      }
      $contents = ["type"=>"carousel","contents"=>$carouselItem];
      //20200216 この配列の三番目の要素があると返ってこない（文字列でも）
      return ['type'=>'flex', 'altText'=>'search', 'contents'=>$contents];

      // type => 'carousel',
      // contents => [
      //   type => 'flex',
      //   altText => 'search',
      //   contents =>[
      //
      //   ]
      // ]


  }

  private function getStarImg($rating, $seq) {
      if ($rating >= $seq) {
        return "https://scdn.line-apps.com/n/channel_devcenter/img/fx/review_gold_star_28.png";
      }
      return "https://scdn.line-apps.com/n/channel_devcenter/img/fx/review_gray_star_28.png";
  }

  private function getFlexTemplate($taberogu) {
    // return [
    //   "type"=> "bubble",
    //   "body"=> [
    //       "type"=> "box",
    //       "layout"=> "vertical",
    //       "contents"=> [
    //         [
    //           "type"=> "text",
    //           "text"=> 'a',
    //         ]
    //       ]
    //     ]
    // ];
    // return  [
    //  "type"=> "bubble",
    //  "hero"=> [
    //    "type"=> "image",
    //    "url"=> 'a',
    //    "size"=> "full",
    //    "aspectRatio"=> "20:13",
    //    "aspectMode"=> "cover",
    //    "action"=> [
    //      "type"=> "uri",
    //      "uri"=> 'a'
    //    ]
    //  ],
    //  "body"=> [
    //    "type"=> "box",
    //    "layout"=> "vertical",
    //    "contents"=> [
    //      [
    //        "type"=> "text",
    //        "text"=> 'a',
    //        "weight"=> "bold",
    //        "size"=> "xl"
    //      ],
    //      [
    //        "type"=> "box",
    //        "layout"=> "baseline",
    //        "margin"=> "md",
    //        "contents"=> [
    //          [
    //            "type"=> "icon",
    //            "size"=> "sm",
    //            "url"=> 'a'
    //          ],
    //          [
    //            "type"=> "icon",
    //            "size"=> "sm",
    //            "url"=> 'a'
    //          ],
    //          [
    //            "type"=> "icon",
    //            "size"=> "sm",
    //            "url"=> 'a'
    //          ],
    //          [
    //            "type"=> "icon",
    //            "size"=> "sm",
    //            "url"=> 'a'
    //          ],
    //          [
    //            "type"=> "icon",
    //            "size"=> "sm",
    //            "url"=> 'a'
    //          ],
    //          [
    //            "type"=> "text",
    //            "text"=> 'a',
    //            "size"=> "sm",
    //            "color"=> "#999999",
    //            "margin"=> "md",
    //            "flex"=> 0
    //          ]
    //        ]
    //      ],
    //      [
    //        "type"=> "box",
    //        "layout"=> "vertical",
    //        "margin"=> "lg",
    //        "spacing"=> "sm",
    //        "contents"=> [
    //          [
    //            "type"=> "box",
    //            "layout"=> "baseline",
    //            "spacing"=> "sm",
    //            "contents"=> [
    //              [
    //                "type"=> "text",
    //                "text"=> "種類",
    //                "color"=> "#aaaaaa",
    //                "size"=> "sm",
    //                "flex"=> 1
    //              ],
    //              [
    //                "type"=> "text",
    //                "text"=> 'a',
    //                "wrap"=> true,
    //                "color"=> "#666666",
    //                "size"=> "sm",
    //                "flex"=> 5
    //              ]
    //            ]
    //          ],
    //          [
    //            "type"=> "box",
    //            "layout"=> "baseline",
    //            "spacing"=> "sm",
    //            "contents"=> [
    //              [
    //                "type"=> "text",
    //                "text"=> "場所",
    //                "color"=> "#aaaaaa",
    //                "size"=> "sm",
    //                "flex"=> 1
    //              ],
    //              [
    //                "type"=> "text",
    //                "text"=> 'a',
    //                "wrap"=> true,
    //                "color"=> "#666666",
    //                "size"=> "sm",
    //                "flex"=> 5
    //              ]
    //            ]
    //          ]
             // [ 仕様変更により取得できなくなったので閉じる
             //   "type"=> "box",
             //   "layout"=> "baseline",
             //   "spacing"=> "sm",
             //   "contents"=> [
             //     [
             //       "type"=> "text",
             //       "text"=> "金額",
             //       "color"=> "#aaaaaa",
             //       "size"=> "sm",
             //       "flex"=> 1
             //     ],
             //     [
             //       "type"=> "text",
             //       "text"=> $taberogu->{'price'},
             //       "wrap"=> true,
             //       "color"=> "#666666",
             //       "size"=> "sm",
             //       "flex"=> 5
             //     ]
             //   ]
             // ]
   //         ]
   //       ]
   //     ]
   //   ],
   //   "footer"=> [
   //     "type"=> "box",
   //     "layout"=> "vertical",
   //     "spacing"=> "sm",
   //     "contents"=> [
   //       [
   //         "type"=> "button",
   //         "style"=> "link",
   //         "height"=> "sm",
   //         "action"=> [
   //           "type"=> "uri",
   //           "label"=> "食べログをみる",
   //           "uri"=> 'a'
   //         ]
   //       ],
   //       [
   //         "type"=> "spacer",
   //         "size"=> "sm"
   //       ]
   //     ],
   //     "flex"=> 0
   //   ]
   // ];


   // $ratingInt = round($taberogu->{'rating'});
   // $distance = round($taberogu->{'distance'}*1000);
   return [
     "type"=> "bubble",
     "hero"=> [
       "type"=> "image",
       "url"=> 'aaa',
       "size"=> "full",
       "aspectRatio"=> "20:13",
       "aspectMode"=> "cover",
       "action"=> [
         "type"=> "uri",
         "uri"=> 'aaa'
       ]
     ],
     "body"=> [
       "type"=> "box",
       "layout"=> "vertical",
       "contents"=> [
         [
           "type"=> "text",
           "text"=> 'bbb',
           "weight"=> "bold",
           "size"=> "xl"
         ],

         [
           "type"=> "box",
           "layout"=> "vertical",
           "margin"=> "lg",
           "spacing"=> "sm",
           "contents"=> [

             [
               "type"=> "box",
               "layout"=> "baseline",
               "spacing"=> "sm",
               "contents"=> [
                 [
                   "type"=> "text",
                   "text"=> "場所",
                   "color"=> "#aaaaaa",
                   "size"=> "sm",
                   "flex"=> 1
                 ],
                 [
                   "type"=> "text",
                   // "text"=> $taberogu->{'street'}.' ('.$distance.'m)',
                   "text"=> 'asjh',
                   "wrap"=> true,
                   "color"=> "#666666",
                   "size"=> "sm",
                   "flex"=> 5
                 ]
               ]
             ]

           ]
         ]
       ]
   //   ],
   //   "footer"=> [
   //     "type"=> "box",
   //     "layout"=> "vertical",
   //     "spacing"=> "sm",
   //     "contents"=> [
   //       [
   //         "type"=> "button",
   //         "style"=> "link",
   //         "height"=> "sm",
   //         "action"=> [
   //           "type"=> "uri",
   //           "label"=> "ぐるナビをみる",
   //           "uri"=> 'fd'
   //         ]
   //       ],
   //       [
   //         "type"=> "spacer",
   //         "size"=> "sm"
   //       ]
   //     ],
   //     "flex"=> 0
   //   ]
   // ];
  }
}

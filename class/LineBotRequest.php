<?php
require_once __DIR__ . '/slack.class.php';

use First1\V1\Notify\Slack;

class LineBotRequest
{
    private $channel_id;
    private $channel_secret;
    private $channel_access_token;
    private $log_path;
    private $userId;
    private $reply_token;

    /************** 初始設定 ********************/
    //初始建立
    public function __construct($channel_id = '', $channel_secret = '', $channel_access_token = '', $path = '')
    {
        $this->channel_id           = $channel_id;
        $this->channel_secret       = $channel_secret;
        $this->channel_access_token = $channel_access_token;

        if (preg_match("/^\//", $path)) {
            $this->log_path = $path;
        } else {
            $this->log_path = __DIR__ . '/log';
        }

        if (! is_dir($this->log_path)) {
            mkdir($this->log_path, 0777, true);
        }

    }
    ##

    //設定 channel id
    public function set_channel_id($id)
    {
        if (empty($id)) {
            return false;
        } else {
            $this->channel_id = $id;
            return true;
        }
    }
    ##

    //設定 channel secret
    public function set_channel_secret($secret)
    {
        if (empty($secret)) {
            return false;
        } else {
            $this->channel_secret = $secret;
            return true;
        }
    }
    ##

    //設定 channel access token
    public function set_access_token($token)
    {
        if (empty($token)) {
            return false;
        } else {
            $this->channel_access_token = $token;
            return true;
        }
    }
    ##

    //設定 log 路徑資訊
    public function set_log_path($path = '')
    {
        if ($path) {
            $this->log_path = $path;
            if (is_dir($this->log_path)) {
                return true;
            } else {
                return mkdir($this->log_path, 0777, true);
            }

        } else {
            return false;
        }

    }
    ##

    /************* Request ****************/
    //建立動作請求
    public function send($data = [], $tracking = [])
    {
        if (empty($data)) {
            $this->stop_action(400, '無動作資訊');
        }

        unset($result);
        $res = [];
        foreach ($data['messages'] as $k => $v) {
            $webhook_type = $v['actionType'];

            switch ($webhook_type) {
                case 'text':
                    $msg = $v[$webhook_type];
                    if (empty($v['sender'])) {
                        $res[] = $this->textConstruct($msg);
                    } else {
                        $res[] = array_merge($this->textConstruct($msg), ['sender' => $v['sender']]);
                    }

                    break;

                case 'sticker':
                    $arr = $v[$webhook_type];
                    if (empty($v['sender'])) {
                        $res[] = $this->stickerConstruct($arr);
                    } else {
                        $res[] = array_merge($this->stickerConstruct($arr), ['sender' => $v['sender']]);
                    }

                    unset($arr);

                    break;

                case 'image':
                    $arr = $v[$webhook_type];
                    if (empty($v['sender'])) {
                        $res[] = $this->imageConstruct($arr);
                    } else {
                        $res[] = array_merge($this->imageConstruct($arr), ['sender' => $v['sender']]);
                    }

                    unset($arr);

                    break;

                case 'video':
                    $arr = $v[$webhook_type];
                    if (empty($v['sender'])) {
                        $res[] = $this->videoConstruct($arr);
                    } else {
                        $res[] = array_merge($this->videoConstruct($arr), ['sender' => $v['sender']]);
                    }

                    unset($arr);

                    break;

                case 'audio':
                    $arr = $v[$webhook_type];
                    if (empty($v['sender'])) {
                        $res[] = $this->audioConstruct($arr);
                    } else {
                        $res[] = array_merge($this->audioConstruct($arr), ['sender' => $v['sender']]);
                    }

                    unset($arr);

                    break;

                case 'location':
                    $arr = $v[$webhook_type];
                    if (empty($v['sender'])) {
                        $res[] = $this->locationConstruct($arr);
                    } else {
                        $res[] = array_merge($this->locationConstruct($arr), ['sender' => $v['sender']]);
                    }

                    unset($arr);

                    break;

                case 'imagemap':
                    $arr = $v[$webhook_type];
                    if (empty($v['sender'])) {
                        $res[] = $this->imagemapConstruct($arr);
                    } else {
                        $res[] = array_merge($this->imagemapConstruct($arr), ['sender' => $v['sender']]);
                    }

                    unset($arr);

                    break;

                case 'confirm':
                    $arr = $v[$webhook_type];
                    if (empty($v['sender'])) {
                        $res[] = $this->confirmConstruct($arr);
                    } else {
                        $res[] = array_merge($this->confirmConstruct($arr), ['sender' => $v['sender']]);
                    }

                    unset($arr);

                    break;

                case 'button':
                    $arr = $v[$webhook_type];
                    if (empty($v['sender'])) {
                        $res[] = $this->buttonConstruct($arr);
                    } else {
                        $res[] = array_merge($this->buttonConstruct($arr), ['sender' => $v['sender']]);
                    }

                    unset($arr);

                    break;

                case 'carousel':
                    $arr = $v[$webhook_type];
                    // $res[] = $this->carouselConstruct($arr) ;
                    if (empty($v['sender'])) {
                        $res[] = $this->carouselConstruct($arr);
                    } else {
                        $res[] = array_merge($this->carouselConstruct($arr), ['sender' => $v['sender']]);
                    }

                    unset($arr);

                    break;

                case 'imagecarousel':
                    $arr = $v[$webhook_type];
                    if (empty($v['sender'])) {
                        $res[] = $this->imageCarouselConstruct($arr);
                    } else {
                        $res[] = array_merge($this->imageCarouselConstruct($arr), ['sender' => $v['sender']]);
                    }

                    unset($arr);

                    break;

                case 'flex':
                    $arr = $v[$webhook_type];
                    if (empty($v['sender'])) {
                        $res[] = $this->flex($arr);
                    } else {
                        $res[] = array_merge($this->flex($arr), ['sender' => $v['sender']]);
                    }

                    unset($arr);

                    break;

                case 'flexJson':
                    $arr = $v[$webhook_type];
                    if (empty($v['sender'])) {
                        $res[] = $this->flexJson($arr);
                    } else {
                        $res[] = array_merge($this->flexJson($arr), ['sender' => $v['sender']]);
                    }

                    unset($arr);

                    break;

                case 'quickReply':
                    $arr = $v[$webhook_type];
                    if (empty($v['sender'])) {
                        $res[] = $this->quickReply($arr);
                    } else {
                        $res[] = array_merge($this->quickReply($arr), ['sender' => $v['sender']]);
                    }

                    unset($arr);

                    break;

                case 'profile':
                    $_token = $v[$webhook_type];
                    $res    = $this->GetProfile($_token);
                    unset($_token);

                    return $res;

                    break;

                case 'roomProfile':
                    $_token = $v[$webhook_type];
                    $res    = $this->GetRoomProfile($_token['roomId'], $_token['userId']);
                    unset($_token);

                    return $res;

                    break;

                case 'memberProfile':
                    $_token = $v[$webhook_type];
                    $res    = $this->GetMemberProfile($_token['groupId'], $_token['userId']);
                    unset($_token);

                    return $res;

                    break;

                case 'groupSummary':
                    $_token = $v[$webhook_type];
                    $res    = $this->GetGroupSummary($_token);
                    unset($_token);

                    return $res;

                    break;

                default:
                    $msg = '無法確認的操作(' . date("Y-m-d H:i:s") . ')';
                    $this->stop_action(400, $msg);

                    break;
            }
        }

        // $this->userId = (preg_match("/^U[0-9a-f]{32}$/i", $data['userId'])) ? $data['userId'] : $data['reply_token'] ;
        // $this->userId = (preg_match("/^[U|C|R]{1}[0-9a-f]{32}$/i", $data['userId'])) ? $data['userId'] : $data['reply_token'] ;
        $this->userId      = $data['userId'];
        $this->reply_token = empty($data['reply_token']) ? null : $data['reply_token'];

        if (empty($res)) {
            $this->stop_action(400, '無法確認回應內容');
            return false;
        } else {
            return $this->push_reply($res, $tracking);
        }
    }
    ##

    //建構 text
    private function textConstruct($txt)
    {
        $txt_type = 'utf-8';
        $limit    = 2000;

        if (empty($txt) || (mb_strlen($txt, $txt_type) > $limit)) {
            return false;
        } else {
            $template = ['type' => 'text', 'text' => $txt];
            return $template;
        }
    }
    ##

    //建構 sticker
    private function stickerConstruct($arr)
    {
        if (empty($arr['packageId']) || empty($arr['stickerId'])) {
            return false;
        } else {
            $template = ['type' => 'sticker', 'packageId' => $arr['packageId'], 'stickerId' => $arr['stickerId']];
            return $template;
        }
    }
    ##

    //建構 image
    private function imageConstruct($arr)
    {
        if (empty($arr['imageUrl']) || empty($arr['previewUrl'])) {
            return false;
        } else {
            $template = ['type' => 'image', 'originalContentUrl' => $arr['imageUrl'], 'previewImageUrl' => $arr['previewUrl']];
            return $template;
        }
    }
    ##

    //建構 video
    private function videoConstruct($arr)
    {
        if (empty($arr['videoUrl']) || empty($arr['previewUrl'])) {
            return false;
        } else {
            $template = ['type' => 'video', 'originalContentUrl' => $arr['videoUrl'], 'previewImageUrl' => $arr['previewUrl']];
            if (isset($arr['trackingId'])) {
                $template['trackingId'] = $arr['trackingId'];
            }

            return $template;
        }
    }
    ##

    //建構 audio
    private function audioConstruct($arr)
    {
        if (empty($arr['audioUrl'])) {
            return false;
        } else {
            $duration = (preg_match("/^\d+$/", $arr['duration'])) ? $arr['duration'] : 60000; //預設 60000 ms
            $template = ['type' => 'audio', 'originalContentUrl' => $arr['audioUrl'], 'duration' => $duration];
            return $template;
        }
    }
    ##

    //建構 location
    private function locationConstruct($arr)
    {
        if (empty($arr['title']) || empty($arr['address']) || empty($arr['latitude']) || empty($arr['longitude'])) {
            return false;
        } else {
            $template = ['type' => 'location', 'title' => $arr['title'], 'address' => $arr['address'], 'latitude' => $arr['latitude'], 'longitude' => $arr['longitude']];
            return $template;
        }
    }
    ##

    //建構 Imagemap
    private function imagemapConstruct($arr = [])
    {
        if (empty($arr)) {
            return false;
        } else {
            $zones     = [];
            $zoneLimit = 50; //max line imagemap zone limit
            $i         = 0;
            foreach ($arr['imapZone'] as $k => $v) {
                $zones[] = $this->imagemapZoneConstruct($v);

                if ((++$i) >= $zoneLimit) {
                    break;
                }

            }

            $template = [
                'type'     => 'imagemap',
                'baseUrl'  => $arr['imapUrl'],
                'altText'  => $arr['imapTitle'],
                'baseSize' => [
                    'width'  => $arr['imapWidth'],
                    'height' => $arr['imapHeight'],
                ],
            ];

            if (is_array($arr['video']) && ! empty($arr['video'])) {
                $template = array_merge($template, [
                    'originalContentUrl' => $arr['video']['videoUrl'],
                    'previewImageUrl'    => $arr['video']['previewUrl'],
                    'area'               => [
                        'x'      => $arr['video']['x'],
                        'y'      => $arr['video']['y'],
                        'width'  => $arr['video']['w'],
                        'height' => $arr['video']['h'],
                    ],
                    'externalLink'       => [
                        'linkUri' => $arr['video']['extUrl'],
                        'label'   => $arr['video']['extLabel'],
                    ],
                ]
                );
            }

            $template['actions'] = $zones;

            return $template;
        }
    }
    ##

    //建構 Imagemap 區域點擊動作
    private function imagemapZoneConstruct($zone = [])
    {
        if (empty($zone['areaUrl'])) { //文字區域
            $obj = [
                'type' => 'message',
                'text' => $zone['areaTitle'],
                'area' => [
                    'x'      => $zone['area']['x'],
                    'y'      => $zone['area']['y'],
                    'width'  => $zone['area']['w'],
                    'height' => $zone['area']['h'],
                ],
            ];
        } else {
            $obj = [
                'type'    => 'uri',
                'linkUri' => $zone['areaUrl'],
                'area'    => [
                    'x'      => $zone['area']['x'],
                    'y'      => $zone['area']['y'],
                    'width'  => $zone['area']['w'],
                    'height' => $zone['area']['h'],
                ],
            ];
        }

        return $obj;
    }
    ##

    //建構 confirm
    private function confirmConstruct($arr = [])
    {
        if (empty($arr)) {
            return false;
        } else {
            //產出 action
            $acts = [];

            $act_max = 2;
            $i       = 0;
            foreach ($arr['actions'] as $k => $v) {
                $acts[] = $this->actionsConstruct($v);

                if ((++$i) >= $act_max) {
                    break;
                }

            }
            ##

            $template = [
                'type'     => 'template',
                'altText'  => $arr['altText'],
                'template' => [
                    'type'    => 'confirm',
                    'text'    => $arr['label'],
                    'actions' => $acts,
                ],
            ];

            return $template;
        }
    }
    ##

    //建構 actions
    private function actionsConstruct($arr = [])
    {
        if (empty($arr)) {
            return false;
        } else {
            $acts = [];

            foreach ($arr as $k => $v) {
                if ($k == 'postback') {
                    $acts = [
                        'type'  => 'postback',
                        'label' => $v['label'],
                        'data'  => $v['data'],
                        'text'  => $v['text'],
                    ];
                } else if ($k == 'message') {
                    $acts = [
                        'type'  => 'message',
                        'label' => $v['label'],
                        'text'  => $v['text'],
                    ];
                } else if ($k == 'uri') {
                    $acts = [
                        'type'  => 'uri',
                        'label' => $v['label'],
                        'uri'   => $v['uri'],
                    ];

                    if (! empty($v['altUri'])) {
                        $acts['altUri'] = ['desktop' => $v['altUri']];
                    }
                } else if ($k == 'datetimepicker') {
                    $v['initial'] = trim($v['initial']);
                    $v['max']     = trim($v['max']);
                    $v['min']     = trim($v['min']);

                    $v['initial'] = preg_replace("/ /", 'T', $v['initial']);
                    $v['max']     = preg_replace("/ /", 'T', $v['max']);
                    $v['min']     = preg_replace("/ /", 'T', $v['min']);

                    $acts = [
                        'type'    => 'datetimepicker',
                        'label'   => $v['label'],
                        'data'    => $v['data'],
                        'mode'    => $v['mode'],
                        'initial' => $v['initial'],
                        'max'     => $v['max'],
                        'min'     => $v['min'],
                    ];
                } else if ($v['type'] == 'camera') { //only for quick_reply
                    $acts = [
                        'type'  => $v['type'],
                        'label' => $v['label'],
                    ];
                } else if ($v['type'] == 'cameraRoll') { //only for quick_reply
                    $acts = [
                        'type'  => $v['type'],
                        'label' => $v['label'],
                    ];
                } else if ($v['type'] == 'location') { //only for quick_reply
                    $acts = [
                        'type'  => $v['type'],
                        'label' => $v['label'],
                    ];
                }
            }

            return $acts;
        }
    }
    ##

    //建構 button
    private function buttonConstruct($arr = [])
    {
        if (empty($arr)) {
            return false;
        } else {
            //產出 action
            $acts = [];

            $act_max = 4;
            $i       = 0;
            foreach ($arr['actions'] as $k => $v) {
                $acts[] = $this->actionsConstruct($v);

                if ((++$i) >= $act_max) {
                    break;
                }

            }
            ##

            $template = [
                'type'     => 'template',
                'altText'  => $arr['altText'],
                'template' => [
                    'type'              => 'buttons',
                    'thumbnailImageUrl' => $arr['imageUrl'],
                    'title'             => $arr['title'],
                    'text'              => $arr['label'],
                ],
            ];

            if (! empty($arr['imageRatio'])) {
                $template['template']['imageAspectRatio'] = $arr['imageRatio'];
            }

            if (! empty($arr['imageSize'])) {
                $template['template']['imageSize'] = $arr['imageSize'];
            }

            if (! empty($arr['imageBackground'])) {
                $template['template']['imageBackgroundColor'] = $arr['imageBackground'];
            }

            if (! empty($arr['defaultAct']) && is_array($arr['defaultAct'])) {
                $template['template']['defaultAction'] = $this->actionsConstruct($arr['defaultAct']);
            }

            $template['template']['actions'] = $acts;

            return $template;
        }
    }
    ##

    //建構 carousel (max cols = 10)
    private function carouselConstruct($arr = [])
    {
        if (empty($arr)) {
            return false;
        } else {
            //產出 columns
            $cols = [];

            $col_max = 10;
            $i       = 0;
            foreach ($arr['columns'] as $k => $v) {
                $cols[] = $this->carouselColumnConstruct($v);

                if ((++$i) >= $col_max) {
                    break;
                }

            }
            ##

            $template = [
                'type'     => 'template',
                'altText'  => $arr['altText'],
                'template' => [
                    'type'    => 'carousel',
                    'columns' => $cols,
                ],
            ];
            if (! empty($arr['imageRatio'])) {
                $template['template']['imageAspectRatio'] = $arr['imageRatio'];
            }

            if (! empty($arr['imageSize'])) {
                $template['template']['imageSize'] = $arr['imageSize'];
            }

            return $template;
        }
    }
    ##

    //建構 carousel column (max action per column = 3)
    private function carouselColumnConstruct($arr = [])
    {
        if (empty($arr)) {
            return false;
        } else {
            //產出 action
            $acts = [];

            $act_max = 3;
            $i       = 0;
            foreach ($arr['actions'] as $k => $v) {
                $acts[] = $this->actionsConstruct($v);

                if ((++$i) >= $act_max) {
                    break;
                }

            }
            ##

            $template = [
                'thumbnailImageUrl' => $arr['imageUrl'],
                'title'             => $arr['title'],
                'text'              => $arr['text'],
            ];

            if (! empty($arr['imageBackground'])) {
                $template['imageBackgroundColor'] = $arr['imageBackground'];
            }

            if (! empty($arr['defaultAct']) && is_array($arr['defaultAct'])) {
                $template['defaultAction'] = $this->actionsConstruct($arr['defaultAct']);
            }

            $template['actions'] = $acts;

            return $template;
        }
    }
    ##

    //建構 image carousel (max cols = 10)
    private function imageCarouselConstruct($arr = [])
    {
        if (empty($arr)) {
            return false;
        } else {
            //產出 columns
            $cols = [];

            $col_max = 10;
            $i       = 0;
            foreach ($arr['columns'] as $k => $v) {
                $cols[] = $this->imageCarouselColumnConstruct($v);

                if ((++$i) >= $col_max) {
                    break;
                }

            }
            ##

            $template = [
                'type'     => 'template',
                'altText'  => $arr['altText'],
                'template' => [
                    'type'    => 'image_carousel',
                    'columns' => $cols,
                ],
            ];

            return $template;
        }
    }
    ##

    //建構 image carousel column (max action per column = 1)
    private function imageCarouselColumnConstruct($arr = [])
    {
        if (empty($arr)) {
            return false;
        } else {
            $template = [
                'imageUrl' => $arr['imageUrl'],
                'action'   => $this->actionsConstruct($arr['action']),
            ];

            return $template;
        }
    }
    ##

    //flex
    private function flex($arr = [], $userId = null)
    {
        global $webhook_bot, $webhook_userId, $webhook_reply_token, $webhook_replyLog, $webhook_pushLog, $webhook_errorLog;

        $userId = (empty($userId)) ? $webhook_userId : $userId;

        $contents = [];
        $contents = $this->contentConstruct($arr['contents']);

        $template = [
            'type'     => 'flex',
            'altText'  => $arr['altText'],
            'contents' => $contents,
        ];

        return $template;
    }
    ##

    //建構 flex contents
    private function contentConstruct($arr = [])
    {
        if (empty($arr)) {
            file_put_contents($webhook_errorLog, date("Y-m-d H:i:s") . ' Empty flex contents parameters.' . "\n\n", FILE_APPEND);
            return false;
        } else {
            $contents = [];

            foreach ($arr as $k => $v) {
                $contents[] = array_merge(['type' => 'bubble'], $this->buildFlexContent($v));
            }

            if (count($contents) <= 0) {
                return false;
            } else if (count($contents) == 1) {
                return $contents[0];
            } else if (count($contents) > 1) {
                return array_merge(['type' => 'carousel'], ['contents' => $contents]);
            }

        }
    }
    ##

    //build flex block
    private function buildFlexContent($arr = [])
    {
        if (empty($arr)) {
            file_put_contents($webhook_errorLog, date("Y-m-d H:i:s") . ' Empty flex contents parameters.' . "\n\n", FILE_APPEND);
            return false;
        } else {
            $direction = '';
            $header    = [];
            $hero      = [];
            $body      = [];
            $footer    = [];
            $styles    = [];

            //block
            foreach ($arr as $k => $v) {
                if (! empty($v) && ($k == 'direction')) {
                    if (strtoupper($v) == 'L') {
                        $direction = 'ltr';
                    } else if (strtoupper($v) == 'R') {
                        $direction = 'rtl';
                    } else if (strtolower($v) == 'ltr') {
                        $direction = 'ltr';
                    } else if (strtolower($v) == 'rtl') {
                        $direction = 'rtl';
                    }

                } else if (! empty($v) && ($k == 'header')) { //Header block. Specify a box component.
                                                                 //header style
                    if ($v['bgColor']) {
                        $styles['header']['backgroundColor'] = $v['bgColor'];
                    }

                    unset($v['bgColor']);

                    if ($v['backgroundColor']) {
                        $styles['header']['backgroundColor'] = $v['backgroundColor'];
                    }

                    if (! empty($v['separator'])) {
                        $styles['header']['separator'] = true;
                    }

                    if ($v['separatorColor']) {
                        $styles['header']['separatorColor'] = $v['separatorColor'];
                    }

                    ##

                    $header = $this->createFlexBox($v);
                                                               // $header = array_merge($header, array('spacing' => 'md')) ;
                } else if (! empty($v) && ($k == 'hero')) { //Hero block. Specify an image component.
                                                               //hero style
                    if ($v['bgColor']) {
                        $styles['hero']['backgroundColor'] = $v['bgColor'];
                    }

                    unset($v['bgColor']);

                    if ($v['backgroundColor']) {
                        $styles['hero']['backgroundColor'] = $v['backgroundColor'];
                    }

                    if (! empty($v['separator'])) {
                        $styles['hero']['separator'] = true;
                    }

                    if ($v['separatorColor']) {
                        $styles['hero']['separatorColor'] = $v['separatorColor'];
                    }

                    ##

                    $hero = $this->createFlexBox($v);
                } else if (! empty($v) && ($k == 'body')) { //Body block. Specify a box component.
                                                               //body style
                    if ($v['bgColor']) {
                        $styles['body']['backgroundColor'] = $v['bgColor'];
                    }

                    unset($v['bgColor']);

                    if ($v['backgroundColor']) {
                        $styles['body']['backgroundColor'] = $v['backgroundColor'];
                    }

                    if (! empty($v['separator'])) {
                        $styles['body']['separator'] = true;
                    }

                    if ($v['separatorColor']) {
                        $styles['body']['separatorColor'] = $v['separatorColor'];
                    }

                    ##

                    $body = $this->createFlexBox($v);
                                                                 // $body = array_merge($body, array('spacing' => 'md')) ;
                } else if (! empty($v) && ($k == 'footer')) { //Footer block. Specify a box component.
                                                                 //footer style
                    if ($v['bgColor']) {
                        $styles['footer']['backgroundColor'] = $v['bgColor'];
                    }

                    unset($v['bgColor']);

                    if ($v['backgroundColor']) {
                        $styles['footer']['backgroundColor'] = $v['backgroundColor'];
                    }

                    if (! empty($v['separator'])) {
                        $styles['footer']['separator'] = true;
                    }

                    if ($v['separatorColor']) {
                        $styles['footer']['separatorColor'] = $v['separatorColor'];
                    }

                    ##

                    $footer = $this->createFlexBox($v);
                                                                 // $footer = array_merge($footer, array('spacing' => 'md')) ;
                } else if (! empty($v) && ($k == 'styles')) { //Style of each block. Specify a bubble style object. For more information, see Objects for the block style.
                    $styles = $this->createFlexBox($v);
                }
            }
            ##

            // $po_data = array('type' => 'bubble') ;
            $template = [];

            if (! empty($direction)) {
                $template = array_merge($template, ['direction' => $direction]);
            }

            if (! empty($header)) {
                $template = array_merge($template, ['header' => $header]);
            }

            if (! empty($hero)) {
                $template = array_merge($template, ['hero' => $hero]);
            }

            if (! empty($body)) {
                $template = array_merge($template, ['body' => $body]);
            }

            if (! empty($footer)) {
                $template = array_merge($template, ['footer' => $footer]);
            }

            if (! empty($styles)) {
                $template = array_merge($template, ['styles' => $styles]);
            }

            return $template;
        }
        ##
    }
    ##

    //create button, icon, image, text, box, filler, separator, spacer
    private function createFlexBox($arr = [], $recursive = '')
    {
        $box = [];

        foreach ($arr as $k => $v) {
            if (($k == 'type') && ($v == 'box')) {
                if (! preg_match("/^[h|v|b]$/isu", $arr['layout'])) {
                    unset($arr['layout']);
                    $box = array_merge($box, ['layout' => 'vertical']);
                }
                $box = array_merge($box, ['type' => $v]);
            } else if ($k == 'weight') {
                $v = strtolower($v);
                if (strtolower($v) == 'y') {
                    $box = array_merge($box, ['weight' => 'bold']);
                } else {
                    $box = array_merge($box, ['weight' => 'regular']);
                }
            } else if (($k == 'contents') && (count($v) > 0)) {
                $tmpBox = [];
                foreach ($v as $ka => $va) {
                    $tmpBox[] = $this->createFlexBox($va);
                }

                if (! empty($tmpBox)) {
                    $box = array_merge($box, ['contents' => $tmpBox]);
                }

            } else if (($k == 'action') && (count($v) > 0)) {
                $box = array_merge($box, ['action' => $this->actionsBuild($v)]);
            } else if ($k == 'btnTextColor') {
                if (strtoupper($v) == 'B') {
                    $box = array_merge($box, ['style' => 'secondary']);
                } else if (strtoupper($v) == 'W') {
                    $box = array_merge($box, ['style' => 'primary']);
                }

            } else if ($k == 'btnColor') {
                if (! empty($v)) {
                    $box = array_merge($box, ['color' => $v]);
                }

            } else if ($k == 'layout') {
                if (strtoupper($v) == 'H') {
                    $box = array_merge($box, ['layout' => 'horizontal']);
                } else if (strtoupper($v) == 'V') {
                    $box = array_merge($box, ['layout' => 'vertical']);
                } else if (strtoupper($v) == 'B') {
                    $box = array_merge($box, ['layout' => 'baseline']);
                }

            } else if ($k == 'wrap') {
                // if (strtoupper($v) == 'Y') $box = array_merge($box, array('wrap' => true)) ;
                if ($v) {
                    $box = array_merge($box, ['wrap' => true]);
                }

            } else if ($k == 'align') {
                if (strtolower($v) == 'l') {
                    $box = array_merge($box, ['align' => 'start']);
                } else if (strtolower($v) == 'c') {
                    $box = array_merge($box, ['align' => 'center']);
                } else if (strtolower($v) == 'r') {
                    $box = array_merge($box, ['align' => 'end']);
                }

            } else if ($k == 'txtColor') {
                if (preg_match("/^\#\w{6}$/isu", $v)) {
                    $box = array_merge($box, ['color' => $v]);
                }

            } else if ($k == 'txtSize') {
                if (! empty($v)) {
                    $box = array_merge($box, ['size' => $v]);
                }

            } else {
                $box = array_merge($box, [$k => $v]);
            }
        }

        if (count($box) >= 1) {
            return $box;
        } else {
            return false;
        }

    }
    ##

    //建構 action 動作
    private function actionsBuild($arr = [])
    {
        $post_data = [];

        if (empty($arr)) {
            return false;
        } else {
            $acts = [];

            foreach ($arr as $k => $v) {
                if ($k == 'postback') {
                    $acts = [
                        'type'  => $k,
                        'label' => $v['label'],
                        'data'  => $v['data'],
                    ];
                } else if ($k == 'message') {
                    $acts = [
                        'type'  => $k,
                        'label' => $v['label'],
                        'text'  => $v['text'],
                    ];
                } else if ($k == 'uri') {
                    $acts = [
                        'type'  => $k,
                        'label' => $v['label'],
                        'uri'   => $v['uri'],
                    ];
                } else if ($k == 'datetimepicker') {
                    $v['type']    = $k;
                    $v['initial'] = trim($v['initial']);
                    $v['max']     = trim($v['max']);
                    $v['min']     = trim($v['min']);

                    $v['initial'] = preg_replace("/ /", 'T', $v['initial']);
                    $v['max']     = preg_replace("/ /", 'T', $v['max']);
                    $v['min']     = preg_replace("/ /", 'T', $v['min']);

                    $acts = $v;
                }
            }

            return $acts;
        }
    }
    ##

    //flex JSON
    private function flexJson($arr = [])
    {
        $contents = [];
        $contents = json_decode($arr['contents'], true);

        $template = [
            'type'     => 'flex',
            'altText'  => $arr['altText'],
            'contents' => $contents,
        ];

        return $template;
    }
    ##

    //Quick Reply
    private function quickReply($arr = [])
    {
        $items = [];
        foreach ($arr['items'] as $k => $v) {
            $items[] = [
                'type'     => 'action',
                'imageUrl' => $v['image'],
                'action'   => $this->actionsConstructRest($v['action']),
            ];
        }

        $temp = [];
        if (! empty($arr['text'])) {
            $temp = [
                'type' => 'text',
                'text' => $arr['text'],
            ];
        } else if (! empty($arr['sticker'])) {
            $temp = $this->stickerConstruct($arr['sticker']);
        } else if (! empty($arr['image'])) {
            $temp = $this->imageConstruct($arr['image']);
        } else if (! empty($arr['video'])) {
            $temp = $this->videoConstruct($arr['video']);
        } else if (! empty($arr['audio'])) {
            $temp = $this->audioConstruct($arr['audio']);
        } else if (! empty($arr['location'])) {
            $temp = $this->locationConstruct($arr['location']);
        } else if (! empty($arr['imagemap'])) {
            $temp = $this->imagemapConstruct($arr['imagemap']);
        } else if (! empty($arr['confirm'])) {
            $temp = $this->confirmConstruct($arr['confirm']);
        } else if (! empty($arr['button'])) {
            $temp = $this->buttonConstruct($arr['button']);
        } else if (! empty($arr['carousel'])) {
            $temp = $this->carouselConstruct($arr['carousel']);
        } else if (! empty($arr['imagecarousel'])) {
            $temp = $this->imageCarouselConstruct($arr['imagecarousel']);
        } else if (! empty($arr['flexJson'])) {
            $temp = $this->flexJson($arr['flexJson']);
        }

        $template = array_merge($temp, ['quickReply' => ['items' => $items]]);

        return $template;
    }
    ##

    //建構 REST 版 actions
    protected function actionsConstructRest($arr = [])
    {
        if (empty($arr)) {
            return false;
        } else {
            $acts = [];

            $v = $arr;
            if ($v['type'] == 'postback') {
                $acts = [
                    'type'  => $v['type'],
                    'label' => $v['label'],
                    'data'  => $v['data'],
                    'text'  => $v['text'],
                ];
            } else if ($v['type'] == 'message') {
                $acts = [
                    'type'  => $v['type'],
                    'label' => $v['label'],
                    'text'  => $v['text'],
                ];
            } else if ($v['type'] == 'uri') {
                $acts = [
                    'type'  => $v['type'],
                    'label' => $v['label'],
                    'uri'   => $v['uri'],
                ];
            } else if ($v['type'] == 'datetimepicker') {
                $v['initial'] = trim($v['initial']);
                $v['max']     = trim($v['max']);
                $v['min']     = trim($v['min']);

                $v['initial'] = preg_replace("/ /", 'T', $v['initial']);
                $v['max']     = preg_replace("/ /", 'T', $v['max']);
                $v['min']     = preg_replace("/ /", 'T', $v['min']);

                $acts = [
                    'type'    => $v['type'],
                    'label'   => $v['label'],
                    'data'    => $v['data'],
                    'mode'    => $v['mode'],
                    'initial' => $v['initial'],
                    'max'     => $v['max'],
                    'min'     => $v['min'],
                ];
            } else if ($v['type'] == 'camera') {
                $acts = [
                    'type'  => $v['type'],
                    'label' => $v['label'],
                ];
            } else if ($v['type'] == 'cameraRoll') {
                $acts = [
                    'type'  => $v['type'],
                    'label' => $v['label'],
                ];
            } else if ($v['type'] == 'location') {
                $acts = [
                    'type'  => $v['type'],
                    'label' => $v['label'],
                ];
            }

            return $acts;
        }
    }
    ##

    //Get User Profile
    private function GetProfile($token)
    {
        $url = 'https://api.line.me/v2/bot/profile/' . $token;

        $opts = [
            'http' => [
                'method' => "GET",
                'header' => "Authorization: Bearer " . $this->channel_access_token . "\r\n",
            ],
        ];

        $context = stream_context_create($opts);
        $res     = file_get_contents($url, false, $context);

        return $res;
    }
    ##

    //Get Room User Profile
    private function GetRoomProfile($roomId, $token)
    {
        $url = 'https://api.line.me/v2/bot/room/' . $roomId . '/member/' . $token;

        $opts = [
            'http' => [
                'method' => "GET",
                'header' => "Authorization: Bearer " . $this->channel_access_token . "\r\n",
            ],
        ];

        $context = stream_context_create($opts);
        $res     = file_get_contents($url, false, $context);

        return $res;
    }
    ##

    //Get Member User Profile
    private function GetMemberProfile($groupId, $token)
    {
        $url = 'https://api.line.me/v2/bot/group/' . $groupId . '/member/' . $token;

        $opts = [
            'http' => [
                'method' => "GET",
                'header' => "Authorization: Bearer " . $this->channel_access_token . "\r\n",
            ],
        ];

        $context = stream_context_create($opts);
        $res     = file_get_contents($url, false, $context);

        return $res;
    }
    ##

    //Get group summary
    private function GetGroupSummary($groupId)
    {
        $url = 'https://api.line.me/v2/bot/group/' . $groupId . '/summary';

        $opts = [
            'http' => [
                'method' => "GET",
                'header' => "Authorization: Bearer " . $this->channel_access_token . "\r\n",
            ],
        ];

        $context = stream_context_create($opts);
        $res     = @file_get_contents($url, false, $context);

        return $res;
    }
    ##

    /*************** 例外終止 *****************/
    //終止動作
    public function stop_action($status, $message)
    {
        $log = $this->log_path . '/error';
        if (! is_dir($log)) {
            mkdir($log, 0777, true);
        }

        $log .= '/error_' . date("Y-m-d") . '.log';

        $json = json_encode(['stauts' => $status, 'message' => $message, 'channel_id' => $this->channel_id, 'userId' => $this->userId, 'datetime' => date("Y-m-d H:i:s") . '.' . microtime()], JSON_UNESCAPED_UNICODE);
        file_put_contents($log, date("Y-m-d H:i:s ") . $json . "\n", FILE_APPEND);
        exit($json);
    }
    ##

    /**************** 發送 ******************/
    //REST PUSH 方式發送
    public function push($userId, $post_data, $tracking)
    {
        return $this->curl($userId, 'push', $post_data, 'POST', $tracking);
    }
    ##

    //REST REPLY 方式發送
    public function reply($reply_token, $post_data, $tracking)
    {
        return $this->curl($reply_token, 'reply', $post_data, 'POST', $tracking);
    }
    ##

    //REST push_reply
    public function push_reply($post_data, $tracking)
    {
        if (! empty($this->reply_token)) {
            $json     = $this->reply($this->reply_token, $post_data, $tracking);
            $response = json_decode($json, true);
            if (! empty($response['sentMessages'])) {
                return $json;
            }
        }

        $json     = $this->push($this->userId, $post_data, $tracking);
        $response = json_decode($json, true);

        return empty($response['sentMessages']) ? false : $json;
    }
    ##

    //curl 發送
    private function curl($target, $way = 'push', $post_data = [], $mehod = 'POST', $tracking = [])
    {
        //請求 log
        $request_log = $this->log_path . '/request';
        if (! is_dir($request_log)) {
            $tf = mkdir($request_log, 0777, true);
        }

        $request_log .= '/request_' . date("Y-m-d") . '.log';
        ##

        if (empty($this->channel_access_token)) {
            $this->stop_action(400, '未指定 channel access token');
        }

        $way   = strtolower($way);
        $mehod = strtoupper($mehod);

        $header = [
            'Authorization: Bearer {' . $this->channel_access_token . '}',
            'Content-Type: application/json; charset=utf-8',
        ];

        $post_data['messages'] = $post_data;

        if ($way == 'reply') {
            $post_data['replyToken'] = $target;
        } else if ($way == 'push') {
            $post_data['to'] = $target;
        } else {
            $this->stop_action(400, '無法確認發送模式');
        }

        $url = 'https://api.line.me/v2/bot/message/' . $way;

        $ch = curl_init($url);

        if ($mehod == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $mehod);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_HEADER, true) ;

        $result     = curl_exec($ch);
        $returnCode = curl_getinfo($ch);
        curl_close($ch);

        $process_log = 'End-Point:' . "\n" . $url . "\n";
        $process_log .= 'Request:' . "\n" . json_encode($post_data, JSON_UNESCAPED_UNICODE) . "\n";
        // $process_log .= 'Response:' . "\n" . json_encode($result, JSON_UNESCAPED_UNICODE) . "\n";
        $process_log .= 'Response:' . "\n" . $result . "\n";

        file_put_contents($request_log, date("Y-m-d H:i:s") . "\n" . $process_log . "\n", FILE_APPEND);

        // if (($result == '{}') && !empty($tracking)) {
        //     $vars = base64_encode(json_encode(array_merge($tracking, ['reply' => json_encode($post_data)])));
        //     $cmd  = 'nohup php -f ' . __DIR__ . '/track.php ' . $vars . ' > /dev/null &';
        //     $res  = shell_exec($cmd);
        // }

        //20250414 推播異常時通知
        $result_array = json_decode($result, true);
        if (! empty($result_array['message'])) {
            Slack::incomingWebhook('Line push 發送異常：' . $result_array['message']);
        }
        $result_array = null;unset($result_array);

        // return $returnCode ;
        return $result;
    }
    ##

}

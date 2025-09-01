<?php
// version: 1.21

class LineBotRequest
{
    private $channel_id;
    private $channel_secret;
    private $channel_access_token;
    private $headers;
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

        $this->log_path = preg_match("/^\//", $path) ? $path : __DIR__ . '/log';
        if (!is_dir($this->log_path)) {
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
    public function send($data = array(), $tracking = array())
    {
        if (empty($data)) {
            $this->stop_action(400, '無動作資訊');
        }

        unset($result);
        $res = array();
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
        $this->reply_token = $data['reply_token'];

        if (empty($res)) {
            $this->stop_action(400, '無法確認回應內容');
        }

        // else return (empty($data['reply_token'])) ? $this->push($data['userId'], $res, $tracking) : $this->reply($data['reply_token'], $res, $tracking) ;
        else {
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
            $template = array('type' => 'text', 'text' => $txt);
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
            $template = array('type' => 'sticker', 'packageId' => $arr['packageId'], 'stickerId' => $arr['stickerId']);
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
            $template = array('type' => 'image', 'originalContentUrl' => $arr['imageUrl'], 'previewImageUrl' => $arr['previewUrl']);
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
            $template = array('type' => 'video', 'originalContentUrl' => $arr['videoUrl'], 'previewImageUrl' => $arr['previewUrl']);
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
            $template = array('type' => 'audio', 'originalContentUrl' => $arr['audioUrl'], 'duration' => $duration);
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
            $template = array('type' => 'location', 'title' => $arr['title'], 'address' => $arr['address'], 'latitude' => $arr['latitude'], 'longitude' => $arr['longitude']);
            return $template;
        }
    }
    ##

    //建構 Imagemap
    private function imagemapConstruct($arr = array())
    {
        if (empty($arr)) {
            return false;
        } else {
            $zones     = array();
            $zoneLimit = 50; //max line imagemap zone limit
            $i         = 0;
            foreach ($arr['imapZone'] as $k => $v) {
                $zones[] = $this->imagemapZoneConstruct($v);

                if ((++$i) >= $zoneLimit) {
                    break;
                }

            }

            $template = array(
                'type'     => 'imagemap',
                'baseUrl'  => $arr['imapUrl'],
                'altText'  => $arr['imapTitle'],
                'baseSize' => array(
                    'width'  => $arr['imapWidth'],
                    'height' => $arr['imapHeight'],
                ),
            );

            if (is_array($arr['video']) && !empty($arr['video'])) {
                $template = array_merge($template, array(
                    'originalContentUrl' => $arr['video']['videoUrl'],
                    'previewImageUrl'    => $arr['video']['previewUrl'],
                    'area'               => array(
                        'x'      => $arr['video']['x'],
                        'y'      => $arr['video']['y'],
                        'width'  => $arr['video']['w'],
                        'height' => $arr['video']['h'],
                    ),
                    'externalLink'       => array(
                        'linkUri' => $arr['video']['extUrl'],
                        'label'   => $arr['video']['extLabel'],
                    ),
                )
                );
            };

            $template['actions'] = $zones;

            return $template;
        }
    }
    ##

    //建構 Imagemap 區域點擊動作
    private function imagemapZoneConstruct($zone = array())
    {
        if (empty($zone['areaUrl'])) { //文字區域
            $obj = array(
                'type' => 'message',
                'text' => $zone['areaTitle'],
                'area' => array(
                    'x'      => $zone['area']['x'],
                    'y'      => $zone['area']['y'],
                    'width'  => $zone['area']['w'],
                    'height' => $zone['area']['h'],
                ),
            );
        } else {
            $obj = array(
                'type'    => 'uri',
                'linkUri' => $zone['areaUrl'],
                'area'    => array(
                    'x'      => $zone['area']['x'],
                    'y'      => $zone['area']['y'],
                    'width'  => $zone['area']['w'],
                    'height' => $zone['area']['h'],
                ),
            );
        }

        return $obj;
    }
    ##

    //建構 confirm
    private function confirmConstruct($arr = array())
    {
        if (empty($arr)) {
            return false;
        } else {
            //產出 action
            $acts = array();

            $act_max = 2;
            $i       = 0;
            foreach ($arr['actions'] as $k => $v) {
                $acts[] = $this->actionsConstruct($v);

                if ((++$i) >= $act_max) {
                    break;
                }

            }
            ##

            $template = array(
                'type'     => 'template',
                'altText'  => $arr['altText'],
                'template' => array(
                    'type'    => 'confirm',
                    'text'    => $arr['label'],
                    'actions' => $acts,
                ),
            );

            return $template;
        }
    }
    ##

    //建構 actions
    private function actionsConstruct($arr = array())
    {
        if (empty($arr)) {
            return false;
        } else {
            $acts = array();

            foreach ($arr as $k => $v) {
                if ($k == 'postback') {
                    $displayText = $v['displayText'];
                    $displayText = empty($displayText) ? $v['text'] : $displayText;

                    $acts = array(
                        'type'        => 'postback',
                        'label'       => $v['label'],
                        'data'        => $v['data'],
                        'displayText' => $displayText,
                    );
                } else if ($k == 'message') {
                    $acts = array(
                        'type'  => 'message',
                        'label' => $v['label'],
                        'text'  => $v['text'],
                    );
                } else if ($k == 'uri') {
                    $acts = array(
                        'type'  => 'uri',
                        'label' => $v['label'],
                        'uri'   => $v['uri'],
                    );

                    if (!empty($v['altUri'])) {
                        $acts['altUri'] = array('desktop' => $v['altUri']);
                    };
                } else if ($k == 'datetimepicker') {
                    $v['initial'] = trim($v['initial']);
                    $v['max']     = trim($v['max']);
                    $v['min']     = trim($v['min']);

                    $v['initial'] = preg_replace("/ /", 'T', $v['initial']);
                    $v['max']     = preg_replace("/ /", 'T', $v['max']);
                    $v['min']     = preg_replace("/ /", 'T', $v['min']);

                    $acts = array(
                        'type'    => 'datetimepicker',
                        'label'   => $v['label'],
                        'data'    => $v['data'],
                        'mode'    => $v['mode'],
                        'initial' => $v['initial'],
                        'max'     => $v['max'],
                        'min'     => $v['min'],
                    );
                } else if ($v['type'] == 'camera') { //only for quick_reply
                    $acts = array(
                        'type'  => $v['type'],
                        'label' => $v['label'],
                    );
                } else if ($v['type'] == 'cameraRoll') { //only for quick_reply
                    $acts = array(
                        'type'  => $v['type'],
                        'label' => $v['label'],
                    );
                } else if ($v['type'] == 'location') { //only for quick_reply
                    $acts = array(
                        'type'  => $v['type'],
                        'label' => $v['label'],
                    );
                }
            }

            return $acts;
        }
    }
    ##

    //建構 button
    private function buttonConstruct($arr = array())
    {
        if (empty($arr)) {
            return false;
        } else {
            //產出 action
            $acts = array();

            $act_max = 4;
            $i       = 0;
            foreach ($arr['actions'] as $k => $v) {
                $acts[] = $this->actionsConstruct($v);

                if ((++$i) >= $act_max) {
                    break;
                }

            }
            ##

            $template = array(
                'type'     => 'template',
                'altText'  => $arr['altText'],
                'template' => array(
                    'type'              => 'buttons',
                    'thumbnailImageUrl' => $arr['imageUrl'],
                    'title'             => $arr['title'],
                    'text'              => $arr['label'],
                ),
            );

            if (!empty($arr['imageRatio'])) {
                $template['template']['imageAspectRatio'] = $arr['imageRatio'];
            }

            if (!empty($arr['imageSize'])) {
                $template['template']['imageSize'] = $arr['imageSize'];
            }

            if (!empty($arr['imageBackground'])) {
                $template['template']['imageBackgroundColor'] = $arr['imageBackground'];
            }

            if (!empty($arr['defaultAct']) && is_array($arr['defaultAct'])) {
                $template['template']['defaultAction'] = $this->actionsConstruct($arr['defaultAct']);
            }

            $template['template']['actions'] = $acts;

            return $template;
        }
    }
    ##

    //建構 carousel (max cols = 10)
    private function carouselConstruct($arr = array())
    {
        if (empty($arr)) {
            return false;
        } else {
            //產出 columns
            $cols = array();

            $col_max = 10;
            $i       = 0;
            foreach ($arr['columns'] as $k => $v) {
                $cols[] = $this->carouselColumnConstruct($v);

                if ((++$i) >= $col_max) {
                    break;
                }

            }
            ##

            $template = array(
                'type'     => 'template',
                'altText'  => $arr['altText'],
                'template' => array(
                    'type'    => 'carousel',
                    'columns' => $cols,
                ),
            );
            if (!empty($arr['imageRatio'])) {
                $template['template']['imageAspectRatio'] = $arr['imageRatio'];
            }

            if (!empty($arr['imageSize'])) {
                $template['template']['imageSize'] = $arr['imageSize'];
            }

            return $template;
        }
    }
    ##

    //建構 carousel column (max action per column = 3)
    private function carouselColumnConstruct($arr = array())
    {
        if (empty($arr)) {
            return false;
        } else {
            //產出 action
            $acts = array();

            $act_max = 3;
            $i       = 0;
            foreach ($arr['actions'] as $k => $v) {
                $acts[] = $this->actionsConstruct($v);

                if ((++$i) >= $act_max) {
                    break;
                }

            }
            ##

            $template = array(
                'thumbnailImageUrl' => $arr['imageUrl'],
                'title'             => $arr['title'],
                'text'              => $arr['text'],
            );

            if (!empty($arr['imageBackground'])) {
                $template['imageBackgroundColor'] = $arr['imageBackground'];
            }

            if (!empty($arr['defaultAct']) && is_array($arr['defaultAct'])) {
                $template['defaultAction'] = $this->actionsConstruct($arr['defaultAct']);
            }

            $template['actions'] = $acts;

            return $template;
        }
    }
    ##

    //建構 image carousel (max cols = 10)
    private function imageCarouselConstruct($arr = array())
    {
        if (empty($arr)) {
            return false;
        } else {
            //產出 columns
            $cols = array();

            $col_max = 10;
            $i       = 0;
            foreach ($arr['columns'] as $k => $v) {
                $cols[] = $this->imageCarouselColumnConstruct($v);

                if ((++$i) >= $col_max) {
                    break;
                }

            }
            ##

            $template = array(
                'type'     => 'template',
                'altText'  => $arr['altText'],
                'template' => array(
                    'type'    => 'image_carousel',
                    'columns' => $cols,
                ),
            );

            return $template;
        }
    }
    ##

    //建構 image carousel column (max action per column = 1)
    private function imageCarouselColumnConstruct($arr = array())
    {
        if (empty($arr)) {
            return false;
        } else {
            $template = array(
                'imageUrl' => $arr['imageUrl'],
                'action'   => $this->actionsConstruct($arr['action']),
            );

            return $template;
        }
    }
    ##

    //flex
    private function flex($arr = array(), $userId = null)
    {
        global $webhook_bot, $webhook_userId, $webhook_reply_token, $webhook_replyLog, $webhook_pushLog, $webhook_errorLog;

        $userId = (empty($userId)) ? $webhook_userId : $userId;

        $contents = array();
        $contents = $this->contentConstruct($arr['contents']);

        $template = array(
            'type'     => 'flex',
            'altText'  => $arr['altText'],
            'contents' => $contents,
        );

        return $template;
    }
    ##

    //建構 flex contents
    private function contentConstruct($arr = array())
    {
        if (empty($arr)) {
            file_put_contents($webhook_errorLog, date("Y-m-d H:i:s") . ' Empty flex contents parameters.' . "\n\n", FILE_APPEND);
            return false;
        } else {
            $contents = array();

            foreach ($arr as $k => $v) {
                $contents[] = array_merge(array('type' => 'bubble'), $this->buildFlexContent($v));
            }

            if (count($contents) <= 0) {
                return false;
            } else if (count($contents) == 1) {
                return $contents[0];
            } else if (count($contents) > 1) {
                return array_merge(array('type' => 'carousel'), array('contents' => $contents));
            }

        }
    }
    ##

    //build flex block
    private function buildFlexContent($arr = array())
    {
        if (empty($arr)) {
            file_put_contents($webhook_errorLog, date("Y-m-d H:i:s") . ' Empty flex contents parameters.' . "\n\n", FILE_APPEND);
            return false;
        } else {
            $direction = '';
            $header    = array();
            $hero      = array();
            $body      = array();
            $footer    = array();
            $styles    = array();

            //block
            foreach ($arr as $k => $v) {
                if (!empty($v) && ($k == 'direction')) {
                    if (strtoupper($v) == 'L') {
                        $direction = 'ltr';
                    } else if (strtoupper($v) == 'R') {
                        $direction = 'rtl';
                    } else if (strtolower($v) == 'ltr') {
                        $direction = 'ltr';
                    } else if (strtolower($v) == 'rtl') {
                        $direction = 'rtl';
                    }

                } else if (!empty($v) && ($k == 'header')) { //Header block. Specify a box component.
                    //header style
                    if ($v['bgColor']) {
                        $styles['header']['backgroundColor'] = $v['bgColor'];
                    }

                    unset($v['bgColor']);

                    if ($v['backgroundColor']) {
                        $styles['header']['backgroundColor'] = $v['backgroundColor'];
                    }

                    if (!empty($v['separator'])) {
                        $styles['header']['separator'] = true;
                    }

                    if ($v['separatorColor']) {
                        $styles['header']['separatorColor'] = $v['separatorColor'];
                    }

                    ##

                    $header = $this->createFlexBox($v);
                    // $header = array_merge($header, array('spacing' => 'md')) ;
                } else if (!empty($v) && ($k == 'hero')) { //Hero block. Specify an image component.
                    //hero style
                    if ($v['bgColor']) {
                        $styles['hero']['backgroundColor'] = $v['bgColor'];
                    }

                    unset($v['bgColor']);

                    if ($v['backgroundColor']) {
                        $styles['hero']['backgroundColor'] = $v['backgroundColor'];
                    }

                    if (!empty($v['separator'])) {
                        $styles['hero']['separator'] = true;
                    }

                    if ($v['separatorColor']) {
                        $styles['hero']['separatorColor'] = $v['separatorColor'];
                    }

                    ##

                    $hero = $this->createFlexBox($v);
                } else if (!empty($v) && ($k == 'body')) { //Body block. Specify a box component.
                    //body style
                    if ($v['bgColor']) {
                        $styles['body']['backgroundColor'] = $v['bgColor'];
                    }

                    unset($v['bgColor']);

                    if ($v['backgroundColor']) {
                        $styles['body']['backgroundColor'] = $v['backgroundColor'];
                    }

                    if (!empty($v['separator'])) {
                        $styles['body']['separator'] = true;
                    }

                    if ($v['separatorColor']) {
                        $styles['body']['separatorColor'] = $v['separatorColor'];
                    }

                    ##

                    $body = $this->createFlexBox($v);
                    // $body = array_merge($body, array('spacing' => 'md')) ;
                } else if (!empty($v) && ($k == 'footer')) { //Footer block. Specify a box component.
                    //footer style
                    if ($v['bgColor']) {
                        $styles['footer']['backgroundColor'] = $v['bgColor'];
                    }

                    unset($v['bgColor']);

                    if ($v['backgroundColor']) {
                        $styles['footer']['backgroundColor'] = $v['backgroundColor'];
                    }

                    if (!empty($v['separator'])) {
                        $styles['footer']['separator'] = true;
                    }

                    if ($v['separatorColor']) {
                        $styles['footer']['separatorColor'] = $v['separatorColor'];
                    }

                    ##

                    $footer = $this->createFlexBox($v);
                    // $footer = array_merge($footer, array('spacing' => 'md')) ;
                } else if (!empty($v) && ($k == 'styles')) { //Style of each block. Specify a bubble style object. For more information, see Objects for the block style.
                    $styles = $this->createFlexBox($v);
                }
            }
            ##

            // $po_data = array('type' => 'bubble') ;
            $template = array();

            if (!empty($direction)) {
                $template = array_merge($template, array('direction' => $direction));
            }

            if (!empty($header)) {
                $template = array_merge($template, array('header' => $header));
            }

            if (!empty($hero)) {
                $template = array_merge($template, array('hero' => $hero));
            }

            if (!empty($body)) {
                $template = array_merge($template, array('body' => $body));
            }

            if (!empty($footer)) {
                $template = array_merge($template, array('footer' => $footer));
            }

            if (!empty($styles)) {
                $template = array_merge($template, array('styles' => $styles));
            }

            return $template;
        }
        ##
    }
    ##

    //create button, icon, image, text, box, filler, separator, spacer
    private function createFlexBox($arr = array(), $recursive = '')
    {
        $box = array();

        foreach ($arr as $k => $v) {
            if (($k == 'type') && ($v == 'box')) {
                if (!preg_match("/^[h|v|b]$/isu", $arr['layout'])) {
                    unset($arr['layout']);
                    $box = array_merge($box, array('layout' => 'vertical'));
                }
                $box = array_merge($box, array('type' => $v));
            } else if ($k == 'weight') {
                $v = strtolower($v);
                if (strtolower($v) == 'y') {
                    $box = array_merge($box, array('weight' => 'bold'));
                } else {
                    $box = array_merge($box, array('weight' => 'regular'));
                }
            } else if (($k == 'contents') && (count($v) > 0)) {
                $tmpBox = array();
                foreach ($v as $ka => $va) {
                    $tmpBox[] = $this->createFlexBox($va);
                }

                if (!empty($tmpBox)) {
                    $box = array_merge($box, array('contents' => $tmpBox));
                }

            } else if (($k == 'action') && (count($v) > 0)) {
                $box = array_merge($box, array('action' => $this->actionsBuild($v)));
            } else if ($k == 'btnTextColor') {
                if (strtoupper($v) == 'B') {
                    $box = array_merge($box, array('style' => 'secondary'));
                } else if (strtoupper($v) == 'W') {
                    $box = array_merge($box, array('style' => 'primary'));
                }

            } else if ($k == 'btnColor') {
                if (!empty($v)) {
                    $box = array_merge($box, array('color' => $v));
                }

            } else if ($k == 'layout') {
                if (strtoupper($v) == 'H') {
                    $box = array_merge($box, array('layout' => 'horizontal'));
                } else if (strtoupper($v) == 'V') {
                    $box = array_merge($box, array('layout' => 'vertical'));
                } else if (strtoupper($v) == 'B') {
                    $box = array_merge($box, array('layout' => 'baseline'));
                }

            } else if ($k == 'wrap') {
                // if (strtoupper($v) == 'Y') $box = array_merge($box, array('wrap' => true)) ;
                if ($v) {
                    $box = array_merge($box, array('wrap' => true));
                }

            } else if ($k == 'align') {
                if (strtolower($v) == 'l') {
                    $box = array_merge($box, array('align' => 'start'));
                } else if (strtolower($v) == 'c') {
                    $box = array_merge($box, array('align' => 'center'));
                } else if (strtolower($v) == 'r') {
                    $box = array_merge($box, array('align' => 'end'));
                }

            } else if ($k == 'txtColor') {
                if (preg_match("/^\#\w{6}$/isu", $v)) {
                    $box = array_merge($box, array('color' => $v));
                }

            } else if ($k == 'txtSize') {
                if (!empty($v)) {
                    $box = array_merge($box, array('size' => $v));
                }

            } else {
                $box = array_merge($box, array($k => $v));
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
    private function actionsBuild($arr = array())
    {
        $post_data = array();

        if (empty($arr)) {
            return false;
        } else {
            $acts = array();

            foreach ($arr as $k => $v) {
                if ($k == 'postback') {
                    $displayText = $v['displayText'];
                    $displayText = empty($displayText) ? $v['text'] : $displayText;

                    $acts = array(
                        'type'        => $k,
                        'label'       => $v['label'],
                        'data'        => $v['data'],
                        'displayText' => $displayText,
                    );
                } else if ($k == 'message') {
                    $acts = array(
                        'type'  => $k,
                        'label' => $v['label'],
                        'text'  => $v['text'],
                    );
                } else if ($k == 'uri') {
                    $acts = array(
                        'type'  => $k,
                        'label' => $v['label'],
                        'uri'   => $v['uri'],
                    );
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
    private function flexJson($arr = array())
    {
        $contents = array();
        $contents = json_decode($arr['contents'], true);

        $template = array(
            'type'     => 'flex',
            'altText'  => $arr['altText'],
            'contents' => $contents,
        );

        return $template;
    }
    ##

    //Quick Reply
    private function quickReply($arr = array())
    {
        $items = array();
        foreach ($arr['items'] as $k => $v) {
            $items[] = array(
                'type'     => 'action',
                'imageUrl' => $v['image'],
                'action'   => $this->actionsConstructRest($v['action']),
            );
        }

        $temp = array();
        if (!empty($arr['text'])) {
            $temp = array(
                'type' => 'text',
                'text' => $arr['text'],
            );
        } else if (!empty($arr['sticker'])) {
            $temp = $this->stickerConstruct($arr['sticker']);
        } else if (!empty($arr['image'])) {
            $temp = $this->imageConstruct($arr['image']);
        } else if (!empty($arr['video'])) {
            $temp = $this->videoConstruct($arr['video']);
        } else if (!empty($arr['audio'])) {
            $temp = $this->audioConstruct($arr['audio']);
        } else if (!empty($arr['location'])) {
            $temp = $this->locationConstruct($arr['location']);
        } else if (!empty($arr['imagemap'])) {
            $temp = $this->imagemapConstruct($arr['imagemap']);
        } else if (!empty($arr['confirm'])) {
            $temp = $this->confirmConstruct($arr['confirm']);
        } else if (!empty($arr['button'])) {
            $temp = $this->buttonConstruct($arr['button']);
        } else if (!empty($arr['carousel'])) {
            $temp = $this->carouselConstruct($arr['carousel']);
        } else if (!empty($arr['imagecarousel'])) {
            $temp = $this->imageCarouselConstruct($arr['imagecarousel']);
        } else if (!empty($arr['flexJson'])) {
            $temp = $this->flexJson($arr['flexJson']);
        }

        $template = array_merge($temp, array('quickReply' => array('items' => $items)));

        return $template;
    }
    ##

    //建構 REST 版 actions
    protected function actionsConstructRest($arr = array())
    {
        if (empty($arr)) {
            return false;
        } else {
            $acts = array();

            $v = $arr;
            if ($v['type'] == 'postback') {
                $displayText = $v['displayText'];
                $displayText = empty($displayText) ? $v['text'] : $displayText;

                $acts = array(
                    'type'        => $v['type'],
                    'label'       => $v['label'],
                    'data'        => $v['data'],
                    'displayText' => $displayText,
                );
            } else if ($v['type'] == 'message') {
                $acts = array(
                    'type'  => $v['type'],
                    'label' => $v['label'],
                    'text'  => $v['text'],
                );
            } else if ($v['type'] == 'uri') {
                $acts = array(
                    'type'  => $v['type'],
                    'label' => $v['label'],
                    'uri'   => $v['uri'],
                );
            } else if ($v['type'] == 'datetimepicker') {
                $v['initial'] = trim($v['initial']);
                $v['max']     = trim($v['max']);
                $v['min']     = trim($v['min']);

                $v['initial'] = preg_replace("/ /", 'T', $v['initial']);
                $v['max']     = preg_replace("/ /", 'T', $v['max']);
                $v['min']     = preg_replace("/ /", 'T', $v['min']);

                $acts = array(
                    'type'    => $v['type'],
                    'label'   => $v['label'],
                    'data'    => $v['data'],
                    'mode'    => $v['mode'],
                    'initial' => $v['initial'],
                    'max'     => $v['max'],
                    'min'     => $v['min'],
                );
            } else if ($v['type'] == 'camera') {
                $acts = array(
                    'type'  => $v['type'],
                    'label' => $v['label'],
                );
            } else if ($v['type'] == 'cameraRoll') {
                $acts = array(
                    'type'  => $v['type'],
                    'label' => $v['label'],
                );
            } else if ($v['type'] == 'location') {
                $acts = array(
                    'type'  => $v['type'],
                    'label' => $v['label'],
                );
            }

            return $acts;
        }
    }
    ##

    //Get User Profile
    private function GetProfile($token)
    {
        $url = 'https://api.line.me/v2/bot/profile/' . $token;

        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => "Authorization: Bearer " . $this->channel_access_token . "\r\n",
            ),
        );

        $context = stream_context_create($opts);
        $res     = @file_get_contents($url, false, $context);

        return $res;
    }
    ##

    //Get Room User Profile
    private function GetRoomProfile($roomId, $token)
    {
        $url = 'https://api.line.me/v2/bot/room/' . $roomId . '/member/' . $token;

        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => "Authorization: Bearer " . $this->channel_access_token . "\r\n",
            ),
        );

        $context = stream_context_create($opts);
        $res     = @file_get_contents($url, false, $context);

        return $res;
    }
    ##

    //Get Member User Profile
    private function GetMemberProfile($groupId, $token)
    {
        $url = 'https://api.line.me/v2/bot/group/' . $groupId . '/member/' . $token;

        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => "Authorization: Bearer " . $this->channel_access_token . "\r\n",
            ),
        );

        $context = stream_context_create($opts);
        $res     = @file_get_contents($url, false, $context);

        return $res;
    }
    ##

    //Get group summary
    private function GetGroupSummary($groupId)
    {
        $url = 'https://api.line.me/v2/bot/group/' . $groupId . '/summary';

        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => "Authorization: Bearer " . $this->channel_access_token . "\r\n",
            ),
        );

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
        if (!is_dir($log)) {
            mkdir($log, 0777, true);
        }

        $log .= '/error_' . date("Y-m-d") . '.log';

        $json = json_encode(array('stauts' => $status, 'message' => $message, 'channel_id' => $this->channel_id, 'userId' => $this->userId, 'datetime' => date("Y-m-d H:i:s") . '.' . microtime()), JSON_UNESCAPED_UNICODE);
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
        if (empty($this->reply_token)) {
            $json = $this->push($this->userId, $post_data, $tracking);
        } else {
            $json = $this->reply($this->reply_token, $post_data, $tracking);

            $response = json_decode($json, true);
            if (empty($response['sentMessages'])) {
                $json = $this->push($this->userId, $post_data, $tracking);
            }

        }

        return $json;
    }
    ##

    //curl 發送
    private function curl($target, $way = 'push', $post_data = array(), $mehod = 'POST', $tracking = array())
    {
        $this->headers = [];

        //請求 log
        $request_log = $this->log_path . '/request';
        if (!is_dir($request_log)) {
            $tf = mkdir($request_log, 0777, true);
        }

        $request_log .= '/request_' . date("Y-m-d") . '.log';
        ##

        if (empty($this->channel_access_token)) {
            $this->stop_action(400, '未指定 channel access token');
        }

        $way   = strtolower($way);
        $mehod = strtoupper($mehod);

        $header = array(
            'Authorization: Bearer {' . $this->channel_access_token . '}',
            'Content-Type: application/json; charset=utf-8',
        );

        $post_data['messages'] = $post_data;

        if ($way == 'reply') {
            $post_data['replyToken'] = $target;
        } else if ($way == 'push') {
            $post_data['to'] = $target;

            if (is_array($target)) {
                $way = 'multicast';
            } else if (strtolower($target) == 'broadcast') {
                $way = 'broadcast';
                unset($post_data['to']);
            }
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
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, [$this, 'readHeader']);

        $response = curl_exec($ch);

        $response_header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $response_header      = substr($response, 0, $response_header_size);
        $result               = substr($response, $response_header_size);

        curl_close($ch);

        $process_log = 'End-Point:' . "\n" . $url . "\n";
        $process_log .= 'Request:' . "\n" . json_encode($post_data, JSON_UNESCAPED_UNICODE) . "\n";
        $process_log .= 'Response(Status):' . "\n" . $response_header;
        $process_log .= 'Response(Header):' . "\n" . json_encode($this->headers, JSON_UNESCAPED_UNICODE) . "\n";
        $process_log .= 'Response(Body):' . "\n" . json_encode($result, JSON_UNESCAPED_UNICODE) . "\n";

        file_put_contents($request_log, date("Y-m-d H:i:s") . "\n" . $process_log . "\n", FILE_APPEND);

        if (($result == '{}') && !empty($tracking) && is_file(__DIR__ . '/track.php')) {
            $vars = base64_encode(json_encode(array_merge($tracking, ['reply' => json_encode($post_data)])));
            $cmd  = 'nohup php -f ' . __DIR__ . '/track.php ' . $vars . ' > /dev/null &';
            $res  = shell_exec($cmd);
        }

        return $result;
    }
    ##

    //取得header資訊
    private function readHeader($ch, $header)
    {
        $this->headers[] = $header;
        return strlen($header);
    }
    ##
}

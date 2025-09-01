<?php
require_once dirname(__DIR__) . '/includes/encode.php';
/**
 *
 */
class LineMsg
{

    private $lineUrl = "https://firstbotnew.azurewebsites.net/";
    public $webUrl   = 'https://www.first1.com.tw';

    public function __construct()
    {

    }

    public function sendButtonTemplateMsg($lineId, $url, $title, $text, $label, $img = '')
    {

        // $lineStr = enCrypt('lineId='.$lineId.'&s=SC0224&c=O');

        $data['lineId']    = $lineId;
        $data['btn_url']   = $url;
        $data['title']     = $title;
        $data['text']      = $text;
        $data['btn_label'] = $label;
        if ($img) {
            $data['img'] = $img;
        }

        $url = $this->lineUrl . "/bot/api/linePushBubble.php?v=" . enCrypt(json_encode($data));
        // echo $url;
        //
        file_get_contents($url);
        // die;

    }

    public function sendFlexTemplateMsg($data, $cat = 1)
    {

        //1:普通版
        // $lineStr = enCrypt('lineId='.$lineId.'&s=SC0224&c=O');
        $data['cat'] = $cat;

        // $data['lineId'] = $lineId;
        // $data['url'] = $url;
        // $data['title'] =$title;
        // $data['text'] = $text;
        // $data['label'] = $label;
        // if ($img) {
        //     $data['img'] = $img;
        // }

        $url = $this->lineUrl . "/bot/api/lineFlexTemplates.php?v=" . enCrypt(json_encode($data));
        // echo $url;
        $res = file_get_contents($url);

        return json_decode($res);

        //
        // file_get_contents($url);
        // die;

    }
}

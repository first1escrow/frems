<?php
// require_once dirname(dirname(__DIR__)) . '/.env.php';

trait OcrParser
{
    public static function parse($fileUrl)
    {
        $url               = 'https://first1.accuhit.com.tw/api/convts/'; //20220627 0624 通知修改網址
        $ts                = time();
        $time              = floor($ts / 300);
        $postData          = array();
        $postData['src']   = $fileUrl; //檔案URL
        $postData['key']   = md5("{$fileUrl}#{$time}"); //
        $postData['token'] = 'ddd4340c-ce78-40e9-a37f-0142c1f05b5b';

        $data_string = json_encode($postData);
        $data_string = str_replace("\/", "/", $data_string);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );

        $result          = curl_exec($ch);
        $response_header = curl_getinfo($ch);

        if ($response_header['http_code'] >= 400) {
            throw new Exception(json_encode($response_header, JSON_UNESCAPED_UNICODE));
        }
        curl_close($ch);

        return $result;
    }
}

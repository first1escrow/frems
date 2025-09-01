<?php
namespace First1\V1\Util;

//半形<=>全形
trait LineNotify
{
    //Line Notify
    public static function notify($key, $msg)
    {
        if ($msg == "") {
            return;
        }

        $body = array(
            'message' => PHP_EOL . $msg, //先斷行，避免跟 Bot 稱呼黏在一起
        );

        // 授權方式
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer ' . $key,
        );
        $url = 'https://notify-api.line.me/api/notify';

        $ch = curl_init();

        $params = array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13',
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => http_build_query($body),
        );

        curl_setopt_array($ch, $params);

        if (!$result = curl_exec($ch)) {
            if ($errno = curl_errno($ch)) {
                $error_message = curl_strerror($errno);
                // debug用
                // echo "cURL error ({$errno}):\n {$error_message}";
                curl_close($ch);
                return false;
            }
        } else {
            curl_close($ch);
            return true;
        }

    }
    ##
}

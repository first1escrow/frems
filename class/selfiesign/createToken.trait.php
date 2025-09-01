<?php
require_once dirname(dirname(__DIR__)) . '/configs/selfiesignSetting.php';

trait createToken
{
    public static function getToken()
    {
        //signature
        $logintime = time();
        $signature = CORPNO . ':' . BIZTYPE . ':' . USERID . ':' . $logintime . ':' . PASSWORD;

        //hash
        $signature = hash('sha256', $signature);

        //get token
        $url = URL . '/Token/create_ap_token';

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Basic ' . $signature,
        );

        $body = [
            "corpno"    => CORPNO,
            "biztype"   => BIZTYPE,
            "userid"    => USERID,
            "logintime" => $logintime,
            // "expire"    => "86400",
            "expire"    => "30",
        ];
        $body = json_encode($body);

        $ch = curl_init();

        $params = array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $body,
        );

        curl_setopt_array($ch, $params);

        $result = curl_exec($ch);
        if ($errno = curl_errno($ch)) {
            $error_message = curl_strerror($errno);

            return false;
        }

        curl_close($ch);
        $response = json_decode($result, true);

        return empty($response['token']['Access']) ? false : $response['token']['Access'];
    }
}
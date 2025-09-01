<?php
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/class/traits/slackIncomingWebhook.trait.php';

/**
 * 使用方式:
 *
 * 1. send message & image
 * desc: 透過指定 pId 或 lCode 發送訊息或圖片
 * @param $target  int or text (required: member id or uuid for line notify)
 * @param $message text (required: text for send)
 * @param $image   file (optional: image file from local disk. valid format: jpg, png)
 *
 * example:
 * $notify = new SendNotify();
 * $notify->send($target, $message, $image);
 *
 * 2. directly send message & image
 * desc: 直接指定 access token 發送訊息或圖片
 * @param $token   access token (required: access token for line notify)
 * @param $message text (required: text for send)
 * @param $image   file (optional: image file from local disk. valid format: jpg, png)
 *
 * example:
 * SendNotify::sendNotify($token, $message, $image);
 */
class SendNotify
{
    use SlackIncomingWebhook;

    private $conn;

    public function __construct()
    {
        $this->conn = new first1DB();

        return $this;
    }

    public function send($target, $message, $image = null)
    {
        if (empty($target)) {
            throw new Exception('No target defined');
            return false;
        }

        $token = $this->getTargetToken($target);
        if (empty($token)) {
            throw new Exception('No target founded');
            return false;
        }

        if (empty($message)) {
            throw new Exception('No message founded');
            return false;
        }

        return $this->sendNotify($token, $message, $image);
    }

    public function getTargetToken($target)
    {
        if (preg_match("/^\d+$/", $target)) {
            $sql = 'SELECT `lAccessToken` as token FROM `tLineNotify` WHERE `lStaffId` = :target;';
        } else {
            $sql = 'SELECT `lAccessToken` as token FROM `tLineNotify` WHERE `lCode` = :target;';
        }

        $rs = $this->conn->one($sql, ['target' => $target]);

        return empty($rs['token']) ? false : $rs['token'];
    }

    public static function sendNotify($token, $message, $image = null)
    {
        //20241009: 因應 line notify 關閉，改用 slack 通知
        self::incomingWebhook($message);

        $posts = [
            'message' => $message,
        ];

        if (!empty($image)) {
            $posts['imageFile'] = new CURLFILE($image);
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://notify-api.line.me/api/notify',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $posts,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $token,
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}

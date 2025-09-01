<?php
namespace First1\V1\Staff;

require_once dirname(__DIR__) . '/.env.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once __DIR__ . '/LineBotRequest.php';
require_once __DIR__ . '/traits/pusher.trait.php';
// require_once __DIR__ . '/traits/LineNotify.traits.php';

class StaffNotify
{
    use \Pusher;
    // use \First1\V1\Util\LineNotify;

    public static function send($member_id, $message)
    {
        $pusher = self::pusher($member_id, $message);
        // $notify = self::lineNotify($member_id, $message); //20250311 關閉LineNotify
        $line = self::line($member_id, $message);

        $pusher = empty($pusher) ? 'FAIL' : 'SUCCESS';
        // $notify = empty($notify) ? 'FAIL' : 'SUCCESS';
        $line = empty($line) ? 'FAIL' : 'SUCCESS';

        // return json_encode(['pusher' => $pusher, 'notify' => $notify, 'line' => $line], JSON_UNESCAPED_UNICODE);
        return json_encode(['pusher' => $pusher, 'line' => $line], JSON_UNESCAPED_UNICODE);
    }

    public static function pusher($member_id, $message, $debug = false)
    {
        $event   = 'first1-notify';
        $channel = $event . '-' . $member_id;

        $response = self::trigger($channel, $event, $message, $debug);
        $request  = json_encode(['member_id' => $member_id, 'channel' => $channel, 'event' => $event, 'message' => $message], JSON_UNESCAPED_UNICODE);

        $log = dirname(__DIR__) . '/log/staff/pusher';
        if (! is_dir($log)) {
            mkdir($log, 0777, true);
        }
        $log .= '/' . date("Ymd") . '.log';

        file_put_contents($log, date('Y-m-d H:i:s') . PHP_EOL . 'Requst:' . PHP_EOL . $request . PHP_EOL . 'Response:' . $response . PHP_EOL . PHP_EOL, FILE_APPEND);

        return $response;
    }

    // public static function lineNotify($member_id, $message)
    // {
    //     $token = self::getNotfyToken($member_id);
    //     if (empty($token)) {
    //         $log = dirname(__DIR__) . '/log/lineNotify';
    //         if (!is_dir($log)) {
    //             mkdir($log, 0777, true);
    //         }
    //         $log .= '/token_error_' . date("Ymd") . '.log';

    //         file_put_contents($log, date('Y-m-d H:i:s') . ' Error: No line notify token founded. Member_id = ' . $member_id . PHP_EOL, FILE_APPEND);

    //         return false;
    //     }

    //     $response = self::notify($token, $message);
    //     $request  = json_encode(['member_id' => $member_id, 'token' => $token, 'message' => $message], JSON_UNESCAPED_UNICODE);

    //     $log = dirname(__DIR__) . '/log/staff/lineNotify';
    //     if (!is_dir($log)) {
    //         mkdir($log, 0777, true);
    //     }
    //     $log .= '/' . date("Ymd") . '.log';

    //     file_put_contents($log, date('Y-m-d H:i:s') . PHP_EOL . 'Request:' . PHP_EOL . $request . PHP_EOL . 'Response:' . PHP_EOL . $response . PHP_EOL . PHP_EOL, FILE_APPEND);

    //     return $response;
    // }

    // public static function getNotfyToken($member_id)
    // {
    //     $conn = new \first1DB;

    //     $sql = 'SELECT lAccessToken FROM tLineNotify WHERE lAccessToken = :pId AND lStatus = "Y";';
    //     if (is_numeric($member_id)) {
    //         $sql = 'SELECT lAccessToken FROM tLineNotify WHERE lStaffId = :pId AND lNotifyTargetType = "USER" AND lStatus = "Y";';
    //     }
    //     $data = $conn->one($sql, ['pId' => $member_id]);

    //     return empty($data['lAccessToken']) ? null : $data['lAccessToken'];
    // }

    public static function line($member_id, $message)
    {
        global $env;

        $token = self::getLineToken($member_id);
        if (empty($token)) {
            $log = dirname(__DIR__) . '/log/line';
            if (! is_dir($log)) {
                mkdir($log, 0777, true);
            }
            $log .= '/token_error_' . date("Ymd") . '.log';

            file_put_contents($log, date('Y-m-d H:i:s') . ' Error: No line token founded. Member_id = ' . $member_id . PHP_EOL, FILE_APPEND);

            return false;
        }

        $response = self::linePush($token, $message);
        $request  = json_encode(['member_id' => $member_id, 'token' => $token, 'message' => $message], JSON_UNESCAPED_UNICODE);

        $log = dirname(__DIR__) . '/log/staff/line';
        if (! is_dir($log)) {
            mkdir($log, 0777, true);
        }
        $log .= '/' . date("Ymd") . '.log';

        file_put_contents($log, date('Y-m-d H:i:s') . PHP_EOL . 'Request:' . PHP_EOL . $request . PHP_EOL . 'Response:' . PHP_EOL . $response . PHP_EOL . PHP_EOL, FILE_APPEND);

        return $response;
    }

    public static function getLineToken($member_id)
    {
        $conn = new \first1DB;

        $sql  = 'SELECT lLineId FROM tLineAccount WHERE lStatus = "Y" AND lpId = :pId ORDER BY lId DESC LIMIT 1;';
        $data = $conn->one($sql, ['pId' => $member_id]);

        return empty($data['lLineId']) ? null : $data['lLineId'];
    }

    private function linePush($token, $message)
    {
        global $env;

        $bot = new \LineBotRequest($env['line']['channel_id'], $env['line']['channel_secret'], $env['line']['channel_access_token'], dirname(__DIR__) . '/log/line');

        $request = [];

        $request['userId']     = $token;
        $request['messages'][] = [
            'actionType' => 'text',
            'text'       => $message,
        ];

        $response = $bot->send($request);
        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }
}

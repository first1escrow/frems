<?php
require_once dirname(dirname(__DIR__)) . '/.env.php';
require_once dirname(dirname(__DIR__)) . '/pusher/vendor/autoload.php';

trait Pusher
{
    /**
     * 推送訊息
     * @param string $channel 頻道名稱
     * @param string $event 事件名稱
     * @param string $message 訊息內容
     * @param bool $debug 是否顯示錯誤訊息
     * @return bool
     */
    public static function trigger($channel, $event, $message, $debug = false)
    {
        global $env;

        if (empty($channel) || empty($event) || empty($message)) {
            return empty($debug) ? false : 'Parameter error';
        }

        $channels = self::getChannels();
        if (empty($channels) || !in_array($channel, $channels)) {
            return empty($debug) ? false : 'Channel not found';
        }

        $message = [
            'alert' => $message,
        ];

        $pusher = new Pusher\Pusher($env['pusher']['key'], $env['pusher']['secret'], $env['pusher']['app_id'], ['cluster' => $env['pusher']['cluster']]);
        return $pusher->trigger($channel, $event, json_encode($message));
    }

    /**
     * 取得頻道列表
     * @return array|bool 頻道列表
     */
    public static function getChannels()
    {
        global $env;

        $pusher   = new Pusher\Pusher($env['pusher']['key'], $env['pusher']['secret'], $env['pusher']['app_id'], ['cluster' => $env['pusher']['cluster']]);
        $response = $pusher->get('/channels');

        if ($response['status'] == 200) {
            $channels    = $response['result']['channels'];
            $channelList = [];
            foreach ($channels as $channel => $data) {
                $channelList[] = $channel;
            }

            return $channelList;
        }

        return false;
    }
}

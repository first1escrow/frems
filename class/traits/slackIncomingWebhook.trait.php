<?php

trait SlackIncomingWebhook
{
    /**
     * Slack Incoming Webhook
     * @param string $message 訊息
     * @param string $webhook Webhook URL
     * @param string $username Username
     * @return bool 是否成功
     */
    public static function incomingWebhook($message, $webhook = 'https://hooks.slack.com/services/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', $username = '系統通知')
    {
        if (empty($message)) {
            return false;
        }

        $body = array(
            'text' => PHP_EOL . $message, //先斷行，避免跟 Bot 稱呼黏在一起
            'username' => $username,
        );

        // 授權方式
        $headers = array(
            'Content-Type: application/json',
        );
        $url = $webhook;

        $ch = curl_init();

        $params = array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($body),
        );
        curl_setopt_array($ch, $params);

        if ($response = curl_exec($ch) === false) {
            throw new \Exception('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);

        return ($response == 'ok') ? true : false;
    }
}

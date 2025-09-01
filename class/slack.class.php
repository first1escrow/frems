<?php
namespace First1\V1\Notify;

require_once __DIR__ . '/traits/slackIncomingWebhook.trait.php';

class Slack
{
    use \SlackIncomingWebhook;

    private static $instance = null;
    private $token, $channelToken;
    private $url = 'https://slack.com/api';

    private function __construct()
    {
    }

    /**
     * Get instance
     * @param string $token Token
     */
    public static function getInstance($token = null, $channelToken = null)
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        if (! is_null($token)) {
            self::$instance->setToken($token);
        }

        if (! is_null($channelToken)) {
            self::$instance->setChannelToken($channelToken);
        }

        return self::$instance;
    }

    /**
     * Set token
     * @param string $token Token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Set channel token
     * @param string $token Token
     */
    public function setChannelToken($token)
    {
        $this->channelToken = $token;
    }
    /**
     * Get token
     * @return string Token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Get channels
     * @return mixed Response data
     */
    public function getChannels()
    {
        $url  = $this->url . '/conversations.list';
        $data = [
            'types' => 'public_channel,private_channel',
        ];

        return $this->curl($url, $data, 'GET');
    }

    /**
     * Get users
     * @return mixed Response data
     */
    public function getUsers()
    {
        $url = $this->url . '/users.list';

        return $this->curl($url, [], 'GET');
    }

    /**
     * Send message to Slack channel or user
     * @param string $message Message
     * @param string $channel Channel or user id
     * @return bool Success or not
     */
    public function chatPostMessage($message, $channel)
    {
        if (empty($this->token)) {
            throw new \Exception('Token is required');
        }

        if (empty($message) || empty($channel)) {
            throw new \Exception('Parameter error');
        }

        $url  = $this->url . '/chat.postMessage';
        $data = [
            'channel' => $channel,
            'text'    => $message,
        ];

        return $this->curl($url, $data);
    }

    /**
     * Curl request
     * @param string $url Request URL
     * @param array $data Request data
     * @param string $method Request method
     * @return mixed Response data
     */
    private function curl($url, $data, $method = 'POST')
    {
        $ch = curl_init();

        $header = [
            'Content-Type: application/json;charset=utf-8',
        ];

        if (preg_match('/conversations.list/', $url)) {
            if (empty($this->token)) {
                throw new \Exception('Token is required');
            }

            $header[] = 'Authorization: Bearer ' . $this->channelToken;
        } else {
            if (empty($this->token)) {
                throw new \Exception('Token is required');
            }

            $header[] = 'Authorization: Bearer ' . $this->token;
        }

        $params = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER     => $header,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_CUSTOMREQUEST  => $method,
        ];
        curl_setopt_array($ch, $params);

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        if ($method == 'GET') {
            curl_setopt($ch, CURLOPT_POST, false);

            if (! empty($data)) {
                $url .= '?' . http_build_query($data);
            }
        }

        curl_setopt($ch, CURLOPT_URL, $url);

        $response = curl_exec($ch);
        if ($response === false) {
            throw new \Exception('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);

        return $response;
    }

    /**
     * Incoming Webhook for Slack channel
     * @param string $message Message
     * @param string $webhook Webhook URL
     * @param string $username Specific Username
     * @return bool Success or not
     */
    public static function channelSend($message, $webhook = 'https://hooks.slack.com/services/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', $username = '系統通知')
    {
        return self::incomingWebhook($message, $webhook, $username);
    }

}

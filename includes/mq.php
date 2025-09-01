<?php
/**
 * @param: topic
 * @param: data(array or string)
 */
class MQ
{
    public static function push($topic, $data)
    {
        $json = json_encode([
            'topic' => $topic,
            'data'  => $data,
        ], JSON_UNESCAPED_UNICODE);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL             => 'http://10.10.1.196/mq/send.php',
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => 'POST',
            CURLOPT_POSTFIELDS      => $json,
            CURLOPT_HTTPHEADER      => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}

?>
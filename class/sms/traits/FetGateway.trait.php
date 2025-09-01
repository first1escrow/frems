<?php

/**
 * SMS Gateway Trait
 * 遠傳簡訊發送
 */
trait FetGateway
{
    /**
     * 遠傳簡訊發送
     *
     * @param array  $fet_setting 遠傳簡訊設定
     * @param string $mobile      手機號碼
     * @param string $txt         簡訊內容
     * @param string $insert_id   簡訊檢查ID
     * @param string $log         紀錄檔位置
     * @return string             簡訊發送結果
     */
    public static function fetSend($fet_setting, $mobile, $txt, $insert_id = '', $log = null)
    {
        // $mobile = '0922785490'; //測試用手機號碼

        $url = 'http://61.20.32.60:6600/mpushapi/smssubmit'; //遠傳API網址

        $fet_SysId      = $fet_setting['fet_SysId'];      //API帳號代號
        $fet_SrcAddress = $fet_setting['fet_SrcAddress']; //發送訊息的來源位址(20個數字)
        $message        = '';

        //編輯傳送簡訊字串
        $message = '<?xml version="1.0" encoding="UTF-8"?>' .
        '<SmsSubmitReq>' .
        '<SysId>' . $fet_SysId . '</SysId>' .
        '<SrcAddress>' . $fet_SrcAddress . '</SrcAddress>' .
        '<DestAddress>' . $mobile . '</DestAddress>' .
        '<SmsBody>' . base64_encode($txt) . '</SmsBody>' .
            '<DrFlag>true</DrFlag>' .
            '</SmsSubmitReq>';

        //透過GET方式、開始傳送欲發送的簡訊資料
        $url .= '?xml=' . urlencode($message);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output     = curl_exec($ch);
        $outputInfo = curl_getinfo($ch);

        $log = empty($log) ? dirname(dirname(dirname(__DIR__))) . '/log/sms/v1/send' : $log;
        if (! is_dir($log)) {
            mkdir($log, 0777, true);
        }
        $log .= '/' . date('Ymd') . '.log';
        file_put_contents($log, date('Y-m-d H:i:s') . "\nRequest: \n" . $message . "\n\nCurl error no: " . curl_errno($ch) . "\n\ntSMS_Check insert_id: " . $insert_id . "\n\nResponse: " . print_r($output, true) . "\n\nResponse Info: " . print_r($outputInfo, true) . "\n\n", FILE_APPEND);

        if (curl_errno($ch)) {
            self::incomingWebhook('簡訊發送錯誤(fetSend)!! tSMS_Check insert_id: ' . $insert_id . ', Mobile: ' . $mobile . ', Curl Id:' . curl_errno($ch) . ', Error:' . curl_error($ch));
            return false;
        }

        curl_close($ch);

        return $output;
    }

    /**
     * 簡訊檢查登錄到資料庫中
     * @param  object $conn   資料庫連線
     * @param  string $mdn    手機號碼
     * @param  string $msisdn 遠傳簡訊查詢ID
     * @return int           簡訊檢查ID
     */
    private function insertToSMSCheck(&$conn, $mdn = '', $msisdn = '')
    {
        if (empty($conn)) {
            self::incomingWebhook('insertToSMSCheck!! DB connection is empty');
            throw new Exception('DB connection is empty');
        }

        $sql = 'INSERT INTO tSMS_Check (tChecked, tMDN, tMSISDN, tRegistTime) VALUES ("n", "' . $mdn . '", "' . $msisdn . '", "' . date("Y-m-d H:i:s") . '");';
        if ($conn->exeSql($sql)) {
            return $conn->lastInsertId();
        }

        self::incomingWebhook('insertToSMSCheck!! Failed to insert tSMS_Check');
        throw new Exception('Failed to insert tSMS_Check');
    }

    /**
     * 更新簡訊檢查資料
     * @param  object $conn      資料庫連線
     * @param  int    $id        簡訊檢查ID
     * @param  array  $parseData 解析資料
     * @return bool              更新結果
     */
    private function updateToSMSCheck(&$conn, $insert_id, $parseData)
    {
        if (empty($conn)) {
            self::incomingWebhook('updateToSMSCheck!! DB connection is empty');
            throw new Exception('DB connection is empty');
        }

        $messageId      = empty($parseData['messageId']) ? 'Fake_' . uniqid() : $parseData['messageId']; //遠傳簡訊查詢ID
        $reason         = empty($parseData['reason']) ? '' : $parseData['reason'];                       //簡訊發送結果
        $code           = empty($parseData['code']) ? '' : $parseData['code'];                           //簡訊發送結果代碼
        $sms_check_code = 'null';                                                                        //減少字數，所以用不到了

        $checked = 'y';
        if (! empty($parseData['code']) && in_array($parseData['code'], ['0', '00000'])) {
            $checked = 'n';
        }

        $sql = 'UPDATE
                    tSMS_Check
                SET
                    tTaskID = "' . $messageId . '",
                    tChecked = "' . $checked . '",
                    tReason = "' . addslashes($reason) . '",
                    tCode = "' . $code . '",
                    tRtnDateTime = "",
                    tSystem = "2",
                    tCheckCode="' . $sms_check_code . '",
                    tRegistTime="' . date("Y-m-d H:i:s") . '"
                WHERE
                    id="' . $insert_id . '" ;';
        if (! $conn->exeSql($sql)) {
            self::incomingWebhook('updateToSMSCheck!! Failed to update tSMS_Check');
            throw new Exception('Failed to update tSMS_Check');
        }

        return true;
    }

    /**
     * 更新簡訊檢查狀態
     * @param  int    $insert_id  簡訊檢查ID
     * @param  string $status     狀態
     * @return bool               更新結果
     */
    private function updateSMSCheckStatus(&$conn, $insert_id, $status = 'n')
    {
        if (empty($conn)) {
            self::incomingWebhook('updateSMSCheckStatus!! DB connection is empty');
            throw new Exception('DB connection is empty');
        }

        $sql = 'UPDATE tSMS_Check SET tChecked = :status WHERE id = :insert_id;';
        if (! $conn->exeSql($sql, ['status' => $status, 'insert_id' => $insert_id])) {
            self::incomingWebhook('updateSMSCheckStatus!! Failed to update tSMS_Check');
            throw new Exception('Failed to update tSMS_Check');
        }

        return true;
    }

    /** 解析取得簡訊發送結果
     * @param  string $output 簡訊發送結果
     * @return array          結果
     */
    private function parseOutput($output)
    {
        $output    = str_replace("\n", "", $output);
        $messageId = 'Fake_' . uniqid();

        $matches = [];
        if (preg_match("/<SubmitRes><ResultCode>(.*)<\/ResultCode><ResultText>(.*)<\/ResultText>(.*)<\/SubmitRes>/", $output, $matches)) {
            $code        = trim($matches[1]); //結果代碼
            $description = trim($matches[2]); //結果說明

            if ($code == '00000') {
                $match = [];
                preg_match("/<MessageId>(.*)<\/MessageId>/", $matches[3], $match); //遠傳簡訊查詢ID
                $messageId = trim($match[1]);

                $reason = '已發送';

                $logMessage = $reason;
                $logMessage .= ' [' . $messageId . ']';

                $errorCode = 's'; //發送成功、代碼"s"
            } else {
                $reason = '發送失敗';

                $logMessage = $reason . ' -[ ' . $description . ' ]' . "\n";
                $logMessage .= '************ error messages ************' . "\n";
                $logMessage .= $txt . "\n";
                $logMessage .= '****************************************' . "\n";

                $errorCode = $code;
            }
        } else {
            //網路連線錯誤失敗
            $reason     = '無法建立網路連線';
            $logMessage = $reason;

            $code = '99999';
            $code = $errorCode;
        }

        return [
            'code'        => $code,
            'description' => $description,
            'reason'      => $reason,
            'logMessage'  => $logMessage,
            'errorCode'   => $errorCode,
            'messageId'   => $messageId,
        ];
    }

    /**
     * 寫入簡訊發送紀錄
     * @param object $conn      資料庫連線
     * @param string $kind      簡訊類別 (income, income2)
     * @param string $cId       保證號碼
     * @param string $expenseId 支出編號
     * @param string $content   簡訊內容
     * @param string $mobile    手機號碼
     * @param string $name      姓名
     * @param string $messageId 遠傳簡訊查詢ID
     */
    private function insertSMSLog(&$conn, $kind, $cId, $expenseId, $content, $mobile, $name, $messageId)
    {
        if (empty($conn)) {
            self::incomingWebhook('insertSMSLog!! DB connection is empty');
            throw new Exception('DB connection is empty');
        }

        $content = preg_replace("/\'+/", "", $content);
        $content = preg_replace("/\"+/", "", $content);

        $sql = 'INSERT INTO
                    tSMS_Log
                (
                    tPID,
                    tKind,
                    tSMS,
                    tTo,
                    tName,
                    tTransId,
                    tTID,
                    sSend_Time
                ) VALUES (
                    "' . substr($cId, -9) . '",
                    "' . $kind . '",
                    "' . addslashes($content) . '",
                    "' . $mobile . '",
                    "' . $name . '",
                    "' . $expenseId . '",
                    "' . $messageId . '",
                    "' . date("Y-m-d H:i:s") . '"
                );';
        return $conn->exeSql($sql);
    }
}

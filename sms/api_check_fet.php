<?php
//簡訊系統發送 專用API
require_once dirname(dirname(__FILE__)) . '/openadodb.php';
require_once dirname(__FILE__) . '/sms_delivery_status_fet.php';
require_once dirname(__FILE__) . '/sms_return_code_fet.php';
require_once dirname(dirname(__FILE__)) . '/includes/checkPS.php';

//檢查是否已有排程執行中
if (checkPS('api_check_fet.php', 4)) {
    $msg = date("Y-m-d H:i:s") . " api_check_fet 排程已在執行中！本次排程觸發取消...";
    echo $msg . "\n\n";

    exit;
}

//搜尋未確認簡訊
$sql = 'SELECT
            a.*,
            b.sSend_Time
        FROM
            tSMS_Check AS a
        JOIN
            tSMS_Log as b ON a.tTaskID=b.tTID
        WHERE
            a.tChecked = "n"
            AND a.tSystem = "2"
            AND a.tTaskID NOT LIKE "Fake_%"
        ORDER BY
            a.id
        DESC LIMIT 300;';
$rs    = $conn->Execute($sql);
$total = $rs->RecordCount();

$count    = 0;
$ans      = "遠傳簡訊 API 檢查完成";
$body_txt = '';

//
while (! $rs->EOF) {
    $id = $rs->fields['id']; //欲查詢簡訊 tSMS_Check 的 id
    $ny = 'y';               //是否要繼續追蹤檢查訊息狀態 y:不檢查、n:繼續檢查

    $fet_SysId  = 'twhg5354';             //遠傳API帳號代碼
    $Q_mobile   = $rs->fields['tMSISDN']; //收訊方手機號碼
    $Q_msgid    = $rs->fields['tTaskID']; //遠傳電信 message id
    $ResultCode = '';
    $ResultText = '';

    //遠傳 URL
    $url = 'http://61.20.32.60:6600/mpushapi/smsquerydr';

    //透過 curl 發動查詢
    $sms_str = '<?xml version="1.0" encoding="UTF-8"?>' .
        '<SmsQueryDrReq>' .
        '<SysId>' . $fet_SysId . '</SysId>' .
        '<MessageId>' . $Q_msgid . '</MessageId>' .
        '<DestAddress>' . $Q_mobile . '</DestAddress>' .
        '</SmsQueryDrReq>';

    //進行curl發送
    $url .= '?xml=' . urlencode($sms_str);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    if ($res = curl_exec($ch)) {
        $res = str_replace("\n", "", $res);
        curl_close($ch);
    } else { //若連線失敗時，結束連線並發送簡訊
        $ans = "遠傳簡訊 API 檢查(連線)失敗\n";
        curl_close($ch);
        echo $ans . "\n";
        exit;
    }

    //檢核並取出 query 結果
    preg_match("/<SmsQueryDrRes><ResultCode>(.*)<\/ResultCode><ResultText>(.*)<\/ResultText>(.*)<\/SmsQueryDrRes>/", $res, $opt);
    $ResultCode = $opt[1];
    $ResultText = $opt[2];

    $arr    = explode('</Receipt>', $opt[3]);
    $arr[0] = preg_replace("/<Receipt>/", "", $arr[0]);

    preg_match("/^<MessageId>(.*)<\/MessageId><DestAddress>(.*)<\/DestAddress><DeliveryStatus>(.*)<\/DeliveryStatus>(.*)<SubmitDate>(.*)<\/SubmitDate>(.*)<Seq>/", $arr[0], $_data); //僅取第一組解析
    unset($opt, $arr);

    $MessageId      = $_data[1];
    $DestAddress    = $_data[2];
    $DeliveryStatus = $_data[3];
    $ErrorCode      = $_data[4];
    $SubmitDate     = $_data[5];
    $DoneDate       = $_data[6];

    //若簡訊查詢回傳錯誤時...
    $arrTmp = [];
    if ($ErrorCode) {
        preg_match("/<ErrorCode>(.*)<\/ErrorCode>/", $ErrorCode, $arrTmp);
        $ErrorCode = $arrTmp[1];
    }
    unset($arrTmp);

    //若簡訊發送成功時...
    $arrTmp = [];
    if ($DoneDate) {
        preg_match("/<DoneDate>(.*)<\/DoneDate>/", $DoneDate, $arrTmp);
        if ($arrTmp[1]) {
            $DoneDate = '20' . $arrTmp[1];
        }
    }
    unset($arrTmp);

    //更新資料庫的簡訊狀態
    if ($ResultCode) {
        //訊息查詢正常完成
        if ($ResultCode == '00000') {
            $ny     = $deliveryStatusArr[$DeliveryStatus]['ny'];
            $code   = $deliveryStatusArr[$DeliveryStatus]['code'];
            $reason = $deliveryStatusArr[$DeliveryStatus]['reason'];

            if ($ny != 'y') {
                $ny = 'n';
                $count++;
            }

            $sql = 'UPDATE
                        tSMS_Check
                    SET
                        tChecked="' . $ny . '",
                        tCode="' . $code . '",
                        tReason="' . $reason . '",
                        tDrDateTime="' . $DoneDate . '"
                    WHERE
                        tTaskID = "' . $MessageId . '";';
            $conn->Execute($sql);

            echo $_txt = date("Ymd H:i:s") . ' 本次更新：TID:' . $MessageId . ',TEL:' . $Q_mobile . ',ID:' . $id . ',Code=' . $code . ',Reason=' . $deliveryStatusArr[$DeliveryStatus]['reason'] . ',tChecked=' . $ny . "\n";
        } else {
            echo $_txt = date("Ymd H:i:s") . ' Return Code：' . $ResultCode . ' Reason：' . $return_code[$ResultCode] . "\n";
        }

        $body_txt .= "<br>" . $_txt;

        $ans = "遠傳簡訊 API 檢查完成\n";
    } else {
        echo '[' . date("Y-m-d H:i:s") . ']' . " 接收資料錯誤!!\n";
        $ans = "遠傳簡訊 API 檢查(連線)失敗\n";
    }

    $rs->MoveNext();
}

<?php
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/sms/sms_function_v2.php';
require_once dirname(__DIR__) . '/.env.php';
require_once dirname(__DIR__) . '/pusher/vendor/autoload.php';
require_once dirname(__DIR__) . '/notify/sendNotify.php';

$json = base64_decode($argv[1]);
$data = json_decode($json, true);

if (empty($data) || !is_array($data)) {
    exit;
}

$bId    = $data['bid'];
$target = $data['target'];
$msg    = $data['msg'];
$batch  = $data['batch'];
$member = $data['member'];

// $batch  = '43c4d1a3-3175-47db-81b7-2a6d1bc99eee';
// $bId    = 'SF01547,SF01548,SF01549,SF01550,SF01551,SF01552,SF01591,SF01605,SF01784,SF02134,SF02135,SF02348,SF02758,SF03007,SF03008,AA03091,SF03111,SF03269,SF03371,SF03511,AA03724,SF03946,SF03962,SF04312,SF04459,AA04620,SF04701,NR04839,AA05197,SF05305,SC0247,SC0750,SC1578';
// $target = '回饋金2';
// $msg    = '第一建經通知：111年第2季<first1>店家名稱</first1>回饋金已結算,請點下列網址至第一建經官網確認,並依辦法請款,謝謝。';

if (empty($bId)) {
    exit;
}

// $sms  = new SMS_Gateway_V2();
// $list = $sms->feedback($bId, $target, $msg);
$arr = explode(',', $bId);
if (empty($arr)) {
    exit;
}

$pusher = new Pusher\Pusher($env['pusher']['key'], $env['pusher']['secret'], $env['pusher']['app_id'], ['cluster' => $env['pusher']['cluster']]);

$conn = new first1DB;
$sms  = new SMS_Gateway_V2();

$total = count($arr);
foreach ($arr as $k => $bId) {
    $list = $sms->feedback($bId, $target, $msg);

    foreach ($list as $v) {
        if ($v['code'] == 'SC') {
            $v['code'] .= str_pad($v['sId'], 4, '0', STR_PAD_LEFT);
        } else {
            $v['code'] .= str_pad($v['bId'], 5, '0', STR_PAD_LEFT);
        }

        $sql = '
            INSERT INTO
                `tSMSWaitSend`
            SET
                `uuid`        = UUID(),
                `sBatch`      = :sBatch,
                `sKind`       = :sKind,
                `sBrand`      = :sBrand,
                `sStore`      = :sStore,
                `sTitle`      = :sTitle,
                `sName`       = :sName,
                `sMobile`     = :sMobile,
                `sBranch`     = :sBranch,
                `sSMS`        = :sSMS,
                `sReady`      = :sReady,
                `sSent`       = :sSent,
                `sCreated_at` = :dt
        ;';
        $conn->exeSql($sql, [
            'sBatch'  => $batch,
            'sKind'   => $target,
            'sBrand'  => $v['brand'],
            'sStore'  => $v['bStore'],
            'sTitle'  => $v['title'],
            'sName'   => $v['mName'],
            'sMobile' => $v['mMobile'],
            'sBranch' => $v['code'],
            'sSMS'    => $v['smsTxt'],
            'sReady'  => 'Y',
            'sSent'   => 'S',
            'dt'      => date("Y-m-d H:i:s"),
        ]);
    }

    $percent = ($k + 1) / $total * 100;
    $pusher->trigger('first1-feedback-sms', 'sms-shorturl', '已轉換：' . number_format($percent, 2, '.', ',') . '%');

    $list = $v = null;
    unset($list, $v);
}

//全部寫入完成-更新狀態
$sql = 'UPDATE `tSMSWaitSend` SET `sSent` = :sSent WHERE `sBatch` = :sBatch;';
$conn->exeSql($sql, ['sSent' => 'N', 'sBatch' => $batch]);

$conn = null;
unset($conn);

$pusher->trigger('first1-feedback-sms', 'sms-shorturl', 'FINISH');
##

//通知顯示
if (!empty($member)) {
    $message = '回饋金短網址轉換已完成！' . "\r\n\r\n" . '請至 "會計作業" -> "回饋金資料" -> "回饋金簡訊" 內查詢';

    $notify = new SendNotify();
    if (!empty($notify->getTargetToken($member))) {
        $notify->send($member, $message);
    }
}
##

exit;

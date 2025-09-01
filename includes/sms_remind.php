<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';

$path = dirname(__DIR__) . '/log2/smscheck';
if (!is_dir($path)) {
    mkdir($path, 0777, true);
}

if (file_exists($path . '/' . $_SESSION['member_id'] . '.log')) {
    $fr  = fopen($path . '/' . $_SESSION['member_id'] . '.log', 'r');
    $txt = fgets($fr);
    $now = date('Y-m-d H:i') . ":00";
    $ck  = strtotime(date('Y-m-d 17:00:00'));

    $txt   = strtotime($txt);
    $now_c = strtotime($now);

    if (($now_c == $txt) || ($ck < $now)) {
        exit;
    }

    fclose($fr);
}
unset($ck);

$fw = fopen($path . '/' . $_SESSION['member_id'] . '.log', 'w+');
fwrite($fw, date('Y-m-d H:i') . ":00");
fclose($fw);

// 打包後 未發簡訊的提醒要改一下
// 15分鐘就跳視窗 之後每5分鐘跳一次[20190326 改成之後每10分鐘跳一次]
// 一直到發簡訊為止

// 這個視窗要開給我/展宏/雪姐/小嘉/財務部/雄哥/政耀
// 我/展宏/雪姐/小嘉/財務部/雄哥/政耀
// 同時也是有權限可以勾選發送的人

$date = date("Y-m-d", strtotime("-7 days"));

$sql = 'SELECT
            tId,
            tVR_Code,
            tCode2,
            tBank_kind,
            tObjKind,
            tObjKind2,
            tMoney,
            tMemo,
            SUM(tMoney) as total_M,
            tKind,
            tExport_time,
            tExport_nu
        FROM
            tBankTrans
        WHERE
            tExport="1" AND
            tSend = 0 AND tExport_time >= "' . $date . '"
        GROUP BY
            tExport_nu;';
$rs = $conn->Execute($sql);

while (!$rs->EOF) {
    if ($rs->fields['tObjKind2'] != "02" && $rs->fields['tObjKind'] != "調帳" && $rs->fields['tKind'] != "利息" && $rs->fields['tCode2'] != "大額繳稅") {
        $list[] = $rs->fields;
    }

    $rs->MoveNext();
}

//確認是否有寄送 count($list)
$msg = '';
for ($i = 0; $i < count($list); $i++) {
    $checkTime5   = date('Y-m-d') . " 17:00:00";
    $tExport_time = substr($list[$i]['tExport_time'], 0, 16); //2017-05-11 15:03:01

    $check2 = 2;
    if (strtotime($tExport_time) > strtotime($checkTime5)) {
        $check2 = 1;
    }

    $dd = date('Y-m-d') . "09:15:00";
    if (strtotime($tExport_time) < strtotime($dd)) { //如果是前一天的就9點開始計算
        $tExport_time = $dd;
    }

    $sql   = "SELECT bId FROM tBankTransSmsLog WHERE bExport_nu ='" . $list[$i]['tExport_nu'] . "'";
    $rs    = $conn->Execute($sql);
    $check = $rs->RecordCount();

    if ($check == 0 && $check2 != 1) {
        $branch = '';
        if (substr($list[$i]['tVR_Code'], 0, 5) == '99985') { //西門
            $branch = '西門';
        } elseif (substr($list[$i]['tVR_Code'], 0, 5) == '99986') { //城中
            $branch = '城中';
        }

        $min = floor((strtotime($now) - strtotime($tExport_time)) / 60);

        //五點的打包 一律隔天再發送提醒
        if ($min >= 15 && $min < 20) {
            $msg .= $list[$i]['tExport_time'] . "出帳金額:" . number_format($list[$i]["total_M"]) . "元(" . $list[$i]['tBank_kind'] . $branch . ")";
            $msg .= "超過十五分鐘未發送簡訊(1、" . $list[$i]['tExport_nu'] . ")\r\n";
            // echo '超過十分鐘未發送簡訊(1)';
        } elseif ($min > 20 && ($min % 10) != 0) {
            $msg .= $list[$i]['tExport_time'] . "出帳金額:" . number_format($list[$i]["total_M"]) . "元(" . $list[$i]['tBank_kind'] . $branch . ")";
            $msg .= "超過十五分鐘未發送簡訊(2、" . $list[$i]['tExport_nu'] . ")\r\n";
        }
    }

}

echo $msg;

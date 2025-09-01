<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';

$now = date('Y-m-d H:i') . ":00";

//開太多視窗，所以要檢查有執行過了沒，同時間只要跳一次就好
$path = dirname(__DIR__) . '/log2/sms_error';
if (!is_dir($path)) {
    mkdir($path, 0777, true);
}

if (file_exists($path . '/' . $_SESSION['member_id'] . '.log')) {
    $fr  = fopen($path . '/' . $_SESSION['member_id'] . '.log', 'r');
    $txt = fgets($fr);

    $time  = strtotime($txt);
    $now_c = strtotime($now);

    if (($now_c == $time)) {
        die();
    }

    fclose($fr);
} else {
    $fw = fopen($path . '/' . $_SESSION['member_id'] . '.log', 'w+');
    fwrite($fw, $now);
    fclose($fw);
}

//每10分鐘一次
$target = strtotime('+10 minutes', strtotime($txt)); //檔案時間加10
$min    = floor((strtotime($now) - $target) / 60);

if ($min > 0) {
    $fw = fopen($path . '/' . $_SESSION['member_id'] . '.log', 'w+');
    fwrite($fw, $now);
    fclose($fw);
} else {
    exit;
}

##################################################################

if ($_SESSION['member_id'] == 6) {
    $str = '';
} else {
    $str = ' AND s.sUndertaker1 = "' . $_SESSION['member_id'] . '"';
}

$date = date("Y-m-d");

$msg = '';

$sql = 'SELECT
			a.*,
			b.*,
			a.id as tId	,
			p.pName
		FROM
			tSMS_Check AS a
		JOIN
			tSMS_Log AS b ON b.tTID = a.tTaskID
		JOIN
			tContractScrivener AS cs ON cs.cCertifiedId=b.tPID
		JOIN
			tScrivener AS s ON s.sId =cs.cScrivener
		JOIN
			tPeopleInfo AS p ON p.pId = s.sUndertaker1
		WHERE
			a.tCode NOT IN ("0","1","77","999999999","00000","99999")
			AND a.tChecked = "y"
			AND b.sSend_Time >= "' . $date . ' 00:00:00"
			' . $str . '
			AND tUndertakerCheck = ""
			AND b.tPID != ""
		GROUP BY b.tPID,tKind
		ORDER BY
			b.sSend_Time
		DESC
		';
if ($_SESSION['member_id'] == 6) {
    header("Content-Type:text/html; charset=utf-8");
}

$rs = $conn->Execute($sql);

$list = array();
$i    = 0;
while (!$rs->EOF) {
    $list[$i] = $rs->fields;

    if ($list[$i]['tKind'] == 'income' || $list[$i]['tKind'] == 'income2') {
        $list[$i]['tKind'] .= '(入帳)';
    } else if ($list[$i]['tKind'] == 'cheque') {
        $list[$i]['tKind'] .= '(票據)';
    } else if ($list[$i]['tKind'] == '手動cheque') {
        $list[$i]['tKind'] .= '(票據)';
    }

    $msg .= '保證號碼:' . $list[$i]['tPID'] . "&nbsp;&nbsp;類別:" . $list[$i]['tKind'] . "<br>";
    $i++;
    $rs->MoveNext();
}

if ($msg) {
    $msg = '【失敗簡訊通知】<br>' . $msg;
    echo $msg;
}

<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once __DIR__ . '/sms_function.php';
require_once dirname(__DIR__) . '/session_check.php';

$_POST    = escapeStr($_POST);
$_REQUEST = escapeStr($_REQUEST);
//民國日期轉成西元日期
function RocConverter($date_str, $ch = '0')
{
    $_tmp = explode('-', $date_str);
    if ($ch == '1') {
        $date_str = ($_tmp[0] - 1911) . '-' . $_tmp[1] . '-' . $_tmp[2];
    } else {
        $date_str = ($_tmp[0] + 1911) . '-' . $_tmp[1] . '-' . $_tmp[2];
    }
    unset($_tmp);
    return $date_str;
}
//

$member_id = $_SESSION['member_id'];
$SMS_Mail  = new SMS_Gateway();

$_id = trim($_POST['id']);
$_ch = trim($_REQUEST['ch']);
$cat = trim($_REQUEST['cat']);

$ng_mobile      = trim($_POST['ng_mobile']);
$ng_certifiedid = trim($_POST['ng_certifiedid']);
$ng_start_date  = trim($_POST['ng_start_date']);
$ng_end_date    = trim($_POST['ng_end_date']);

$ok_mobile      = trim($_POST['ok_mobile']);
$ok_certifiedid = trim($_POST['ok_certifiedid']);
$ok_start_date  = trim($_POST['ok_start_date']);
$ok_end_date    = trim($_POST['ok_end_date']);

//view 表只有三個月
$table = "tSMS_Check_View AS a
	LEFT JOIN
		tSMS_Log_View AS b ON b.tTID = a.tTaskID";
$query = '';
$today = date("Y-m-d");
if ($_ch == 'a') { //今日全部簡訊
    $query = 'b.sSend_Time >= "' . $today . ' 00:00:00" AND b.tTID != "" AND b.tTID != "error" ';
} elseif ($_ch == 's') { //今日成功簡訊
    $query = 'b.sSend_Time >= "' . $today . ' 00:00:00" AND a.tChecked = "y" AND a.tCode IN ("0","00000")';
} elseif ($_ch == 'f') { //今日失敗簡訊
    $query = 'b.sSend_Time >= "' . $today . ' 00:00:00" AND a.tCode NOT IN ("0","1","77","999999999","00000","99999") AND a.tChecked = "y" ';
} elseif ($_ch == 'c') { //今日傳送中簡訊
    $query = 'b.sSend_Time >= "' . $today . ' 00:00:00" AND a.tChecked = "n"';
} elseif ($_ch == 'b') { //今日伺服器端失敗簡訊
    $query = 'b.sSend_Time >= "' . $today . ' 00:00:00" AND a.tCode IN ("77","999999999","99999") AND a.tChecked = "y" ';
} elseif ($_ch == 'ff') { //失敗簡訊提醒(2020-12-16 生效)
    $table = 'tSMS_Check AS a
		LEFT JOIN
		tSMS_Log AS b ON b.tTID = a.tTaskID';
    if ($_SESSION['member_id'] == 6 || $_SESSION['member_id'] == 1) {
        $str = '';
    } else {
        $str = ' AND s.sUndertaker1 = "' . $_SESSION['member_id'] . '"';
    }
    $query = 'b.sSend_Time >= "2020-12-17 00:00:00" AND a.tCode NOT IN ("0","1","77","999999999","00000","99999") AND a.tChecked = "y" AND tUndertakerCheck = "" ' . $str;
} else {
    //查全部
    $table = 'tSMS_Check AS a
		LEFT JOIN
		tSMS_Log AS b ON b.tTID = a.tTaskID';

    //增加搜尋時的條件--異常案件
    if ($ng_mobile) {
        if ($query) {$query .= ' AND ';}
        $query .= ' a.tMSISDN="' . $ng_mobile . '" ';
    }

    if ($ng_certifiedid) {
        if ($query) {$query .= ' AND ';}
        $query .= ' b.tPID="' . $ng_certifiedid . '" ';
    }

    if ($ng_start_date) {
        if ($query) {$query .= ' AND ';}
        $query .= ' b.sSend_Time >= "' . RocConverter($ng_start_date) . ' 00:00:00" ';
    }

    if ($ng_end_date) {
        if ($query) {$query .= ' AND ';}
        $query .= ' b.sSend_Time <= "' . RocConverter($ng_end_date) . ' 23:59:59" ';
    }

    if ($query == '') {
        $query .= ' a.tChecked = "n" AND b.sSend_Time >= "' . $today . ' 00:00:00" ';
    }
}
$sql = '
	SELECT
		a.*,
		b.*,
		a.id as tId,
		p.pName as staff
	FROM
		' . $table . '
	JOIN
		tContractScrivener AS cs ON cs.cCertifiedId=b.tPID
	JOIN
		tScrivener AS s ON s.sId =cs.cScrivener
	JOIN
		tPeopleInfo AS p ON p.pId = s.sUndertaker1

	WHERE
		' . $query . '
	ORDER BY
		b.sSend_Time
	DESC ;
';

$rs       = $conn->Execute($sql);
$i        = 1;
$sms_list = array();
while (!$rs->EOF) {
    $detail = array();

    //20240618 Project S只有特定人員可以查看
    if (!in_array($_SESSION['member_id'], [1, 3, 6, 12, 13, 36, 84, 90]) && (tPID == '130119712')) {
        continue;
    }

    $detail = $rs->fields;
    switch ($detail['tSystem']) {
        case '1':
            $detail['tSystem'] = '中華';
            break;
        case '2':
            $detail['tSystem'] = '遠傳';
            break;
        case '3':
            $detail['tSystem'] = '亞太';
            break;
    }
    $finger = '';
    if ($detail['tSystem'] == '遠傳') {
        $detail['finger'] = 'cursor:pointer;';
    }

    if ($detail['tSystem'] == '遠傳') {
        $detail['style'] = "text-decoration:underline;color:blue;";

    }

    if ($detail['tKind'] == '回饋金' || $detail['tKind'] == 'sheep') {
        $detail['staff'] = '吳佩琦';
    } elseif ($detail['tKind'] == '手動' || $detail['tKind'] == 'APP' || $detail['tKind'] == 'LINE') {

        $detail['staff'] = getSendName($rs->fields['tTID']);
    }

    $sms_list[$i] = $detail;
    $i++;
    $rs->MoveNext();
}

function getSendName($tid)
{
    global $conn;

    $sql = "SELECT tSendName FROM tSMS_Log WHERE tTID ='" . $tid . "'";

    $rs = $conn->Execute($sql);

    return $rs->fields['tSendName'];
}

##
$smarty->assign('cat', $cat);
$smarty->assign('ok_end_date', $ok_end_date);
$smarty->assign('ok_start_date', $ok_start_date);
$smarty->assign('ok_certifiedid', $ok_certifiedid);
$smarty->assign('ok_mobile', $ok_mobile);
$smarty->assign('ng_end_date', $ng_end_date);
$smarty->assign('ng_start_date', $ng_start_date);
$smarty->assign('ng_mobile', $ng_mobile);
$smarty->assign('ng_certifiedid', $ng_certifiedid);
$smarty->assign('_id', $_id);
$smarty->assign('_ch', $_ch);
$smarty->assign('sms_list', $sms_list);
$smarty->display('sms_list.inc.tpl', '', 'others');

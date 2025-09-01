<?php
require_once dirname(__DIR__).'/first1DB.php';
require_once dirname(__DIR__).'/session_check.php';
require_once dirname(__DIR__).'/tracelog.php';

$tlog = new TraceLog();
$tlog->selectWrite($_SESSION['member_id'], json_encode($_REQUEST), '查詢保證號碼所屬的代書與狀態');

$_POST = escapeStr($_POST);

// 取得變數值
$cid = $_POST['cid'];
$data['msg'] = '';

if (!$cid) {
	$data['msg'] = "查無資料...(無任何輸入)" ;
	echo json_encode($data);
	exit;
}

if (!preg_match("/[0-9]{9}/",$cid)) {
	$data['msg'] = "查無資料...(保證號碼不足９碼)";
	echo json_encode($data);
	exit;
}
##

// 資料庫處理
$conn = new first1DB;

$sql = '
	SELECT
		bUsed,
		bDel,
		bSID,
		(SELECT sName FROM tScrivener WHERE sId=a.bSID) scrivener,
		bAccount,
		bApplication
	FROM
		tBankCode AS a 
	WHERE
		bAccount LIKE "%'.$cid.'";
';

$tmp = $conn->one($sql);
$bank = substr($tmp['bAccount'], 0, 5);

##銀行別
$sql = "SELECT cId,cBankFullName,cBranchFullName FROM tContractBank WHERE cBankVR LIKE '".$bank."%'";

$tmp2 = $conn->one($sql);
$bankname = $tmp2['cBankFullName'];

if ($tmp2['cBankFullName'] == '永豐銀行') {
	$bankname .= $tmp2['cBranchFullName'];
}
$data['bankname'] = $bankname;
##

##合約書用途bApplication 1土地2建物3預售屋
$Application = 0;

if ($tmp['bApplication'] == 1 ) {
	// $Application = '土地';
	$Application = 1;
} else if ($tmp['bApplication'] == 2) {
	// $Application = '建物';
	$Application = 2;
} else if ($tmp['bApplication'] == 3) {
	// $Application = '預售屋';
	$Application = 3;
}

$data['Application'] = $Application;
##

if ($tmp['scrivener']) {
	$data['cid'] = $cid;
	$data['scrivener'] = $tmp['scrivener'];

	if ($tmp['bDel'] == 'y') {//已刪除
		$data['msg'] = '保證號碼"'.$cid.'"已被刪除!!';
	} else if ($tmp['bUsed']=='1') {//已使用
		$data['msg'] = '保證號碼"'.$cid.'"已使用!!';
		$data['use'] = 1;
	} else {//未使用
		$data['msg'] = '保證號碼"'.$cid.'"尚未使用!!';
	}
} else {
	$data['msg'] = "ng_查無資料...(查無此保證號碼)";
}

echo json_encode($data);
exit;
?>
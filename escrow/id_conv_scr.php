<?php
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/includes/lib/contractBank.php';

/**
 * 2022-06-15
 * 取得所有合約銀行資訊
 * Function getContractBank() {}
 * Path: /includes/lib/contractBank.php
 */

// 取得變數值
$cid = $_REQUEST['cid'];

if (!$cid) {
    $data['status']    = 'ng';
    $data['statusMsg'] = '查無資料...(無任何輸入)';

    exit(json_encode($data, JSON_UNESCAPED_UNICODE));
}

if (!preg_match("/[0-9]{9}/", $cid)) {
    $data['status']    = 'ng';
    $data['statusMsg'] = '查無資料...(保證號碼不足９碼)';

    exit(json_encode($data, JSON_UNESCAPED_UNICODE));
}
##

//取得所有合約銀行資訊
$banks = getContractBank();
##

//取得合約銀行虛擬帳號資訊
$sql_arr = array();
foreach ($banks as $v) {
    $sql_arr[] = 'bAccount = "' . substr($v['cBankVR'], 0, 5) . $cid . '"';
}

$sql = '
	SELECT
		bUsed,
		bDel,
		bSID,
		(SELECT sName FROM tScrivener WHERE sId=a.bSID) scrivener,
		bBrand,
		bAccount,
		bCategory,
		bApplication,
		bFrom
	FROM
		tBankCode AS a
	WHERE
		' . implode(' OR ', $sql_arr) . '
;';

$rs   = $conn->Execute($sql);
$tmp  = $rs->fields;
$bank = substr($tmp['bAccount'], 0, 5);

$sql_arr = null;
unset($sql_arr);
##

##銀行別
$sql      = "SELECT cId,cBankFullName,cBranchFullName,cBankCode FROM tContractBank WHERE cBankVR LIKE '" . $bank . "%';";
$rs       = $conn->Execute($sql);
$tmp2     = $rs->fields;
$bankname = $tmp2['cBankFullName'] . $tmp2['cBranchFullName'];
##

##合約書用途bApplication 1土地2建物3預售屋
$Application = 0;
if ($tmp['bApplication'] == 1) {
    // $Application = '土地';
    $Application = 1;
} elseif ($tmp['bApplication'] == 2) {
    // $Application = '建物';
    $Application = 2;
} elseif ($tmp['bApplication'] == 3) {
    // $Application = '預售屋';
    $Application = 3;
}

##
if ($tmp['scrivener']) {
    //取得保證號碼的狀態
    if ($tmp['bDel'] == 'y') { //已刪除
        $data['status']    = 'del';
        $data['statusMsg'] = '保證號碼"' . $cid . '"已被刪除!!';
    } else if ($tmp['bUsed'] == '1') { //已使用
        $data['status']    = 'used';
        $data['statusMsg'] = '保證號碼"' . $cid . '"已使用!!';
    } else { //未使用
        $data['status']    = 'ok';
        $data['statusMsg'] = '保證號碼"' . $cid . '"尚未使用!!';
    }

    $data['sId']       = $tmp['bSID'];
    $data['scrivener'] = $tmp['scrivener'];
    $data['bank']      = $bankname;
    $data['app']       = $tmp['bApplication'];
    $data['account']   = $tmp['bAccount'];
    $data['bankCode']  = $tmp2['cBankCode'];
    $data['category']  = $tmp['bCategory'];
    $data['brand']     = $tmp['bBrand'];
    $data['cId']       = $cid;
    $data['bFrom']     = $tmp['bFrom'];
} else {
    $data['status']    = 'ng';
    $data['statusMsg'] = '查無資料...(查無此保證號碼)';
}

$tmp = $tmp2 = null;
unset($tmp, $tmp2);

$conn->close();

exit(json_encode($data, JSON_UNESCAPED_UNICODE));

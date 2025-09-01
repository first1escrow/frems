<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/includes/writelog.php';

$act    = trim($_POST['act']); //動作
$bid    = trim($_POST['bid']); //仲介店編號
$sales  = trim($_POST['sales']); //業務編號
$num    = trim($_POST['num']); //第幾間
$cid    = trim($_POST['cid']); //保證號碼
$target = trim($_POST['target']); //服務對象、1:仲介、2:地政士

if ($act == 'add') {
    $sql = 'INSERT INTO tContractSales (cCertifiedId,cTarget,cSalesId,cBranch,cCreator ) VALUES("' . $cid . '","' . $target . '","' . $sales . '","' . $bid . '","' . $_SESSION['member_id'] . '")';
    write_log('使用者自己新增' . $cid . ':target' . $target . ",sales" . $sales . ",branch" . $bid, 'escrowSalse');

} else if ($act == 'del') {
    $sql = "DELETE FROM tContractSales WHERE cCertifiedId='" . $cid . "' AND cSalesId='" . $sales . "' AND cBranch='" . $bid . "'";
    write_log('使用者自己刪除' . $cid . ':target' . $target . ",sales" . $sales . ",branch" . $bid, 'escrowSalse');
}

//編輯資料
$returns = false;
$returns = $conn->Execute($sql);
##

$sql = '
	SELECT
		cId,
		cCertifiedId,
		cSalesId,
		cBranch,
		(SELECT pName FROM tPeopleInfo WHERE pId=cSalesId ) as bSalesName
	FROM
		 tContractSales
	WHERE
		cBranch="' . $bid . '"
	AND
		cCertifiedId ="' . $cid . '"
';
$rs = $conn->Execute($sql);

$tmp = array();
while (!$rs->EOF) {
    if ($_SESSION['member_pDep'] == 7) {
        $tmp[] = '<span style="padding:2px;background-color:yellow;">' . $rs->fields['bSalesName'] . '</span>';
    } else {
        $tmp[] = '<span style="padding:2px;background-color:yellow;"><span onclick="del(' . $num . ',' . $rs->fields['cSalesId'] . ',' . $rs->fields['cBranch'] . ')" style="cursor:pointer;">X</span>' . $rs->fields['bSalesName'] . '</span>';
    }
    $rs->MoveNext();
}
$Sales1 = implode(',', $tmp);
unset($tmp);

echo $Sales1;

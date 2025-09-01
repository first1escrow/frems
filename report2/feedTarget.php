<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/web_addr.php';
require_once dirname(__DIR__) . '/session_check.php';

$_POST     = escapeStr($_POST);
$tmp       = explode('-', $_POST['dateStart']);
$dateStart = ($tmp[0] + 1911) . "-" . $tmp[1] . "-" . $tmp[2] . ' 00:00:00';
unset($tmp);

$tmp     = explode('-', $_POST['dateEnd']);
$dateEnd = ($tmp[0] + 1911) . "-" . $tmp[1] . "-" . $tmp[2] . ' 00:00:00';
unset($tmp);
###

//無合作契約書的要更改回饋對象給地政士
$sql = "SELECT
			cc.cCertifiedId,
			cr.cBranchNum,
			cr.cBranchNum1,
			cr.cBranchNum2,
			(SELECT bName FROM tBrand WHERE bId = cr.cBrand) AS Brand,
			(SELECT bName FROM tBrand WHERE bId = cr.cBrand1) AS Brand1,
			(SELECT bName FROM tBrand WHERE bId = cr.cBrand2) AS Brand2,
			(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum) AS bStore,
			(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum1) AS bStore1,
			(SELECT bStore FROM tBranch WHERE bId = cr.cBranchNum2) AS bStore2,
			CONCAT((Select bCode From `tBrand` c Where c.bId = cr.cBrand ),LPAD(cr.cBranchNum,5,'0')) as bCode,
			CONCAT((Select bCode From `tBrand` c Where c.bId = cr.cBrand1 ),LPAD(cr.cBranchNum1,5,'0')) as bCode1,
			CONCAT((Select bCode From `tBrand` c Where c.bId = cr.cBrand2 ),LPAD(cr.cBranchNum2,5,'0')) as bCode2,
			(SELECT bCategory FROM tBranch WHERE bId = cr.cBranchNum) AS bCategory,
			(SELECT bCategory FROM tBranch WHERE bId = cr.cBranchNum1) AS bCategory1,
			(SELECT bCategory FROM tBranch WHERE bId = cr.cBranchNum2) AS bCategory2
		FROM
			tContractCase AS cc
		LEFT JOIN
			tContractRealestate AS cr ON cr.cCertifyId=cc.cCertifiedId
		WHERE
			cc.cEndDate >='" . $dateStart . "' AND cc.cEndDate <='" . $dateEnd . "' AND cc.cCaseStatus IN(3,4,9) AND cc.cCaseFeedBackModifier = ''";
$rs    = $conn->Execute($sql);
$total = $rs->RecordCount();
$i     = 0;

while (!$rs->EOF) {
    //配件剔除，不是配件的才要更改
    if ($rs->fields['cBranchNum'] > 0 && $rs->fields['cBranchNum1'] == 0 && $rs->fields['cBranchNum2'] == 0) {
        $ck = check($rs->fields['cBranchNum']);
        if (!$ck && $rs->fields['bCategory'] != 2) {
            $list[] = $rs->fields;
        } else {
            $unlist[] = $rs->fields; //有合作契約書的
        }
    } else if ($rs->fields['cBranchNum1'] > 0 || $rs->fields['cBranchNum2'] > 0) {
        $pair[$i] = $rs->fields; //配件
        $tmp[0]   = "(" . $pair[$i]['bCode'] . ")" . $pair[$i]['Brand'] . $pair[$i]['bStore'];

        if ($rs->fields['cBranchNum1'] > 0) {
            $tmp[1] = "(" . $pair[$i]['bCode1'] . ")" . $pair[$i]['Brand1'] . $pair[$i]['bStore1'];
        }

        if ($rs->fields['cBranchNum2'] > 0) {
            $tmp[2] = "(" . $pair[$i]['bCode2'] . ")" . $pair[$i]['Brand2'] . $pair[$i]['bStore2'];
        }

        $tmp2                  = implode("<bR>", $tmp);
        $pair[$i]['allBranch'] = $tmp2;
        $i++;
    } else {
        $unknow[] = $rs->fields; //未知
    }
    $rs->MoveNext();
}

//非配件且沒有合作契約書
$path = dirname(__DIR__) . '/log2';
if (!is_dir($path)) {
    mkdir($path, 0777, true);
}
$fw = fopen($path . '/feedTarget_' . date('YmdHis') . '.log', 'a+');

for ($i = 0; $i < count($list); $i++) {
    $sql = "UPDATE tContractCase SET cFeedbackTarget = 2 WHERE cCertifiedId = '" . $list[$i]['cCertifiedId'] . "'";
    fwrite($fw, $sql . "\r\n");

    if ($conn->Execute($sql)) {
        $arr[] = $list[$i];
    } else {
        $arr2[] = $list[$i];
    }

}
fclose($fw);

function check($bId)
{
    global $conn;

    $sql   = "SELECT fStoreId FROM tFeedBackData WHERE fType =2 AND fStatus = 0 AND fStoreId ='" . $bId . "'";
    $rs    = $conn->Execute($sql);
    $total = $rs->RecordCount();

    if ($total > 0) {
        return true;
    } else {
        return false; //沒有合作契約書
    }
}

#####
$smarty->assign('arr', $arr);
$smarty->assign('arr2', $arr2);
$smarty->assign('countfail', count($arr2));
$smarty->assign('pair', $pair);
$smarty->display('feedTarget.inc.tpl', '', 'report2');

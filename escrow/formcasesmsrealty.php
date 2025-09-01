<?php
include_once '../configs/config.class.php';
include_once '../class/SmartyMain.class.php';
include_once '../class/sms.class.php';
include_once '../class/contract.class.php';
include_once '../session_check.php';
include_once '../openadodb.php';

$certified_id = isset($_REQUEST['cid']) ? trim(addslashes($_REQUEST['cid'])) : '';
$bid          = isset($_REQUEST['bid']) ? trim(addslashes($_REQUEST['bid'])) : '';
$index        = isset($_REQUEST['in']) ? trim(addslashes($_REQUEST['in'])) : '';
$ok           = isset($_REQUEST['ok']) ? trim(addslashes($_REQUEST['ok'])) : '';

//找出案件預設簡訊對象
$index -= 1;
if ($index <= 0) {
    $index = '';
}

$sql = "SELECT cCaseStatus FROM tContractCase AS cc WHERE cc.cCertifiedId = '" . $certified_id . "'";

$rs = $conn->Execute($sql);

if ($rs->fields['cCaseStatus'] == 2) {

    //cSignCategory
    $sql = '
		SELECT
			cr.cSmsTarget' . $index . ' as cSmsTarget,
			(SELECT cCaseStatus FROM tContractCase AS cc WHERE cc.cCertifiedId=cr.cCertifyId) AS cCaseStatus,
			(SELECT cSignCategory FROM tContractCase AS cc WHERE cc.cCertifiedId=cr.cCertifyId) AS cSignCategory
		FROM
			tContractRealestate  AS cr
		WHERE
			cr.cCertifyId="' . $certified_id . '" ;
	';
    //echo 'SQL='.$sql ;
    $rs            = $conn->Execute($sql);
    $tgArr         = $rs->fields;
    $cCaseStatus   = $rs->fields['cCaseStatus'];
    $cSignCategory = $rs->fields['cSignCategory'];
    $SmsScrTarget  = [];

    //取得勾選的簡訊對象
    $SmsScrTarget = explode(',', $tgArr['cSmsTarget']);
    ##
    //print_r($SmsScrTarget) ;

    //建立簡訊列表
    $sql = '
		SELECT
			a.bNID as id,
			a.bId as bId,
			b.tTitle as tTitle,
			a.bName as bName,
			a.bDefault as bDefault,
			a.bMobile as bMobile,
			a.bCheck_id,
			a.bBranch as branch
		FROM
			tBranchSms AS a
		JOIN
			tTitle_SMS AS b ON b.id=a.bNID
		WHERE
			a.bBranch="' . $bid . '"
			AND a.bCheck_id =0
			AND b.tKind="0"
			AND a.bDel = 0
			AND b.id NOT IN ("14","15")

		ORDER BY
			b.tTitle,a.bId
		ASC;
	';
    //echo 'SQL='.$sql ;
    $rs   = $conn->Execute($sql);
    $book = [];
    $i    = 0;
    while (! $rs->EOF) {
        $book[$i] = $rs->fields;
        // 確保所有必要的欄位都有預設值
        $book[$i]['bId']     = isset($rs->fields['bId']) ? $rs->fields['bId'] : '';
        $book[$i]['bMobile'] = isset($rs->fields['bMobile']) ? $rs->fields['bMobile'] : '';
        $book[$i]['branch']  = isset($rs->fields['branch']) ? $rs->fields['branch'] : '';

        foreach ($SmsScrTarget as $k => $v) {
            if ($book[$i]['bMobile'] == $v) {
                $book[$i]['isSelect'] = '1';
            }
        }

        $i++;
        $rs->MoveNext();
    }
    ##
    //額外增加的
    $sql = '
		SELECT
			a.bNID as id,
			a.bId as bId,
			b.tTitle as tTitle,
			a.bName as bName,
			a.bDefault as bDefault,
			a.bMobile as bMobile,
			a.bCheck_id,
			a.bBranch  as branch
		FROM
			tBranchSms AS a
		JOIN
			tTitle_SMS AS b ON b.id=a.bNID
		WHERE
			a.bBranch="' . $bid . '"

			AND a.bCheck_id ="' . $certified_id . '"
		ORDER BY
			a.bId
		ASC;
	';
    $rs = $conn->Execute($sql);

    while (! $rs->EOF) {
        $book[$i] = $rs->fields;
        // 確保所有必要的欄位都有預設值
        $book[$i]['bId']     = isset($rs->fields['bId']) ? $rs->fields['bId'] : '';
        $book[$i]['bMobile'] = isset($rs->fields['bMobile']) ? $rs->fields['bMobile'] : '';
        $book[$i]['branch']  = isset($rs->fields['branch']) ? $rs->fields['branch'] : '';

        foreach ($SmsScrTarget as $k => $v) {
            if ($book[$i]['bMobile'] == $v) {
                $book[$i]['isSelect'] = '1';
            }
        }

        $i++;
        $rs->MoveNext();
    }

} else {
    $sql = '
		SELECT
			cr.cSmsTarget' . $index . ' as cSmsTarget,
			(SELECT cCaseStatus FROM tContractCase AS cc WHERE cc.cCertifiedId=cr.cCertifyId) AS cCaseStatus,
			(SELECT cSignCategory FROM tContractCase AS cc WHERE cc.cCertifiedId=cr.cCertifyId) AS cSignCategory
		FROM
			tContractRealestate  AS cr
		WHERE
			cr.cCertifyId="' . $certified_id . '" ;
	';
    //echo 'SQL='.$sql ;
    $rs            = $conn->Execute($sql);
    $tgArr         = $rs->fields;
    $cCaseStatus   = $rs->fields['cCaseStatus'];
    $cSignCategory = $rs->fields['cSignCategory'];
    $SmsScrTarget  = [];

    //取得勾選的簡訊對象
    $SmsScrTarget = explode(',', $tgArr['cSmsTarget']);
    ##
    for ($i = 0; $i < count($SmsScrTarget); $i++) {

        $BranchSms = getBranchSms($bid, $SmsScrTarget[$i], '');

        $book[$SmsScrTarget[$i]]['tTitle']   = $BranchSms['tTitle'];
        $book[$SmsScrTarget[$i]]['bMobile']  = $SmsScrTarget[$i];
        $book[$SmsScrTarget[$i]]['bName']    = $BranchSms['bName'];
        $book[$SmsScrTarget[$i]]['isSelect'] = 1;

        unset($BranchSms);

        $count++;
    }

}
//是否顯示"已更新"
$dialog = '';
if ($ok == '1') {
    $dialog = '
			$("#dialog").html("簡訊對象已更新!!") ;
			$("#dialog").dialog({
				modal: true,
				buttons: {
					"OK": function () {
						$(this).dialog("close") ;
					}
				}
			}) ;
		';
}
##
##
//職稱下拉
$sql = "SELECT * FROM tTitle_SMS WHERE id IN (12,13,14,15,28) ORDER BY id ASC;";
$rs  = $conn->Execute($sql);
while (! $rs->EOF) {
    $option_title[$rs->fields['id']] = $rs->fields['tTitle'];
    $rs->MoveNext();
}
##
function getBranchSms($bId, $mobile, $cid)
{
    global $conn;

    $sql = '
		SELECT
			a.bNID as id,
			a.bId as bId,
			b.tTitle as tTitle,
			a.bName as bName,
			a.bDefault as bDefault,
			a.bMobile as bMobile,
			a.bCheck_id,
			a.bBranch as branch
		FROM
			tBranchSms AS a
		JOIN
			tTitle_SMS AS b ON b.id=a.bNID
		WHERE
			a.bBranch="' . $bId . '"
			AND a.bMobile = "' . $mobile . '"

		ORDER BY
			b.tTitle,a.bId
		ASC;
	';

    $rs = $conn->Execute($sql);

    return $rs->fields;

}
##
// 確保所有變數都有預設值
$option_title  = isset($option_title) ? $option_title : '';
$cCaseStatus   = isset($cCaseStatus) ? $cCaseStatus : '';
$cSignCategory = isset($cSignCategory) ? $cSignCategory : '';
$book          = isset($book) ? $book : [];
$dialog        = isset($dialog) ? $dialog : '';

$smarty->assign('option_title', $option_title);
$smarty->assign('cCaseStatus', $cCaseStatus); //cSignCategory
$smarty->assign('cSignCategory', $cSignCategory);
$smarty->assign('book', $book);
$smarty->assign('certified_id', $certified_id);
$smarty->assign('cCertifiedId', $certified_id);
$smarty->assign('bBranch', $bid);
$smarty->assign('index', $index);
$smarty->assign('dialog', $dialog);
$smarty->assign('id', isset($_REQUEST['id']) ? $_REQUEST['id'] : '');
$smarty->display('formcasesmsrealty.inc.tpl', '', 'escrow');

<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/payByCase/payByCase.class.php';
require_once dirname(__DIR__) . '/class/payByCase/payByCaseInfo.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';

use First1\V1\PayByCase\PayByCase;
use First1\V1\PayByCase\PayByCaseInfo;

$conn = new first1DB;

//所有業務名單
$sql = 'SELECT pId, pName FROM tPeopleInfo WHERE pDep IN (4, 7) ORDER BY pId ASC;';
$rs  = $conn->all($sql);

$sales = [];
foreach ($rs as $v) {
    $sales[$v['pId']] = $v['pName'];
}
$sql = $rs = null;
unset($sql, $rs);
##

//撈紀錄
$sql = 'SELECT
            a.fId,
            a.fCertifiedId,
            a.fTarget,
            a.fTargetId,
            a.fSales,
            a.fSalesConfirmDate,
            a.fAccountant,
            a.fAccountantConfirmDate,
            a.fDetail,
            b.fId as bankId,
            CASE b.fType
                WHEN 2 THEN "身份證編號"
                WHEN 3 THEN "統一編號"
                WHEN 4 THEN "居留證號碼"
                ELSE "------"
            END as identity,
            b.fIdentityIdNumber,
            (SELECT bBank4_name FROM tBank WHERE bBank3 = b.fBankMain AND bBank4 = "") as bankMain,
            (SELECT bBank4_name FROM tBank WHERE bBank3 = b.fBankMain AND bBank4 = b.fBankBranch) as bankBranch,
            b.fBankAccount as bankAccount,
            b.fBankAccountName as bankAccountName
        FROM
            tFeedBackMoneyPayByCase AS a
        JOIN
            tFeedBackMoneyPayByCaseAccount AS b ON a.fCertifiedId = b.fCertifiedId AND b.fTarget = "S" AND a.fId = b.fPayByCaseId
        WHERE
            a.fTarget = "S"
            AND a.fSalesConfirmDate IS NOT NULL
            AND a.fAccountantConfirmDate IS NULL
        ORDER BY
            a.fId
        DESC;';
$list = $conn->all($sql);

if (!empty($list)) {
    $pay_by_case      = new PayByCase;
    $pay_by_case_info = new PayByCaseInfo($conn);

    foreach ($list as $k => $v) {
        $tmp = json_decode($v['fDetail'], true);

        if (!empty($tmp['case'])) {
            // $list[$k]['detail'] = $tmp;
            $list[$k]['detail'] = array_merge($tmp, $pay_by_case_info->getCaseOtherInfo($v['fCertifiedId'], $tmp['total']));
        }

        $list[$k]['sales'] = $sales[$v['fSales']];

        $tmp = null;unset($tmp);
    }
}
##

$_tab = preg_match("/^[0-2]{1}$/", $_POST['_tab_id']) ? $_POST['_tab_id'] : 0;

$smarty->assign('list', $list);
$smarty->assign('_tab', $_tab);

$smarty->display('payByCaseAccountingConfirm.inc.tpl', '', 'accounting');

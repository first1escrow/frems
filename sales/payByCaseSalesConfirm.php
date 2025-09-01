<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/payByCase/payByCaseScrivener.class.php';
require_once dirname(__DIR__) . '/class/payByCase/payByCaseInfo.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/class/scrivener.class.php';

use First1\V1\PayByCase\PayByCaseInfo;
use First1\V1\PayByCase\PayByCaseScrivener;

$sales_counter = [];//顯示待確認業務數量
$admin_payByCaseSalesConfirm = [1, 6, 12];

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
$sql = ($_SESSION['member_id'] && $_SESSION['member_pDep'] == 7) ? ' AND a.fSales = "' . $_SESSION['member_id'] . '"' : '';
if(in_array($_SESSION['member_id'], $admin_payByCaseSalesConfirm) and $_GET['id']){
    $sql = ' AND a.fSales = ' . $_GET['id'];
}
$sql_pay  = 'SELECT
            a.*,
            (SELECT fStatus FROM tFeedBackMoneyReview WHERE fCertifiedId = a.fCertifiedId AND fFail = 0 ORDER BY fApplyTime DESC LIMIT 1) AS reviewStatus,
            (SELECT sRecall FROM tScrivener WHERE sId = a.fTargetId AND a.fTarget = "S" ) AS sRecall,
            (SELECT sSpRecall FROM tScrivener WHERE sId = a.fTargetId AND a.fTarget = "S" ) AS sSpRecall,
            (SELECT cSignDate FROM tContractCase WHERE cCertifiedId = a.fCertifiedId ) AS signDate
         FROM 
             tFeedBackMoneyPayByCase AS a 
         WHERE 
             a.fSalesConfirmDate IS NULL' . $sql . ' 
         ORDER BY 
            fId 
        DESC;';
$list = $conn->all($sql_pay);

if (!empty($list)) {
    $scrivener = new Scrivener();
    $pay_by_case      = new PayByCaseScrivener($conn);
    $pay_by_case_info = new PayByCaseInfo($conn);

    foreach ($list as $k => $v) {
        $tmp = json_decode($v['fDetail'], true);

        if (!empty($tmp['case'])) {
            $_bank    = $pay_by_case->getFeedBackBank($tmp['cScrivener']);
            $_checked = (count($_bank) == 1) ? 'checked' : '';

            $list[$k]['detail'] = $tmp;
            $list[$k]['bank']   = ['bank' => $_bank, 'checked' => $_checked];

            $_bank = $_checked = null;
            $scrivenerInfo = $scrivener->GetScrivenerInfo($v['fTargetId']);
            $list[$k]['scrivenerId'] = $scrivenerInfo['sName'];
            unset($_bank, $_checked);
        }

        $list[$k]['sales']  = $sales[$list[$k]['fSales']];
        $list[$k]['detail'] = array_merge($list[$k]['detail'], $pay_by_case_info->getCaseOtherInfo($v['fCertifiedId'], $list[$k]['detail']['total']));

        $list[$k]['feedbackTotal'] = $pay_by_case_info->getFeedbackTotal($v['fCertifiedId']);
        if($list[$k]['detail']['cCertifiedMoney'] > 0){
            $list[$k]['totalRatio'] = number_format(($list[$k]['feedbackTotal']) / ($list[$k]['detail']['cCertifiedMoney']) * 100, 1);
        } else {
            $list[$k]['totalRatio'] = 0;
        }
        if(in_array($_SESSION['member_id'], $admin_payByCaseSalesConfirm)){
            $sales_counter[$list[$k]['sales']] = (isset($sales_counter[$list[$k]['sales']])) ? ++$sales_counter[$list[$k]['sales']] : 1;
        }
        $tmp = null;unset($tmp);
    }

    $pay_by_case = null;unset($pay_by_case);
}

//撈紀錄
$sql = ($_SESSION['member_id'] && $_SESSION['member_pDep'] == 7) ? ' AND a.fSales = "' . $_SESSION['member_id'] . '"' : '';
if(in_array($_SESSION['member_id'], $admin_payByCaseSalesConfirm) and $_GET['id']){
    $sql = ' AND a.fSales = ' . $_GET['id'];
}
$sql_pay  = 'SELECT
            a.*,
            (SELECT fStatus FROM tFeedBackMoneyReview WHERE fCertifiedId = a.fCertifiedId AND fFail = 0 ORDER BY fApplyTime DESC LIMIT 1) AS reviewStatus,
            (SELECT sRecall FROM tScrivener WHERE sId = a.fTargetId AND a.fTarget = "S" ) AS sRecall,
            (SELECT sSpRecall FROM tScrivener WHERE sId = a.fTargetId AND a.fTarget = "S" ) AS sSpRecall,
            (SELECT cSignDate FROM tContractCase WHERE cCertifiedId = a.fCertifiedId ) AS signDate
         FROM 
             tFeedBackConfirm AS a 
         WHERE 
             a.fSalesConfirmDate IS NULL AND a.fHidden = 0 ' . $sql . ' 
         ORDER BY 
            fId 
        DESC;';
$list2 = $conn->all($sql_pay);

if (!empty($list2)) {
    $scrivener = new Scrivener();
    $pay_by_case      = new PayByCaseScrivener($conn);
    $pay_by_case_info = new PayByCaseInfo($conn);

    foreach ($list2 as $k => $v) {
        $tmp = json_decode($v['fDetail'], true);
        if (!empty($tmp['case'])) {
            $list2[$k]['detail'] = $tmp;
            $scrivenerInfo = $scrivener->GetScrivenerInfo($v['fTargetId']);
            $list2[$k]['scrivenerId'] = $scrivenerInfo['sOffice'];
        }

        $list2[$k]['sales']  = $sales[$list2[$k]['fSales']];
        $list2[$k]['detail'] = array_merge($list2[$k]['detail'], $pay_by_case_info->getCaseOtherInfo($v['fCertifiedId'], $list2[$k]['detail']['total']));

        $list2[$k]['feedbackTotal'] = $pay_by_case_info->getFeedbackTotal($v['fCertifiedId']);
        if($list2[$k]['detail']['cCertifiedMoney'] > 0){
            $list2[$k]['totalRatio'] = number_format(($list2[$k]['feedbackTotal']) / ($list2[$k]['detail']['cCertifiedMoney']) * 100, 1);
        } else {
            $list2[$k]['totalRatio'] = 0;
        }
        if(in_array($_SESSION['member_id'], $admin_payByCaseSalesConfirm)){
            $sales_counter[$list2[$k]['sales']] = (isset($sales_counter[$list2[$k]['sales']])) ? ++$sales_counter[$list2[$k]['sales']] : 1;
        }
        $tmp = null;unset($tmp);
    }

    $pay_by_case = null;unset($pay_by_case);
}

## tFeedBackMoneyPayByCaseLog 再確認資料
$sql_recheck  = 'SELECT
            a.*,
            (SELECT fStatus FROM tFeedBackMoneyReview WHERE fCertifiedId = a.fCertifiedId AND fFail = 0 ORDER BY fApplyTime DESC LIMIT 1) AS reviewStatus,
            (SELECT sRecall FROM tScrivener WHERE sId = a.fTargetId AND a.fTarget = "S" ) AS sRecall,
            (SELECT sSpRecall FROM tScrivener WHERE sId = a.fTargetId AND a.fTarget = "S" ) AS sSpRecall,
            (SELECT cSignDate FROM tContractCase WHERE cCertifiedId = a.fCertifiedId ) AS signDate
         FROM
             tFeedBackMoneyPayByCaseLog AS a
         WHERE
             a.fStatus = 1 AND a.fKind = 2 AND a.fSalesConfirmDate IS NULL' . $sql . '
         ORDER BY
            fId
        DESC;';
$list_recheck = $conn->all($sql_recheck);

if (!empty($list_recheck)) {
    $scrivener = new Scrivener();
    $pay_by_case      = new PayByCaseScrivener($conn);
    $pay_by_case_info = new PayByCaseInfo($conn);

    foreach ($list_recheck as $k => $v) {
//        $tmp = json_decode($v['fDetail'], true);//未調整前紀錄
        $tmp = json_decode($v['fDetail2'], true);
        $list_recheck[$k]['detail'] = $tmp;

        if (!empty($tmp['case'])) {
            $_bank    = $pay_by_case->getFeedBackBank($tmp['cScrivener']);
            $_checked = (count($_bank) == 1) ? 'checked' : '';

            $list_recheck[$k]['detail'] = $tmp;
            $list_recheck[$k]['bank']   = ['bank' => $_bank, 'checked' => $_checked];

            $_bank = $_checked = null;
            $scrivenerInfo = $scrivener->GetScrivenerInfo($v['fTargetId']);
            $list_recheck[$k]['scrivenerId'] = $scrivenerInfo['sName'];
            unset($_bank, $_checked);
        }

        $list_recheck[$k]['sales']  = $sales[$list_recheck[$k]['fSales']];
        $list_recheck[$k]['detail'] = array_merge($list_recheck[$k]['detail'], $pay_by_case_info->getCaseOtherInfo($v['fCertifiedId'], $list_recheck[$k]['detail']['total']));

        $list_recheck[$k]['feedbackTotal'] = $pay_by_case_info->getFeedbackTotal($v['fCertifiedId']);
        if($list_recheck[$k]['detail']['cCertifiedMoney'] > 0){
            $list_recheck[$k]['totalRatio'] = number_format(($list_recheck[$k]['feedbackTotal']) / ($list_recheck[$k]['detail']['cCertifiedMoney']) * 100, 1);
        } else {
            $list_recheck[$k]['totalRatio'] = 0;
        }
        $list_recheck[$k]['memo'] = $list_recheck[$k]['fMemo'];

        $tmp = null;unset($tmp);
    }

    $pay_by_case = null;unset($pay_by_case);
}

arsort($sales_counter);

$_tab = preg_match("/^[0-2]{1}$/", $_POST['_tab_id']) ? $_POST['_tab_id'] : 0;

$smarty->assign('list', $list);
$smarty->assign('list2', $list2);
$smarty->assign('_tab', $_tab);
$smarty->assign('sales_counter', $sales_counter);
$smarty->assign('list_recheck', $list_recheck);
$smarty->assign('toggle', $_GET['toggle']);

$smarty->display('payByCaseSalesConfirm.inc.tpl', '', 'sales');

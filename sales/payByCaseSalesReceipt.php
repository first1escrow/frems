<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/payByCase/payByCase.class.php';
require_once dirname(__DIR__) . '/class/payByCase/payByCaseInfo.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';
include_once dirname(__DIR__) . '/openadodb.php';

$condition = '';
if ($_SESSION['member_pDep'] == 7) {
    $condition = 'AND fSales = ' . $_SESSION['member_id'];
}

#匯出批次選項
$menu_exp = ['0' => '全部'];
$sql      = "
        SELECT
            fExportTime
        FROM
            tFeedBackMoneyPayByCase
        WHERE
            fExportTime != ''
        " . $condition . "
        GROUP BY
            fExportTime
        ORDER BY
            fExportTime
        DESC
    ";

$rs = $conn->Execute($sql);
while (!$rs->EOF) {
    $menu_exp[$rs->fields['fExportTime']] = $rs->fields['fExportTime'];
    $rs->MoveNext();
}

$smarty->assign('menu_exp', $menu_exp);
$smarty->display('payByCaseSalesReceipt.inc.tpl', '', 'sales');

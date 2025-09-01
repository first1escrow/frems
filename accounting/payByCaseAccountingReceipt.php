<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/payByCase/payByCase.class.php';
require_once dirname(__DIR__) . '/class/payByCase/payByCaseInfo.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/first1DB.php';
include_once dirname(__DIR__) . '/openadodb.php' ;

$_POST = escapeStr($_POST) ;

if($_POST['export'] == 1) {


    if ($_POST['allForm']) {
        $qstr = "fId IN(".@implode(',', $_POST['allForm']).")";
    }

    $exportTime = date('YmdHis');
    $sql = "UPDATE 
            tFeedBackMoneyPayByCase 
        SET 
            fCaseCloseTime = '".date('Y-m-d')."',
            fExportTime = '".$exportTime."' 
        WHERE 
            ".$qstr;

    $conn->Execute($sql);
}

#匯出批次選項
$menu_exp =['0' => '全部'];
$sql = "
            SELECT 
                fExportTime 
            FROM 
                tFeedBackMoneyPayByCase 
            WHERE 
                fExportTime != '' 
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

$smarty->assign('menu_exp',$menu_exp);
$smarty->display('payByCaseAccountingReceipt.inc.tpl', '', 'accounting');
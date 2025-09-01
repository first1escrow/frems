<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/includes/writelog.php';

$cid = addslashes(trim($_POST['cid']));

$filename = date("Y-m-d") . "-" . $cid;

//IP
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $usrIP = $_SERVER['HTTP_CLIENT_IP'];
} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $usrIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $usrIP = $_SERVER['REMOTE_ADDR'];
}

$dir = $GLOBALS['FILE_PATH'] . 'log2/esc_del';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

$fw = fopen($dir . '/' . $filename . '.log', 'a+');

$info = $_SESSION['member_name'] . "-" . $_SESSION['member_acc'] . $ip . date("Y-m-d H:i:s") . "\r\n";

fwrite($fw, $info);
$i = 0;

$arr[$i]['cid']   = 'cCertifiedId';
$arr[$i++]['tbl'] = 'tContractCase';

$arr[$i]['cid']   = 'cCertifiedId';
$arr[$i++]['tbl'] = 'tContractScrivener';

$arr[$i]['cid']   = 'cCertifyId';
$arr[$i++]['tbl'] = 'tContractRealestate';

$arr[$i]['cid']   = 'cCertifiedId';
$arr[$i++]['tbl'] = 'tContractPropertyObject';

$arr[$i]['cid']   = 'cCertifiedId';
$arr[$i++]['tbl'] = 'tContractProperty';

$arr[$i]['cid']   = 'cCertifiedId';
$arr[$i++]['tbl'] = 'tContractOwner';

$arr[$i]['cid']   = 'cCertifiedId';
$arr[$i++]['tbl'] = 'tContractOthers';

$arr[$i]['cid']   = 'cCertifiedId';
$arr[$i++]['tbl'] = 'tContractLand';

$arr[$i]['cid']   = 'cCertifiedId';
$arr[$i++]['tbl'] = 'tContractInvoice';

$arr[$i]['cid']   = 'cCertifiedId';
$arr[$i++]['tbl'] = 'tContractInvoiceExt';

$arr[$i]['cid']   = 'cCertifiedId';
$arr[$i++]['tbl'] = 'tContractInterestExt';

$arr[$i]['cid']   = 'cCertifiedId';
$arr[$i++]['tbl'] = 'tContractIncome';

$arr[$i]['cid']   = 'cCertifiedId';
$arr[$i++]['tbl'] = 'tContractExpenditure';

$arr[$i]['cid']   = 'cCertifiedId';
$arr[$i++]['tbl'] = 'tContractBuyer';

$arr[$i]['cid']   = 'oCertifiedId';
$arr[$i++]['tbl'] = 'tChecklistOlist';

$arr[$i]['cid']   = 'bCertifiedId';
$arr[$i++]['tbl'] = 'tChecklistBlist';

$arr[$i]['cid']   = 'cCertifiedId';
$arr[$i++]['tbl'] = 'tChecklistBank';

$arr[$i]['cid']   = 'cCertifiedId'; //點交表銀行清單
$arr[$i++]['tbl'] = 'tChecklist';

$arr[$i]['cid']   = 'cCertifiedId';
$arr[$i++]['tbl'] = 'tContractSales';

$arr[$i]['cid']   = 'bCheck_id';
$arr[$i++]['tbl'] = 'tBranchSms';

$arr[$i]['cid']   = 'cCertifiedId';
$arr[$i++]['tbl'] = 'tContractParking';

$arr[$i]['cid']   = 'cCertifyId';
$arr[$i++]['tbl'] = 'tContractFurniture';

$arr[$i]['cid']   = 'cCertifiedId';
$arr[$i++]['tbl'] = 'tContractAscription';

$arr[$i]['cid']   = 'cCertifiedId';
$arr[$i++]['tbl'] = 'tContractSpecial';

$arr[$i]['cid']   = 'cCertifiedId';
$arr[$i++]['tbl'] = 'tContractPhone';

$arr[$i]['cid']   = 'cCertifiedId';
$arr[$i++]['tbl'] = 'tContractRent';

$arr[$i]['cid']   = 'cCertifiedId';
$arr[$i++]['tbl'] = 'tContractInvoiceQuery';

$arr[$i]['cid']   = 'cCertifiedId';
$arr[$i++]['tbl'] = 'tContractInterestExt';

$arr[$i]['cid']   = 'cCertifiedId';
$arr[$i++]['tbl'] = 'tContractInvoiceExt';

foreach ($arr as $k => $v) {
    fwrite($fw, "========================" . $v['tbl'] . "========================\r\n");

    $sql = 'SELECT * FROM ' . $v['tbl'] . ' WHERE ' . $v['cid'] . '="' . $cid . '";';
    $rs  = $conn->Execute($sql);

    $tmp[] = $rs->fields;

    if ($v['tbl'] == 'tContractCase') {
        $acc = $rs->fields['cEscrowBankAccount'];
    }

    fwrite($fw, json_encode($tmp) . "\r\n");
    fwrite($fw, "============================================================\r\n");

    unset($tmp);

    $sql = 'DELETE FROM ' . $v['tbl'] . ' WHERE ' . $v['cid'] . '="' . $cid . '";';
    $conn->Execute($sql);
}
fclose($fw);

$sql = "UPDATE tBankCode SET bDel ='y' WHERE bAccount ='" . $acc . "'";
$conn->Execute($sql);

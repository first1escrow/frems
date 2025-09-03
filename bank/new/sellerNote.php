<?php
include_once dirname(dirname(dirname(__FILE__))) . '/openadodb.php';

$_POST = escapeStr($_POST);

// 確保 sellerTarget 是陣列再使用 implode
$anothersellerCb = '';
if (isset($_POST['sellerTarget'])) {
    if (is_array($_POST['sellerTarget'])) {
        $anothersellerCb = implode(',', $_POST['sellerTarget']);
    } else {
        // 如果是字串，直接使用該值
        $anothersellerCb = $_POST['sellerTarget'];
    }
}
// echo $anothersellerCb;
// print_r($_POST);
// die;
$sql = "SELECT tId,eSend FROM tBankTransSellerNote WHERE tCertifiedId = '" . $_POST['cId'] . "'";

$rs = $conn->Execute($sql);

if ($rs->fields['eSend'] == 0) {
    if ($rs->fields['tId'] == '') {
        $sql = "INSERT INTO tBankTransSellerNote SET tCertifiedId = '" . $_POST['cId'] . "',tAnother='" . $anothersellerCb . "',tAnotherNote = '" . $_POST['sellerNote'] . "',relation1 ='" . $_POST['relation1'] . "', relation3 ='" . $_POST['relation3'] . "', relation4 ='" . $_POST['relation4'] . "' ";

        $conn->Execute($sql);

    } else {
        $sql = "UPDATE tBankTransSellerNote SET tAnother='" . $anothersellerCb . "',tAnotherNote = '" . $_POST['sellerNote'] . "',relation1 = '" . $_POST['relation1'] . "',relation3 = '" . $_POST['relation3'] . "', relation4 = '" . $_POST['relation4'] . "' WHERE  tCertifiedId = '" . $_POST['cId'] . "'";

        $conn->Execute($sql);
    }
} else {
    //echo 'error';
}

// echo $sql;

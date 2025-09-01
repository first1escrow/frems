<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/includes/writelog.php';

$cid = addslashes(trim($_POST['cid']));

write_log($cid . ':更改電子合約書位置->地政士', "esc");

##tContractRealestate
$query .= " cSmsTarget ='' ,cSerialNumber='',cTelArea = '',cTelMain='',cFaxArea='',cFaxMain='',cZip='',cAddress='',";
$query .= " cSmsTarget1 ='' ,cSerialNumber1='',cTelArea1 = '',cTelMain1='',cFaxArea1='',cFaxMain1='',cZip1='',cAddress1=''";

##查詢
$sql = "SELECT * FROM tContractRealestate WHERE cCertifyId='" . $cid . "'";
$rs  = $conn->Execute($sql);

$branch = re_Store($rs->fields['cBrand'], $rs->fields['cName']);

if ($query != '') {
    $query .= ",";
}

$query .= "cBranchNum='" . $branch . "'";

if ($rs->fields['cBranchNum1'] != '0') {
    $branch1 = re_Store($rs->fields['cBrand1'], $rs->fields['cName1']);

    if ($query != '') {
        $query .= ",";
    }

    $query .= "cBranchNum1='" . $branch1 . "'";

}
##

$sql = "UPDATE tContractRealestate SET " . $query . " WHERE cCertifyId='" . $cid . "'";
$conn->Execute($sql);
##

##tContractCase
$sql = "UPDATE tContractCase SET cCaseFeedBackMoney='', cCaseFeedBackMoney1='', cCaseFeedBackMoney2='',cSpCaseFeedBackMoney='',cSignCategory='2' WHERE cCertifiedId='" . $cid . "'";
$conn->Execute($sql);
##

##tContractScrivener
$sql = "UPDATE tContractScrivener SET cSmsTarget='',cSend2 = '' WHERE cCertifiedId='" . $cid . "'";
$conn->Execute($sql);

##tContractExpenditure
$sql = "DELETE FROM tContractExpenditure WHERE cCertifiedId='" . $cid . "'";
$conn->Execute($sql);

## tContractInvoice
$sql = "DELETE FROM tContractInvoice WHERE cCertifiedId='" . $cid . "'";
$conn->Execute($sql);
##

##tContractIncome
$sql = "UPDATE tContractIncome SET cCertifiedMoney='0' WHERE cCertifiedId='" . $cid . "'";
$conn->Execute($sql);
##

echo '電子合約書切換回地政士';

function re_Store($brand_id, $name)
{
    global $conn;

    if ($bid == '-1') {
        return '-1';
    }

    $sql = "SELECT * FROM tCategoryRealty WHERE cBrandId='" . $brand_id . "'   AND cCompany ='" . $name . "'";
    $rs  = $conn->Execute($sql);

    if ($rs->fields['cId']) {
        return $rs->fields['cId'];
    } else {
        return false;
    }
}

<?php
include_once '../../configs/config.class.php';
include_once '../../includes/lib.php' ;
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;
require_once dirname(dirname(__DIR__)).'/first1DB.php';

$type = trim($_POST['type']);
$cid = trim($_POST['cid']);
$cMoneyCheck = trim($_POST['cMoneyCheck']);
$identifyId = trim($_POST['identifyId']);
$money = trim($_POST['money']);

//賣方金額確認-主要
if($type == 'owner') {
    if($money == 'inv') {
        $sql="UPDATE tContractOwner SET cInvoiceMoneyCheck='".$cMoneyCheck."' WHERE cCertifiedId='".$cid."' AND cIdentifyId='".$identifyId."'";
    }
    if($money == 'int') {
        $sql="UPDATE tContractOwner SET cInterestMoneyCheck='".$cMoneyCheck."' WHERE cCertifiedId='".$cid."' AND cIdentifyId='".$identifyId."'";
    }

    $res = $conn->Execute($sql);
}

//賣方金額確認-其他
if($type == 'ownerOther') {
    if($money == 'inv') {
        $sql="UPDATE tContractOthers SET cInvoiceMoneyCheck='".$cMoneyCheck."' WHERE cIdentity = 2 AND cCertifiedId='".$cid."' AND cIdentifyId='".$identifyId."'";
    }
    if($money == 'int') {
        $sql="UPDATE tContractOthers SET cInterestMoneyCheck='".$cMoneyCheck."' WHERE cIdentity = 2 AND cCertifiedId='".$cid."' AND cIdentifyId='".$identifyId."'";
    }

    $res = $conn->Execute($sql);
}

//買方金額確認-主要
if($type == 'buyer') {
    if($money == 'inv') {
        $sql="UPDATE tContractBuyer SET cInvoiceMoneyCheck='".$cMoneyCheck."' WHERE cCertifiedId='".$cid."' AND cIdentifyId='".$identifyId."'";
    }
    if($money == 'int') {
        $sql="UPDATE tContractBuyer SET cInterestMoneyCheck='".$cMoneyCheck."' WHERE cCertifiedId='".$cid."' AND cIdentifyId='".$identifyId."'";
    }

    $res = $conn->Execute($sql);
}

//買方金額確認-其他
if($type == 'buyerOther') {
    if($money == 'inv') {
        $sql="UPDATE tContractOthers SET cInvoiceMoneyCheck='".$cMoneyCheck."' WHERE cIdentity = 1 AND cCertifiedId='".$cid."' AND cIdentifyId='".$identifyId."'";
    }
    if($money == 'int') {
        $sql="UPDATE tContractOthers SET cInterestMoneyCheck='".$cMoneyCheck."' WHERE cIdentity = 1 AND cCertifiedId='".$cid."' AND cIdentifyId='".$identifyId."'";
    }

    $res = $conn->Execute($sql);
}

//仲介
if($type == 'realestate') {
    $no = trim($_POST['no']);
    if($money == 'inv') {
        $cMoneyCheckNo = 'cInvoiceMoneyCheck' . $no;
        $sql="UPDATE tContractRealestate SET ".$cMoneyCheckNo."='".$cMoneyCheck."' WHERE cCertifyId='".$cid."'";
    }
    if($money == 'int') {
        $cMoneyCheckNo = 'cInterestMoneyCheck' . $no;
        $sql="UPDATE tContractRealestate SET ".$cMoneyCheckNo."='".$cMoneyCheck."' WHERE cCertifyId='".$cid."'";
    }

    $res = $conn->Execute($sql);
}

//地政士
if($type == 'scrivener') {
    if($money == 'inv') {
        $sql="UPDATE tContractScrivener SET cInvoiceMoneyCheck ='".$cMoneyCheck."' WHERE cCertifiedId='".$cid."'";
    }
    if($money == 'int') {
        $sql="UPDATE tContractScrivener SET cInterestMoneyCheck ='".$cMoneyCheck."' WHERE cCertifiedId='".$cid."'";
    }

    $res = $conn->Execute($sql);
}

if($res->EOF) {
    echo "儲存完成";
    exit();

}
echo "儲存失敗";
exit();

?>
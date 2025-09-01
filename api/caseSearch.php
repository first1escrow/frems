<?php
require_once dirname(__DIR__) . '/openadodb.php';

//get var data
// $mobile = $_REQUEST[''] ;
// $certifiedId = $_REQUEST[''] ;

$mobile = '0933720222';
// $mobile = '0938124510' ;
$certifiedId = '002021966';

if (!preg_match("/^09\d{8}$/", $mobile)) {
    // echo "No Mobile Number Founded!!\n" ;
    echo 'error';
    exit;
}
##

//Get Identify (scrivener = 1、realty = 2)
$detail = array();
$data   = array();

$sql    = 'SELECT * FROM tScrivenerSms WHERE sMobile = "' . $mobile . '";';
$rs     = $conn->Execute($sql);
$detail = $rs->fields;

if (empty($detail)) {
    $sql    = 'SELECT * FROM tBranchSms WHERE bMobile = "' . $mobile . '";';
    $rs     = $conn->Execute($sql);
    $detail = $rs->fields;

    if (empty($detail)) {
        $detail = array();
        echo 'error';
        exit;
    } else {
        $data['id']       = $detail['bBranch'];
        $data['title']    = $detail['bNID'];
        $data['name']     = $detail['bName'];
        $data['mobile']   = $mobile;
        $data['line']     = $detail['bLine'];
        $data['identity'] = '2';
    }
} else {
    $data['id']       = $detail['sScrivener'];
    $data['title']    = $detail['sNID'];
    $data['name']     = $detail['sName'];
    $data['mobile']   = $mobile;
    $data['line']     = $detail['sLine'];
    $data['identity'] = '1';
}

// print_r($data) ;
##

// Get Certified Id List
$cid = array();
if ($data['identity'] == '1') {
    // $sql = 'SELECT * FROM tContractScrivener WHERE cScrivener = "'.$data['id'].'" AND cSmsTarget LIKE "%'.$data['mobile'].'%" ORDER BY cCertifiedId ASC;' ;
    $sql = 'SELECT * FROM tContractScrivener WHERE cCertifiedId = "' . $certifiedId . '" AND cScrivener = "' . $data['id'] . '" AND cSmsTarget LIKE "%' . $data['mobile'] . '%" ORDER BY cCertifiedId ASC;';
    // echo $sql ; exit ;
    $rs = $conn->Execute($sql);
    while (!$rs->EOF) {
        $cid[] = $rs->fields['cCertifiedId'];
        $rs->MoveNext();
    }
} else if ($data['identity'] == '2') {
    if ($data['id'] && ($data['id'] != 0)) {
        // $sql = 'SELECT * FROM tContractRealestate WHERE cBranchNum = "'.$data['id'].'" AND cSmsTarget LIKE "%'.$data['mobile'].'%" ORDER BY cCertifiedId ASC;' ;
        $sql = 'SELECT * FROM tContractRealestate WHERE cCertifyId = "' . $certifiedId . '" AND (cBranchNum = "' . $data['id'] . '" OR cBranchNum1 = "' . $data['id'] . '" OR cBranchNum2 = "' . $data['id'] . '") ORDER BY cCertifiedId ASC;';
        // echo $sql ; exit ;
        $rs = $conn->Execute($sql);
        while (!$rs->EOF) {
            $cid[] = $rs->fields['cCertifiedId'];
            $rs->MoveNext();
        }
    }
}

print_r($cid);
##

// if no exist certified id then exit
if (empty($cid)) {
    echo 'no data';
    exit;
}
##

// Get Data List
$list = array();

##

<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/advance.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/includes/lib.php';
require_once dirname(__DIR__) . '/tracelog.php';

$advance = new Advance();

$_POST       = escapeStr($_POST);
$_REQUEST    = escapeStr($_REQUEST);
$CertifiedId = $_REQUEST['id'];

if ($_POST) {
    $str = ($_POST['Checklist'] == 1) ? 'cChecklistBank = 1,' : 'cChecklistBank = "",';

    for ($i = 0; $i < count($_POST['newBankMain']); $i++) {
        if ($_POST['newBankMain'][$i] != '' && $_POST['newBankMain'][$i] != 0) {
            $sql = "INSERT INTO
					tContractCustomerBank
				SET
					cIdentity = 3,
					cCertifiedId = '" . $CertifiedId . "',
					" . $str . "
					cBankMain = '" . $_POST['newBankMain'][$i] . "',
					cBankBranch = '" . $_POST['newcBankBranch'][$i] . "',
					cBankAccountNo = '" . $_POST['newBankAccNum'][$i] . "',
					cBankAccountName = '" . $_POST['newBankAccName'][$i] . "'
				";
            $conn->Execute($sql);
        }
    }

    for ($i = 0; $i < count($_POST['otherBankId']); $i++) {
        $sql = "UPDATE
					tContractCustomerBank
				SET
					" . $str . "
					cBankMain = '" . $_POST['oldBankMain'][$i] . "',
					cBankBranch = '" . $_POST['oldcBankBranch'][$i] . "',
					cBankAccountNo = '" . $_POST['oldBankAccNum'][$i] . "',
					cBankAccountName = '" . $_POST['oldBankAccName'][$i] . "'
				WHERE
					cIdentity = 3 AND cCertifiedId = '" . $CertifiedId . "' AND cId = '" . $_POST['otherBankId'][$i] . "'
				";
        $conn->Execute($sql);
    }
}

//顯示相關資料
$sql   = 'SELECT * FROM tContractCustomerBank WHERE cCertifiedId = "' . $CertifiedId . '" AND cIdentity = 3';
$rs    = $conn->Execute($sql);
$total = $rs->RecordCount();

$i = 0;
while (! $rs->EOF) {
    $list[$i]                    = $rs->fields;
    $list[$i]['no']              = $i + 1;
    $list[$i]['cBankBranchMenu'] = getBankBranch($list[$i]['cBankMain']);

    if ($list[$i]['cChecklistBank'] == 1) {
        $ChecklistBank = "checked=checked";
    }

    $i++;
    $rs->MoveNext();
}

##銀行##
$menuBank[0]       = '';
$menuBankBranch[0] = '';

$sql = 'SELECT bBank4_name,bBank3 FROM tBank WHERE bBank4="" ORDER BY bBank3 ASC;';
$rs  = $conn->CacheExecute($sql);

while (! $rs->EOF) {
    $menuBank[$rs->fields['bBank3']] = $rs->fields['bBank4_name'] . '(' . $rs->fields['bBank3'] . ')';
    $rs->MoveNext();
}

function getBankBranch($bank)
{
    global $conn;

    $sql = 'SELECT bBank4_name,bBank3,bBank4 FROM tBank WHERE bBank3="' . $bank . '" AND bBank4<>"" ORDER BY bBank3,bBank4 ASC;';
    $rs  = $conn->CacheExecute($sql);

    while (! $rs->EOF) {
        $arr[$rs->fields['bBank4']] = $rs->fields['bBank4_name'] . '(' . $rs->fields['bBank4'] . ')';
        $rs->MoveNext();
    }

    return $arr;
}

$smarty->assign('ChecklistBank', $ChecklistBank);
$smarty->assign('list', $list);
$smarty->assign('menuBank', $menuBank);
$smarty->assign('menuBankBranch', $menuBankBranch);
$smarty->assign('cCertifiedId', $CertifiedId);
$smarty->assign('save', $save);
$smarty->display('formbuyownerBank.inc.tpl', '', 'escrow');

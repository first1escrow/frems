<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/advance.class.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/includes/lib.php';
require_once dirname(__DIR__) . '/tracelog.php';
require_once dirname(__DIR__) . '/includes/lib.php';
require_once dirname(__DIR__) . '/includes/IDCheck.php';

$advance = new Advance();

$tlog = new TraceLog();
$tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '多買賣');

$_POST    = escapeStr($_POST);
$_REQUEST = escapeStr($_REQUEST);

// 安全讀取外部輸入，避免 undefined index warnings
$_iden        = isset($_REQUEST['iden']) ? $_REQUEST['iden'] : '';
$save         = isset($_REQUEST['save']) ? $_REQUEST['save'] : '';
$del          = isset($_POST['del']) ? $_POST['del'] : '';
$cCertifiedId = isset($_REQUEST['cCertifyId']) ? $_REQUEST['cCertifyId'] : '';
$cCaseStatus  = isset($_REQUEST['cCaseStatus']) && $_REQUEST['cCaseStatus'] ? $_REQUEST['cCaseStatus'] : 0;

if ($_iden == 'o') { // 賣：2
    $_ide      = '賣';
    $cIdentity = 2;
    $BankIden  = 52;
} else if ($_iden == 'b') { // 買：1
    $_ide      = '買';
    $cIdentity = 1;
    $BankIden  = 53;
} else {
    echo "資料錯誤!!";
    exit;
}

if ($_POST) {
    //刪除
    if ($del == 'ok') {
        $del_no = $_POST['del_no'];

        if ($del_no) {
            $sql = 'DELETE FROM tContractOthers WHERE cId = "' . $del_no . '";';
            $conn->Execute($sql);

            $sql = "DELETE FROM tContractCustomerBank WHERE cOtherId = '" . $del_no . "'";
            $conn->Execute($sql);

            $tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '多筆買賣案件刪除');
        }
    }
}

//顯示相關資料
$sql = 'SELECT
            a.*,
            b.zCity as cRegistCity,
            b.zArea as cRegistArea,
            c.zCity as cBaseCity,
            c.zArea as cBaseArea
        FROM
            tContractOthers AS a
        LEFT JOIN
            tZipArea AS b ON a.cRegistZip=b.zZip
        LEFT JOIN
            tZipArea AS c ON a.cBaseZip=c.zZip
        WHERE
            a.cCertifiedId="' . $cCertifiedId . '"
            AND a.cIdentity="' . $cIdentity . '"
        ORDER BY
            a.cId
        ASC;';
$rs    = $conn->Execute($sql);
$total = $rs->RecordCount();

// 預先初始化 list，避免模板或後續使用時未定義
$list = [];
$i    = 0;
while (! $rs->EOF) {
    $list[$i]              = $rs->fields;
    $list[$i]['no']        = $i + 1;
    $checklistCheckedCount = 0;

    $list[$i]['checkforeign']    = (preg_match("/[a-zA-Z]{2}/", $list[$i]['cIdentifyId']) || preg_match("/[a-zA-z]{1}[8|9]{1}[0-9]{8}/", $list[$i]['cIdentifyId']) || preg_match("/^[0-9]{7}$/", $list[$i]['cIdentifyId'])) ? 'show' : 'display';
    $list[$i]['cNHITaxChecked']  = ($list[$i]['cNHITax'] == 1) ? "checked=checked" : '';
    $list[$i]['checkIDImg']      = (checkUID($list[$i]['cIdentifyId'])) ? '<img src="/images/ok.png">' : '<img src="/images/ng.png">';
    $list[$i]['cRegistAreaMenu'] = getArea($rs->fields['cRegistCity']);
    $list[$i]['cBaseAreaMenu']   = getArea($rs->fields['cBaseCity']);

    if ($list[$i]['cBaseZip'] == $list[$i]['cRegistZip'] && $list[$i]['cBaseAddr'] == $list[$i]['cRegistAddr']) {
        $list[$i]['sameAddr'] = "checked=checked";
    }

    $list[$i]['cChecklistBankChecked'] = ($list[$i]['cChecklistBank'] == 1) ? "checked=checked" : '';

    // 計算不帶入的勾選數
    if ($list[$i]['cChecklistBank'] == 1) {
        $checklistCheckedCount++;
    }

    $list[$i]['OtherNameShow']   = ($list[$i]['cOtherName']) ? '' : 'display';
    $list[$i]['cBirthdayDay']    = $advance->ConvertDateToRoc($list[$i]['cBirthdayDay'], base::DATE_FORMAT_NUM_DATE);
    $list[$i]['cPaymentDate']    = (preg_match("/0000-00-00/", $list[$i]['cPaymentDate'])) ? '' : $list[$i]['cPaymentDate'];
    $list[$i]['cResidentLimit']  = ($list[$i]['cResidentLimit'] == '') ? '0' : $list[$i]['cResidentLimit'];
    $list[$i]['cBankBranchMenu'] = getBankBranch($list[$i]['cBankMain']);
    $list[$i]['OtherBank']       = getBankAccount($list[$i]['cCertifiedId'], $BankIden, $list[$i]['cId']);
    $list[$i]['OtherBankCount']  = count($list[$i]['OtherBank']);

    // 預設 BankChecked，避免模板取值時未定義
    $list[$i]['BankChecked'] = '';
    // 比對是否要將全部的勾選起來
    if ($checklistCheckedCount == ($list[$i]['OtherBankCount'] + 1)) {
        $list[$i]['BankChecked'] = 'checked=checked';
    }

    $i++;
    $rs->MoveNext();
}

##外國人國籍代碼
$sql = "SELECT * FROM  `data_country` Order by cCountry; ";
$rs  = $conn->Execute($sql);

$menuCountry[0] = '請選擇';
while (! $rs->EOF) {
    $menuCountry[$rs->fields['cCode']] = $rs->fields['cCountry'];
    $rs->MoveNext();
}

##縣市
$sql = 'SELECT DISTINCT zCity FROM tZipArea ORDER BY nid ASC';
$rs  = $conn->CacheExecute($sql);

$menuCity[0] = "縣市";
while (! $rs->EOF) {
    $menuCity[$rs->fields['zCity']] = $rs->fields['zCity'];
    $rs->MoveNext();
}

##銀行##
$menuBank[0]       = '';
$menuBankBranch[0] = '';
$sql               = 'SELECT bBank4_name,bBank3 FROM tBank WHERE bBank4="" ORDER BY bBank3 ASC;';
$rs                = $conn->CacheExecute($sql);

while (! $rs->EOF) {
    $menuBank[$rs->fields['bBank3']] = $rs->fields['bBank4_name'] . '(' . $rs->fields['bBank3'] . ')';
    $rs->MoveNext();
}

##檢查是否會計發票關掉
$sql = "SELECT cInvoiceClose,cSignCategory FROM tContractCase WHERE cCertifiedId ='" . $cCertifiedId . "'";
$rs  = $conn->Execute($sql);

$cInvoiceClose = $rs->fields['cInvoiceClose'];
$cSignCategory = $rs->fields['cSignCategory'];

$checkSave = 1;
if ($cInvoiceClose == 'Y' && ! in_array($_SESSION['member_pDep'], [1, 9, 10])) {
    $checkSave = 0;
}

if ($cSignCategory == 0) {
    $checkSave = 0;
}

function getArea($city)
{
    global $conn;

    $arr = [];
    $sql = 'SELECT zZip,zArea FROM tZipArea WHERE zCity="' . $city . '";';
    $rs  = $conn->CacheExecute($sql);

    while (! $rs->EOF) {
        $arr[$rs->fields['zZip']] = $rs->fields['zArea'];
        $rs->moveNext();
    }

    return $arr;
}

function getBankBranch($bank)
{
    global $conn;

    $arr = [];
    $sql = 'SELECT bBank4_name,bBank3,bBank4 FROM tBank WHERE bBank3="' . $bank . '" AND bBank4<>"" ORDER BY bBank3,bBank4 ASC;';
    $rs  = $conn->CacheExecute($sql);

    while (! $rs->EOF) {
        $arr[$rs->fields['bBank4']] = $rs->fields['bBank4_name'] . '(' . $rs->fields['bBank4'] . ')';
        $rs->MoveNext();
    }

    return $arr;
}

function getBankAccount($cId, $iden, $id)
{
    global $conn, $checklistCheckedCount; // 計算不帶入的勾選數

    $arr = [];
    $sql = "SELECT * FROM tContractCustomerBank WHERE cCertifiedId ='" . $cId . "' AND cIdentity ='" . $iden . "' AND cOtherId ='" . $id . "' ORDER bY cId ASC";
    $rs  = $conn->Execute($sql);

    $i = 0;
    while (! $rs->EOF) {
        $arr[$i]                          = $rs->fields;
        $arr[$i]['bankBranch']            = getBankBranch($rs->fields['cBankMain']);
        $arr[$i]['index']                 = ($i + 1);
        $arr[$i]['cChecklistBankChecked'] = ($arr[$i]['cChecklistBank'] == 1) ? 'checked=checked' : '';

        // 計算不帶入的勾選數
        if ($arr[$i]['cChecklistBank'] == 1) {
            $checklistCheckedCount++;
        }

        $i++;
        $rs->MoveNext();
    }

    return $arr;
}

$smarty->assign('del', $del);
$smarty->assign('checkSave', $checkSave);
$smarty->assign('SignCategory', $cSignCategory);
$smarty->assign('InvoiceClose', $cInvoiceClose);
$smarty->assign('countData', $total);
$smarty->assign('menuBank', $menuBank);
$smarty->assign('menuBankBranch', $menuBankBranch);
$smarty->assign('cIdentity', $cIdentity);
$smarty->assign('meunCity', $menuCity);
$smarty->assign('menuArea', ['0' => '區域']);
$smarty->assign('menuCountry', $menuCountry);
$smarty->assign('menuResident', [0 => '否', 1 => '是']);
$smarty->assign('Resident', '0');
$smarty->assign('list', $list);
$smarty->assign('_iden', $_iden);
$smarty->assign('_ide', $_ide);
$smarty->assign('cCertifiedId', $cCertifiedId);
$smarty->assign('save', $save);
$smarty->assign('cCaseStatus', $cCaseStatus);

$smarty->display('buyerownerlist.inc.tpl', '', 'escrow');

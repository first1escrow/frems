<?php
require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
require_once dirname(dirname(__DIR__)) . '/class/SmartyMain.class.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/tracelog.php';
require_once __DIR__ . '/bookFunction.php';

$_POST = escapeStr($_POST);

$tlog = new TraceLog();
$tlog->selectWrite($_SESSION['member_id'], ' ', '新增指示書');

//比對跟第一次建檔
$sql = "SELECT tVR_Code,tBank_kind,tCode,tCode2,tMoney,tBankCode FROM tBankTrans WHERE tId ='" . $_POST['id'] . "'";
$rs  = $conn->Execute($sql);

$dataTran = $rs->fields;
####

$ck = checkBook($_POST['id'], $dataTran['tCode2']); //0:有資料 1更改了交易類別 2沒有資料

if ($ck != 0) { //確認使否建立指示書了
    $tmp          = getBank(substr($dataTran['tBankCode'], 0, 3), substr($dataTran['tBankCode'], 3));
    $reBank       = $tmp['BankName']; //預留
    $reBankBranch = $tmp['BanchName'];

    //銀行類別
    if (preg_match("/^999850/", $dataTran['tVR_Code'])) {
        $bank = 4;
    } else if (preg_match("/^999860/", $dataTran['tVR_Code'])) {
        $bank = 6;
    } else if (preg_match("/^96988/", $dataTran['tVR_Code'])) {
        $bank         = 5;
        $reBankBranch = '';
    } else if (preg_match("/^55006/", $dataTran['tVR_Code'])) {
        $bank = 7;
    } else {
        $bank = 1; //一銀20160603++
    }

    //2虛轉虛3開票4繳稅5臨櫃6補通訊7退票
    $cat = getCategoryBookId($dataTran["tCode2"]); //

    $sql = "INSERT INTO tBankTrankBook(
				bCertifiedId,
				bBankTranId,
				bMoney,
				bBank,
				bCategory,
				bReBank,
				bCreatorId,
				bCreatName,
				bCreatTime
			)VALUES(
				'" . $dataTran['tVR_Code'] . "',
				'" . $_POST['id'] . "',
				'" . $dataTran['bMoney'] . "',
				'" . $bank . "',
				'" . $cat . "',
				'" . $reBankBranch . "',
				'" . $_SESSION['member_id'] . "',
				'" . $_SESSION['member_name'] . "',
				'" . date('Y-m-d H:i:s') . "'
			)";
    $conn->Execute($sql);

    $bank = $cat = null;
    unset($bank, $cat);
}
###########################
$sql  = "SELECT *,(SELECT cName FROM tCategoryBook WHERE cId =bCategory) AS bCategoryName,(SELECT cTrustAccountName FROM tContractBank WHERE cId=bBank) AS cTrustAccountName FROM tBankTrankBook WHERE bBankTranId ='" . $_POST['id'] . "' AND bDel = 0";
$rs   = $conn->Execute($sql);
$data = $rs->fields;

$data['CertifiedId_9'] = substr($data['bCertifiedId'], 5);
$data['expMoney']      = $dataTran['tMoney'];
$data['bStatusName']   = BookStatus($rs->fields['bStatus']);

//細項
$sql = "SELECT * FROM tBankTrankBookDetail WHERE bTrankBookId ='" . $data['bId'] . "' AND bDel = 0";
$rs  = $conn->Execute($sql);

while (!$rs->EOF) {
    $data_detail[] = $rs->fields;
    $rs->MoveNext();
}
###

//人員傳真號碼
$sql = "SELECT pFaxNum FROM tPeopleInfo WHERE pId ='" . $data['bCreatorId'] . "'";
$rs  = $conn->Execute($sql);
$Fax = $rs->fields['pFaxNum'];

$smarty->assign('ck', $ck);
$smarty->assign('Fax', $Fax);
$smarty->assign('data', $data);
$smarty->assign('data_detail', $data_detail);
$smarty->assign('Mod', 0);
$smarty->assign('opStaus', array(0 => '待確認', 1 => '待審核', 2 => '已審核'));
$smarty->assign('stopStatus', array(0 => '禁止', 1 => '不禁止'));

##############
if ($data['bBank'] == 4 || $data['bBank'] == 6) {
    //一般的不會出現在這
    if ($data['bCategory'] == 2) { //虛轉虛跟大額繳稅之類的很像所以共用
        $smarty->assign('pdf', 'sinopac02_pdf.php');
        $smarty->display('IBook03.inc.tpl', '', 'bank');
    } else if ($data['bCategory'] == 3 || $data['bCategory'] == 4 || $data['bCategory'] == 5) {
        $smarty->assign('pdf', 'sinopac03_pdf.php');
        $smarty->display('IBook03.inc.tpl', '', 'bank');
    }
} else if ($data['bBank'] == 1 || $data['bBank'] == 7) {
    if ($data['bCategory'] == 3 || $data['bCategory'] == 4 || $data['bCategory'] == 5) {
        $smarty->assign('pdf', 'firstInform1.php');
        $smarty->display('IBook03.inc.tpl', '', 'bank');
    } else if ($data['bCategory'] == 2) {
        echo '無虛轉虛';
    }
} else if ($data['bBank'] == 5) {
    if ($data['bCategory'] == 3 || $data['bCategory'] == 4 || $data['bCategory'] == 5) {
        $smarty->assign('pdf', 'taishin03_pdf.php');
        $smarty->display('IBook03.inc.tpl', '', 'bank');
    } else if ($data['bCategory'] == 2) {
        echo '無虛轉虛';
    }
}
##

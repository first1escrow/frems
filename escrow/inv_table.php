<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
include_once dirname(__DIR__) . '/class/contract.class.php';
require_once dirname(__DIR__) . '/openadodb.php';

$id = addslashes(trim($_POST['id']));

$contract = new Contract();

$data_buyer     = $contract->GetBuyer($id);
$data_owner     = $contract->GetOwner($id);
$data_scrivener = $contract->GetScrivener($id);
$data_realstate = $contract->GetRealstate($id);
$data_invoice   = $contract->GetInvoice($id);
##店家資料
$sql = "
	SELECT
		(SELECT b.bStore FROM tBranch AS b WHERE b.bId=cr.cBranchNum) AS store,
		(SELECT b.bStore FROM tBranch AS b WHERE b.bId=cr.cBranchNum1) AS store1,
		(SELECT b.bStore FROM tBranch AS b WHERE b.bId=cr.cBranchNum2) AS store2,
		(SELECT b.bStore FROM tBranch AS b WHERE b.bId=cr.cBranchNum3) AS store3,
		cr.cBranchNum1,
		cr.cBranchNum2,
		cr.cBranchNum3,
		cr.cInvoicePrint,
		cr.cInvoicePrint1,
		cr.cInvoicePrint2
	FROM
		 tContractRealestate AS cr
	WHERE
		cr.cCertifyId = '" . $id . "'";
// echo $sql;
$rs = $conn->Execute($sql);

$branch_type1 = $rs->fields['store'];
$branch_type2 = '';
$branch_type3 = '';
$branch_type4 = '';

if ($rs->fields['cBranchNum1'] > 0) {
    $branch_type2 = $rs->fields['store1'];
}

if ($rs->fields['cBranchNum2'] > 0) {
    $branch_type3 = $rs->fields['store2'];
}
if ($rs->fields['cBranchNum3'] > 0) {
    $branch_type4 = $rs->fields['store3'];
}

// echo $branch_type1;

//取得地政士資料
$scr = [];
$sql = 'SELECT * FROM tScrivener WHERE sId="' . $data_scrivener['cScrivener'] . '";';
$rs  = $conn->Execute($sql);
$scr = $rs->fields;

$data_scrivener['sName'] = $rs->fields['sName'];

##
##
//發票指定對象
$data_invoice_another = [];
$sql                  = "SELECT * FROM tContractInvoiceExt  WHERE cCertifiedId ='" . $id . "'";

$rs = $conn->Execute($sql);
$i  = 0;
while (! $rs->EOF) {
    if ($rs->fields['cInvoiceDonate'] == 1) {

        $rs->fields['cInvoiceDonate'] = '[捐贈]';
    } else {
        $rs->fields['cInvoiceDonate'] = '';
    }
    $data_invoice_another[] = $rs->fields;

    $rs->MoveNext();
}

//取得其他買賣方利息金額(增加查詢姓名及發票金額)
$buyer_other = [];
$owner_other = [];
$int_money   = 0;

$sql = 'SELECT cInterestMoney,cInvoiceMoney,cName,cIdentity,cInvoiceDonate,cId,cInvoicePrint, cIdentifyId, cInvoiceMoneyCheck FROM tContractOthers WHERE cCertifiedId="' . $id . '" ORDER BY cId ASC;';

$rs = $conn->Execute($sql);
while (! $rs->EOF) {

    if ($rs->fields['cIdentity'] == 1) {

        $buyer_other[] = $rs->fields;
    } elseif ($rs->fields['cIdentity'] == 2) {

        $owner_other[] = $rs->fields;
    }

    $int_money += $rs->fields['cInterestMoney'] + 1 - 1;
    $rs->MoveNext();
}
unset($rs);

######
$smarty->assign('data_invoice', $data_invoice);

$smarty->assign('data_buyer', $data_buyer);
$smarty->assign('data_owner', $data_owner);
$smarty->assign('data_realstate', $data_realstate);
$smarty->assign('branch_type1', $branch_type1); //仲介店(1)
$smarty->assign('branch_type2', $branch_type2); //仲介店(2)
$smarty->assign('branch_type3', $branch_type3); //仲介店(3)
$smarty->assign('branch_type4', $branch_type4); //仲介店(4)
$smarty->assign('data_scrivener', $data_scrivener);

$smarty->assign('data_invoice_another', $data_invoice_another);
$smarty->assign('data_buyer_other', $buyer_other);
$smarty->assign('buyer_other_count', is_array($buyer_other) ? count($buyer_other) + 2 : 2);
$smarty->assign('data_owner_other', $owner_other);
$smarty->assign('owner_other_count', is_array($owner_other) ? count($owner_other) + 2 : 2);
$smarty->assign('branch_count', isset($branch_count) && is_array($branch_count) ? count($branch_count) + 2 : 2);

$smarty->display('inv_table.inc.tpl', '', 'escrow');

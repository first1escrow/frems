<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/class/contract.class.php';
require_once dirname(__DIR__) . '/openadodb.php';

// $_POST['id'] = '003030947';
$id = addslashes(trim($_POST['id']));

$contract = new Contract();

$data_buyer     = $contract->GetBuyer($id);
$data_owner     = $contract->GetOwner($id);
$data_scrivener = $contract->GetScrivener($id);
$data_realstate = $contract->GetRealstate($id);

##店家資料
$sql = "
	SELECT
		(SELECT b.bStore FROM tBranch AS b WHERE b.bId=cr.cBranchNum) AS store,
		(SELECT b.bStore FROM tBranch AS b WHERE b.bId=cr.cBranchNum1) AS store1,
		(SELECT b.bStore FROM tBranch AS b WHERE b.bId=cr.cBranchNum2) AS store2,
		(SELECT b.bStore FROM tBranch AS b WHERE b.bId=cr.cBranchNum3) AS store3,
		cr.cBranchNum1,
		cr.cBranchNum2,
		cr.cBranchNum3
	FROM
		 tContractRealestate AS cr
	WHERE
		cr.cCertifyId = '" . $id . "'";
// echo $sql;
$rs = $conn->Execute($sql);

// 初始化分店變數
$branch_type1 = isset($rs->fields['store']) ? $rs->fields['store'] : '';
$branch_type2 = '';
$branch_type3 = '';
$branch_type4 = '';

if (isset($rs->fields['cBranchNum1']) && $rs->fields['cBranchNum1'] > 0) {
    $branch_type2 = $rs->fields['store1'];
}

if (isset($rs->fields['cBranchNum2']) && $rs->fields['cBranchNum2'] > 0) {
    $branch_type3 = $rs->fields['store2'];
}
if (isset($rs->fields['cBranchNum3']) && $rs->fields['cBranchNum3'] > 0) {
    $branch_type4 = $rs->fields['store3'];
}

//取得地政士資料
$scr = [];
$sql = 'SELECT * FROM tScrivener WHERE sId="' . $data_scrivener['cScrivener'] . '";';
$rs  = $conn->Execute($sql);
$scr = $rs->fields;

$data_scrivener['sName'] = $rs->fields['sName'];

##
##
//取得利息
$sql       = 'SELECT * FROM tChecklist WHERE cCertifiedId="' . $id . '";';
$rs        = $conn->Execute($sql);
$int_total = '尚未產生利息';

if ($rs->RecordCount() > 0) {
    $int_total = $rs->fields['cInterest'] + 1 - 1;
    $int_total += $rs->fields['bInterest'] + 1 - 1;
    $int_money = 0;

    $int_total = '<span id="int_total">NT$' . $int_total . '元</span><input type="hidden" name="int_total" value="' . $int_total . '">';

    //取得買方利息金額
    $sql = 'SELECT cInterestMoney FROM tContractBuyer WHERE cCertifiedId="' . $id . '";';
    $rs  = $conn->Execute($sql);
    $int_money += $rs->fields['cInterestMoney'] + 1 - 1;
    unset($rs);
    ##

    //取得賣方利息金額
    $sql = 'SELECT cInterestMoney FROM tContractOwner WHERE cCertifiedId="' . $id . '";';
    $rs  = $conn->Execute($sql);
    $int_money += $rs->fields['cInterestMoney'] + 1 - 1;
    unset($rs);
    ##

    //取得其他買賣方利息金額(增加查詢姓名及發票金額)
    $sql = 'SELECT cInterestMoney,cInvoiceMoney,cName,cIdentity,cInvoiceDonate,cId, cIdentifyId, cInterestMoneyCheck FROM tContractOthers WHERE cCertifiedId="' . $id . '" ORDER BY cId ASC;';

    // 初始化買方和賣方陣列
    $buyer_other = [];
    $owner_other = [];

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

    ##

    //取得仲介利息金額
    $sql = 'SELECT cInterestMoney,cInterestMoney1,cInterestMoney2 FROM tContractRealestate WHERE cCertifyId="' . $id . '";';
    $rs  = $conn->Execute($sql);
    $int_money += $rs->fields['cInterestMoney'] + 1 - 1;
    $int_money += $rs->fields['cInterestMoney1'] + 1 - 1;
    $int_money += $rs->fields['cInterestMoney2'] + 1 - 1;
    unset($rs);
    ##

    //取得代書利息金額
    $sql = 'SELECT cInterestMoney,cInvoiceDonate FROM tContractScrivener WHERE cCertifiedId="' . $id . '";';
    $rs  = $conn->Execute($sql);
    $int_money += $rs->fields['cInterestMoney'] + 1 - 1;
    unset($rs);
    ##

    //利息指定對象
    $sql = "SELECT * FROM  tContractInterestExt  WHERE cCertifiedId ='" . $id . "'";

    // 初始化利息指定對象陣列
    $data_int_another = [];

    $rs = $conn->Execute($sql);
    $i  = 0;
    while (! $rs->EOF) {

        $data_int_another[] = $rs->fields;

        $int_money += $rs->fields['cInterestMoney'] + 1 - 1;

        $rs->MoveNext();
    }

    $int_total .= '<span id="int_money">(已分配：' . $int_money . '元)</span><input type="hidden" name="int_money" value="' . $int_money . '">';
}
##

######

$smarty->assign('int_total', $int_total);
$smarty->assign('data_buyer', $data_buyer);
$smarty->assign('data_owner', $data_owner);
$smarty->assign('data_realstate', $data_realstate);
$smarty->assign('branch_type1', $branch_type1); //仲介店(1)
$smarty->assign('branch_type2', $branch_type2); //仲介店(2)
$smarty->assign('branch_type3', $branch_type3); //仲介店(3)
$smarty->assign('branch_type4', $branch_type4); //仲介店(4)
$smarty->assign('data_scrivener', $data_scrivener);

// 確保陣列已初始化
$data_int_another = isset($data_int_another) ? $data_int_another : [];
$buyer_other      = isset($buyer_other) ? $buyer_other : [];
$owner_other      = isset($owner_other) ? $owner_other : [];
$branch_count     = isset($branch_count) ? $branch_count : [];

$smarty->assign('data_int_another', $data_int_another);
$smarty->assign('data_buyer_other', $buyer_other);
$smarty->assign('buyer_other_count', count($buyer_other) + 2);
$smarty->assign('data_owner_other', $owner_other);
$smarty->assign('owner_other_count', count($owner_other) + 2);
$smarty->assign('branch_count', count($branch_count) + 2);

$smarty->display('int_table.inc.tpl', '', 'escrow');

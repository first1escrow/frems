<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once 'class/contract.class.php';
include_once 'class/brand.class.php';
include_once 'class/scrivener.class.php';

$msg = '';

if ($_POST['certifiedId']) {
	$contract = new Contract();
	$sc = new Scrivener();
	
	$date = getBankTransDate($_POST['certifiedId']);
	
	$total = 0;

	$list = array();
	
	##
	
	$sql = "SELECT cDeliveryNo FROM tContractInvoiceQuery WHERE cDeliveryNo LIKE '".$date."%' ORDER BY cDeliveryNo DESC LIMIT 1";
	$rs = $conn->Execute($sql);
	$flowNo = (int)substr($rs->fields['cDeliveryNo'], 7) ;

	//合約書買方
	$data_buyer = $contract->GetBuyer($_POST['certifiedId']);

	if ($data_buyer['cInvoiceMoney'] > 0) {
		$giver_num = $_POST['certifiedId'].'1'.str_pad((++ $total),2,'0',STR_PAD_LEFT) ;		//履保編號9碼+對象1碼(買1賣2仲介3地政士4)+人數2碼
		$invoiceDonate = ($data_buyer['cInvoiceDonate'] == 1)?1:0;
	 
		$data = array();
		$data['cCertifiedId'] = $_POST['certifiedId'];
		$data['cDeliveryNo'] = $date.str_pad($flowNo ++,6,'0',STR_PAD_LEFT) ;
		$data['cDefineFields'] = $data['cDeliveryNo'].$giver_num.$invoiceDonate;
		$data['cInvoiceNo'] = '';
		$data['cInvoiceDate'] = '';
		$data['cTB'] = 'tContractBuyer';
		$data['cTargetId'] = $data_buyer['cId'];
		$data['cName'] = $data_buyer['cName'];
		$data['cIdentifyId'] = $data_buyer['cIdentifyId'];
		$data['cAcc'] = $data_buyer['cIdentifyId'];
		$data['cPass'] = $_POST['certifiedId'];
		$data['cMoney'] = $data_buyer['cInvoiceMoney'];
		$data['cQuery'] = 'Y';

		array_push($list, $data);

		unset($data);
	}
	

	//發票其他對象(買)[買方改開給別人]
	getContractInvoiceExt($_POST['certifiedId'],'tContractBuyer');
	//合約書其他買方
	getContractOtherInvoice($_POST['certifiedId'],1);

	//發票其他對象(其他買方)
	getContractInvoiceExt($_POST['certifiedId'],'tContractOthersB');

	//合約書賣方
	$data_owner = $contract->GetOwner($_POST['certifiedId']);

	if ($data_owner['cInvoiceMoney'] > 0) {
		$giver_num = $_POST['certifiedId'].'2'.str_pad((++ $total),2,'0',STR_PAD_LEFT) ;		//履保編號9碼+對象1碼(買1賣2仲介3地政士4)+人數2碼
		$invoiceDonate = ($data_owner['cInvoiceDonate'] == 1)?1:0;
	 
		$data = array();
		$data['cCertifiedId'] = $_POST['certifiedId'];
		$data['cDeliveryNo'] = $date.str_pad($flowNo ++,6,'0',STR_PAD_LEFT) ;
		$data['cDefineFields'] = $data['cDeliveryNo'].$giver_num.$invoiceDonate;
		$data['cInvoiceNo'] = '';
		$data['cInvoiceDate'] = '';
		$data['cTB'] = 'tContractOwner';
		$data['cTargetId'] = $data_owner['cId'];
		$data['cName'] = $data_owner['cName'];
		$data['cIdentifyId'] = $data_owner['cIdentifyId'];
		$data['cAcc'] = $data_owner['cIdentifyId'];
		$data['cPass'] = $_POST['certifiedId'];
		$data['cMoney'] = $data_owner['cInvoiceMoney'];
		$data['cQuery'] = 'Y';

		array_push($list, $data);

		unset($data);
	}

	//發票其他對象(賣)[賣方改開給別人]
	getContractInvoiceExt($_POST['certifiedId'],'tContractOwner');

	//合約書其他賣方
	getContractOtherInvoice($_POST['certifiedId'],2);

	//發票其他對象(其他賣方)
	getContractInvoiceExt($_POST['certifiedId'],'tContractOthersO');

	##仲介
	$data_res = $contract->GetRealstate($_POST['certifiedId']);
	//仲介(1)
	if ($data_res['cBranchNum'] > 0 && $data_res['cInvoiceMoney'] > 0) {
		$branch = getBranch($data_res['cBranchNum']);

		$giver_num = $_POST['certifiedId'].'3'.str_pad((++ $total),2,'0',STR_PAD_LEFT) ;

		$invoiceDonate = ($data_res['cInvoiceDonate'] == 1)?1:0;

		$data = array();
		$data['cCertifiedId'] = $_POST['certifiedId'];
		$data['cDeliveryNo'] = $date.str_pad($flowNo ++,6,'0',STR_PAD_LEFT) ;
		$data['cDefineFields'] = $data['cDeliveryNo'].$giver_num.$invoiceDonate;
		$data['cInvoiceNo'] = '';
		$data['cInvoiceDate'] = '';
		$data['cTB'] = 'tContractRealestate_R';
		$data['cTargetId'] = $data_res['cBranchNum'];
		$data['cName'] = $branch['bName'];
		$data['cIdentifyId'] = $branch['bSerialnum'];
		$data['cAcc'] = $branch['code'];
		$data['cPass'] = $branch['bPassword'];
		$data['cMoney'] = $data_res['cInvoiceMoney'];
		$data['cQuery'] = 'Y';

		array_push($list, $data);
		unset($branch);unset($data);
	}

	//發票其他對象(仲介)[仲介改開給別人]
	getContractInvoiceExt($_POST['certifiedId'],'tContractRealestate');


	//仲介(2)
	
	if ($data_res['cBranchNum1'] > 0 && $data_res['cInvoiceMoney1'] > 0) {
		$branch = getBranch($data_res['cBranchNum1']);

		$giver_num = $_POST['certifiedId'].'3'.str_pad((++ $total),2,'0',STR_PAD_LEFT) ;

		$invoiceDonate = ($data_res['cInvoiceDonate1'] == 1)?1:0;

		$data = array();
		$data['cCertifiedId'] = $_POST['certifiedId'];
		$data['cDeliveryNo'] = $date.str_pad($flowNo ++,6,'0',STR_PAD_LEFT) ;
		$data['cDefineFields'] = $data['cDeliveryNo'].$giver_num.$invoiceDonate;
		$data['cInvoiceNo'] = '';
		$data['cInvoiceDate'] = '';
		$data['cTB'] = 'tContractRealestate_R1';
		$data['cTargetId'] = $data_res['cBranchNum1'];
		$data['cName'] = $branch['bName'];
		$data['cIdentifyId'] = $branch['bSerialnum'];
		$data['cAcc'] = $branch['code'];
		$data['cPass'] = $branch['bPassword'];
		$data['cMoney'] = $data_res['cInvoiceMoney1'];
		$data['cQuery'] = 'Y';

		array_push($list, $data);
		unset($branch);unset($data);
	}

	//發票其他對象(仲介(2))[仲介(2)改開給別人]
	getContractInvoiceExt($_POST['certifiedId'],'tContractRealestate1');

	//仲介(3)
	if ($data_res['cBranchNum2'] > 0 && $data_res['cInvoiceMoney2'] > 0) {
		$branch = getBranch($data_res['cBranchNum2']);

		$giver_num = $_POST['certifiedId'].'3'.str_pad((++ $total),2,'0',STR_PAD_LEFT) ;

		$invoiceDonate = ($data_res['cInvoiceDonate2'] == 1)?1:0;

		$data = array();
		$data['cCertifiedId'] = $_POST['certifiedId'];
		$data['cDeliveryNo'] = $date.str_pad($flowNo ++,6,'0',STR_PAD_LEFT) ;
		$data['cDefineFields'] = $data['cDeliveryNo'].$giver_num.$invoiceDonate;
		$data['cInvoiceNo'] = '';
		$data['cInvoiceDate'] = '';
		$data['cTB'] = 'tContractRealestate_R2';
		$data['cTargetId'] = $data_res['cBranchNum2'];
		$data['cName'] = $branch['bName'];
		$data['cIdentifyId'] = $branch['bSerialnum'];
		$data['cAcc'] = $branch['code'];
		$data['cPass'] = $branch['bPassword'];
		$data['cMoney'] = $data_res['cInvoiceMoney2'];
		$data['cQuery'] = 'Y';

		array_push($list, $data);
		unset($branch);unset($data);
	}

	//發票其他對象(仲介(3))[仲介(3)改開給別人]
	getContractInvoiceExt($_POST['certifiedId'],'tContractRealestate2');

	//仲介(4)
	if ($data_res['cBranchNum3'] > 0 && $data_res['cInvoiceMoney3'] > 0) {
		$branch = getBranch($data_res['cBranchNum3']);

		$giver_num = $_POST['certifiedId'].'3'.str_pad((++ $total),2,'0',STR_PAD_LEFT) ;

		$invoiceDonate = ($data_res['cInvoiceDonate3'] == 1)?1:0;

		$data = array();
		$data['cCertifiedId'] = $_POST['certifiedId'];
		$data['cDeliveryNo'] = $date.str_pad($flowNo ++,6,'0',STR_PAD_LEFT) ;
		$data['cDefineFields'] = $data['cDeliveryNo'].$giver_num.$invoiceDonate;
		$data['cInvoiceNo'] = '';
		$data['cInvoiceDate'] = '';
		$data['cTB'] = 'tContractRealestate_R3';
		$data['cTargetId'] = $data_res['cBranchNum3'];
		$data['cName'] = $branch['bName'];
		$data['cIdentifyId'] = $branch['bSerialnum'];
		$data['cAcc'] = $branch['code'];
		$data['cPass'] = $branch['bPassword'];
		$data['cMoney'] = $data_res['cInvoiceMoney3'];
		$data['cQuery'] = 'Y';

		array_push($list, $data);
		unset($branch);unset($data);
	}

	//發票其他對象(仲介(4))[仲介(4)改開給別人]
	getContractInvoiceExt($_POST['certifiedId'],'tContractRealestate3');


	//合約書代書
	$data_invoice = $contract->GetInvoice($_POST['certifiedId']);
	$data_sc = $contract->GetScrivener($v['cCertifiedId']);
	$info_sc = $sc->GetScrivenerInfo($data_sc['cScrivener']);

	if ($data_invoice['cInvoiceScrivener'] > 0) {
		$giver_num = $_POST['certifiedId'].'4'.str_pad((++ $total),2,'0',STR_PAD_LEFT) ;		//履保編號9碼+對象1碼(買1賣2仲介3地政士4)+人數2碼
		$invoiceDonate = ($data_sc['cInvoiceDonate'] == 1)?1:0;


		if ($data_sc['cInvoiceTo'] == '2') {
			$_name = $info_sc['sOffice'] ;					
		}else{
			$_name = $info_sc['sName'] ;
		}
		
		$data = array();
		$data['cCertifiedId'] = $_POST['certifiedId'];
		$data['cDeliveryNo'] = $date.str_pad($flowNo ++,6,'0',STR_PAD_LEFT) ;
		$data['cDefineFields'] = $data['cDeliveryNo'].$giver_num.$invoiceDonate;
		$data['cInvoiceNo'] = '';
		$data['cInvoiceDate'] = '';
		$data['cTB'] = 'tContractScrivener';
		$data['cTargetId'] = $info_sc['sId'];
		$data['cName'] = $_name;
		$data['cIdentifyId'] = $info_sc['sIdentifyId'];
		$data['cAcc'] = 'SC'.str_pad($info_sc['sId'],4,'0',STR_PAD_LEFT);
		$data['cPass'] = $_POST['certifiedId'];
		$data['cMoney'] = $data_invoice['cInvoiceScrivener'];
		$data['cQuery'] = 'Y';

		array_push($list, $data);

		unset($data);
	}
	
	//清除舊資料
	$sql = "DELETE FROM tContractInvoiceQuery WHERE cCertifiedId='".$_POST['certifiedId']."'";
	$conn->Execute($sql);
	//新增資料
	foreach ($list as $data) {
		$conn->AutoExecute("tContractInvoiceQuery", $data, 'INSERT');
	}
	
	$msg = '資料已重新建立';
}


function getContractInvoiceExt($id,$tb){
	global $conn;
	global $list;
	global $total;
	global $date;
	global $flowNo;

	if ($tb == 'tContractBuyer' || $tb == 'tContractOthersB') {
		$cTB = 'tContractInvoiceExt_B';
		$iden = 1;
	}elseif ($tb == 'tContractOwner' || $tb == 'tContractOthersO') {
		$cTB = 'tContractInvoiceExt_O';
		$iden = 2;
	}elseif ($tb == 'tContractRealestate') {
		$cTB = 'tContractInvoiceExt_R';
		$iden = 3;
	}

	$sql = 'SELECT * FROM tContractInvoiceExt WHERE cCertifiedId="'.$id.'" AND cDBName="'.$tb.'" ORDER BY cId ASC;' ;
	
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		if ($rs->fields['cInvoiceMoney'] > 0) {
			$giver_num = $id.$iden.str_pad((++ $total),2,'0',STR_PAD_LEFT) ;		//履保編號9碼+對象1碼(買1賣2仲介3地政士4)+人數2碼
			$invoiceDonate = ($rs->fields['cInvoiceDonate'] == 1)?1:0;

			$data = array();
			$data['cCertifiedId'] = $id;
			$data['cDeliveryNo'] = $date.str_pad($flowNo ++,6,'0',STR_PAD_LEFT) ;
			$data['cDefineFields'] = $data['cDeliveryNo'].$giver_num.$invoiceDonate;
			$data['cInvoiceNo'] = '';
			$data['cInvoiceDate'] = '';
			$data['cTB'] = $cTB;
			$data['cTargetId'] = $rs->fields['cId'];
			$data['cName'] = $rs->fields['cName'];
			$data['cIdentifyId'] = $rs->fields['cIdentifyId'];
			$data['cAcc'] = $rs->fields['cIdentifyId'];
			$data['cPass'] = $id;
			$data['cMoney'] = $rs->fields['cInvoiceMoney'];
			$data['cQuery'] = 'Y';

			array_push($list, $data);
			unset($data);
		}
		

		$rs->MoveNext();
	}

	

}

function getContractOtherInvoice($id,$identity){
	global $conn;
	global $list;
	global $total;
	global $date;
	global $flowNo;

	if ($identity == 1) {
		$cTB = 'tContractOthers_B';
	}elseif ($identity == 2) {
		$cTB = 'tContractOthers_O';
	}

	$sql = 'SELECT * FROM tContractOthers WHERE	cCertifiedId="'.$id.'" AND cIdentity="'.$identity.'"	ORDER BY cId ASC' ;
	$rs = $conn->Execute($sql)	;
	while (!$rs->EOF) {
		if ($rs->fields['cInvoiceMoney'] > 0) {
			$giver_num = $id.$identity.str_pad((++ $total),2,'0',STR_PAD_LEFT) ;		//履保編號9碼+對象1碼(買1賣2仲介3地政士4)+人數2碼
			$invoiceDonate = ($rs->fields['cInvoiceDonate'] == 1)?1:0;

			$data = array();
			$data['cCertifiedId'] = $id;
			$data['cDeliveryNo'] = $date.str_pad($flowNo ++,6,'0',STR_PAD_LEFT) ;
			$data['cDefineFields'] = $data['cDeliveryNo'].$giver_num.$invoiceDonate;
			$data['cInvoiceNo'] = '';
			$data['cInvoiceDate'] = '';
			$data['cTB'] = $cTB;
			$data['cTargetId'] = $rs->fields['cId'];
			$data['cName'] = $rs->fields['cName'];
			$data['cIdentifyId'] = $rs->fields['cIdentifyId'];
			$data['cAcc'] = $rs->fields['cIdentifyId'];
			$data['cPass'] = $id;
			$data['cMoney'] = $rs->fields['cInvoiceMoney'];
			$data['cQuery'] = 'Y';

			array_push($list, $data);
			unset($data);
		}
		

		$rs->MoveNext();
	}
}



function getBankTransDate($id){
	global $conn;

	$sql = '
			SELECT 
				tra.tVR_Code vr_code,
				tra.tMoney tMoney,
				SUBSTR(tExport_time,1,10) tDate,
				cas.cCertifiedId cCertifiedId,
				cas.cCaseStatus cCaseStatus,
				cas.cFinishDate cFinishDate,
				tra.tBankLoansDate cEndDate,
				cas.cLastEditor
			FROM 
				tBankTrans AS tra
			JOIN 
				tContractCase AS cas ON tra.tMemo=cas.cCertifiedId
			WHERE 
				tra.tExport="1" 
				AND tra.tPayOk="1"
				AND tKind="保證費"
				AND tra.tMemo = "'.$id.'"
			GROUP BY
				tra.tMemo
			ORDER BY
				tra.tBankLoansDate,cas.cCertifiedId
			ASC ; 
		' ;
	$rs = $conn->Execute($sql);	
	$date = '';
	if (!$rs->EOF) {
		$date = (substr($rs->fields['cEndDate'], 0,4)-1911).substr($rs->fields['cEndDate'], 5,2).substr($rs->fields['cEndDate'], 8,2);
	}else{
		$sql = '
			SELECT
				cas.cEscrowBankAccount as vr_code,
				cas.cBankList as tDate,
				cas.cCertifiedId as cCertifiedId,
				cas.cCaseStatus cCaseStatus,
				cas.cFinishDate cFinishDate,
				cas.cBankList cEndDate,
				cas.cLastEditor
			FROM
				tContractCase AS cas
			WHERE
				cas.cCertifiedId = "'.$id.'"
			ORDER BY
				cas.cBankList,cas.cCertifiedId
			ASC ;
		' ;
		// echo $sql;
		$rs = $conn->Execute($sql);	
		$date = (substr($rs->fields['cEndDate'], 0,4)-1911).substr($rs->fields['cEndDate'], 5,2).substr($rs->fields['cEndDate'], 8,2);

	}

	
	return $date;
}

function getBranch($bId){
	global $conn;

	$sql = "SELECT bName,bSerialnum,CONCAT((Select bCode From `tBrand`  Where bId = bBrand ),LPAD(bId,5,'0')) as code,bPassword FROM tBranch WHERE bId = '".$bId."'";

	$rs = $conn->Execute($sql);

	return $rs->fields;
}
##
$smarty->assign('msg',$msg);
$smarty->display('invoice.inc.tpl', '', 'accounting');
?>
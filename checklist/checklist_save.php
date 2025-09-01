<?php
require_once 'fpdf/chinese-unicode.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../configs/config.class.php';
include_once 'writelog.php';
include_once '../tracelog.php' ;

$tlog = new TraceLog() ;

$cell_y1 = 4.5 ;															// 內容用
$cell_y4 = 4 ;																// 內容用
$cell_y2 = 5 ;																// 標題用
$cell_y3 = 1 ;																// 手動跳行調行距用
$line_gap = 0.4 ;

Function cell_height($str,$len=42) {										// 計算欄位高度
	$str_len = strlen($str) ;
	if ($str_len == 0) { $str_len = 1 ; }
	$cell_height = intval($str_len / $len) ;
	if ($str_len % $len > 0) { $cell_height ++ ; }
	$cell_height *= 4.5 ; 
	return $cell_height ;
}

// 取得所有變數
# 點交表資訊部分
$preview = $_REQUEST['preview'] ;
$cCertifiedId = $_REQUEST['cCertifiedId'] ;
$cScrivener = $_REQUEST['cScrivener'] ;
$bScrivener = $_REQUEST['bScrivener'] ;
$cBuyer = $_REQUEST['cBuyer'] ;
$bBuyer = $_REQUEST['bBuyer'] ;
$cBuyerId = $_REQUEST['cBuyerId'] ;
$bBuyerId = $_REQUEST['bBuyerId'] ;
$cOwner = $_REQUEST['cOwner'] ;
$bOwner = $_REQUEST['bOwner'] ;
$cOwnerId = $_REQUEST['cOwnerId'] ;
$bOwnerId = $_REQUEST['bOwnerId'] ;
$cBrand = $_REQUEST['cBrand'] ;
$bBrand = $_REQUEST['bBrand'] ;
$cStore = $_REQUEST['cStore'] ;
$bStore = $_REQUEST['bStore'] ;
$cMoreStore = $_REQUEST['cMoreStore'];
$bMoreStore = $_REQUEST['bMoreStore'];
$cTotalMoney = $_REQUEST['cTotalMoney'] ;
$bTotalMoney = $_REQUEST['bTotalMoney'] ;
$cTotalMoneyNote = $_REQUEST['cTotalMoneyNote'] ;
$bTotalMoneyNote = $_REQUEST['bTotalMoneyNote'] ;

$cCompensation2 = $_REQUEST['cCompensation2'] ;
$bCompensation2 = $_REQUEST['bCompensation2'] ;
$cCompensation3 = $_REQUEST['cCompensation3'] ;
$bCompensation3 = $_REQUEST['bCompensation3'] ;
$cCompensation4 = $_REQUEST['cCompensation4'] ;
$bCompensation4 = $_REQUEST['bCompensation4'] ;
$bNotIntoMoney = $_REQUEST['bNotIntoMoney'] ;
$cNotIntoMoney = $_REQUEST['cNotIntoMoney'] ;

$cProperty = $_REQUEST['cProperty'] ;
$bProperty = $_REQUEST['bProperty'] ;
$cInterest = $_REQUEST['cInterest'] ;
$bInterest = $_REQUEST['bInterest'] ;
$cRealestateBalance = $_REQUEST['cRealestateBalance'] ;
$bRealestateBalance = $_REQUEST['bRealestateBalance'] ;
$cCertifiedMoney = $_REQUEST['cCertifiedMoney'] ;
$bCertifiedMoney = $_REQUEST['bCertifiedMoney'] ;
$cCertifiedMoney2 = $_REQUEST['cCertifiedMoney2'] ;
$cScrivenerMoney = $_REQUEST['cScrivenerMoney'] ;
$bScrivenerMoney = $_REQUEST['bScrivenerMoney'] ;
$cTax = $_REQUEST['cTax'] ;
$bTax = $_REQUEST['bTax'] ;
$cNHITax = $_REQUEST['cNHITax'] ;
$bNHITax = $_REQUEST['bNHITax'] ;
$balance_remark = $_REQUEST['balance_remark'] ;
$realty_remark = $_REQUEST['realty_remark'] ;
$certify_remark = $_REQUEST['certify_remark'] ;
$bcertify_remark = $_REQUEST['bcertify_remark'] ;
$certify_remark2 = $_REQUEST['certify_remark2'] ;


$scrivener_remark = $_REQUEST['scrivener_remark'] ;
$scrivener_remark2 = $_REQUEST['scrivener_remark2'] ;
$cTaxTitle = $_REQUEST['cTaxTitle'] ;
$bTaxTitle = $_REQUEST['bTaxTitle'] ;
$cTaxRemark = $_REQUEST['cTaxRemark'] ;
$bTaxRemark = $_REQUEST['bTaxRemark'] ;
$other_remark = $_REQUEST['other_remark'] ;
$other_remark_buyer = $_REQUEST['other_remark_buyer'] ;

$last_modify = date("Ymd.His").str_pad($_SESSION['member_id'],4,'0',STR_PAD_LEFT) ;
$save_this = $_REQUEST['save_this'] ;
$bNote = $_REQUEST['bNote'] ;
$cNote = $_REQUEST['cNote'] ;
# ~~

# 點交表收支部分(買方)
$bId = $_REQUEST['bId'] ;

//確認買方人數
$sql = "SELECT * FROM tContractOthers WHERE cCertifiedId = '" . $cCertifiedId . "' AND cIdentity='1'";
$res  = $conn->Execute($sql);
$buyer = $res->recordCount();
##
if($buyer > 0) {
	$bCertifyQue = $_REQUEST['bCertifyQue'] ;
	$bCertifyAns = $_REQUEST['bCertifyAns'] ;
	$bCertifyOption1 = $_REQUEST['bCertifyOption1'] ;
	$bCertifyOption2 = $_REQUEST['bCertifyOption2'] ;
	$bCertifyDesc = $_REQUEST['bCertifyDesc'] ;
}
$bDate = $_REQUEST['bDate'] ;
$bKind = $_REQUEST['bKind'] ;
$bIncome = $_REQUEST['bIncome'] ;
$bExpense = $_REQUEST['bExpense'] ;
$bRemark = $_REQUEST['bRemark'] ;
## 新增收支部分 ##
$bDate_new = $_REQUEST['bDate_new'] ;
$bKind_new = $_REQUEST['bKind_new'] ;
$bIncome_new = $_REQUEST['bIncome_new'] ;
$bExpense_new = $_REQUEST['bExpense_new'] ;
$bRemark_new = $_REQUEST['bRemark_new'] ;
##
# ~~

# 點交表收支部分(賣方)
$oId = $_REQUEST['oId'] ;

//確認賣方人數
$sql = "SELECT * FROM tContractOthers WHERE cCertifiedId = '" . $cCertifiedId . "' AND cIdentity='2'";
$res = $conn->Execute($sql);
$owner = $res->recordCount();
##
if($owner > 0) {
	$cCertifyQue = $_REQUEST['cCertifyQue'] ;
	$cCertifyAns = $_REQUEST['cCertifyAns'] ;
	$cCertifyOption1 = $_REQUEST['cCertifyOption1'] ;
	$cCertifyOption2 = $_REQUEST['cCertifyOption2'] ;
	$cCertifyDesc = $_REQUEST['cCertifyDesc'] ;

	$cAvgQue = $_REQUEST['cAvgQue'] ;
	$cAvgAns = $_REQUEST['cAvgAns'] ;
	$cAvgOption1 = $_REQUEST['cAvgOption1'] ;
	$cAvgOption2 = $_REQUEST['cAvgOption2'] ;
	$cAvgDesc = $_REQUEST['cAvgDesc'] ;
}
$oDate = $_REQUEST['oDate'] ;
$oKind = $_REQUEST['oKind'] ;
$oIncome = $_REQUEST['oIncome'] ;
$oExpense = $_REQUEST['oExpense'] ;
$oRemark = $_REQUEST['oRemark'] ;
## 新增收支部分 ##
$oDate_new = $_REQUEST['oDate_new'] ;
$oKind_new = $_REQUEST['oKind_new'] ;
$oIncome_new = $_REQUEST['oIncome_new'] ;
$oExpense_new = $_REQUEST['oExpense_new'] ;
$oRemark_new = $_REQUEST['oRemark_new'] ;
##
# ~~
$sql = "SELECT cInterest,bInterest FROM tChecklist WHERE cCertifiedId='".$cCertifiedId."'";
$rs = $conn->Execute($sql);

$int_check = 0;
if ($cInterest != $rs->fields['cInterest'] ||$bInterest != $rs->fields['bInterest']) {
	$int_check = 1;
}

# 更新點交表資訊部分
$sql = '
	UPDATE 
		tChecklist 
	SET 
		cScrivener="'.$cScrivener.'",
		bScrivener="'.$bScrivener.'",
		cBuyer="'.$cBuyer.'",
		bBuyer="'.$bBuyer.'",
		cBuyerId="'.$cBuyerId.'",
		bBuyerId="'.$bBuyerId.'",
		cOwner="'.$cOwner.'",
		bOwner="'.$bOwner.'",
		cOwnerId="'.$cOwnerId.'",
		bOwnerId="'.$bOwnerId.'",
		cBrand="'.$cBrand.'",
		bBrand="'.$bBrand.'",
		cStore="'.$cStore.'",
		bStore="'.$bStore.'",
		cMoreStore = "'.$cMoreStore.'",
		bMoreStore = "'.$bMoreStore.'",
		cTotalMoney="'.$cTotalMoney.'",
		bTotalMoney="'.$bTotalMoney.'",
		cTotalMoneyNote = "'.$cTotalMoneyNote.'",
		bTotalMoneyNote = "'.$bTotalMoneyNote.'",
		cNotIntoMoney = "'.$cNotIntoMoney.'",
		bNotIntoMoney = "'.$bNotIntoMoney.'", 
		cCompensation2 = "'.$cCompensation2.'", 
		bCompensation2 = "'.$bCompensation2.'", 
		cCompensation3 = "'.$cCompensation3.'", 
		bCompensation3 = "'.$bCompensation3.'", 
		cCompensation4 = "'.$cCompensation4.'", 
		bCompensation4 = "'.$bCompensation4.'", 
		cProperty="'.$cProperty.'",
		bProperty="'.$bProperty.'",
		cInterest="'.$cInterest.'",
		bInterest="'.$bInterest.'",
		cRealestateBalance="'.$cRealestateBalance.'",
		bRealestateBalance="'.$bRealestateBalance.'",
		cCertifiedMoney="'.$cCertifiedMoney.'",
		bCertifiedMoney="'.$bCertifiedMoney.'",
		cCertifiedMoney2="'.$cCertifiedMoney2.'",
		cScrivenerMoney="'.$cScrivenerMoney.'",
		bScrivenerMoney="'.$bScrivenerMoney.'",
		cTax="'.$cTax.'",
		bTax="'.$bTax.'",
		cNHITax="'.$cNHITax.'",
		bNHITax="'.$bNHITax.'",
		balance_remark="'.$balance_remark.'",
		realty_remark="'.$realty_remark.'",
		certify_remark="'.$certify_remark.'",
		bcertify_remark = "'.$bcertify_remark.'",
		certify_remark2 = "'.$certify_remark2.'",
		scrivener_remark="'.$scrivener_remark.'",
		scrivener_remark2="'.$scrivener_remark2.'",
		cTaxTitle="'.$cTaxTitle.'", 
		bTaxTitle="'.$bTaxTitle.'", 
		cTaxRemark="'.$cTaxRemark.'", 
		bTaxRemark="'.$bTaxRemark.'", 
		other_remark="'.$other_remark.'",
		other_remark_buyer="'.$other_remark_buyer.'",
		bNote="'.$bNote.'",
		cNote="'.$cNote.'",
		last_modify="'.$last_modify.'"
' ;
if($owner > 0) {
    $sql .=',cCertifyQue="'.$cCertifyQue.'",
    	cCertifyAns="'.$cCertifyAns.'",
    	cCertifyOption1="'.$cCertifyOption1.'",
    	cCertifyOption2="'.$cCertifyOption2.'",
    	cCertifyDesc="'.$cCertifyDesc.'",
    	cAvgQue="'.$cAvgQue.'",
		cAvgAns="'.$cAvgAns.'",
		cAvgOption1="'.$cAvgOption1.'",
		cAvgOption2="'.$cAvgOption2.'",
		cAvgDesc="'.$cAvgDesc.'"
	';
}
if($buyer > 0) {
	$sql .= ',bCertifyQue="'.$bCertifyQue.'",
    	bCertifyAns="'.$bCertifyAns.'",
    	bCertifyOption1="'.$bCertifyOption1.'",
    	bCertifyOption2="'.$bCertifyOption2.'",
    	bCertifyDesc="'.$bCertifyDesc.'"
	';
}
$sql .= ' WHERE 
		cCertifiedId="'.$cCertifiedId.'";
' ;
$tlog->updateWrite($_SESSION['member_id'], $sql, '更新點交表') ;
$conn->Execute($sql);
# ~~

# 更新點交表資訊部分(買方)
for ($i = 0 ; $i < count($bId) ; $i ++) {
	$sql = '
		UPDATE 
			tChecklistBlist 
		SET 
			bCertifiedId="'.$cCertifiedId.'", 
			bDate="'.$bDate[$i].'", 
			bKind="'.$bKind[$i].'", 
			bIncome="'.$bIncome[$i].'", 
			bExpense="'.$bExpense[$i].'", 
			bRemark="'.$bRemark[$i].'" 
		WHERE
			bId="'.$bId[$i].'";
	' ;
	$conn->Execute($sql);
}

if ($bDate_new) {
	$sql = '
		INSERT INTO 
			tChecklistBlist 
			(bCertifiedId, bDate, bKind, bIncome, bExpense, bRemark)
		VALUES 
			("'.$cCertifiedId.'","'.$bDate_new.'","'.$bKind_new.'","'.$bIncome_new.'","'.$bExpense.'","'.$bRemark_new.'");
	' ;
}
# ~~

# 更新點交表資訊部分(賣方)
for ($i = 0 ; $i < count($oId) ; $i ++) {
	$sql = '
		UPDATE 
			tChecklistOlist 
		SET 
			oCertifiedId="'.$cCertifiedId.'", 
			oDate="'.$oDate[$i].'", 
			oKind="'.$oKind[$i].'", 
			oIncome="'.$oIncome[$i].'", 
			oExpense="'.$oExpense[$i].'", 
			oRemark="'.$oRemark[$i].'" 
		WHERE
			oId="'.$oId[$i].'";
	' ;
	$conn->Execute($sql);
}

##結清撥付款項明細
for ($i=0; $i < count($_POST['TaxTitle']) ; $i++) { 
	
	$sql="
		UPDATE 
			tChecklistOther
		SET 
			cTaxTitle = '".$_POST['TaxTitle'][$i]."',
			cTax	= '".$_POST['Tax'][$i]."',
			cTaxRemark = '".$_POST['TaxRemark'][$i]."'
		WHERE
			cId = '".$_POST['tax_id'][$i]."'
		";
		$conn->Execute($sql);
}
##
##結清撥付款項明細-其他
for ($i=0; $i < count($_POST['data_other_remark']) ; $i++) { 
	$sql="
		UPDATE 
			tChecklistRemark
		SET 
			cRemark = '".$_POST['data_other_remark'][$i]."'
			
		WHERE
			cId = '".$_POST['data_other_remark_id'][$i]."'
		";
		$conn->Execute($sql);
}
##

if ($oDate_new) {
	$sql = '
		INSERT INTO 
			tChecklistOlist 
			(oCertifiedId, oDate, oKind, oIncome, oExpense, oRemark)
		VALUES 
			("'.$cCertifiedId.'","'.$oDate_new.'","'.$oKind_new.'","'.$oIncome_new.'","'.$oExpense_new.'","'.$oRemark_new.'");
	' ;
	$conn->Execute($sql);
}
# ~~
if ($save_this != 'ok') {		//若要同步至前台時
	//(2). 更新 upload  點交表下載資料庫
	$sql = 'SELECT tId FROM tUploadFile WHERE tCertifiedId="'.$cCertifiedId.'" ;' ;
	$rs = $conn->Execute($sql);
	
	if ($rs->EOF) {
		$sql = 'INSERT INTO tUploadFile (tCertifiedId) VALUES ("'.$cCertifiedId.'") ;' ;
	}
	else {
		$sql = 'UPDATE tUploadFile SET tIdentify="", tFileName="" WHERE tCertifiedId="'.$cCertifiedId.'" ;' ;
	}

	$conn->Execute($sql);
}
//

echo '
<script type="text/javascript">
	alert("點交表已儲存!!") ;
' ;

if ($int_check == 1) {
	echo "alert('利息已更動，請記得修改合約書「利息分配」部分')";
}

if ($preview) {
	echo 'window.open("list_pdf.php?cCertifiedId='.$cCertifiedId.'","","width=800px,height=1200px,status=yes,scrollbars=yes,location=no,menubar=no,location=no") ;' ;
}

echo '
	location = "checklist.php?cCertifiedId='.$cCertifiedId.'" ;
</script>
' ;

//埋log紀錄
checklist_log('點交表儲存(保證號碼:'.$cCertifiedId.')');
##
?>



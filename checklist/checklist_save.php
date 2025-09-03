<?php
require_once 'fpdf/chinese-unicode.php';
include_once '../openadodb.php';
include_once '../session_check.php';
include_once '../configs/config.class.php';
include_once 'writelog.php';
include_once '../tracelog.php';

$tlog = new TraceLog();

$cell_y1  = 4.5; // 內容用
$cell_y4  = 4;   // 內容用
$cell_y2  = 5;   // 標題用
$cell_y3  = 1;   // 手動跳行調行距用
$line_gap = 0.4;

function cell_height($str, $len = 42)
{ // 計算欄位高度
    $str_len = strlen($str);
    if ($str_len == 0) {$str_len = 1;}
    $cell_height = intval($str_len / $len);
    if ($str_len % $len > 0) {$cell_height++;}
    $cell_height *= 4.5;
    return $cell_height;
}

// 取得所有變數
# 點交表資訊部分
$preview         = isset($_REQUEST['preview']) ? $_REQUEST['preview'] : '';
$cCertifiedId    = isset($_REQUEST['cCertifiedId']) ? $_REQUEST['cCertifiedId'] : '';
$cScrivener      = isset($_REQUEST['cScrivener']) ? $_REQUEST['cScrivener'] : '';
$bScrivener      = isset($_REQUEST['bScrivener']) ? $_REQUEST['bScrivener'] : '';
$cBuyer          = isset($_REQUEST['cBuyer']) ? $_REQUEST['cBuyer'] : '';
$bBuyer          = isset($_REQUEST['bBuyer']) ? $_REQUEST['bBuyer'] : '';
$cBuyerId        = isset($_REQUEST['cBuyerId']) ? $_REQUEST['cBuyerId'] : '';
$bBuyerId        = isset($_REQUEST['bBuyerId']) ? $_REQUEST['bBuyerId'] : '';
$cOwner          = isset($_REQUEST['cOwner']) ? $_REQUEST['cOwner'] : '';
$bOwner          = isset($_REQUEST['bOwner']) ? $_REQUEST['bOwner'] : '';
$cOwnerId        = isset($_REQUEST['cOwnerId']) ? $_REQUEST['cOwnerId'] : '';
$bOwnerId        = isset($_REQUEST['bOwnerId']) ? $_REQUEST['bOwnerId'] : '';
$cBrand          = isset($_REQUEST['cBrand']) ? $_REQUEST['cBrand'] : '';
$bBrand          = isset($_REQUEST['bBrand']) ? $_REQUEST['bBrand'] : '';
$cStore          = isset($_REQUEST['cStore']) ? $_REQUEST['cStore'] : '';
$bStore          = isset($_REQUEST['bStore']) ? $_REQUEST['bStore'] : '';
$cMoreStore      = isset($_REQUEST['cMoreStore']) ? $_REQUEST['cMoreStore'] : '';
$bMoreStore      = isset($_REQUEST['bMoreStore']) ? $_REQUEST['bMoreStore'] : '';
$cTotalMoney     = isset($_REQUEST['cTotalMoney']) ? $_REQUEST['cTotalMoney'] : '';
$bTotalMoney     = isset($_REQUEST['bTotalMoney']) ? $_REQUEST['bTotalMoney'] : '';
$cTotalMoneyNote = isset($_REQUEST['cTotalMoneyNote']) ? $_REQUEST['cTotalMoneyNote'] : '';
$bTotalMoneyNote = isset($_REQUEST['bTotalMoneyNote']) ? $_REQUEST['bTotalMoneyNote'] : '';

$cCompensation2 = isset($_REQUEST['cCompensation2']) ? $_REQUEST['cCompensation2'] : '';
$bCompensation2 = isset($_REQUEST['bCompensation2']) ? $_REQUEST['bCompensation2'] : '';
$cCompensation3 = isset($_REQUEST['cCompensation3']) ? $_REQUEST['cCompensation3'] : '';
$bCompensation3 = isset($_REQUEST['bCompensation3']) ? $_REQUEST['bCompensation3'] : '';
$cCompensation4 = isset($_REQUEST['cCompensation4']) ? $_REQUEST['cCompensation4'] : '';
$bCompensation4 = isset($_REQUEST['bCompensation4']) ? $_REQUEST['bCompensation4'] : '';
$bNotIntoMoney  = isset($_REQUEST['bNotIntoMoney']) ? $_REQUEST['bNotIntoMoney'] : '';
$cNotIntoMoney  = isset($_REQUEST['cNotIntoMoney']) ? $_REQUEST['cNotIntoMoney'] : '';

$cProperty          = isset($_REQUEST['cProperty']) ? $_REQUEST['cProperty'] : '';
$bProperty          = isset($_REQUEST['bProperty']) ? $_REQUEST['bProperty'] : '';
$cInterest          = isset($_REQUEST['cInterest']) ? $_REQUEST['cInterest'] : '';
$bInterest          = isset($_REQUEST['bInterest']) ? $_REQUEST['bInterest'] : '';
$cRealestateBalance = isset($_REQUEST['cRealestateBalance']) ? $_REQUEST['cRealestateBalance'] : '';
$bRealestateBalance = isset($_REQUEST['bRealestateBalance']) ? $_REQUEST['bRealestateBalance'] : '';
$cCertifiedMoney    = isset($_REQUEST['cCertifiedMoney']) ? $_REQUEST['cCertifiedMoney'] : '';
$bCertifiedMoney    = isset($_REQUEST['bCertifiedMoney']) ? $_REQUEST['bCertifiedMoney'] : '';
$cCertifiedMoney2   = isset($_REQUEST['cCertifiedMoney2']) ? $_REQUEST['cCertifiedMoney2'] : '';
$cScrivenerMoney    = isset($_REQUEST['cScrivenerMoney']) ? $_REQUEST['cScrivenerMoney'] : '';
$bScrivenerMoney    = isset($_REQUEST['bScrivenerMoney']) ? $_REQUEST['bScrivenerMoney'] : '';
$cTax               = isset($_REQUEST['cTax']) ? $_REQUEST['cTax'] : '';
$bTax               = isset($_REQUEST['bTax']) ? $_REQUEST['bTax'] : '';
$cNHITax            = isset($_REQUEST['cNHITax']) ? $_REQUEST['cNHITax'] : '';
$bNHITax            = isset($_REQUEST['bNHITax']) ? $_REQUEST['bNHITax'] : '';
$balance_remark     = isset($_REQUEST['balance_remark']) ? $_REQUEST['balance_remark'] : '';
$realty_remark      = isset($_REQUEST['realty_remark']) ? $_REQUEST['realty_remark'] : '';
$certify_remark     = isset($_REQUEST['certify_remark']) ? $_REQUEST['certify_remark'] : '';
$bcertify_remark    = isset($_REQUEST['bcertify_remark']) ? $_REQUEST['bcertify_remark'] : '';
$certify_remark2    = isset($_REQUEST['certify_remark2']) ? $_REQUEST['certify_remark2'] : '';

$scrivener_remark   = isset($_REQUEST['scrivener_remark']) ? $_REQUEST['scrivener_remark'] : '';
$scrivener_remark2  = isset($_REQUEST['scrivener_remark2']) ? $_REQUEST['scrivener_remark2'] : '';
$cTaxTitle          = isset($_REQUEST['cTaxTitle']) ? $_REQUEST['cTaxTitle'] : '';
$bTaxTitle          = isset($_REQUEST['bTaxTitle']) ? $_REQUEST['bTaxTitle'] : '';
$cTaxRemark         = isset($_REQUEST['cTaxRemark']) ? $_REQUEST['cTaxRemark'] : '';
$bTaxRemark         = isset($_REQUEST['bTaxRemark']) ? $_REQUEST['bTaxRemark'] : '';
$other_remark       = isset($_REQUEST['other_remark']) ? $_REQUEST['other_remark'] : '';
$other_remark_buyer = isset($_REQUEST['other_remark_buyer']) ? $_REQUEST['other_remark_buyer'] : '';

$last_modify = date("Ymd.His") . str_pad($_SESSION['member_id'], 4, '0', STR_PAD_LEFT);
$save_this   = isset($_REQUEST['save_this']) ? $_REQUEST['save_this'] : '';
$bNote       = isset($_REQUEST['bNote']) ? $_REQUEST['bNote'] : '';
$cNote       = isset($_REQUEST['cNote']) ? $_REQUEST['cNote'] : '';
# ~~

# 點交表收支部分(買方)
$bId = isset($_REQUEST['bId']) ? $_REQUEST['bId'] : '';

//確認買方人數
$sql   = "SELECT * FROM tContractOthers WHERE cCertifiedId = '" . $cCertifiedId . "' AND cIdentity='1'";
$res   = $conn->Execute($sql);
$buyer = $res->recordCount();
##
if ($buyer > 0) {
    $bCertifyQue     = isset($_REQUEST['bCertifyQue']) ? $_REQUEST['bCertifyQue'] : '';
    $bCertifyAns     = isset($_REQUEST['bCertifyAns']) ? $_REQUEST['bCertifyAns'] : '';
    $bCertifyOption1 = isset($_REQUEST['bCertifyOption1']) ? $_REQUEST['bCertifyOption1'] : '';
    $bCertifyOption2 = isset($_REQUEST['bCertifyOption2']) ? $_REQUEST['bCertifyOption2'] : '';
    $bCertifyDesc    = isset($_REQUEST['bCertifyDesc']) ? $_REQUEST['bCertifyDesc'] : '';
}
$bDate    = isset($_REQUEST['bDate']) ? $_REQUEST['bDate'] : [];
$bKind    = isset($_REQUEST['bKind']) ? $_REQUEST['bKind'] : [];
$bIncome  = isset($_REQUEST['bIncome']) ? $_REQUEST['bIncome'] : [];
$bExpense = isset($_REQUEST['bExpense']) ? $_REQUEST['bExpense'] : [];
$bRemark  = isset($_REQUEST['bRemark']) ? $_REQUEST['bRemark'] : [];
## 新增收支部分 ##
$bDate_new    = isset($_REQUEST['bDate_new']) ? $_REQUEST['bDate_new'] : [];
$bKind_new    = isset($_REQUEST['bKind_new']) ? $_REQUEST['bKind_new'] : [];
$bIncome_new  = isset($_REQUEST['bIncome_new']) ? $_REQUEST['bIncome_new'] : [];
$bExpense_new = isset($_REQUEST['bExpense_new']) ? $_REQUEST['bExpense_new'] : [];
$bRemark_new  = isset($_REQUEST['bRemark_new']) ? $_REQUEST['bRemark_new'] : [];
##
# ~~

# 點交表收支部分(賣方)
$oId = isset($_REQUEST['oId']) ? $_REQUEST['oId'] : '';

//確認賣方人數
$sql   = "SELECT * FROM tContractOthers WHERE cCertifiedId = '" . $cCertifiedId . "' AND cIdentity='2'";
$res   = $conn->Execute($sql);
$owner = $res->recordCount();
##
if ($owner > 0) {
    $cCertifyQue     = isset($_REQUEST['cCertifyQue']) ? $_REQUEST['cCertifyQue'] : '';
    $cCertifyAns     = isset($_REQUEST['cCertifyAns']) ? $_REQUEST['cCertifyAns'] : '';
    $cCertifyOption1 = isset($_REQUEST['cCertifyOption1']) ? $_REQUEST['cCertifyOption1'] : '';
    $cCertifyOption2 = isset($_REQUEST['cCertifyOption2']) ? $_REQUEST['cCertifyOption2'] : '';
    $cCertifyDesc    = isset($_REQUEST['cCertifyDesc']) ? $_REQUEST['cCertifyDesc'] : '';

    $cAvgQue     = isset($_REQUEST['cAvgQue']) ? $_REQUEST['cAvgQue'] : '';
    $cAvgAns     = isset($_REQUEST['cAvgAns']) ? $_REQUEST['cAvgAns'] : '';
    $cAvgOption1 = isset($_REQUEST['cAvgOption1']) ? $_REQUEST['cAvgOption1'] : '';
    $cAvgOption2 = isset($_REQUEST['cAvgOption2']) ? $_REQUEST['cAvgOption2'] : '';
    $cAvgDesc    = isset($_REQUEST['cAvgDesc']) ? $_REQUEST['cAvgDesc'] : '';
}
$oDate    = isset($_REQUEST['oDate']) ? $_REQUEST['oDate'] : [];
$oKind    = isset($_REQUEST['oKind']) ? $_REQUEST['oKind'] : [];
$oIncome  = isset($_REQUEST['oIncome']) ? $_REQUEST['oIncome'] : [];
$oExpense = isset($_REQUEST['oExpense']) ? $_REQUEST['oExpense'] : [];
$oRemark  = isset($_REQUEST['oRemark']) ? $_REQUEST['oRemark'] : [];
## 新增收支部分 ##
$oDate_new    = isset($_REQUEST['oDate_new']) ? $_REQUEST['oDate_new'] : [];
$oKind_new    = isset($_REQUEST['oKind_new']) ? $_REQUEST['oKind_new'] : [];
$oIncome_new  = isset($_REQUEST['oIncome_new']) ? $_REQUEST['oIncome_new'] : [];
$oExpense_new = isset($_REQUEST['oExpense_new']) ? $_REQUEST['oExpense_new'] : [];
$oRemark_new  = isset($_REQUEST['oRemark_new']) ? $_REQUEST['oRemark_new'] : [];
##
# ~~
$sql = "SELECT cInterest,bInterest FROM tChecklist WHERE cCertifiedId='" . $cCertifiedId . "'";
$rs  = $conn->Execute($sql);

$int_check = 0;
if ($cInterest != $rs->fields['cInterest'] || $bInterest != $rs->fields['bInterest']) {
    $int_check = 1;
}

# 更新點交表資訊部分
$sql = '
	UPDATE
		tChecklist
	SET
		cScrivener="' . $cScrivener . '",
		bScrivener="' . $bScrivener . '",
		cBuyer="' . $cBuyer . '",
		bBuyer="' . $bBuyer . '",
		cBuyerId="' . $cBuyerId . '",
		bBuyerId="' . $bBuyerId . '",
		cOwner="' . $cOwner . '",
		bOwner="' . $bOwner . '",
		cOwnerId="' . $cOwnerId . '",
		bOwnerId="' . $bOwnerId . '",
		cBrand="' . $cBrand . '",
		bBrand="' . $bBrand . '",
		cStore="' . $cStore . '",
		bStore="' . $bStore . '",
		cMoreStore = "' . $cMoreStore . '",
		bMoreStore = "' . $bMoreStore . '",
		cTotalMoney="' . $cTotalMoney . '",
		bTotalMoney="' . $bTotalMoney . '",
		cTotalMoneyNote = "' . $cTotalMoneyNote . '",
		bTotalMoneyNote = "' . $bTotalMoneyNote . '",
		cNotIntoMoney = "' . $cNotIntoMoney . '",
		bNotIntoMoney = "' . $bNotIntoMoney . '",
		cCompensation2 = "' . $cCompensation2 . '",
		bCompensation2 = "' . $bCompensation2 . '",
		cCompensation3 = "' . $cCompensation3 . '",
		bCompensation3 = "' . $bCompensation3 . '",
		cCompensation4 = "' . $cCompensation4 . '",
		bCompensation4 = "' . $bCompensation4 . '",
		cProperty="' . $cProperty . '",
		bProperty="' . $bProperty . '",
		cInterest="' . $cInterest . '",
		bInterest="' . $bInterest . '",
		cRealestateBalance="' . $cRealestateBalance . '",
		bRealestateBalance="' . $bRealestateBalance . '",
		cCertifiedMoney="' . $cCertifiedMoney . '",
		bCertifiedMoney="' . $bCertifiedMoney . '",
		cCertifiedMoney2="' . $cCertifiedMoney2 . '",
		cScrivenerMoney="' . $cScrivenerMoney . '",
		bScrivenerMoney="' . $bScrivenerMoney . '",
		cTax="' . $cTax . '",
		bTax="' . $bTax . '",
		cNHITax="' . $cNHITax . '",
		bNHITax="' . $bNHITax . '",
		balance_remark="' . $balance_remark . '",
		realty_remark="' . $realty_remark . '",
		certify_remark="' . $certify_remark . '",
		bcertify_remark = "' . $bcertify_remark . '",
		certify_remark2 = "' . $certify_remark2 . '",
		scrivener_remark="' . $scrivener_remark . '",
		scrivener_remark2="' . $scrivener_remark2 . '",
		cTaxTitle="' . $cTaxTitle . '",
		bTaxTitle="' . $bTaxTitle . '",
		cTaxRemark="' . $cTaxRemark . '",
		bTaxRemark="' . $bTaxRemark . '",
		other_remark="' . $other_remark . '",
		other_remark_buyer="' . $other_remark_buyer . '",
		bNote="' . $bNote . '",
		cNote="' . $cNote . '",
		last_modify="' . $last_modify . '"
';
if ($owner > 0) {
    $sql .= ',cCertifyQue="' . $cCertifyQue . '",
    	cCertifyAns="' . $cCertifyAns . '",
    	cCertifyOption1="' . $cCertifyOption1 . '",
    	cCertifyOption2="' . $cCertifyOption2 . '",
    	cCertifyDesc="' . $cCertifyDesc . '",
    	cAvgQue="' . $cAvgQue . '",
		cAvgAns="' . $cAvgAns . '",
		cAvgOption1="' . $cAvgOption1 . '",
		cAvgOption2="' . $cAvgOption2 . '",
		cAvgDesc="' . $cAvgDesc . '"
	';
}
if ($buyer > 0) {
    $sql .= ',bCertifyQue="' . $bCertifyQue . '",
    	bCertifyAns="' . $bCertifyAns . '",
    	bCertifyOption1="' . $bCertifyOption1 . '",
    	bCertifyOption2="' . $bCertifyOption2 . '",
    	bCertifyDesc="' . $bCertifyDesc . '"
	';
}
$sql .= ' WHERE
		cCertifiedId="' . $cCertifiedId . '";
';
$tlog->updateWrite($_SESSION['member_id'], $sql, '更新點交表');
$conn->Execute($sql);
# ~~

# 更新點交表資訊部分(買方)
if (is_array($bId) && count($bId) > 0) {
    for ($i = 0; $i < count($bId); $i++) {
        $sql = '
            UPDATE
                tChecklistBlist
            SET
                bCertifiedId="' . $cCertifiedId . '",
                bDate="' . $bDate[$i] . '",
                bKind="' . $bKind[$i] . '",
                bIncome="' . $bIncome[$i] . '",
			bExpense="' . $bExpense[$i] . '",
			bRemark="' . $bRemark[$i] . '"
		WHERE
			bId="' . $bId[$i] . '";
	';
        $conn->Execute($sql);
    }
}

if ($bDate_new) {
    $sql = '
		INSERT INTO
			tChecklistBlist
			(bCertifiedId, bDate, bKind, bIncome, bExpense, bRemark)
		VALUES
			("' . $cCertifiedId . '","' . $bDate_new . '","' . $bKind_new . '","' . $bIncome_new . '","' . $bExpense . '","' . $bRemark_new . '");
	';
}
# ~~

# 更新點交表資訊部分(賣方)
if (is_array($oId) && count($oId) > 0) {
    for ($i = 0; $i < count($oId); $i++) {
        $sql = '
            UPDATE
                tChecklistOlist
            SET
                oCertifiedId="' . $cCertifiedId . '",
                oDate="' . $oDate[$i] . '",
                oKind="' . $oKind[$i] . '",
                oIncome="' . $oIncome[$i] . '",
                oExpense="' . $oExpense[$i] . '",
			oRemark="' . $oRemark[$i] . '"
		WHERE
			oId="' . $oId[$i] . '";
	';
        $conn->Execute($sql);
    }
}

##結清撥付款項明細
if (isset($_POST['TaxTitle']) && is_array($_POST['TaxTitle']) && count($_POST['TaxTitle']) > 0) {
    for ($i = 0; $i < count($_POST['TaxTitle']); $i++) {

        $sql = "
            UPDATE
                tChecklistOther
            SET
                cTaxTitle = '" . $_POST['TaxTitle'][$i] . "',
                cTax	= '" . $_POST['Tax'][$i] . "',
                cTaxRemark = '" . $_POST['TaxRemark'][$i] . "'
            WHERE
			cId = '" . $_POST['tax_id'][$i] . "'
		";
        $conn->Execute($sql);
    }
}
##
##結清撥付款項明細-其他
if (isset($_POST['data_other_remark']) && is_array($_POST['data_other_remark']) && count($_POST['data_other_remark']) > 0) {
    for ($i = 0; $i < count($_POST['data_other_remark']); $i++) {
        $sql = "
            UPDATE
                tChecklistRemark
            SET
                cRemark = '" . $_POST['data_other_remark'][$i] . "'

		WHERE
			cId = '" . $_POST['data_other_remark_id'][$i] . "'
		";
        $conn->Execute($sql);
    }
}
##

if ($oDate_new) {
    $sql = '
		INSERT INTO
			tChecklistOlist
			(oCertifiedId, oDate, oKind, oIncome, oExpense, oRemark)
		VALUES
			("' . $cCertifiedId . '","' . $oDate_new . '","' . $oKind_new . '","' . $oIncome_new . '","' . $oExpense_new . '","' . $oRemark_new . '");
	';
    $conn->Execute($sql);
}
                          # ~~
if ($save_this != 'ok') { //若要同步至前台時
                              //(2). 更新 upload  點交表下載資料庫
    $sql = 'SELECT tId FROM tUploadFile WHERE tCertifiedId="' . $cCertifiedId . '" ;';
    $rs  = $conn->Execute($sql);

    if ($rs->EOF) {
        $sql = 'INSERT INTO tUploadFile (tCertifiedId) VALUES ("' . $cCertifiedId . '") ;';
    } else {
        $sql = 'UPDATE tUploadFile SET tIdentify="", tFileName="" WHERE tCertifiedId="' . $cCertifiedId . '" ;';
    }

    $conn->Execute($sql);
}
//

echo '
<script type="text/javascript">
	alert("點交表已儲存!!") ;
';

if ($int_check == 1) {
    echo "alert('利息已更動，請記得修改合約書「利息分配」部分')";
}

if ($preview) {
    echo 'window.open("list_pdf.php?cCertifiedId=' . $cCertifiedId . '","","width=800px,height=1200px,status=yes,scrollbars=yes,location=no,menubar=no,location=no") ;';
}

echo '
	location = "checklist.php?cCertifiedId=' . $cCertifiedId . '" ;
</script>
';

//埋log紀錄
checklist_log('點交表儲存(保證號碼:' . $cCertifiedId . ')');
##

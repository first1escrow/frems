<?php
// ini_set("display_errors", "On"); 
// error_reporting(E_ALL & ~E_NOTICE);

include_once '../configs/config.class.php';
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
// include_once '../opendb.php' ;
include_once '../openadodb.php' ;
include_once '../web_addr.php' ;
include_once '../session_check.php' ;
include_once '../includes/maintain/feedBackData.php';

//判定身份別(法人、自然人'其他)
Function obj_id($iden) {
	$_ide = '' ;
	if (preg_match("/^[0-9]{8}$/",$iden)) {						// 若身分為法人(八碼、公司)
		$_ide = '法人' ;	
	}
	else if (preg_match("/^\w{10}$/",$iden)) {					// 若為自然人(十碼、個人)
		if (preg_match("/[a-zA-Z]{2}/",$iden) || preg_match("/[a-zA-z]{1}[8|9]{1}[0-6]{8}/",$iden) ) {				// 若證號有兩碼英文字，則為外國人
			$_ide = '非本國人' ;
		}
		else if (preg_match("/^[a-zA-Z]{1}[0-9]{9}$/",$iden)) {	// 符合1+9碼、則為本國人
			$_ide = '自然人' ;
		}
		else {
			$_ide = '其他' ;
		}
	}
	// elseif (preg_match("/^9[0-9]{6}$/",$iden)) {
	// 	$_ide = '其他' ;
	// }
	return $_ide ;
}
##

//計算 10 % 所得稅額
Function payTax($_id,$_int=0) {
	$_len = strlen($_id) ; 										// 個人10碼 公司8碼

	if ($_len == '10') {										// 個人10碼
		if (preg_match("/[A-Za-z]{2}/",$_id) || preg_match("/[a-zA-z]{1}[8|9]{1}[0-6]{8}/",$_id)) {					// 判別是否為外國人(兩碼英文字母者) 		
			$_o = 1 ;											// 外國籍自然人(一般民眾)
			$_tax = 0.2 ;										// 稅率：20%
		} 
		else {
			$_o = 2 ; 											// 本國籍自然人(一般民眾)
			$_tax = 0.1 ; 										// 稅率：10%
		}
	}
	else if ($_len == '8') {									// 公司8碼
		$_o = 2 ; // 本國籍自然人(一般民眾)						// 本國籍法人(公司)
		$_tax = 0.1 ;											// 稅率：10%
	}elseif ($_len == '7') {
		if (preg_match("/^9[0-9]{6}$/",$_id)) {					// 判別是否為外國人	
			$_o = 1 ;											// 外國籍自然人(一般民眾)
			$_tax = 0.2 ;										// 稅率：20%
		}
	}

	if ($_o == "1") {
		$cTax = round($_int * $_tax) ;
	}
	else if ($_o == "2") {
		$cTax = 0 ;
		if ($_int > 20000) {
			$cTax = round($_int * $_tax) ;
		}
	}
	
	return $cTax ;
}

//計算2%補充保費 2016/01/15改1.91%(0.0191)  //2021/01/01 調整為2.11%(0.0211)
Function payNHITax($_id,$_ide=0,$_int=0) {
	$NHI = 0 ;
	if (preg_match("/\w{10}/",$_id)) {								// 若為自然人身分(10碼)則需要代扣 NHI2 稅額		
		if (preg_match("/[A-Za-z]{2}/",$_id) || preg_match("/[a-zA-z]{1}[8|9]{1}[0-6]{8}/",$_id)) {						// 若為外國人
			if ($_ide == '1') {										// 若有加保健保者
				//if ($_int >= 5000) {								// 若餘額大於等於 5000
				if ($_int >= 20000) {								// 若餘額大於等於 5000
					$NHI = round($_int * 0.0211) ;					// 則代扣 2% 保費 20160115改1.91%(0.0191)  //2021/01/01 調整為2.11%(0.0211)
				}
			}
		}
		else {
			//if ($_int >= 5000) {									// 若利息大於等於 5,000 元
			if ($_int >= 20000) {									// 若利息大於等於 20,000 元(105-01-01起額度調為2萬元)
				$NHI = round($_int * 0.0211) ;						// 則代扣 2% 保費 2016/01/15改1.91%(0.0191) //2021/01/01 調整為2.11%(0.0211)
			}
		}
	}
	return $NHI ;
}
##

//仲介店型態
Function realtyCat($id,$_link) {
	global $conn;

	$_sql = 'SELECT bBrand,bCategory FROM tBranch WHERE bId="'.$id.'";' ;
	$rs = $conn->Execute($_sql);
	
	return array($rs->fields['bBrand'],$rs->fields['bCategory']) ;
}
##

//起始日期
if ($fds) {
	$tmp = explode('-',$fds) ;
	$tmp[0] += 1911 ;
	$fds = implode('-',$tmp) ;
	unset($tmp) ;
}
#

//結束日期
if ($fde) {
	$tmp = explode('-',$fde) ;
	$tmp[0] += 1911 ;
	$fde = implode('-',$tmp) ;
	unset($tmp) ;
}
#

//取得合約銀行活儲帳號
$bank_name = '' ;
$bank_acc = '' ;
if ($bank_option) {
	$sql = 'SELECT * FROM tContractBank WHERE cBankCode="'.$bank_option.'";' ;
	$rs = $conn->Execute($sql);

	//活儲帳號
	$bank_acc = $rs->fields['cBankAccount'] ;
	##
	
	//銀行代號


	$cBankCode = $rs->fields['cBankCode'] ;

	if ($bank_option == 77) {
		$cBankCode = "77,80";
	}
	##
	
	// unset($tmp) ;
}
//echo "bank_sql=".$sql."<br>\n" ; exit ;
#

//因有未結案先行出履保費所以增加條件[tra.tObjKind = "其他" AND tKind="保證費"]20151113
$sql = '
SELECT 
	tra.tBankLoansDate as tDate,
	cas.cEndDate AS cEndDate,
	tra.tMemo as tCertifiedId,
	tra.tVR_Code as VR_Code,
	tra.tMoney,
	(SELECT cCertifiedMoney FROM tContractIncome WHERE cCertifiedId=tra.tMemo) as cCertifiedMoney, 
	own.cIdentifyId as ownerId,
	own.cNHITax as ownerNHI,
	buy.cIdentifyId as buyerId,
	buy.cNHITax as buyerNHI,
	rea.cBranchNum as cBranchNum,
	rea.cBranchNum1 as cBranchNum1,
	rea.cBranchNum2 as cBranchNum2,
	cas.cCaseStatus AS cCaseStatus,
	tra.tAccount AS tAccount,
	cas.cSpCaseFeedBackMoney,
	cas.cCaseFeedBackMoney,
	cas.cCaseFeedBackMoney1,
	cas.cCaseFeedBackMoney2,
	buy.cInterestMoney AS buyer_cInterestMoney,
	buy.cInvoiceMoney AS buyer_cInvoiceMoney,
	own.cInterestMoney AS owner_cInterestMoney,
	own.cInvoiceMoney AS owner_cInvoiceMoney,
	rea.cInterestMoney AS branch_cInterestMoney,
	rea.cInterestMoney1 AS branch_cInterestMoney1,
	rea.cInterestMoney2 AS branch_cInterestMoney2,
	rea.cInvoiceMoney AS branch_cInvoiceMoney,
	rea.cInvoiceMoney1 AS branch_cInvoiceMoney1,
	rea.cInvoiceMoney2 AS branch_cInvoiceMoney2,
	(SELECT cInterestMoney FROM tContractScrivener AS csc WHERE csc.cCertifiedId=tra.tMemo) AS scr_cInterestMoney,
	(SELECT pName FROM tPeopleInfo WHERE pId=cas.cLastEditor) as lastmodify,
	(SELECT sName FROM tStatusCase WHERE sId =cas.cCaseStatus) AS status,
	(SELECT cInvoiceScrivener FROM tContractInvoice AS a WHERE a.cCertifiedId = tra.tMemo) AS scr_cInvoiceMoney,
	(SELECT cInvoiceOther FROM tContractInvoice AS a WHERE a.cCertifiedId = tra.tMemo) AS cInvoiceOther
FROM 
	tBankTrans AS tra 
JOIN
	tContractBuyer AS buy ON buy.cCertifiedId=tra.tMemo
JOIN
	tContractOwner AS own ON own.cCertifiedId=tra.tMemo
JOIN
	tContractRealestate AS rea ON rea.cCertifyId=tra.tMemo
JOIN 
	tContractCase AS cas ON cas.cCertifiedId=tra.tMemo
WHERE 
	tra.tExport="1"
	AND tra.tPayOk="1"
	AND tra.tExport_nu NOT LIKE "aaaaaaaaaaaa_"
	AND tKind="保證費"
	AND tra.tAccount="'.$bank_acc.'" 
	AND
	(tra.tBankLoansDate>="'.$fds.'" AND tra.tBankLoansDate<="'.$fde.'") 
 
ORDER BY
	tra.tExport_time,tra.tMemo 
ASC ;
' ;
$rs = $conn->Execute($sql);
$list = array() ;
while (!$rs->EOF) {
	array_push($list, $rs->fields);

	$rs->MoveNext();
}

##
// print_r($list) ; exit ;

//無履保費出款但有出利息
$sql = '
	SELECT
		cas.cBankList as tDate,
		cas.cEndDate as cEndDate,
		cas.cCertifiedId as tCertifiedId,
		cas.cEscrowBankAccount as VR_Code,
		cas.cCaseStatus AS cCaseStatus,		
		cas.cSpCaseFeedBackMoney,
		cas.cCaseFeedBackMoney,
		cas.cCaseFeedBackMoney1,
		cas.cCaseFeedBackMoney2,
		buy.cIdentifyId as buyerId,
		buy.cNHITax as buyerNHI,
		buy.cInterestMoney AS buyer_cInterestMoney,
		buy.cInvoiceMoney AS buyer_cInvoiceMoney,
		own.cInterestMoney AS owner_cInterestMoney,
		own.cInvoiceMoney AS owner_cInvoiceMoney,
		own.cIdentifyId as ownerId,
		own.cNHITax as ownerNHI,
		rea.cBranchNum as cBranchNum,
		rea.cBranchNum1 as cBranchNum1,
		rea.cBranchNum2 as cBranchNum2,
		rea.cInterestMoney AS branch_cInterestMoney,
		rea.cInterestMoney1 AS branch_cInterestMoney1,
		rea.cInterestMoney2 AS branch_cInterestMoney2,
		rea.cInvoiceMoney AS branch_cInvoiceMoney,
		rea.cInvoiceMoney1 AS branch_cInvoiceMoney1,
		rea.cInvoiceMoney2 AS branch_cInvoiceMoney2,
		(SELECT cCertifiedMoney FROM tContractIncome AS a WHERE a.cCertifiedId = cas.cCertifiedId) as cCertifiedMoney,
		(SELECT pName FROM tPeopleInfo WHERE pId=cas.cLastEditor) as lastmodify,
		(SELECT sName FROM tStatusCase WHERE sId =cas.cCaseStatus) AS status,
		(SELECT cInterestMoney FROM tContractScrivener AS a WHERE a.cCertifiedId=cas.cCertifiedId) as scr_cInterestMoney,
		(SELECT cInvoiceScrivener FROM tContractInvoice AS a WHERE a.cCertifiedId = cas.cCertifiedId) as scr_cInvoiceMoney,
		(SELECT cInvoiceOther FROM tContractInvoice AS a WHERE a.cCertifiedId = cas.cCertifiedId) as cInvoiceOther
	FROM
		tContractCase AS cas
	JOIN
		tContractBuyer AS buy ON buy.cCertifiedId=cas.cCertifiedId
	JOIN
		tContractOwner AS own ON own.cCertifiedId=cas.cCertifiedId
	JOIN
		tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId
	
	WHERE
		cas.cBankList>="'.$fds.'"
		AND cas.cBankList<="'.$fde.'"
		AND cas.cBankList<>""
		AND cas.cBank IN ('.$cBankCode.')
	ORDER BY
		cas.cBankList,cas.cCertifiedId
	ASC ;
' ;
$rs = $conn->Execute($sql);
$arr = array() ;
$i = 0;
while (!$rs->EOF) {
	$arr[$i] = $rs->fields;
	$arr[$i]['tMoney'] = '0' ;
	$i++;

	$rs->MoveNext();
}
##

$list = array_merge($list,$arr) ;
unset($arr);

// print_r($list) ; exit ;
$max = count($list) ;

//計算利息
for ($i = 0 ; $i < $max ; $i ++) {
	$list[$i]['expDate'] = $list[$i]['tDate'] ;
	$tmp = explode('-',$list[$i]['tDate']) ;
	$list[$i]['tDate'] = $tmp[1].'/'.$tmp[2] ;
	unset($tmp) ;
	
	//取得保證號碼、利息
	$tInterest = 0 ;
	
	//發票數量統計歸零
	$invoiceNo = 0 ;

	//買方人數
	$buyerNo = 0 ;
	//賣方人數
	$ownerNo = 0;
	##

	//回饋金
	$feedbackmoney = 0;
	$feedbackmoney = $list[$i]['cCaseFeedBackMoney'] + $list[$i]['cCaseFeedBackMoney1'] + $list[$i]['cCaseFeedBackMoney2']+$list[$i]['cSpCaseFeedBackMoney'];
	
	$tmp = getFeedBackMoney($list[$i]['tCertifiedId']);
	
	if (is_array($tmp)) {
		foreach ($tmp as $k => $v) {
			$feedbackmoney += $v['fMoney'];
		}
	}
	$list[$i]['Feed'] = $feedbackmoney;
	// $fedback[$list[$i]['tCertifiedId']] = $feedbackmoney;

	##

	$tInterest += $list[$i]['buyer_cInterestMoney'] + 1 - 1 ;
	if ($list[$i]['buyer_cInvoiceMoney'] != 0) {
		$invoiceNo +=1 ;
	}
	##
	
	//其他買方
	$sql = 'SELECT cInterestMoney,cInvoiceMoney FROM tContractOthers WHERE cCertifiedId="'.$list[$i]['tCertifiedId'].'" AND cIdentity="1";' ;
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$tInterest += $rs->fields['cInterestMoney'] + 1 - 1 ;
		if ($rs->fields['cInvoiceMoney'] != '0') {		//若開發票金額大於0，則累加
			$invoiceNo += 1 ;
		}
		$buyerNo++;
		$rs->MoveNext();
	}

	if ($buyerNo > 0) {
		$list[$i]['buyerNo'] = '('.($buyerNo + 1).'人)' ;
	}
	##

	$tInterest += $list[$i]['owner_cInterestMoney'] + 1 - 1 ;
	if ($list[$i]['owner_cInvoiceMoney'] != '0') {			//若開發票金額大於0，則累加
		$invoiceNo += 1 ;
	}
	##
	
	//其他賣方
	$sql = 'SELECT cInterestMoney,cInvoiceMoney FROM tContractOthers WHERE cCertifiedId="'.$list[$i]['tCertifiedId'].'" AND cIdentity="2";' ;
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$tInterest += $rs->fields['cInterestMoney'] + 1 - 1 ;
		if ($rs->fields['cInvoiceMoney'] != '0') {		//若開發票金額大於0，則累加
			$invoiceNo += 1 ;
		}
		$ownerNo++;
		$rs->MoveNext();
	}

	if ($ownerNo > 0) {
		$list[$i]['ownerNo'] = '('.($ownerNo + 1).'人)' ;
	}

	##
	
	$tInterest += $list[$i]['branch_cInterestMoney'] + 1 - 1 ;
	$tInterest += $list[$i]['branch_cInterestMoney1'] + 1 - 1 ;
	$tInterest += $list[$i]['branch_cInterestMoney2'] + 1 - 1 ;
	if ($list[$i]['branch_cInvoiceMoney'] != '0') {			//若開發票金額大於0，則累加
		$invoiceNo += 1 ;
	}
	
	if ($list[$i]['branch_cInvoiceMoney1'] != '0') {		//若開發票金額大於0，則累加
		$invoiceNo += 1 ;
	}

	if ($list[$i]['branch_cInvoiceMoney2'] != '0') {		//若開發票金額大於0，則累加
		$invoiceNo += 1 ;
	}
	##
	
	$tInterest += $list[$i]['scr_cInterestMoney'] + 1 - 1 ;
	if ($list[$i]['scr_cInvoiceMoney'] != '0') {			//若開發票金額大於0，則累加
		$invoiceNo += 1 ;
	}
	##

	if ($list[$i]['cInvoiceOther'] != '0') {
		$invoiceNo += 1 ;
	}

	$sql = 'SELECT cInvoiceMoney FROM tContractInvoiceExt WHERE cCertifiedId = "'.$list[$i]['tCertifiedId'].'"';
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		if ($rs->fields['cInvoiceMoney'] > 0) {
			$invoiceNo += 1 ;
		}


		$rs->MoveNext();
	}
	##

	//指定對象的利息
	$sql = 'SELECT cInterestMoney FROM tContractInterestExt WHERE cCertifiedId = "'.$list[$i]['tCertifiedId'].'"';
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$tInterest += $rs->fields['cInterestMoney'] + 1 - 1 ;

		$rs->MoveNext();
	}
	
	//利息總和
	$list[$i]['tInterest'] = $tInterest ;
	##
	
	//發票數總和
	$list[$i]['invoiceNo'] = $invoiceNo ;
	##
	// echo $list[$i]['tCertifiedId']."<Br>";
	
	
}
// print_r($list) ; exit ;
##


// $startDate = preg_replace("/-/",'',$startDate) ;
// $endDate = preg_replace("/-/",'',$endDate) ;


//剔除退款回存重複金額
$k = 0 ;
$max = count($list) ;
for ($i = 0 ; $i < $max ; $i ++) {
	$tmp = explode('-',$list[$i]['expDate']) ;
	$tDate = ($tmp[0] - 1911).$tmp[1].$tmp[2] ;
	unset($tmp) ;
	$tMoney = str_pad($list[$i]['tMoney'],13,'0',STR_PAD_LEFT).'00' ;
	
	$sql = '
		SELECT 
			* 
		FROM 
			tExpense 
		WHERE 
			eTradeCode="178Y" 
			AND eExportCode="8888888" 
			AND eDepAccount="00'.$list[$i]['VR_Code'].'" 
			AND (ePayTitle LIKE "%退款回存%" OR ePayTitle LIKE "退匯存入")
			AND eTradeDate="'.$tDate.'"
			AND eLender="'.$tMoney.'"
		;' ;
	//echo "Q=".$sql ;
	$rs = $conn->Execute($sql);
	$fg = 0 ; 
	while (!$rs->EOF) {
		$arr[$list[$i]['tCertifiedId']] ++ ;
		if ($arr[$list[$i]['tCertifiedId']] > 1) {
			$detail[$k] = $list[$i] ;
			$k ++ ;
		}
		$fg ++ ;

		$rs->MoveNext();
	}
	
	if (!$fg) {
		$detail[$k] = $list[$i] ;
		$k ++ ;
	}
	// echo $list[$i]['tCertifiedId']."\r\n";
}
// die('----');
##

$max = count($detail) ;
//print_r($detail) ; exit ;

$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("查詢明細");
$objPHPExcel->getProperties()->setDescription("第一建經案件資料查詢明細結果");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);

//調整欄位寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(16);

//調整欄位高度
$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(42);

//繪製框線
$objPHPExcel->getActiveSheet()->getStyle('A2:R2')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('A2:R2')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_NONE);
$objPHPExcel->getActiveSheet()->getStyle('A2:R2')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_NONE);
$objPHPExcel->getActiveSheet()->getStyle('A2:R2')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_NONE);

//總表標題列填色
$objPHPExcel->getActiveSheet()->getStyle('C2:F2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('C2:F2')->getFill()->getStartColor()->setARGB('00DBDCF2');

$objPHPExcel->getActiveSheet()->getStyle('L2:M2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('L2:M2')->getFill()->getStartColor()->setARGB('00DBDCF2');
//設定總表文字置中
$objPHPExcel->getActiveSheet()->getStyle('A:R')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('F1:P1')->getAlignment()->setWrapText(true);

//設定總表所有案件金額千分位符號
//$objPHPExcel->getActiveSheet()->getStyle('C2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

//設定字型大小
$objPHPExcel->getActiveSheet()->getStyle('A:R')->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->getStyle('A1:R1')->getFont()->setSize(12);
//$objPHPExcel->getActiveSheet()->getStyle('A2:N2')->getFont()->setSize(10);

//設定字型顏色
$objPHPExcel->getActiveSheet()->getStyle('G1:H1')->getFont()->getColor()->setARGB('000070C0');
$objPHPExcel->getActiveSheet()->getStyle('K1')->getFont()->getColor()->setARGB('000070C0');
$objPHPExcel->getActiveSheet()->getStyle('C1:F1')->getFont()->getColor()->setARGB('00FF0000');
$objPHPExcel->getActiveSheet()->getStyle('I1:J1')->getFont()->getColor()->setARGB('00FF0000');

//寫入表頭資料
$objPHPExcel->getActiveSheet()->setCellValue('C1','銀行入帳金額=A-B');//存入金額
$objPHPExcel->getActiveSheet()->setCellValue('E1','利息=B');//利息出
$objPHPExcel->getActiveSheet()->setCellValue('F1','應付履約保證費額=A');//履保費收入總額
$objPHPExcel->getActiveSheet()->setCellValue('G1','公式算出');//收入未稅
$objPHPExcel->getActiveSheet()->setCellValue('H1','公式算出');//收入稅額
$objPHPExcel->getActiveSheet()->setCellValue('I1','代扣利息所得稅');//代扣10%稅款
$objPHPExcel->getActiveSheet()->setCellValue('J1','代扣利息所得稅');//代扣2%保費
$objPHPExcel->getActiveSheet()->setCellValue('K1','公式算出');//差異數
$objPHPExcel->getActiveSheet()->setCellValue('S1','金額 = F 欄(履保費收入總額) - R 欄(回饋成本)');//差異數
$col=65;//ASCII 65
$objPHPExcel->getActiveSheet()->setCellValue('A2','交易日期');
$objPHPExcel->getActiveSheet()->setCellValue('B2','序號');
$objPHPExcel->getActiveSheet()->setCellValue('C2','存入金額');
$objPHPExcel->getActiveSheet()->setCellValue('D2','保證號碼');
$objPHPExcel->getActiveSheet()->setCellValue('E2','利息支出');
$objPHPExcel->getActiveSheet()->setCellValue('F2','履保費收入總額');
$objPHPExcel->getActiveSheet()->setCellValue('G2','收入未稅');
$objPHPExcel->getActiveSheet()->setCellValue('H2','收入稅額');
$objPHPExcel->getActiveSheet()->setCellValue('I2','代扣10%稅款');
$objPHPExcel->getActiveSheet()->setCellValue('J2','代扣2%保費');
$objPHPExcel->getActiveSheet()->setCellValue('K2','差異數');
$objPHPExcel->getActiveSheet()->setCellValue('L2','買方身份');
$objPHPExcel->getActiveSheet()->setCellValue('M2','賣方身份');
$objPHPExcel->getActiveSheet()->setCellValue('N2','應開發票數');
$objPHPExcel->getActiveSheet()->setCellValue('O2','仲介類型');
$objPHPExcel->getActiveSheet()->setCellValue('P2','最後修改者');
$objPHPExcel->getActiveSheet()->setCellValue('Q2','案件狀態');
$objPHPExcel->getActiveSheet()->setCellValue('R2','回饋成本');
$objPHPExcel->getActiveSheet()->setCellValue('S2','淨收入');

//寫入查詢資料
$k = 3 ;	// 起始位置
for ($i = 0 ; $i < $max ; $i ++) {
	$j = $k + $i ;
	
	//計算10%稅額
	$detail[$i]['paytax'] = $paytax = 0 ;
	//$paytax = payTax($detail[$i]['buyerId'],$detail[$i]['tInterest']) ;
	$detail[$i]['paytax'] = payTax($detail[$i]['ownerId'],$detail[$i]['tInterest']) ;
	$detail[$i]['paytax'] += $paytax ;
	##
	
	//計算2%補充保費
	$detail[$i]['NHITax'] = $NHITax = 0 ;
	//$NHITax = payNHITax($detail[$i]['buyerId'],$detail[$i]['buyerNHI'],$detail[$i]['tInterest']) ;
	$detail[$i]['NHITax'] = payNHITax($detail[$i]['ownerId'],$detail[$i]['ownerNHI'],$detail[$i]['tInterest']) ;
	$detail[$i]['NHITax'] += $NHITax ;
	##
	
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$j,$detail[$i]['tDate']);//交易日期

	

	

	$objPHPExcel->getActiveSheet()->setCellValue('B'.$j,($i+1));//序號
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$j,$detail[$i]['tMoney']); //存入金額
	$objPHPExcel->getActiveSheet()->getCell('D'.$j)->setValueExplicit($detail[$i]['tCertifiedId'], PHPExcel_Cell_DataType::TYPE_STRING); //保證號碼
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$j,$detail[$i]['tInterest']);//利息支出
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$j,$detail[$i]['cCertifiedMoney']);//履保費收入總額

	$money1 = round(($detail[$i]['cCertifiedMoney']/1.05),0);
	$money2 = ($detail[$i]['cCertifiedMoney']-$money1);
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$j,$money1);//公式算出1 = 履保費/1.05
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$j,$money2);//公式算出2 = 履保費-公式算出1

	$objPHPExcel->getActiveSheet()->setCellValue('I'.$j,$detail[$i]['paytax']);	//代扣10%稅款
	$objPHPExcel->getActiveSheet()->setCellValue('J'.$j,$detail[$i]['NHITax']);	//代扣2%保費


	//差異數=履保費收入總額-利息支出-存入金額+代扣10%稅款+代扣2%保費(X)
	// $objPHPExcel->getActiveSheet()->setCellValue('K'.$j,'=F'.$j.'-E'.$j.'-C'.$j.'+I'.$j.'+J'.$j);//差異數
	//K欄=存入金額+利息支出-公式算出1-公式算出2-代扣10%稅款-代扣2%保費
	
	$money3 = $detail[$i]['tMoney']+$detail[$i]['tInterest']-$money1-$money2-$detail[$i]['paytax']-$detail[$i]['NHITax'];
	$objPHPExcel->getActiveSheet()->setCellValue('K'.$j,$money3);//差異數

	$objPHPExcel->getActiveSheet()->getCell('L'.$j)->setValueExplicit(obj_id($detail[$i]['buyerId']).$detail[$i]['buyerNo']);//買方
	$objPHPExcel->getActiveSheet()->getCell('M'.$j)->setValueExplicit(obj_id($detail[$i]['ownerId']).$detail[$i]['ownerNo']);//賣方
	
	



	// $objPHPExcel->getActiveSheet()->setCellValue('M'.$j,'=H'.$j.'-G'.$j.'-F'.$j.'+K'.$j.'+L'.$j);
	



	$objPHPExcel->getActiveSheet()->setCellValue('N'.$j,$detail[$i]['invoiceNo']);//應開發票數
	
	//配件依據 "1.加盟(其他品牌)、2.加盟(台灣房屋)、3.優美、4.直營、5.非仲介成交" 順序掛帳
	$cBrand = '' ;
	$o = 0 ;			//加盟--其他品牌
	$t = 0 ;			//加盟--台灣房屋
	$u = 0 ;			//優美
	$s = 0 ;			//直營
	$n = 0 ;			//非仲介成交
	
	$bId = $detail[$i]['cBranchNum'] ;					//第一組仲介品牌代號
	if ($bId > 0) {
		$arrTmp = realtyCat($bId,$link) ;
		if ($arrTmp[0] == '1') {			//台灣房屋
			if ($arrTmp[1] == '2') {			//直營
				$s ++ ;
			}
			else {								//加盟
				$t ++ ;
			}
		}
		else if ($arrTmp[0] == '2') {		//非仲介成交
			$n ++ ;
		}
		else if ($arrTmp[0] == '49') {		//優美
			$u ++ ;
		}
		else {								//其他品牌
			$o ++ ;
		}
	}
	
	$bId = $detail[$i]['cBranchNum1'] ;
	if ($bId > 0) {										//第二組仲介是否存在
		$arrTmp = realtyCat($bId,$link) ;
		if ($arrTmp[0] == '1') {			//台灣房屋
			if ($arrTmp[1] == '2') {			//直營
				$s ++ ;
			}
			else {								//加盟
				$t ++ ;
			}
		}
		else if ($arrTmp[0] == '2') {		//非仲介成交
			$n ++ ;
		}
		else if ($arrTmp[0] == '49') {		//優美
			$u ++ ;
		}
		else {								//其他品牌
			$o ++ ;
		}
	}
	
	$bId = $detail[$i]['cBranchNum2'] ;
	if ($bId > 0) {										//第三組仲介是否存在
		$arrTmp = realtyCat($bId,$link) ;
		if ($arrTmp[0] == '1') {			//台灣房屋
			if ($arrTmp[1] == '2') {			//直營
				$s ++ ;
			}
			else {								//加盟
				$t ++ ;
			}
		}
		else if ($arrTmp[0] == '2') {		//非仲介成交
			$n ++ ;
		}
		else if ($arrTmp[0] == '49') {		//優美
			$u ++ ;
		}
		else {								//其他品牌
			$o ++ ;
		}
	}
	
	if ($o > 0) {
		$cBrand = '加盟(其他品牌)' ;
	}
	else if ($t > 0) {
		$cBrand = '加盟(台灣房屋)' ;
	}
	else if ($u > 0) {
		$cBrand = '加盟(優美地產)' ;
	}
	else if ($s > 0) {
		$cBrand = '直營' ;
	}
	else {
		$cBrand = '非仲介成交' ;
	}
	##
	
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$j,$cBrand);//仲介類型
	
	//最後修改人
	$objPHPExcel->getActiveSheet()->setCellValue('P'.$j,$detail[$i]['lastmodify']);
	##



	

	$objPHPExcel->getActiveSheet()->setCellValue('Q'.$j,$detail[$i]['status']);	
	##

	//回饋金
	##
	$objPHPExcel->getActiveSheet()->setCellValue('R'.$j,$list[$i]['Feed']);

	//
	$objPHPExcel->getActiveSheet()->setCellValue('S'.$j,($detail[$i]['cCertifiedMoney']-$list[$i]['Feed']));

	// preg_match("/(.*)-(.*)-(.*) (.*):(.*):(.*)/i",$detail[$i]['cEndDate'],$tmp);
	
	// $tmp[1] = $tmp[1]-1911;
	// $detail[$i]['cEndDate'] = $tmp[2].'/'.$tmp[3];
	
	// $objPHPExcel->getActiveSheet()->setCellValue('R'.$j,$detail[$i]['cEndDate']);

	// unset($tmp);
	##
}


//Rename sheet 重命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('銀行點交結算統計');

//Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


//Save Excel 2007 file 保存
//$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

//$file_name = date("Y_m_d").'.xlsx' ;
//$file_name = '銀行點交結算統計表.xlsx' ;

//$file_path = '/home/httpd/html/'.substr($web_addr,7).'/accounting/excel/' ;

//$_file = $file_path.$file_name ;
//$objWriter->save($_file);

$_file = 'bankChecklist.xlsx' ;

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-type:application/force-download');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$_file);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("php://output");



exit ;

?>
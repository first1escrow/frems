<?php
ini_set('memory_limit', '2048M');
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../session_check.php' ;
require_once dirname(__DIR__).'/first1DB.php';

$conn = new first1DB();

if ($_SESSION['member_id'] == 6) {
	ini_set("display_errors", "On"); 
	error_reporting(E_ALL & ~E_NOTICE);
}

//增加利息對項手動新增對象撈取(2015-08-11)
Function getOthers($cCertifiedId, $tb, $tBankLoansDate) {
	global $conn ;
	
	//取得其他方資料
	$sql = '
		SELECT 
			*,
			cCertifiedId as sn,
			cBankAccNum as BankAccNo,
			cBankAccName as BankAccName,
			cInterestMoney as cInterest,
			(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4="") as MainBank,
			(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4=a.cBankBranch) as BranchBank
		FROM 
			tContractInterestExt AS a
		WHERE 
			cCertifiedId="'.$cCertifiedId.'" 
			AND cInterestMoney<>"0"
			AND cDBName="'.$tb.'"
		ORDER BY
			cId
		ASC
	;' ;
	$rel = $conn->all($sql);

	$list = array() ;
	foreach ($rel as $tmp) {
		$tmp['cRegistCity'] = zipConv($conn, $tmp['cRegistZip'], 'zCity') ;
		$tmp['cRegistArea'] = zipConv($conn, $tmp['cRegistZip'], 'zArea') ;
		$tmp['cRegistZip'] = preg_replace("/[a-zA-Z]/",'',$tmp['cRegistZip']) ;
		
		$tmp['cBaseCity'] = zipConv($conn, $tmp['cBaseZip'], 'zCity') ;
		$tmp['cBaseArea'] = zipConv($conn, $tmp['cBaseZip'], 'zArea') ;
		$tmp['cBaseZip'] = preg_replace("/[a-zA-Z]/",'',$tmp['cBaseZip']) ;
		
		
		if (($tb == 'tContractBuyer') || ($tb == 'tContractOthersB')) $tmp['obj'] = '買方' ;
		else if (($tb == 'tContractOwner') || ($tb == 'tContractOthersO')) $tmp['obj'] = '賣方' ;
		else if (($tb == 'tContractRealestate') || ($tb == 'tContractRealestate1') || ($tb == 'tContractRealestate2')) $tmp['obj'] = '仲介' ;
		else if ($tb == 'tContractScrivener') $tmp['obj'] = '地政士' ;
		
		$tmp['tBankLoansDate'] = $tBankLoansDate ;
		
		$list[] = $tmp ;
		
		unset($tmp) ;
	}
	##
	
	return $list ;
}
##

//取得匯出時間
Function ETime(&$conn, $no) {
	$_sql = 'SELECT tBankLoansDate FROM tBankTrans WHERE tMemo="'.$no.'";' ;
	$_tmp = $conn->one($_sql);

	return $_tmp['tBankLoansDate'] ;
}
##

Function payTax($_id, $_int=0) {
	$_len = strlen($_id) ; 										// 個人10碼 公司8碼

	if ($_len == '10') {										// 個人10碼
		if (preg_match("/[A-Za-z]{2}/",$_id) || preg_match("/[a-zA-z]{1}[8|9]{1}[0-9]{8}/",$_id)) {					// 判別是否為外國人(兩碼英文字母者) 		
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
Function payNHITax($_id, $_ide=0, $_int=0) {
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

//取得仲介基本資料
Function getBranch(&$conn, $sn, $no=0, $m=0) {
	$_sql = '
		SELECT
			bName as cName,
			bSerialnum as Serialnum1,
			bIdentityNumber as Serialnum2,
			bZip as cRegistZip,
			bAddress as cRegistAddr,
			(SELECT bBank4_name FROM tBank WHERE bBank3=a.bAccountNum1 AND bBank4="") as MainBank,
			(SELECT bBank4_name FROM tBank WHERE bBank3=a.bAccountNum1 AND bBank4=a.bAccountNum2) as BranchBank,
			bAccount3 as BankAccNo,
			bAccount4 as BankAccName
		FROM
			tBranch AS a
		WHERE
			bId="'.$no.'"
	' ;
	$arr = $conn->one($_sql);

	if ($arr) {
		$arr['cCertifiedId'] = $arr['sn'] = $sn ;
		$arr['cInterest'] = $m ;
		
		//確認統一編號
		if ($arr['Serialnum1']) {		//有
			$arr['cIdentifyId'] = $arr['Serialnum1'] ;
		}
		##
					
		$arr['cRegistCity'] = zipConv($conn, $arr['cRegistZip'], 'zCity') ;
		$arr['cRegistArea'] = zipConv($conn, $arr['cRegistZip'], 'zArea') ;
		$arr['cRegistZip'] = preg_replace("/[a-zA-Z]/", '', $arr['cRegistZip']) ;
		
		$arr['cBaseCity'] = zipConv($conn, $arr['cBaseZip'], 'zCity') ;
		$arr['cBaseArea'] = zipConv($conn, $arr['cBaseZip'], 'zArea') ;
		$arr['cBaseZip'] = preg_replace("/[a-zA-Z]/", '', $arr['cBaseZip']) ;
	}
	
	return $arr ;
}
##

//郵遞區號轉縣市區域名稱
Function zipConv(&$conn, $_zip, $_name='zCity') {
	$_sql = 'SELECT '.$_name.' FROM tZipArea WHERE zZip="'.$_zip.'";';
	$_tmp = $conn->one($_sql);

	return $_tmp[$_name] ;
}
##

$identity = trim($_POST['identity']) ;
$feedback_year = trim($_POST['feedback_year']) ;
$feedback_month = trim($_POST['feedback_month']) ;
$sn = trim($_POST['sn']) ;
$tax_name = trim($_POST['tax_name']) ;
$tax_id = trim($_POST['tax_id']) ;

//取得合約銀行資訊
$sql = 'SELECT * FROM tContractBank WHERE cShow="1" ORDER BY cId ASC ;' ;
$rel = $conn->all($sql);
for ($i = 0 ; $i < count($rel) ; $i ++) {
	$conBank[$i] = $rel[$i];
	$sNo[$i] = $conBank[$i]['cBankAccount'] ;		//活儲帳號
}

if ($sNo) {		//若搜尋到活儲帳號，則編輯成SQL字串格式
	$savingsNo = implode($sNo,'","') ;
	$savingsNo = '"'.$savingsNo.'"' ;
}
$rel = null;
unset($rel);
##

// 依據身份別決定搜尋資料表
$list = [];
switch ($identity) {
	case "1":	// 買賣方利息
		$arr3 = array() ;
		
		if ($feedback_month) {
			$f_month = $e_month = str_pad($feedback_month,2,'0',STR_PAD_LEFT) ;
		}
		else {
			$f_month = '01' ;
			$e_month = '12' ;
		}
		
		$tmp = explode('-',$feedback_year ) ;
		$f_date = $tmp[0].'-'.$f_month.'-01' ;
		$e_date = $tmp[0].'-'.$e_month.'-31' ;
		unset($tmp) ;
		
		// 取得特定保證號碼
		if ($sn) { $ssn = ' AND cCertifiedId="'.$sn.'" ' ; }
		if ($sn) { $sn = ' AND tMemo = "'.$sn.'" ' ; }

		//取得年度內無履保但有出利息之案件
		$tmp = explode(' ',$f_date) ;
		$ff_date = $tmp[0] ;
		$tmp = explode(' ',$e_date) ;
		$ee_date = $tmp[0] ;

		$tmp = null;
		unset($tmp);
		
		$sql = '
			SELECT
				cCertifiedId,
				cFeedbackDate as tBankLoansDate
			FROM
				tContractCase
			WHERE
				cFeedbackDate<>""
				AND cFeedbackDate>="'.$ff_date.'"
				AND cFeedbackDate<="'.$ee_date.'"
				'.$ssn.'
			ORDER BY
				cFeedbackDate,cCertifiedId
			ASC ;
		' ;

		$rel = $conn->all($sql);
		$list = array_merge($list, $rel);

		$rel = null;
		unset($rel);
		##

		$bindex = 0 ;
		$buyer = array() ;
		$oindex = 0 ;
		$owner = array() ;
		$rindex = 0 ;
		$realty = array() ;
		$scr = array() ;
		
		$max = count($list) ;
		$allCertifiedId = array();
		for ($i = 0 ; $i < $max ; $i ++) {
			if(in_array($list[$i]['cCertifiedId'], $allCertifiedId)) {
				continue;
			}
			$allCertifiedId[] = $list[$i]['cCertifiedId'];

			//取得匯出時間
			if (!$list[$i]['tBankLoansDate']) {
				$list[$i]['tBankLoansDate'] = ETime($conn, $list[$i]['cCertifiedId']) ;
			}
			##
			
			//取得合約書買方資料
			$sql = '
				SELECT 
					*,
					cCertifiedId as sn,
					cBankAccNumber as BankAccNo,
					cBankAccName as BankAccName,
					cInterestMoney as cInterest,
					(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankKey2 AND bBank4="") as MainBank,
					(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankKey2 AND bBank4=a.cBankBranch2) as BranchBank
				FROM 
					tContractBuyer AS a
				WHERE 
					cCertifiedId="'.$list[$i]['cCertifiedId'].'" 
					AND a.cInterestMoney<>"0";
				' ;
			$rel = $conn->one($sql);
			if (!empty($rel)) {
				$buyer[$bindex] = $rel;
				
				$buyer[$bindex]['cRegistCity'] = zipConv($conn, $buyer[$bindex]['cRegistZip'],'zCity') ;
				$buyer[$bindex]['cRegistArea'] = zipConv($conn, $buyer[$bindex]['cRegistZip'],'zArea') ;
				$buyer[$bindex]['cRegistZip'] = preg_replace("/[a-zA-Z]/", '', $buyer[$bindex]['cRegistZip']) ;
				
				$buyer[$bindex]['cBaseCity'] = zipConv($conn, $buyer[$bindex]['cBaseZip'],'zCity') ;
				$buyer[$bindex]['cBaseArea'] = zipConv($conn, $buyer[$bindex]['cBaseZip'],'zArea') ;
				$buyer[$bindex]['cBaseZip'] = preg_replace("/[a-zA-Z]/", '', $buyer[$bindex]['cBaseZip']) ;
				
				$buyer[$bindex]['obj'] = '買方' ;
				$buyer[$bindex]['tBankLoansDate'] = $list[$i]['tBankLoansDate'] ;
				
				$arr3[] = $buyer[$bindex] ;
				
				$bindex ++ ;
			}

			$rel = null;
			unset($rel);
			##
			
			//增加利息對象 (2015-08-11)
			$_arr = array() ;
			$_arr = getOthers($list[$i]['cCertifiedId'], 'tContractBuyer', $list[$i]['tBankLoansDate']) ;
			$arr3 = array_merge($arr3,$_arr) ;

			$_arr = null;
			unset($_arr) ;
			##
			
			//取得其他買方資料
			$sql = '
				SELECT 
					*,
					cCertifiedId as sn,
					cBankAccNum as BankAccNo,
					cBankAccName as BankAccName,
					cInterestMoney as cInterest,
					(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4="") as MainBank,
					(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4=a.cBankBranch) as BranchBank
				FROM 
					tContractOthers AS a
				WHERE 
					cCertifiedId="'.$list[$i]['cCertifiedId'].'" 
					AND cInterestMoney<>"0"
					AND cIdentity="1" ;
				' ;
			$rel = $conn->all($sql);

			if (!empty($rel)) {
				foreach ($rel as $v) {
					$buyer[$bindex] = $v;

					$buyer[$bindex]['cRegistCity'] = zipConv($conn, $buyer[$bindex]['cRegistZip'],'zCity') ;
					$buyer[$bindex]['cRegistArea'] = zipConv($conn, $buyer[$bindex]['cRegistZip'],'zArea') ;
					$buyer[$bindex]['cRegistZip'] = preg_replace("/[a-zA-Z]/", '', $buyer[$bindex]['cRegistZip']) ;
					
					$buyer[$bindex]['cBaseCity'] = zipConv($conn, $buyer[$bindex]['cBaseZip'], 'zCity') ;
					$buyer[$bindex]['cBaseArea'] = zipConv($conn, $buyer[$bindex]['cBaseZip'], 'zArea') ;
					$buyer[$bindex]['cBaseZip'] = preg_replace("/[a-zA-Z]/", '', $buyer[$bindex]['cBaseZip']) ;
					
					$buyer[$bindex]['obj'] = '買方' ;
					$buyer[$bindex]['tBankLoansDate'] = $list[$i]['tBankLoansDate'] ;
					
					$arr3[] = $buyer[$bindex] ;
					
					$bindex ++ ;
				}
			}

			$rel = null;
			unset($rel);
			##
			
			//增加利息對象 (2015-08-11)
			$_arr = array() ;
			$_arr = getOthers($list[$i]['cCertifiedId'], 'tContractOthersB', $list[$i]['tBankLoansDate']) ;
			$arr3 = array_merge($arr3,$_arr) ;

			$_arr = null;
			unset($_arr) ;
			##

			//取得合約書賣方資料
			$sql = '
				SELECT 
					*,
					cCertifiedId as sn,
					cBankAccNumber as BankAccNo,
					cBankAccName as BankAccName,
					cInterestMoney as cInterest,
					(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankKey2 AND bBank4="") as MainBank,
					(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankKey2 AND bBank4=a.cBankBranch2) as BranchBank
				FROM 
					tContractOwner AS a
				WHERE 
					cCertifiedId="'.$list[$i]['cCertifiedId'].'" 
					AND cInterestMoney<>"0";
				' ;
			$rel = $conn->one($sql);
			if (!empty($rel)) {
				$owner[$oindex] = $rel;
				
				$owner[$oindex]['cRegistCity'] = zipConv($conn, $owner[$oindex]['cRegistZip'], 'zCity') ;
				$owner[$oindex]['cRegistArea'] = zipConv($conn, $owner[$oindex]['cRegistZip'], 'zArea') ;
				$owner[$oindex]['cRegistZip'] = preg_replace("/[a-zA-Z]/", '', $owner[$oindex]['cRegistZip']) ;
				
				$owner[$oindex]['cBaseCity'] = zipConv($conn, $owner[$oindex]['cBaseZip'], 'zCity') ;
				$owner[$oindex]['cBaseArea'] = zipConv($conn, $owner[$oindex]['cBaseZip'], 'zArea') ;
				$owner[$oindex]['cBaseZip'] = preg_replace("/[a-zA-Z]/", '', $owner[$oindex]['cBaseZip']) ;

				$owner[$oindex]['obj'] = '賣方' ;
				$owner[$oindex]['tBankLoansDate'] = $list[$i]['tBankLoansDate'] ;
				
				$arr3[] = $owner[$oindex] ;
				
				$oindex ++ ;
			}

			$rel = null;
			unset($rel);
			##
			
			//增加利息對象 (2015-08-11)
			$_arr = array() ;
			$_arr = getOthers($list[$i]['cCertifiedId'], 'tContractOwner', $list[$i]['tBankLoansDate']) ;
			$arr3 = array_merge($arr3,$_arr) ;

			$_arr = null;
			unset($_arr) ;
			##

			//取得其他賣方資料
			$sql = '
				SELECT 
					*,
					cCertifiedId as sn,
					cBankAccNum as BankAccNo,
					cBankAccName as BankAccName,
					cInterestMoney as cInterest,
					(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4="") as MainBank,
					(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4=a.cBankBranch) as BranchBank
				FROM 
					tContractOthers AS a
				WHERE 
					cCertifiedId="'.$list[$i]['cCertifiedId'].'" 
					AND cInterestMoney<>"0"
					AND cIdentity="2" ;
				' ;
			$rel = $conn->all($sql);
			if (!empty($rel)) {
				foreach ($rel as $v) {
					$owner[$oindex] = $v;

					$owner[$oindex]['cRegistCity'] = zipConv($conn, $owner[$oindex]['cRegistZip'], 'zCity') ;
					$owner[$oindex]['cRegistArea'] = zipConv($conn, $owner[$oindex]['cRegistZip'], 'zArea') ;
					$owner[$oindex]['cRegistZip'] = preg_replace("/[a-zA-Z]/", '', $owner[$oindex]['cRegistZip']) ;
					
					$owner[$oindex]['cBaseCity'] = zipConv($conn, $owner[$oindex]['cBaseZip'], 'zCity') ;
					$owner[$oindex]['cBaseArea'] = zipConv($conn, $owner[$oindex]['cBaseZip'], 'zArea') ;
					$owner[$oindex]['cBaseZip'] = preg_replace("/[a-zA-Z]/", '', $owner[$oindex]['cBaseZip']) ;
					
					$owner[$oindex]['obj'] = '賣方' ;
					$owner[$oindex]['tBankLoansDate'] = $list[$i]['tBankLoansDate'] ;
					
					$arr3[] = $owner[$oindex] ;
					
					$oindex ++ ;
				}
			}

			$rel = null;
			unset($rel);
			##
			
			//增加利息對象 (2015-08-11)
			$_arr = array() ;
			$_arr = getOthers($list[$i]['cCertifiedId'], 'tContractOthersO', $list[$i]['tBankLoansDate']) ;
			$arr3 = array_merge($arr3,$_arr) ;
			unset($_arr) ;
			##

			//取得仲介資料
			$sql = '
				SELECT
					*
				FROM
					tContractRealestate
				WHERE
					cCertifyId="'.$list[$i]['cCertifiedId'].'" ;					
			' ;
			$rel = $conn->one($sql);
			if (!empty($rel)) {
				$tmp = $rel ;
				//第一家仲介
				if ($tmp['cInterestMoney'] != '0') {
					$realty[$rindex] = getBranch($conn, $list[$i]['cCertifiedId'], $tmp['cBranchNum'], $tmp['cInterestMoney']) ;				
					$realty[$rindex]['obj'] = '仲介' ;
					$realty[$rindex]['tBankLoansDate'] = $list[$i]['tBankLoansDate'] ;
					
					$arr3[] = $realty[$rindex] ;
					
					$rindex ++ ;
				}
				##

				//增加利息對象 (2015-08-11)
					$_arr = array() ;
					$_arr = getOthers($list[$i]['cCertifiedId'], 'tContractRealestate', $list[$i]['tBankLoansDate']) ;
					$arr3 = array_merge($arr3,$_arr) ;
					unset($_arr) ;
					##
				
				//第二家仲介
				if ($tmp['cInterestMoney1'] != '0') {
					$realty[$rindex] = getBranch($conn, $list[$i]['cCertifiedId'], $tmp['cBranchNum1'], $tmp['cInterestMoney1']) ;
					$realty[$rindex]['obj'] = '仲介' ;
					$realty[$rindex]['tBankLoansDate'] = $list[$i]['tBankLoansDate'] ;
					
					$arr3[] = $realty[$rindex] ;
					
					$rindex ++ ;
				}
				##

				//增加利息對象 (2015-08-11)
					$_arr = array() ;
					$_arr = getOthers($list[$i]['cCertifiedId'], 'tContractRealestate1', $list[$i]['tBankLoansDate']) ;
					$arr3 = array_merge($arr3,$_arr) ;
					unset($_arr) ;
					##
				
				//第三家仲介
				if ($tmp['cInterestMoney2'] != '0') {
					$realty[$rindex] = getBranch($conn, $list[$i]['cCertifiedId'], $tmp['cBranchNum2'], $tmp['cInterestMoney2']) ;
					$realty[$rindex]['obj'] = '仲介' ;
					$realty[$rindex]['tBankLoansDate'] = $list[$i]['tBankLoansDate'] ;
					
					$arr3[] = $realty[$rindex] ;
					
					$rindex ++ ;
					
					
				}
				##
				//增加利息對象 (2015-08-11)
					$_arr = array() ;
					$_arr = getOthers($list[$i]['cCertifiedId'], 'tContractRealestate2', $list[$i]['tBankLoansDate']) ;
					$arr3 = array_merge($arr3,$_arr) ;
					unset($_arr) ;
					##
				
				unset($tmp) ;
			}

			$rel = null;
			unset($rel);
			##
			
			//取得代書資料
			$sql = '
				SELECT
					*,
					cInterestMoney as cInterest
				FROM
					tContractScrivener 
				WHERE
					cCertifiedId="'.$list[$i]['cCertifiedId'].'"
					AND cInterestMoney<>"0"
			' ;
			
			$tmp = $conn->one($sql);
			if (!empty($tmp)) {
				$sql = '
					SELECT
						sName as cName,
						sIdentifyId as cIdentifyId,
						sZip1 as cRegistZip,
						sAddress as cRegistAddr,
						(SELECT zCity FROM tZipArea WHERE zZip=a.sZip1) as cRegistCity,
						(SELECT zArea FROM tZipArea WHERE zZip=a.sZip1) as cRegistArea,
						(SELECT bBank4_name FROM tBank WHERE bBank3=a.sAccountNum1 AND bBank4="") as MainBank,
						(SELECT bBank4_name FROM tBank WHERE bBank3=a.sAccountNum1 AND bBank4=a.sAccountNum2) as BranchBank,
						sAccount3 as BankAccNo,
						sAccount4 as BankAccName
					FROM
						tScrivener AS a
					WHERE
						sId="'.$tmp['cScrivener'].'"
				' ;
				
				$scr[0] = $conn->one($sql);
				
				$scr[0]['cRegistCity'] = zipConv($conn, $scr[0]['cRegistZip'], 'zCity') ;
				$scr[0]['cRegistArea'] = zipConv($conn, $scr[0]['cRegistZip'], 'zArea') ;
				$scr[0]['cRegistZip'] = preg_replace("/[a-zA-Z]/", '', $scr[0]['cRegistZip']) ;
				
				$scr[0]['sn'] = $list[$i]['cCertifiedId'] ;
				$scr[0]['cInterest'] = $tmp['cInterest'] ;
				$scr[0]['obj'] = '地政士' ;
				$scr[0]['tBankLoansDate'] = $list[$i]['tBankLoansDate'] ;
				
				$arr3[] = $scr[0] ;
			}

			$tmp = null;
			unset($tmp);
			##
			
			//增加利息對象 (2015-08-11)
			$_arr = array() ;
			$_arr = getOthers($list[$i]['cCertifiedId'], 'tContractScrivener', $list[$i]['tBankLoansDate']) ;
			$arr3 = array_merge($arr3,$_arr) ;

			$_arr = null;
			unset($_arr);
			##
		}
		//$owner = array_merge($buyer,$owner) ;
		//$realty = array_merge($owner,$realty) ;
		//$arr3 = array_merge($realty,$scr) ;
		
		$list = null;
		unset($list);

		$max = count($arr3) ;
		$j = 0 ;
		$arr = array() ;
		
		if ($tax_id && $tax_name) {			// 過濾條件包含身份證字號與姓名
			for ($i = 0 ; $i < $max ; $i ++) {
				if ((($arr3[$i]['cIdentifyId']==$tax_id)&&($arr3[$i]['cName']==$tax_name))&&($arr3[$i]['cInterest']>0)) {		//利息需大於零
					$arr[$j++] = $arr3[$i] ;
				}
			}
		}
		else if ($tax_id && !$tax_name) {	// 過濾條件包含身份證字號
			for ($i = 0 ; $i < $max ; $i ++) {
				if (($arr3[$i]['cIdentifyId']==$tax_id)&&($arr3[$i]['cInterest']>0)) {		//利息需大於零
					$arr[$j++] = $arr3[$i] ;
				}
			}
		}
		else if (!$tax_id && $tax_name) {	// 過濾條件包含姓名
			for ($i = 0 ; $i < $max ; $i ++) {
				if (($arr3[$i]['cName']==$tax_name)&&($arr3[$i]['cInterest']>0)) {		//利息需大於零
					$arr[$j++] = $arr3[$i] ;
				}
			}
		}
		else {								// 過濾條件沒有定義身份證字號與姓名條件
			for ($i = 0 ; $i < $max ; $i ++) {
				if ($arr3[$i]['cInterest']>0) {		//利息需大於零
					$arr[$j++] = $arr3[$i] ;
				}
			}
		}
		
		$arr3 = null;
		unset($arr3) ;	
		
		$detail = array_merge($arr) ;
				
		$max = count($detail) ;

		$arr = null;
		unset($arr);
		break ;
		
	case "2":	// 個人回饋金
		$sql_str = '' ;
		// 取得特定店編號
		if ($sn) { 
			$sn = preg_replace("/^[a-zA-Z]{2}/",'',$sn) ;
			$sql_str .= ' AND tax.cBranchNum = "'.$sn.'" ' ;	
		}

		// 取得姓名資料
		if ($tax_name) {
			$sql_str .= ' AND bra.bTtitle LIKE "%'.$tax_name.'%" ' ;
		}
		
		// 取得身分證字號
		if ($tax_id) {
			$sql_str .= ' AND bra.bIdentityNumber="'.$tax_id.'" ' ;
		}
		
		// 本年度前三季
		$sql = '
		SELECT 
			tax.cBranchNum cBranchNum,
			tax.FBYear FBYear,
			tax.FBS1 FBS1,
			tax.FBS2 FBS2,
			tax.FBS3 FBS3,
			tax.FBS4 FBS4,
			CONCAT(
				(SELECT bCode FROM tBrand WHERE bId=bra.bBrand),
				LPAD(bra.bId,5,"0")
			) sn,
			bra.bStore bStore,
			bra.bTtitle cName,
			bra.bIdentityNumber cIdentifyId,
			SUBSTR(bra.bZip2,1,3) as cRegistZip,
			(SELECT zCity FROM tZipArea WHERE zZip=bra.bZip2) cRegistCity,
			(SELECT zArea FROM tZipArea WHERE zZip=bra.bZip2) cRegistArea,
			bra.bAddr2 cRegistAddr,
			SUBSTR(bra.bZip3,1,3) as cBaseZip,
			(SELECT zCity FROM tZipArea WHERE zZip=SUBSTR(bra.bZip3,1,3)) as cBaseCity,
			(SELECT zArea FROM tZipArea WHERE zZip=SUBSTR(bra.bZip3,1,3)) as cBaseArea,
			bra.bAddr3 cBaseAddr,
			bra.bAccountNum5 as MainBank_no,
			bra.bAccountNum6 as BranchBank_no,
			(SELECT bBank4_name FROM tBank WHERE bBank3=bra.bAccountNum5 AND bBank4="") as MainBank,
			(SELECT bBank4_name FROM tBank WHERE bBank3=bra.bAccountNum5 AND bBank4=bra.bAccountNum6) as BranchBank,
			bra.bAccount7 as BankAccNo,
			bra.bAccount8 as BankAccName
		FROM 
			tTaxFeedBack AS tax
		JOIN 
			tBranch AS bra ON bra.bId=tax.cBranchNum
		WHERE
			FBYear="'.$feedback_year.'" '.$sql_str.'
		ORDER BY 
			cBranchNum 
		ASC ;
		' ;
		$rel = $conn->all($sql);

		for ($i = 0 ; $i < count($rel) ; $i ++) {
			$arr1[$i] = $rel[$i];
			$arr1[$i]['cInterest'] = $arr1[$i]['FBS1'] + $arr1[$i]['FBS2'] + $arr1[$i]['FBS3'] ;
			if ($arr1[$i]['MainBank_no']) {
				$arr1[$i]['MainBank'] = $arr1[$i]['MainBank'].'('.$arr1[$i]['MainBank_no'].')' ;
			}
			if ($arr1[$i]['BranchBank']) {
				$arr1[$i]['BranchBank'] = $arr1[$i]['BranchBank'].'('.$arr1[$i]['BranchBank_no'].')' ;
			}
		}
		
		//去年度第四季
		$_feedback_year = date("Y",mktime(0,0,0,1,1,($feedback_year-1))) ;

		$sql = '
			SELECT 
				tax.cBranchNum cBranchNum,
				tax.FBYear FBYear,
				tax.FBS1 FBS1,
				tax.FBS2 FBS2,
				tax.FBS3 FBS3,
				tax.FBS4 FBS4,
				CONCAT(
					(SELECT bCode FROM tBrand WHERE bId=bra.bBrand),
					LPAD(bra.bId,5,"0")
				) sn,
				bra.bStore bStore,
				bra.bTtitle cName,
				bra.bIdentityNumber cIdentifyId,
				SUBSTR(bra.bZip2,1,3) as cRegistZip,
				(SELECT zCity FROM tZipArea WHERE zZip=bra.bZip2) cRegistCity,
				(SELECT zArea FROM tZipArea WHERE zZip=bra.bZip2) cRegistArea,
				bra.bAddr2 cRegistAddr,
				SUBSTR(bra.bZip3,1,3) as cBaseZip,
				(SELECT zCity FROM tZipArea WHERE zZip=SUBSTR(bra.bZip3,1,3)) as cBaseCity,
				(SELECT zArea FROM tZipArea WHERE zZip=SUBSTR(bra.bZip3,1,3)) as cBaseArea,
				bra.bAddr3 cBaseAddr,
				bra.bAccountNum5 as MainBank_no,
				bra.bAccountNum6 as BranchBank_no,
				(SELECT bBank4_name FROM tBank WHERE bBank3=bra.bAccountNum5 AND bBank4="") as MainBank,
				(SELECT bBank4_name FROM tBank WHERE bBank3=bra.bAccountNum5 AND bBank4=bra.bAccountNum6) as BranchBank,
				bra.bAccount7 as BankAccNo,
				bra.bAccount8 as BankAccName
			FROM 
				tTaxFeedBack AS tax
			JOIN 
				tBranch AS bra ON bra.bId=tax.cBranchNum
			WHERE
				FBYear="'.$_feedback_year.'" '.$sql_str.'
			ORDER BY 
				cBranchNum 
			ASC ;
		' ;
		
		$max = count($arr1) ;
		$rel = $conn->all($sql);
		$j = 0 ;
		$arr2 = array() ;

		foreach ($rel as $tmp) {
			$fg = 0 ; 
			for ($i = 0 ; $i < $max ; $i ++) {
				if ($arr1[$i]['cBranchNum']==$tmp['cBranchNum']) {
					$arr1[$i]['cInterest'] += ($tmp['FBS4'] + 1 - 1) ;
					$fg ++ ;						// 若有相同仲介編號，則將旗標 +1
				}
			}
			if (!$fg) {								// 若查無此公司編號，則加入陣列中
				$arr2[$j] = $tmp ; 
				
				if ($arr2[$j]['MainBank_no']) {
					$arr2[$j]['MainBank'] = $arr2[$j]['MainBank'].'('.$arr2[$j]['MainBank_no'].')' ;
				}
				if ($arr2[$j]['BranchBank']) {
					$arr2[$j]['BranchBank'] = $arr2[$j]['BranchBank'].'('.$arr2[$j]['BranchBank_no'].')' ;
				}
				
				$j ++ ;
			}
			unset($tmp) ;
		}

		$_detail = @array_merge($arr1,$arr2) ;
		unset($arr1) ;
		unset($arr2) ;
		
		$j = 0 ;
		for ($i = 0 ; $i < count($_detail) ; $i ++) {
			if ($_detail[$i]['cInterest'] > 0) {
				$detail[$j++] = $_detail[$i] ;
			}
		}
		unset($_detail) ;		
		
		$max = count($detail) ;
		
		
		
		break ;

	case "3":	 //回饋扣繳表
		//取得月份
		$f_month = '01';
		$e_month = '12';
		if($feedback_month) {
			$f_month = $e_month = str_pad($feedback_month,2,'0',STR_PAD_LEFT) ;
		}

		//取得年份
		$tmp = explode('-', $feedback_year ) ;
		$f_date = $tmp[0].'-'.$f_month.'-01 00:00:00' ;
		$e_date = $tmp[0].'-'.$e_month.'-31 23:59:59' ;
		unset($tmp) ;


		$sql_str = '' ;
		// 取得保證號碼
		if ($sn) {
			$sql_str .= ' AND bCertifiedId = "'.$sn.'" ' ;
		}

		// 取得姓名資料
		if ($tax_name) {
			$sql_str .= ' AND account.fBankAccountName LIKE "%'.$tax_name.'%" ' ;
		}

		// 取得身分證字號
		if ($tax_id) {
			$sql_str .= ' AND account.fIdentityIdNumber = "'.$tax_id.'" ' ;
		}

		//撈出時間範圍內的出款地政士回饋金
		$sql = '
			SELECT 
				bCertifiedId as cCertifiedId,
				account.fBankAccountName,
				account.fIdentityIdNumber,
				relay.bMoney,
				paycase.fTax,
				paycase.fNHI,
				account.fZipR,
				(SELECT zCity FROM tZipArea WHERE zZip=account.fZipR) cRegistCity,
			    (SELECT zArea FROM tZipArea WHERE zZip=account.fZipR) cRegistArea,
			    account.fAddrR,
			    account.fZipC,
				(SELECT zCity FROM tZipArea WHERE zZip=account.fZipC) cBaseCity,
			    (SELECT zArea FROM tZipArea WHERE zZip=account.fZipC) cBaseArea,
			    account.fAddrC,
				(SELECT bBank4_name FROM tBank WHERE bBank3 = account.fBankMain AND bBank4 = "") as MainBank,
				(SELECT bBank4_name FROM tBank WHERE bBank3 = account.fBankMain AND bBank4 = account.fBankBranch) as BranchBank,
				account.fBankAccount,
				relay.bExport_time,
				(SELECT cCaseFeedback FROM tContractCase WHERE cCertifiedId = relay.bCertifiedId) AS cCaseFeedback,
				(SELECT cCaseFeedback1 FROM tContractCase WHERE cCertifiedId = relay.bCertifiedId) AS cCaseFeedback1,
				(SELECT cCaseFeedback2 FROM tContractCase WHERE cCertifiedId = relay.bCertifiedId) AS cCaseFeedback2,
				(SELECT cCaseFeedback3 FROM tContractCase WHERE cCertifiedId = relay.bCertifiedId) AS cCaseFeedback3,
				(SELECT cFeedbackTarget FROM tContractCase WHERE cCertifiedId = relay.bCertifiedId) AS cFeedbackTarget,
				(SELECT cFeedbackTarget1 FROM tContractCase WHERE cCertifiedId = relay.bCertifiedId) AS cFeedbackTarget1,
				(SELECT cFeedbackTarget2 FROM tContractCase WHERE cCertifiedId = relay.bCertifiedId) AS cFeedbackTarget2,
				(SELECT cFeedbackTarget3 FROM tContractCase WHERE cCertifiedId = relay.bCertifiedId) AS cFeedbackTarget3,
				(SELECT cCaseFeedBackMoney FROM tContractCase WHERE cCertifiedId = relay.bCertifiedId) AS cCaseFeedBackMoney,
				(SELECT cCaseFeedBackMoney1 FROM tContractCase WHERE cCertifiedId = relay.bCertifiedId) AS cCaseFeedBackMoney1,
				(SELECT cCaseFeedBackMoney2 FROM tContractCase WHERE cCertifiedId = relay.bCertifiedId) AS cCaseFeedBackMoney2,
				(SELECT cCaseFeedBackMoney3 FROM tContractCase WHERE cCertifiedId = relay.bCertifiedId) AS cCaseFeedBackMoney3,
				(SELECT cSpCaseFeedBackMoney FROM tContractCase WHERE cCertifiedId = relay.bCertifiedId) AS cSpCaseFeedBackMoney
			FROM 
				tBankTransRelay as relay
			LEFT JOIN 
				tFeedBackMoneyPayByCase AS paycase
			  ON (relay.bCertifiedId = paycase.fCertifiedId AND paycase.fTarget = "S")
			LEFT JOIN 
				tFeedBackMoneyPayByCaseAccount AS account 
			  ON (relay.bCertifiedId = account.fCertifiedId AND account.fTarget = "S" AND paycase.fId = account.fPayByCaseId)
			WHERE
				bPayOk = "1"
				AND bExport_time>="'.$f_date.'" 
				AND bExport_time<="'.$e_date.'" 
				AND bKind = "地政士回饋金"
				'.$sql_str.'
			ORDER BY
				bExport_time
			ASC;
		' ;

		// 取得年度範圍內的保證號碼資料
		$list = $conn->all($sql);

		foreach ($list as $key => $value) {

			$detail[$key]['sn'] = $value['cCertifiedId'];
			$detail[$key]['cName'] = $value['fBankAccountName'];
			$detail[$key]['obj'] = '地政士';
			$detail[$key]['cIdentifyId'] = $value['fIdentityIdNumber'];

			$detail[$key]['scrivenerFeedBack'] = 0;
			//回饋金對象(1:仲介、2:代書)    &&    是否回饋(0:回饋/1:不回饋)
			if($value['cFeedbackTarget'] == 2 and $value['cCaseFeedback'] == 0) {
				$detail[$key]['scrivenerFeedBack'] = $detail[$key]['scrivenerFeedBack'] + $value['cCaseFeedBackMoney'];
			}
			if($value['cFeedbackTarget1'] == 2 and $value['cCaseFeedback1'] == 0) {
				$detail[$key]['scrivenerFeedBack'] = $detail[$key]['scrivenerFeedBack'] + $value['cCaseFeedBackMoney1'];
			}
			if($value['cFeedbackTarget2'] == 2 and $value['cCaseFeedback2'] == 0) {
				$detail[$key]['scrivenerFeedBack'] = $detail[$key]['scrivenerFeedBack'] + $value['cCaseFeedBackMoney2'];
			}
			if($value['cFeedbackTarget3'] == 2 and $value['cCaseFeedback3'] == 0) {
				$detail[$key]['scrivenerFeedBack'] = $detail[$key]['scrivenerFeedBack'] + $value['cCaseFeedBackMoney3'];
			}
			//特殊回饋
			if($value['cSpCaseFeedBackMoney'] > 0) {
				$detail[$key]['scrivenerFeedBack'] = $detail[$key]['scrivenerFeedBack'] + $value['cSpCaseFeedBackMoney'];
			}
			//其他回饋對象 地政
			$sql = 'SELECT * FROM tFeedBackMoney WHERE fCertifiedId ="'. $value['cCertifiedId'] . '" AND fDelete = 0 AND fType = 1';
			$rs = $conn->one($sql);
			if(!empty($rs)) {
				$detail[$key]['scrivenerFeedBack'] = $detail[$key]['scrivenerFeedBack'] + $rs['fMoney'];
			}
			$tax = $detail[$key]['scrivenerFeedBack'] > 20000 ? round($detail[$key]['scrivenerFeedBack'] * 0.1) : 0;

			$detail[$key]['cInterest'] = $detail[$key]['scrivenerFeedBack'];
			$detail[$key]['beforeReviseFeedBack'] = $value['bMoney'] + $value['fTax'] + $value['fNHI'];
			$detail[$key]['cTax'] = $tax;
			$detail[$key]['cRegistZip'] = $value['fZipR'];
			$detail[$key]['cRegistCity'] = $value['cRegistCity'];
			$detail[$key]['cRegistArea'] = $value['cRegistArea'];
			$detail[$key]['cRegistAddr'] = $value['fAddrR'];
			$detail[$key]['cBaseZip'] = $value['fZipC'];
			$detail[$key]['cBaseCity'] = $value['cBaseCity'];
			$detail[$key]['cBaseArea'] = $value['cBaseArea'];
			$detail[$key]['cBaseAddr'] = $value['fAddrC'];
			$detail[$key]['MainBank'] = $value['MainBank'];
			$detail[$key]['BranchBank'] = $value['BranchBank'];
			$detail[$key]['BankAccNo'] = $value['fBankAccount'];
			$detail[$key]['BankAccName'] = $value['fBankAccountName'];
			$detail[$key]['tBankLoansDate'] = $value['bExport_time'];
			$detail[$key]['cCountryCode'] = '';

		}
		$max = count($detail) ;

		unset($arr) ;

		break;
	default:
		break ;
}


$rel = $list = $_detail = $arr2 = $arr3 = $_arr = $tmp = $buyer = $owner = $realty = $scr = $conn = $conBank = $sNo = null;
unset($rel, $list, $_detail, $arr2, $arr3, $_arr, $tmp, $buyer, $owner, $realty, $scr, $conn, $conBank, $sNo);

//產生Excel表
$objPHPExcel = new PHPExcel();
//Set properties 設置文件屬性
$objPHPExcel->getProperties()->setCreator("第一建經");
$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
$objPHPExcel->getProperties()->setTitle("第一建經");
$objPHPExcel->getProperties()->setSubject("查詢明細");
$objPHPExcel->getProperties()->setDescription("第一建經扣繳憑單清單明細");

//指定目前工作頁
$objPHPExcel->setActiveSheetIndex(0);

//調整欄位寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(28);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(50);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(50);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(22);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(22);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);

$cell_no = 1 ;

//清單標題列填色
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no.':K'.$cell_no)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no.':K'.$cell_no)->getFill()->getStartColor()->setARGB('00CCCCCC');

//寫入清單標題列資料
$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,'店編號(保證號碼)') ;
$objPHPExcel->getActiveSheet()->setCellValue('B'.$cell_no,'姓名') ;
$objPHPExcel->getActiveSheet()->setCellValue('C'.$cell_no,'統一編號(身份證字號)') ;
$objPHPExcel->getActiveSheet()->setCellValue('D'.$cell_no,'所得金額') ;
$objPHPExcel->getActiveSheet()->setCellValue('E'.$cell_no,'代扣稅額') ;
$objPHPExcel->getActiveSheet()->setCellValue('F'.$cell_no,'郵遞區號') ;
$objPHPExcel->getActiveSheet()->setCellValue('G'.$cell_no,'戶籍地址') ;
$objPHPExcel->getActiveSheet()->setCellValue('H'.$cell_no,'郵遞區號') ;
$objPHPExcel->getActiveSheet()->setCellValue('I'.$cell_no,'通訊地址') ;
$objPHPExcel->getActiveSheet()->setCellValue('J'.$cell_no,'銀行') ;
$objPHPExcel->getActiveSheet()->setCellValue('K'.$cell_no,'分行') ;
$objPHPExcel->getActiveSheet()->setCellValue('L'.$cell_no,'帳號') ;
$objPHPExcel->getActiveSheet()->setCellValue('M'.$cell_no,'戶名') ;
$objPHPExcel->getActiveSheet()->setCellValue('N'.$cell_no,'結案日期') ;
if($identity != '3') {
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$cell_no,'國籍代碼') ;
	$objPHPExcel->getActiveSheet()->setCellValue('P'.$cell_no,'是否滿183天') ;
}

//設定標題列置中
$objPHPExcel->getActiveSheet()->getStyle('A'.$cell_no.":M".$cell_no)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$cell_no += 1 ;	//愈填寫查詢結果起始的儲存格位置
//寫入查詢結果
for ($i = 0 ; $i < $max ; $i ++) {
	//調整地址顯示
	$tmp = $detail[$i]['cRegistCity'] ;
	$detail[$i]['cRegistAddr'] = preg_replace("/$tmp/","",$detail[$i]['cRegistAddr']) ;
	$tmp = $detail[$i]['cRegistArea'] ;
	$detail[$i]['cRegistAddr'] = preg_replace("/$tmp/","",$detail[$i]['cRegistAddr']) ;
	$detail[$i]['cRegistAddr'] = $detail[$i]['cRegistCity'].$detail[$i]['cRegistArea'].$detail[$i]['cRegistAddr'] ;
	
	$tmp = $detail[$i]['cBaseCity'] ;
	$detail[$i]['cBaseAddr'] = preg_replace("/$tmp/","",$detail[$i]['cBaseAddr']) ;
	$tmp = $detail[$i]['cBaseArea'] ;
	$detail[$i]['cBaseAddr'] = preg_replace("/$tmp/","",$detail[$i]['cBaseAddr']) ;
	$detail[$i]['cBaseAddr'] = $detail[$i]['cBaseCity'].$detail[$i]['cBaseArea'].$detail[$i]['cBaseAddr'] ;
	
	//設定字體大小
	$objPHPExcel->getActiveSheet()->getStyle('A'.($i+$cell_no).':M'.($i+$cell_no))->getFont()->setSize(9);
	$objPHPExcel->getActiveSheet()->getStyle('L'.($i+$cell_no))->getFont()->setSize(11);
	
	if ($identity == '1') {
		//計算所得稅
		$detail[$i]['cTax'] = 0 ;
		if ($detail[$i]['cIdentifyId']) {
			$detail[$i]['cTax'] = payTax($detail[$i]['cIdentifyId'],$detail[$i]['cInterest']) ;
		}
		##
		
		//計算補充保費
		$detail[$i]['NHITax'] = 0 ;
		if ($detail[$i]['cIdentifyId']) {
			$detail[$i]['NHITax'] = payNHITax($detail[$i]['cIdentifyId'],$detail[$i]['cNHITax'],$detail[$i]['cInterest']) ;
		}
		##
	}
	
	//寫入資料
	$objPHPExcel->getActiveSheet()->getCell('A'.($i+$cell_no))->setValueExplicit($detail[$i]['sn'], PHPExcel_Cell_DataType::TYPE_STRING);	//設定文字格式
	
	if ($detail[$i]['obj']) { 
		$detail[$i]['obj'] = '/'.$detail[$i]['obj'] ;
	}
	
	$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+$cell_no),$detail[$i]['cName']);
	$objPHPExcel->getActiveSheet()->setCellValue('C'.($i+$cell_no),($detail[$i]['cIdentifyId'].' '));
	$objPHPExcel->getActiveSheet()->setCellValue('D'.($i+$cell_no),$detail[$i]['cInterest']);
	$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+$cell_no),$detail[$i]['cTax']);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.($i+$cell_no),$detail[$i]['cRegistZip']);
	$objPHPExcel->getActiveSheet()->setCellValue('G'.($i+$cell_no),$detail[$i]['cRegistAddr']);
	$objPHPExcel->getActiveSheet()->setCellValue('H'.($i+$cell_no),$detail[$i]['cBaseZip']);
	$objPHPExcel->getActiveSheet()->setCellValue('I'.($i+$cell_no),$detail[$i]['cBaseAddr']);
	$objPHPExcel->getActiveSheet()->setCellValue('J'.($i+$cell_no),$detail[$i]['MainBank']);
	$objPHPExcel->getActiveSheet()->setCellValue('K'.($i+$cell_no),$detail[$i]['BranchBank']);
	$objPHPExcel->getActiveSheet()->getCell('L'.($i+$cell_no))->setValueExplicit($detail[$i]['BankAccNo'], PHPExcel_Cell_DataType::TYPE_STRING);	//設定文字格式
	$objPHPExcel->getActiveSheet()->setCellValue('M'.($i+$cell_no),$detail[$i]['BankAccName']);
	$objPHPExcel->getActiveSheet()->setCellValue('N'.($i+$cell_no),$detail[$i]['tBankLoansDate']);
	$objPHPExcel->getActiveSheet()->setCellValue('O'.($i+$cell_no),$detail[$i]['cCountryCode']);
	
	
	if ($detail[$i]['cResidentLimit'] == '1') { $detail[$i]['cResidentLimit'] = '是' ; }
	else { $detail[$i]['cResidentLimit'] = '' ; }
	$objPHPExcel->getActiveSheet()->setCellValue('P'.($i+$cell_no),$detail[$i]['cResidentLimit']);
	
	//$totalMoney += $list[$i]['bRecall'] ;
	
	//設定案件金額千分位符號
	$objPHPExcel->getActiveSheet()->getStyle('D'.($i+$cell_no).':E'.($i+$cell_no))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	
	//設定保證號碼置中
	$objPHPExcel->getActiveSheet()->getStyle('A'.($i+$cell_no).":C".($i+$cell_no))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	//回饋金有修改過 和中繼帳戶不同 加底色
	if($detail[$i]['cInterest'] != $detail[$i]['beforeReviseFeedBack']) {
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i+$cell_no).':N'.($i+$cell_no))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i+$cell_no).':N'.($i+$cell_no))->getFill()->getStartColor()->setARGB('00e4beb1');
	}
		
}

//Rename sheet 重命名工作表標籤
$objPHPExcel->getActiveSheet()->setTitle('扣繳憑單清單');
############################

//Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

if ($identity=='1') {
	$_file = 'InterestTaxReceipt.xlsx' ;
}else if($identity == '2') {
	$_file = 'FeedbackTaxReceipt.xlsx' ;
}else if($identity == '3') {
	$_file = 'FeedbackWithholdingTax.xlsx' ;
}


// die;

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

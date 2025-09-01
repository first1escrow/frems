<?php
set_time_limit(3000);
include_once dirname(__DIR__) . '/configs/config.class.php' ;
include_once dirname(__DIR__) . '/class/SmartyMain.class.php' ;
include_once dirname(__DIR__) . '/session_check.php' ;
require_once dirname(__DIR__) . '/first1DB.php';

if ($_SESSION['member_job'] != '1') header('Location: http://' . $GLOBALS['DOMAIN']) ; 

$conn = new first1DB();

//增加利息對項手動新增對象撈取(2015-08-11)
Function getOthers($cCertifiedId, $tb, $tBankLoansDate='') {
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
	for ($i = 0 ; $i < count($rel) ; $i ++) {
		$tmp = $rel[$i];
		
		$tmp['cRegistCity'] = zipConv($conn, $tmp['cRegistZip'],'zCity') ;

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

//計算實際金額歸屬
Function money_belong($_c = 0 ,$_b = 0) {
	if (($_c > 0)&&($_b <= 0)) {
		$_interest = $_c ;
	}
	else if (($_c <= 0)&&($_b > 0)) {
		$_interest = $_b ;
	}
	else if (($_c > 0)&&($_b > 0)) {
		$_interest = $_c + $_b ;
	}
	return $_interest ;
}
##

Function payTax($_id,$_int=0) {
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

//取得仲介基本資料
Function getBranch(&$conn, $sn, $no=0, $m=0) {
	$_sql = '
		SELECT
			bName as cName,
			bSerialnum as Serialnum1,
			bIdentityNumber as Serialnum2,
			(SELECT zCity FROM tZipArea WHERE zZip=a.bZip) as cRegistCity
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
			// $_POST['identity'] = 1 ; 	$_POST['feedback_year'] = '2015';  $_POST['feedback_month'] = '08';
$identity = trim(addslashes($_POST['identity'])) ;
$feedback_year = trim(addslashes($_POST['feedback_year'])) ;
$feedback_month = trim(addslashes($_POST['feedback_month'])) ;
$sn = trim(addslashes($_POST['sn'])) ;
$tax_name = trim(addslashes($_POST['tax_name'])) ;
$tax_id = trim(addslashes($_POST['tax_id'])) ;

$total_page = trim(addslashes($_POST['total_page'])) + 1 - 1 ;
$current_page = trim(addslashes($_POST['current_page'])) + 1 - 1 ;
$record_limit = trim(addslashes($_POST['record_limit'])) + 1 - 1 ;

if (!$record_limit) $record_limit = 10 ;

$functions = '' ;
$total = 0 ;

$buyer1 = $buyer ;
$owner1 = $owner ;
$scrivener1 = $scrivener ;
$branch1 = $branch ;

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
##

// 依據身份別決定搜尋資料表
switch ($identity) {
	case "1":	// 買賣方利息
		$arr3 = array() ;
		
		// 取得年度
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
		
		// 取得特定保證號碼(注意因為下了 DISTINCT 指令，所以不能再增加其他資料欄位)
		if ($sn) $ssn = ' AND cCertifiedId="'.$sn.'" ' ;
		if ($sn) $sn = ' AND tMemo = "'.$sn.'" ' ;

		
		//取得年度內無履保但有出利息之案件
		$tmp = explode(' ',$f_date) ;
		$ff_date = $tmp[0] ;
		$tmp = explode(' ',$e_date) ;
		$ee_date = $tmp[0] ;
		unset($tmp) ;
			
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
				cFeedbackDate, cCertifiedId
			ASC ;
		' ;
		
		$rel = $conn->all($sql);
		$list = $rel;
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

			//取得合約書買方資料
			$sql = '
				SELECT 
					*,
					cCertifiedId as sn,
					cBankAccNumber as cBankAccNum,
					cInterestMoney as cInterest,
					(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankKey2 AND bBank4="") as BankMain,
					(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankKey2 AND bBank4=a.cBankBranch2) as BankBranch,
					(SELECT zCity FROM tZipArea WHERE zZip=a.cRegistZip) as cRegistCity
				FROM 
					tContractBuyer AS a
				WHERE 
					cCertifiedId="'.$list[$i]['cCertifiedId'].'" 
					AND cInterestMoney<>"0";
				' ;
			
			$rel = $conn->one($sql);
			if (!empty($rel)) {
				$arr3[] = $rel;
			}
			##
			
			//增加利息對象 (2015-08-11)
			$_arr = array() ;
			$_arr = getOthers($list[$i]['cCertifiedId'], 'tContractBuyer') ;
			$arr3 = array_merge($arr3,$_arr) ;
			unset($_arr) ;
			##
			
			//取得其他買方資料
			$sql = '
				SELECT 
					*,
					cCertifiedId as sn,
					cInterestMoney as cInterest,
					(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4="") as BankMain,
					(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4=a.cBankBranch) as BankBranch,
					(SELECT zCity FROM tZipArea WHERE zZip=a.cRegistZip) as cRegistCity
				FROM 
					tContractOthers AS a
				WHERE 
					cCertifiedId="'.$list[$i]['cCertifiedId'].'" 
					AND cInterestMoney<>"0"
					AND cIdentity="1" ;
				' ;
			$rel = $conn->all($sql);
			if (!empty($rel)) {
				for ($j = 0 ; $j < count($rel) ; $j ++) {
					$arr3[] = $rel[$j];
				}
			}
			##
			
			//增加利息對象 (2015-08-11)
			$_arr = array() ;
			$_arr = getOthers($list[$i]['cCertifiedId'], 'tContractOthersB') ;
			$arr3 = array_merge($arr3,$_arr) ;
			unset($_arr) ;
			##
			
			//取得合約書賣方資料
			$sql = '
				SELECT 
					*,
					cCertifiedId as sn,
					cBankAccNumber as cBankAccNum,
					cInterestMoney as cInterest,
					(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankKey2 AND bBank4="") as BankMain,
					(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankKey2 AND bBank4=a.cBankBranch2) as BankBranch,
					(SELECT zCity FROM tZipArea WHERE zZip=a.cRegistZip) as cRegistCity
				FROM 
					tContractOwner AS a
				WHERE 
					cCertifiedId="'.$list[$i]['cCertifiedId'].'" 
					AND cInterestMoney<>"0";
				' ;
			$rel = $conn->one($sql);
			if (!empty($rel)) {
				$arr3[] = $rel;
			}
			##
			
			//增加利息對象 (2015-08-11)
			$_arr = array() ;
			$_arr = getOthers($list[$i]['cCertifiedId'], 'tContractOwner') ;
			$arr3 = array_merge($arr3,$_arr) ;
			unset($_arr) ;
			##
			
			//取得其他賣方資料
			$sql = '
				SELECT 
					*,
					cCertifiedId as sn,
					cInterestMoney as cInterest,
					(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4="") as BankMain,
					(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4=a.cBankBranch) as BankBranch,
					(SELECT zCity FROM tZipArea WHERE zZip=a.cRegistZip) as cRegistCity
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
					$arr3[] = $v;
				}
			}
			##
			
			//增加利息對象 (2015-08-11)
			$_arr = array() ;
			$_arr = getOthers($list[$i]['cCertifiedId'], 'tContractOthersO') ;
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
			$tmp = $conn->one($sql);
			if (!empty($tmp)) {
				//第一家仲介
				if ($tmp['cInterestMoney'] != '0') {
					$arr3[] = getBranch($conn, $list[$i]['cCertifiedId'], $tmp['cBranchNum'], $tmp['cInterestMoney']) ;
				}
				##

				//增加利息對象 (2015-08-11)
				$_arr = array() ;
				$_arr = getOthers($list[$i]['cCertifiedId'], 'tContractRealestate') ;
				$arr3 = array_merge($arr3,$_arr) ;
				unset($_arr) ;
				##
				
				//第二家仲介
				if ($tmp['cInterestMoney1'] != '0') {
					$arr3[] = getBranch($conn, $list[$i]['cCertifiedId'], $tmp['cBranchNum1'], $tmp['cInterestMoney1']) ;
					
					//增加利息對象 (2015-08-11)
				}
				##

				$_arr = array() ;
				$_arr = getOthers($list[$i]['cCertifiedId'], 'tContractRealestate1') ;
				$arr3 = array_merge($arr3,$_arr) ;
				unset($_arr) ;
				##
				
				//第三家仲介
				if ($tmp['cInterestMoney2'] != '0') {
					$arr3[] = getBranch($conn, $list[$i]['cCertifiedId'], $tmp['cBranchNum2'], $tmp['cInterestMoney2']) ;
				}
				##

				//增加利息對象 (2015-08-11)
				$_arr = array() ;
				$_arr = getOthers($list[$i]['cCertifiedId'], 'tContractRealestate2') ;
				$arr3 = array_merge($arr3,$_arr) ;
				unset($_arr) ;
				##
				
				unset($tmp) ;
			}
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
						(SELECT zCity FROM tZipArea WHERE zZip=a.sZip1) as cRegistCity
					FROM
						tScrivener AS a
					WHERE
						sId="'.$tmp['cScrivener'].'"
				' ;
				
				$scr[0] = $conn->one($sql);
				$scr[0]['sn'] = $list[$i]['cCertifiedId'] ;
				$scr[0]['cInterest'] = $tmp['cInterest'] ;
				
				$arr3[] = $scr[0] ;
			}			
			##
			
			//增加利息對象 (2015-08-11)
			$_arr = array() ;
			$_arr = getOthers($list[$i]['cCertifiedId'], 'tContractScrivener') ;
			$arr3 = array_merge($arr3,$_arr) ;
			unset($_arr) ;
			
			##
		}
		//$owner = array_merge($buyer,$owner) ;
		//$realty = array_merge($owner,$realty) ;
		//$arr3 = array_merge($realty,$scr) ;
		
		unset($list) ;
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
		
		$detail = array_merge($arr) ;
		$max = count($detail) ;
		unset($arr) ;
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
		$arr1 = array() ;
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
			bra.bZip2 cRegistZip,
			(SELECT zCity FROM tZipArea WHERE zZip=bra.bZip2) cRegistCity,
			(SELECT zArea FROM tZipArea WHERE zZip=bra.bZip2) cRegistArea,
			bra.bAddr2 cRegistAddr
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
				bra.bZip2 cRegistZip,
				(SELECT zCity FROM tZipArea WHERE zZip=bra.bZip2) cRegistCity,
				(SELECT zArea FROM tZipArea WHERE zZip=bra.bZip2) cRegistArea,
				bra.bAddr2 cRegistAddr
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
		
		$rel = $conn->all($sql);

		$max = count($arr1) ;
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
				$arr2[$j++] = $tmp ; 
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
    case "3":	// 回饋扣繳表

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

        if ($sn) {
            $sql_str = ' AND bCertifiedId = "'.$sn.'" ' ;
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
				bMoney,
				paycase.fTax,
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
			LEFT JOIN 
				tFeedBackData AS feedback
			  ON (feedback.fId = account.fBankId)
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
        $list = $conn->all($sql);
        ##

        foreach ($list as $key => $value) {

            $detail[$key]['sn'] = $value['cCertifiedId'];
            $detail[$key]['cName'] = $value['fBankAccountName'];
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
            $detail[$key]['cTax'] = $tax;
            $detail[$key]['cRegistCity'] = $value['cRegistCity'];
        }
        $max = count($detail) ;

        break ;
	default:
		break ;
}

$tbl = '' ;

# 計算總頁數
if (($max % $record_limit) == 0) {
	$total_page = $max / $record_limit ;
}
else {
	$total_page = floor($max / $record_limit) + 1 ;
}
##

# 設定目前頁數顯示範圍
if ($current_page) {
	if ($current_page >= ($max / $record_limit)) {
		if ($max % $record_limit == 0) {
			$current_page = floor($max / $record_limit) ;
		}
		else {
			$current_page = floor($max / $record_limit) + 1 ;
		}
	}
	$i_end = $current_page * $record_limit ;
	$i_begin = $i_end - $record_limit ;
	if ($i_end > $max) {
		$i_end = $max ;
	}
	if($i_end > $max) { $i_end = $max ; }
}
else {
	$i_end = $record_limit ;
	if($i_end > $max) { $i_end = $max ; }
	$i_begin = 0 ;
	$current_page = 1 ;
}

$j = 1 ; 
$tbl = '' ;

if ($max > 0) {	
	for ($i = $i_begin ; $i < $i_end ; $i ++) {
		if ($i % 2 == 0) { $color_index = "#FFFFFF" ; }
		else { $color_index = "#F8ECE9" ; }
		
		if ($detail[$i]['cRegistCity']) { $detail[$i]['cRegistCity'] .= '...' ; }

        $money = $detail[$i]['cInterest'] ? number_format($detail[$i]['cInterest']) : number_format($detail[$i]['scrivenerFeedBack']);

        //計算所得稅
        if($identity != 3) {
            $detail[$i]['cTax'] = 0 ;
            if ($detail[$i]['cIdentifyId']) {
                $detail[$i]['cTax'] = payTax($detail[$i]['cIdentifyId'], $money) ;
            }
        }
		##
		
		//計算補充保費
		$detail[$i]['NHITax'] = 0 ;
		if ($detail[$i]['cIdentifyId']) {
			$detail[$i]['NHITax'] = payNHITax($detail[$i]['cIdentifyId'], $detail[$i]['cNHITax'], $money) ;
		}
		##
		$tbl .= '
			<tr style="background-color:'.$color_index.';">
				<td style="border:1px solid #ccc;">'.$detail[$i]['sn'].'&nbsp;</td>
				<td style="border:1px solid #ccc;">'.$detail[$i]['cName'].'&nbsp;</td>
				<td style="border:1px solid #ccc;">'.$detail[$i]['cIdentifyId'].'&nbsp;</td>
				<td style="border:1px solid #ccc;text-align:right;">'. $money .'</td>
				<td style="border:1px solid #ccc;text-align:right;">'.number_format($detail[$i]['cTax']).'</td>
				<td style="border:1px solid #ccc;">'.$detail[$i]['cRegistCity'].'&nbsp;</td>
			</tr>
		' ;
	}
}
else {
	$tbl .= '	
		<tr style="text-align:center;background-color:#FFFFFF">
			<td colspan="6" style="height:20px;text-align:left;border:1px solid #ccc;"><span style="font-size:9pt;color:red;">目前尚無任何資料！</span></td>
		</tr>
	' ;

}
	

if ($record_limit==10) { $records_limit .= '<option value="10" selected="selected">10</option>'."\n" ; }
else { $records_limit .= '<option value="10">10</option>'."\n" ; }
if ($record_limit==50) { $records_limit .= '<option value="50" selected="selected">50</option>'."\n" ; }
else { $records_limit .= '<option value="50">50</option>'."\n" ; }
if ($record_limit==100) { $records_limit .= '<option value="100" selected="selected">100</option>'."\n" ; }
else { $records_limit .= '<option value="100">100</option>'."\n" ; }
if ($record_limit==150) { $records_limit .= '<option value="150" selected="selected">150</option>'."\n" ; }
else { $records_limit .= '<option value="150">150</option>'."\n" ; }
if ($record_limit==200) { $records_limit .= '<option value="200" selected="selected">200</option>'."\n" ; }
else { $records_limit .= '<option value="200">200</option>'."\n" ; }

include('../closedb.php') ;

$functions = "

" ;


if ($max==0) {
	$i_begin = 0 ;
	$i_end = 0 ;
}
else {
	$i_begin += 1 ;
}

# 頁面資料
$smarty->assign('i_begin',$i_begin) ;
$smarty->assign('i_end',$i_end) ;
$smarty->assign('current_page',$current_page) ;
$smarty->assign('total_page',$total_page) ;
$smarty->assign('record_limit',$records_limit) ;
$smarty->assign('max',$max) ;
$smarty->assign('pntA4',$pntA4) ;

# 搜尋資訊
$smarty->assign('identity',$identity) ;
$smarty->assign('feedback_year',$feedback_year) ;
$smarty->assign('feedback_month',$feedback_month) ;
$smarty->assign('sn',$sn) ;
$smarty->assign('tax_name',$tax_name) ;
$smarty->assign('tax_id',$tax_id) ;

# 搜尋結果
$smarty->assign('tbl',$tbl) ;

# 其他
//$smarty->assign('functions',$functions) ;
$smarty->assign('show_hide',$show_hide) ;

$smarty->display('taxreceipt_result.inc.tpl', '', 'report');
?>
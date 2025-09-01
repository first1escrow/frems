<?php
#顯示錯誤

include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/intolog.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../tracelog.php' ;
// include_once 'getBranchType.php';
include_once 'includes/maintain/feedBackData.php';
include_once '../report/getBranchType.php';
$tlog = new TraceLog() ;

##
// $_POST = escapeStr($_POST) ;
$functions = '' ;
// print_r($_POST);

$xls = $_POST['xls'] ;

$row = $_POST['row'];
$col = $_POST['col'];
$time = $_POST['time'];

$year_s = $_POST['year_s'];
$month_s = $_POST['month_s'];
$season_s = $_POST['season_s'];

$year_e = $_POST['year_e'];
$month_e = $_POST['month_e'];
$season_e = $_POST['season_e'];


$bank = $_POST['bank'] ;
$status = $_POST['status'] ;
$brand = $_POST['brand'] ;
$realestate = $_POST['realestate'] ;
$branch = $_POST['branch'] ;
$zip = $_POST['zip'] ;
$manager = $_POST['manager'];
$group = $_POST['group'];
$scrivener = $_POST['scrivener'] ;
$sales = $_POST['sales'] ;
$undertaker = $_POST['undertaker'];
// echo $scrivener;
$citys = $_POST['city'];
$area = $_POST['area'];
$city_t = $_POST['city_t'];

if ($manager=='0') {$manager='';}
if ($sales=='0') {$sales='';}
if ($group=='0') {$group='';}
if ($undertaker=='0') {$undertaker='';}


$total_page = $_POST['total_page'] + 1 - 1 ;
$current_page = $_POST['current_page'] + 1 - 1 ;
$record_limit = $_POST['record_limit'] + 1 - 1 ;

if (!$record_limit) { $record_limit = 10 ; }



$query = ' cas.cCertifiedId<>"" AND cas.cCertifiedId !="005030342"' ;
// // 搜尋條件-案件狀態
// if ($status) {
// 	if ($query) { $query .= " AND " ; }
// 	$query .= ' cas.cCaseStatus="'.$status.'" ' ;
// }else {
// 	if ($query) { $query .= " AND " ; }
// 	$query .= ' cas.cCaseStatus<>"8" ' ;
// }
###############################表格上方時間#########################################
##
$y1 = $year_s+1911;//年
$y2 = $year_e+1911;//年
// echo $time;

if ($time=='y') {

	$m1 = 1;
	$m2 = 12;

	for ($i=$y1; $i <= $y2; $i++) { 
		$col_date[$i] = ($i-1911);
	}

}elseif ($time=='s') {

	if ($season_s) {
		if ($season_s =='S1') {
			$m1 = "01";
			$ss = 1;
		}elseif ($season_s =='S2') {
			$m1 = "04";
			$ss = 2;
		}elseif ($season_s =='S3') {
			$m1 = "07";
			$ss = 3;
		}elseif ($season_s =='S4') {
			$m1 = "10";
			$ss = 4;
		}
	}

	if ($season_e) {
		if ($season_e =='S1') {
			$m2 = "03";
			$se=1;
		}elseif ($season_e =='S2') {
			$m2 = "06";
			$se=2;
		}elseif ($season_e =='S3') {
			$m2 = "09";
			$se=3;
		}elseif ($season_e =='S4') {
			$m2 = "12";
			$se=4;
		}
	}

	for ($i=$y1; $i <=$y2 ; $i++) { 
		for ($j=$ss; $j <=$se ; $j++) { 

			
			$col_date[$i."-s".$j] = ($i-1911)."-s".$j;
			unset($tmp);
		}
	}

}elseif ($time =='m') {
	
	if ($month_s) { $m1 = $month_s;}
	if ($month_e) { $m2 = $month_e;}	

	##col
	for ($i=$y1; $i <=$y2 ; $i++) { 

		for ($j=1; $j <= 12; $j++) { 

			if ($j<10) {
				$tmp = '0'.$j;
			}else{
				$tmp =$j;
			}

			if (($i==$y1 && $j >=$m1 ) || ($i<=$y2 && $i!=$y1 && $j<=$m2 )) {
				$col_date[$i."-".$tmp] = ($i-1911)."-".$tmp;
			
			}
			
			unset($tmp);
			if ($i==$y2 && $j ==$m2) {
				break;
			}

		}
		# code...
	}
	##
	
}

$date_start = $y1."-".$m1.'-01'; //開始時間
$date_end = $y2."-".$m2.'-'.date('t',$y2."-".$m2);//結束時間
// echo $date_start.";".$date_end;
// die;
##########################時間查詢條件######################################
if ($col=='in') {

	if ($query) { $query .= " AND " ; }
		$query .= ' cas.cApplyDate>="'.$date_start.' 00:00:00" ' ;
		if ($query) { $query .= " AND " ; }
		$query .= ' cas.cApplyDate<="'.$date_end.' 23:59:59" ' ;


}elseif ($col == 'check') {
	if ($query) { $query .= " AND " ; }
		$query .= ' cas.cEndDate>="'.$date_start.' 00:00:00" ' ;

		if ($query) { $query .= " AND " ; }
		$query .= ' cas.cEndDate<="'.$date_end.' 23:59:59" ' ;

}elseif ($col=='sign') {
	
		if ($query) { $query .= " AND " ; }
		$query .= ' cas.cSignDate>="'.$date_start.' 00:00:00" ' ;
		if ($query) { $query .= " AND " ; }
		$query .= ' cas.cSignDate<="'.$date_end.' 23:59:59" ' ;

}
	unset($tmp) ;

##########################查詢條件######################################
if ($bank) {
	
	$query .= ' AND cas.cBank = "'.$bank.'"';
}

// 搜尋條件-案件狀態
if ($status) {

	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cCaseStatus="'.$status.'" ' ;
}else {
	if ($query) { $query .= " AND " ; }
	$query .= ' cas.cCaseStatus<>"8" ' ;
}



// 搜尋條件-仲介店
if ($branch) {
	if ($query) { $query .= " AND " ; }
	$query .= ' (rea.cBranchNum="'.$branch.'" OR rea.cBranchNum1="'.$branch.'" OR rea.cBranchNum2="'.$branch.'") ' ;
}

// 搜尋條件-地政士
if ($scrivener) {
	if ($query) { $query .= " AND " ; }
	$query .= ' csc.cScrivener="'.$scrivener.'" ' ;
}


// 搜尋條件-仲介品牌
if (($brand != '') && ($realestate != '11') && ($realestate != '12') && ($realestate != '13') && ($realestate != '14')) {
	if ($query) { $query .= " AND " ; }
	$query .= ' (rea.cBrand="'.$brand.'" OR rea.cBrand1="'.$brand.'" OR rea.cBrand2="'.$brand.'") ' ;
}


// 搜尋條件-地區
if ($zip && $city_t==1 ) {
	if ($query) { $query .= " AND " ; }
	$query .= ' pro.cZip="'.$zip.'" ' ;
}else if ($citys && $city_t==1) {

	$zipArr = array() ;
	$zipStr = '' ;

	$sql = 'SELECT zZip FROM tZipArea WHERE zCity="'.$citys.'" ORDER BY zCity,zZip ASC;' ;
	// echo $sql;
	// die;
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$zipArr[] = "'".$rs->fields['zZip']."'" ;
		$rs->MoveNext();
	}

	
	$zipStr = implode(',',$zipArr) ;
	if ($query) { $query .= " AND " ; }
	$query .= ' pro.cZip IN ('.$zipStr.') ' ;
	unset($zipArr) ;
	unset($zipStr) ;
}elseif ($city_t==2) {
 	if (($zip || $citys) && $city_t==2) {
		if ($zip) {
			$str_a .=" zZip='".$zip."'";
		}elseif ($citys) {
			$str_a .= " zCity='".$citys."'";
		}else
		{
			$str_a = "1=1";
		}

		$sql = "SELECT * FROM tZipArea WHERE ".$str_a."  ORDER BY zZip ASC";

		$rs = $conn->Execute($sql);
		
		while (!$rs->EOF) {
			
			$tmp[] = "'".$rs->fields['zZip']."'";

			$rs->MoveNext();
		}

		$tmp_zip = implode(',', $tmp);
	

		if ($query) { $query .= " AND " ; }
		$query .= "pro.cZip IN(".$tmp_zip.")";

		unset($tmp_zip);unset($tmp);
	}
	##

}
##



if ($status=='3') {
	$t_day = '結案日期' ;
}else {
	$t_day = '簽約日期' ;
}

if ($manager) {

	$str =" AND bManager='".$manager."'";
	$sql = "SELECT bId FROM tBranch  WHERE bId !=0  ".$str." ORDER BY bId ASC";

	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		
		$tmp_b[] =  "'".$rs->fields['bId']."'";

		$rs->MoveNext();
	}

	$str = implode(',', $tmp_b);
	if ($query) { $query .= " AND " ; }
	$query .= ' (rea.cBranchNum IN ('.$str.') OR rea.cBranchNum1 IN ('.$str.') OR rea.cBranchNum2 IN ('.$str.'))';

	unset($str);
	unset($tmp_b);
}

//群組
if ($group) {
		
	$str = "WHERE bGroup ='".$group."' AND bStatus =1";

	$sql = "SELECT * FROM tBranch ".$str."  ORDER BY bId ASC";

	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		
		// $row_title[$rs->fields['bId']] = $rs->fields['bName'];
		$tmp_b[] = "'".$rs->fields['bId']."'";
		


		$rs->MoveNext();
	}

	// if ($_SESSION['member_id'] == 6) {
	// 	echo "<pre>";
	// 	print_r($tmp_b);
	// }

	$str = implode(',', $tmp_b);
	if ($query) { $query .= " AND " ; }
	$query .= ' (rea.cBranchNum IN ('.$str.') OR rea.cBranchNum1 IN ('.$str.') OR rea.cBranchNum2 IN ('.$str.'))';
	unset($str);
	unset($tmp_b);
}


##########################左側欄位######################################
// $leftColum
if ($row=='bank') {

	##查詢條件##
	if ($bank) {
		$str = ' AND cBankCode="'.$bank.'"'; //leftColum
	}
	###########

	$sql ="SELECT cBankCode,cBankName,cBranchName FROM tContractBank WHERE cShow=1 ".$str."";
	// echo $sql;
	$rs = $conn->Execute($sql);
	$max = $rs->RecordCount();
	while (!$rs->EOF) {
		$branchName = '';

		$leftColum[$rs->fields['cBankCode']]['key'] = $rs->fields['cBankCode'];
		if ($rs->fields['cBankName'] =='永豐') {
			$branchName = $rs->fields['cBranchName'];
		}
		$leftColum[$rs->fields['cBankCode']]['name'] = $rs->fields['cBankName'].$branchName;

		$checkColum[]= $rs->fields['cBankCode'];
		// $i++;
		$rs->MoveNext();
	}

}elseif ($row == 'status') {

	##查詢條件##
	if ($status) {
		$str .= 'WHERE sId="'.$status.'"';
	}
	###########

	$sql ="SELECT sId,sName FROM tStatusCase ".$str;
	// echo $sql;
	$rs = $conn->Execute($sql);
	$max = $rs->RecordCount();

	while (!$rs->EOF) {
		
		$leftColum[$rs->fields['sId']]['key'] = $rs->fields['sId'];
		$leftColum[$rs->fields['sId']]['name'] = $rs->fields['sName'];

		$checkColum[]= $rs->fields['sId'];

		$i++;

		$rs->MoveNext();
	}
}elseif ($row == 'brand') {

	##查詢條件##
	$str = "1=1 ";

	if ($brand && ($realestate != 12 && $realestate != 13 && $realestate != 14)) {
		if ($str) {$str .= " AND";}
		$str .= ' bId = "'.$brand.'"';
	}

	if ($realestate) {

		if ($str) {$str .= " AND";}

		switch ($realestate) {
			case '11': //加盟(其他品牌)
				$str .=" bId !='1' AND bId !='49'";
				break;
			case '12': //加盟(台灣房屋)
				$str .=" bId ='1'";
				break;
			case '13': //加盟(優美地產)
				$str .=" bId ='49'";
				break;
			case '14': //加盟(永春不動產)
				$str .=" bId ='56'";
				break;
		
		}
	}
	#############
	
	$sql ="SELECT bId,bName FROM tBrand WHERE ".$str;
	
	$rs = $conn->Execute($sql);
	$max = $rs->RecordCount();
	while (!$rs->EOF) {
		
		$leftColum[$rs->fields['bId']]['key'] = $rs->fields['bId'];
		$leftColum[$rs->fields['bId']]['name'] = $rs->fields['bName'];
		$checkColum[]= $rs->fields['bId'];
		$i++;

		$rs->MoveNext();
	}
}elseif ($row == 'branch_type') {
	$i = 0;
	if ($realestate == '' || $realestate == 11) {
		$leftColum[11]['key'] = 11;
		$leftColum[11]['name'] = '加盟(其他品牌)';
		$checkColum[]= 11;
		$i++;
	}
	
	if ($realestate == '' || $realestate == 12 || $brand==1) {
		$leftColum[12]['key'] = 12;
		$leftColum[12]['name'] = '加盟(台灣房屋)';
		$checkColum[]= 12;
		$i++;
	}

	if ($realestate == '' || $realestate == 13 || $brand==49) {
		$leftColum[13]['key'] = 13;
		$leftColum[13]['name'] = '加盟(優美地產)';
		$checkColum[]= 13;
		$i++;
	}

	if ($realestate == '' || $realestate == 14 || $brand==56) {
		$leftColum[14]['key'] = 14;
		$leftColum[14]['name'] = '加盟(永春不動產)';
		$checkColum[]= 14;
		$i++;
	}

	if ($realestate == '' || $realestate == 1) {
		$leftColum[1]['key'] = 1;
		$leftColum[1]['name'] = '加盟';
		$checkColum[]= 1;
		$i++;
	}

	if ($realestate == '' || $realestate == 2 || $brand==1) {
		$leftColum[2]['key'] = 2;
		$leftColum[2]['name'] = '直營';
		$checkColum[]= 2;
		$i++;
	}

	if ($realestate == '' || $realestate == 3) {
		$leftColum[3]['key'] = 3;
		$leftColum[3]['name'] = '非仲介成交';
		$checkColum[]= 3;
		$i++;
	}
	
	if ($realestate == '' || $realestate == 4) {
		$leftColum[4]['key'] = 4;
		$leftColum[4]['name'] = '其他(未指定)';
		$checkColum[]= 4;
		$i++;
	}
	$max = $i;
	unset($i);
	// print_r($leftColum);

}elseif ($row=='branch') {

	##查詢條件##
	if ($branch) {
		$str = ' AND b.bId = "'.$branch.'"';
	}

	if ($brand && $realestate !='13' && $realestate !='12' && $realestate !='14') {
		$str .=" AND b.bBrand = '".$brand."'";
	}

	if ($manager) {
		$str .= ' AND b.bManager = "'.$manager.'"';
	}

	if ($group) {
		
		$str .= " AND b.bGroup = '".$group."'";
	}

	if ($realestate) {
		switch ($realestate) {
			case '11': //加盟(其他品牌)
				$str .=" AND b.bBrand != '1' AND b.bBrand != '49'";
				break;
			case '12': //加盟(台灣房屋)
				$str .=" AND b.bBrand = '1' AND bCategory = '1'";
				break;
			case '13': //加盟(優美地產)
				$str .=" AND b.bBrand = '49'";
				break;
			case '14': //加盟(永春不動產)
				$str .=" AND b.bBrand = '56'";
				break;
			case '1': //加盟
				$str .=" AND b.bCategory = '1'";
				break;
			case '2': //直營
				$str .=" AND b.bCategory = '2'";
				break;
			case '3': //非仲介成交
				$str .=" AND b.bCategory = '3'";
				break;
		
		}
	}
	###########
	##地區
	if (($zip || $citys) && $city_t==2) {
		$str_a  ='';
		if ($zip) {
			$str_a .=" zZip='".$zip."'";
		}elseif ($citys) {
			$str_a .= " zCity='".$citys."'";
		}else
		{
			$str_a = "1=1";
		}

		$sql = "SELECT * FROM tZipArea WHERE ".$str_a."  ORDER BY zZip ASC";
		

		$rs = $conn->Execute($sql);
		
		while (!$rs->EOF) {
			
			$tmp[] = "'".$rs->fields['zZip']."'";

			$rs->MoveNext();
		}

		$tmp_zip = implode(',', $tmp);
		$str .=" AND bZip IN(".$tmp_zip.")";

		unset($tmp_zip);unset($tmp);
	}
	##


	$sql = "SELECT 
				b.bId,
				b.bStore,
				(SELECT bName FROM tBrand AS a WHERE a.bId=b.bBrand) AS brand ,
				CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ),LPAD(b.bId,5,'0')) as bCode
			FROM 
				tBranch AS b 
			WHERE 
				bId != 0 ".$str." ORDER BY bId ";


	$rs = $conn->Execute($sql);
	$max = $rs->RecordCount();
	while (!$rs->EOF) {
		
		// $row_title[$rs->fields['bId']] = $rs->fields['brand'].$rs->fields['bStore'];

		$leftColum[$rs->fields['bId']]['key'] = $rs->fields['bId'];
		$leftColum[$rs->fields['bId']]['name'] = "(".$rs->fields['bCode'].")".$rs->fields['brand'].$rs->fields['bStore'];
		$checkColum[]= $rs->fields['bId'];
		$i++;

		$rs->MoveNext();
	}
}elseif ($row == 'area') {
	$str = "1=1";
	if ($zip) {
		$str =" zZip='".$zip."'";
	}elseif ($citys) {
		$str = " zCity='".$citys."'";
	}else{
		$str2 = "GROUP BY zCity";
	}


	$sql = "SELECT * FROM tZipArea WHERE ".$str." ".$str2." ORDER BY zZip ASC";
	// echo $sql."<br>";
	// die;
	$rs =$conn->Execute($sql);
	$max = $rs->RecordCount();
	while (!$rs->EOF) {
		
		// $row_title[$rs->fields['zZip']] = $rs->fields['zCity'].$rs->fields['zArea'];
		
		
		if ($zip) {
			$leftColum[$rs->fields['zZip']]['key'] = $rs->fields['zZip'];			
			$leftColum[$rs->fields['zZip']]['name'] = $rs->fields['zCity'].$rs->fields['zArea'];
			$checkColum[]= $rs->fields['zZip'];
		}else if ($citys) {
			$leftColum[$rs->fields['zCity'].$rs->fields['zArea']]['key'] = $rs->fields['zCity'].$rs->fields['zArea'];
			$leftColum[$rs->fields['zCity'].$rs->fields['zArea']]['name'] = $rs->fields['zCity'].$rs->fields['zArea'];
			$checkColum[]= $rs->fields['zCity'].$rs->fields['zArea'];
		}else{
			$leftColum[$rs->fields['zCity']]['key'] = $rs->fields['zCity'];
			$leftColum[$rs->fields['zCity']]['name'] = $rs->fields['zCity'];
			$checkColum[]= $rs->fields['zCity'];
		}
		

		

		$rs->MoveNext();
	}
}elseif ($row == 'manager') {
	

	$sql = "SELECT bManager FROM tBranch  WHERE 1=1 ".$str." GROUP BY bManager  ORDER BY bId ASC";

	// echo $sql;

	$rs = $conn->Execute($sql);
	$max = $rs->RecordCount();
	while (!$rs->EOF) {
		
		// $row_title[$rs->fields['bManager']] = $rs->fields['bManager'];
		$leftColum[$rs->fields['bManager']]['key'] = $rs->fields['bManager'];
		$leftColum[$rs->fields['bManager']]['name'] = $rs->fields['bManager'];
		$checkColum[]= $rs->fields['bManager'];
	
		$i++;

		$rs->MoveNext();
	}
}elseif ($row == 'group') {

	$leftColum[0]['key'] = 0;
	$leftColum[0]['name'] = '未知群組';

	$sql = "SELECT * FROM tBranchGroup ".$str." ORDER BY bId ASC";

	$rs = $conn->Execute($sql);
	$max = $rs->RecordCount();
	while (!$rs->EOF) {
		
		$leftColum[$rs->fields['bId']]['key'] = $rs->fields['bId'];
		$leftColum[$rs->fields['bId']]['name'] = $rs->fields['bName'];
		$checkColum[]= $rs->fields['bId'];

		$i++;

		$rs->MoveNext();
	}
}elseif ($row == 'scrivener') {

	if ($scrivener) {
		$str .= " AND sId = '".$scrivener."'";
	}

	##地區
	$str_a = '';
	if (($zip || $citys) && $city_t==2) {
		if ($zip) {
			$str_a .=" zZip='".$zip."'";
		}elseif ($citys) {
			$str_a .= " zCity='".$citys."'";
		}else
		{
			$str_a = "1=1";
		}

		$sql = "SELECT * FROM tZipArea WHERE ".$str_a."  ORDER BY zZip ASC";

		$rs = $conn->Execute($sql);
		
		while (!$rs->EOF) {
			
			$tmp[] = "'".$rs->fields['zZip']."'";

			$rs->MoveNext();
		}

		$tmp_zip = implode(',', $tmp);
		$str .=" AND sCpZip1 IN(".$tmp_zip.")";

		unset($tmp_zip);unset($tmp);
	}
	##

	$sql = "SELECT sId,sName FROM tScrivener WHERE sStatus='1' ".$str." ORDER BY sId ASC";

	$rs = $conn->Execute($sql);
	$max = $rs->RecordCount();
	while (!$rs->EOF) {
		
		$leftColum[$rs->fields['sId']]['key'] = $rs->fields['sId'];
		$leftColum[$rs->fields['sId']]['name'] = $rs->fields['sName'];
		$checkColum[]= $rs->fields['sId'];

		$i++;

		$rs->MoveNext();
	}
}elseif ($row == 'sales') {
	$sql = "SELECT pId,pName FROM tPeopleInfo WHERE pDep IN('4','7') AND pJob=1 ".$str;

	$rs = $conn->Execute($sql);
	$max = $rs->RecordCount();
	while (!$rs->EOF) {
		
		// $row_title[$rs->fields['pId']] = $rs->fields['pName'];
		$leftColum[$rs->fields['pId']]['key'] = $rs->fields['pId'];
		$leftColum[$rs->fields['pId']]['name'] = $rs->fields['pName'];
		$checkColum[]= $rs->fields['pId'];
		
		$i++;

		$rs->MoveNext();
	}
}elseif ($row == 'undertaker') {
	$sql = "SELECT pId,pName FROM tPeopleInfo WHERE pDep IN('5','6') AND pJob=1 ".$str;

	$rs = $conn->Execute($sql);
	$max = $rs->RecordCount();
	while (!$rs->EOF) {
		
		// $row_title[$rs->fields['pId']] = $rs->fields['pName'];

		$leftColum[$rs->fields['pId']]['key'] = $rs->fields['pId'];
		$leftColum[$rs->fields['pId']]['name'] = $rs->fields['pName'];
		$checkColum[]= $rs->fields['pId'];
		$i++;

		$rs->MoveNext();
	}	
}
###############




$sql ='
	SELECT
		cas.cCertifiedId,
		cas.cBank,
		cas.cCaseStatus,
		cas.cApplyDate,
		cas.cEndDate,
		cas.cSignDate,
		cas.cCaseFeedBackMoney,
		cas.cCaseFeedBackMoney1,
		cas.cCaseFeedBackMoney2,
		cas.cSpCaseFeedBackMoney,
		cas.cCaseFeedback,
		cas.cCaseFeedback1,
		cas.cCaseFeedback2,
		cas.cFeedbackTarget,
		cas.cFeedbackTarget1,
		cas.cFeedbackTarget2,
		scr.sUndertaker1 as undertaker,
		rea.cBrand AS brand,
		rea.cBrand1 AS brand1,
		rea.cBrand2 AS brand2,
		rea.cBranchNum AS branch,
		rea.cBranchNum1 AS branch1,
		rea.cBranchNum2 AS branch2,
		(SELECT sName FROM tStatusCase WHERE sId=cas.cCaseStatus) AS statusName,
		(SELECT bName FROM tBrand WHERE bId = rea.cBrand) AS brandName,
		(SELECT bName FROM tBrand WHERE bId = rea.cBrand1) AS brandName1,
		(SELECT bName FROM tBrand WHERE bId = rea.cBrand2) AS brandName2,
		(SELECT bGroup FROM tBranch WHERE bId = rea.cBranchNum) AS bGroup,
		(SELECT bGroup FROM tBranch WHERE bId = rea.cBranchNum1) AS bGroup1,
		(SELECT bGroup FROM tBranch WHERE bId = rea.cBranchNum2) AS bGroup2,
		(SELECT bManager FROM tBranch WHERE bId = rea.cBranchNum) AS bManager,
		(SELECT bManager FROM tBranch WHERE bId = rea.cBranchNum1) AS bManager1,
		(SELECT bManager FROM tBranch WHERE bId = rea.cBranchNum2) AS bManager2,
		(SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum) AS bStore,
		(SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum1) AS bStore1,
		(SELECT bStore FROM tBranch WHERE bId = rea.cBranchNum2) AS bStore2,
		CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand ),LPAD(rea.cBranchNum,5,"0")) as bCode,
		CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand ),LPAD(rea.cBranchNum,5,"0")) as bCode1,
		CONCAT((Select bCode From `tBrand` c Where c.bId = rea.cBrand ),LPAD(rea.cBranchNum,5,"0")) as bCode2,
		(SELECT sName FROM tScrivener WHERE sId =csc.cScrivener) AS scrivenername,
		inc.cCertifiedMoney,
		csc.cScrivener,
		pro.cZip AS zip,
		pro.cAddr AS cAddr,
		zip.zCity AS city,
		zip.zArea AS area,
		buy.cName AS buyer,
		own.cName AS owner,
		inc.cTotalMoney
	FROM 
		tContractCase AS cas 
	LEFT JOIN 
		tContractBuyer AS buy ON buy.cCertifiedId=cas.cCertifiedId 
	LEFT JOIN 
		tContractOwner AS own ON own.cCertifiedId=cas.cCertifiedId 
	LEFT JOIN 
		tContractRealestate AS rea ON rea.cCertifyId=cas.cCertifiedId 
	LEFT JOIN 
		tContractScrivener AS csc ON csc.cCertifiedId=cas.cCertifiedId 
	LEFT JOIN 
		tContractProperty AS pro ON pro.cCertifiedId=cas.cCertifiedId 
	LEFT JOIN 
		tContractIncome AS inc ON inc.cCertifiedId=cas.cCertifiedId 
	LEFT JOIN
		tZipArea AS zip ON zip.zZip=pro.cZip
	LEFT JOIN tScrivener AS scr ON scr.sId = csc.cScrivener
	WHERE
	'.$query.' 
	GROUP BY
		cas.cCertifiedId
	ORDER BY 
		cas.cApplyDate,cas.cId,cas.cSignDate ASC;
' ;



// echo "<br>";
$rs = $conn->Execute($sql);

function branch_type3($branch,$brand,$cat=''){
	global $conn;
	// echo $branch."_".$brand;
	$cat = checkCat($conn,$branch,$brand) ; 
	
	if ($cat == '加盟其他品牌') {
		$val = 11;
	}elseif ($cat == '加盟台灣房屋') {
		$val = 12;
	}elseif ($cat == '加盟優美地產') {
		$val = 13;
	}elseif ($cat == '加盟永春不動產') {
		$val = 14;
	}elseif (preg_match("/^加盟/",$cat1)) {
		$val = 1;
	}elseif ($cat == '直營') {
		$val = 2;
	}elseif ($cat == '非仲介成交') {
		$val = 3;
	}else{
		$val = 4;
	}



	return $val;
}


$i = 0;
while (!$rs->EOF) {




	$data[$i] = $rs->fields;


	if ($data[$i]['brand'] > 0) {
		$tmp[] = $data[$i]['brandName'];
		$tmp2[] = $data[$i]['bStore'];
		$tmp3[] = $data[$i]['bCode'];
	}

	if ($data[$i]['brand1'] > 0) {
		$tmp[] = $data[$i]['brandName1'];
		$tmp2[] = $data[$i]['bStore1'];
		$tmp3[] = $data[$i]['bCode1'];
	}

	if ($data[$i]['brand1'] > 0) {
		$tmp[] = $data[$i]['brandName2'];
		$tmp2[] = $data[$i]['bStore2'];
		$tmp3[] = $data[$i]['bCode2'];
	}

	if (count($tmp) > 1) {
		$data[$i]['brandNameS'] = '*';
		$data[$i]['bStoreS'] = '*';
	}

	$data[$i]['newNrandName'] = @implode(',', $tmp);
	$data[$i]['newbStore'] = @implode(',',$tmp2);
	$data[$i]['newCode'] = @implode(',', $tmp3);
	unset($tmp);unset($tmp2);unset($tmp3);

	$data[$i]['cSignDate'] = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$data[$i]['cSignDate'])) ;
	$tmp = explode('-',$data[$i]['cSignDate']) ;
				
	if (preg_match("/0000/",$tmp[0])) { $tmp[0] = '000' ; }
	else { $tmp[0] -= 1911 ; }

	$data[$i]['cSignDate'] = $tmp[0]."-".$tmp[1]."-".$tmp[2];
	unset($tmp);

	$data[$i]['cApplyDate'] = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$data[$i]['cApplyDate'])) ;
	$tmp = explode('-',$data[$i]['cApplyDate']) ;
				
	if (preg_match("/0000/",$tmp[0])) { $tmp[0] = '000' ; }
	else { $tmp[0] -= 1911 ; }

	$data[$i]['cApplyDate'] = $tmp[0]."-".$tmp[1]."-".$tmp[2];
	unset($tmp);

	$data[$i]['cEndDate'] = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$data[$i]['cEndDate'])) ;
	$tmp = explode('-',$data[$i]['cEndDate']) ;
				
	if (preg_match("/0000/",$tmp[0])) { $tmp[0] = '000' ; }
	else { $tmp[0] -= 1911 ; }

	$data[$i]['cEndDate'] = $tmp[0]."-".$tmp[1]."-".$tmp[2];
	unset($tmp);
	// echo $data[$i]['cCertifiedId']."<br>";
	$i++;

	$rs->MoveNext();
}

// echo "<pre>";
// 	print_r($data);
// 	echo "</pre>";
// 	die;

// if ($_SESSION['member_id'] == 6) {
// 	// echo "<pre>";
// 	// print_r($data);
// 	// echo "</pre>";
// 	// die;
// 	echo $sql;
// }


//無法從SQL下條件的
if ($realestate) {	

	$list = array() ;
	$j = 0 ;
	for ($i = 0 ; $i < count($data) ; $i ++) {
		
		$type = branch_type($conn,$data[$i]);
		

		if ($realestate == '11' && $type == 'O') {
			//$cat = '加盟其他品牌' ;
			$list[$j++] = $data[$i] ;
		}
		else if ($realestate == '12' && $type == 'T') {
			//$cat = '加盟台灣房屋' ;
				$list[$j++] = $data[$i] ;
	
		}
		else if ($realestate == '13' && $type == 'U') {
			//$cat = '加盟優美地產' ;
			$list[$j++] = $data[$i] ;
		}
		else if ($realestate == '14' && $type == 'F') {
			//$cat = '加盟永春不動產' ;
			$list[$j++] = $data[$i] ;
		}
		else if ($realestate == '3' && $type == '3') {
			//$cat = '非仲介成交' ;
			$list[$j++] = $data[$i] ;
		}
		else if ($realestate == '1' && ($type == 'O' || $type == 'T' || $type == 'U' || $type == 'F')) {
			//$cat = '所有加盟(其他品牌、台灣房屋、優美地產、永春不動產)' ;
			
			$list[$j++] = $data[$i] ;
		}
		else if ($realestate == '2' && $type == '2') {
			//$cat = '直營' ;
			//$list[$j++] = $data[$i] ;
			$list[$j++] = $data[$i] ;
		}
		
		else if ($realestate == '4' && $type == 'N' ) {
			$list[$j++] = $data[$i] ;
		}
	}
	unset($data) ;
	$data = array() ;
	
	$data = array_merge($list) ;

	unset($list);
}



// echo $col;
for ($i=0; $i < count($data); $i++) { 
	##時間
	if ($col=='in') {
			// $data[$i]['cApplyDate'] = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$data[$i]['cApplyDate'])) ;
			$tmp = explode('-',$data[$i]['cApplyDate']) ;
			
			// if (preg_match("/0000/",$tmp[0])) { $tmp[0] = '000' ; }
			// else { $tmp[0] -= 1911 ; }

			if ($time=='y') {
				$range = $tmp[0];
			}elseif ($time=='s') {

				if ($tmp[1] >=1 && $tmp[1] <=3) {
					$range =$tmp[0]."-s1";
				}elseif ($tmp[1] >=4 && $tmp[1] <=6) {
					$range =$tmp[0]."-s2";
				}elseif ($tmp[1] >=7 && $tmp[1] <=9) {
					$range =$tmp[0]."-s3";
				}elseif ($tmp[1] >=10 && $tmp[1] <=12) {
					$range =$tmp[0]."-s";
				}
				# code...
			}elseif ($time=='m') {
				$range = $tmp[0].'-'.$tmp[1] ;
			}
	}elseif ($col == 'check') {

			// $data[$i]['cEndDate'] = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$data[$i]['cEndDate'])) ;
			$tmp = explode('-',$data[$i]['cEndDate']) ;
			
			// if (preg_match("/0000/",$tmp[0])) { $tmp[0] = '000' ; }
			// else { $tmp[0] -= 1911 ; }

			if ($time=='y') {
				$range = $tmp[0];
			}elseif ($time=='s') {

				if ($tmp[1] >=1 && $tmp[1] <=3) {
					$range =$tmp[0]."-s1";
				}elseif ($tmp[1] >=4 && $tmp[1] <=6) {
					$range =$tmp[0]."-s2";
				}elseif ($tmp[1] >=7 && $tmp[1] <=9) {
					$range =$tmp[0]."-s3";
				}elseif ($tmp[1] >=10 && $tmp[1] <=12) {
					$range =$tmp[0]."-s4";
				}
				# code...
			}elseif ($time=='m') {
				$range = $tmp[0].'-'.$tmp[1] ;
			}
		
	}elseif ($col=='sign') {

		// $data[$i]['cSignDate'] = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$data[$i]['cSignDate'])) ;
			$tmp = explode('-',$data[$i]['cSignDate']) ;
			
		// 	if (preg_match("/0000/",$tmp[0])) { $tmp[0] = '000' ; }
		// 	else { $tmp[0] -= 1911 ; }

			if ($time=='y') {
				$range = $tmp[0];
			}elseif ($time=='s') {

				if ($tmp[1] >=1 && $tmp[1] <=3) {
					$range =$tmp[0]."-s1";
				}elseif ($tmp[1] >=4 && $tmp[1] <=6) {
					$range =$tmp[0]."-s2";
				}elseif ($tmp[1] >=7 && $tmp[1] <=9) {
					$range =$tmp[0]."-s3";
				}elseif ($tmp[1] >=10 && $tmp[1] <=12) {
					$range =$tmp[0]."-s4";
				}
				# code...
			}elseif ($time=='m') {
				$range = $tmp[0].'-'.$tmp[1] ;
			}

	}
	unset($tmp) ;
	
	$type = branch_type2($conn,$data[$i]);

	if ($row == 'bank') {
		$key = $data[$i]['cBank'];
	}elseif ($row == 'status') {
		$key = $data[$i]['cCaseStatus'];
	}elseif ($row == 'brand'){
		
		$key = $type['brand'];
		$key2 = 2;//品牌非仲介成交代碼
		$key_b = $data[$i]['brand'];
		$key_b1 = $data[$i]['brand1'];
		$key_b2 = $data[$i]['brand2'];
		
		$sp = 1;
		// $sp = 2;
	}elseif ($row == 'branch_type') {
		
		$key = $type['type'];	
		$key2 = 3;//品牌非仲介成交代碼
		$key_b = branch_type3($data[$i]['branch'],$data[$i]['brand']);
		if ($data[$i]['branch1'] > 0) {
			$key_b1 = branch_type3($data[$i]['branch1'],$data[$i]['brand1']);
		}
		if ($data[$i]['branch2'] > 0) {
			$key_b2 = branch_type3($data[$i]['branch1'],$data[$i]['brand2']);
		}

		$sp = 1;
		// $sp = 2;
	}elseif ($row == 'branch') {
		
		$key = $type['bid'];
		$key2 = 505;//品牌非仲介成交代碼
		$key_b = $data[$i]['branch'];
		$key_b1 = $data[$i]['branch1'];
		$key_b2 = $data[$i]['branch2'];
		

		$sp = 1;
		// $sp = 2;
	}elseif ($row == 'area') {
		// $key = $data[$i]['cBank'];
		if ($zip) {
			$key =$data[$i]['zip'];
		}elseif ($citys) {
			$key = $data[$i]['city'].$data[$i]['area'];
			// echo $key;
		}else{
			$key = $data[$i]['city'];
		}

	}elseif ($row == 'manager') {
		
		$key = $type['manager'];
		$key2 = 0;//品牌非仲介成交代碼
		$key_b = $data[$i]['bManager'];
		$key_b1 = $data[$i]['bManager1'];
		$key_b2 = $data[$i]['bManager2'];
		

		// $sp = 1;
	}elseif ($row == 'group') {
		
		$key = $type['group'];
		$key2 = 0;//品牌非仲介成交代碼
		$key_b = $data[$i]['bGroup'];
		$key_b1 = $data[$i]['bGroup1'];
		$key_b2 = $data[$i]['bGroup2'];
		
		// $sp = 2;
		// $countsp = 1;
		$sp = 1;
		$countsp = 2;
	}elseif ($row == 'scrivener') {
		
		$key = $data[$i]['cScrivener'];
		$key2 = $data[$i]['cScrivener'];

		$sp = 2;
		// $sp = 1;
	}elseif ($row == 'sales') {
		$key = getBranchSales($data[$i]['branch']);
		$key2 = getScrivenerSales($data[$i]['cScrivener']);
		$key_b = getBranchSales($data[$i]['branch']);
		$key_b1 = getBranchSales($data[$i]['branch1']);
		$key_b2 = getBranchSales($data[$i]['branch2']);

		$countsp = 1; //業務應該是只要有回饋有負責到就算他的
		// $sp = 1;
		$sp = 2;
	}elseif ($row == 'undertaker') {
		

		$key = $data[$i]['undertaker'];
		
	}
	
	##統計##
	if ($key != '' && $countsp == '') {
		if ($branch || $row == 'branch') {

			$ck = checkCount($data[$i]);

			if ($branch) {
				$leftColum[$key]['one'][$range] += $ck['one'];
				$leftColum[$key]['pair'][$range] += $ck['pair'];

				if ($type['bid'] == $branch) {
					$leftColum[$key]['count'][$range]++;//數量
				}else{
					$leftColum[$key]['unpair'][$range]++;
					$showDataUnpair[] = $data[$i];//蒐集案件資訊
				}



			}else{
				

				if ($data[$i]['branch'] > 0) {
					$leftColum[$key_b]['one'][$range] += $ck['one'];
					$leftColum[$key_b]['pair'][$range] += $ck['pair'];
					// $leftColum[$key_b]['count'][$range] ++;
				}

				if ($data[$i]['branch1'] > 0) {
					$leftColum[$key_b1]['one'][$range] += $ck['one'];
					$leftColum[$key_b1]['pair'][$range] += $ck['pair'];
					// $leftColum[$key_b1]['count'][$range] ++;
				}

				if ($data[$i]['branch2'] > 0) {
					$leftColum[$key_b2]['one'][$range] += $ck['one'];
					$leftColum[$key_b2]['pair'][$range] += $ck['pair'];
					// $leftColum[$key_b2]['count'][$range] ++;
				}

				if ($data[$i]['branch'] != $type['bid']) {
					$leftColum[$key_b]['unpair'][$range]++;
					$showDataUnpair[] = $data[$i];//蒐集案件資訊
				}elseif ($data[$i]['branch1'] != $type['bid'] && $data[$i]['branch1'] > 0) {
					$leftColum[$key_b1]['unpair'][$range]++;
					$showDataUnpair[] = $data[$i];//蒐集案件資訊
				}elseif ($data[$i]['branch2'] != $type['bid'] && $data[$i]['branch2'] > 0) {
					$leftColum[$key_b2]['unpair'][$range]++;
					$showDataUnpair[] = $data[$i];//蒐集案件資訊
				}


			}

			if ($ck['one'] == 1) {
				$showDataOne[] = $data[$i];//蒐集案件資訊
			}elseif ($ck['pair'] == 1) {
				$showDataPair[] = $data[$i];//蒐集案件資訊
			}

		}else{
			$leftColum[$key]['count'][$range]++;//數量
		}

		
		// print_r($leftColum);
		$showData[] = $data[$i];
		// echo $key;
	}else if($countsp == 1){
		$tmp = getContractSales($data[$i]['cCertifiedId']);
		
		
		foreach ($tmp as $k => $v) {
			$tmpC = round(1/count($tmp),2);
			$leftColum[$k]['count'][$range]+=$tmpC;//數量

		}

		$showData[] = $data[$i];
		unset($tmp);
	}elseif ($countsp == 2) {
		$tmpCountG = 0;
		if ($data[$i]['brand'] > 0 ) {
			$tmpCountG++;
			$realCount[$key_b]++;
		}

		if ($data[$i]['brand1'] > 0) {
			$tmpCountG++;
			$realCount[$key_b1]++;
		}

		if ($data[$i]['brand2'] > 0) {
			$tmpCountG++;
			$realCount[$key_b2]++;
		}

		
		$tmpC = round($realCount[$key]/$tmpCountG,4);
		$leftColum[$key]['count'][$range]+=$tmpC;//數量
		
		// if ($_SESSION['member_id'] == 6) {
		// 	echo $key."_".$data[$i]['cCertifiedId']."_".$tmpC."<br>";
		// }


		$showData[] = $data[$i];
		unset($tmpC); unset($realCount);
	}


	
	//保證費回饋金
	if ($sp == 1) {
		
		
		if ($data[$i]['brand'] > 0 ) {
			
			if ($data[$i]['cFeedbackTarget'] == 1) {//1:仲介、2:代書
				if ($data[$i]['cCaseFeedback'] == 0) {
					

					$leftColum[$key_b]['caseFeedBackMoney']+=$data[$i]['cCaseFeedBackMoney'];
				}
				$branchCount['B'.$data[$i]['branch']]['cat']=$key_b;//計算保證費用
				// if ($countsp) {
				// 	$leftColum[$key_b]['count'][$range]++;//數量
				// }

			}elseif ($data[$i]['cFeedbackTarget'] == 2){
				if ($data[$i]['cCaseFeedback'] == 0) {
					$leftColum[$key2]['caseFeedBackMoney']+=$data[$i]['cCaseFeedBackMoney'];
					// echo $data[$i]['cCertifiedId']."_".$data[$i]['cCaseFeedBackMoney']."<br>";
				}
				$branchCount['S'.$data[$i]['cScrivener']]['cat']=$key2;//計算保證費用
				// if ($countsp) {
				// 	$leftColum[$key2]['count'][$range]++;//數量
				// }

			}


			
		}

		if ($data[$i]['brand1'] > 0 ) {
			
			if ($data[$i]['cFeedbackTarget1'] == 1) {//1:仲介、2:代書
				if ($data[$i]['cCaseFeedback1'] == 0) {
					$leftColum[$key_b1]['caseFeedBackMoney']+=$data[$i]['cCaseFeedBackMoney1'];
				}
				$branchCount['B'.$data[$i]['branch1']]['cat']=$key_b1;//計算保證費用
				// if ($countsp) {
				// 	$leftColum[$key_b1]['count'][$range]++;//數量
				// }
			}elseif ($data[$i]['cFeedbackTarget1'] == 2){
				if ($data[$i]['cCaseFeedback1'] == 0) {
					$leftColum[$key2]['caseFeedBackMoney']+=$data[$i]['cCaseFeedBackMoney1'];
					// echo $data[$i]['cCertifiedId']."_".$data[$i]['cCaseFeedBackMoney1']."<br>";
				}
				$branchCount['S'.$data[$i]['cScrivener']]['cat']=$key2;//計算保證費用
				// if ($countsp) {
				// 	$leftColum[$key2]['count'][$range]++;//數量
				// }
			}
				
			
		}

		if ($data[$i]['brand2'] > 0 ) {
			
			if ($data[$i]['cFeedbackTarget2'] == 1) {//1:仲介、2:代書
				if ($data[$i]['cCaseFeedback2'] == 0) {
					$leftColum[$key_b2]['caseFeedBackMoney']+=$data[$i]['cCaseFeedBackMoney2'];
				}
				$branchCount['B'.$data[$i]['branch2']]['cat']=$key_b2;//計算保證費用
				// if ($countsp) {
				// 	$leftColum[$key_b2]['count'][$range]++;//數量
				// }
			}elseif ($data[$i]['cFeedbackTarget2'] == 2){
				if ($data[$i]['cCaseFeedback2'] == 0) {
					$leftColum[$key2]['caseFeedBackMoney']+=$data[$i]['cCaseFeedBackMoney2'];
					// echo $data[$i]['cCertifiedId']."_".$data[$i]['cCaseFeedBackMoney2']."<br>";
				}
				$branchCount['S'.$data[$i]['cScrivener']]['cat']=$key2;//計算保證費用
				// if ($countsp) {
				// 	$leftColum[$key2]['count'][$range]++;//數量
				// }
			}
			
		}

		//直接歸給非仲介成交
		if ($data[$i]['cSpCaseFeedBackMoney'] > 0) {
			$leftColum[$key2]['caseFeedBackMoney']+=$data[$i]['cSpCaseFeedBackMoney'];
			// echo $data[$i]['cCertifiedId']."_".$data[$i]['cSpCaseFeedBackMoney']."<br>";
			$branchCount['S'.$data[$i]['cScrivener']]['cat']=$key2;//計算保證費用
			// if ($countsp) {
			// 	$leftColum[$key2]['count'][$range]++;//數量
			// }
		}



		$tmp = getOtherFeed3($data[$i]['cCertifiedId']);
		if (is_array($tmp)) {
			
			foreach ($tmp as $k => $v) {
				
				if ($v['fType'] == 2) { //仲介
					if ($row == 'brand') {
						$leftColum[$v['storeType']]['caseFeedBackMoney']+=$v['fMoney'];
						$branchCount['B'.$v['fStoreId']]['cat']=$v['storeType'];//計算保證費用
					}elseif ($row == 'branch') {
						$leftColum[$v['fStoreId']]['caseFeedBackMoney']+=$v['fMoney'];
						$branchCount['B'.$v['fStoreId']]['cat']=$v['fStoreId'];//計算保證費用
					}
					

				}else{
					$leftColum[$key2]['caseFeedBackMoney']+=$v['fMoney'];
					// echo $data[$i]['cCertifiedId']."_".$v['fMoney']."<br>";
					$branchCount['S'.$v['fStoreId']]['cat']=$key2;//計算保證費用
				}

			}
			
			
		}

		//計算總保證費
		// 保證費 要依回饋對像來看
		// 如果AB店配
		// 1.回饋給A或B 那麼保證費就算給A或B
		// 2.回饋給AB 那麼保證費就除以2各半

		$tmp = getcCertifiedMoney($data[$i]['cCertifiedMoney'],$branchCount);
		
		// echo $data[$i]['cCertifiedId']."_".$data[$i]['cCertifiedMoney']."_".$data[$i]['brand']."_".$data[$i]['cApplyDate'];
		// echo "<pre>";
		// 	print_r($tmp);
		// echo "</pre>";
		// die;
		if (is_array($tmp)) {
			foreach ($tmp as $k => $v) {
				// if ($v['money'] == 5220) {
				// 	echo $data[$i]['cCertifiedId']."<br>";
				// }
				$leftColum[$v['cat']]['certifiedMoney']+=$v['money'];
				// echo $leftColum[$v['cat']]['certifiedMoney'];
			}

		}else{

			// $leftColum[$key]['certifiedMoney']+=$v['money'];
		}
		unset($branchCount);


	}elseif($sp ==2){

		if ($countsp == 1) {

			if (is_array($key)) {
				
				$leftColum[$key[0]]['certifiedMoney'] += $data[$i]['cCertifiedMoney'];//保證費
			}

			if (is_array($key_b)) {

				if ($data[$i]['brand'] > 0 ) {
					if ($data[$i]['cCaseFeedback'] == 0) {
						foreach ($key_b as $k => $v) {

						 	$leftColum[$v]['caseFeedBackMoney']+=$data[$i]['cCaseFeedBackMoney'];
						}
					}
				}
			}

			if (is_array($key_b1)) {
				if ($data[$i]['brand1'] > 0 ) {
					if ($data[$i]['cCaseFeedback1'] == 0) {
						
						foreach ($key_b1 as $k => $v) {

						 	$leftColum[$v]['caseFeedBackMoney']+=$data[$i]['cCaseFeedBackMoney1'];
						}
					}
				}
			}

			

			if ($data[$i]['brand2'] > 0 ) {
				if ($data[$i]['cCaseFeedback2'] == 0) {
					// $leftColum[$key_b2]['caseFeedBackMoney']+=$data[$i]['cCaseFeedBackMoney2'];
					foreach ($key_b2 as $k => $v) {

						 	$leftColum[$v]['caseFeedBackMoney']+=$data[$i]['cCaseFeedBackMoney'];
						}
					
				}
			}
			
			if (is_array($key2)) {
				if ($data[$i]['cSpCaseFeedBackMoney'] > 0) {
					foreach ($key2 as $k => $v) {
						$leftColum[$v]['caseFeedBackMoney']+=$data[$i]['cSpCaseFeedBackMoney'];
					}
				
				}
			}
			
			
			$tmp = getOtherFeedMoney($data[$i]['cCertifiedId']);
			if ($tmp['fMoney'] > 0) {
				if (is_array($key)) {
					$leftColum[$key[0]]['caseFeedBackMoney']+=$tmp['fMoney'];
				}
				
			}
			
		}else{



			$leftColum[$key]['certifiedMoney']+=$data[$i]['cCertifiedMoney'];//保證費

			if ($data[$i]['brand'] > 0 ) {
				if ($data[$i]['cCaseFeedback'] == 0) {
					$leftColum[$key_b]['caseFeedBackMoney']+=$data[$i]['cCaseFeedBackMoney'];
					
				}
			}

			if ($data[$i]['brand1'] > 0 ) {
				if ($data[$i]['cCaseFeedback1'] == 0) {
					$leftColum[$key_b1]['caseFeedBackMoney']+=$data[$i]['cCaseFeedBackMoney1'];
					
				}
			}

			if ($data[$i]['brand2'] > 0 ) {
				if ($data[$i]['cCaseFeedback2'] == 0) {
					$leftColum[$key_b2]['caseFeedBackMoney']+=$data[$i]['cCaseFeedBackMoney2'];
					
				}
			}
			
			if ($data[$i]['cSpCaseFeedBackMoney'] > 0) {
				$leftColum[$data[$i]['cScrivener']]['caseFeedBackMoney']+=$data[$i]['cSpCaseFeedBackMoney'];

			}
			
			$tmp = getOtherFeedMoney($data[$i]['cCertifiedId']);
			if ($tmp['fMoney'] > 0) {
				
				$leftColum[$key]['caseFeedBackMoney']+=$tmp['fMoney'];
			}
		}

		
		
	}else{
		$leftColum[$key]['certifiedMoney']+=$data[$i]['cCertifiedMoney'];//保證費
		if ($data[$i]['brand'] > 0 ) {
			if ($data[$i]['cCaseFeedback'] == 0) {
				$leftColum[$key]['caseFeedBackMoney']+=$data[$i]['cCaseFeedBackMoney'];
			}
		}

		if ($data[$i]['brand1'] > 0 ) {
			if ($data[$i]['cCaseFeedback1'] == 0) {
				$leftColum[$key]['caseFeedBackMoney']+=$data[$i]['cCaseFeedBackMoney1'];
			}
		}

		if ($data[$i]['brand2'] > 0 ) {
			if ($data[$i]['cCaseFeedback2'] == 0) {
				$leftColum[$key]['caseFeedBackMoney']+=$data[$i]['cCaseFeedBackMoney2'];
			}
		}
		
		if ($data[$i]['cSpCaseFeedBackMoney'] > 0) {
			$leftColum[$data[$i]['cScrivener']]['caseFeedBackMoney']+=$data[$i]['cSpCaseFeedBackMoney'];
		}
		
		$tmp = getOtherFeedMoney($data[$i]['cCertifiedId']);
		if ($tmp['fMoney'] > 0) {
			$leftColum[$key]['caseFeedBackMoney']+=$tmp['fMoney'];
		}
		
	}	
	
	
	//

	
	
	unset($key);unset($tmp);unset($type);unset($key2);unset($key_b);unset($key_b1);
	unset($key_b2);
}

// if ($_SESSION['member_id'] == 6) {
// 	echo "<pre>";
// 	print_r($leftColum);
// }

function getBranchSales($id){
	global $conn;

	// $sql = "SELECT * FROM tContractSales WHERE cCertifiedId = '".$cId."'";
	$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = bSales) AS Name,bSales AS sales FROM tBranchSales WHERE bBranch = '".$id."'";
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$sales[] =  $rs->fields['sales'];

		$rs->MoveNext();
	}

	return $sales;
}

function getScrivenerSales($id){
	global $conn;

	// $sql = "SELECT * FROM tContractSales WHERE cCertifiedId = '".$cId."'";
	$sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = sSales) AS Name,sSales AS sales FROM tScrivenerSales WHERE sScrivener = '".$id."'";
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$sales[] =  $rs->fields['sales'];

		$rs->MoveNext();
	}

	return $sales;
}

function getContractSales($id){
	global $conn;

	$sql = "SELECT cSalesId FROM tContractSales WHERE cCertifiedId = '".$id."'";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$sales[$rs->fields['cSalesId']] = $rs->fields['cSalesId'];

		$rs->MoveNext();
	}

	// $score = round(1/count($sales),2);

	return $sales;
}
// echo "<pre>";
// print_r($data);
// echo "</pre>";
// die;



if ($xls == 'ok') {
	include_once 'analysiscase_excel.php';
	die;
}
// $max = count($leftColum);
// echo $max;
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

$i = 0;
$tbl = '';
// $i = $i_begin ;// ; $i ++
// $j = 0;
// if ($_SESSION['member_id'] == 6) {
// 		echo "<pre>";
// 		print_r($leftColum);
// }


foreach ($leftColum as $k => $v) {


	if ($i >= $i_begin && $i <= $i_end) {
			$one = 0; $pair = 0; $unpair = 0;
		if ($k != "0") {
			// in_array(needle, haystack)
			if (in_array($k, $checkColum)) {

				// echo 'GO';
				$tbl .='<tr>';
				$tbl .='<th>'.$v['name'].'</th>';	

				foreach ($col_date as $key => $value) {
					$one += $v['one'][$value];
					$pair += $v['pair'][$value];
					$unpair +=$v['unpair'][$value];
					if ($row == 'branch' || $branch != '') {
						$tmp = $v['one'][$value]+$v['pair'][$value]-$v['unpair'][$value];

						$tbl .='<td>'.number_format($tmp).'</td>';
					}else{

						if ($v['count'][$value] != '') {
							$tbl .='<td>'.$v['count'][$value].'</td>';
						}else{
							$tbl .='<td>0</td>';
						}	
					}
					

					
				
				}
				if ($row == 'branch' || $branch != '') {
						if ($one != '' && $pair != '' ) {
							$tmp = $one+$pair-$unpair;
							$tbl .='<td>'.number_format($tmp).'</td>';
							// $tbl .='<td>'.number_format($tmp).'</td>';
							unset($tmp);
						}else{
							$tbl .='<td>0</td>';
							// $tbl .='<td>0</td>';
						}	

						if ($one != '') {
							$tbl .='<td>'.number_format($one).'</td>';
						}else{
							$tbl .='<td>0</td>';
						}

						if ($pair != '') {
							$tbl .='<td>'.number_format($pair).'</td>';
						}else{
							$tbl .='<td>0</td>';
						}

						if ($unpair != '') {
							$tbl .='<td>'.number_format($unpair).'</td>';
						}else{
							$tbl .='<td>0</td>';
						}
					}
				
				//保證費	
				$tbl .='<td>'.number_format($v['certifiedMoney']).'</td>';
				
				//回饋金			
				$tbl .='<td>'.number_format($v['caseFeedBackMoney']).'</td>';
				
				//收入
				$tbl .='<td>'.number_format(($v['certifiedMoney']-$v['caseFeedBackMoney'])).'</td>';
				$tbl .='</tr>';
			}
			
			
			
		}
		
	}
	$i++;
	# code...
}


##
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


function checkCount($arr)
{
	$tmp['one'] = 0;
	$tmp['pair'] = 0;

	if ($arr['branch'] > 0 && $arr['branch1'] <= 0 ) {
		$tmp['one'] = 1 ;
	}else{
		$tmp['pair'] = 1;
	}

	return $tmp;
}




##
// # 頁面資料
$smarty->assign('i_begin',$i_begin) ;
$smarty->assign('i_end',$i_end) ;
$smarty->assign('current_page',$current_page) ;
$smarty->assign('total_page',$total_page) ;
$smarty->assign('record_limit',$records_limit) ;
$smarty->assign('max',number_format($max)) ;

//搜尋條件
$smarty->assign('row',$row);
$smarty->assign('col',$col);

$smarty->assign('time',$time) ;
$smarty->assign('year_s',$year_s) ;
$smarty->assign('month_s',$month_s) ;
$smarty->assign('season_s',$season_s) ;
$smarty->assign('year_e',$year_e) ;
$smarty->assign('month_e',$month_e) ;
$smarty->assign('season_e',$season_e) ;

$smarty->assign('bank',$bank) ;
$smarty->assign('status',$status) ;
$smarty->assign('brand',$brand) ;
$smarty->assign('realestate',$realestate) ;
$smarty->assign('branch',$branch) ;
$smarty->assign('zip',$zip) ;
$smarty->assign('manager',$manager);
$smarty->assign('group',$group);
$smarty->assign('scrivener',$scrivener) ;
$smarty->assign('sales',$sales) ;
$smarty->assign('undertaker',$undertaker) ;
$smarty->assign('city',$citys);
$smarty->assign('area',$area);
$smarty->assign('city_t',$city_t);



$smarty->assign('col_date',$col_date) ;
$smarty->assign('leftColum',$leftColum) ;
$smarty->assign('tbl',$tbl);
$smarty->assign('showData',$showData);

$smarty->display('analysiscase_result.inc.tpl', '', 'report');
?>
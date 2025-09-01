<?php

include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../includes/first1Sales.php';
include_once '../session_check.php' ;
include_once '../report/getBranchType.php';
include_once '../includes/sales/getSalesInfo.php'; //function 都在這
include_once '../includes/maintain/feedBackData.php';
// include_once 'getSalesInfo.php'; //function 都在這
##
$_POST = escapeStr($_POST) ;
foreach ($menu_sales as $k => $v) {
	if ($k == 3) unset($menu_sales[$k]) ;
}

// $sales = $_SESSION['member_id'];
// $sales = 25;

if (empty($_POST['sales'])) {
	if ($_SESSION['member_id'] && $_SESSION['member_pDep'] == 7) {
		$sales = $_SESSION['member_id'];
	}
}else{
	$sales = $_POST['sales'];
}

//時間下拉
$yr = trim($_POST['sdateYear']) ;
$mn = trim($_POST['sdateMonth']) ;
$yr2 = trim($_POST['edateYear']) ;
$mn2 = trim($_POST['edateMonth']) ;
$ok = trim($_POST['ck']) ;
$trace = trim($_POST['traceXls']) ;



if (!$yr) $yr = date("Y") - 1911 ;
// if (!$mn) $mn = date("m",mktime(0,0,0,(date("m")-1))) ;
if (!$mn) $mn = date("m",mktime(0,0,0,(date("m")))) ;

$grade = 0;
##

//年度顯示
$y = '' ;
for ($i = 0 ; $i < 100 ; $i ++) {
	$patt = $i + 100 ;
	
	// if (($patt == $yr) && ($mn != '12')) { $sl = " selected='selected'" ; echo 'a'; }
	// else if ((($patt+1)==$yr)&&($mn=='12')) { $sl = " selected='selected'" ; echo 'b';}
	if (($patt == $yr) ) { $sl = " selected='selected'" ; }
	else { $sl = '' ; }
	
	$y .= "<option value='".$patt."'".$sl.">".$patt."</option>\n" ;
}

//月份顯示
$m = '' ;
for ($i = 0 ; $i < 12 ; $i ++) {
	$patt = $i + 1 ;
	
	if ($patt==$mn) { $sl = " selected='selected'" ; }
	else { $sl = '' ; }
	
	$m .= "<option value='".$patt."'".$sl.">".$patt."</option>\n" ;
}

##


// echo sprintf("%d",date('Y')).":".$yr;
if (sprintf("%d",date('Y')) > $yr ) {
	$now_check = '1';
}
$now_month = sprintf("%d",date('m')) ;
###########
if ($ok==1) {

	$sql= "SELECT pName FROM tPeopleInfo WHERE pId = '".$sales."'";
	$rs = $conn->Execute($sql);
	$sales_name = $rs->fields['pName'];
	// echo $sales_name;

	$date_start = ($yr + 1911).'-'.$mn.'-01' ;   //今年起始
	$date_end = ($yr2 + 1911).'-'.$mn2.'-31' ;     //今年結束

	$i = 1;
	$sql = "SELECT * FROM tSalesReport WHERE sDate >= '".$date_start."' AND sDate <= '".$date_end."' AND sSales ='".$sales."' ORDER BY sDate ASC";

	$rs = $conn->Execute($sql) ;
    
	while (!$rs->EOF) {
		//簽約數 達成率
		// $summary1[$i]['targetcount'] = $rs->fields['sSignQuantity'];    //簽約數
		//2017-01-01
		$tmp = explode('-', $rs->fields['sDate']);

        $time = $tmp[0].$tmp[1];

	
		$date_start = ($tmp[0]).'-'.str_pad($tmp[1],2,'0',STR_PAD_LEFT).'-01 00:00:00';
		$date_end = ($tmp[0]).'-'.str_pad($tmp[1],2,'0',STR_PAD_LEFT).'-31 23:59:59';
			
		$Branch[$time] = getOwnBranch($sales,$date_start,$date_end) ; //該月新進仲介店數
		$Scrivener[$time] = getOwnScrivener($sales,$date_start,$date_end);//該月新進地政士數
		$BranchCount = count($Branch[$time]);
		$ScrivenerCount = count($Scrivener[$time]);
			
		$tmp_cut = getSameStore1($sales,$Branch[$time],$Scrivener[$time]);
			
			
		$summary1[$time]['targetcount'] = $BranchCount+$ScrivenerCount-$tmp_cut['total'];
		$summary1[$time]['BranchCount'] = $BranchCount-$tmp_cut['branch'];
		$summary1[$time]['ScrivenerCount'] = $ScrivenerCount-$tmp_cut['scrivener'];
		// $summary1[$time]['target'] = getOwnStoreTarget($summary1[$time]['targetcount'],($tmp[0]-1911),$tmp[1]);   //達成率
		unset($tmp_cut);unset($tmp);
		

		
		

		$rs->MoveNext();
	}

	

	include_once 'salesSignReportExcel.php';
	exit;

}

unset($tmp_use);
function getSameStore1($sales,$arr,$arr2){
	global $conn;

	$score['total'] = 0;
	$score['scrivener'] = 0;
	$score['branch'] = 0;

	if (is_array($arr)) {
		foreach ($arr as $k => $v) {
			$sql= "SELECT sStore,COUNT(sStore) AS storeCount FROM tSalesSign WHERE sType = 2 AND sStore = '".$v['bId']."'";
			$rs = $conn->Execute($sql);

			if ($rs->fields['storeCount'] > 1) {
				$score['total'] += Round(1/$rs->fields['storeCount'],2);
				$score['branch'] += Round(1/$rs->fields['storeCount'],2);
			}
			
		}
	}
	
	if (is_array($arr2)) {
		foreach ($arr2 as $k => $v) {
			$sql= "SELECT sStore,COUNT(sStore) AS storeCount FROM tSalesSign WHERE sType = 1 AND sStore = '".$v['sId']."'";
			$rs = $conn->Execute($sql);

			if ($rs->fields['storeCount'] > 1) {
				$score['total'] += Round(1/$rs->fields['storeCount'],2);
				$score['scrivener'] += Round(1/$rs->fields['storeCount'],2);
			}
		}
	}
	
	// echo $score;
	return $score;
}

##
$smarty->assign("y",$y) ;
$smarty->assign("m",$m) ;
$smarty->assign('menu_sales',$menu_sales);
$smarty->assign('sales',$sales);




	$smarty->display('salesSignReport.inc.tpl', '', 'report') ;


?>
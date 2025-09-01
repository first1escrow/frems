<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../web_addr.php' ;
include_once '../session_check.php' ;
include_once '../openadodb.php' ;
include_once '../tracelog.php' ;

//將陣列內容轉為表格
Function fillData($arr) {
	$str = '' ;
	
	foreach ($arr as $k => $v) {
		$colorIndex = '' ;
		if (($k % 2) == 1) $colorIndex = '#99FFFF' ;
		$str .= '
			<tr style="background-color:'.$colorIndex.';">
				<td style="padding:5px;">'.$v['sName'].'</td><td style="padding:5px;">'.$v['sCompanyName'].'</td><td style="padding:5px;">'.$v['sTel'].'</td><td style="padding:5px;">'.$v['sAddress'].'</td>
			</tr>
		' ;
	}
	
	return $str ;
}
function addCount($todayList,$lastdayList){
	$adding = array() ;
	
	// print_r($todayList);
	
	$cnt = 0 ;
	for ($i = 0 ; $i < count($todayList) ; $i ++) {
		$notFound = true ;
		$isName = $todayList[$i]['sName'] ;
		
		for ($j = 0 ; $j < count($lastdayList) ; $j ++) {
			$jsName = $lastdayList[$j]['sName'] ;
			//echo 'is='.$isName.', js='.$jsName."<br>\n" ;
			
			if ($isName == $jsName) {
			//if (preg_match("/^$isName$/isu",$jsName)) {
				$notFound = false ;
				
				//echo "Founded!!<br>\n" ;
				break ;
			}
		}
		
		if ($notFound) {
			$adding[] = $todayList[$i] ;
		}
	}
	
	//$adding = array_diff($todayList, $lastdayList) ;
	$maxAdd = count($adding) ;
	return $maxAdd;
}
function delCount($todayList,$lastdayList){
	//本日解約
	$deleting = array() ;
	
	$cnt = 0 ;
	for ($i = 0 ; $i < count($lastdayList) ; $i ++) {
		$notFound = true ;
		$isName = $lastdayList[$i]['sName'] ;
		
		for ($j = 0 ; $j < count($todayList) ; $j ++) {
			$jsName = $todayList[$j]['sName'] ;//echo 'is='.$isName.', js='.$jsName."<br>\n" ;
			if ($isName == $jsName) {//echo 'YES!!'."<br>\n" ;
			//if (preg_match("/^$isName$/isu",$jsName)) {
				$notFound = false ;
				break ;
			}
		}
		
		if ($notFound) {
			$deleting[] = $lastdayList[$i] ;
		}
		// print_r($deleting) ;
	}
	
	//$deleting = array_diff($lastdayList, $todayList) ;
	$maxDel = count($deleting) ;
	return $maxDel;
}
##

$qC = $_REQUEST['qC'] ;
$qDate = $_REQUEST['qDate'] ;
$qDate2 = $_REQUEST['qDate2'] ;
if (!$qDate) $qDate = (date("Y") - 1911).date("-m-d") ;
if (!$qDate2) $qDate2 = (date("Y") - 1911).date("-m-d") ;

if ($qC == 'ok') {
	$qBrand = '' ;
	$today = '' ;
	$lastday = '' ;
	
	$maxAll = 0 ;
	$maxAdd = 0 ;
	$maxDel = 0 ;
	
	$todayList = array() ;
	$lastdayList = array() ;
	
	//設定查詢品牌
	if ($_REQUEST['qBrand'] == 'CF') $qBrand = '僑馥' ;
	else if ($_REQUEST['qBrand'] == 'AS') $qBrand = '安新' ;
	##
	
	//定義查詢日期
	$qdArr = explode('-',$qDate) ;
	$qdArr[0] += 1911 ;
	$sDay = implode('-',$qdArr) ;
	// $day = date("Y-m-d", strtotime(implode('-',$qdArr)."-1 day")) ;
	$qdArr = explode('-',$qDate2) ;
	$qdArr[0] += 1911 ;
	$eDay = implode('-', $qdArr);
	
	unset($qdArr) ;
	##
	
	//本日特約地政士
	$sql = 'SELECT * FROM tSalesGetScrivenerCount WHERE sBrand="'.$qBrand.'" AND sDate>="'.$sDay.' 00:00:00" AND sDate<="'.$eDay.' 23:59:59" ORDER BY sDate ASC;' ;
	$rs = $conn->Execute($sql);

	$i = 0;
	while (!$rs->EOF) {
		$dayList[$i] = $rs->fields;
		$dayList[$i]['lastday'] = ($rs->fields['sDate2'] == '0000-00-00')? date("Y-m-d", strtotime($rs->fields['sDate']."-1 day")):$rs->fields['sDate2'];

		$i++;

		$rs->MoveNext();
	}
	##
	
	//本日新增
	$smarty->assign('qBrand', $qBrand) ;
	$smarty->assign('qDate', $qDate) ;
	$smarty->assign('qDate2', $qDate2) ;
	$smarty->assign('dayList',$dayList);

	$smarty->display('scrivenerCompareResult2.inc.tpl', '', 'sales');
}
else {
	$smarty->assign('qDate', $qDate) ;
	$smarty->assign('qDate2', $qDate2) ;
	$smarty->assign('web_addr', $web_addr) ;

	$smarty->display('scrivenerCompare2.inc.tpl', '', 'sales');
}
?> 

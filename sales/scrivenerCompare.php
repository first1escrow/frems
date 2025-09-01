<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../web_addr.php' ;
include_once '../session_check.php' ;
// include_once '../opendb.php' ;
include_once '../tracelog.php' ;
require_once dirname(__DIR__).'/first1DB.php';

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
##

$qC = $_REQUEST['qC'] ;
$qDate = $_REQUEST['qDate'] ;
if (!$qDate) $qDate = (date("Y") - 1911).date("-m-d") ;

$conn = new first1DB;

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
	
	$today = implode('-',$qdArr) ;
	$lastday = date("Y-m-d", strtotime($today."-1 day")) ;
	
	unset($qdArr) ;
	##
	
	//本日特約地政士
	$sql = 'SELECT * FROM tSalesGetScrivener WHERE sBrand="'.$qBrand.'" AND sDateTime>="'.$today.' 00:00:00" AND sDateTime<="'.$today.' 23:59:59" ORDER BY sId ASC;' ;
	$todayList = $conn->all($sql);
	$maxAll = count($todayList) ;
	
	$alls = '' ;
	if ($maxAll > 0) {
		$alls = '
			<table>
				<tr style="height:50px;background-color:#00FFCC;">
					<th>姓名</th><th>事務所</th><th>電話</th><th>地址</th>
				</tr>
				'.fillData($todayList).'
			</table>
		' ;
		
		$maxAll = '<a class="inline" href="#alls">'.number_format($maxAll).'</a>' ;
	}
	##
	
	//前日特約地政士
	$sql = 'SELECT * FROM tSalesGetScrivener WHERE sBrand="'.$qBrand.'" AND sDateTime>="'.$lastday.' 00:00:00" AND sDateTime<="'.$lastday.' 23:59:59" ORDER BY sId ASC;' ;
	$lastdayList = $conn->all($sql);
	##
	
	//本日新增
	$adding = array() ;
	
	$cnt = 0 ;
	for ($i = 0 ; $i < count($todayList) ; $i ++) {
		$notFound = true ;
		$isName = $todayList[$i]['sName'] ;
		
		for ($j = 0 ; $j < count($lastdayList) ; $j ++) {
			$jsName = $lastdayList[$j]['sName'] ;
			
			if ($isName == $jsName) {
				$notFound = false ;

				break ;
			}
		}
		
		if ($notFound) {
			$adding[] = $todayList[$i] ;
		}
	}
	
	$maxAdd = count($adding) ;
	
	$adds = '' ;
	if ($maxAdd > 0) {
		$adds = '
			<table>
				<tr style="height:50px;background-color:#00FFCC;">
					<th>姓名</th><th>事務所</th><th>電話</th><th>地址</th>
				</tr>
				'.fillData($adding).'
			</table>
		' ;
		
		$maxAdd = '<a class="inline" href="#adds">'.number_format($maxAdd).'</a>' ;
	}
	##
	
	//本日解約
	$deleting = array() ;
	
	$cnt = 0 ;
	for ($i = 0 ; $i < count($lastdayList) ; $i ++) {
		$notFound = true ;
		$isName = $lastdayList[$i]['sName'] ;
		
		for ($j = 0 ; $j < count($todayList) ; $j ++) {
			$jsName = $todayList[$j]['sName'] ;
			if ($isName == $jsName) {
				$notFound = false ;
				break ;
			}
		}
		
		if ($notFound) {
			$deleting[] = $lastdayList[$i] ;
		}
	}
	
	$maxDel = count($deleting) ;
	
	$dels = '' ;
	if ($maxDel > 0) {
		$dels = '
			<table>
				<tr style="height:50px;background-color:#00FFCC;">
					<th>姓名</th><th>事務所</th><th>電話</th><th>地址</th>
				</tr>
				'.fillData($deleting).'
			</table>
		' ;
		
		$maxDel = '<a class="inline" href="#dels">'.number_format($maxDel).'</a>' ;
	}
	##
	
	$smarty->assign('qBrand', $qBrand) ;
	$smarty->assign('qDate', $qDate) ;
	
	$smarty->assign('alls', $alls) ;
	$smarty->assign('adds', $adds) ;
	$smarty->assign('dels', $dels) ;
	$smarty->assign('maxAll', $maxAll) ;
	$smarty->assign('maxAdd', $maxAdd) ;
	$smarty->assign('maxDel', $maxDel) ;
	
	$smarty->assign('web_addr', $web_addr) ;

	$smarty->display('scrivenerCompareResult.inc.tpl', '', 'sales');
}
else {
	$smarty->assign('qDate', $qDate) ;
	$smarty->assign('web_addr', $web_addr) ;

	$smarty->display('scrivenerCompare.inc.tpl', '', 'sales');
}
?> 

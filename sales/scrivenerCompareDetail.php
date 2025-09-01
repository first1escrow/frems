<?php

include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../web_addr.php' ;
include_once '../session_check.php' ;
include_once '../openadodb.php' ;
include_once '../tracelog.php' ;

//將陣列內容轉為表格
function fillData($arr) {
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
$_REQUEST = escapeStr($_REQUEST) ;

$qC = $_REQUEST['qC'] ;
$qDate = $_REQUEST['qDate'] ;
$qDate2 = $_REQUEST['qDate2'];
$type = $_REQUEST['type'];
// if (!$qDate) $qDate = (date("Y") - 1911).date("-m-d") ;
// if (!$qDate2) $qDate2 = (date("Y") - 1911).date("-m-d") ;
##
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
$today = implode('-',$qdArr) ;
unset($qdArr) ;

$qdArr = explode('-',$qDate2) ;	
$lastday = implode('-',$qdArr) ;
// $lastday = date("Y-m-d", strtotime($today."-1 day")) ;
unset($qdArr) ;
##
if ($type == 'all') {
	//本日特約地政士
	$sql = 'SELECT * FROM tSalesGetScrivener WHERE sBrand="'.$qBrand.'" AND sDateTime>="'.$today.' 00:00:00" AND sDateTime<="'.$today.' 23:59:59" ORDER BY sId ASC;' ;

	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$todayList[] = $rs->fields ;

		$rs->MoveNext();
	}
		
		
	$maxAll = count($todayList) ;
		
	$alls = '' ;
	if ($maxAll > 0) {
		$alls = '
			<table width="100%">
				<tr style="height:50px;background-color:#00FFCC;">
					<th>姓名</th><th>事務所</th><th>電話</th><th>地址</th>
				</tr>
				'.fillData($todayList).'
			</table>
		' ;
			
		$maxAll = '<a class="inline" href="#alls">'.number_format($maxAll).'</a>' ;
	}
}	

##
if ($type == 'add') {
	$adding = array() ;
	
	$sql = 'SELECT sAddId,sAdd FROM tSalesGetScrivenerCount WHERE sBrand="'.$qBrand.'" AND sDate>="'.$today.'" AND sDate<="'.$today.'" ORDER BY sId ASC;' ;
	$rs = $conn->Execute($sql);

	if ($rs->fields['sAdd'] > 0) {
		$addId = implode(',', json_decode($rs->fields['sAddId']));
		// print_r($addId);

		$sql = "SELECT * FROM tSalesGetScrivener WHERE sId IN(".$addId.")";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$adding[] = $rs->fields ;


			$rs->MoveNext();
		}
	}

	$maxAdd = count($adding) ;
		
	$adds = '' ;
	if ($maxAdd > 0) {
		$adds = '
			<table width="100%">
				<tr style="height:50px;background-color:#00FFCC;">
					<th>姓名</th><th>事務所</th><th>電話</th><th>地址</th>
				</tr>
				'.fillData($adding).'
			</table>
		' ;
			
		$maxAdd = '<a class="inline" href="#adds">'.number_format($maxAdd).'</a>' ;
	}
}
	//本日新增

##
	
//本日解約
if ($type == 'del') {
	$deleting = array() ;
	
	$sql = 'SELECT sDeleteId,sDelete FROM tSalesGetScrivenerCount WHERE sBrand="'.$qBrand.'" AND sDate>="'.$today.'" AND sDate<="'.$today.'" ORDER BY sId ASC;' ;
	$rs = $conn->Execute($sql);

	if ($rs->fields['sDelete'] > 0) {
		$delId = implode(',', json_decode($rs->fields['sDeleteId']));
		// print_r($addId);

		$sql = "SELECT * FROM tSalesGetScrivener WHERE sId IN(".$delId.")";
		$rs = $conn->Execute($sql);
		while (!$rs->EOF) {
			$deleting[] = $rs->fields ;


			$rs->MoveNext();
		}
	}
	 // print_r($deleting) ;
	//$deleting = array_diff($lastdayList, $todayList) ;
	$maxDel = count($deleting) ;
		
	$dels = '' ;
	if ($maxDel > 0) {
		$dels = '
			<table width="100%">
				<tr style="height:50px;background-color:#00FFCC;">
					<th>姓名</th><th>事務所</th><th>電話</th><th>地址</th>
				</tr>
				'.fillData($deleting).'
			</table>
		' ;
			
		$maxDel = '<a class="inline" href="#dels">'.number_format($maxDel).'</a>' ;
	}
}


// echo $alls;
// echo $type;
##
##
$smarty->assign('alls', $alls) ;
$smarty->assign('adds', $adds) ;
$smarty->assign('dels', $dels) ;
$smarty->assign('type',$type);
$smarty->assign('qDate', $qDate) ;
$smarty->assign('qDate2', $qDate2) ;
$smarty->assign('web_addr', $web_addr) ;

$smarty->display('scrivenerCompareDetail.inc.tpl', '', 'sales');
?> 

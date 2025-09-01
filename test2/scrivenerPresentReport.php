<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

if ($_SESSION['member_id'] == 6) {
	ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
}

$_POST = escapeStr($_POST) ;
##
$cat = $_POST['cat'];

$display = "none";

if ($cat) {
	$display = "";


	$year = $_POST['year'];
	$month = $_POST['month'];

	$month2 = $_POST['month2'];
	$scrivener = $_POST['scrivener'];
	$status = $_POST['status'];

	$str = "sl.sYear = '".($year+1911)."'";
	if ($month) {
		if ($str) { $str .= " AND " ; }
		// $query .= ' s.sBirthday ="'.$scrivener.'" ' ;
		$str .= " (MONTH(s.sBirthday) >= '".$month."' AND MONTH(s.sBirthday) <= '".$month2."')";		
	}

	if ($scrivener) {
		if ($str) { $str .= " AND " ; }
		$str .= "s.sId = '".$scrivener."'";
	}

	if ($status == 0) {
		if ($str) { $str .= " AND " ; }
		$str .= "sl.sStatus IN(2,4) ";
	}else{
		if ($str) { $str .= " AND " ; }
		$str .= "sl.sStatus = '".$status."'";
	}


	if ($_POST['sId'] && $cat == 'xls') {
		$check = false;
		if ($_POST['sId']) {
			$ss = implode(',', $_POST['sId']);
			if ($str) { $str .= " AND " ; }
			$str .= "sl.sId IN (".$ss.")";
			$check = true;
		}

	
	}

	if ($_POST['target'] == 1) {
		if ($str) { $str .= " AND " ; }
		$str .= " sl.sLevel = 0";
	}elseif ($_POST['target'] == 2) {
		if ($str) { $str .= " AND " ; }
		$str .= " sl.sLevel > 0";
	}


	$sql = "SELECT
				sl.sId AS ss,
				s.sId,
				s.sBirthday,
				s.sName,
				s.sOffice,
				CONCAT('SC', LPAD(s.sId,4,'0')) as sCode2,
				sl.sMoney,
				(SELECT pName FROM tPeopleInfo WHERE pId = sl.sApplicant) AS sApplicant,
				(SELECT pName FROM tPeopleInfo WHERE pId = sl.sInspetor) AS sInspetor,
				sl.sReceipt,
				sl.sName AS taxName,
				sl.sIdentify,
				sl.sIdentifyIdNumber,
				sl.sTicket,
				sl.sZip,
				sl.sLevel,
				sl.sGift,
				(SELECT zCity FROM tZipArea WHERE zZip = sl.sZip) city,
				(SELECT zArea FROM tZipArea WHERE zZip = sl.sZip) area,
				sl.sAddress,
				sl.sSales			
			FROM
				tScrivenerLevel AS sl
			LEFT JOIN
	        	tScrivener AS s ON sl.sScrivener = s.sId
			WHERE
				 ".$str." ORDER BY MONTH(s.sBirthday),DAY(s.sBirthday) ASC";
			
				 // echo $sql;
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$rs->fields['gift'] = getGift($rs->fields['sLevel'],$rs->fields['sGift']);
		$rs->fields['sBirthday'] = substr($rs->fields['sBirthday'], 5);
		$rs->fields['sReceipt'] = ($rs->fields['sReceipt']== 1)?'是':'否';
		$rs->fields['sLevel'] = ($rs->fields['sLevel'] == 0)?'未達標':'已達標';
		
		$tmp = explode(',', $rs->fields['sSales']);
		for ($i=0; $i < count($tmp); $i++) { 
			$tmp[$i] = getSalesName($tmp[$i]);
		}
		$rs->fields['salesName'] = @implode(',', $tmp) ;

		$list[] = $rs->fields;


		$rs->MoveNext();
	}

	if ($check) {
		include_once 'scrivenerPresentReportExcel.php';
	}
	

}

##
$nowYear = date('Y')-1911;
$option_year = '';
$selectedYear = ($_POST['year'])?$_POST['year']:$nowYear;

for ($i=($nowYear+1); $i >=107 ; $i--) { 

	$selected = ($i == $selectedYear) ? "selected=selected" :'';
	
	$option_year .= "<option value=\"".$i."\" ".$selected.">".$i."</option>";
}
$option_month = "<option value=''></option>";
for ($i=1; $i <= 12; $i++) { 
	
	$selected = ($i == $_POST['month'] && $_POST['month']) ?"selected=selected":"";

	$option_month .= "<option value=\"".$i."\" ".$selected.">".$i."</option>";
}
$option_month2 = "<option value=''></option>";
for ($i=1; $i <= 12; $i++) { 
	
	$selected = ($i == $_POST['month2'] && $_POST['month2']) ?"selected=selected":"";

	$option_month2 .= "<option value=\"".$i."\" ".$selected.">".$i."</option>";
}
##
//狀態 (0:未申請 1:申請中 2:審核通過 (政耀)3:不通過(政耀)4:已處理
$option_status = array(0=>'全部',1=>'申請中',2=>'審核通過',4=>'已處理');
//達標
$option_target = array(0=>'全部',1=>'未達標',4=>'已達標');
##
$sql = "SELECT sId,sName,sOffice FROM tScrivener ORDER BY sId";

$rs = $conn->Execute($sql);
// $menu_scr[0] = '';
while (!$rs->EOF) {
	
	$scrivener_search .= '<option value="'.$rs->fields['sId'].'">SC'.str_pad($rs->fields['sId'],4,'0',STR_PAD_LEFT).$rs->fields['sName'].'('.$rs->fields['sOffice'].')</option>';
	$rs->MoveNext();
}
function getGift($level,$gift){
	global $conn;

	if ($gift == 7) {
		$str = " gId = '".$gift."'";
	}else{
		$str = "sLevel = '".$level."'";
	}

	$sql = "SELECT CONCAT(gCode,gName) AS gift FROM tGift WHERE ".$str;
	// echo $sql;
	// if ($_SESSION['member_id'] == 6) {
	// 	echo $sql;
	// }
	

	$rs = $conn->Execute($sql);

	return $rs->fields['gift'];
}
function getSalesName($sales){
	global $conn;

	$sql = "SELECT pName FROM tPeopleInfo WHERE pId =  '".$sales."'";
	$rs = $conn->Execute($sql);
	

	return $rs->fields['pName'];

}
##
$smarty->assign('tax',$_POST['tax']);
$smarty->assign('option_year',$option_year);
$smarty->assign('option_month',$option_month);
$smarty->assign('option_month2',$option_month2);
$smarty->assign('option_status',$option_status);
$smarty->assign('scrivener_search',$scrivener_search);
$smarty->assign('list',$list);
$smarty->assign('display',$display);
$smarty->assign('option_target',$option_target);
$smarty->display('scrivenerPresentReport.inc.tpl', '', 'accounting') ;
?> 

<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

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
		// $d = ($year+1911)."-".$month."-01 00:00:00";
		// $d2 = ($year+1911)."-".$month2."-31 23:59:59";
		// $str .= "sl.sTime >= '".$d."' AND  sTime <= '".$d2."'";
		
	}

	if ($scrivener) {
		if ($str) { $str .= " AND " ; }
		$str .= "s;.sScrivener = '".$scrivener."'";
	}

	if ($str) { $str .= " AND " ; }
	$str .= "sl.sStatus = 4 ";

	// if ($_POST['sId'] && $cat == 'xls') {
	// 	$check = false;
	// 	if ($_POST['sId']) {
	// 		$ss = implode(',', $_POST['sId']);
	// 		if ($str) { $str .= " AND " ; }
	// 		$str .= "sl.sId IN (".$ss.")";
	// 		$check = true;
	// 	}

	
	// }


	$sql = "SELECT
			sl.sName,
			sl.sIdentifyIdNumber,
			sl.sZip,
			(SELECT zCity FROM tZipArea WHERE zZip = sl.sZip) city,
			(SELECT zArea FROM tZipArea WHERE zZip = sl.sZip) area,
			sl.sAddress,
			sl.sMoney,
			sl.sTicket,
			(SELECT pName FROM tPeopleInfo WHERE pId = sl.sApplicant) AS sApplicant,
			s.sName AS scrivnerName,
			CONCAT('SC', LPAD(s.sId,4,'0')) as sCode2,
			sl.sSales
			FROM
				tScrivenerLevel AS sl
			LEFT JOIN
				tScrivener AS s ON s.sId = sl.sScrivener
			WHERE
				 ".$str."";
				
				 // echo $sql;
				 // die;
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {

		$tmp = explode(',', $rs->fields['sSales']);
		for ($i=0; $i < count($tmp); $i++) { 
			$tmp[$i] = getSalesName($tmp[$i]);
		}

		$rs->fields['salesName'] = @implode(',', $tmp) ;

		$list[] = $rs->fields;


		$rs->MoveNext();
	}

	if ($cat == 'xls') {
		include_once 'scrivenerPresentTaxExcel.php';
	}
	

}

##
$nowYear = date('Y')-1910;
$option_year = '';
for ($i=$nowYear; $i >=107 ; $i--) { 

	$selected = ($i == $_POST['year'] && $_POST['year']) ? "selected=selected" :'';
	
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
$option_status = array(0=>'全部',2=>'審核通過',4=>'已處理');
##
$sql = "SELECT sId,sName,sOffice FROM tScrivener ORDER BY sId";

$rs = $conn->Execute($sql);
// $menu_scr[0] = '';
while (!$rs->EOF) {
	
	$scrivener_search .= '<option value="'.$rs->fields['sId'].'">SC'.str_pad($rs->fields['sId'],4,'0',STR_PAD_LEFT).$rs->fields['sName'].'('.$rs->fields['sOffice'].')</option>';
	$rs->MoveNext();
}

function getSalesName($sales){
	global $conn;

	$sql = "SELECT pName FROM tPeopleInfo WHERE pId =  '".$sales."'";
	$rs = $conn->Execute($sql);
	

	return $rs->fields['pName'];

}
##
$smarty->assign('option_year',$option_year);
$smarty->assign('option_month',$option_month);
$smarty->assign('option_month2',$option_month2);
$smarty->assign('option_status',$option_status);
$smarty->assign('scrivener_search',$scrivener_search);
$smarty->assign('list',$list);
$smarty->assign('display',$display);
$smarty->display('scrivenerPresentTax.inc.tpl', '', 'accounting') ;
?> 

<?php
include_once '../../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/contract.class.php';
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;
header('Content-Type: text/html; charset=utf-8');

$_POST = escapeStr($_POST) ;

$year = ($_POST['year'])?($_POST['year']+1911):date('Y');
$month = ($_POST['month'])?$_POST['month']:date('m');
$searchShipDateStart = '';
$searchShipDateEnd = '';

if ($_POST['searchShipDateStart'] != '') {
	$expDate = array();
	$expDate = explode('-', $_POST['searchShipDateStart']);
	$searchShipDateStart = ($expDate[0]+1911).'-'.$expDate[1].'-'.$expDate[2];

	unset($expDate);
}

if ($_POST['searchShipDateEnd'] != '') {
	$expDate = array();
	$expDate = explode('-', $_POST['searchShipDateEnd'] );

	$searchShipDateEnd = ($expDate[0]+1911).'-'.$expDate[1].'-'.$expDate[2];
	unset($expDate);
}



// echo $searchShipDateStart."\r\n";


$CaseType = array(1=>'土地',2=>'建物',3=>'預售屋');//1土地2建物3預售屋

$sql ="SELECT pId,pName FROM tPeopleInfo WHERE (pDep = '11' OR pDep = '12') AND pJob = 1";
$rs = $conn->Execute($sql);
$menuPeople[0] = "請選擇";
while (!$rs->EOF) {
	$menuPeople[$rs->fields['pId']] = $rs->fields['pName'];

	$rs->MoveNext();
}



##
//預售屋先不用

$dd = strtotime($year."-".$month."-01");
$dd2 = strtotime("2020-09-01");
if (($dd < $dd2) && ($searchShipDateStart == '' && $searchShipDateEnd == '')) {
	$template = 0;
}else{
	$template = 1;
}

if ($_SESSION['member_id'] == 6) {
	$str = '';
}else{
	$str = 'bShow != 1';
}

// print_r($_POST);
if (!empty($_POST['searchShipScrivener'])) {
	if ($str) { $str .= " AND ";}

	$str .= " bSID = '".$_POST['searchShipScrivener']."'";
}	

if (!empty($_POST['searchShipApplicant'])) {
	if ($str) { $str .= " AND ";}

	$str .= " bApplicant = '".$_POST['searchShipApplicant']."'";
}	
	
if ($searchShipDateStart != '' && $searchShipDateEnd != '') {
	if ($str) { $str .= " AND ";}
			
		
	$str .= " ((bShipDate >= '".$searchShipDateStart."' AND bShipDate <= '".$searchShipDateEnd."') OR (bUrgentDate >= '".$searchShipDateStart."' AND bUrgentDate <= '".$searchShipDateEnd."'))";
}else{
	if ($str) { $str .= " AND ";}

	$str .= " (bDate >='".$year."-".$month."-01' AND bDate <= '".$year."-".$month."-".date('t',$year."-".$month)."')";

}


$sql = "SELECT
			bId,
			(SELECT sName FROM tScrivener WHERE sId= bSID) AS scrivener,
			(SELECT bName FROM tBrand WHERE bId= bBrand) bBrand,
			bNo,
			CASE bCategory 
				WHEN 1 THEN '加盟'
				WHEN 2 THEN '直營'
				WHEN 3 THEN '非仲介成交' END bCategory,
			bCount,
			CASE bApplication 
				WHEN 1 THEN '土地'
				WHEN 2 THEN '建物'
				WHEN 3 THEN '預售屋' END bApplication,			
			(SELECT pName FROM tPeopleInfo WHERE pId = bApplicant) AS Applicant,
			bDate,
			(SELECT pName FROM tPeopleInfo WHERE pId = bProducer) AS Producer,
			bProducer,
			bShipDate,			
			bUrgentDate,
			bNote,
			CONCAT('SC',LPAD(bSID,4,'0')) code,
			(SELECT cBankName FROM tContractBank WHERE cId = bBank) AS bankName
		FROM
			tBankCodeForm2 WHERE ".$str."ORDER BY bShipDate ASC,bDate DESC";

// echo $sql;
$rs = $conn->Execute($sql);
$i=0;
while (!$rs->EOF) {
	$list[$i] = $rs->fields;
	$list[$i]['bUrgentChecked'] = ($list[$i]['bUrgentDate'] != '0000-00-00')?'checked=checked':'';
	// $list[$i]['bank'] = $list;
	
	$list[$i]['bDate'] = ($list[$i]['bDate'])?(substr($rs->fields['bDate'], 0,4)-1911).substr($rs->fields['bDate'], 4):'000-00-00';
	$list[$i]['bShipDate'] = ($list[$i]['bShipDate'] != '0000-00-00')? (substr($rs->fields['bShipDate'], 0,4)-1911).substr($rs->fields['bShipDate'], 4):'000-00-00';
	$list[$i]['bUrgentDate'] = ($list[$i]['bUrgentDate'] != '0000-00-00')? (substr($rs->fields['bUrgentDate'], 0,4)-1911).substr($rs->fields['bUrgentDate'], 4):'000-00-00';
	$list[$i]['bShowBrand'] = ($list[$i]['bCategory'] == '非仲介成交')?$list[$i]['bBrand']:$list[$i]['bBrand'].$list[$i]['bCategory'];
	$i++;

	$rs->MoveNext();
}
##
$smarty->assign('template',$template);
$smarty->assign("menuPeople",$menuPeople);
$smarty->assign("list",$list);
$smarty->display("bankCodeProcess.inc.tpl","","escrow");
?>
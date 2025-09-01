<?php

include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../web_addr.php' ;
require_once '../bank/Classes/PHPExcel.php' ;
require_once '../bank/Classes/PHPExcel/Writer/Excel2007.php' ;
include_once '../class/getAddress.php' ;
include_once '../class/getBank.php' ;
include_once '../includes/maintain/feedBackData.php' ;
// include_once 'getBranchType.php';
// include_once '../report/getBranchType.php';
$brand = '' ;
$status = '' ;
$category = '' ;
$contract_bank = '' ;

$_POST = escapeStr($_POST) ;
$cat = ($_POST['cat'])?$_POST['cat']:1;
$sp = ($_POST['sp'])?$_POST['sp']:'';

// print_r($sp);


// if ($cat == 1 || $cat == '') {
// 	$checked1 = 'checked=checked';
// 	$checked2 = '';
// 	$checked3 = '';
// }elseif ($cat == 2) {
// 	$checked1 = '';
// 	$checked2 = 'checked=checked';
// 	$checked3 = '';
// }elseif ($cat == 3) {
// 	$checked1 = '';
// 	$checked2 = '';
// 	$checked3 = 'checked=checked';
// }elseif ($cat == 16) {
// 	$checkedSp = 'checked=checked';
// 	$checkedSp2 = '';
// }elseif ($cat == 72) {
// 	$checkedSp = '';
// 	$checkedSp2 = 'checked=checked';
// }



if ($_POST['ok'] == 'ok') {

	include_once 'feedBackErrorResult.php';

}
##
$arrayCategory =  array('1' => '全部案件','2'=>'未收足保證費案件');

if ($_SESSION['member_id'] == 6 || $_SESSION['member_id'] == 1 || $_SESSION['member_id'] == 3) {

	$arrayCategory2 = array();
	//品牌
	$sql = "SELECT bId,bName FROM tBrand WHERE bRecall != '' AND bRecall != 0";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$arrayCategory2['b'.$rs->fields['bId']] = $rs->fields['bName'];
		$rs->MoveNext();
	}
	//群組
	$sql = "SELECT bId,bName FROM tBranchGroup WHERE bRecall != ''";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$arrayCategory2['g'.$rs->fields['bId']] = $rs->fields['bName'];
		$rs->MoveNext();
	}

	// $arrayCategory2 =  array('16'=>'飛鷹(只顯示額外回饋案件)','72'=>'群義(只顯示額外回饋案件)');
} 
##

$sql = "SELECT pId,pName FROM tPeopleInfo WHERE pDep IN(4,7) AND pJob = 1 ";
$salesList = '';
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$selected = '';
	if ($rs->fields['pId'] == $_POST["sales"]) {
		$selected = "selected=selected";
	}
	$salesList .= "<option value='".$rs->fields['pId']."' ".$selected.">".$rs->fields['pName']."</option>\n" ;
	$rs->MoveNext();
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
##

if ($sEndDate) {
	$tmp = explode('-', $sEndDate);
	$sEndDate = ($tmp[0]-1911)."-".$tmp[1]."-".$tmp[2];
}

if ($eEndDate) {
	$tmp = explode('-', $eEndDate);
	$eEndDate = ($tmp[0]-1911)."-".$tmp[1]."-".$tmp[2];
}
##
$smarty->assign('i_begin',$i_begin) ;
$smarty->assign('i_end',$i_end) ;
$smarty->assign('current_page',$current_page) ;
$smarty->assign('total_page',$total_page) ;
$smarty->assign('record_limit',$records_limit) ;
$smarty->assign('max',number_format($max)) ;

$smarty->assign('cat',$cat);
$smarty->assign('sp',$sp);
$smarty->assign('arrayCategory',$arrayCategory);
$smarty->assign('arrayCategory2',$arrayCategory2);
$smarty->assign('list',$list);
$smarty->assign('salesList',$salesList);
$smarty->assign('sEndDate',$sEndDate);
$smarty->assign('eEndDate',$eEndDate);
$smarty->assign('checked1',$checked1);
$smarty->assign('checked2',$checked2);
$smarty->assign('checked3',$checked3);
$smarty->assign('checkedSp',$checkedSp);
$smarty->assign('checkedSp2',$checkedSp2);
$smarty->display('feedBackError.inc.tpl', '', 'report') ;
?> 

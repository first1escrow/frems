<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../includes/first1Sales.php';
include_once '../session_check.php' ;

// if($_SESSION['member_id']!=6)
// {
// 	echo '建置中';
// 	die();
// }
if ($_SESSION['tmp']) { //剛登入
	$script = "window.open('../calendar/calendar.php','_blank');";
	unset($_SESSION['tmp']);
}
##時間下拉
$yr = date("Y") - 1911 ;
$mn = date("m",mktime(0,0,0,(date("m")-1))) ;

// 年度顯示
$y = '' ;
for ($i = 0 ; $i < 100 ; $i ++) {
	$patt = $i + 100 ;
	
	if (($patt==$yr)&&($mn!='12')) { $sl = " selected='selected'" ; }
	else if ((($patt+1)==$yr)&&($mn=='12')) { $sl = " selected='selected'" ; }
	else { $sl = '' ; }
	
	$y .= "<option value='".$patt."'".$sl.">".$patt."</option>\n" ;
}

// 月份顯示
$m = '' ;
for ($i = 0 ; $i < 12 ; $i ++) {
	$patt = $i + 1 ;
	
	if ($patt==$mn) { $sl = " selected='selected'" ; }
	else { $sl = '' ; }
	
	$m .= "<option value='".$patt."'".$sl.">".$patt."</option>\n" ;
}

## 案件狀態
$sql = 'SELECT sId,sName FROM tStatusCase WHERE sId<>"0" ORDER BY sId ASC;' ;

$rs = $conn->Execute($sql);
$menu_status[0]='';
while (!$rs->EOF) {
	$menu_status[$rs->fields['sId']]=$rs->fields['sName'];

	$rs->MoveNext();
}
##

// ##仲介類別
// $menu_caregory ="<option value=''></option>";
// $menu_caregory .= "<option value='11'>加盟(其他品牌)</option>\n" ;
// $menu_caregory .= "<option value='12'>加盟(台灣房屋)</option>\n" ;
// $menu_caregory .= "<option value='13'>加盟(優美地產)</option>\n" ;
// $menu_caregory .= "<option value='1'>加盟</option>\n" ;
// $menu_caregory .= "<option value='2'>直營</option>\n" ;
// $menu_caregory .= "<option value='3'>非仲介成交</option>\n" ;
##
if ($_SESSION['member_id'] == 6) {
	$mid = 38;
}else{
	$mid = $_SESSION['member_id'];
}



$sql = "
	SELECT
		b.bId, 
		CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ), LPAD(b.bId,5,'0') ) AS bCode2,
		(SELECT bName FROM tBrand AS bd WHERE bd.bId = b.bBrand) AS brand,
		b.bStore,
		b.bName
	FROM
		tBranchSales AS bs
	LEFT JOIN
		tBranch AS b ON b.bId = bs.bBranch
	WHERE
		bs.bSales = '".$mid."'
	ORDER BY b.bBrand,b.bId ASC

	";


$rs = $conn->Execute($sql);
$menu_branch[0]='';
while (!$rs->EOF) {
	
	$menu_branch[$rs->fields['bId']] = $rs->fields['bCode2'].'/'.$rs->fields['brand'].'-'.$rs->fields['bStore'];

	$rs->MoveNext();
}
##

$sql = "
		SELECT
			s.sId,
			CONCAT('SC', LPAD(s.sId,4,'0') ) AS sCode,
			sName,
			sOffice
		FROM
			tScrivener AS s
		LEFT JOIN
			tScrivenerSales AS ss ON ss.sScrivener = s.sId
		WHERE
			ss.sSales = '".$mid."'
		ORDER BY s.sId ASC
		"; 
// echo $sql;
$rs = $conn->Execute($sql);
$menu_scrivener[0]='';

while (!$rs->EOF) {
	
	$menu_scrivener[$rs->fields['sId']] = $rs->fields['sCode'].$rs->fields['sName']."(".$rs->fields['sOffice'].")";

	$rs->MoveNext();
}
##

##
$smarty->assign('script',$script);
$smarty->assign("y",$y) ;
$smarty->assign("m",$m) ;
$smarty->assign('menu_sales', $menu_sales) ;
$smarty->assign('menu_status', $menu_status) ;
$smarty->assign('menu_branch',$menu_branch);
$smarty->assign('menu_scrivener',$menu_scrivener);
$smarty->display('salesBusiness.inc.tpl', '', 'sales') ;

?>